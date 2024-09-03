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

    /**
     * Return all activities using mutliple potential param
     * @param int|null $idUser
     * @param Campus|null $campus
     * @param string|null $name
     * @param DateTime|null $dateDebut
     * @param DateTime|null $dateMax
     * @param bool|null $organisateur
     * @param bool|null $inscript
     * @param bool|null $finis
     * @return mixed
     */
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
        $param = new ArrayCollection();
        $qb->select(array('a'))
            ->from('App\Entity\Activity', 'a');
        /*OK return ativities if campusId is equal to param*/
        if ($campus) {
            $qb->andWhere($qb->expr()->eq('a.campus',
                '?1'
            ));
            $param[1] = new Parameter('1', $campus->getId());
        }
        /*OK return activities with like string in name*/
        if ($name) {
            $qb->andWhere($qb->expr()->like('a.name',
                '?2'
            ));
            $param[2] = new Parameter('2', '%' . $name . "%");

        }

        /* TODO revoir pour utiliser 1 seule date*/
        if ($dateDebut && $dateMax) {
            $qb->andWhere($qb->expr()->between('a.dateDebut',
                '?3', '?4'
            ));
            $param[3] = new Parameter('3', $dateDebut);
            $param[4] = new Parameter('4', $dateMax);
        }

        /* OK return activity if organisateurId id equal to idUser*/
        if ($organisateur) {
            $qb->andWhere($qb->expr()->eq("a.organisateur", "?6"));
            $param[6] = new Parameter('6', $idUser);

        }
        /* OK return all activities where idUser is present in activity.inscrits*/
        if ($inscript) {
            $qb->innerJoin('a.inscrits', 'p', 'WITH', 'p.id = ?6');
            $param[6] = new Parameter('6', $idUser);
        }
        /* OK return all activities with 1 or 2 in status_id*/
        if ($finis) {
            $qb->innerJoin('a.state', 's', 'WITH', 's.id = 1 OR s.id = 3 OR s.id = 5');
        }
        $qb->setParameters($param);
        return $qb->getQuery()->getResult();
    }
}
