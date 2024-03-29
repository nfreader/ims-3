<?php

namespace App\Domain\User\Service;

use App\Domain\Notification\Email\Service\SendEmailNotificationService;
use App\Domain\User\Data\PasswordResetToken;
use App\Domain\User\Repository\UserPasswordResetRepository;
use App\Domain\User\Repository\UserRepository;
use App\Service\ApplicationSettingsService;
use Exception;
use Symfony\Component\HttpFoundation\Session\Session;

class ResetPasswordService
{
    public function __construct(
        private UserRepository $userRepository,
        private FetchUserService $fetchUserService,
        private UserPasswordResetRepository $resetRepository,
        private SendEmailNotificationService $email,
        private ApplicationSettingsService $settings,
        private Session $session
    ) {

    }

    public function generatePasswordReset(string $email)
    {
        $user = $this->fetchUserService->findUserByEmail($email);
        if(!$user) {
            return;
        }
        $token = PasswordResetToken::newToken($user);
        $this->persistPasswordResetToken($token);
        $this->email->sendTemplateEmail($user->getEmail(), 'email/resetPassword.html.twig', ['token' => $token->getClearTextToken()]);
    }

    public function validateCode(string $code): bool
    {
        $selector = substr($code, 0, 32);
        $validator = substr($code, 32);
        $token = $this->resetRepository->getTokenBySelector($selector);
        if(!$token) {
            throw new Exception("The password reset code you provided is invalid");
        }
        if($this->validateToken($token['validator'], $validator)) {
            $this->resetRepository->deleteResetToken($selector);
            $this->session->set('passwordReset', $token['user']);
            return true;
        } else {
            throw new Exception("The password reset code you provided is invalid");
        }
    }

    public function resetPassword(string $password)
    {
        if(!$user = $this->session->get('passwordReset', null)) {
            throw new Exception("The password reset code you provided is invalid");
        }
        $this->session->invalidate();
        $this->userRepository->setPassword(password_hash($password, PASSWORD_DEFAULT), $user);
    }

    private function validateToken(string $knownToken, string $userToken): bool
    {
        $userToken = bin2hex(hash_hmac(
            PasswordResetToken::HASH_ALGO,
            $userToken,
            $this->settings->getSettings()['secret_key'],
            true
        ));
        return hash_equals(
            $knownToken,
            $userToken
        );
    }

    private function persistPasswordResetToken(PasswordResetToken $token)
    {
        $this->resetRepository->insertNewPasswordReset(
            $token->getUser()->getId(),
            $token->getSelector(),
            $token->getHashedValidator(
                $this->settings->getSettings()['secret_key']
            )
        );
    }

}
