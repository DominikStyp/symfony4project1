<?php
/**
 * Created by PhpStorm.
 * User: Dominik
 * Date: 2019-06-11
 * Time: 03:34
 */

namespace App\Tests\Repository;


use App\Entity\User;
use App\Entity\UserAdditionalAttributes;
use App\Repository\UserAdditionalAttributesRepository;
use App\Tests\CommonTestCase;

class WorkingWithDoctrineJSONArrayTypeTest extends CommonTestCase {

    public function testGetUserAttributes() {
        /** @var User $user */
        $user = $this->entityManager->getRepository(User::class)->find(1);
        /** @var UserAdditionalAttributes $attributes */
        $attributes = $user->getUserAdditionalAttributes();
        $json = $attributes->getAttributesJson();
        $this->assertEquals($json['common_attr'], '123');
    }

    /**
     * Following method (plain SQL) does not require JSON_CONTAINS to be defined inside Doctrine
     *
     * @throws \Doctrine\DBAL\DBALException
     */
    public function testSearchForJSONFieldInterestsForContainingTV(){
        // WARNING !!! Strings in JSON_CONTAINS must be wrapped in " "
        $sql = "SELECT * FROM user_additional_attributes WHERE JSON_CONTAINS(attributes_json, '\"tv\"', '$.interests') = 1";
        $result = $this->entityManager->getConnection()->executeQuery($sql)->fetchAll();
        //var_dump($result);
        $this->assertNotEmpty($result);
    }




    /**
     * WARNING!! First you must install following package:
     * @link https://github.com/ScientaNL/DoctrineJsonFunctions
     * in order to use JSON_CONTAINS inside Doctrine Query Builder
     *
     *
     */
    public function testUseQueryBuilderForJSON_CONTAINS(){
        /** @var UserAdditionalAttributesRepository $repository */
        $repository = $this->entityManager->getRepository(UserAdditionalAttributes::class);
        $userWithInterestTv = $repository->getRandomUserWithInterest('tv');
        $queryBuilder = $this->entityManager->createQueryBuilder();
        $query = $queryBuilder
            ->select(['a', 'u'])
            ->from(UserAdditionalAttributes::class, 'a')
            ->innerJoin('a.user', 'u', 'WITH', 'u.id = :userId')
            ->where("JSON_CONTAINS(a.attributes_json, :interestAttr, '$.interests') = 1")
            ->getQuery();
        echo PHP_EOL."User id: ". $userWithInterestTv->getId() . PHP_EOL;
        $result = $query->execute(array(
            'interestAttr' => '"tv"',
            'userId' => $userWithInterestTv->getId()
        ));
        $this->assertNotEmpty($result);
        $attributesObj = $result[0];
        $this->assertInstanceOf(UserAdditionalAttributes::class, $attributesObj);
        $user = $attributesObj->getUser();
        $this->assertInstanceOf(User::class, $user);
        $this->assertEquals($userWithInterestTv->getId(), $user->getId());
    }
}