<?php

namespace App\Controller;

use App\Form\ActivityFilterType;
use App\Form\FilterObject;
use App\Model\ActivityFilter;
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
        $filter = new ActivityFilter();
        $form = $this->createForm(ActivityFilterType::class, $filter);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $activities = $activityRepository->filter(
                $this->getUser()->getId(),
                $filter
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