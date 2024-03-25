<?php

namespace App\Action\Profile;

use App\Action\Action;
use App\Action\ActionInterface;
use App\Domain\Profile\Service\UpdateProfileService;
use App\Exception\ValidationException;
use DI\Attribute\Inject;
use Exception;
use Nyholm\Psr7\Response;

class UpdateProfileAction extends Action implements ActionInterface
{
    #[Inject()]
    private UpdateProfileService $profileService;

    public function action(): Response
    {
        if('POST' === $this->request->getMethod()) {
            try {
                $this->profileService->updateUserPreferences(
                    $this->getUser(),
                    $this->request->getParsedBody()
                );
            } catch (ValidationException $e) {
                $this->addContext('errors', $e->getErrors());
            }
            return $this->redirectFor('user.profile');
        }
        return $this->render('profile/profile.html.twig');
    }
}
