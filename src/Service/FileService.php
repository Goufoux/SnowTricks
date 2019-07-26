<?php

namespace App\Service;

use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;

class FileService
{
    private $fileName;
    private $params;

    public function __construct(ParameterBagInterface $parameterBagInterface)
    {
        $this->params = $parameterBagInterface;
    }

    public function uploadFile(UploadedFile $file, string $directory)
    {
        $this->setFileName($file->guessClientExtension());
        
        $file->move($this->params->get($directory), $this->getFileName());
    }

    public function getFileName()
    {
        return $this->fileName;
    }

    public function deleteFile(string $directory, string $fileName)
    {
        $state = unlink($this->params->get($directory).$fileName);

        return $state;
    }

    private function setFileName(string $extension)
    {
        $this->fileName = uniqid().'.'.$extension;

        return $this;
    }
}