<?php

namespace Openium\SymfonyToolKitBundle\Tests\Service;

use Exception;
use Openium\SymfonyToolKitBundle\Service\ExceptionFormatService;
use PHPUnit\Framework\Attributes\CoversNothing;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\HttpKernel\Exception\TooManyRequestsHttpException;
use Symfony\Component\HttpKernel\Exception\UnauthorizedHttpException;
use Symfony\Component\HttpKernel\KernelInterface;
use Symfony\Component\Security\Core\Exception\BadCredentialsException;
use Symfony\Component\Security\Core\Exception\UserNotFoundException;
use TypeError;

/**
 * Class ExceptionFormatServiceTest
 *
 * @package Openium\SymfonyToolKitBundle\Test\Service
 */
#[CoversNothing]
class ExceptionFormatServiceTest extends TestCase
{

    public function testGetStatusCodeWithHttpException(): void
    {
        $testKernel = $this->createStub(KernelInterface::class);
        $httpException = new HttpException(Response::HTTP_FORBIDDEN);
        $exceptionFormatService = new ExceptionFormatService($testKernel);
        self::assertTrue($exceptionFormatService instanceof ExceptionFormatService);
        $statusCode = $exceptionFormatService->getStatusCode($httpException);
        self::assertTrue(is_int($statusCode));
        self::assertEquals($statusCode, Response::HTTP_FORBIDDEN);
    }

    public function testGetStatusCodeWithAccessDeniedException(): void
    {
        $testKernel = $this->createStub(KernelInterface::class);
        $exception = new Exception();
        $exceptionFormatService = new ExceptionFormatService($testKernel);
        self::assertTrue($exceptionFormatService instanceof ExceptionFormatService);
        $statusCode = $exceptionFormatService->getStatusCode($exception);
        self::assertTrue(is_int($statusCode));
        self::assertEquals($statusCode, Response::HTTP_INTERNAL_SERVER_ERROR);
    }

    public function testGetStatusTextWith402HttpException(): void
    {
        $testKernel = $this->createStub(KernelInterface::class);
        $httpException = new HttpException(Response::HTTP_PAYMENT_REQUIRED);
        $exceptionFormatService = new ExceptionFormatService($testKernel);
        self::assertTrue($exceptionFormatService instanceof ExceptionFormatService);
        $text = $exceptionFormatService->getStatusText($httpException);
        self::assertTrue(is_string($text));
        self::assertEquals($text, 'Request Failed');
    }

    public function testGetStatusTextWith404HttpException(): void
    {
        $testKernel = $this->createStub(KernelInterface::class);
        $httpException = new HttpException(Response::HTTP_NOT_FOUND);
        $exceptionFormatService = new ExceptionFormatService($testKernel);
        self::assertTrue($exceptionFormatService instanceof ExceptionFormatService);
        $text = $exceptionFormatService->getStatusText($httpException);
        self::assertTrue(is_string($text));
        self::assertEquals($text, Response::$statusTexts[Response::HTTP_NOT_FOUND]);
    }

    public function testGetStatusTextWithUnknowHttpException(): void
    {
        $testKernel = $this->createStub(KernelInterface::class);
        $httpException = new HttpException(456987);
        $exceptionFormatService = new ExceptionFormatService($testKernel);
        self::assertTrue($exceptionFormatService instanceof ExceptionFormatService);
        $text = $exceptionFormatService->getStatusText($httpException);
        self::assertTrue(is_string($text));
        self::assertEquals($text, Response::$statusTexts[Response::HTTP_INTERNAL_SERVER_ERROR]);
    }

    public function testGetArrayWith404HttpException(): void
    {
        $httpException = new HttpException(Response::HTTP_NOT_FOUND);
        $testKernel = $this->getMockBuilder(KernelInterface::class)
            ->disableOriginalConstructor()
            ->getMock();
        $testKernel->expects(self::once())
            ->method('getEnvironment')
            ->willReturn("prod");
        $exceptionFormatService = new ExceptionFormatService($testKernel);
        self::assertTrue($exceptionFormatService instanceof ExceptionFormatService);
        $result = $exceptionFormatService->getArray($httpException);
        self::assertTrue(is_array($result));
        self::assertTrue(array_key_exists('status_code', $result));
        self::assertEquals($result['status_code'], 404);
        self::assertTrue(array_key_exists('status_text', $result));
        self::assertEquals($result['status_text'], Response::$statusTexts[Response::HTTP_NOT_FOUND]);
        self::assertTrue(array_key_exists('message', $result));
        self::assertFalse(array_key_exists('trace', $result));
        self::assertFalse(array_key_exists('previous', $result));
    }

