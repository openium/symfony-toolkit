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
    final public const string MESSAGE = "Wrong parameters, missing parameter(s)";

    final public const int CODE = 2_015_151_203;

    public function __construct(
        string $key = "",
        string $message = self::MESSAGE,
        int $code = self::CODE,
        ?Exception $exception = null
    ) {
        parent::__construct($key, $message, $code, $exception);
    }
}
