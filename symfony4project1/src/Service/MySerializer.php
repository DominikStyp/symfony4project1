<?php
/**
 * Created by PhpStorm.
 * User: Dominik
 * Date: 2019-05-13
 * Time: 02:42
 */

namespace App\Service;

use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Encoder\XmlEncoder;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Serializer;

class MySerializer {

    private static $serializer;

    public function getSerializer() : Serializer{
        if(empty(self::$serializer)){
            $encoders = [new XmlEncoder(), new JsonEncoder()];
            $normalizers = [new ObjectNormalizer()];
            $serializer = new Serializer($normalizers, $encoders);
            self::$serializer = $serializer;
        }
        return self::$serializer;
    }
}