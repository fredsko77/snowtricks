<?php
namespace App\Services;

use Symfony\Component\HttpFoundation\File\UploadedFile;

class UploadService
{

    /**
     * @var string $targetDirectory
     */
    private $targetDirectory;

    /**
     * @var string $dir
     */
    private $dir = 'uploads';

    const MAX_FILE_SIZE = 10; // File size MB

    const FILE_ACCEPTED = [
        'image' => ['jpeg', 'gif', 'jpg', 'png', 'svg'],
    ];

    public function __construct(string $targetDirectory)
    {
        $this->targetDirectory = $targetDirectory;
    }

    public function upload($uploadedFile)
    {
        if ($uploadedFile instanceof UploadedFile) {
            if (!$this->isDir()) {
                mkdir($this->targetDirectory, 0777, true);
            }
            $errors = [];
            if (!$this->checkExtension($uploadedFile->guessExtension())) {
                $errors['extension'] = 'Votre image doit être au format .jpg, .jpeg, .svg, .gif ou .png';
            }
            if ($this->mb($uploadedFile->getSize()) > self::MAX_FILE_SIZE) {
                $errors['size'] = 'Votre fichier est trop lourd. Poids max 10mb. ';
            }
            if (count($errors) > 0) {

                return $errors;
            }

            $filename = $this->filename($uploadedFile->guessExtension());

            $path = $this->targetDirectory . DIRECTORY_SEPARATOR . $filename;

            $uploadedFile->move($this->targetDirectory, $filename);

            return $this->dir . '/' . $filename;
        }
    }

    /**
     * @return bool
     */
    public function isDir(): bool
    {
        if (!file_exists($this->targetDirectory)) {
            return false;
        }
        return true;
    }

    /**
     * @return float $bytes
     */
    public function mb(int $fileSize): float
    {
        return number_format($fileSize / 1048576, 2);
    }

    public function checkExtension(string $fileExtension)
    {
        return in_array($fileExtension, self::FILE_ACCEPTED['image']);
    }

    public function filename(string $fileExtension)
    {
        return $this->generateFilename() . '.' . $fileExtension;
    }

    /**
     * Generate a filename
     * @return string
     */
    public function generateFilename(): string
    {
        $char_to_shuffle = 'azertyuiopqsdfghjklwxcvbnAZERTYUIOPQSDFGHJKLLMWXCVBN1234567890';
        return substr(str_shuffle($char_to_shuffle), 0, 20) . '-' . (new \DateTime)->format('YmwdHsiu');
    }

}
