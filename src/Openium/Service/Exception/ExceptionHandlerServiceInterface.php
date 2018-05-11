<?php

    namespace Openium\SymfonyToolKit\Service\Exception;

    /**
     * Class ExceptionHandlerServiceInterface
     * @package Openium\SymfonyToolKit\Service\Exception
     */
    interface ExceptionHandlerServiceInterface
    {
        /**
         * Log an exception information for debug
         *
         * @param \Throwable $throwable
         */
        public function log(\Throwable $throwable);


        /**
         * Catch & Process the throwable
         *
         * @param \Throwable $throwable
         */
        public function treatment(\Throwable $throwable);
    }
?>