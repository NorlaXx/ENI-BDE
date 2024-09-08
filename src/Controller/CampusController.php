<?php

namespace App\Controller;

use App\Repository\CampusRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

class CampusController extends AbstractController
{

    public function __construct(
        private CampusRepository $campusRepository,
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

}