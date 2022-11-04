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
use Throwable;

/**
 * Class MissingContentException
 *
 * @package Openium\SymfonyToolKitBundle\Exception
 */
abstract class ContentExtractorException extends Exception
{
    protected ?string $key = null;

    public function __construct(
        string $key = "",
        string $message = "",
        int $code = 0,
        Throwable $previous = null
    ) {
        parent::__construct($message, $code, $previous);
        $this->key = $key;
    }

    public function getKey(): ?string
    {
        return $this->key;
    }
}
