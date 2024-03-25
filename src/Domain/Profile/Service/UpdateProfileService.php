<?php

namespace App\Domain\Profile\Service;

use App\Domain\Profile\Data\ProfileSetting;
use App\Domain\Profile\Repository\ProfileRepository;
use App\Domain\User\Data\User;
use App\Service\FlashMessageService;

class UpdateProfileService
{
    public function __construct(
        private ProfileRepository $profileRepository,
        private FlashMessageService $flash
    ) {

    }

    public function updateUserPreferences(User $user, array $data)
    {
        ValidateUserSettingsService::validateSettings($data);
        foreach($data as $k => &$d) {
            $d = new ProfileSetting($k, $d);
        }
        $settings = FetchUserSettingsService::mapSettings($data);
        $this->profileRepository->setUserSettings($user->getId(), $settings);
        $this->flash->addSuccessMessage("Your preferences have been updated");
    }

}
