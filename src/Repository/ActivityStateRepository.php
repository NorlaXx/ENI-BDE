<?php

namespace App\Repository;

use App\Entity\ActivityState;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<ActivityState>
 */
class ActivityStateRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, ActivityState::class);
    }

    public function getDefautState(){
        return $this->findOneBy(['state' => 1]);
    }
}
