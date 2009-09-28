<?php

require_once 'PHPUnit/Framework/TestCase.php';

class Redis_Test extends PHPUnit_Framework_TestCase
{
    /**
     * @var Redis
     */
    public $redis;

    public function setUp()
    {
        $this->redis = new Redis();
        $this->redis->connect('127.0.0.1', 6379);
    }
    
    public function tearDown()
    {
        $this->redis->close();
        unset($this->redis);
    }
    
    public function reset()
    {
        $this->setUp();
        $this->tearDown();
    }

    public function testPing()
    {
        $this->assertEquals('+PONG', $this->redis->ping());
    }

    public function testGet()
    {
        $this->redis->set('key', 'val');

        $this->assertEquals('val', $this->redis->get('key'));
    }

    public function testSet()
    {
        $this->redis->set('key', 'val1');

        $this->assertEquals('val1', $this->redis->get('key'));

        $this->redis->set('key', 'val2');

        $this->assertEquals('val2', $this->redis->get('key'));

        $this->redis->set('key', 42);

        $this->assertEquals('42', $this->redis->get('key'));
    }

    public function testSetnx()
    {
        $key = 'key' . rand();

        $this->assertTrue($this->redis->setnx($key, 'val'));
        $this->assertFalse($this->redis->setnx($key, 'val'));
    }

    public function testIncr()
    {
        $this->redis->set('key', 0);

        $this->redis->incr('key');

        $this->assertEquals(1, $this->redis->get('key'));

        $this->redis->incr('key');

        $this->assertEquals(2, $this->redis->get('key'));

        $this->redis->incr('key', 3);

        $this->assertEquals(5, $this->redis->get('key'));
    }

    public function testDecr()
    {
        $this->redis->set('key', 5);

        $this->redis->decr('key');

        $this->assertEquals(4, $this->redis->get('key'));

        $this->redis->decr('key');

        $this->assertEquals(3, $this->redis->get('key'));

        $this->redis->decr('key', 2);

        $this->assertEquals(1, $this->redis->get('key'));
    }

    public function testExists()
    {
        $key = 'key' . rand();

        $this->assertFalse($this->redis->exists($key));

        $this->redis->setnx($key, 'val');

        $this->assertTrue($this->redis->exists($key));
    }

    public function testKeys()
    {
        $keys = $this->redis->keys('a*');
        $key  = 'a' . rand();

        $this->redis->setnx($key, 'val');

        $keys2 = $this->redis->keys('a*');

        $this->assertEquals((count($keys) + 1), count($keys2));
    }

    public function testDelete()
    {
        $this->redis->set('key', 'val');

        $this->assertEquals('val', $this->redis->get('key'));

        $this->redis->delete('key');

        $this->assertEquals(null, $this->redis->get('key'));
    }

    public function testType()
    {
        $this->redis->set('key', 'val');

        $this->assertEquals(1, $this->redis->type('key'));
    }

    public function testLpop()
    {
        $this->redis->delete('list');

        $this->redis->lpush('list', 'val');
        $this->redis->lpush('list', 'val2');
        $this->redis->lpush('list', 'val3', 1);

        $this->assertEquals('val3', $this->redis->lpop('list', 1));
        $this->assertEquals('val2', $this->redis->lpop('list'));
        $this->assertEquals('val', $this->redis->lpop('list'));
    }

    public function testLlen()
    {
        $this->redis->delete('list');

        $this->redis->lpush('list', 'val');
        
        $this->assertEquals(1, $this->redis->llen('list'));

        $this->redis->lpush('list', 'val');
        
        $this->assertEquals(2, $this->redis->llen('list'));
    }

    public function testLtrim()
    {
        $this->redis->delete('list');

        $this->redis->lpush('list', 'val');
        $this->redis->lpush('list', 'val2');
        $this->redis->lpush('list', 'val3');

        $this->redis->ltrim('list', 0, 0);
  
        $this->assertEquals(1, $this->redis->llen('list'));
        $this->assertEquals('val', $this->redis->lpop('list'));
    }

    public function testLindex()
    {
        $this->redis->delete('list');

        $this->redis->lpush('list', 'val');
        $this->redis->lpush('list', 'val2');
        $this->redis->lpush('list', 'val3');

        $this->assertEquals('val', $this->redis->lindex('list', 0));
        $this->assertEquals('val2', $this->redis->lindex('list', 1));
        $this->assertEquals('val3', $this->redis->lindex('list', 2));

        $this->redis->lpush('list', 'val4');
        $this->assertEquals('val4', $this->redis->lindex('list', 3));
    }

    public function testsAdd()
    {
        $this->redis->delete('set');

        $this->redis->sAdd('set', 'val');

        $this->assertTrue($this->redis->sismember('set', 'val'));
        $this->assertFalse($this->redis->sismember('set', 'val2'));

        $this->redis->sAdd('set', 'val2');

        $this->assertTrue($this->redis->sismember('set', 'val2'));
    }

    public function testScard()
    {
        $this->redis->delete('set');

        $this->redis->sAdd('set', 'val');
        
        $this->assertEquals(1, $this->redis->scard('set'));

        $this->redis->sAdd('set', 'val2');
        
        $this->assertEquals(2, $this->redis->scard('set'));
    }

    public function testSrem()
    {
        $this->redis->delete('set');

        $this->redis->sAdd('set', 'val');
        $this->redis->sAdd('set', 'val2');
        
        $this->redis->srem('set', 'val');

        $this->assertEquals(1, $this->redis->scard('set'));

        $this->redis->srem('set', 'val2');

        $this->assertEquals(0, $this->redis->scard('set'));
    }

    public function testSismember()
    {
        $this->redis->delete('set');

        $this->redis->sAdd('set', 'val');
        
        $this->assertTrue($this->redis->sismember('set', 'val'));
        $this->assertFalse($this->redis->sismember('set', 'val2'));
    }

    public function testSmembers()
    {
        $this->redis->delete('set');

        $this->redis->sAdd('set', 'val');
        $this->redis->sAdd('set', 'val2');
        $this->redis->sAdd('set', 'val3');

        $array = array('val', 'val2', 'val3');
        
        $this->assertEquals($array, $this->redis->smembers('set'));
    }
}

?>