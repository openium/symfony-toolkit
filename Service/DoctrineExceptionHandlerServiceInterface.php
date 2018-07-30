<?php

/**
 * DoctrineExceptionHandlerServiceInterface
 *
 * PHP Version 7.1
 *
 * @package  Openium\SymfonyToolKitBundle\Service
 * @author   Thomas LEDUC <thomaslmoi15@hotmail.fr>, Alexandre Caillot <a.caillot@openium.fr>
 * @link     https://openium.fr/
 */

namespace Openium\SymfonyToolKitBundle\Service;

use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\HttpKernel\Exception\ConflictHttpException;

/**
 * Interface DoctrineExceptionHandlerServiceInterface
 *
 * @package  Openium\SymfonyToolKitBundle\Service
 */
interface DoctrineExceptionHandlerServiceInterface
{
    public function log(\Throwable $throwable);

    /**
     * Catch & Process the throwable
     *
     * @param \Throwable $throwable
     *
     * @throws BadRequestHttpException
     * @throws ConflictHttpException
     */
    public function toHttpException(\Throwable $throwable);
}
