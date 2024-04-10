<?php

namespace App\Action\User\Auth;

use App\Action\Action;
use App\Action\ActionInterface;
use App\Domain\User\Service\ResetPasswordService;
use DI\Attribute\Inject;
use Nyholm\Psr7\Response;

class ResetPasswordAction extends Action implements ActionInterface
{
    #[Inject()]
    private ResetPasswordService $resetService;

    public function action(): Response
    {
        if('POST' === $this->request->getMethod()) {
            if($this->getQueryPart('reset')) {
                $this->resetService->resetPassword($this->request->getParsedBody()['password']);
                $this->addSuccessMessage("Your password has been reset. You may now log in");

                return $this->redirectFor('home');
            }
            $this->resetService->generatePasswordReset(
                $this->request->getParsedBody()['email']
            );
            return $this->redirectFor('user.password', queryParams:['state' => 'ack']);
        }
        if($this->getQueryPart('state')) {
            return $this->render('guest/resetPasswordAcknowledge.html.twig');
        }
        //Reset flow
        if($this->getQueryPart('code')) {
            $this->resetService->validateCode($this->getQueryPart('code'));
            return $this->render('guest/resetPasswordForm.html.twig');
        } else { //Generate reset flow
            return $this->render('guest/resetPassword.html.twig');
        }
    }
}
