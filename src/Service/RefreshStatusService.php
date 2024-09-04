<?php

namespace App\Service;

use App\Repository\ActivityRepository;
use App\Repository\ActivityStateRepository;

class RefreshStatusService
{
    public function __construct(private ActivityRepository $activityRepository, private ActivityStateRepository $activityStateRepository){}

    public function refreshStatus()
    {
        $activities = $this->activityRepository->findAll();
        foreach ($activities as $activity) {
            $currentDate = new \DateTime();
            $dateIn1Month = new \DateTime();
            $dateIn1Month->modify('+1 month');
            $dateFinalInscription = $activity->getDateFinalInscription();
            $dateDebut = $activity->getDateDebut();

            if($dateIn1Month > $dateDebut) {
                $this->setStateById($activity, 6);
                continue;
            }
            if($currentDate > $dateDebut) {
                $this->setStateById($activity, 5);
                continue;
            }
            if($currentDate == $dateDebut) {
                $this->setStateById($activity, 4);
                continue;
            }
            if($currentDate > $dateFinalInscription) {
                $this->setStateById($activity, 3);
                continue;
            }
        }
    }

    private function setStateById($activity, $id){
        $state = $this->activityStateRepository->getStateById($id);
        $activity->setState($state);

        $this->activityRepository->update($activity);
    }
}