<?php

declare(strict_types=1);

namespace Openium\SymfonyToolKitBundle\DTO;

class DevExceptionDTO extends ExceptionDTO
{
    /**
     * @param array<int, array<string, mixed>> $trace
     */
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
