<?php

declare(strict_types=1);

namespace Openium\SymfonyToolKitBundle\Service;

use Exception;
use InvalidArgumentException;
use Openium\SymfonyToolKitBundle\DTO\DevExceptionDTO;
use Openium\SymfonyToolKitBundle\DTO\DevPreviousExceptionDTO;
use Openium\SymfonyToolKitBundle\DTO\ExceptionDTO;
use Openium\SymfonyToolKitBundle\Utils\ExceptionFormatUtilsInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
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

    public function formatExceptionResponse(Throwable $throwable): Response
    {
        if ($throwable instanceof Exception) {
            $dto = $this->getDTO($throwable);
            return JsonResponse::fromJsonString(
                $this->serializer->serialize($dto, 'json'),
                $dto->code
            );
        }
        return new Response($throwable->getMessage(), Response::HTTP_INTERNAL_SERVER_ERROR);
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
     * getDTO
     *
     *
     */
    private function getDTO(
        Exception $exception
    ): ExceptionDTO | DevExceptionDTO {
        [$code, $text, $message] = $this->genericExceptionResponse($exception);
        $messageValue = $message ?? $exception->getMessage();
        return match ($this->env) {
            'prod' => new ExceptionDTO(
                $code,
                $text,
                $messageValue
            ),
            default => new DevExceptionDTO(
                $code,
                $text,
                $messageValue,
                $exception->getTrace(),
                $this->buildPreviousDTO($exception)
            ),
        };
    }

    /**
     * Builds the previous-exception DTO, unless either the exception itself or its previous
     * is an AuthenticationException: Symfony's AuthenticatorManager intentionally wraps a
     * UserNotFoundException inside a BadCredentialsException to avoid revealing whether an
     * account exists, and re-exposing that message here would defeat that protection.
     */
    private function buildPreviousDTO(Exception $exception): ?DevPreviousExceptionDTO
    {
        $previous = $exception->getPrevious();
        if (
            !$previous instanceof \Throwable
            || $exception instanceof AuthenticationException
            || $previous instanceof AuthenticationException
        ) {
            return null;
        }

        return new DevPreviousExceptionDTO($previous->getCode(), $previous->getMessage());
    }
}
