<?php

namespace App\Controller;

use App\Entity\Activity;
use App\Form\ActivityFilterType;
use App\Form\ActivityType;
use App\Form\UserType;
use App\Repository\ActivityRepository;
use App\Repository\ActivityStateRepository;
use App\Repository\UserRepository;
use DateTime;
use phpDocumentor\Reflection\Types\Boolean;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class ActivityController extends AbstractController
{

    public function __construct(
        private ActivityRepository $activityRepository,
        private ActivityStateRepository $activityStateRepository
    )
    {}

    #[Route('/activity/create', name: 'activity_create')]
    public function createActivity(Request $request): Response
    {
        $activity = new Activity();
        $form = $this->createForm(ActivityType::class, $activity);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            // Ajouter les propriétés manquantes
            $activity->setOrganisateur($this->getUser());
            $activity->setState($this->activityStateRepository->getDefautState());
            $activity->setDateCreation(new DateTime());
            $this->activityRepository->createActivity($activity);
            //TODO Redirection de la route
        }

        return $this->render('activity/create.html.twig', [
            'form' => $form->createView()
        ]);
    }
    #[Route('/activity', name: 'activity_list')]
    public function listActivities(ActivityRepository $activityRepository, Request $request, UserRepository $userRepository): Response
    {
        $activities = $activityRepository->findAll();
        $form = $this->createForm(ActivityFilterType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            /** @var User */
            $user = $this->getUser();
            $activities = $activityRepository->filter(
                $user->getId(),
                $form->get("campus")->getData(),
                $form->get("name")->getData(),
                $form->get("dateMin")->getData(),
                $form->get("dateMax")->getData(),
                $form->get("organisateur")->getData(),
                $form->get("inscrit")->getData(),
                $form->get("finis")->getData()
            );
            dd($activities);
        }

        return $this->render('activity/all.html.twig', [
            'form' => $form->createView(),
            'activities' => $activities
        ]);
    }

}
