<?php

namespace Openium\SymfonyToolKitBundle\Entity;

use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\Serializer\Attribute\Ignore;

/**
 * Trait WithUploadTrait
 *
 * @package  Openium\SymfonyToolKitBundle\Entity
 */
trait WithUploadTrait
{
    #[Ignore]
    protected ?File $file = null;

    protected ?string $imagePath = null;

    /**
     * Getter for file
     */
    public function getFile(): ?File
    {
        return $this->file;
    }

    /**
     * Setter for file
     */
    public function setFile(?File $file): WithUploadInterface
    {
        $this->file = $file;
        return $this;
    }

    /**
     * Getter for imagePath
     */
    public function getImagePath(): ?string
    {
        return $this->imagePath;
    }

    /**
     * Setter for imagePath
     */
    public function setImagePath(?string $path): WithUploadInterface
    {
        $this->imagePath = $path;
        return $this;
    }
}
