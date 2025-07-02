<?php

namespace Openium\SymfonyToolKitBundle\DTO;

class DevPreviousExceptionDTO
{
    public function __construct(
        public readonly string $code,
        public readonly string $message,
    ) {
    }
}
