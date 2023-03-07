<?php

namespace App\Repository;

use App\Entity\Sujet;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Sujet>
 *
 * @method Sujet|null find($id, $lockMode = null, $lockVersion = null)
 * @method Sujet|null findOneBy(array $criteria, array $orderBy = null)
 * @method Sujet[]    findAll()
 * @method Sujet[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class SujetRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Sujet::class);
    }

    public function save(Sujet $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(Sujet $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

//    /**
//     * @return Sujet[] Returns an array of Sujet objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('s')
//            ->andWhere('s.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('s.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?Sujet
//    {
//        return $this->createQueryBuilder('s')
//            ->andWhere('s.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }

public function search($keyword)
    {
        dd($keyword);
        return $this->createQueryBuilder('s')
            ->where('s.title LIKE :key')
            ->setParameter('key', '%' . $keyword . '%')
            ->getQuery()->getResult();
    }
    public function getLikesCount($sujetId)
    {
        $qb = $this->createQueryBuilder('s');
        $qb->select('COUNT(l.id)');
        $qb->leftJoin('s.likes', 'l');
        $qb->where('s.id = :sujetId');
        $qb->setParameter('sujetId', $sujetId);
        $query = $qb->getQuery();
        $result = $query->getSingleScalarResult();
    
        return $result;
    }
    /**
     * Returns number of Annonces
     * @return void 
     */
    public function getTotalAnnonces($filters = null){
        $query = $this->createQueryBuilder('s')
            ->select('COUNT(s)');
        // On filtre les données
        if($filters != null){
            $query->andWhere('s.specialites IN(:spec)')
                ->setParameter(':spec', array_values($filters));
        }

        return $query->getQuery()->getSingleScalarResult();
    }
}
