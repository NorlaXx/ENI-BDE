<?php

namespace App\Controller;

use App\Entity\Activity;
use App\Form\ActivityType;
use App\Form\CancelType;
use App\Repository\ActivityRepository;
use App\Repository\ActivityStateRepository;
use App\Service\ActivityService;
use App\Service\FileUploaderService;
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
        private FileUploaderService $fileUploaderService,
        private ActivityService $activityService
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

    #[Route('/activity/cancel/{id}', name: 'app_cancel_activity')]
    public function cancelActivity(int $id, Request $request)
    {
        $form = $this->createForm(CancelType::class);
        $form->handleRequest($request);
        //Récupération de la sortie
        $activity = $this->activityRepository->find($id);
        if ($form->isSubmitted() && $form->isValid()) {
            //récupération du motif
            $reason = $form->get('reason')->getData();
            //Annulation de la sortie
            $this->activityService->cancelActivity($activity, $reason);

            return $this->redirectToRoute('app_home');
        }

        return $this->render('activity/cancel.html.twig', [
            'activity' => $activity,
            'form' => $form->createView()
        ]);

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
            $file = $form->get('pictureFileName')->getData();
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
            $file = $form->get('pictureFileName')->getData();
            if ($file){
                $activity->setPictureFileName($this->fileUploaderService->upload($file));
            }else{
                $activity->setPictureFileName('defaut_activity_picture.webp');
            }

            // Ajouter les propriétés manquantes
            $this->activityService->addOtherproperties($activity);

            // Sauvegarde en BDD
            $this->activityRepository->createActivity($activity);

            return $this->redirectToRoute('app_home');
        }

        return $this->render('activity/create.html.twig', [
            'form' => $form->createView()
        ]);
    }
}
