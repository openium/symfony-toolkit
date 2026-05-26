<?php

namespace Openium\SymfonyToolKitBundle\Service;

use Exception;
use InvalidArgumentException;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\HttpExceptionInterface;
use Symfony\Component\HttpKernel\KernelInterface;
use Throwable;

/**
 * Class ExceptionFormatService
 *
 * @package Openium\SymfonyToolKitBundle\Service
 */
class ExceptionFormatService implements ExceptionFormatServiceInterface
{
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
            $error['trace'] = $exception->getTrace();
            $error['previous'] = [];
            if (!is_null($exception->getPrevious())) {
                $error['previous']['message'] = $exception->getPrevious()->getMessage();
                $error['previous']['code'] = $exception->getPrevious()->getCode();
            }
        }

        return $error;
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
        return ($exception instanceof HttpExceptionInterface)
            ? $exception->getStatusCode()
            : Response::HTTP_INTERNAL_SERVER_ERROR;
    }

    /**
     * getStatusText
     */
    #[\Override]
    public function getStatusText(Exception $exception): string
    {
        $code = $this->getStatusCode($exception);
        if ($code == Response::HTTP_PAYMENT_REQUIRED) {
            return 'Request Failed';
        } else {
            $isCodeExists = array_key_exists($code, Response::$statusTexts);
            return ($isCodeExists)
                ? Response::$statusTexts[$code]
                : Response::$statusTexts[Response::HTTP_INTERNAL_SERVER_ERROR];
        }
    }
}
