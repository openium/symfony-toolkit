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
    public static function getDateTimeFromString(
        string $dateString,
        ?string $format = null,
        ?DateTimeZone $dateTimeZone = null
    ): DateTime | false {
        if (null === $format) {
            if (strlen($dateString) > 10) {
                $format = str_starts_with(substr($dateString, -3), ':')
                    ? DateTimeInterface::ATOM
                    : DateTimeInterface::ISO8601;
            } else {
                $format = 'Y-m-d';
            }
        }

        if (!$dateTimeZone instanceof \DateTimeZone) {
            $dateTimeZone = new DateTimeZone('Europe/Paris');
        }

        return DateTime::createFromFormat(
            $format,
            $dateString,
            $dateTimeZone
        );
    }
}
