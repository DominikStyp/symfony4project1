<?php

namespace App\Repository;

use App\Entity\Post;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Knp\Component\Pager\Pagination\PaginationInterface;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bridge\Doctrine\RegistryInterface;
use Symfony\Component\VarDumper\VarDumper;

/**
 * @method Post|null find($id, $lockMode = null, $lockVersion = null)
 * @method Post|null findOneBy(array $criteria, array $orderBy = null)
 * @method Post[]    findAll()
 * @method Post[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class PostRepository extends ServiceEntityRepository
{
    /** @var PaginatorInterface */
    private $paginator;

    public function __construct(RegistryInterface $registry, PaginatorInterface $paginator)
    {
        parent::__construct($registry, Post::class);
        $this->paginator = $paginator;
    }

    /**
     * @param $userId
     * @param $orderBy
     * @param string $orderType
     * @param int $limitPerPage
     * @return PaginationInterface
     */
    public function findUserPosts(int $userId, $orderBy, $orderType = 'DESC', $limitPerPage = 10, $pageNr = 1)
    {

        $queryBuilder = $this->createQueryBuilder('p')
            ->andWhere('p.user_id = :uid')
            ->setParameter('uid', $userId)
            ->orderBy($orderBy, $orderType);
        // proper DQL: "SELECT p FROM App\Entity\Post p WHERE p.user_id = :uid ORDER BY p.id DESC"
        // VarDumper::dump($queryBuilder->getQuery());die;
        $pagination = $this->paginator->paginate(
            $queryBuilder,
            $pageNr,
            $limitPerPage
        );
        return $pagination;
    }

    // /**
    //  * @return Post[] Returns an array of Post objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('p')
            ->andWhere('p.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('p.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?Post
    {
        return $this->createQueryBuilder('p')
            ->andWhere('p.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
