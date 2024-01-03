<?php

namespace Openium\SymfonyToolKitBundle\Tests\Service;

use Exception;
use Openium\SymfonyToolKitBundle\Service\ExceptionFormatService;

/**
 * Class ExceptionFormatServiceTest
 *
 * @package Openium\SymfonyToolKitBundle\Test\Service
 */
#[\PHPUnit\Framework\Attributes\CodeCoverageIgnore]
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