    public function testGetArrayWith404HttpExceptionAndDevEnd(): void
    {
        $previous = new Exception('previous exception', 123456, null);
        $testKernel = $this->getMockBuilder(KernelInterface::class)
            ->disableOriginalConstructor()
            ->getMock();
        $testKernel->expects(self::once())
            ->method('getEnvironment')
            ->willReturn("dev");
        $httpException = new HttpException(Response::HTTP_NOT_FOUND, 'message exception', $previous);
        $exceptionFormatService = new ExceptionFormatService($testKernel);
        self::assertTrue($exceptionFormatService instanceof ExceptionFormatService);
        $result = $exceptionFormatService->getArray($httpException);
        self::assertTrue(is_array($result));
        self::assertTrue(array_key_exists('status_code', $result));
        self::assertEquals($result['status_code'], 404);
        self::assertTrue(array_key_exists('status_text', $result));
        self::assertEquals($result['status_text'], Response::$statusTexts[Response::HTTP_NOT_FOUND]);
        self::assertTrue(array_key_exists('message', $result));
        self::assertEquals('message exception', $result['message']);
        self::assertTrue(array_key_exists('trace', $result));
        self::assertTrue(array_key_exists('previous', $result));
        self::assertEquals('previous exception', $result['previous']['message']);
        self::assertEquals(123456, $result['previous']['code']);
    }

    public function testFormatExceptionResponse(): void
    {
        $httpException = new HttpException(Response::HTTP_NOT_FOUND);
        $testKernel = $this->getMockBuilder(KernelInterface::class)
            ->disableOriginalConstructor()
            ->getMock();
        $testKernel->expects(self::once())
            ->method('getEnvironment')
            ->willReturn("prod");
        $exceptionFormatService = new ExceptionFormatService($testKernel);
        self::assertTrue($exceptionFormatService instanceof ExceptionFormatService);
        $response = $exceptionFormatService->formatExceptionResponse($httpException);
        self::assertTrue($response instanceof Response);
        self::assertEquals($response->getStatusCode(), Response::HTTP_NOT_FOUND);
    }


    public function testFormatExceptionResponseWithTypeError(): void
    {
        $testKernel = $this->createStub(KernelInterface::class);
        $errorMsg = 'Type error';
        $typeError = new TypeError($errorMsg);
        $exceptionFormatService = new ExceptionFormatService($testKernel);
        self::assertTrue($exceptionFormatService instanceof ExceptionFormatService);
        $response = $exceptionFormatService->formatExceptionResponse($typeError);
        self::assertTrue($response instanceof Response);
        self::assertEquals(Response::HTTP_INTERNAL_SERVER_ERROR, $response->getStatusCode());
        self::assertEquals($errorMsg, $response->getContent());
    }

    public function testGenericExceptionResponseNotFound(): void
    {
        $testKernel = $this->createStub(KernelInterface::class);
        $httpException = new HttpException(Response::HTTP_NOT_FOUND);
        $exceptionFormatService = new ExceptionFormatService($testKernel);
        [$code, $text, $message] = $exceptionFormatService->genericExceptionResponse($httpException);
        self::assertEquals(404, $code);
        self::assertEquals('Not Found', $text);
        self::assertNull($message);
    }

    public function testGenericExceptionResponseUnauthorized(): void
    {
        $testKernel = $this->createStub(KernelInterface::class);
        $httpException = new HttpException(Response::HTTP_UNAUTHORIZED);
        $exceptionFormatService = new ExceptionFormatService($testKernel);
        [$code, $text, $message] = $exceptionFormatService->genericExceptionResponse($httpException);
        self::assertEquals(401, $code);
        self::assertEquals('Unauthorized', $text);
        self::assertNull($message);
    }

    public function testGetStatusCodeWithBadCredentialsException(): void
    {
        $testKernel = $this->createStub(KernelInterface::class);
        $exception = new BadCredentialsException();
        $exceptionFormatService = new ExceptionFormatService($testKernel);
        $statusCode = $exceptionFormatService->getStatusCode($exception);
        self::assertEquals(Response::HTTP_UNAUTHORIZED, $statusCode);
    }

