<?php

namespace App\Domain\Profile\Service;

use App\Exception\ValidationException;
use Cake\Validation\Validator;

class ValidateUserSettingsService
{
    public static function validateSettings(array $data): void
    {
        $validator = new Validator();
        $validator
            ->requirePresence(['appLayout'])
            ->inList('appLayout', ['dashboard','linear'], "The app layout you specified is invalid");

        $errors = $validator->validate($data);

        if($errors) {
            throw new ValidationException("Errors were detected", $errors);
        }
    }

}
