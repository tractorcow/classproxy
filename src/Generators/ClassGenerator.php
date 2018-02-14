<?php

namespace TractorCow\ClassProxy\Generators;

use ReflectionClass;

/**
 * Scaffolds code for a class
 */
interface ClassGenerator
{
    /**
     * Add a method generator
     *
     * @param MethodGenerator $methodGenerator
     * @return $this
     */
    public function addMethod(MethodGenerator $methodGenerator);

    /**
     * Add a non-generated trait
     *
     * @param ReflectionClass $trait
     * @return $this
     */
    public function addTrait(ReflectionClass $trait);

    /**
     * Get code for this class
     *
     * @return string
     */
    public function __toString();
}
