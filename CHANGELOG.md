# Changelog

## Unreleased

### Changed

- `PathKernelExceptionListener` now logs the exception the way Symfony's `ErrorListener` would
  have (it never runs, since the listener sets the response and stops the propagation):
  `$logger->log($level, 'Uncaught PHP Exception …', ['exception' => $throwable])`. The throwable
  is now in the log context, so structured handlers (JSON, ELK) get its class, file, line and
  stack trace instead of a plain string.
- Log levels changed: a 5xx response is still logged as `critical`; every 4xx is now logged as
  `warning` (401 and 404 were `info`, so dropped by handlers starting at `notice`; the other 4xx
  were `error`, and would trip a `fingers_crossed` handler with `action_level: error`). The
  level choice lives in the new protected `logException()` method, which a subclass can override.

## 3.2.1

### Fixed

- `ServerService::getBasePath` now includes the port when it differs from the scheme's
  default (80 for `http`, 443 for `https`), e.g. `http://localhost:8080/` instead of
  `http://localhost/`. Links built from a non-default port (common in local dev) now work.

## 3.2.0

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
