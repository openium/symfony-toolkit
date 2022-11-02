<?php
/**
 * PHP Version >=8.0
 *
 * @package  Openium\SymfonyToolKitBundle\Exception
 * @author   Openium <contact@openium.fr>
 * @license  Openium All right reserved
 * @link     https://www.openium.fr/
 */

namespace Openium\SymfonyToolKitBundle\Exception;

use Exception;

/**
 * Class MissingContentException
 *
 * @package Openium\SymfonyToolKitBundle\Exception
 */
class ContentExtractorDateFormatException extends ContentExtractorException
{
    public const MESSAGE = "Wrong date format";
    public const CODE = 2015151208;

    public function __construct(
        string $message = self::MESSAGE,
        int $code = self::CODE,
        Exception $previous = null
    ) {
        parent::__construct($message, $code, $previous);
    }
}
