<?php

namespace Openium\SymfonyToolKitBundle\DTO;

class DevExceptionDTO extends ExceptionDTO
{
    public function __construct(
        int $code,
        string $text,
        string $message,
        public readonly array $trace = [],
        public readonly ?DevPreviousExceptionDTO $previous = null
    ) {
        parent::__construct($code, $text, $message);
    }
}
