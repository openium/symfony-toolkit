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
 * Class InvalidContentFormatException
 *
 * @package Openium\SymfonyToolKitBundle\Exception
 */
class InvalidContentFormatException extends BadRequestHttpException
{
    public const MESSAGE = "Incorrect content format";
    public const CODE = 2015151202;

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