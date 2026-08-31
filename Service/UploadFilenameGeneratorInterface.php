<?php

namespace Openium\SymfonyToolKitBundle\Service;

use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

/**
 * Interface UploadFilenameGeneratorInterface
 *
 * Generates the filename (basename + extension) used to store an uploaded file. Override the
 * "openium_symfony_toolkit.upload_filename_generator" service to plug in a custom naming
 * strategy, inspired by VichUploaderBundle's namers, without touching FileUploaderService itself.
 *
 * @package Openium\SymfonyToolKitBundle\Service
 */
interface UploadFilenameGeneratorInterface
{
    /**
     * Generates the filename (including its extension) for the given file.
     *
     * @throws BadRequestHttpException if the file's extension cannot be determined
     */
    public function generate(File $file, ?string $imageName = null): string;
}
