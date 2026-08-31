Symfony toolkit
===============

This symfony bundle provides abstractions for many common cases.

Installation
------------

Open a command console, enter your project directory and execute:

```bash
$ composer require openium/symfony-toolkit
```

### Version compatibility

| Bundle version | Symfony | PHP    | Migration guide |
|----------------|---------|--------|------------------|
| v7 (current)   | ^8.1    | ^8.4   | [migrate-v6-to-v7.md](migrate-v6-to-v7.md) |
| v6             | ^8.1    | ^8.4   | [migrate-v5-to-v6.md](migrate-v5-to-v6.md) |
| v5             | ^8.0    | ^8.4   | - |
| v4             | ^7.0    | ^8.2   | - |
| v3             | ^6.0    | ^8.1   | [migrate-v2-to-v3.md](migrate-v2-to-v3.md) |
| v2             | ^6.0    | ^8.0   | - |
| v1             | ^4.3 \|\| ^5.0 | ^7.1 | - |

v3/v4/v5 have no dedicated migration guide: each only bumped the minimum Symfony/PHP version
(enforced by Composer itself) with cosmetic code changes, no consumer-facing API change.

> Since 6.0.0, `ExceptionFormatService` has breaking changes and no longer supports being
> extended — see [migrate-v5-to-v6.md](migrate-v5-to-v6.md). If you rely on the pre-6.0
> subclassing API (`getArray`, `addKeyToErrorArray`, `$jsonKeys`, ...), use the v5 branch instead.

> Since 7.0.0, the bundle exposes a real semantic configuration tree under the
> `openium_symfony_toolkit` key (see the Configuration section below), and several classes are
> deprecated — see [migrate-v6-to-v7.md](migrate-v6-to-v7.md) for the full list and what to do
> about each.

Usage
-----

### Configuration

All bundle options are declared under the `openium_symfony_toolkit` key. Every key below shows
its default value:

~~~yaml
openium_symfony_toolkit:
    uploads:
        public_dir: '%kernel.project_dir%/public'
        dir_name: 'uploads'
    kernel_exception_listener:
        enabled: false
        path: '/api'
        class: 'Openium\SymfonyToolKitBundle\EventListener\PathKernelExceptionListener'
~~~

- `uploads.public_dir` / `uploads.dir_name`: used by `FileUploaderService` (see below).
- `kernel_exception_listener.*`: used by `PathExceptionListener` (see below).

### AbstractCommand

> **Deprecated since 7.0, will be removed in 8.0.** The `--nl` option reimplements what Symfony's
> console component already provides natively: use the standard `-q`/`--quiet` flag and
> `OutputInterface::isQuiet()` instead of `prepareExecute()`/`writeMessage()`.

Base class for commands, adding a `--nl` option to disable log output and a `writeMessage()`
helper that respects it. Call `prepareExecute($input, $output)` at the start of `execute()`, then
use `writeMessage()` instead of `$output->writeln()`.

### AbstractController

Add 2 protected methods for controllers :

- getContentFromRequest: get json body from request
- getMultipartContent: get json body from multipart request
- extractObjectFromString: get json from string (used by getContentFromRequest and getMultipartContent)
- getFilterParameters: get filter query parameters from request

### Filters

Add a class containing filters from the query parameters.

To get filters, use getFilterParameters in AbstractController.

You can also use FilterRepositoryUtils->applyFilters() to define the sort, limit and offset in queries.

Notes on filters :
- if the page parameter is passed but not the limit parameter, the limit is set to 10
- if order-by parameter is passed but not order parameter, order is set to ASC

### PaginatedResult

The PaginatedResult allow you to have a formatted result for endpoints who used filters. 

### ServerService

> **Deprecated since 7.0, will be removed in 8.0.** `getBasePath()` only ever duplicated
> Symfony's own `Request::getSchemeAndHttpHost()`. Inject `RequestStack` (or `Request` directly)
> and call `$request->getSchemeAndHttpHost() . '/'` instead.

This service provide a way to get the actual server url.

Add ServerServiceInterface with dependencies injection and use the method `getBasePath()` from it.

~~~php
    function myFunc(ServerServiceInterface $serverService): mixed
    {
        // ...
        $basePath = $serverService->getBasePath();
        // ...
    }
~~~

---

### FileUploaderService

This service help you to manage an entity with a uploaded **file reference.
Caution, this service (in its single-field form below) allow only one upload property**.

First, implements your entity with WithUploadInterface.

Next, you can use the WithUploadTrait, which contains certain methods and properties required by the interface.

Then inject into your entity event listener the FileUploaderServiceInterface service.

Finally, use the service like that :

- _prepareUploadPath_ in prePersist and preUpdate to set entity properties before persist in database

~~~php
    $fileUploaderService->prepareUploadPath($entity);
~~~

- _uploadEntity_ postPersist and postUpdate to move upload to right directory

~~~php
    $fileUploaderService->uploadEntity($entity);
~~~

- _removeUpload_ postPersist and postRemove to delete upload file

