<?php

namespace App\Repository;

use App\Entity\Medecin;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Medecin>
 *
 * @method Medecin|null find($id, $lockMode = null, $lockVersion = null)
 * @method Medecin|null findOneBy(array $criteria, array $orderBy = null)
 * @method Medecin[]    findAll()
 * @method Medecin[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class MedecinRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Medecin::class);
    }

    public function save(Medecin $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(Medecin $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

//    /**
//     * @return Medecin[] Returns an array of Medecin objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('m')
//            ->andWhere('m.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('m.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?Medecin
//    {
//        return $this->createQueryBuilder('m')
//            ->andWhere('m.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }



public function searchByTerm($searchTerm)
{
    $qb = $this->createQueryBuilder('m');

    $qb->where($qb->expr()->orX(
            $qb->expr()->like('m.nom', ':searchTerm'),
            $qb->expr()->like('m.prenom', ':searchTerm'),
            $qb->expr()->like('m.email', ':searchTerm'),
            $qb->expr()->like('m.telephone', ':searchTerm'),
            $qb->expr()->like('m.tarif', ':searchTerm'),
            $qb->expr()->like('m.cin', ':searchTerm'),
            $qb->expr()->like('m.specialites', ':searchTerm'),
            $qb->expr()->like('m.adresse', ':searchTerm'),
            $qb->expr()->like('m.sexe', ':searchTerm'),
            $qb->expr()->like('m.titre', ':searchTerm'),
            $qb->expr()->like('m.telephone', ':searchTerm'),
        ))
        ->setParameter('searchTerm', '%'.$searchTerm.'%');

    return $qb->getQuery()->getResult();
}


//    /**
//     * @return Medecin[] Returns an array of Medecin objects
//     */
public function listByPatient($idPatient): array
{
    return $this->createQueryBuilder('m')
        ->andWhere('m.idPatient = :patient')
        ->setParameter('patient', $idPatient)
        ->orderBy('m.id', 'DESC')
        ->getQuery()
        ->getResult()
    ;
}
}
