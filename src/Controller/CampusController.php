<?php

namespace App\Controller;

use App\Entity\Campus;
use App\Form\CampusType;
use App\Repository\CampusRepository;
use App\Service\FileUploaderService;
use App\Service\LieuService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

class CampusController extends AbstractController
{


    public function __construct(
        private CampusRepository $campusRepository,
        private FileUploaderService $fileUploaderService,
        private LieuService $lieuService
    )
    {
    }
    #[IsGranted("ROLE_USER")]
    #[Route('/campus', name: 'app_home_campus')]
    public function homePage(): Response
    {
        return $this->render('campus/index.html.twig', [
            'campusList' => $this->campusRepository->findAll(),
        ]);
    }
    #[IsGranted('ROLE_ADMIN')]
    #[Route('/campus/update/{id}', name: 'app_campus_update')]
    public function updateActivity(int $id, Request $request): RedirectResponse|Response
    {
        return $this->handleCampusForm($request, $this->campusRepository->find($id), 'update');
    }
    #[IsGranted('ROLE_ADMIN')]
    #[Route('/campus/create', name: 'app_campus_create')]
    public function createActivity(Request $request): RedirectResponse|Response
    {
        return $this->handleCampusForm($request, new Campus(), 'create');
    }


    private function handleCampusForm(Request $request, Campus $campus, string $action): RedirectResponse|Response
    {
        $form = $this->createForm(CampusType::class, $campus);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $file = $form->get('fileName')->getData();
            if ($file){
                $campus->setFileName($this->fileUploaderService->upload("thumbnails", $file));
            }else if($action == 'create'){
                $campus->setFileName('defaut_activity_picture.webp');
            }
            $latlng = $this->lieuService->getLatLng($campus->getAdresse(), $campus->getCity(), $campus->getPostalCode());
            $campus->setLatitude($latlng['lat']);
            $campus->setLongitude($latlng['lng']);

            $this->campusRepository->update($campus);
            return $this->redirectToRoute('app_home_campus');
        }

        return $this->render('campus/' . $action . '.html.twig', [
            'form' => $form->createView(),
            'campus' => $campus,
        ]);
    }

}