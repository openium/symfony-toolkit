<?php

    namespace Openium\Service\Exception;

    use Doctrine\DBAL\DBALException;
    use Doctrine\DBAL\Exception\NotNullConstraintViolationException;
    use Doctrine\DBAL\Exception\UniqueConstraintViolationException;
    use Doctrine\ORM\ORMInvalidArgumentException;
    use Psr\Log\LoggerInterface;
    use Symfony\Component\HttpKernel\Exception\ConflictHttpException;
    use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

    use \UnexpectedValueException;

    /**
     * Class ExceptionHandlerService
     * @package App\Service\Exception
     */
    class ExceptionHandlerService implements ExceptionHandlerServiceInterface {

        /** @var LoggerInterface */
        protected $logger;

        /** @var \Throwable */
        protected $throwable;

        /**
         * ExceptionHandlerService constructor.
         * @param LoggerInterface $logger
         */
        public function __construct(LoggerInterface $logger)
        {
            $this->logger = $logger;
        }




        # -------------------------------------------------------------
        #   Logger
        # -------------------------------------------------------------

        /**
         * Log an exception information for debug
         *
         * @param \Throwable $throwable
         */
        public function log(\Throwable $throwable)
        {
            $this->logger->error('-------------------------------------');
            $this->logger->error(get_class($throwable));
            $this->logger->error($throwable->getMessage());
            $this->logger->error($throwable->getTraceAsString());
            $this->logger->error('-------------------------------------');
        }




        # -------------------------------------------------------------
        #   General Process
        # -------------------------------------------------------------

        /**
         * Catch & Process the throwable
         *
         * @param \Throwable $throwable
         */
        public function treatment(\Throwable $throwable)
        {
            // Call the logger
            $this->log($throwable);

            // Select the process
            switch (get_class($throwable)) {
                case UniqueConstraintViolationException::class:
                    $this->treatmentConflict($throwable);
                    break;

                case DBALException::class:
                    $this->treatmentDBAL($throwable);
                    break;

                case NotNullConstraintViolationException::class:
                case ORMInvalidArgumentException::class:
                case UnexpectedValueException::class:
                    $this->treatmentORM($throwable);
                    break;

                default:
                    break;
            }
        }




        # -------------------------------------------------------------
        #   Personal Process
        # -------------------------------------------------------------

        /**
         * @throws BadRequestHttpException
         */
        protected function treatmentORM(\Throwable $throwable)
        {
            throw new BadRequestHttpException("Entity's treatment error", $throwable);
        }

        /**
         * @param \Throwable $throwable
         * @throws ConflictHttpException
         */
        protected function treatmentConflict(\Throwable $throwable, $message = null)
        {
            $this->logger->error($throwable->getPrevious()->getCode());
            throw new ConflictHttpException($message ?? "Conflict error");
        }

        /**
         * @param \Throwable $throwable
         */
        protected function treatmentDBAL(\Throwable $throwable)
        {
            switch ($throwable->getPrevious()->getCode()) {
                case '23000' :
                    $this->treatmentConflict($throwable);
                    break;
                case '42000' :
                    throw new BadRequestHttpException("Database error");
                case '21000' :
                    throw new BadRequestHttpException("Database request error");
                case '21S01' :
                    throw new BadRequestHttpException("Database schema error (Missing property)");
                case '42S02' :
                    throw new BadRequestHttpException("Missing database table");
            }

            $this->treatmentORM($throwable);
        }
    }
?>