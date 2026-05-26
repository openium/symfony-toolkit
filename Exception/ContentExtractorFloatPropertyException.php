<?php

namespace Openium\SymfonyToolKitBundle\Exception;

use Exception;

/**
 * Class MissingContentException
 *
 * @package Openium\SymfonyToolKitBundle\Exception
 */
class ContentExtractorFloatPropertyException extends ContentExtractorException
{
    final public const string MESSAGE = "Property must be a float";

    final public const int CODE = 2_015_151_206;

    public function __construct(
        string $key = "",
        string $message = self::MESSAGE,
        int $code = self::CODE,
        ?Exception $exception = null
    ) {
        parent::__construct($key, $message, $code, $exception);
    }
}
