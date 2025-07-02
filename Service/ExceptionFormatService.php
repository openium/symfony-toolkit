<?php

namespace Openium\SymfonyToolKitBundle\Service;

use Exception;
use InvalidArgumentException;
use Openium\SymfonyToolKitBundle\DTO\DevExceptionDTO;
use Openium\SymfonyToolKitBundle\DTO\DevPreviousExceptionDTO;
use Openium\SymfonyToolKitBundle\DTO\ExceptionDTO;
use Openium\SymfonyToolKitBundle\Utils\ExceptionFormatUtilsInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\Serializer\SerializerInterface;
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
    public function __construct(
        protected readonly SerializerInterface $serializer,
        protected readonly ExceptionFormatUtilsInterface $exceptionFormatUtils,
        protected readonly string $env
    ) {
    }

    public function formatExceptionResponse(Throwable $exception): Response
    {
        if ($exception instanceof Exception) {
            $dto = $this->getDTO($exception);
            return JsonResponse::fromJsonString(
                $this->serializer->serialize($dto, 'json'),
                $dto->code
            );
        } else {
            return new Response($exception->getMessage(), Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * @return array{0: int, 1: string, 2:string|null} [code, text, message]
     */
    private function genericExceptionResponse(Exception $exception): array
    {
        $code = $this->exceptionFormatUtils->getStatusCode($exception);
        $text = $this->exceptionFormatUtils->getStatusText($exception);
        $message = null;
        return [$code, $text, $message];
    }

    /**
     * getArray
     *
     * @param Exception $exception
     *
     * @return ExceptionDTO|DevExceptionDTO
     */
    private function getDTO(
        Exception $exception
    ): ExceptionDTO | DevExceptionDTO {
        [$code, $text, $message] = $this->genericExceptionResponse($exception);
        /** @var array<string, int|string|array<string|int, mixed>|null> $error */
        $codeValue = $code ?? $this->exceptionFormatUtils->getStatusCode($exception);
        $textValue = $text ?? $this->exceptionFormatUtils->getStatusText($exception);
        $messageValue = $message ?? $exception->getMessage();
        return match ($this->env) {
            'prod' => new ExceptionDTO(
                $codeValue,
                $textValue,
                $messageValue
            ),
            default => new DevExceptionDTO(
                $codeValue,
                $textValue,
                $messageValue,
                $exception->getTrace(),
                $exception->getPrevious() ? new DevPreviousExceptionDTO(
                    $exception->getPrevious()->getCode(),
                    $exception->getPrevious()->getMessage()
                ) : null
            ),
        };
    }
}
