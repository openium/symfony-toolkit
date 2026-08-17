# Changelog

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
