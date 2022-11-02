<?php

namespace Openium\SymfonyToolKitBundle\Exception;

use Exception;

/**
 * Class MissingContentException
 *
 * @package Openium\SymfonyToolKitBundle\Exception
 */
class ContentExtractorIntegerPropertyException extends ContentExtractorException
{
    public const MESSAGE = "Property must be an integer";
    public const CODE = 2015151205;

    public function __construct(
        string $message = self::MESSAGE,
        int $code = self::CODE,
        Exception $previous = null
    ) {
        parent::__construct($message, $code, $previous);
    }
}
