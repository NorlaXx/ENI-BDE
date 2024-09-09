<?php

namespace App\Repository;

use App\Entity\Campus;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Campus>
 */
class CampusRepository extends ServiceEntityRepository
{
    public function update(Campus $campus): void
    {
        $this->getEntityManager()->persist($campus);
        $this->getEntityManager()->flush();
    }
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Campus::class);
    }
}
