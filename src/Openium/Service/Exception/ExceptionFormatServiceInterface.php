<?php

    namespace Openium\Service\Utils\Exception;

    use \Symfony\Component\HttpFoundation\Response;

    interface ExceptionFormatServiceInterface
    {
        public function formatExceptionResponse(\Exception $exception): Response;

        public function getArray(\Exception $exception, $code = null, $text = null, $message = null): array;

        public function getStatusCode(\Exception $exception);

        public function getStatusText(\Exception $exception);
    }
?>