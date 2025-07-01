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
    final public const MESSAGE = "Incorrect content format";

    final public const CODE = 2_015_151_202;

    /**
     * MissingContentException constructor.
     *
     * @param Exception|null $previous
     * @param array<string, string> $headers
     */
    public function __construct(Exception $previous = null, array $headers = [])
    {
        parent::__construct(self::MESSAGE, $previous, self::CODE, $headers);
    }
}
