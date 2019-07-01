<?php


namespace App\Tests;

use Symfony\Component\Cache\Adapter\FilesystemAdapter;
use Symfony\Contracts\Cache\ItemInterface;


class SymfonyCacheTest extends CommonTestCase {

    protected static $reloadFixturesBeforeTests = false;

    const CACHE_KEY = 'my_cache_key';
    private $cacheMisses = 0;
    /** @var FilesystemAdapter */
    private $cache;
    /**
     * @throws \Psr\Cache\InvalidArgumentException
     */
    public function testSymfonyCacheFileAdapter() {

        $this->cache = new FilesystemAdapter();
        // attempt nr 1
        $this->assertEquals("time_consuming_value_1", $this->getCachedValue());
        $this->assertEquals(1, $this->cacheMisses);
        // attempt nr 2 - cache should not expire yet
        $this->assertEquals("time_consuming_value_1", $this->getCachedValue());
        // attempt nr 3 - cache should not expire yet
        $this->assertEquals("time_consuming_value_1", $this->getCachedValue());
        $this->assertEquals(1, $this->cacheMisses);
        sleep(3);
        // cache SHOULD expire NOW and should be RE-COMPUTED
        $this->assertEquals("time_consuming_value_2", $this->getCachedValue());
        $this->assertEquals(2, $this->cacheMisses);

    }

    /**
     * @return mixed
     * @throws \Psr\Cache\InvalidArgumentException
     */
    private function getCachedValue(){
        $cacheMisses = & $this->cacheMisses;
        $value = $this->cache->get(self::CACHE_KEY, function (ItemInterface $item) use (& $cacheMisses) {
            $cacheMisses++;
            $item->expiresAfter(2);
            $computedValue = "time_consuming_value_{$cacheMisses}";
            echo PHP_EOL, ">>>>>>>> Retrieving {$computedValue} from cache", PHP_EOL;
            return $computedValue;
        });
        var_dump($value);
        return $value;
    }
}