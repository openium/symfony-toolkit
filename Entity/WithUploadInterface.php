<?php

/**
 * WithUpload interface
 *
 * PHP Version 7.1, 7.2
 *
 * @package  Openium\SymfonyToolKitBundle\Entity
 * @author   Openium <contact@openium.fr>
 * @license  Openium All right reserved
 * @link     https://www.openium.fr/
 */

namespace Openium\SymfonyToolKitBundle\Entity;

use Symfony\Component\HttpFoundation\File\File;

/**
 * Interface WithUploadInterface
 *
 * @package  Openium\SymfonyToolKitBundle\Entity
 */
interface WithUploadInterface
{
    /**
     * Get the file
     *
     * @return ?File
     */
    public function getFile(): ?File;

    /**
     * Set the file
     *
     * @param $file
     *
     * @return WithUploadInterface
     */
    public function setFile($file): WithUploadInterface;

    /**
     * Set the file local path
     *
     * @return ?string
     */
    public function getImagePath(): ?string;

    /**
     * Get the file local path
     *
     * @param string|null $path
     *
     * @return WithUploadInterface
     */
    public function setImagePath($path): WithUploadInterface;

    /**
     * Get the uploads sub-dir name
     *
     * @return string
     */
    public function getUploadsDir(): string;
}
