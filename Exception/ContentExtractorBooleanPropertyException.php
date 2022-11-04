<?php

namespace Openium\SymfonyToolKitBundle\Exception;

use Exception;

/**
 * Class MissingContentException
 *
 * @package Openium\SymfonyToolKitBundle\Exception
 */
class ContentExtractorBooleanPropertyException extends ContentExtractorException
{
    public const MESSAGE = "Property must be a boolean";
    public const CODE = 2015151204;

    public function __construct(
        string $key = "",
        string $message = self::MESSAGE,
        int $code = self::CODE,
        Exception $previous = null
    ) {
        parent::__construct($key, $message, $code, $previous);
    }
}
