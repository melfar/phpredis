<?php

require_once 'PHPUnit/Framework/TestCase.php';

class Redis_Test extends PHPUnit_Framework_TestCase
{
    /**
     * @var Redis
     */
    public $redis;

    public function setUp() {
      $this->redis = new Redis();
      $this->redis->connect('127.0.0.1', 6379);
      $this->redis->set('test', 'str');

      $this->key = 'a' . rand();
      $this->redis->setnx($this->key, 'val');
    }
    
    public function tearDown() {
      $this->redis->close();
      unset($this->redis);
    }
    
    public function reset() {
      $this->setUp();
      $this->tearDown();
    }

    public function assertMemoryLeakFree($body, $iterations = 100) {
      # warm up
      for ($i = 0; $i < 100; $i++)  eval($body);
      # measure
      $mem_usage = memory_get_peak_usage(true);
      # run
      for ($i = 0; $i < $iterations; $i++)  eval($body);
      # check
      $this->assertEquals($mem_usage, memory_get_peak_usage(true));
    }

    public function testGet() {
      $this->assertMemoryLeakFree('$this->redis->get("test");');
    }

    public function testSet() {
      $this->assertMemoryLeakFree('$this->redis->set("test", "str");');
    }
    
    public function testPing() {
      $this->assertMemoryLeakFree('$this->redis->ping();');
    }

    public function testSetnx() {
      $this->assertMemoryLeakFree('$this->redis->setnx("key", "val");');
    }

    public function testIncr() {
      $this->assertMemoryLeakFree('$this->redis->incr("key_int");');
      $this->assertMemoryLeakFree('$this->redis->incr("key_int", 5);');
    }

    public function testDecr() {
      $this->assertMemoryLeakFree('$this->redis->decr("key_int");');
      $this->assertMemoryLeakFree('$this->redis->decr("key_int", 5);');
    }

    public function testExists() {
      $this->assertMemoryLeakFree('$this->redis->exists("test");');
    }

    public function testKeys() {
      $this->assertMemoryLeakFree('$this->redis->keys("a*");');
    }

    public function testDelete() {
      $this->assertMemoryLeakFree('$this->redis->delete("key");');
    }

    public function testType() {
      $this->assertMemoryLeakFree('$this->redis->type("key");');
    }

    public function testLpop() {
      $this->assertMemoryLeakFree('$this->redis->lpop("key");');
      $this->assertMemoryLeakFree('$this->redis->lpop("key", 1);');
    }

    public function testLlen() {
      $this->assertMemoryLeakFree('$this->redis->llen("key");');
    }

    public function testLtrim() {
      $this->assertMemoryLeakFree('$this->redis->ltrim("key", 0, 0);');
    }

    public function testLindex() {
      $this->assertMemoryLeakFree('$this->redis->lindex("key", 0);');
    }

    public function testsAdd() {
      $this->assertTrue(false);
      #$this->assertMemoryLeakFree('$this->redis->sadd("key", "val");');
    }

    public function testScard() {
      $this->assertMemoryLeakFree('$this->redis->scard("key");');
    }

    public function testSrem() {
      $this->assertMemoryLeakFree('$this->redis->srem("key", "val");');
    }

    public function testSismember() {
      $this->assertMemoryLeakFree('$this->redis->sismember("key", "val");');
    }

    public function testSmembers() {
      $this->assertMemoryLeakFree('$this->redis->smembers("key");');
    }
  
}

?>