~~~php
    $fileUploaderService->removeUpload($entity);
~~~

#### Custom filename generation

Since 7.0.0, the filename generation (previously hardcoded in `FileUploaderService::getPath()`) is
delegated to an overridable `UploadFilenameGeneratorInterface` service, inspired by
VichUploaderBundle's namers. The default `RandomUploadFilenameGenerator` reproduces the pre-7.0
behavior (a random 32-char basename, or the given `$imageName`, suffixed with the guessed
extension). To use a custom naming strategy, implement the interface and override the
`openium_symfony_toolkit.upload_filename_generator` service, the same way as
`ExceptionFormatUtils` (see the ExceptionFormatService section):

```yaml
    openium_symfony_toolkit.upload_filename_generator:
        class: App\Service\MyUploadFilenameGenerator
        public: true
```

#### Multiple upload fields per entity

Since 7.0.0, an entity that needs more than one upload property is no longer limited to
`WithUploadInterface`'s single fixed pair of properties. Implement the additive
`MultiUploadInterface` (with the `MultiUploadTrait` helper) instead: every method takes a
`$field` key identifying which upload slot it operates on. `WithUploadInterface`/`WithUploadTrait`
are untouched and remain the right choice for an entity with a single upload property.

~~~php
class Product implements MultiUploadInterface
{
    use MultiUploadTrait;

    public function getUploadsDir(string $field): string
    {
        return match ($field) {
            'thumbnail' => 'products/thumbnails',
            'gallery' => 'products/gallery',
        };
    }
}
~~~

Use `prepareMultiUploadPath($entity, $field)` / `uploadMultiEntity($entity, $field)` /
`removeMultiUpload($entity, $field)` instead of their single-field counterparts, once per field,
in the same lifecycle hooks.

---

### AtHelper

Allow you to execute some commands with Unix AT command.

