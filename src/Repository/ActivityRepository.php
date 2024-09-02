<?php

namespace App\Repository;

use App\Entity\Activity;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Activity>
 */
class ActivityRepository extends ServiceEntityRepository
{

    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Activity::class);
    }

    /**
     * Création d'une sortie
     *
     * @param $activity
     * @return void
     */
    public function createActivity($activity): void
    {
        $this->getEntityManager()->persist($activity);
        $this->getEntityManager()->flush();
    }
}
