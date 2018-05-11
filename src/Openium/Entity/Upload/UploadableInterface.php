<?php

    namespace Openium\SymfonyToolKit\Entity\Upload;

    use Symfony\Component\HttpFoundation\File\File;

    interface UploadableInterface
    {
        # -------------------------------------------------------------
        #   File
        # -------------------------------------------------------------

        /**
         * @return ?File
         */
        public function getFile(): ?File;

        /**
         * @param File $file
         *
         * @return UploadableInterface
         */
        public function setFile(File $file): UploadableInterface;

        /**
         * @return ?string
         */
        public function getImagePath(): ?string;

        /**
         * @param string $path
         *
         * @return UploadableInterface
         */
        public function setImagePath(string $path): UploadableInterface;



        # -------------------------------------------------------------
        #   Created At
        # -------------------------------------------------------------

        /**
         * @return int
         */
        public function getCreatedAt(): int;

        /**
         * @param int $createdAt
         *
         * @return UploadableInterface
         */
        public function setCreatedAt(int $createdAt): UploadableInterface;




        # -------------------------------------------------------------
        #   Updated At
        # -------------------------------------------------------------

        /**
         * @return int
         */
        public function getUpdatedAt(): int;

        /**
         * @param int $updatedAt
         *
         * @return UploadableInterface
         */
        public function setUpdatedAt(int $updatedAt): UploadableInterface;
    }
?>