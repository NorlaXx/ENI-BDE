<?php

namespace App\Controller;

use App\Repository\CampusRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class CampusController extends AbstractController
{
    private CampusRepository $campusRepository;

    public function __construct(CampusRepository $campusRepository)
    {
        $this->campusRepository = $campusRepository;
    }

    #[Route('/campus', name: 'app_home_campus')]
    public function homePage(): Response
    {
      $campus = $this->campusRepository->findAll();
        return $this->render('campus/index.html.twig', [
            'campusList' => $campus,
        ]);
    }

}