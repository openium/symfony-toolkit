<?php

    namespace Openium\SymfonyToolKit\Service\Exception;

    use Symfony\Component\HttpFoundation\JsonResponse;
    use Symfony\Component\HttpFoundation\Response;
    use Symfony\Component\HttpKernel\Exception\HttpExceptionInterface;
    use Symfony\Component\HttpKernel\KernelInterface;

    /**
     * Class ExceptionFormatService
     * @package App\Service\Utils\Exception
     */
    class ExceptionFormatService implements ExceptionFormatServiceInterface
    {
        protected const DEFAULT_STATUS_TEXT = 'Internal Server Error';

        /** @var KernelInterface */
        protected $kernel;

        /**
         * ExceptionFormatService constructor.
         * @param KernelInterface $kernel
         */
        public function __construct(KernelInterface $kernel)
        {
            $this->kernel = $kernel;
        }

        /**
         * @param \Exception $exception
         * @return Response
         * @throws \InvalidArgumentException
         * @throws \UnexpectedValueException
         */
        public function formatExceptionResponse(\Exception $exception): Response
        {
            // Get Status Code & Name of Code
            $code = $this->getStatusCode($exception);
            $text = $this->getStatusText($exception);

            // Get Error
            $error = $this->getArray($exception, $code, $text, NULL);

            // Make Json Response
            $response = new JsonResponse();
            $response->setStatusCode($code ?: $this->getStatusCode($exception));
            $response->setContent((json_encode($error)) ?: '');

            return $response;
        }

        /**
         * @param \Exception $exception
         * @param null       $code
         * @param null       $text
         * @param null       $message
         * @return array
         */
        public function getArray(\Exception $exception, $code = null, $text = null, $message = null): array
        {
            $error = [
                'status_code' => $code ?: $this->getStatusCode($exception),
                'status_text' => $text ?: $this->getStatusText($exception),
                'message' => $message ?: $exception->getMessage()
            ];

            if ($this->kernel->getEnvironment() != 'prod') {
                $error['trace'] = $exception->getTrace();
            }

            return $error;
        }

        /**
         * @param \Exception $exception
         * @return int
         */
        public function getStatusCode(\Exception $exception)
        {
            if ($exception instanceof HttpExceptionInterface) {
                return $exception->getStatusCode();
            }
            return Response::HTTP_INTERNAL_SERVER_ERROR;
        }

        /**
         * @param \Exception $exception
         * @return string
         */
        public function getStatusText(\Exception $exception)
        {
            $code = $this->getStatusCode($exception);
            return array_key_exists($code, Response::$statusTexts) ? Response::$statusTexts[$code] : self::DEFAULT_STATUS_TEXT;
        }
    }
?>