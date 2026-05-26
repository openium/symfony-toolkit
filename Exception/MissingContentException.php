<?php

namespace Openium\SymfonyToolKitBundle\Exception;

use Exception;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

/**
 * Class MissingContentException
 *
 * @package Openium\SymfonyToolKitBundle\Exception
 */
class MissingContentException extends BadRequestHttpException
{
    final public const string MESSAGE = "Missing content";

    final public const int CODE = 2_015_151_201;

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
