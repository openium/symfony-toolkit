<?php

namespace Openium\SymfonyToolKitBundle\Exception;

use Exception;

/**
 * Class MissingContentException
 *
 * @package Openium\SymfonyToolKitBundle\Exception
 */
class ContentExtractorStringPropertyException extends ContentExtractorException
{
    final public const MESSAGE = "Property must be a string";
    final public const CODE = 2_015_151_209;

    public function __construct(
        string $key = "",
        string $message = self::MESSAGE,
        int $code = self::CODE,
        Exception $previous = null
    ) {
        parent::__construct($key, $message, $code, $previous);
    }
}
