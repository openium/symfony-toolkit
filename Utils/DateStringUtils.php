<?php

namespace Openium\SymfonyToolKitBundle\Utils;

use DateTime;
use DateTimeInterface;
use DateTimeZone;

/**
 * Class DateStringService
 *
 * @package Openium\SymfonyToolKitBundle\Service
 *
 * @deprecated since 7.0, will be removed in 8.0. The format guess based on string length/suffix
 *             is fragile; use Symfony Serializer's DateTimeNormalizer (or plain
 *             `new DateTimeImmutable($dateString)`, which already parses ATOM/ISO8601 and most
 *             common formats) instead.
 */
class DateStringUtils
{
    public static function getDateTimeFromString(
        string $dateString,
        ?string $format = null,
        ?DateTimeZone $dateTimeZone = null
    ): DateTime | false {
        trigger_deprecation(
            'openium/symfony-toolkit',
            '7.0',
            'The "%s" class is deprecated and will be removed in 8.0, use Symfony Serializer\'s'
            . ' DateTimeNormalizer (or "new DateTimeImmutable($dateString)") instead.',
            self::class
        );

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
