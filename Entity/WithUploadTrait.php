<?php

/**
 * WithUpload trait
 *
 * PHP Version >=8.0
 *
 * @package  Openium\SymfonyToolKitBundle\Entity
 * @author   Openium <contact@openium.fr>
 * @license  Openium All right reserved
 * @link     https://www.openium.fr/
 */

namespace Openium\SymfonyToolKitBundle\Entity;

use Symfony\Component\HttpFoundation\File\File;

/**
 * Trait WithUploadTrait
 *
 * @package  Openium\SymfonyToolKitBundle\Entity
 */
trait WithUploadTrait
{
    protected ?File $file = null;

    protected ?string $imagePath = null;

    /**
     * Getter for file
     *
     * @return File|null
     */
    public function getFile(): ?File
    {
        return $this->file;
    }

    /**
     * Setter for file
     *
     * @param File|null $file
     *
     * @return WithUploadInterface
     */
    public function setFile(?File $file): WithUploadInterface
    {
        $this->file = $file;
        return $this;
    }

    /**
     * Getter for imagePath
     *
     * @return string|null
     */
    public function getImagePath(): ?string
    {
        return $this->imagePath;
    }

    /**
     * Setter for imagePath
     *
     * @param string|null $path
     *
     * @return WithUploadInterface
     */
    public function setImagePath(?string $path): WithUploadInterface
    {
        $this->imagePath = $path;
        return $this;
    }
}
