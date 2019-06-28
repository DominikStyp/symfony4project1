<?php


namespace App\Tests;

use Symfony\Component\Cache\Adapter\FilesystemAdapter;
use Symfony\Contracts\Cache\ItemInterface;


class SymfonyCacheTest extends CommonTestCase {

    protected static $reloadFixturesBeforeTests = false;

    const CACHE_KEY = 'my_cache_key';
    private $cacheMisses = 0;
    /**
     * @throws \Psr\Cache\InvalidArgumentException
     */
    public function testSymfonyCacheFileAdapter() {
        $cacheMisses = & $this->cacheMisses;
        $cache = new FilesystemAdapter();
        // The callable will only be executed on a cache miss.
        $value = $cache->get(self::CACHE_KEY, function (ItemInterface $item) use (& $cacheMisses) {
            $cacheMisses++;
            $item->expiresAfter(2);
            $computedValue = "time_consuming_value_{$cacheMisses}";
            echo PHP_EOL, "Retrieving {$computedValue} from cache", PHP_EOL;
            return $computedValue;
        });

        // attempt nr 1
        $this->assertEquals(1, $this->cacheMisses);
        $this->assertEquals("time_consuming_value_1", $cache->getItem(self::CACHE_KEY)->get());
        // attempt nr 2 - cache should not expire yet
        $this->assertEquals(1, $this->cacheMisses);
        $this->assertEquals("time_consuming_value_1", $cache->getItem(self::CACHE_KEY)->get());
        // attempt nr 3 - cache should not expire yet
        $this->assertEquals(1, $this->cacheMisses);
        $this->assertEquals("time_consuming_value_1", $cache->getItem(self::CACHE_KEY)->get());

        sleep(3);
        // cache SHOULD expire NOW and should be RE-COMPUTED
        $this->assertEquals("time_consuming_value_2", $cache->getItem(self::CACHE_KEY)->get());
        $this->assertEquals(2, $this->cacheMisses);

        $cache->delete(self::CACHE_KEY);
    }
}