<?php

namespace App\Repository;

use App\Entity\Kita;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method Kita|null find($id, $lockMode = null, $lockVersion = null)
 * @method Kita|null findOneBy(array $criteria, array $orderBy = null)
 * @method Kita[]    findAll()
 * @method Kita[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class KitaRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, Kita::class);
    }

//    /**
//     * @return Kita[] Returns an array of Kita objects
//     */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('k')
            ->andWhere('k.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('k.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?Kita
    {
        return $this->createQueryBuilder('k')
            ->andWhere('k.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
