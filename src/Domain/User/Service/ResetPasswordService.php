<?php

namespace App\Domain\User\Service;

use App\Domain\Notification\Email\Service\SendEmailNotificationService;
use App\Domain\User\Data\PasswordResetToken;
use App\Domain\User\Repository\UserPasswordResetRepository;
use App\Domain\User\Repository\UserRepository;
use App\Service\ApplicationSettingsService;
use Exception;
use Symfony\Component\HttpFoundation\Session\Session;

/**
 * ResetPasswordService
 *
 * @see https://paragonie.com/blog/2017/02/split-tokens-token-based-authentication-protocols-without-side-channels
 *
 */
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

    /**
     * generatePasswordReset
     *
     * Looks up a user by their email address. If found, generate a password
     * reset token, save it to the database, and send a password reset email
     *
     * @param string $email
     * @return void
     */
    public function generatePasswordReset(string $email): void
    {
        $user = $this->fetchUserService->findUserByEmail($email);
        if(!$user) {
            return;
        }
        $token = PasswordResetToken::newToken($user);
        $this->persistPasswordResetToken($token);
        $this->email->sendTemplateEmail($user->getEmail(), 'email/resetPassword.html.twig', ['token' => $token->getClearTextToken()]);
    }

    /**
     * validateCode
     *
     * Given the reset code from a password reset request, validate it against
     * what is stored in the database. Throws an exception if anything is wrong
     *
     * If the password reset code is valid, we set the user's ID in the session
     * so we know who we're resetting the password for later on.
     *
     * @param string $code
     * @return true
     * @throws Exception
     */
    public function validateCode(string $code): true
    {
        if(64 !== strlen($code)) {
            throw new Exception("The password reset code you provided is invalid");
        }
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


    /**
     * resetPassword
     *
     * Sets the password for the user identified in the session.
     *
     * @param string $password
     * @return void
     * @throws Exception
     */
    public function resetPassword(string $password)
    {
        if(!$user = $this->session->get('passwordReset', null)) {
            throw new Exception("The password reset code you provided is invalid");
        }
        $this->session->invalidate();
        $this->userRepository->setPassword(password_hash($password, PASSWORD_DEFAULT), $user);
    }
    /**
     * validateToken
     *
     * Checks that the user provided token matches what was queried from the
     * database.
     *
     * @param string $knownToken
     * @param string $userToken
     * @return boolean
     */
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

    /**
     * persistPasswordResetToken
     *
     * Handles storing the password reset token in the database
     *
     * @param PasswordResetToken $token
     * @return bool
     */
    private function persistPasswordResetToken(PasswordResetToken $token): bool
    {
        try {
            $this->resetRepository->insertNewPasswordReset(
                $token->getUser()->getId(),
                $token->getSelector(),
                $token->getHashedValidator(
                    $this->settings->getSettings()['secret_key']
                )
            );
        } catch (UniqueConstraintViolationException $e) {
            //TODO: Logging
            return false;
        } catch (Exception $f) {
            return false;
        }
        return true;
    }
}
