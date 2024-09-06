<?php

namespace App\Controller;


use App\Form\UserType;
use App\Repository\UserRepository;
use App\Repository\ActivityRepository;
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
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Component\String\Slugger\SluggerInterface;


class UserController extends AbstractController
{

    public function __construct(
        private UserRepository      $userRepository,
        private Security            $security,
        private FileUploaderService $fileUploaderService,
        private ActivityRepository  $activityRepository,
        private EntityManagerInterface $entityManager
    )
    {
    }
    #[IsGranted("ROLE_USER")]
    #[Route(path: '/profil', name: 'app_profile')]
    public function profile(): Response
    {
        $activites = $this->activityRepository->findByCreator($this->getUser());

            return $this->render('user/profile.html.twig', [
                'activities' => $activites,
                'user' => $this->getUser(),
            ]);
        }

    #[IsGranted("ROLE_USER")]
    #[Route(path: '/profil/{id}', name: 'app_profile_view')]
    public function profileView(User $user): Response
    {
        return $this->render('user/profileView.html.twig', [
            'user' => $user,
        ]);
    }

    #[IsGranted("ROLE_ADMIN")]
    #[Route(path: '/profil/list', name: 'app_profile_list')]
    public function profileList(): Response
    {
        return $this->render('user/profileView.html.twig', [
            'users' => $this->userRepository->findAll(),
        ]);
    }


    /**
     * @throws OptimisticLockException
     * @throws ORMException
     */
    #[IsGranted("ROLE_USER")]
    #[Route(path: '/profileEdit', name: 'app_profile_edit')]
    public function edit(Request $request, SluggerInterface $slugger): Response
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
            $this->entityManager->persist($user);
            $this->entityManager->flush();
            return $this->redirectToRoute('app_profile');
        }

        return $this->render('user/profileEdit.html.twig', [
            'form' => $form->createView(),
        ]);
    }
}