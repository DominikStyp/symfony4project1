<?php

namespace App\Repository;

use App\Entity\Category;
use App\Entity\Post;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Collections\Collection;
use Knp\Component\Pager\Pagination\PaginationInterface;
use Knp\Component\Pager\PaginatorInterface;
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
    /** @var PaginatorInterface */
    private $paginator;

    public function __construct(RegistryInterface $registry, PaginatorInterface $paginator)
    {
        parent::__construct($registry, Category::class);
        $this->paginator = $paginator;
    }

    /**
     * @param $categoryId
     * @param $orderBy
     * @param string $orderType
     * @param int $limitPerPage
     * @return PaginationInterface
     */
    public function findPosts($categoryId, $orderBy, $orderType = 'DESC', $limitPerPage = 10, $pageNr = 1)
    {
        /**
         * WARNING!
         * This is not standard usage of query builder, since it's defined for the Post class (not Category)
         * so we can't use: $this->createQueryBuilder('c') - because it creates query builder ASSOCIATED with Category
         * But creating queryBuilder via entityManager works
         */
         $queryBuilder = $this->getEntityManager()
             ->createQueryBuilder()
             ->select(array('p'))
             ->from(Post::class, 'p')
             ->join('p.categories', 'c' )
             ->andWhere('c.id = :cid')
             ->setParameter('cid', $categoryId)
             ->orderBy($orderBy, $orderType);
         //VarDumper::dump($queryBuilder->getQuery());die;

            $pagination = $this->paginator->paginate(
                $queryBuilder, /* query NOT result */
                $pageNr,
                $limitPerPage
            );
         //VarDumper::dump($query);exit;
        return $pagination;
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
