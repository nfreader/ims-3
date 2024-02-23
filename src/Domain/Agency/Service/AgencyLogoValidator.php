<?php

namespace App\Domain\Agency\Service;

use Cake\Validation\Validator;
use Exception;
use League\Flysystem\Filesystem;
use Nyholm\Psr7\UploadedFile;
use Psr\Container\ContainerInterface;

class AgencyLogoValidator
{
    // private Filesystem $fileSystem;

    // public function __construct(private ContainerInterface $container)
    // {
    //     $this->fileSystem = $container->get(FileSystem::class);
    // }

    public function validateLogo(UploadedFile $file): void
    {
        // $validator = new Validator();
        // $validator->requirePresence('clientMediaType', message:"Agency logo must be a PNG file");

    }

    public function validateDimensions(string $filePath): bool
    {
        $info = getimagesize($filePath);
        if(!$info) {
            throw new Exception("The image file provided is invalid", 401);
        }
        if($info[0] === $info[1] && ((256 <= $info[0]) && ($info[0] <= 512)) && (256 <= $info[1]) && ($info[1] <= 512)) {
            return true;
        }
        return false;
    }

}
