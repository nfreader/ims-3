<?php

namespace App\Domain\Incident\Service;

use App\Domain\Incident\Data\Incident;
use App\Domain\Permissions\Data\PermissionTypeEnum;
use App\Domain\Incident\Repository\IncidentPermissionsRepository;
use App\Domain\User\Data\User;
use App\Service\FlashMessageService;
use DI\Attribute\Inject;
use Exception;
use JustSteveKing\StatusCode\Http;

class IncidentSettingsService
{
    #[Inject()]
    private FetchIncidentService $incidentService;

    #[Inject()]
    private IncidentPermissionsRepository $permissionsRepository;

    #[Inject()]
    private FlashMessageService $flash;

    private Incident $incident;

    public function updateSetting(string $setting, array $data, Incident $incident, User $user)
    {

        $this->incident = $incident;

        switch ($setting) {
            default:
                throw new Exception("Invalid data", (int) Http::BAD_REQUEST);
                break;

            case 'permissions':
                $this->updatePermissions($data);
                break;
        }
    }

    private function updatePermissions(array $data)
    {
        $updates['agencies'] = [];
        $updates['roles'] = [];
        foreach($data as $k => $d) {
            $k = explode('-', $k);
            if('agency' === $k[1]) {
                $updates['agencies'][$k[2]][] = $d;
            } elseif('role' === $k[1]) {
                $updates['roles'][$k[2]][] = $d;
            } else {
                throw new Exception('Invalid data provided', (int) Http::BAD_REQUEST);
            }
        }
        foreach($updates['agencies'] as $agency => &$a) {
            $this->updatePermissionsForAgency($agency, array_sum($a));
        }
        foreach($updates['roles'] as $role => &$r) {
            $this->updatePermissionsForRole($role, array_sum($r));
        }
        $this->flash->addSuccessMessage("Permissions for this incident have been updated");
    }

    private function updatePermissionsForAgency(int $agency, int $flags)
    {
        $this->permissionsRepository->insertPermissions(PermissionTypeEnum::AGENCY, $agency, $flags, $this->incident->getId());
    }

    private function updatePermissionsForRole(int $role, int $flags)
    {
        $this->permissionsRepository->insertPermissions(PermissionTypeEnum::ROLE, $role, $flags, $this->incident->getId());
    }

}
