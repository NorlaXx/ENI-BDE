<?php

namespace App\Form;

use Symfony\Component\Form\DataTransformerInterface;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
class DataTransformer implements DataTransformerInterface
{
    private string $uploadDir;

    public function __construct()
    {
        $this->uploadDir = 'thumbnails';
    }

    public function transform($value): ?File
    {
        if (null === $value) {
            return null;
        }

        $filePath = $this->uploadDir . '/' . $value;

        if (!file_exists($filePath)) {
            throw new NotFoundHttpException('File not found: ' . $filePath);
        }

        return new File($filePath);
    }

    public function reverseTransform($value): ?string
    {
        if (null === $value) {
            return null;
        }

        return $value->getFilename();
    }
}