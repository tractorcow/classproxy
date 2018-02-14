<?php

namespace TractorCow\ClassProxy\Generators;

use ReflectionClass;

class ExtendsClassGenerator implements ClassGenerator
{
    /**
     * @var ReflectionClass
     */
    protected $parent;

    /**
     * @var string
     */
    protected $name;

    /**
     * @var MethodGenerator[]
     */
    protected $methods = [];

    /**
     * Generator for code that extends a parent class
     *
     * @param ReflectionClass $parent
     * @param $name
     */
    public function __construct(ReflectionClass $parent, $name)
    {
        $this->parent = $parent;
        $this->name = $name;
    }

    /**
     * Add a method generator
     *
     * @param MethodGenerator $methodGenerator
     * @return $this
     */
    public function addMethod(MethodGenerator $methodGenerator)
    {
        $this->methods[$methodGenerator->getName()] = $methodGenerator;
        return $this;
    }

    /**
     * Add a non-generated trait
     *
     * @param ReflectionClass $trait
     * @return $this
     */
    public function addTrait(ReflectionClass $trait)
    {
        // @todo
        return $this;
    }

    /**
     * Get code for this class
     *
     * @return string
     */
    public function __toString()
    {
        $name = $this->getName();
        $parent = $this->parent->getName();
        // Build body
        $body = '';
        foreach ($this->methods as $method) {
            $body .= $method->__toString();
        }

        // Template string
        return <<<EOS
<?php
/**
 * Automatically scaffolded
 */
class $name extends $parent
{
$body
}
EOS;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }
}
