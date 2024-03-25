<?php

namespace App\Domain\Profile\Service;

use App\Domain\Profile\Data\ProfileDefaults;
use App\Domain\Profile\Repository\ProfileRepository;
use App\Domain\User\Data\User;

class FetchUserSettingsService
{
    public function __construct(private ProfileRepository $profileRepository)
    {

    }

    public function getSettingsForUser(User $user): array
    {
        var_dump(ProfileDefaults::DEFAULT_SETTINGS);
        return $this->getSettingsForUser($user);
    }

    /**
     * mapSettings
     *
     * @param array $settings<ProfileSetting>
     * @return array
     */
    public static function mapSettings(array $settings): array
    {
        $defaults = ProfileDefaults::DEFAULT_SETTINGS;
        foreach($settings as $s) {
            $defaults[$s->getName()] = $s->getValue();
        }
        return $defaults;
    }

}
