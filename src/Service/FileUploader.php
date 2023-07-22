<?php

namespace App\Service;

use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\String\Slugger\SluggerInterface;

class FileUploader
{
    private $targetDirectory;
    private $slugger;

    public function __construct(SluggerInterface $slugger)
    {
        $this->slugger = $slugger;
    }

    public function upload(UploadedFile $file)
    {
        $originalFilename = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
        $safeFilename = $this->slugger->slug($originalFilename);
        $fileName = $safeFilename . '-' . uniqid() . '.' . $file->guessExtension();

        try {
            $file->move($this->getTargetDirectory(), $fileName);
        } catch (FileException $e) {
            // ... handle exception if something happens during file upload
        }

        return $fileName;
    }

    public function delete(string $fileName)
    {
       
            $filePath = $this->getTargetDirectory() . '/' . $fileName;

            if (file_exists($filePath)) {
                unlink($filePath);
            }
        
    }

    public function setTargetDirectory($targetDirectory): self
    {
        $this->targetDirectory = $targetDirectory;

        return $this;
    }

    public function getTargetDirectory()
    {
        return $this->targetDirectory;
    }
}
