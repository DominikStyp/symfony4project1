<?php

namespace App\Repository;

use App\Entity\User;
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


    /**
     * @param string $interests
     * @return User
     */
    public function getRandomUserWithInterest($interests = 'tv'): User{
        $queryBuilder = $this->createQueryBuilder('a');
        $query = $queryBuilder
            ->where("JSON_CONTAINS(a.attributes_json, :interestAttr, '$.interests') = 1")
            ->add("orderBy", "RAND()")
            ->orderBy('RAND()')
            ->setMaxResults(1)
            ->getQuery();

        $result = $query->execute(array(
            'interestAttr' => '"'.$interests.'"',
        ));
        $user = $result[0]->getUser();
        return $user;
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
