<?php

/**
 * PHP Version 7.1, 7.2
 *
 * @package  Openium\SymfonyToolKitBundle\Service
 * @author   Openium <contact@openium.fr>
 * @license  Openium All right reserved
 * @link     https://www.openium.fr/
 */

namespace Openium\SymfonyToolKitBundle\Service;

use Psr\Log\LoggerInterface;

/**
 * Class AtHelper
 *
 * @package Openium\SymfonyToolKitBundle\Service
 */
class AtHelper implements AtHelperInterface
{
    /**
     * @var LoggerInterface
     */
    private $logger;

    /**
     * AtHelper constructor.
     *
     * @param LoggerInterface $logger
     */
    public function __construct(LoggerInterface $logger)
    {
        $this->logger = $logger;
    }

    /**
     * executeAndCaptureOutput
     *
     * @param $cmd
     * @param $result
     *
     * @return string
     */
    public function executeAndCaptureOutput($cmd, &$result)
    {
        $res = 0;
        ob_start();
        passthru($cmd, $res);
        $output = ob_get_contents();
        ob_end_clean();
        $result = $res;
        return $output;
    }

    /**
     * createAtCommand
     * Execute a command with at.
     *
     * @param string $cmd command to execute
     * @param int $timestamp when execute command
     * @param &$result result of at
     *
     * @throws \InvalidArgumentException
     *
     * @return string : the output of at command
     */
    public function createAtCommand(string $cmd, int $timestamp, &$result)
    {
        $date = $this->formatTimestampForAt($timestamp);
        $fullCmd = sprintf('echo "%s" | at %s 2>&1 ; let \!PIPESTATUS', $cmd, $date);
        $this->logger->debug("Create AT command (${fullCmd})");
        $output = $this->executeAndCaptureOutput($fullCmd, $result);
        $this->logger->debug("AT Output : ${output}");
        $this->logger->debug("AT Result : ${result})");
        if (strstr($output, "garbled time") || $result != 0) {
            $this->logger->error("Creation of AT command failed (${result}) : ${fullCmd}");
        }
        return $output;
    }

    /**
     * extractJobNumberFromAtOutput
     *
     * @param $output
     *
     * @return string|null
     */
    public function extractJobNumberFromAtOutput($output): ?string
    {
        $explodedOutput = explode(' ', $output);
        if (sizeof($explodedOutput) >= 2) {
            return $explodedOutput[1];
        }
        return null;
    }

    /**
     * removeAtCommand
     *
     * @param string $atJobNumber
     *
     * @return bool
     */
    public function removeAtCommand(string $atJobNumber): bool
    {
        $fullCmd = sprintf("atrm %s", $atJobNumber);
        $output = $this->executeAndCaptureOutput($fullCmd, $result);
        return empty($output);
    }

    /**
     * formatDateForAt
     * transform timestamp to format 'g:i A F j Y'
     * example :
     * 1514761200 => '11:00 PM December 31 2017'
     *
     * @param int $timestamp
     *
     * @throws \InvalidArgumentException
     *
     * @return string
     */
    public function formatTimestampForAt(int $timestamp): string
    {
        if ($timestamp < 0) {
            throw new \InvalidArgumentException('timestamp < 0');
        }
        $timeZone = new \DateTimeZone("Europe/Paris");
        $date = new \DateTime(null, $timeZone);
        return $date->setTimestamp($timestamp)->format('g:i A F j Y');
    }
}
