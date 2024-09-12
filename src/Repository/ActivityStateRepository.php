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
    private $allState;

    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, ActivityState::class);
//        $this->allState = $this->findAll();
    }

    private function loadStates(): void
    {
        if($this->allState == null){
            $this->allState = $this->findAll();
        }
    }

    public function getDefaultState(){
        return $this->getStateByCode('ACT_CR');
    }

    public function getStateByCode($code){
        $this->loadStates();
        foreach($this->allState as $state){
            if($state->getCode() == $code){
                return $state;
            }
        }
    }

    public function getCancelledState(){
        return $this->getStateByCode('ACT_ANN');
    }
}
