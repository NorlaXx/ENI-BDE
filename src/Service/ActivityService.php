<?php

namespace App\Service;

use App\Entity\Activity;
use App\Repository\ActivityRepository;
use App\Repository\ActivityStateRepository;
use DateTime;
use Symfony\Bundle\SecurityBundle\Security;

class ActivityService
{
    public function __construct(
        private ActivityStateRepository $activityStateRepository,
        private ActivityRepository $activityRepository,
        private Security $security
    )
    {

    }

    /**
     * Ajoute les propriétés manquantes à une sortie
     *
     * @param Activity $activity
     * @return void
     */
    public function addOtherproperties(Activity $activity): void
    {
        $activity->setState($this->activityStateRepository->getDefaultState());
        $activity->setOrganizer($this->security->getUser());
        $activity->setCreationDate(new DateTime());
    }

    /**
     * Annulation d'une sortie
     *
     * @param Activity $activity
     * @param $reason
     * @return void
     */
    public function cancelActivity(Activity $activity, $reason): void
    {
        $description = $activity->getDescription();
        $activity->setDescription("$description \nMotif : $reason");
        $activity->setState($this->activityStateRepository->getCancelledState());
        $this->activityRepository->update($activity);
    }
}