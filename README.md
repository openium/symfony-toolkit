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
