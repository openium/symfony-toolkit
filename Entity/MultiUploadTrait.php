<?php

namespace Openium\SymfonyToolKitBundle\Entity;

use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\Serializer\Attribute\Ignore;

/**
 * Trait MultiUploadTrait
 *
 * Storage for {@see MultiUploadInterface}: one file/path pair per $field key, instead of the
 * single fixed pair managed by {@see WithUploadTrait}.
 *
 * @package  Openium\SymfonyToolKitBundle\Entity
 */
trait MultiUploadTrait
{
    /** @var array<string, File|null> */
    #[Ignore]
    protected array $files = [];

    /** @var array<string, string|null> */
    protected array $imagePaths = [];

    /**
     * Getter for the file of the given upload field
     */
    public function getFile(string $field): ?File
    {
        return $this->files[$field] ?? null;
    }

    /**
     * Setter for the file of the given upload field
     */
    public function setFile(string $field, ?File $file): static
    {
        $this->files[$field] = $file;
        return $this;
    }

    /**
     * Getter for the image path of the given upload field
     */
    public function getImagePath(string $field): ?string
    {
        return $this->imagePaths[$field] ?? null;
    }

    /**
     * Setter for the image path of the given upload field
     */
    public function setImagePath(string $field, ?string $path): static
    {
        $this->imagePaths[$field] = $path;
        return $this;
    }
}
