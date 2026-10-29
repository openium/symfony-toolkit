<?php

namespace Openium\SymfonyToolKitBundle\Tests\Service;

use Exception;
use Openium\SymfonyToolKitBundle\DTO\DevExceptionDTO;
use Openium\SymfonyToolKitBundle\DTO\DevPreviousExceptionDTO;
use Openium\SymfonyToolKitBundle\DTO\ExceptionDTO;
use Openium\SymfonyToolKitBundle\Service\ExceptionFormatService;
use Openium\SymfonyToolKitBundle\Utils\ExceptionFormatUtilsInterface;
use PHPUnit\Framework\TestCase;
use ReflectionClass;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpKernel\Exception\TooManyRequestsHttpException;
use Symfony\Component\HttpKernel\Exception\UnauthorizedHttpException;
use Symfony\Component\Security\Core\Exception\BadCredentialsException;
use Symfony\Component\Security\Core\Exception\UserNotFoundException;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Serializer;
use Symfony\Component\Serializer\SerializerInterface;

/**
 * Class ExceptionFormatServiceTest
 *
 * @package Openium\SymfonyToolKitBundle\Test\Service
 */
class ExceptionFormatServiceTest extends TestCase
{
    public function testFormatExceptionResponseReturnsJsonResponseWithCorrectStatusCodeAndContentInProd(
    ): void
    {
        $serializer = $this->createMock(SerializerInterface::class);
        $utils = $this->createMock(ExceptionFormatUtilsInterface::class);
        $utils->method('getStatusCode')->willReturn(400);
        $utils->method('getStatusText')->willReturn('Bad Request');
        $serializer->method('serialize')->willReturn(json_encode([
            'code' => 400,
            'text' => 'Bad Request',
            'message' => 'Erreur',
        ]));
        $exceptionFormatService = new ExceptionFormatService($serializer, $utils, 'prod');
        $exception = new Exception('Erreur', 400);
        $response = $exceptionFormatService->formatExceptionResponse($exception);
        $this->assertInstanceOf(JsonResponse::class, $response);
        $this->assertEquals(\Symfony\Component\HttpFoundation\Response::HTTP_BAD_REQUEST, $response->getStatusCode());
        $content = json_decode($response->getContent(), true);
        $this->assertEquals(400, $content['code']);
        $this->assertEquals('Bad Request', $content['text']);
        $this->assertEquals('Erreur', $content['message']);
    }

    public function testFormatExceptionResponseReturnsJsonResponseWithTraceInDev(): void
    {
        $serializer = $this->createMock(SerializerInterface::class);
        $utils = $this->createMock(ExceptionFormatUtilsInterface::class);
        $utils->method('getStatusCode')->willReturn(500);
        $utils->method('getStatusText')->willReturn('Internal Server Error');
        $serializer->method('serialize')->willReturn(json_encode([
            'code' => 500,
            'text' => 'Internal Server Error',
            'message' => 'Erreur dev',
            'trace' => [],
            'previous' => null,
        ]));
        $exceptionFormatService = new ExceptionFormatService($serializer, $utils, 'dev');
        $exception = new Exception('Erreur dev', 500);
        $response = $exceptionFormatService->formatExceptionResponse($exception);
        $this->assertInstanceOf(JsonResponse::class, $response);
        $this->assertEquals(\Symfony\Component\HttpFoundation\Response::HTTP_INTERNAL_SERVER_ERROR, $response->getStatusCode());
        $content = json_decode($response->getContent(), true);
        $this->assertEquals(500, $content['code']);
        $this->assertEquals('Internal Server Error', $content['text']);
        $this->assertEquals('Erreur dev', $content['message']);
        $this->assertIsArray($content['trace']);
    }

    public function testGetDTOReturnsExceptionDTOInProd(): void
    {
        $serializer = $this->createStub(SerializerInterface::class);
        $utils = $this->createMock(ExceptionFormatUtilsInterface::class);
        $utils->method('getStatusCode')->willReturn(401);
        $utils->method('getStatusText')->willReturn('Unauthorized');
        $exceptionFormatService = new ExceptionFormatService($serializer, $utils, 'prod');
        $exception = new Exception('Non autorisé', 401);
        $reflectionClass = new ReflectionClass($exceptionFormatService);
        $reflectionMethod = $reflectionClass->getMethod('getDTO');

        $dto = $reflectionMethod->invoke($exceptionFormatService, $exception);
        $this->assertInstanceOf(ExceptionDTO::class, $dto);
        $this->assertEquals(401, $dto->code);
        $this->assertEquals('Unauthorized', $dto->text);
        $this->assertEquals('Non autorisé', $dto->message);
    }

