<?php
/**
 * Created by PhpStorm.
 * User: Dominik
 * Date: 2019-06-13
 * Time: 03:39
 */

namespace App\Tests\Doctrine;


use App\Entity\User;
use App\Tests\CommonTestCase;
use Doctrine\Common\Collections\Criteria;
use Doctrine\Common\Collections\ExpressionBuilder;
use Doctrine\ORM\QueryBuilder;

class DoctrineCriteriaTest extends CommonTestCase {

    protected static $reloadFixturesBeforeTests = false;
    /**
     * @throws \Doctrine\ORM\Query\QueryException
     */
    public function testAppendCriteria() {
        $builder = $this->entityManager->createQueryBuilder();
        $results = $builder
                    ->select('u')
                    ->from(User::class,'u')
                    ->addCriteria( $this->getIdBetweenCriteria(1,20) )
                    ->addCriteria( $this->getOrderByAndLimitCriteria(10) )
                    ->getQuery()
                    ->execute();
        //var_dump($results);
        $this->assertEquals(10, count($results));
        $this->assertGreaterThan(11,12);
        // see if order is maitained first user id should be bigger than second etc...
        $this->assertGreaterThan($results[1]->getId(), $results[0]->getId());

    }

    /**
     * @return Criteria
     */
    private function getOrderByAndLimitCriteria(int $maxResults = 10): Criteria{
        $criteria = Criteria::create();
        $criteria->orderBy(['id' => 'DESC']);
        $criteria->setMaxResults($maxResults);
        return $criteria;
    }

    /**
     * @return Criteria
     */
    private function getIdBetweenCriteria(int $min, int $max): Criteria{
        $criteria = Criteria::create();
        $expressionBuilder = Criteria::expr();
        $criteria->andWhere($expressionBuilder->lt("u.id", $max))
                 ->andWhere($expressionBuilder->gt("u.id", $min));
        return $criteria;
    }

}