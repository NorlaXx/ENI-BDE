<?php

namespace App\Repository;

use App\Entity\Activity;
use App\Entity\Campus;
use App\Model\ActivityFilter;
use DateTime;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Query\Parameter;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Activity>
 */
class ActivityRepository extends ServiceEntityRepository
{
    private EntityManagerInterface $entityManager;

    public function __construct(EntityManagerInterface $entityManager, ManagerRegistry $registry)
    {
        parent::__construct($registry, Activity::class);
        $this->entityManager = $entityManager;
    }

    public function findAll(): array
    {
        $entityManager = $this->getEntityManager();

        $query = $entityManager->createQuery(
            'SELECT a, l, c
            FROM App\Entity\Activity a
            INNER JOIN a.lieu l
            INNER JOIN a.campus c
            ORDER BY a.startDate ASC'
        );

        return $query->getResult();
    }

    public function update($activity): void
    {
        $this->getEntityManager()->persist($activity);
        $this->getEntityManager()->flush();
    }

    public function findByCreator($idUser){
        $qb = $this->entityManager->createQueryBuilder();
        $qb->select(array('activity'))
            ->from('App\Entity\Activity', 'activity')
            ->where('activity.organizer = :idUser')
            ->setParameter('idUser', $idUser);

        return $qb->getQuery()->getResult();
    }
    
    public function filter(
        int      $idUser,
        ActivityFilter $filter
    )
    {

        $qb = $this->entityManager->createQueryBuilder();
        $param = new ArrayCollection();
        $qb->select(array('activity'))
            ->from('App\Entity\Activity', 'activity');
        
        if ($filter->getName() != null) {
            $qb->andWhere('activity.name LIKE :name');
            $param->add(new Parameter('name', '%' . $filter->getName() . '%'));
        }

        if ($filter->getCampus() != null) {
            $qb->andWhere('activity.campus = :campus');
            $param->add(new Parameter('campus', $filter->getCampus()));
        }

        if ($filter->getMinDate() != null) {
            $qb->andWhere('activity.startDate >= :minDate');
            $param->add(new Parameter('minDate', $filter->getMinDate()));
        }

        if ($filter->getMaxDate() != null) {
            $qb->andWhere('activity.startDate <= :maxDate');
            $param->add(new Parameter('maxDate', $filter->getMaxDate()));
        }

        if ($filter->getOrganizer() != null) {
            $qb->andWhere('activity.organizer = :id');
            $param->add(new Parameter('id', $idUser));
        }

        if ($filter->getRegistered() && $filter->getNotRegistered()){
            $qb->innerJoin('activity.registered', 'p', 'WITH', 'p.id = :idUser');
            $qb->leftJoin('activity.registered', 'p2', 'WITH', 'p2.id = :idUser');
            $qb->andWhere('p2.id IS NULL');
            $param->add(new Parameter('idUser', $idUser));
        }elseif ($filter->getRegistered()) {
            $qb->innerJoin('activity.registered', 'p', 'WITH', 'p.id = :idUser');
            $param->add(new Parameter('idUser', $idUser));
        }elseif ($filter->getNotRegistered()) {
            $qb->leftJoin('activity.registered', 'p2', 'WITH', 'p2.id = :idUser');
            $qb->andWhere('p2.id IS NULL');
            $param->add(new Parameter('idUser', $idUser));
        }

        if ($filter->getFinished() != null) {
            $qb->andWhere('activity.startDate < :now');
            $param->add(new Parameter('now', new DateTime()));
        }

        $qb->setParameters($param);
        return $qb->getQuery()->getResult();
    }
}