    public function testGetDTOReturnsDevExceptionDTOInDevWithPrevious(): void
    {
        $serializer = $this->createStub(SerializerInterface::class);
        $utils = $this->createMock(ExceptionFormatUtilsInterface::class);
        $utils->method('getStatusCode')->willReturn(500);
        $utils->method('getStatusText')->willReturn('Internal Server Error');
        $exceptionFormatService = new ExceptionFormatService($serializer, $utils, 'dev');
        $previous = new Exception('Précédent', 123);
        $exception = new Exception('Erreur dev', 500, $previous);
        $reflectionClass = new ReflectionClass($exceptionFormatService);
        $reflectionMethod = $reflectionClass->getMethod('getDTO');

        $dto = $reflectionMethod->invoke($exceptionFormatService, $exception);
        $this->assertInstanceOf(DevExceptionDTO::class, $dto);
        $this->assertEquals(500, $dto->code);
        $this->assertEquals('Internal Server Error', $dto->text);
        $this->assertEquals('Erreur dev', $dto->message);
        $this->assertIsArray($dto->trace);
        $this->assertInstanceOf(DevPreviousExceptionDTO::class, $dto->previous);
        $this->assertEquals('Précédent', $dto->previous->message);
        $this->assertEquals(123, $dto->previous->code);
    }

    public function testGenericExceptionResponseReturnsExpectedArray(): void
    {
        $serializer = $this->createStub(SerializerInterface::class);
        $utils = $this->createMock(ExceptionFormatUtilsInterface::class);
        $utils->method('getStatusCode')->willReturn(404);
        $utils->method('getStatusText')->willReturn('Not Found');
        $exceptionFormatService = new ExceptionFormatService($serializer, $utils, 'prod');
        $exception = new Exception('Introuvable', 404);
        $reflectionClass = new ReflectionClass($exceptionFormatService);
        $reflectionMethod = $reflectionClass->getMethod('genericExceptionResponse');

        $result = $reflectionMethod->invoke($exceptionFormatService, $exception);
        $this->assertEquals([404, 'Not Found', null], $result);
    }

    public function testGetDTOReturnsDevExceptionDTOWithoutPreviousForAuthenticationException(): void
    {
        $serializer = $this->createStub(SerializerInterface::class);
        $utils = $this->createMock(ExceptionFormatUtilsInterface::class);
        $utils->method('getStatusCode')->willReturn(401);
        $utils->method('getStatusText')->willReturn('Unauthorized');
        $exceptionFormatService = new ExceptionFormatService($serializer, $utils, 'dev');
        $userNotFoundException = new UserNotFoundException('User "x@y.com" not found.');
        $badCredentialsException = new BadCredentialsException('Invalid credentials.', 0, $userNotFoundException);
        $reflectionClass = new ReflectionClass($exceptionFormatService);
        $reflectionMethod = $reflectionClass->getMethod('getDTO');

        $dto = $reflectionMethod->invoke($exceptionFormatService, $badCredentialsException);
        $this->assertInstanceOf(DevExceptionDTO::class, $dto);
        $this->assertEquals(401, $dto->code);
        $this->assertNull($dto->previous);
    }

    public function testFormatExceptionResponseDoesNotLeakPreviousForBadCredentialsExceptionInDev(): void
    {
        $serializer = $this->createMock(SerializerInterface::class);
        $serializer->method('serialize')->willReturnCallback(
            fn ($dto) => json_encode([
                'code' => $dto->code,
                'text' => $dto->text,
                'message' => $dto->message,
                'trace' => $dto->trace,
                'previous' => $dto->previous,
            ])
        );
        $utils = $this->createMock(ExceptionFormatUtilsInterface::class);
        $utils->method('getStatusCode')->willReturn(401);
        $utils->method('getStatusText')->willReturn('Unauthorized');
        $exceptionFormatService = new ExceptionFormatService($serializer, $utils, 'dev');
        $userNotFoundException = new UserNotFoundException('User "x@y.com" not found.');
        $badCredentialsException = new BadCredentialsException('Invalid credentials.', 0, $userNotFoundException);
        $response = $exceptionFormatService->formatExceptionResponse($badCredentialsException);
        $this->assertEquals(\Symfony\Component\HttpFoundation\Response::HTTP_UNAUTHORIZED, $response->getStatusCode());
        $content = json_decode($response->getContent(), true);
        $this->assertNull($content['previous']);
        $this->assertStringNotContainsString('not found', (string) $response->getContent());
    }

