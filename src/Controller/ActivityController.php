<?php

namespace App\Controller;

use App\Entity\Activity;
use App\Form\ActivityType;
use App\Form\CancelType;
use App\Repository\ActivityRepository;
use App\Service\ActivityService;
use App\Service\FileUploaderService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

class ActivityController extends AbstractController
{

    public function __construct(
        private ActivityRepository      $activityRepository,
        private EntityManagerInterface  $entityManager,
        private FileUploaderService $fileUploaderService,
        private ActivityService $activityService
    )
    {
    }

    #[IsGranted('register', 'activity')]
    #[Route('/activity/add/inscrit/{id}', name: 'activity_add_inscrit')]
    public function addInscrit(Activity $activity): RedirectResponse
    {
        $activity->addInscrit($this->getUser());
        $this->activityRepository->update($activity);

        return $this->redirectToRoute('app_home');
    }

    #[IsGranted('withdraw', 'activity')]
    #[Route('/activity/remove/inscrit/{id}', name: 'activity_remove_inscrit')]
    public function removeInscription(Activity $activity): RedirectResponse
    {
        $activity->removeInscrit($this->getUser());
        $this->activityRepository->update($activity);
        return $this->redirectToRoute('app_home');
    }

    #[IsGranted('edit', 'activity')]
    #[Route('/activity/cancel/{id}', name: 'app_cancel_activity')]
    public function cancelActivity(Activity $activity, Request $request): RedirectResponse|Response
    {
        $form = $this->createForm(CancelType::class);
        $form->handleRequest($request);
        //Récupération de la sortie
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

    #[IsGranted('edit', 'activity')]
    #[Route('/activity/update/{id}', name: 'activity_update')]
    public function updateActivity(Activity $activity, Request $request): RedirectResponse|Response
    {
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
    public function createActivity(Request $request): RedirectResponse|Response
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
