<?php

namespace App\Controller;

use App\Entity\Lieu;
use App\Form\ActivityFilterType;
use App\Form\LieuType;
use App\Form\LieuUpdateType;
use App\Repository\ActivityRepository;
use App\Repository\LieuRepository;
use App\Service\FileUploaderService;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class LieuController extends AbstractController
{

    public function __construct(
        private FileUploaderService $fileUploaderService,
        private EntityManagerInterface $entityManager,
        private LieuRepository $lieuRepository
    )
    {
    }

    #[Route('/lieu/liste', name: 'app_lieu_list')]
    public function listLieu(): Response
    {
        return $this->render('lieu/index.html.twig', [
            'lieuList' => $this->lieuRepository->findAll(),
        ]);
    }
    #[Route('/lieu/create', name: 'app_lieu_create')]
    public function createLieu(Request $request): Response
    {
        $lieu = new Lieu();
        $form = $this->createForm(LieuType::class, $lieu);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $profilePicture = $form->get('fileName')->getData();
            /* Upload file using fileUploader Service + set PictureFileName on Entity*/
            if ($profilePicture) {
                $lieu->setFileName($this->fileUploaderService->upload($profilePicture));
            }
            $lieu->setLat("26");
            $lieu->setLongitude("26");
            $this->entityManager->persist($lieu);
            $this->entityManager->flush();
        }
        return $this->render('lieu/create.html.twig', [
            'form' => $form->createView(),
        ]);
    }
    #[Route('/lieu/update/{id}', name: 'app_lieu_update')]
    public function updateLieu(int $id, Request $request): Response
    {
        $form = $this->createForm(LieuUpdateType::class, $this->lieuRepository->find($id));
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $profilePicture = $form->get('fileName')->getData();
            /* Upload file using fileUploader Service + set PictureFileName on Entity*/
            if ($profilePicture) {
                $this->getUser()->setPictureFileName($this->fileUploaderService->upload($profilePicture));
            }
            $this->entityManager->persist($this->getUser());
            $this->entityManager->flush();
        }
        return $this->render('lieu/update.html.twig', [
            'form' => $form->createView(),
        ]);
    }
}