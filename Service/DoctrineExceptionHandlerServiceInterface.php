<?php

/**
 * DoctrineExceptionHandlerServiceInterface
 *
 * PHP Version 7.1, 7.2
 *
 * @package  Openium\SymfonyToolKitBundle\Service
 * @author   Openium <contact@openium.fr>
 * @license  Openium All right reserved
 * @link     https://www.openium.fr/
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

    /**
     * Log an exception information for debug
     *
     * @param \Throwable $throwable
     */
    public function log(\Throwable $throwable);

    /**
     * toHttpException
     * Catch & Process the throwable
     *
     * @param \Throwable $throwable
     *
     * @throws BadRequestHttpException
     * @throws ConflictHttpException
     */
    public function toHttpException(\Throwable $throwable);
}