    public function testGetArrayWithBadCredentialsExceptionDoesNotLeakPreviousInDev(): void
    {
        $previous = new UserNotFoundException('User "x@y.com" not found.');
        $exception = new BadCredentialsException('Invalid credentials.', 0, $previous);
        $testKernel = $this->getMockBuilder(KernelInterface::class)
            ->disableOriginalConstructor()
            ->getMock();
        $testKernel->expects(self::once())
            ->method('getEnvironment')
            ->willReturn("dev");
        $exceptionFormatService = new ExceptionFormatService($testKernel);
        $result = $exceptionFormatService->getArray($exception);
        self::assertEquals(Response::HTTP_UNAUTHORIZED, $result['status_code']);
        self::assertTrue(array_key_exists('trace', $result));
        self::assertFalse(array_key_exists('previous', $result));
    }

    public function testFormatExceptionResponseWithBadCredentialsException(): void
    {
        $previous = new UserNotFoundException('User "x@y.com" not found.');
        $exception = new BadCredentialsException('Invalid credentials.', 0, $previous);
        $testKernel = $this->getMockBuilder(KernelInterface::class)
            ->disableOriginalConstructor()
            ->getMock();
        $testKernel->expects(self::once())
            ->method('getEnvironment')
            ->willReturn("dev");
        $exceptionFormatService = new ExceptionFormatService($testKernel);
        $response = $exceptionFormatService->formatExceptionResponse($exception);
        self::assertEquals(Response::HTTP_UNAUTHORIZED, $response->getStatusCode());
        self::assertStringNotContainsString('not found', (string) $response->getContent());
    }

    public function testFormatExceptionResponseKeepsHttpExceptionHeadersInProd(): void
    {
        $testKernel = $this->createStub(KernelInterface::class);
        $testKernel->method('getEnvironment')->willReturn('prod');
        $exceptionFormatService = new ExceptionFormatService($testKernel);
        $tooManyRequestsHttpException = new TooManyRequestsHttpException(30, 'Trop de requêtes', null, 0, [
            'X-RateLimit-Limit' => 60,
            'X-RateLimit-Remaining' => 0,
        ]);
        $response = $exceptionFormatService->formatExceptionResponse($tooManyRequestsHttpException);
        self::assertEquals(Response::HTTP_TOO_MANY_REQUESTS, $response->getStatusCode());
        self::assertSame('30', $response->headers->get('Retry-After'));
        self::assertSame('60', $response->headers->get('X-RateLimit-Limit'));
        self::assertSame('0', $response->headers->get('X-RateLimit-Remaining'));
        self::assertSame('application/json', $response->headers->get('Content-Type'));
    }

    public function testFormatExceptionResponseKeepsHttpExceptionHeadersInDev(): void
    {
        $testKernel = $this->createStub(KernelInterface::class);
        $testKernel->method('getEnvironment')->willReturn('dev');
        $exceptionFormatService = new ExceptionFormatService($testKernel);
        $unauthorizedHttpException = new UnauthorizedHttpException('Bearer realm="api"', 'Non autorisé');
        $response = $exceptionFormatService->formatExceptionResponse($unauthorizedHttpException);
        self::assertEquals(Response::HTTP_UNAUTHORIZED, $response->getStatusCode());
        self::assertSame('Bearer realm="api"', $response->headers->get('WWW-Authenticate'));
    }

    public function testFormatExceptionResponseEncodesTraceWithUnencodableArgumentsInDev(): void
    {
        $resource = fopen('php://memory', 'r');
        self::assertIsResource($resource);
        $previousIgnoreArgs = ini_set('zend.exception_ignore_args', '0');
        try {
            $exception = $this->throwWithArgument($resource);
        } finally {
            ini_set('zend.exception_ignore_args', (string) $previousIgnoreArgs);
            fclose($resource);
        }

        self::assertArrayHasKey('args', $exception->getTrace()[0]);
        $testKernel = $this->createStub(KernelInterface::class);
        $testKernel->method('getEnvironment')->willReturn('dev');
        $exceptionFormatService = new ExceptionFormatService($testKernel);
        $response = $exceptionFormatService->formatExceptionResponse($exception);
        self::assertEquals(Response::HTTP_NOT_FOUND, $response->getStatusCode());
        $content = json_decode((string) $response->getContent(), true);
        self::assertIsArray($content);
        self::assertNotEmpty($content['trace']);
        self::assertSame('throwWithArgument', $content['trace'][0]['function']);
        self::assertSame(self::class, $content['trace'][0]['class']);
        foreach ($content['trace'] as $frame) {
            self::assertArrayNotHasKey('args', $frame);
        }
    }

    private function throwWithArgument(mixed $argument): HttpException
    {
        try {
            throw new HttpException(Response::HTTP_NOT_FOUND, sprintf('Introuvable (%s)', get_debug_type($argument)));
        } catch (HttpException $httpException) {
            return $httpException;
        }
    }
}
