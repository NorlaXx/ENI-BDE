<?php

namespace App\Controller;

use App\Entity\Lieu;
use App\Form\ActivityFilterType;
use App\Form\LieuType;
use App\Form\LieuUpdateType;
use App\Repository\ActivityRepository;
use App\Repository\LieuRepository;
use App\Service\FileUploaderService;
use App\Service\LieuService;
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
        private LieuRepository $lieuRepository,
        private LieuService $lieuService
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
        return $this->handleLieuForm($request, $lieu, 'create');
    }

    #[Route('/lieu/update/{id}', name: 'app_lieu_update')]
    public function updateLieu(int $id, Request $request): Response
    {
        $lieu = $this->lieuRepository->find($id);
        if (!$lieu) {
            throw $this->createNotFoundException('Lieu non trouvé');
        }
        return $this->handleLieuForm($request, $lieu, 'update');
    }

    private function handleLieuForm(Request $request, Lieu $lieu, string $action): Response
    {
        $form = $this->createForm(LieuType::class, $lieu);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $profilePicture = $form->get('fileName')->getData();
            if ($profilePicture) {
                $lieu->setFileName($this->fileUploaderService->upload($profilePicture));
            }

            $latlng = $this->lieuService->getLatLng($lieu->getAddresse(), $lieu->getVille(), $lieu->getCp());
            $lieu->setLat($latlng['lat']);
            $lieu->setLongitude($latlng['lng']);

            $this->entityManager->persist($lieu);
            $this->entityManager->flush();

            return $this->redirectToRoute('app_lieu_list');
        }

        return $this->render('lieu/' . $action . '.html.twig', [
            'form' => $form->createView(),
            'lieu' => $lieu,
        ]);
    }
}