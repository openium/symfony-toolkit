<?php

namespace Openium\SymfonyToolKitBundle\Exception;

use Exception;

/**
 * Class ContentExtractorMissingParameterException
 *
 * @package Openium\SymfonyToolKitBundle\Exception
 */
class ContentExtractorMissingParameterException extends ContentExtractorException
{
    public const MESSAGE = "Wrong parameters, missing parameter(s)";
    public const CODE = 2015151203;

    public function __construct(
        string $key = "",
        string $message = self::MESSAGE,
        int $code = self::CODE,
        Exception $previous = null
    ) {
        parent::__construct($key, $message, $code, $previous);
    }
}
