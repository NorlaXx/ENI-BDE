<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

class UserController extends AbstractController
{
    public function __construct(Security $security)
    {
        $this->security = $security;
    }

    #[Route(path: '/profile', name: 'app_profile')]
    public function profile(): Response
    {
        if($this->security->isGranted('ROLE_USER')){
            $userData = $this->security->getUser();

            return $this->render('user/profile.html.twig', [
                'user' => $userData,
            ]);
        } else {
            return $this->redirectToRoute('app_home');
        }

    }

    #[Route(path: '/profileEdit', name: 'app_profile_edit')]
    public function profileEdit(): Response
    {
        if($this->security->isGranted('ROLE_USER')){
            $userData = $this->security->getUser();

            return $this->render('user/profile.html.twig', [
                'user' => $userData,
            ]);
        } else {
            return $this->redirectToRoute('app_home');
        }
    }
}