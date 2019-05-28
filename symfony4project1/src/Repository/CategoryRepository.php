<?php

namespace App\Repository;

use App\Entity\Category;
use App\Entity\Post;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Collections\Collection;
use Symfony\Bridge\Doctrine\RegistryInterface;
use Symfony\Component\VarDumper\VarDumper;

/**
 * @method Category|null find($id, $lockMode = null, $lockVersion = null)
 * @method Category|null findOneBy(array $criteria, array $orderBy = null)
 * @method Category[]    findAll()
 * @method Category[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class CategoryRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, Category::class);
    }

    /**
     * @param $categoryId
     * @param $orderBy
     * @param string $orderType
     * @param int $limit
     * @return Post[]|null
     * @throws \Doctrine\ORM\NoResultException
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function findPosts($categoryId, $orderBy, $orderType = 'DESC', $limit = 10)
    {
         $query = $this->createQueryBuilder('c')
            ->select(array('p', 'c'))
            ->join('c.posts', 'p' )
             ->andWhere('c.id = :cid')
             ->setParameter('cid', $categoryId)
            ->orderBy($orderBy, $orderType)
            ->setMaxResults($limit)
            ->getQuery();
         //VarDumper::dump($query);exit;
            /** @var Category $category */
            $category = $query->getSingleResult();
            if(!empty($category)) {
                return $category->getPosts();
            }
            return null;
    }

    // /**
    //  * @return Category[] Returns an array of Category objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('c')
            ->andWhere('c.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('c.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?Category
    {
        return $this->createQueryBuilder('c')
            ->andWhere('c.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
