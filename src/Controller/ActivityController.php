<?php

namespace App\Controller;

use App\Entity\Activity;
use App\Form\ActivityType;
use App\Repository\ActivityRepository;
use App\Repository\ActivityStateRepository;
use DateTime;
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
    public function createActivity(Request $request){
        $activity = new Activity();
        $form = $this->createForm(ActivityType::class, $activity);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            // Ajouter les propriétés manquantes
            $activity->setOrganisateur($this->getUser());
            $activity->setState($this->activityStateRepository->getDefautState());
            $activity->setDateCreation(new DateTime());
            $this->activityRepository->createActivity($activity);
            //Redirection de la route
        }

        return $this->render('activity/create.html.twig', [
            'form' => $form->createView()
        ]);
    }
}
