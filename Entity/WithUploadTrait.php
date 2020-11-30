<?php

/**
 * WithUpload trait
 *
 * PHP Version >=7.1
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
    /**
     * @var File
     */
    protected $file;

    /**
     * @var string
     */
    protected $imagePath;

    /**
     * Getter for file
     *
     * @return mixed
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
     * @return self
     */
    public function setFile($file): WithUploadInterface
    {
        $this->file = $file;
        return $this;
    }

    /**
     * Getter for imagePath
     *
     * @return string
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
     * @return self
     */
    public function setImagePath($path): WithUploadInterface
    {
        $this->imagePath = $path;
        return $this;
    }
}
