<?php

namespace Openium\SymfonyToolKitBundle\Service;

use DateTime;
use DateTimeZone;
use InvalidArgumentException;
use Psr\Log\LoggerInterface;

/**
 * Class AtHelper
 *
 * @package Openium\SymfonyToolKitBundle\Service
 */
class AtHelper implements AtHelperInterface
{
    /**
     * AtHelper constructor.
     */
    public function __construct(private readonly LoggerInterface $logger)
    {
    }

    /**
     * executeAndCaptureOutput
     */
    public function executeAndCaptureOutput(string $cmd, int &$result): string | false
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
     * createAtCommandFromPath
     * Execute a command with at.
     *
     * @param string $cmd command to execute
     * @param int $timestamp when the command will be executed
     * @param string $path path where the at creation command will be executed
     * @param int &$result result of at
     *
     * @throws InvalidArgumentException
     * @return false|string the output of at command
     */
    public function createAtCommandFromPath(
        string $cmd,
        int $timestamp,
        string $path,
        int &$result
    ): false | string {
        $date = $this->formatTimestampForAt($timestamp);
        $fullCmd = sprintf('cd %s; echo "%s" | at %s 2>&1 ; let \!PIPESTATUS', $path, $cmd, $date);
        return $this->atCommand($fullCmd, $result);
    }

    /**
     * createAtCommand
     * Execute a command with at.
     *
     * @deprecated use createAtCommandFromPath instead
     *
     * @param int $timestamp when the command will be executed
     * @param int &$result result of at
     * @param string $cmd command to execute
     *
     * @throws InvalidArgumentException
     * @return false|string the output of at command
     */
    public function createAtCommand(string $cmd, int $timestamp, int &$result): false | string
    {
        $date = $this->formatTimestampForAt($timestamp);
        $fullCmd = sprintf('echo "%s" | at %s 2>&1 ; let \!PIPESTATUS', $cmd, $date);
        return $this->atCommand($fullCmd, $result);
    }

    /**
     * atCommand
     */
    private function atCommand(string $fullCmd, int &$result): false | string
    {
        $this->logger->debug(sprintf("Create AT command (%s)", $fullCmd));
        $output = $this->executeAndCaptureOutput($fullCmd, $result);
        $this->logger->debug(sprintf("AT Output : %s", $output));
        $this->logger->debug(sprintf("AT Result : %s)", $result));
        if ($output === false || str_contains($output, "garbled time") || $result != 0) {
            $this->logger->error(
                sprintf("Creation of AT command failed (%s) : %s", $result, $fullCmd)
            );
        }

        return $output;
    }

    /**
     * extractJobNumberFromAtOutput
     */
    public function extractJobNumberFromAtOutput(string $output): ?string
    {
        $cleanOutput = preg_replace('/\s+/', ' ', $output);
        if (is_string($cleanOutput)) {
            $cleanOutput = trim($cleanOutput);
            $explodedOutput = explode(' ', $cleanOutput);
            if (count($explodedOutput) >= 2) {
                $jobIndex = array_search('job', $explodedOutput, true);
                if ($jobIndex !== false) {
                    $jobNumber = $explodedOutput[$jobIndex + 1];
                    if (is_numeric($jobNumber)) {
                        return $jobNumber;
                    }
                }
            }
        }

        return null;
    }

    /**
     * removeAtCommand
     */
    public function removeAtCommand(string $atJobNumber): bool
    {
        $fullCmd = sprintf("atrm %s", $atJobNumber);
        $result = -1;
        $output = $this->executeAndCaptureOutput($fullCmd, $result);
        return $output !== false;
    }

    /**
     * formatDateForAt
     * transform timestamp to format 'g:i A F j Y'
     * example :
     * 1514761200 => '11:00 PM December 31 2017'
     *
     * @throws InvalidArgumentException
     */
    public function formatTimestampForAt(int $timestamp): string
    {
        if ($timestamp < 0) {
            throw new InvalidArgumentException('timestamp < 0');
        }

        $timeZone = new DateTimeZone("Europe/Paris");
        $date = new DateTime('now', $timeZone);
        return $date->setTimestamp($timestamp)->format('g:i A F j Y');
    }
}
