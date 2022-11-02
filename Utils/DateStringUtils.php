<?php

namespace Openium\SymfonyToolKitBundle\Utils;

use DateTime;
use DateTimeInterface;
use DateTimeZone;

/**
 * Class DateStringService
 *
 * @package Openium\SymfonyToolKitBundle\Service
 */
class DateStringUtils
{
    /**
     * getDateTimeFromString
     *
     * @param string $dateString
     * @param string|null $format
     * @param DateTimeZone|null $timeZone
     *
     * @return DateTime | false
     */
    public static function getDateTimeFromString(
        string $dateString,
        ?string $format = null,
        ?DateTimeZone $timeZone = null
    ) {
        if (null === $format) {
            if (strlen($dateString) > 10) {
                if (str_starts_with(substr($dateString, -3), ':')) {
                    $format = DateTimeInterface::ATOM;
                } else {
                    $format = DateTimeInterface::ISO8601;
                }
            } else {
                $format = 'Y-m-d';
            }
        }
        if (null === $timeZone) {
            $timeZone = new DateTimeZone('Europe/Paris');
        }
        return DateTime::createFromFormat(
            $format,
            $dateString,
            $timeZone
        );
    }
}
