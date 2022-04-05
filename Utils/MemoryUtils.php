<?php

namespace Openium\SymfonyToolKitBundle\Utils;

/**
 * Class MemoryService
 *
 * @package Openium\SymfonyToolKitBundle\Utils
 */
class MemoryUtils
{
    /**
     * convert
     *
     * @param int $bytes
     *
     * @return string
     */
    public static function convert(int $bytes): string
    {
        $unit = ['b', 'kb', 'mb', 'gb', 'tb', 'pb'];
        return @round($bytes / pow(1024, ($i = floor(log($bytes, 1024)))), 2) . ' ' . $unit[$i];
    }

    /**
     * getMemoryUsage
     *
     * @param bool $real_usage
     *
     * @return void
     */
    public static function getMemoryUsage(bool $real_usage = true): string
    {
        return self::convert(memory_get_usage($real_usage));
    }
}
