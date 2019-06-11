<?php

namespace App\Repository;

use App\Entity\UserAdditionalAttributes;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method UserAdditionalAttributes|null find($id, $lockMode = null, $lockVersion = null)
 * @method UserAdditionalAttributes|null findOneBy(array $criteria, array $orderBy = null)
 * @method UserAdditionalAttributes[]    findAll()
 * @method UserAdditionalAttributes[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class UserAdditionalAttributesRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, UserAdditionalAttributes::class);
    }

    // /**
    //  * @return UserAdditionalAttributes[] Returns an array of UserAdditionalAttributes objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('u')
            ->andWhere('u.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('u.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?UserAdditionalAttributes
    {
        return $this->createQueryBuilder('u')
            ->andWhere('u.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
