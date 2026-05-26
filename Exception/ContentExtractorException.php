<?php

namespace Openium\SymfonyToolKitBundle\Exception;

use Exception;
use Throwable;

/**
 * Class MissingContentException
 *
 * @package Openium\SymfonyToolKitBundle\Exception
 */
abstract class ContentExtractorException extends Exception
{
    public function __construct(
        protected ?string $key = "",
        string $message = "",
        int $code = 0,
        ?Throwable $throwable = null
    ) {
        parent::__construct($message, $code, $throwable);
    }

    public function getKey(): ?string
    {
        return $this->key;
    }
}
