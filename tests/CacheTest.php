<?php

use crodas\FileUtil\Cache;
use crodas\FileUtil\File;

$foo = rand();
$counter = 0;

class fooClass
{
    public function foobar()
    {
        global $foo, $counter;
        $counter++;
        return array($foo, rand(), rand());
    }
}

class CacheTest extends \phpunit_framework_testcase
{
    public function testFirst()
    {
        global $foo, $counter;
        $file = File::generateFilepath('class_cache', 'fooClass');
        if (is_file($file)) {
            unlink($file);
        }
        $proxy = new Cache('fooClass');
        $this->assertEquals($proxy->foobar(1, 2, 3), $proxy->foobar(1, 2, 3));
        $this->assertEquals($proxy->foobar(1, 2, 3), $proxy->foobar(1, 2, 3));
        $x = $proxy->foobar(1, 2, 3);
        $this->assertEquals($x[0], $foo);
        $this->assertEquals($counter, 1);
        unset($proxy);
    }

    /** @dependsOn testFirst */
    public function testSecond()
    {
        global $foo, $counter;
        $proxy = new Cache('fooClass');
        $this->assertEquals($proxy->foobar(1, 2, 3), $proxy->foobar(1, 2, 3));
        $this->assertEquals($counter, 1);
    }

    public function testPrefix()
    {
        $proxy = new Cache(new fooClass, 'foobar.php');
        $this->assertEquals($proxy->foobar(1, 2, 3), $proxy->foobar(1, 2, 3));
        unset($proxy); /* destroy object to write the cache file */
        $this->assertTrue(is_file(sys_get_temp_dir() . '/php-cache-foobar.php'));
    }
}
