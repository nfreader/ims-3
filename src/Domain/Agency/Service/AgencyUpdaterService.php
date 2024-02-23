<?php

namespace App\Domain\Agency\Service;

use App\Domain\Agency\Data\Agency;
use App\Domain\Agency\Repository\AgencyRepository;
use DI\Attribute\Inject;
use App\Domain\Agency\Service\AgencyLogoUploadService;
use Exception;
use Symfony\Component\HttpFoundation\Session\Session;

class AgencyUpdaterService
{
    #[Inject()]
    private AgencyLogoUploadService $logoUploader;

    #[Inject()]
    private AgencyRepository $agencyRepository;

    #[Inject()]
    private Session $session;

    public function updateAgency(Agency $agency, array $data, array $files): void
    {
        if(!$files['logo']->getError()) {
            try {
                $data['logo'] = $this->logoUploader->uploadLogo($files['logo']);
            } catch (Exception $e) {
                $this->session->getFlashBag()->add('warning', $e->getMessage());
                $data['logo'] = $agency->getLogo();
            }
        } else {
            $data['logo'] = $agency->getLogo();
        }
        $this->agencyRepository->updateAgency($agency->getId(), ...$data);
    }

}
