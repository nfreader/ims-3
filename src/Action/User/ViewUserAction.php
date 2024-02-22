<?php

namespace App\Action\User;

use App\Action\Action;
use App\Domain\Agency\Repository\AgencyMembershipRepository;
use App\Domain\Agency\Repository\AgencyRepository;
use App\Domain\User\Repository\UserRepository;
use App\Domain\User\Service\FetchUserService;
use DI\Attribute\Inject;
use Nyholm\Psr7\Response;

class ViewUserAction extends Action
{
    #[Inject()]
    private FetchUserService $userService;

    #[Inject()]
    private AgencyRepository $agencyRepository;

    public function action(): Response
    {
        $user = $this->getArg('user');
        $user = $this->userService->getUser($user);
        $agencies = $this->agencyRepository->getAgencies();
        return $this->render('manage/user/user.html.twig', [
            'user' => $user,
            'agencies' => $agencies
        ]);
    }

}
