<?php

namespace App\Service;

use App\Entity\Activity;
use App\Repository\ActivityStateRepository;
use DateTime;
use Symfony\Bundle\SecurityBundle\Security;

class ActivityService
{
    public function __construct(
        private ActivityStateRepository $activityStateRepository,
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
    public function addOtherproperties(Activity $activity){
        $activity->setState($this->activityStateRepository->getDefautState());
        $activity->setOrganisateur($this->security->getUser());
        $activity->setDateCreation(new DateTime());
    }
}