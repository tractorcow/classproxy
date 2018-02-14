<?php

namespace TractorCow\ClassProxy\Tests;

use PHPUnit_Framework_TestCase;
use TractorCow\ClassProxy\ProxyFactory;

class ProxyFactoryTest extends PHPUnit_Framework_TestCase
{
    public function testInstance()
    {
        $proxy = ProxyFactory::create(ProxyFactoryTest\TestClassA::class);
        /** @var ProxyFactoryTest\TestClassA $instance */
        $instance = $proxy->instance();
        $this->assertInstanceOf(ProxyFactoryTest\TestClassA::class, $instance);
        $this->assertEquals('I am a TestClassA_fd60603', $instance->describe());
    }
}
