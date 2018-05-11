<?php

    namespace Openium\Entity\Utils\Upload;

    use Doctrine\ORM\Mapping as ORM;
    use Symfony\Component\HttpFoundation\File\File;
    use Symfony\Component\Validator\Constraints as Assert;

    trait UploadableTrait
    {
        /**
         * @var ?File
         *
         * @Assert\Image(mimeTypesMessage="Ce fichier n'est pas une image")
         * @Assert\File(maxSize="2M", maxSizeMessage="Ce fichier est trop volumineux")
         */
        protected $file;

        /**
         * @var int
         *
         * @ORM\Column(name="`created_at`", type="integer")
         */
        protected $createdAt;

        /**
         * @var int
         *
         * @ORM\Column(name="`updated_at`", type="integer", nullable=true)
         */
        protected $updatedAt;




        # -------------------------------------------------------------
        #   File
        # -------------------------------------------------------------

        /**
         * @return ?File
         */
        public function getFile(): ?File
        {
            return $this->file;
        }

        /**
         * @param File $file
         *
         * @return UploadableInterface
         */
        public function setFile($file): UploadableInterface
        {
            $this->file = $file;

            return $this;
        }




        # -------------------------------------------------------------
        #   Created At
        # -------------------------------------------------------------

        /**
         * @return int
         */
        public function getCreatedAt(): int
        {
            return $this->createdAt;
        }

        /**
         * @param int $createdAt
         *
         * @return UploadableInterface
         */
        public function setCreatedAt(int $createdAt): UploadableInterface
        {
            $this->createdAt = $createdAt;

            return $this;
        }




        # -------------------------------------------------------------
        #   Updated At
        # -------------------------------------------------------------

        /**
         * @return int
         */
        public function getUpdatedAt(): int
        {
            return $this->updatedAt;
        }

        /**
         * @param int $updatedAt
         *
         * @return UploadableInterface
         */
        public function setUpdatedAt(int $updatedAt): UploadableInterface
        {
            $this->updatedAt = $updatedAt;

            return $this;
        }
    }
?>