    public function testGetDTODoesNotLeakPreviousWhenAuthenticationExceptionIsEncapsulated(): void
    {
        $serializer = $this->createStub(SerializerInterface::class);
        $utils = $this->createMock(ExceptionFormatUtilsInterface::class);
        $utils->method('getStatusCode')->willReturn(500);
        $utils->method('getStatusText')->willReturn('Internal Server Error');
        $exceptionFormatService = new ExceptionFormatService($serializer, $utils, 'dev');
        $userNotFoundException = new UserNotFoundException('User "x@y.com" not found.');
        $exception = new Exception('Erreur inattendue', 500, $userNotFoundException);
        $reflectionClass = new ReflectionClass($exceptionFormatService);
        $reflectionMethod = $reflectionClass->getMethod('getDTO');

        $dto = $reflectionMethod->invoke($exceptionFormatService, $exception);
        $this->assertInstanceOf(DevExceptionDTO::class, $dto);
        $this->assertNull($dto->previous);
    }

    public function testFormatExceptionResponseSerializesTraceWithObjectArgumentsInDev(): void
    {
        $previousIgnoreArgs = ini_set('zend.exception_ignore_args', '0');
        try {
            $exception = $this->throwWithObjectArgument(new \ReflectionMethod(\ArrayObject::class, 'count'));
        } finally {
            ini_set('zend.exception_ignore_args', (string) $previousIgnoreArgs);
        }

        $this->assertArrayHasKey('args', $exception->getTrace()[0]);
        $serializer = new Serializer([new ObjectNormalizer()], [new JsonEncoder()]);
        $utils = $this->createStub(ExceptionFormatUtilsInterface::class);
        $utils->method('getStatusCode')->willReturn(404);
        $utils->method('getStatusText')->willReturn('Not Found');
        $exceptionFormatService = new ExceptionFormatService($serializer, $utils, 'dev');
        $response = $exceptionFormatService->formatExceptionResponse($exception);
        $this->assertEquals(\Symfony\Component\HttpFoundation\Response::HTTP_NOT_FOUND, $response->getStatusCode());
        $content = json_decode($response->getContent(), true);
        $this->assertNotEmpty($content['trace']);
        $this->assertSame('throwWithObjectArgument', $content['trace'][0]['function']);
        $this->assertSame(self::class, $content['trace'][0]['class']);
        foreach ($content['trace'] as $frame) {
            $this->assertArrayNotHasKey('args', $frame);
        }
    }

    public function testFormatExceptionResponseKeepsHttpExceptionHeadersInProd(): void
    {
        $serializer = new Serializer([new ObjectNormalizer()], [new JsonEncoder()]);
        $utils = $this->createStub(ExceptionFormatUtilsInterface::class);
        $utils->method('getStatusCode')->willReturn(429);
        $utils->method('getStatusText')->willReturn('Too Many Requests');
        $exceptionFormatService = new ExceptionFormatService($serializer, $utils, 'prod');
        $tooManyRequestsHttpException = new TooManyRequestsHttpException(30, 'Trop de requêtes', null, 0, [
            'X-RateLimit-Limit' => 60,
            'X-RateLimit-Remaining' => 0,
        ]);
        $response = $exceptionFormatService->formatExceptionResponse($tooManyRequestsHttpException);
        $this->assertEquals(\Symfony\Component\HttpFoundation\Response::HTTP_TOO_MANY_REQUESTS, $response->getStatusCode());
        $this->assertSame('30', $response->headers->get('Retry-After'));
        $this->assertSame('60', $response->headers->get('X-RateLimit-Limit'));
        $this->assertSame('0', $response->headers->get('X-RateLimit-Remaining'));
        $this->assertSame('application/json', $response->headers->get('Content-Type'));
    }

    public function testFormatExceptionResponseKeepsHttpExceptionHeadersInDev(): void
    {
        $serializer = new Serializer([new ObjectNormalizer()], [new JsonEncoder()]);
        $utils = $this->createStub(ExceptionFormatUtilsInterface::class);
        $utils->method('getStatusCode')->willReturn(401);
        $utils->method('getStatusText')->willReturn('Unauthorized');
        $exceptionFormatService = new ExceptionFormatService($serializer, $utils, 'dev');
        $unauthorizedHttpException = new UnauthorizedHttpException('Bearer realm="api"', 'Non autorisé');
        $response = $exceptionFormatService->formatExceptionResponse($unauthorizedHttpException);
        $this->assertEquals(\Symfony\Component\HttpFoundation\Response::HTTP_UNAUTHORIZED, $response->getStatusCode());
        $this->assertSame('Bearer realm="api"', $response->headers->get('WWW-Authenticate'));
    }

    private function throwWithObjectArgument(object $argument): Exception
    {
        try {
            throw new Exception(sprintf('Introuvable (%s)', $argument::class), 404);
        } catch (Exception $exception) {
            return $exception;
        }
    }
}
