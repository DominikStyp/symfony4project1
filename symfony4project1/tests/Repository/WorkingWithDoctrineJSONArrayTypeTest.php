<?php
/**
 * Created by PhpStorm.
 * User: Dominik
 * Date: 2019-06-11
 * Time: 03:34
 */

namespace App\Tests\Repository;


use App\DataFixtures\AppFixtures;
use App\Entity\User;
use App\Entity\UserAdditionalAttributes;
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

    public function testSearchForJSONFieldInterestsForContainingTV(){
        // WARNING !!! Strings in JSON_CONTAINS must be wrapped in " "
        $sql = "SELECT * FROM user_additional_attributes WHERE JSON_CONTAINS(attributes_json, '\"tv\"', '$.interests')";
        $result = $this->entityManager->getConnection()->executeQuery($sql)->fetchAll();
        //var_dump($result);
        $this->assertNotEmpty($result);
    }

    public function testUseQueryBuilderForJSON_CONTAINS(){
        $queryBuilder = $this->entityManager->createQueryBuilder();
        $query = $queryBuilder
            ->select('a')
            ->from(UserAdditionalAttributes::class, 'a')
            ->where("JSON_CONTAINS(a.attributes_json, :attr, '$.interests') = 1")
            ->getQuery();

        $result = $query->execute(array(
            'attr' => '"tv"',
        ));
        var_dump($result);
        $this->assertNotEmpty($result);
    }
}