<?php

class TestCase extends PHPUnit_Framework_TestCase
{
    /** @var string */
    protected static $document_root;
    /** @var string */
    protected static $test_files;

    public static function setupBeforeClass()
    {
        self::$document_root = __DIR__;
        self::$test_files = __DIR__ . '/_test_files';
    }

    /**
     * Get number of bytes in a string regardless of mbstring.func_overload
     *
     * @param string $str
     * @return int
     */
    protected function countBytes($str)
    {
        return (function_exists('mb_strlen') && ((int)ini_get('mbstring.func_overload') & 2))
            ? mb_strlen($str, '8bit')
            : strlen($str);
    }

    /**
     * Common assertion for cache tests.
     *
     * @param Minify_CacheInterface $cache
     * @param string $id
     * @param string $data
     */
    protected function assertTestCache(Minify_CacheInterface $cache, $id, $data)
    {
        $this->assertTrue($cache->store($id, $data), "$id store");
        $this->assertEquals($cache->getSize($id), $this->countBytes($data), "$id getSize");
        $this->assertTrue($cache->isValid($id, $_SERVER['REQUEST_TIME'] - 10), "$id isValid");

        ob_start();
        $cache->display($id);
        $displayed = ob_get_contents();
        ob_end_clean();

        $this->assertSame($data, $displayed, "$id display");
        $this->assertEquals($data, $cache->fetch($id), "$id fetch");
    }
}