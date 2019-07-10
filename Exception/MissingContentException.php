<?php

/**
 * PHP Version 7.1, 7.2
 *
 * @package  Openium\SymfonyToolKitBundle\Exception
 * @author   Openium <contact@openium.fr>
 * @license  Openium All right reserved
 * @link     https://www.openium.fr/
 */

namespace Openium\SymfonyToolKitBundle\Exception;

use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

/**
 * Class MissingContentException
 *
 * @package Openium\SymfonyToolKitBundle\Exception
 */
class MissingContentException extends BadRequestHttpException
{
    public const MESSAGE = "Missing content";
    public const CODE = 2015151201;

    /**
     * MissingContentException constructor.
     *
     * @param \Exception|null $previous
     * @param array $headers
     */
    public function __construct(\Exception $previous = null, array $headers = array())
    {
        parent::__construct(self::MESSAGE, $previous, self::CODE, $headers);
    }
}
