Symfony toolkit
===============

Installation
------------

Open a command console, enter your project directory and execute:

```bash
$ composer require openium/symfony-toolkit
```

Usage
-----

### ServerService

You can get the actual server url with the method `getBasePath()`

---

### FileUploaderService

implements an entity with WithUploadInterface.

You can use WithUploadTrait for implements some methods and properties

Prepare entity properties
~~~php
    $fileUploaderService->prepareUploadPath($entity);
~~~

Move upload to right directory
~~~php
    $fileUploaderService->upload($entity);
~~~

---

### AtHelper

Allow you to execute some commande with Unix At command

#### Example

~~~php
    $result = '';
    $cmb = 'bin/console app:some:thing';
    
    // Create at
    $output = $atHelper->createAtCommand($cmd, time(), $result);
    
    // get at job number
    $jobNumber = $atHelper->extractJobNumberFromAtOutput($output);
    
    // remove at job
    $removeSuccess = $atHelper->removeAtCommand($jobNumber);
~~~


---

### DoctrineExceptionHandlerService

Transform doctrine exceptions to HttpException

#### Example

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

Transform exceptions to json Response

#### Example

~~~php
    $response = $this->exceptionFormat->formatExceptionResponse($exception);
~~~

---

### AuthenticationFailureListener

The listener catch authentication failure.
It is enabled by default and have this configuration :

~~~yaml
parameters:
    openium_symfony_toolkit.authentication_failure_listener_enable: true
    openium_symfony_toolkit.authentication_failure_listener_class: 'Openium\SymfonyToolKitBundle\EventListener\AuthenticationFailureListener'
~~~

Throw HttpException 401 when an AuthenticationFailureEvent is throw.
Except for 'Symfony/Component/Security/Core/Authentication/Provider/UserAuthenticationProvider.php'

---

### PathExceptionListener

The listener catch kernel exceptions.
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

Use to display memory usage or juste bytes into human readable string

~~~php
$str = MemoryUtils::convert(1024);
// $str = '1 kb';

$phpMemory = MemoryUtils::getMemoryUsage();
// use convert() with actual php memory usage
~~~


### ContentExtractorService

Use to extract types data from array with specific key

~~~php
    $myString = $this->contentExtractor->getString($content, $key);
~~~

With option to allow null value, set a default value and set if value is required.

List of methods :
- getString
- getBool
- getInt
- getFloat
- getDateTimeInterface
- getArray

All methods throws 400 HTTP error with correct message if the value is missing or is not with the right type (depends of parameters)


### DateStringUtils

Provide a static method to get date from string :

~~~php
public static function getDateTimeFromString(
    string $dateString,
    ?string $format = null,
    ?DateTimeZone $timeZone = null
): DateTime | false
~~~

Date format can be :
- ATOM `'Y-m-d\TH:i:sP'`
- ISO8601 `'Y-m-d\TH:i:sO'`
- `'Y-m-d'`

return false if the string can't be parse as DateTime.
