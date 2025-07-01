<?php

namespace Openium\SymfonyToolKitBundle\Tests\Service;

use Exception;
use Openium\SymfonyToolKitBundle\Service\ExceptionFormatService;
use PHPUnit\Framework\Attributes\CoversNothing;

/**
 * Class ExceptionFormatServiceTest
 *
 * @package Openium\SymfonyToolKitBundle\Test\Service
 */
#[CoversNothing]
class ExceptionFormatExtendService extends ExceptionFormatService
{
    protected array $jsonKeys = [
        'code' => 'statusCode',
        'text' => 'statusText',
        'message' => 'message',
    ];

    /**
     * @return array [code, text, message]
     */
    public function genericExceptionResponse(Exception $exception): array
    {
        $code = 400;
        $text = "bad request";
        $message = "bad request";
        return [$code, $text, $message];
    }

    public function addKeyToErrorArray(array $error, Exception $exception): array
    {
        $error['exception'] = $exception::class;
        return $error;
    }
}
