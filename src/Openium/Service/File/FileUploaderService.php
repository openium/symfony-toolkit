<?php

    namespace App\Service\File;

    use Openium\Entity\Utils\Upload\UploadableInterface;
    use Symfony\Component\HttpFoundation\File\Exception\FileException;
    use Symfony\Component\HttpFoundation\File\UploadedFile;
    use Symfony\Component\HttpKernel\Exception\ConflictHttpException;
    use Symfony\Component\HttpKernel\KernelInterface;

    class FileUploaderService implements FileUploaderServiceInterface
    {
        /** @var string */
        protected $publicDirPath;

        /** @var string */
        protected $uploadDirName;

        /** @var string */
        protected $uploadDirPath;

        /**
         * FileUploaderService constructor.
         * @param KernelInterface $kernel
         */
        public function __construct(KernelInterface $kernel)
        {
            $this->publicDirPath = $kernel->getRootDir() . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . 'public';

            $this->uploadDirName = 'uploads';
            $this->uploadDirPath = DIRECTORY_SEPARATOR . $this->uploadDirName;
        }

        /**
         * Prepare the uploaf path for the file
         *
         * @param UploadableInterface $uploadEntity
         *
         * @return UploadableInterface
         */
        public function prepareUploadPath(UploadableInterface $uploadEntity): UploadableInterface
        {
            if (!is_null($uploadEntity->getFile())) {

                /** @var UploadedFile $file */
                $file = $uploadEntity->getFile();
                $format = strtolower($file->guessClientExtension());

                if (!is_null($uploadEntity->getImagePath())) {
                    $this->remove($uploadEntity);
                }

                $imageName = substr(sha1(uniqid(mt_rand(), true)), 0, 32) . '.' . $format;
                $imagePath = $this->uploadDirPath . DIRECTORY_SEPARATOR . 'app' . DIRECTORY_SEPARATOR . $imageName;

                $uploadEntity->setImagePath($imagePath);
            }

            return $uploadEntity;
        }

        /**
         * Upload the file
         *
         * @param UploadableInterface $uploadEntity
         *
         * @return UploadableInterface
         *
         * @throws ConflictHttpException
         */
        public function upload(UploadableInterface $uploadEntity): UploadableInterface
        {
            if (!is_null($uploadEntity->getFile())) {
                /** @var UploadedFile $file */
                $file = $uploadEntity->getFile();

                $imagePath = explode(DIRECTORY_SEPARATOR, $uploadEntity->getImagePath());
                $imageName = array_pop($imagePath);
                $folder = $this->publicDirPath . implode(DIRECTORY_SEPARATOR, $imagePath);

                try {
                    $file->move($folder, $imageName);
                    $uploadEntity->setFile(null);
                } catch (FileException $e) {
                    throw new ConflictHttpException($e->getMessage(), $e, $e->getCode());
                }
            }

            return $uploadEntity;
        }

        /**
         * Remove the file
         *
         * @param UploadableInterface $uploadEntity
         */
        public function remove(UploadableInterface $uploadEntity)
        {
            $removeFile = $this->publicDirPath . $uploadEntity->getImagePath();

            if (!is_null($uploadEntity->getImagePath()) && file_exists($removeFile)) {
                unlink($removeFile);
            }
        }
    }
?>