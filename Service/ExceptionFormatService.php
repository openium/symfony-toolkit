<?php

namespace Openium\SymfonyToolKitBundle\Service;

use Exception;
use InvalidArgumentException;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\HttpExceptionInterface;
use Symfony\Component\HttpKernel\KernelInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Throwable;

/**
 * Class ExceptionFormatService
 *
 * @package Openium\SymfonyToolKitBundle\Service
 */
class ExceptionFormatService implements ExceptionFormatServiceInterface
{
    private const array TRACE_KEYS = ['file', 'line', 'class', 'type', 'function'];

    /**
     * ExceptionFormatService constructor.
     */
    public function __construct(protected KernelInterface $kernel)
    {
    }

    /** @var array{code: string, text: string, message: string} */
    protected array $jsonKeys = [
        'code' => 'status_code',
        'text' => 'status_text',
        'message' => 'message',
    ];

    /**
     * formatExceptionResponse
     *
     * @throws InvalidArgumentException
     */
    #[\Override]
    public function formatExceptionResponse(Throwable $throwable): Response
    {
        $jsonResponse = new JsonResponse();
        if ($throwable instanceof Exception) {
            [$code, $text, $message] = $this->genericExceptionResponse($throwable);
            $error = $this->getArray($throwable, $code, $text, $message);
            $jsonResponse->setStatusCode($code);
            try {
                $json = json_encode($error, JSON_THROW_ON_ERROR);
                $jsonResponse->setContent($json);
            } catch (\JsonException) {
                $jsonResponse->setContent('');
            }
        } else {
            $jsonResponse->setStatusCode(Response::HTTP_INTERNAL_SERVER_ERROR);
            $jsonResponse->setContent($throwable->getMessage());
        }

        // HttpKernel only copies HttpException headers onto responses that are not already 4xx/5xx,
        // so Retry-After, WWW-Authenticate, Allow... must be carried over here.
        if ($throwable instanceof HttpExceptionInterface) {
            $jsonResponse->headers->add($throwable->getHeaders());
        }

        return $jsonResponse;
    }

    /**
     * @return array{0: int, 1: string, 2:string|null} [code, text, message]
     */
    #[\Override]
    public function genericExceptionResponse(Exception $exception): array
    {
        $code = $this->getStatusCode($exception);
        $text = $this->getStatusText($exception);
        $message = null;
        return [$code, $text, $message];
    }

    /**
     * getArray
     *
     * @return array<string, int|string|array<string|int, mixed>|null>
     */
    #[\Override]
    public function getArray(
        Exception $exception,
        ?int $code = null,
        ?string $text = null,
        ?string $message = null
    ): array {
        /** @var array<string, int|string|array<string|int, mixed>|null> $error */
        $error = [
            $this->jsonKeys['code'] => $code ?? $this->getStatusCode($exception),
            $this->jsonKeys['text'] => $text ?? $this->getStatusText($exception),
            $this->jsonKeys['message'] => $message ?? $exception->getMessage(),
        ];
        $error = $this->addKeyToErrorArray($error, $exception);
        if ($this->kernel->getEnvironment() !== 'prod') {
            $error['trace'] = $this->buildTrace($exception);
            if (!($exception instanceof AuthenticationException)) {
                $error['previous'] = [];
                if (!is_null($exception->getPrevious())) {
                    $error['previous']['message'] = $exception->getPrevious()->getMessage();
                    $error['previous']['code'] = $exception->getPrevious()->getCode();
                }
            }
        }

        return $error;
    }

    /**
     * buildTrace
     *
     * @return array<int, array<string, int|string>>
     */
    private function buildTrace(Exception $exception): array
    {
        // With zend.exception_ignore_args=0, frames carry live arguments under `args` that may not be
        // JSON-encodable (resources, recursive objects) or may expose sensitive values: keep only the
        // scalar keys.
        return array_map(
            static fn (array $frame): array => array_intersect_key($frame, array_flip(self::TRACE_KEYS)),
            $exception->getTrace()
        );
    }

    /**
     * addKeyToErrorArray
     *
     * @param array<string, int|string|array<string|int, mixed>|null> $error
     *
     * @return array<string, int|string|array<string|int, mixed>|null>
     */
    #[\Override]
    public function addKeyToErrorArray(array $error, Exception $exception): array
    {
        return $error;
    }

    /**
     * getStatusCode
     */
    #[\Override]
    public function getStatusCode(Exception $exception): int
    {
        if ($exception instanceof HttpExceptionInterface) {
            return $exception->getStatusCode();
        }
        if ($exception instanceof AuthenticationException) {
            return Response::HTTP_UNAUTHORIZED;
        }
        return Response::HTTP_INTERNAL_SERVER_ERROR;
    }

    /**
     * getStatusText
     */
    #[\Override]
    public function getStatusText(Exception $exception): string
    {
        $code = $this->getStatusCode($exception);
        if ($code === Response::HTTP_PAYMENT_REQUIRED) {
            return 'Request Failed';
        } else {
            $isCodeExists = array_key_exists($code, Response::$statusTexts);
            return ($isCodeExists)
                ? Response::$statusTexts[$code]
                : Response::$statusTexts[Response::HTTP_INTERNAL_SERVER_ERROR];
        }
    }
}
