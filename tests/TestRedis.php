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

    public function testAdd()
    {
        $key = 'key' . rand();

        $this->assertTrue($this->redis->add($key, 'val'));
        $this->assertFalse($this->redis->add($key, 'val'));
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

        $this->redis->add($key, 'val');

        $this->assertTrue($this->redis->exists($key));
    }

    public function testGetKeys()
    {
        $keys = $this->redis->getKeys('a*');
        $key  = 'a' . rand();

        $this->redis->add($key, 'val');

        $keys2 = $this->redis->getKeys('a*');

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

    public function testlPop()
    {
        $this->redis->delete('list');

        $this->redis->lPush('list', 'val');
        $this->redis->lPush('list', 'val2');
        $this->redis->lPush('list', 'val3', 1);

        $this->assertEquals('val3', $this->redis->lPop('list', 1));
        $this->assertEquals('val2', $this->redis->lPop('list'));
        $this->assertEquals('val', $this->redis->lPop('list'));
    }

    public function testlSize()
    {
        $this->redis->delete('list');

        $this->redis->lPush('list', 'val');
        
        $this->assertEquals(1, $this->redis->lSize('list'));

        $this->redis->lPush('list', 'val');
        
        $this->assertEquals(2, $this->redis->lSize('list'));
    }

    public function testlistTrim()
    {
        $this->redis->delete('list');

        $this->redis->lPush('list', 'val');
        $this->redis->lPush('list', 'val2');
        $this->redis->lPush('list', 'val3');

        $this->redis->listTrim('list', 0, 0);
  
        $this->assertEquals(1, $this->redis->lSize('list'));
        $this->assertEquals('val', $this->redis->lPop('list'));
    }

    public function testlGet()
    {
        $this->redis->delete('list');

        $this->redis->lPush('list', 'val');
        $this->redis->lPush('list', 'val2');
        $this->redis->lPush('list', 'val3');

        $this->assertEquals('val', $this->redis->lGet('list', 0));
        $this->assertEquals('val2', $this->redis->lGet('list', 1));
        $this->assertEquals('val3', $this->redis->lGet('list', 2));

        $this->redis->lPush('list', 'val4');
        $this->assertEquals('val4', $this->redis->lGet('list', 3));
    }

    public function testsAdd()
    {
        $this->redis->delete('set');

        $this->redis->sAdd('set', 'val');

        $this->assertTrue($this->redis->sContains('set', 'val'));
        $this->assertFalse($this->redis->sContains('set', 'val2'));

        $this->redis->sAdd('set', 'val2');

        $this->assertTrue($this->redis->sContains('set', 'val2'));
    }

    public function testsSize()
    {
        $this->redis->delete('set');

        $this->redis->sAdd('set', 'val');
        
        $this->assertEquals(1, $this->redis->sSize('set'));

        $this->redis->sAdd('set', 'val2');
        
        $this->assertEquals(2, $this->redis->sSize('set'));
    }

    public function testsRemove()
    {
        $this->redis->delete('set');

        $this->redis->sAdd('set', 'val');
        $this->redis->sAdd('set', 'val2');
        
        $this->redis->sRemove('set', 'val');

        $this->assertEquals(1, $this->redis->sSize('set'));

        $this->redis->sRemove('set', 'val2');

        $this->assertEquals(0, $this->redis->sSize('set'));
    }

    public function testsContains()
    {
        $this->redis->delete('set');

        $this->redis->sAdd('set', 'val');
        
        $this->assertTrue($this->redis->sContains('set', 'val'));
        $this->assertFalse($this->redis->sContains('set', 'val2'));
    }

    public function testsGetMembers()
    {
        $this->redis->delete('set');

        $this->redis->sAdd('set', 'val');
        $this->redis->sAdd('set', 'val2');
        $this->redis->sAdd('set', 'val3');

        $array = array('val', 'val2', 'val3');
        
        $this->assertEquals($array, $this->redis->sGetMembers('set'));
    }
}

?>