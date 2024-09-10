<?php

namespace App\Controller;


use App\Entity\User;
use App\Form\CsvImport;
use App\Form\UserType;
use App\Repository\UserRepository;
use App\Repository\ActivityRepository;
use App\Security\UserProvider;
use App\Service\CsvImportService;
use App\Service\FileUploaderService;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Exception\ORMException;
use Doctrine\ORM\OptimisticLockException;
use PhpParser\Node\Scalar\MagicConst\File;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mailer\Exception\TransportExceptionInterface;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Component\String\Slugger\SluggerInterface;


class UserController extends AbstractController
{

    public function __construct(
        private MailerInterface $mailer,
        private UserRepository         $userRepository,
        private Security               $security,
        private CsvImportService     $csvImportService,
        private FileUploaderService    $fileUploaderService,
        private ActivityRepository     $activityRepository,
        private EntityManagerInterface $entityManager,
        private UserPasswordHasherInterface $passwordHasher,
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
    #[Route(path: '/profil/search/{id}', name: 'app_profile_view')]
    public function profileView(int $id): Response
    {
        $userSearch = $this->userRepository->find($id);
        $activitesSearch = $this->activityRepository->findByCreator($userSearch);

        return $this->render('user/profile.html.twig', [
            'user' => $userSearch,
            'activities' => $activitesSearch,
        ]);
    }

    #[IsGranted("ROLE_ADMIN")]
    #[Route(path: '/profil/create', name: 'app_user_create')]
    public function create(Request $request): Response
    {
        $user = new User();
        $form = $this->createForm(UserType::class, $user);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            if(!$this->userRepository->findBy(['email' => $user->getEmail()])) {
                /*  set default PictureFileName on Entity */
                $hashedPassword = $this->passwordHasher->hashPassword(
                    $user,
                    "password"
                );
                $user->setPassword($hashedPassword);
                $user->setActive(true);
                $user->setFileName("icons8-avatar-48-66d71c8776738.png");
                $this->entityManager->persist($user);
                $this->entityManager->flush();
                return $this->redirectToRoute('app_profil_list');
            }

        }
        return $this->render('user/profileCreate.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    #[IsGranted("ROLE_ADMIN")]
    #[Route(path: '/profil/list', name: 'app_profil_list')]
    public function profileList(Request $request): Response
    {
        $form = $this->createForm(CsvImport::class, new File());
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $csvFile = $this->fileUploaderService->upload("csv",$form->get("csvFile")->getData());
            $results = $this->csvImportService->importCsv("csv/" . $csvFile);
            if (!empty($results['errors'])) {
                foreach ($results['errors'] as $error) {
                    $this->addFlash('error', $error);
                }
            }
            return $this->redirectToRoute('app_profil_list');
        }

        return $this->render('user/profileList.html.twig', [
            'form' => $form->createView(),
            'users' => $this->userRepository->findAll(),
        ]);
    }

    // TODO CHECK WHY USER CAN'T BE FIND WITH ID AUTOMATICALLY

    /**
     * @throws TransportExceptionInterface
     */
    #[IsGranted("ROLE_ADMIN")]
    #[Route(path: '/profil/delete/{id}', name: 'app_user_delete')]
    public function deleteUser(int $id): Response
    {
        $user = $this->userRepository->find($id);
        $email = (new Email())
            ->from('noreply@blizzfull.fr')
            ->to($user->getEmail())
            ->subject('Compte ENI-BDE supprimé')
            ->text('Votre compte ENI-BDE a été supprimé')
            ->html('<p>Votre compte '. $user->getPseudo() .'ENI-BDE a été supprimé</p>');
        try {
            $this->mailer->send($email);
        } catch (TransportExceptionInterface $e) {
            dd($e->getMessage());
        }

        $this->userRepository->removeAllRelations($user);
        $this->entityManager->flush();
        return $this->redirectToRoute("app_profil_list");
    }

    // TODO CHECK WHY USER CAN'T BE FIND WITH ID AUTOMATICALLY
    #[IsGranted("ROLE_ADMIN")]
    #[Route(path: '/profil/desactivate/{id}', name: 'app_user_desactivate')]
    public function desactivateUser(int $id): Response
    {
        // TODO QUE FAIRE DU USER, PROPRIETE A AJOUTER
        $user = $this->userRepository->find($id);
        $user->setActive(false);
        return $this->redirectToRoute("app_profil_list");

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
                $user->setFileName($this->fileUploaderService->upload("thumbnails", $profilePicture));
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