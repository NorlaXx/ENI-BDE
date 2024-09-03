<?php

namespace App\Controller;

use App\Form\ActivityFilterType;
use App\Repository\ActivityRepository;
use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class HomeController extends AbstractController
{
    #[Route('/', name: 'app_home')]
    public function homePage(ActivityRepository $activityRepository, Request $request, UserRepository $userRepository): Response
    {
        $form = $this->createForm(ActivityFilterType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            /** @var User */
            $user = $this->getUser();
            $activities = $activityRepository->filter(
                $user->getId(),
                $form->get("campus")->getData(),
                $form->get("name")->getData(),
                $form->get("dateMin")->getData(),
                $form->get("dateMax")->getData(),
                $form->get("organisateur")->getData(),
                $form->get("inscrit")->getData(),
                $form->get("finis")->getData()
            );
            dd($activities);
        } else {
            $activities = $activityRepository->findAll();
        }

        return $this->render('home/index.html.twig', [
            'form' => $form->createView(),
            'activities' => $activities
        ]);
    }
}