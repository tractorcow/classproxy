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
        $this->assertEquals('I am a TestClassA_fa1d985', $instance->describe());
    }

    /**
     * Test class methods
     */
    public function testAbstractInstance()
    {
        $proxy = ProxyFactory::create(ProxyFactoryTest\TestClassB::class)
            ->addMethod('abstractmethod', 'return "This is an abstract method";');

        /** @var ProxyFactoryTest\TestClassB $instance */
        $instance = $proxy->instance();
        $this->assertInstanceOf(ProxyFactoryTest\TestClassB::class, $instance);
        $this->assertEquals('I am a TestClassB_4881cfb', $instance->describe());
        $this->assertEquals('This is an abstract method', $instance->abstractmethod());
    }

    /**
     * Test chained methods
     */
    public function testChainedMethods()
    {
        $proxy = ProxyFactory::create(ProxyFactoryTest\TestClassC::class)
            ->addMethod('greet', function ($args, $next) {
                return 'Firstly, ' . $next(...$args);
            })
            ->addMethod('greet', function ($args, $next) {
                return "I want to talk to you {$args[0]}, " . $next("Mr. {$args[0]}");
            });

        /** @var ProxyFactoryTest\TestClassC $instance */
        $instance = $proxy->instance();
        $this->assertInstanceOf(ProxyFactoryTest\TestClassC::class, $instance);
        $this->assertEquals('Firstly, I want to talk to you Robert, Hello Mr. Robert', $instance->greet("Robert"));
    }
}
