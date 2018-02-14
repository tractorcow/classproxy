<?php

namespace TractorCow\ClassProxy\Generators;

/**
 * Scaffolds code for a method
 */
interface MethodGenerator
{
    /**
     * Code that implements this method
     *
     * @return string
     */
    public function __toString();

    /**
     * Get name of this method
     *
     * @return string
     */
    public function getName();
}
