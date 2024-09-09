<?php

namespace App\Service;

use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\String\Slugger\SluggerInterface;

class FileUploaderService
{
    private $slugger;

    public function __construct(SluggerInterface $slugger)
    {
        $this->slugger = $slugger;
    }

    public function upload(string $targetDir, UploadedFile $file): string
    {
        $originalFileName = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
        $safeFileName = $this->slugger->slug($originalFileName);
        $newFileName = $safeFileName.'-'.uniqid().'.'.$file->guessExtension();

        $file->move($targetDir, $newFileName);

        return $newFileName;
    }

    public function getTargetDirectory()
    {
        return $this->targetDirectory;
    }
}