<?php

namespace App\Controller;


use App\Form\UserType;
use App\Security\UserProvider;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Exception\ORMException;
use Doctrine\ORM\OptimisticLockException;
use Symfony\Bundle\SecurityBundle\Security;


use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\String\Slugger\SluggerInterface;


class UserController extends AbstractController
{
    private $security;
    private $userProvider;


    public function __construct(Security $security)
    {
        $this->security = $security;

    }

    #[Route(path: '/profile', name: 'app_profile')]
    public function profile(): Response
    {
        if ($this->security->isGranted('ROLE_USER')) {
            $user = $this->getUser();
            return $this->render('user/profile.html.twig', [
                'user' => $user,
            ]);
        } else {
            return $this->redirectToRoute('app_home');
        }

    }

    /**
     * @throws OptimisticLockException
     * @throws ORMException
     */
    #[Route(path: '/profileEdit', name: 'app_profile_edit')]
    public function edit(EntityManagerInterface  $entityManager, Request $request, SluggerInterface $slugger): Response
    {

        $user = $this->getUser();


        $form = $this->createForm(UserType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $profilePicture = $form->get('profilePicture')->getData();

            if ($profilePicture) {
                $originalFilename = pathinfo($profilePicture->getClientOriginalName(), PATHINFO_FILENAME);
                $safeFilename = $slugger->slug($originalFilename);
                $newFilename = $safeFilename . '-' . uniqid() . '.' . $profilePicture->guessExtension();

                $profilePicture->move(
                    $this->getParameter('avatars_directory'),
                    $newFilename
                );

                $user->setPictureFileName($newFilename);
            }
            $entityManager->persist($user);
            $entityManager->flush();

            return $this->redirectToRoute('app_profile');
        }

        return $this->render('user/profileEdit.html.twig', [
            'form' => $form->createView(),
        ]);
    }
}