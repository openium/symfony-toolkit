<?php

/**
 * WithUpload interface
 *
 * PHP Version 7.1
 *
 * @package  Openium\SymfonyToolKitBundle\Entity
 * @author   Thomas LEDUC <thomaslmoi15@hotmail.fr>, Alexandre Caillot <a.caillot@openium.fr>
 * @link     https://openium.fr/
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
     * @param string $path
     *
     * @return WithUploadInterface
     */
    public function setImagePath(string $path): WithUploadInterface;

    /**
     * Get the uploads sub-dir name
     *
     * @return string
     */
    public function getUploadsDir(): string;
}
