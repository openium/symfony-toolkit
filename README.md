Installation
============


Open a command console, enter your project directory and execute:

```bash
$ composer require openium/symfony-toolkit
```

Usage
=====

## DoctrineExceptionHandlerService

~~~php
        try {
            $this->em->persist($y);
            $this->em->flush();
        } catch (\Exception $e) {
            $this->doctrineExceptionHandlerService->toHttpException($e);
        }
~~~

## ExceptionFormatService

*Example for path with /api*

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

## ServerService

just use it if you need server url

## FileUploaderService

implements WithUploadInterface.

You can use WithUploadTrait for implements some methods and properties

and use the service like that :

~~~php
    $fileUploaderService->prepareUploadPath($entity);
    $fileUploaderService->upload($entity);
~~~