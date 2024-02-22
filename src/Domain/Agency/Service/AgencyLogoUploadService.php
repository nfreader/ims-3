<?php

namespace App\Domain\Agency\Service;

use Psr\Container\ContainerInterface;
use DI\Attribute\Inject;
use League\Flysystem\Filesystem;
use Nyholm\Psr7\UploadedFile;
use Ramsey\Uuid\Nonstandard\Uuid;

class AgencyLogoUploadService
{
    private Filesystem $fileSystem;

    #[Inject()]
    private AgencyLogoValidator $logoValidator;

    public function __construct(private ContainerInterface $container)
    {
        $this->fileSystem = $container->get(FileSystem::class);
    }

    public function uploadLogo(UploadedFile $file)
    {
        $name = $this->generateFileName();
        $extension = $this->getExtension($file);
        $tmpPath = sprintf('/tmp/%s.%s', $name, $extension);
        $file->moveTo($tmpPath);
        if($this->logoValidator->validateDimensions($tmpPath)) {
            $this->fileSystem->write(sprintf('%s.%s', $name, $extension), file_get_contents($tmpPath));
            return sprintf("%s.%s", $name, $extension);
        }
    }

    private function getExtension(UploadedFile $file): string
    {
        return pathinfo($file->getClientFilename())['extension'];
    }

    private function generateFileName(): string
    {
        return Uuid::uuid7()->toString();
    }

}
