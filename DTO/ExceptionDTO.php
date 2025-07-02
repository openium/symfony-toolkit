<?php

namespace Openium\SymfonyToolKitBundle\DTO;

use Symfony\Component\Serializer\Attribute\SerializedName;

class ExceptionDTO
{
    public function __construct(
        #[SerializedName('status_code')]
        public readonly int $code,
        #[SerializedName('status_text')]
        public readonly string $text,
        #[SerializedName('message')]
        public readonly string $message
    ) {
    }
}
