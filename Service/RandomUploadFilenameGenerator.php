<?php

namespace Openium\SymfonyToolKitBundle\Service;

use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

/**
 * Class RandomUploadFilenameGenerator
 *
 * Default filename generator: a random 32-char hex basename (or the given $imageName), suffixed
 * with the file's guessed extension.
 *
 * @package Openium\SymfonyToolKitBundle\Service
 */
class RandomUploadFilenameGenerator implements UploadFilenameGeneratorInterface
{
    #[\Override]
    public function generate(File $file, ?string $imageName = null): string
    {
        $fileName = $imageName ?? substr(sha1(uniqid((string)random_int(0, mt_getrandmax()), true)), 0, 32);

        $fileExtension = $this->guessExtension($file);
        if ($fileExtension === null || $fileExtension === '') {
            throw new BadRequestHttpException(
                'The file extension is empty.',
                null,
                Response::HTTP_UNSUPPORTED_MEDIA_TYPE
            );
        }

        return sprintf('%s.%s', $fileName, $fileExtension);
    }

    private function guessExtension(File $file): ?string
    {
        if ($file instanceof UploadedFile) {
            return strtolower($file->guessClientExtension() ?? '');
        }

        $fileNameParts = explode('.', $file->getFilename());
        if (count($fileNameParts) > 1) {
            return trim($fileNameParts[count($fileNameParts) - 1]);
        }

        return null;
    }
}
