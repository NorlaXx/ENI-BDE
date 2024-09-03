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
use Symfony\Component\String\Slugger\SluggerInterface;

class ActivityController extends AbstractController
{

    public function __construct(
        private ActivityRepository $activityRepository,
        private ActivityStateRepository $activityStateRepository
    )
    {}

    #[Route('/activity/create', name: 'activity_create')]
    public function createActivity(Request $request, SluggerInterface $slugger){
        $activity = new Activity();
        $form = $this->createForm(ActivityType::class, $activity);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            //Récupération du fichier et sauvegarde sur le serveur
            $file = $form->get('pictureFileName')->getData();
            $orifinalFileName = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
            $saveFileName = $slugger->slug($orifinalFileName);
            $newFileName = $saveFileName.'-'.uniqid().'.'.$file->guessExtension();
            $file->move(
                $this->getParameter('thumbnail_directory'),
                $newFileName
            );

            //TODO gérer les dates

            // Ajouter les propriétés manquantes
            $activity->setOrganisateur($this->getUser());
            $activity->setState($this->activityStateRepository->getDefautState());
            $activity->setDateCreation(new DateTime());
            $activity->setPictureFileName($newFileName);
            $this->activityRepository->createActivity($activity);
            //TODO Redirection de la route
        }

        return $this->render('activity/create.html.twig', [
            'form' => $form->createView()
        ]);
    }
}
