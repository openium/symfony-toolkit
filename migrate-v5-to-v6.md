# Migration V5 to V6

## What's changing

`ExceptionFormatService` was rewritten to build a typed DTO (`ExceptionDTO`/`DevExceptionDTO`)
serialized through the Symfony Serializer, instead of a hand-built associative array. As a
consequence, it **no longer supports being extended** (no more subclassing).

The following public methods/property have been removed from `ExceptionFormatServiceInterface`:
`getArray`, `addKeyToErrorArray`, `getStatusCode`, `getStatusText`, `genericExceptionResponse`,
`$jsonKeys`. The interface now only exposes `formatExceptionResponse(Throwable $exception): Response`.

The constructor signature also changed: it now takes `SerializerInterface`,
`ExceptionFormatUtilsInterface` and the kernel environment string, instead of the kernel service.

Two security fixes are also included and require no code change: `AuthenticationException` (and
subclasses like `BadCredentialsException`, `UserNotFoundException`) now correctly returns `401
Unauthorized` instead of falling back to `500`, and the `previous` exception is never exposed in
the response when either the exception or its `previous` is an `AuthenticationException` — this
closes an account-enumeration leak.

If you rely on the pre-6.0 subclassing API and are not ready to migrate, stay on the `v5` branch.

## Code changes

### Before

```php
<?php
namespace App\Service;

use Openium\SymfonyToolKitBundle\Service\ExceptionFormatService as BaseExceptionFormatService;
use Openium\SymfonyToolKitBundle\Service\ExceptionFormatServiceInterface;

class ExceptionFormatService extends BaseExceptionFormatService implements ExceptionFormatServiceInterface
{
    public function genericExceptionResponse(Exception $exception): array
    {
        // ...
        return [$code, $text, $message];
    }

    public function addKeyToErrorArray(array $error, Exception $exception): array
    {
        // ...
        return $error;
    }
}
```

```yaml
    openium_symfony_toolkit.exception_format:
        class: App\Service\ExceptionFormatService
        arguments:
            - '@kernel'
        public: true
```

### After

Most of the time you don't need to replace the whole service: override the
`ExceptionFormatUtils` service instead, which only defines the text, message and code of the
exception. The new class must implement `ExceptionFormatUtilsInterface`:

```php
<?php
namespace App\Service;

use Exception;
use Openium\SymfonyToolKitBundle\Utils\ExceptionFormatUtilsInterface;

class ExceptionFormatUtils implements ExceptionFormatUtilsInterface
{
    public function getStatusCode(Exception $exception): int
    {
        // ...
    }

    public function getStatusText(Exception $exception): string
    {
        // ...
    }
}
```

```yaml
    openium_symfony_toolkit.exception_format_utils:
        class: App\Service\ExceptionFormatUtils
        public: true
```

If you need full control over the response body (not just the code/text/message), implement
`ExceptionFormatServiceInterface` yourself (`ExceptionFormatService` can no longer be extended)
and override the `openium_symfony_toolkit.exception_format` service instead:

```php
<?php
namespace App\Service;

use Openium\SymfonyToolKitBundle\Service\ExceptionFormatServiceInterface;
use Symfony\Component\HttpFoundation\Response;
use Throwable;

class ExceptionFormatService implements ExceptionFormatServiceInterface
{
    public function formatExceptionResponse(Throwable $exception): Response
    {
        $response = new Response();
        $response->setContent(json_encode(['error' => 'Custom error message']));
        $response->setStatusCode(Response::HTTP_BAD_REQUEST);

        return $response;
    }
}
```

```yaml
    openium_symfony_toolkit.exception_format:
        class: App\Service\ExceptionFormatService
        public: true
```

See the README's ExceptionFormatService section for more details.
