<?php

declare(strict_types=1);

namespace Openium\SymfonyToolKitBundle\DTO;

class DevPreviousExceptionDTO
{
    public function __construct(
        public readonly int $code,
        public readonly string $message,
    ) {
    }
}
