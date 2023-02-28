<?php

namespace App\Repository;

use App\Entity\OAuthAccountConnector;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<OAuthAccountConnector>
 *
 * @method OAuthAccountConnector|null find($id, $lockMode = null, $lockVersion = null)
 * @method OAuthAccountConnector|null findOneBy(array $criteria, array $orderBy = null)
 * @method OAuthAccountConnector[]    findAll()
 * @method OAuthAccountConnector[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class OAuthAccountConnectorRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, OAuthAccountConnector::class);
    }

    public function save(OAuthAccountConnector $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(OAuthAccountConnector $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

//    /**
//     * @return OAuthAccountConnector[] Returns an array of OAuthAccountConnector objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('o')
//            ->andWhere('o.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('o.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?OAuthAccountConnector
//    {
//        return $this->createQueryBuilder('o')
//            ->andWhere('o.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
