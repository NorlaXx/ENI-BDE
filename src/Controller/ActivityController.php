<?php

namespace App\Controller;

use App\Entity\Activity;
use App\Form\ActivityType;
use App\Form\ActivityUpdateType;
use App\Repository\ActivityRepository;
use App\Repository\ActivityStateRepository;
use App\Service\FileUploaderService;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\String\Slugger\SluggerInterface;

class ActivityController extends AbstractController
{

    public function __construct(
        private ActivityRepository      $activityRepository,
        private ActivityStateRepository $activityStateRepository,
        private EntityManagerInterface  $entityManager,
        private FileUploaderService $fileUploaderService
    )
    {
    }

    #[Route('/activity/add/inscrit/{id}', name: 'activity_add_inscrit')]
    public function addInscrit(int $id)
    {
        $activity = $this->activityRepository->find($id);
        /* Vérification du nombre de places restantes dans l'activité */
        if($activity->getInscrits()->count() <= $activity->getNbLimitParticipants()){
            /* vérification de la non inscription du l'utilisateur */
            if(!$activity->getInscrits()->contains($this->getUser())){
                $activity->addInscrit($this->getUser());
                $this->entityManager->persist($activity);
                $this->entityManager->flush();
            }
        }
        return $this->redirectToRoute('app_home');
    }

    #[Route('/activity/remove/inscrit/{id}', name: 'activity_remove_inscrit')]
    public function removeInscription(int $id)
    {
        $activity = $this->activityRepository->find($id);
        if($activity->getInscrits()->contains($this->getUser())){
            $user = $this->getUser();
            $activity->removeInscrit($user);
            $this->entityManager->persist($activity);
            $this->entityManager->flush();
            return $this->redirectToRoute('app_home');
        }
    }
    #[Route('/activity/remove/{id}', name: 'app_remove_activity')]
    public function removeActivity(int $id)
    {
        $activity = $this->activityRepository->find($id);
        if($activity->getState() != 6){
            $activity->setState(6);
            $this->entityManager->persist($activity);
            $this->entityManager->flush();
        }
    }

    #[Route('/activity/update/{id}', name: 'activity_update')]
    public function updateActivity(int $id, Request $request, SluggerInterface $slugger)
    {
        $activity = $this->activityRepository->find($id);
        if (!$activity) {
            throw $this->createNotFoundException('Activity not found');
        }
        $form = $this->createForm(ActivityType::class, $activity);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $file = $form->get('fileName')->getData();
            if ($file) {
                /* Use Uploader Service to move file + set Image name on Entity*/
                $activity->setPictureFileName($this->fileUploaderService->upload($file));
            }
            $this->entityManager->flush();
            return $this->redirectToRoute('app_home');
        }

        return $this->render('activity/update.html.twig', [
            'form' => $form->createView(),
            'activity' => $activity
        ]);
    }

    #[Route('/activity/create', name: 'activity_create')]
    public function createActivity(Request $request, SluggerInterface $slugger)
    {
        $activity = new Activity();
        $form = $this->createForm(ActivityType::class, $activity);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            //Récupération du fichier et sauvegarde sur le serveur
            $file = $form->get('fileName')->getData();
            if ($file){
                $activity->setPictureFileName($this->fileUploaderService->upload($file));
            }else{
                $activity->setPictureFileName('defaut_activity_picture.webp');
            }

            $dateDebut = $form->get('dateDebut')->getData();
            $dateFinInscription = $form->get('dateFinalInscription')->getData();

            // Si la date de debut n'est pas rentrée, on prend la date de fin d'inscription comme date de debut et inversement
            if (!$dateDebut && $dateFinInscription) {
                $dateDebut = $dateFinInscription;
            }elseif (!$dateFinInscription && $dateDebut){
                $dateFinInscription = $dateDebut;
            }

            //erreurs du formulaire
            if (!$dateDebut && !$dateFinInscription){
                return $this->render('activity/create.html.twig', [
                    'form' => $form->createView(),
                    'errorMessage' => 'Veuillez renseigner au moins une date'
                ]);
            }elseif ($dateDebut < new DateTime() || $dateFinInscription < new DateTime()) {
                return $this->render('activity/create.html.twig', [
                    'form' => $form->createView(),
                    'errorMessage' => 'La date de début et la date de fin d\'inscription doivent être supérieures à la date actuelle'
                ]);
            }elseif ($dateFinInscription > $dateDebut){
                return $this->render('activity/create.html.twig', [
                    'form' => $form->createView(),
                    'errorMessage' => 'La date de fin d\'inscription doit être inférieur à la date de début'
                ]);
            }

            // Ajouter les propriétés manquantes
            $activity->setOrganisateur($this->getUser());
            $activity->setState($this->activityStateRepository->getDefautState());
            $activity->setDateDebut($dateDebut);
            $activity->setDateFinalInscription($dateFinInscription);
            $activity->setDateCreation(new DateTime());
            $this->activityRepository->createActivity($activity);
            return $this->redirectToRoute('app_home');
        }

        return $this->render('activity/create.html.twig', [
            'form' => $form->createView()
        ]);
    }
}
