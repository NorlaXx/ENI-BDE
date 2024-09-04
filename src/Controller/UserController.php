<?php

namespace App\Controller;


use App\Form\UserType;
use App\Repository\UserRepository;
use App\Security\UserProvider;
use App\Service\FileUploaderService;
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

    public function __construct(
        private UserRepository $userRepository,
        private Security            $security,
        private FileUploaderService $fileUploaderService
    )
    {
    }

    #[Route(path: '/profil', name: 'app_profile')]
    public function profile(): Response
    {
        if ($this->security->isGranted('ROLE_USER')) {
            return $this->render('user/profile.html.twig');
        } else {
            return $this->redirectToRoute('app_home');
        }
    }
    #[Route(path: '/profil/{id}', name: 'app_profile_view')]
    public function profileView(int $id): Response
    {
        if ($this->security->isGranted('ROLE_USER')) {
            return $this->render('user/profileView.html.twig', [
                'user' => $this->userRepository->find($id),
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
    public function edit(EntityManagerInterface $entityManager, Request $request, SluggerInterface $slugger): Response
    {

        $user = $this->getUser();


        $form = $this->createForm(UserType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $profilePicture = $form->get('profilePicture')->getData();
            /* Upload file using fileUploader Service + set PictureFileName on Entity*/
            if ($profilePicture) {
                $user->setPictureFileName($this->fileUploaderService->upload($profilePicture));
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