<?php

namespace App\Controller;

use App\Form\ActivityFilterType;
use App\Form\FilterObject;
use App\Repository\ActivityRepository;
use App\Repository\UserRepository;
use App\Service\RefreshStatusService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class HomeController extends AbstractController
{
    public function __construct(private RefreshStatusService $refreshStatusService)
    {
    }

    #[Route('/', name: 'app_home')]
    public function homePage(ActivityRepository $activityRepository, Request $request): Response
    {
        $this->refreshStatusService->refreshStatus();

        $form = $this->createForm(ActivityFilterType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $activities = $activityRepository->filter(
                $this->getUser()->getId(),
                $form->get("campus")->getData(),
                $form->get("name")->getData(),
                $form->get("dateMin")->getData(),
                $form->get("dateMax")->getData(),
                $form->get("organisateur")->getData(),
                $form->get("inscrit")->getData(),
                $form->get("finis")->getData()
            );
        } else {
            $activities = $activityRepository->findAll();
        }
        return $this->render('home/index.html.twig', [
            'form' => $form->createView(),
            'activities' => $activities,
            'user' => $this->getUser()
        ]);
    }
}