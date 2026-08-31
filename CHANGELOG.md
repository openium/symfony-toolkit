# Changelog

## 7.0.0

### Fixed

- `DoctrineExceptionHandlerService::toHttpException()` used `switch ($throwable::class)`, which
  only ever matches an exact class, never a subclass. Two consequences: any project-specific or
  future Doctrine exception subclassing one of the handled types (e.g. a custom subclass of
  `TableNotFoundException`) fell through to `default: throw $throwable;` instead of being
  converted to an HTTP exception, and the `case Exception::class:` branch (`Doctrine\DBAL\Exception`
  is an interface) could never match anything, making `dbalExceptionManagement()` dead code.
  Replaced with `instanceof` checks ordered from most to least specific, so subclasses are now
  handled correctly and the SQLSTATE-based fallback is reachable again.

### Deprecated

- `ServerService` / `ServerServiceInterface` are deprecated and will be removed in 8.0.
  `getBasePath()` only duplicated `Symfony\Component\HttpFoundation\Request::getSchemeAndHttpHost()`;
  the service now delegates to it internally. Instantiating `ServerService` triggers a
  deprecation notice. Use `Request::getSchemeAndHttpHost() . '/'` directly instead.

### Security fix

- `AtHelper::createAtCommand()` / `createAtCommandFromPath()` now pass `$cmd` and `$path` through
  `escapeshellarg()` before building the shell command executed via `passthru()`. Previously
  these values were interpolated unescaped, allowing shell command injection if either came from
  untrusted input. `$cmd` is now treated as literal data given to `at`, not as a shell snippet —
  see the README's AtHelper section for the BC impact.

### BREAKING CHANGE

- The bundle now exposes a real semantic configuration tree under the `openium_symfony_toolkit`
  key (`uploads.public_dir`, `uploads.dir_name`, `kernel_exception_listener.enabled`,
  `kernel_exception_listener.path`, `kernel_exception_listener.class`). Previously,
  `DependencyInjection/Configuration` was an empty tree and the actual values were plain
  `parameters:` hardcoded in `Resources/config/services.yaml`; overriding them from a consuming
  project relied on redefining those raw parameters. Any such raw `parameters:` override no
  longer has any effect and must be migrated to the new `openium_symfony_toolkit:` config block
  (see the README's Configuration section).

## 6.0.1

### Fixed

- `ServerService::getBasePath` now includes the port when it differs from the scheme's
  default (80 for `http`, 443 for `https`), e.g. `http://localhost:8080/` instead of
  `http://localhost/`. Links built from a non-default port (common in local dev) now work.

## 6.0.0

### BREAKING CHANGE

`ExceptionFormatService` is rewritten to build a typed DTO (`ExceptionDTO`/`DevExceptionDTO`)
serialized through the Symfony Serializer, instead of a hand-built associative array. As a
consequence:

- `ExceptionFormatServiceInterface` now only exposes `formatExceptionResponse`.
  `getArray`, `addKeyToErrorArray`, `getStatusCode`, `getStatusText`,
  `genericExceptionResponse` and the `$jsonKeys` property have been removed — the
  pre-6.0 subclassing pattern no longer works.
- The constructor now takes `SerializerInterface`, the new `ExceptionFormatUtilsInterface`
  and the kernel environment string, instead of the kernel service.
- The new extension point is `ExceptionFormatUtilsInterface` (implemented by
  `Utils/ExceptionFormatUtils`): override its service id to customize the status code/text of
  the response. See the README's ExceptionFormatService section for the migration example.
- Projects that rely on the pre-6.0 API should stay on the `v5` branch.

### Security fix

- `ExceptionFormatUtils::getStatusCode` returns `401 Unauthorized` for
  `Symfony\Component\Security\Core\Exception\AuthenticationException` (and its subclasses, e.g.
  `BadCredentialsException`, `UserNotFoundException`, `CustomUserMessageAuthenticationException`),
  instead of falling back to `500 Internal Server Error`.
- `ExceptionFormatService::getDTO` never builds a `DevPreviousExceptionDTO` when either the
  formatted exception or its `previous` is an `AuthenticationException`, regardless of the
  environment — closing the account-enumeration leak in both directions of the exception chain
  (also fixed for the pre-6.0 API in 5.1.0/4.5.0/3.2.0, see below).

### Dependencies & tooling

- Symfony requirement bumped to `^8.1`.
- PHPStan upgraded to `^2.x` (the `^1.10` line was silently crashing under PHP 8.4 due to a
  missing lazy-object/`var-exporter` compatibility, meaning static analysis had stopped running
  in practice); `rector/rector` bumped to `^2.0` accordingly. Pre-existing findings outside the
  scope of this change are tracked in `phpstan-baseline.neon`.
- Added PHP_CodeSniffer (`squizlabs/php_codesniffer`, PSR-12) with `composer cs-check` /
  `composer cs-fix` scripts. Two pre-existing files with unrelated style debt
  (`Service/DoctrineExceptionHandlerService.php`, `Utils/ContentExtractorUtils.php`) are
  excluded for now; left as a follow-up cleanup.

## 5.1.0

### Security fix

- `ExceptionFormatService::getStatusCode` now returns `401 Unauthorized` for
  `Symfony\Component\Security\Core\Exception\AuthenticationException` (and its subclasses, e.g.
  `BadCredentialsException`, `UserNotFoundException`, `CustomUserMessageAuthenticationException`).
  Previously these exceptions did not implement `HttpExceptionInterface` and therefore fell back to a
  generic `500 Internal Server Error`, hiding real authentication failures behind server errors.
- `ExceptionFormatService::getArray` no longer includes the `previous` key in the JSON response when the
  formatted exception is an `AuthenticationException`, regardless of the environment. Symfony's
  `AuthenticatorManager` intentionally wraps a `UserNotFoundException` inside a `BadCredentialsException`
  to avoid revealing whether an account exists; re-exposing `previous.message` in non-prod environments
  defeated that protection and allowed account enumeration (e.g. `User "x@y.com" not found.`).
