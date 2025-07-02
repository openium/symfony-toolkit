<?php

namespace Openium\SymfonyToolKitBundle\Utils;

use Exception;

interface ExceptionFormatUtilsInterface
{
    public function getStatusCode(Exception $exception): int;

    public function getStatusText(Exception $exception): string;
}
