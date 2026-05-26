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
    final public const string MESSAGE = "Property must be a boolean";

    final public const int CODE = 2_015_151_204;

    public function __construct(
        string $key = "",
        string $message = self::MESSAGE,
        int $code = self::CODE,
        ?Exception $exception = null
    ) {
        parent::__construct($key, $message, $code, $exception);
    }
}
