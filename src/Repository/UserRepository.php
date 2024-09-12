<?php

namespace App\Repository;

use App\Entity\Activity;
use App\Entity\User;
use App\Service\ActivityService;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bridge\Doctrine\Security\User\UserLoaderInterface;
use Symfony\Component\Security\Core\Exception\UnsupportedUserException;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\PasswordUpgraderInterface;

/**
 * @extends ServiceEntityRepository<User>
 */
class UserRepository extends ServiceEntityRepository implements PasswordUpgraderInterface, UserLoaderInterface
{
    public function __construct(private ActivityService $activityService,ManagerRegistry $registry)
    {
        parent::__construct($registry, User::class);
    }

    public function upgradePassword(PasswordAuthenticatedUserInterface $user, string $newHashedPassword): void
    {
        if (!$user instanceof User) {
            throw new UnsupportedUserException(sprintf('Instances of "%s" are not supported.', $user::class));
        }

        $user->setPassword($newHashedPassword);
        $this->update($user);
    }

    public function update($user): void
    {
        $this->getEntityManager()->persist($user);
        $this->getEntityManager()->flush();
    }

    public function removeAllRelations(User $user): void
    {
        $entityManager = $this->getEntityManager();
        if ($user->getCampus() !== null) {
            $user->setCampus(null);
            $entityManager->persist($user); // Persist user update
        }
        foreach ($user->getActivities() as $activity) {
            $activity->removeInscrit($user);
            $entityManager->persist($activity); // Persist updated activity
        }
        foreach ($this->getEntityManager()->getRepository(Activity::class)->findBy(["organizer" => $user->getId()]) as $activity) {
            $this->activityService->removeOrganizer($activity);
        }
        $entityManager->remove($user);
        $entityManager->flush();
    }

    public function loadUserByIdentifier(string $usernameOrEmail): ?User
    {
        $entityManager = $this->getEntityManager();

        return $entityManager->createQuery(
            'SELECT u
                FROM App\Entity\User u
                WHERE u.pseudo = :query
                OR u.email = :query
                AND u.isActive = true'
        )
            ->setParameter('query', $usernameOrEmail)
            ->getOneOrNullResult();
    }
}
