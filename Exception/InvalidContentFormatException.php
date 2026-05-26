<?php

namespace Openium\SymfonyToolKitBundle\Exception;

use Exception;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

/**
 * Class InvalidContentFormatException
 *
 * @package Openium\SymfonyToolKitBundle\Exception
 */
class InvalidContentFormatException extends BadRequestHttpException
{
    final public const string MESSAGE = "Incorrect content format";

    final public const int CODE = 2_015_151_202;

    /**
     * MissingContentException constructor.
     *
     * @param array<string, string> $headers
     */
    public function __construct(?Exception $exception = null, array $headers = [])
    {
        parent::__construct(self::MESSAGE, $exception, self::CODE, $headers);
    }
}