> Since 7.0.0, `$cmd` and `$path` are passed through `escapeshellarg()` before being interpolated
> into the shell command run by `createAtCommand()` / `createAtCommandFromPath()` (previously,
> unescaped values allowed shell command injection). `$cmd` is treated as literal data given to
> `at`, not as a shell snippet: if you relied on shell metacharacters (`;`, `|`, `` ` ``, `$(...)`,
> quotes, ...) in `$cmd` being interpreted by the shell, wrap your command in `sh -c '...'`
> yourself before passing it in.

- To create a new AT job :

~~~php
    
    // $cmd command to execute
    // $timestamp when the command will be executed
    // $path path where the at creation command will be executed
    // &$result result of at
    $output = $atHelper->createAtCommandFromPath($cmd, $timestamp, $path, $result);
    
    // get at job number
    $jobNumber = $atHelper->extractJobNumberFromAtOutput($output);
~~~

- to remove existing AT job, save the jobNumber from extractJobNumberFromAtOutput() method and use it with
  removeAtCommand() method.

~~~php
    $removeSuccess = $atHelper->removeAtCommand($jobNumber);
~~~

---

### DoctrineExceptionHandlerService

Transform doctrine exceptions into HttpException.

In most cases, the exception will be a BadRequestHttpException.

But if the database error refers to a conflict, the method will throw a ConflictHttpException.

> Since 7.0.0, exception matching uses `instanceof` instead of an exact-class comparison, so a
> subclass of any handled Doctrine exception is now recognized too (it previously fell through
> and was rethrown as-is).

> Since 7.0.0, the constructor also requires a `Symfony\Contracts\Translation\TranslatorInterface`
> (the bundle's `openium_symfony_toolkit.doctrine_exception_handler` service definition already
> passes `@translator`, so consumers using the service via DI need no change). The default
> messages are now translation ids resolved from the `openium_symfony_toolkit` domain (English and
> French catalogs are shipped in `Resources/translations/`). The `set*Message()` setters still
> accept a literal string as before: `trans()` falls back to returning an unknown id unchanged, so
> a custom message set that way is used verbatim regardless of the current locale. If you
> instantiate `DoctrineExceptionHandlerService` directly (not via the container), update the call
> site to pass a translator.

To use it, you need to inject DoctrineExceptionHandlerServiceInterface service.

~~~php
        try {
            $this->em->persist($y);
            $this->em->flush();
        } catch (\Exception $e) {
            $this->doctrineExceptionHandlerService->toHttpException($e);
        }
~~~

Work fine with doctrine exceptions but not with other/custom exceptions

---

### ExceptionFormatService

Transform exceptions to a JSON `Response`, built from a typed DTO
(`ExceptionDTO`/`DevExceptionDTO`) serialized through the Symfony Serializer:

```json
{
    "status_code": 404,
    "status_text": "Not Found",
    "message": "..."
}
```

Outside the `prod` environment, the response also includes `trace` and `previous`
(the wrapped exception's code/message), **except** when the exception is a
`Symfony\Component\Security\Core\Exception\AuthenticationException` (or wraps/is wrapped by
one): in that case `previous` is always omitted, and the status code is forced to `401
Unauthorized` instead of falling back to `500`. This avoids leaking whether a given account
exists when Symfony's `AuthenticatorManager` wraps a `UserNotFoundException` inside a
`BadCredentialsException`.

#### Breaking changes since 6.0.0

`ExceptionFormatService` was rewritten to use DTOs and no longer supports being extended.
The following public methods/property have been removed:
`getArray`, `addKeyToErrorArray`, `getStatusCode`, `getStatusText`, `genericExceptionResponse`,
`$jsonKeys`. `ExceptionFormatServiceInterface` now only exposes `formatExceptionResponse`.
The constructor signature also changed: it now takes `SerializerInterface`,
`ExceptionFormatUtilsInterface` and the kernel environment string, instead of the kernel.

If you want to customize the code/text/message of the response, override the
`ExceptionFormatUtils` service instead (see below) — that is now the supported extension
point, replacing the old subclassing pattern.

#### Example

If you need full control over the response, implement `ExceptionFormatServiceInterface`
yourself (`ExceptionFormatService` can no longer be extended — see breaking changes above)
and override the `openium_symfony_toolkit.exception_format` service in your `services.yaml`:

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
        // Define your own response entirely
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

The exception you formatted is going to be used in the method `formatExceptionResponse`.
This way you can handle a custom exception.
~~~php
    $response = $this->exceptionFormat->formatExceptionResponse($exception);
~~~

Most of the time you don't need to replace the whole service: override the
`ExceptionFormatUtils` service instead, which only defines the text, message and code of the
exception. The new class must implement `ExceptionFormatUtilsInterface`, and be registered
under the same service id in your project:

```yaml
    openium_symfony_toolkit.exception_format_utils:
      class: App\Service\ExceptionFormatUtils
      public: true
```

---

### PathExceptionListener

The listener catch kernel exceptions and transform them into HttpException thanks to ExceptionFormatService.

It is disabled by default and have this configuration (see the Configuration section above) :

~~~yaml
openium_symfony_toolkit:
    kernel_exception_listener:
        enabled: false
        path: '/api'
        class: 'Openium\SymfonyToolKitBundle\EventListener\PathExceptionListener'
~~~

it use the ExceptionFormatService to format automatically the kernel exceptions
only for the routes defined in the `kernel_exception_listener.path` option

Caution, this listener was enabled by default before version 4.3 of the bundle.

---

### MemoryUtils

Use to display memory usage or juste bytes into human-readable string.

~~~php
$str = MemoryUtils::convert(1024);
// $str = '1 kb';

$phpMemory = MemoryUtils::getMemoryUsage();
// apply convert() to actual php memory usage
~~~

### ContentExtractorService

> **Deprecated since 7.0, will be removed in 8.0.** Validate request payloads with a typed DTO
> and Symfony's Validator instead (e.g. via `#[MapRequestPayload]`), rather than manually pulling
> and type-checking individual keys out of an array.

Use to extract types data from array with specific key

~~~php
    $myString = ContentExtractorUtils::getString($content, $key);
~~~

With option to allow null value, set a default value and set if value is required.

List of methods :

- getString
- getBool
- getInt
- getFloat
- getDateTimeInterface
- getArray

All methods throws 400 HTTP error with correct message if the value is missing or is not with the right type (depends of
parameters)

Behind all these methods are control methods.

List of check methods :

- checkKeyExists
- checkKeyIsString
- checkKeyIsBoolean
- checkKeyIsInt
- checkKeyIsFloat
- checkKeyIsArray

Methods checkKeyIs{type} use checkKeyExists().

All the methods in this class are static.

### DateStringUtils

> **Deprecated since 7.0, will be removed in 8.0.** The format guess based on string
> length/suffix is fragile. Use Symfony Serializer's `DateTimeNormalizer`, or plain
> `new DateTimeImmutable($dateString)` (which already parses ATOM/ISO8601 and most common
> formats), instead.

Provide a static method to get date from string :

~~~php
public static function getDateTimeFromString(
    string $dateString,
    ?string $format = null,
    ?DateTimeZone $timeZone = null
): DateTime | false
~~~

If no format has been supplied, the method attempts to determine the correct date format.

Two formats can be detected:

- ATOM `'Y-m-d\TH:i:sP'`
- ISO8601 `'Y-m-d\TH:i:sO'`

If no format is detected, the method falls back to the `'Y-m-d'` format and return false if the string can't be parse as
DateTime.

### DebugUtils

Provide static methods to help in debugging.

#### Doctrine Query Debug

Allow to log doctrine queries with parameters and types.

Activate logging for Doctrine :
~~~yaml
doctrine:
    dbal:
        logging: '%kernel.debug%'
~~~

And in the repository, set the logger for the entity manager (at the beginning of the method for example) :
~~~php
    DebugUtils::setDoctrineQueryLogger($entityManager);
~~~

Then log the query (use it just before execute the query) :
~~~php
    DebugUtils::logDoctrineQuery($query);
~~~
