<?php

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
     */
    public function getFile(): ?File;

    /**
     * Set the file
     */
    public function setFile(?File $file): WithUploadInterface;

    /**
     * Set the file local path
     */
    public function getImagePath(): ?string;

    /**
     * Get the file local path
     */
    public function setImagePath(?string $path): WithUploadInterface;

    /**
     * Get the uploads sub-dir name
     */
    public function getUploadsDir(): string;
}
