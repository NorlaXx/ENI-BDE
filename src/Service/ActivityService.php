<?php

namespace App\Service;

use App\Entity\Activity;
use App\Form\ActivityFilterType;
use App\Model\ActivityFilter;
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
    public function addOtherproperties(bool $isShare,Activity $activity, string $action): void
    {
        if($isShare){
            $activity->setState($this->activityStateRepository->getStateByCode("ACT_INS"));
            if (!in_array($this->security->getUser(), (array)$activity->getRegistered())){
                $activity->addInscrit($this->security->getUser());
            }
        } else {
            $activity->setState($this->activityStateRepository->getDefaultState());
        }

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

    /**
     * Filtre les sorties
     *
     * @param $request
     * @param $form
     * @return array
     */
    public function findByFilter($request, $form, $filter): array
    {
        $form->handleRequest($request, $form);

        if ($form->isSubmitted() && $form->isValid()) {
            $activities = $this->activityRepository->filter(
                $this->security->getUser()->getId(),
                $filter
            );
        } else {
            $activities = $this->activityRepository->findAll();
        }

        return [
            'form' => $form->createView(),
            'activities' => $activities,
            ];
    }

    public function removeOrganizer(Activity $activity): void
    {
        $state = $this->activityStateRepository->getCancelledState();
        $activity->setOrganizer(null);
        $activity->setState($state);
        $this->activityRepository->update($activity);
    }
}