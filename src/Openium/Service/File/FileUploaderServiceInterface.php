<?php

    namespace App\Service\File;

    use Openium\Entity\Utils\Upload\UploadableInterface;
    use Symfony\Component\HttpKernel\Exception\ConflictHttpException;

    interface FileUploaderServiceInterface
    {
        /**
         * Prepare the uploaf path for the file
         *
         * @param UploadableInterface $uploadEntity
         *
         * @return UploadableInterface
         */
        public function prepareUploadPath(UploadableInterface $uploadEntity): UploadableInterface;

        /**
         * Upload the file
         *
         * @param UploadableInterface $uploadEntity
         *
         * @return UploadableInterface
         *
         * @throws ConflictHttpException
         */
        public function upload(UploadableInterface $uploadEntity): UploadableInterface;

        /**
         * Remove the file
         *
         * @param UploadableInterface $uploadEntity
         */
        public function remove(UploadableInterface $uploadEntity);
    }
?>