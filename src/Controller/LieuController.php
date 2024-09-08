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
use Symfony\Component\Security\Http\Attribute\IsGranted;

class LieuController extends AbstractController
{

    public function __construct(
        private FileUploaderService    $fileUploaderService,
        private EntityManagerInterface $entityManager,
        private LieuRepository         $lieuRepository,
        private LieuService            $lieuService
    )
    {
    }

    #[IsGranted("ROLE_USER")]
    #[Route('/lieu/liste', name: 'app_lieu_list')]
    public function listLieu(): Response
    {
        return $this->render('lieu/index.html.twig', [
            'lieuList' => $this->lieuRepository->findAll(),
        ]);
    }

    #[IsGranted("ROLE_USER")]
    #[Route('/lieu/create', name: 'app_lieu_create')]
    public function createLieu(Request $request): Response
    {
        return $this->handleLieuForm($request, new Lieu(), 'create');
    }

    #[IsGranted("ROLE_USER")]
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
                $lieu->setFileName($this->fileUploaderService->upload("thumbnails",$profilePicture));
            } else {
                $lieu->setFileName("default.png");
            }

            $latlng = $this->lieuService->getLatLng($lieu->getAddress(), $lieu->getCity(), $lieu->getPostalCode());
            $lieu->setLatitude($latlng['lat']);
            $lieu->setLongitude($latlng['lng']);

            $this->lieuRepository->update($lieu);
            return $this->redirectToRoute('app_lieu_list');
        }

        return $this->render('lieu/' . $action . '.html.twig', [
            'form' => $form->createView(),
            'lieu' => $lieu,
        ]);
    }
}