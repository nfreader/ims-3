<?php

namespace App\Action\Agency;

use App\Action\Action;
use App\Domain\Agency\Service\AgencyCreationService;
use App\Domain\Agency\Service\AgencyLogoUploadService;
use DI\Attribute\Inject;
use Nyholm\Psr7\Response;

class NewAgencyAction extends Action
{
    #[Inject()]
    private AgencyLogoUploadService $logoUploader;

    #[Inject()]
    private AgencyCreationService $agencyCreator;

    public function action(): Response
    {
        $request = $this->getRequest();
        $files = $request->getUploadedFiles();
        $logo = null;
        if(!$files['logo']->getError()) {
            $file = $request->getUploadedFiles()['logo'];
            $logo = $this->logoUploader->uploadLogo($file);
        }
        $data = $request->getParsedBody();
        $this->agencyCreator->createNewAgency(
            $data['name'],
            $logo,
            $data['fullname'],
            $data['location']
        );
        $this->addSuccessMessage("This agency has been created");
        return $this->redirectFor('agencies.home');
    }
}
