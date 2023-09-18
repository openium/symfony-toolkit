Symfony toolkit
===============

This symfony bundle provides abstractions for many common cases.

Installation
------------

Open a command console, enter your project directory and execute:

```bash
$ composer require openium/symfony-toolkit
```

Usage
-----

### ServerService

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
Caution, this service allow only one upload property**.

First, implements your entity with WithUploadInterface.

Next, you can use the WithUploadTrait, which contains certain methods and properties required by the interface.

Then inject into your entity event listener the FileUploaderServiceInterface service.

Finally, use the service like that :

- _prepareUploadPath_ in prePersist and preUpdate to set entity properties before persist in database

~~~php
    $fileUploaderService->prepareUploadPath($entity);
~~~

- _upload_ postPersist and postUpdate to move upload to right directory

~~~php
    $fileUploaderService->upload($entity);
~~~

- _removeUpload_ postPersist and postRemove to delete upload file

~~~php
    $fileUploaderService->removeUpload($entity);
~~~

---

### AtHelper

Allow you to execute some commands with Unix AT command.

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

Transform exceptions to json Response.
Exception's response is now generic, which means you can give your own code, text and message to the response.<br>

Firstly, to override the service of the bundle, you have to add your own service in config/services.yaml.  
  For example:
```yaml
    openium_symfony_toolkit.exception_format:
        class: App\Service\ExceptionFormatService
        arguments:
            - '@kernel'
        public: true
  ```

Then, you need to add a method `genericExceptionResponse` in your service which will be defining each part of the exception: `$code, $text, $message`.

#### Example
```php
<?php
namespace App\Service;

use Openium\SymfonyToolKitBundle\Service\ExceptionFormatService as BaseExceptionFormatService;
use Openium\SymfonyToolKitBundle\Service\ExceptionFormatServiceInterface;

class ExceptionFormatService extends BaseExceptionFormatService implements ExceptionFormatServiceInterface {

    public function genericExceptionResponse(Exception $exception): array
    {
        // You define conditions and exceptions you want here 
        if ($exception instanceof MyException) {
            $code = 123;
            $text = 'This is my custom exception text';
            $message = $text;
            return [$code, $text, $message];
        }
        // Or use the default method in the toolkit
        return parent::genericExceptionResponse($exception);
    }
}
```
The exception you formatted is going to be used in the method `formatExceptionResponse`.
This way you can handle a custom exception.
~~~php
    $response = $this->exceptionFormat->formatExceptionResponse($exception);
~~~

---

### PathExceptionListener

The listener catch kernel exceptions and transform them into HttpException thanks to ExceptionFormatService.

It is enabled by default and have this configuration :

~~~yaml
parameters:
  openium_symfony_toolkit.kernel_exception_listener_enable: true
  openium_symfony_toolkit.kernel_exception_listener_path: '/api'
  openium_symfony_toolkit.kernel_exception_listener_class: 'Openium\SymfonyToolKitBundle\EventListener\PathExceptionListener'
~~~

it use the ExceptionFormatService to format automatically the kernel exceptions
only for the routes defined in exception_listener_path parameter

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
