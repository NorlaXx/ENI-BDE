<?php

namespace App\Service;

use App\Repository\ActivityRepository;
use App\Repository\ActivityStateRepository;

class RefreshStatusService
{
    public function __construct(private ActivityRepository $activityRepository, private ActivityStateRepository $activityStateRepository){}

    public function refreshStatus(): void
    {
        $activities = $this->activityRepository->findAll();
        foreach ($activities as $activity) {
            $currentDate = new \DateTime();
            $dateFinalInscription = $activity->getDateFinalInscription();
            $dateDebut = $activity->getDateDebut();
            $dateIn1Month = $dateDebut;
            $dateIn1Month->modify('+1 month');

            if($currentDate > $dateIn1Month) {
                $this->setStateByCode($activity, 'ACT_ARC');
                continue;
            }
            if($currentDate >= $dateDebut) {
                $this->setStateByCode($activity, 'ACT_TER');
                continue;
            }
            if($currentDate > $dateFinalInscription) {
                $this->setStateByCode($activity, 'ACT_INS_F');
            }
        }
    }

    private function setStateByCode($activity, $code): void
    {
        $state = $this->activityStateRepository->getStateByCode($code);
        $activity->setState($state);

        $this->activityRepository->update($activity);
    }
}