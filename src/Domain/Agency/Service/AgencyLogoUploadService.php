<?php

namespace App\Domain\Agency\Service;

use Psr\Container\ContainerInterface;
use DI\Attribute\Inject;
use Exception;
use League\Flysystem\Filesystem;
use Nyholm\Psr7\UploadedFile;
use Ramsey\Uuid\Nonstandard\Uuid;
use Symfony\Component\HttpFoundation\Session\Session;

class AgencyLogoUploadService
{
    private Filesystem $fileSystem;

    #[Inject()]
    private AgencyLogoValidator $logoValidator;

    #[Inject()]
    private Session $session;

    public function __construct(private ContainerInterface $container)
    {
        $this->fileSystem = $container->get(FileSystem::class);
    }

    public function uploadLogo(UploadedFile $file): ?string
    {
        $name = $this->generateFileName();
        $extension = $this->getExtension($file);
        $tmpPath = sprintf('/tmp/%s.%s', $name, $extension);
        $file->moveTo($tmpPath);
        if($this->logoValidator->validateDimensions($tmpPath)) {
            $this->fileSystem->write(sprintf('%s.%s', $name, $extension), file_get_contents($tmpPath));
            return sprintf("%s.%s", $name, $extension);
        }
        // $this->session->getFlashBag()->add('danger', "Logos must be square images between 256x256 and 512x512 pixels");
        throw new Exception('Logos must be square images between 256x256 and 512x512 pixels', 401);
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
