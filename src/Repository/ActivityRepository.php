<?php

namespace App\Repository;

use App\Entity\Activity;
use App\Entity\Campus;
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

    public function findAllActive(): array
    {
        $entityManager = $this->getEntityManager();

        $query = $entityManager->createQuery(
            'SELECT a
            FROM App\Entity\Activity a
            WHERE a.state >= 1 
            ORDER BY a.dateDebut ASC'
        );
        return $query->getResult();
    }

    public function findAll(): array
    {
        $entityManager = $this->getEntityManager();
        $query = $entityManager->createQuery(
            'SELECT a
            FROM App\Entity\Activity a
            ORDER BY a.dateDebut ASC'
        );
        return $query->getResult();
    }

    public function filter(
        ?int      $idUser,
        ?Campus   $campus,
        ?string   $name,
        ?DateTime $dateDebut,
        ?DateTime $dateMax,
        ?bool     $organisateur,
        ?bool     $inscript,
        ?bool     $finis,
    )
    {

        $qb = $this->entityManager->createQueryBuilder();
        $qb->select(array('a'))
            ->from('App\Entity\Activity', 'a');

        if ($campus) {
            $qb->andWhere($qb->expr()->eq('a.campus',
                '?1'
            ));
            $qb->setParameters(new ArrayCollection([
                new Parameter('1', $campus->getId()),
            ]));
        }
        if ($name) {
            $qb->andWhere($qb->expr()->like('a.name',
                '?2'
            ));
            $qb->setParameters(new ArrayCollection([
                new Parameter('2', $name),
            ]));
        }
        if ($dateMax) {
            $qb->andWhere($qb->expr()->lte("a.dateDebut", "?3"));
            $qb->setParameters(new ArrayCollection([
                new Parameter('3', $dateDebut),
            ]));
        }
        if ($dateDebut) {
            $qb->andWhere($qb->expr()->gte("a.dateDebut", "?3"));
            $qb->setParameters(new ArrayCollection([
                new Parameter('3', $dateDebut),
            ]));
        }
/*TODO REVOIR LES DATES */
        if ($dateDebut && $dateMax) {
            $qb->andWhere($qb->expr()->between('a.dateDebut',
                '?3', '?4'
            ));
            $qb->setParameters(new ArrayCollection([
            ]));
        }


        if ($organisateur) {
            $qb->andWhere($qb->expr()->eq("a.organisateur", "?6"));
            $qb->setParameters(new ArrayCollection([
                new Parameter('6', $idUser),
            ]));
        }
        /*TODO JOINTURE PROBABLE*/
        if ($inscript) {
          /*  $qb->andWhere($qb->expr()->co("a.dateDebut", "?3", "?4"));
            $qb->setParameters(new ArrayCollection([
                new Parameter('6', $idUser),
            ]));*/
        }

        if ($finis) {

        }
        return $qb->getQuery()->getResult();
    }
}
