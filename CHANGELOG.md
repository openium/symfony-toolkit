# Changelog

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
