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

    public function getDefaultState(){
        return $this->findOneBy(['code' => 'ACT_INS']);
    }

    public function getStateByCode($code){
        return $this->findOneBy(['code' => $code]);
    }

    public function getCancelledState(){
        return $this->findOneBy(['code' => 'ACT_ANN']);
    }
}
