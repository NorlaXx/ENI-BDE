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
        /** @var User */
        $user = $this->getUser();
        $activity->addInscrit($user);
        $this->entityManager->persist($activity);
        $this->entityManager->flush();
        return $this->redirectToRoute('app_home');
    }

    #[Route('/activity/remove/inscrit/{id}', name: 'activity_remove_inscrit')]
    public function removeInscription(int $id)
    {
        $activity = $this->activityRepository->find($id);
        /** @var User */
        $user = $this->getUser();
        $activity->removeInscrit($user);
        $this->entityManager->persist($activity);
        $this->entityManager->flush();
        return $this->redirectToRoute('app_home');
    }
    #[Route('/activity/remove/{id}', name: 'app_remove_activity')]
    public function removeActivity(int $id)
    {
        $activity = $this->activityRepository->find($id);
        /** @var User */
        $this->entityManager->remove($activity);
        $this->entityManager->flush();
        return $this->redirectToRoute('app_home');
    }

    #[Route('/activity/update/{id}', name: 'activity_update')]
    public function updateActivity(int $id, Request $request, SluggerInterface $slugger)
    {
        $activity = $this->activityRepository->find($id);
        if (!$activity) {
            throw $this->createNotFoundException('Activity not found');
        }
        $form = $this->createForm(ActivityUpdateType::class, $activity);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $file = $form->get('pictureFileName')->getData();
            if ($file) {
                /* Use Uploader Service to move file + set Image name on Entity*/
                $activity->setPictureFileName($this->fileUploaderService->upload($file));
            }
           /* $activity->setName($form->get('name')->getData());
            $activity->setCampus($form->get('campus')->getData());
            $activity->setLieu($form->get('lieu')->getData());
            $activity->setDateDebut($form->get('dateDebut')->getData());
            $activity->setDateFinalInscription($form->get('dateFinalInscription')->getData());
            $activity->setNbLimitParticipants($form->get('nbLimitParticipants')->getData());
            $this->entityManager->persist($activity);*/
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
            $file = $form->get('pictureFileName')->getData();
            $orifinalFileName = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
            $saveFileName = $slugger->slug($orifinalFileName);
            $newFileName = $saveFileName . '-' . uniqid() . '.' . $file->guessExtension();
            $file->move(
                $this->getParameter('thumbnail_directory'),
                $newFileName
            );

            //TODO gérer les dates
            $dateDebut = $form->get('dateDebut')->getData();
            $dateFinInscription = $form->get('dateFinalInscription')->getData();

            // Si la date de debut n'est pas rentrée, on prend la date de fin d'inscription comme date de debut
            // Si la date de fin d'inscription n'est pas rentrée, on prend la date de debut comme date de fin d'inscription
            // Si aucune date n'est rentrée, on renvoie une erreur
            // Si la date de début est inférieur à la date actuelle, on renvoie une erreur
            // Si la date de fin d'inscription est supérieur à la date de début, on renvoie une erreur
            // Si la date de fin d'inscription est inférieur à la date actuelle, on renvoie une erreur

            // Ajouter les propriétés manquantes
            $activity->setOrganisateur($this->getUser());
            $activity->setState($this->activityStateRepository->getDefautState());
            $activity->setDateCreation(new DateTime());
            $activity->setPictureFileName($newFileName);
            $this->activityRepository->createActivity($activity);
            return $this->redirectToRoute('app_home');
        }

        return $this->render('activity/create.html.twig', [
            'form' => $form->createView()
        ]);
    }
}
