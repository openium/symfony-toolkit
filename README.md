# Symfony toolkit


## Installation


Open a command console, enter your project directory and execute:

```bash
$ composer require openium/symfony-toolkit
```

## Usage

### ServerService

You can get the actual server url with the method `getBasePath()

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

### AtHelper

Allow you to execute some commande with Unix At command

#### Example

~~~php
    $result = '';
    $cmb = 'bin/console app:some:thing';
    $output = createAtCommand($cmd, time(), $result);
~~~

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

### ExceptionFormatService

Transform exceptions to json

#### Example for automaticaly transform exceptions

with example works with /api path

Create an ExceptionListener :

~~~php
<?php

namespace App\EventListener;

use Openium\SymfonyToolKitBundle\Service\ExceptionFormatServiceInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\GetResponseForExceptionEvent;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\Security\Core\Event\AuthenticationFailureEvent;

/**
 * Class ExceptionListener
 *
 * @package App\EventListener
 */
class ExceptionListener
{
    /**
     * @var ExceptionFormatServiceInterface
     */
    protected $exceptionFormat;

    /**
     * ExceptionListener constructor.
     *
     * @param ExceptionFormatServiceInterface $exceptionFormat
     */
    public function __construct(ExceptionFormatServiceInterface $exceptionFormat)
    {
        $this->exceptionFormat = $exceptionFormat;
    }

    /**
     * @param GetResponseForExceptionEvent $event
     *
     * @return void
     */
    public function onKernelException(GetResponseForExceptionEvent $event)
    {
        $exception = $event->getException();
        if (strpos($event->getRequest()->getRequestUri(), '/api') !== false) {
            $response = $this->exceptionFormat->formatExceptionResponse($exception);
            $event->setResponse($response);
        }
    }

    /**
     * @param AuthenticationFailureEvent $event
     *
     * @throws HttpException
     *
     * @return void
     */
    public function onSymfonyAuthenticationFailure(AuthenticationFailureEvent $event)
    {
        if (strpos(
            $event->getAuthenticationException()->getFile(),
            'Symfony/Component/Security/Core/Authentication/Provider/UserAuthenticationProvider.php'
        ) === false) {
            throw new HttpException(
                Response::HTTP_UNAUTHORIZED,
                $event->getAuthenticationException()
                ->getMessage()
            );
        }
    }
}

~~~

add in service.yaml

~~~yaml
services:

    App\EventListener\ExceptionListener:
        tags:
            - { name: kernel.event_listener, event: kernel.exception, method: onKernelException }
            - { name: kernel.event_listener, event: security.authentication.failure, method: onSymfonyAuthenticationFailure }
~~~
