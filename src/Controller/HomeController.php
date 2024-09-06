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
use Symfony\Component\Mailer\Mailer;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

class HomeController extends AbstractController
{
    public function __construct(
        private MailerInterface      $mailer,
        private ActivityRepository   $activityRepository,
        private RefreshStatusService $refreshStatusService,
    )
    {
    }

    #[IsGranted("ROLE_USER")]
    #[Route('/', name: 'app_home')]
    public function homePage(Request $request): Response
    {

        $this->refreshStatusService->refreshStatus();
        $filter = new ActivityFilter();
        $form = $this->createForm(ActivityFilterType::class, $filter);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $activities = $this->activityRepository->filter(
                $this->getUser()->getId(),
                $filter
            );
        } else {
            $activities = $this->activityRepository->findAll();
        }

        return $this->render('home/index.html.twig', [
            'form' => $form->createView(),
            'activities' => $activities,
            'user' => $this->getUser()
        ]);
    }
}