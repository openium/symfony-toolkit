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
    public function formatExceptionResponse(Throwable $exception): Response
    {
        $response = new JsonResponse();
        if ($exception instanceof Exception) {
            [$code, $text, $message] = $this->genericExceptionResponse($exception);
            $error = $this->getArray($exception, $code, $text, $message);
            $response->setStatusCode($code);
            try {
                $json = json_encode($error, JSON_THROW_ON_ERROR);
                $response->setContent($json);
            } catch (\JsonException) {
                $response->setContent('');
            }
        } else {
            $response->setStatusCode(Response::HTTP_INTERNAL_SERVER_ERROR);
            $response->setContent($exception->getMessage());
        }

        return $response;
    }

    /**
     * @return array{0: int, 1: string, 2:string|null} [code, text, message]
     */
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
    public function addKeyToErrorArray(array $error, Exception $exception): array
    {
        return $error;
    }

    /**
     * getStatusCode
     */
    public function getStatusCode(Exception $exception): int
    {
        return ($exception instanceof HttpExceptionInterface)
            ? $exception->getStatusCode()
            : Response::HTTP_INTERNAL_SERVER_ERROR;
    }

    /**
     * getStatusText
     */
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
