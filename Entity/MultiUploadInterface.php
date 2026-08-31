<?php

namespace Openium\SymfonyToolKitBundle\Entity;

use Symfony\Component\HttpFoundation\File\File;

/**
 * Interface MultiUploadInterface
 *
 * Additive alternative to {@see WithUploadInterface} for entities that need more than one
 * upload property, inspired by VichUploaderBundle's named mappings: each upload slot is
 * identified by a $field key instead of being a single fixed pair of properties.
 *
 * WithUploadInterface/WithUploadTrait are untouched and remain the right choice for an entity
 * with a single upload property.
 *
 * @package  Openium\SymfonyToolKitBundle\Entity
 */
interface MultiUploadInterface
{
    /**
     * Get the file for the given upload field
     */
    public function getFile(string $field): ?File;

    /**
     * Set the file for the given upload field
     */
    public function setFile(string $field, ?File $file): static;

    /**
     * Get the file local path for the given upload field
     */
    public function getImagePath(string $field): ?string;

    /**
     * Set the file local path for the given upload field
     */
    public function setImagePath(string $field, ?string $path): static;

    /**
     * Get the uploads sub-dir name for the given upload field
     */
    public function getUploadsDir(string $field): string;
}
