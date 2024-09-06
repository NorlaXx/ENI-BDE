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
        $finalActivities = [];

        $act_arc = $this->activityStateRepository->getStateByCode('ACT_ARC');
        $act_ter = $this->activityStateRepository->getStateByCode('ACT_TER');
        $act_ins_f = $this->activityStateRepository->getStateByCode('ACT_INS_F');

        foreach ($activities as $activity) {
            $currentDate = new \DateTime();
            $dateFinalInscription = $activity->getDateFinalInscription();
            $dateDebut = $activity->getDateDebut();
            $dateIn1Month = $dateDebut;
            $dateIn1Month->modify('+1 month');

            if($currentDate > $dateIn1Month && $activity->getState()->getId() != $act_arc->getId()) {
                $activity = $this->setStateByCode($activity, $act_arc);
            }else if($currentDate >= $dateDebut && $activity->getState()->getId() != $act_ter->getId()) {
                $activity = $this->setStateByCode($activity, $act_ter);
            }else if($currentDate > $dateFinalInscription && $activity->getState()->getId() != $act_ins_f->getId()) {
                $activity = $this->setStateByCode($activity, $act_ins_f);
            }

            $finalActivities[] = $activity;
        }

        return $finalActivities;
    }

    private function setStateByCode($activity, $state)
    {
        $activity->setState($state);

        $this->activityRepository->update($activity);
        return $activity;
    }
}   