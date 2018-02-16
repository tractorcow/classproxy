<?php

namespace TractorCow\ClassProxy\Generators;

use InvalidArgumentException;
use Prophecy\Doubler\Doubler;
use Prophecy\Doubler\Generator\ClassCreator;
use ReflectionClass;
use ReflectionProperty;
use TractorCow\ClassProxy\Proxied\Proxied;

/**
 * Uses prophecy to generate classes
 */
class ProxyGenerator
{
    /**
     * @var ReflectionClass
     */
    protected $parent;

    /**
     * List of method implementations.
     *
     * @var array
     */
    protected $methods = [];

    /**
     * List of interfaces to apply to this class
     *
     * @var ReflectionClass[]
     */
    protected $interfaces = [];

    /**
     * List of custom properties. Keys are property names, values are flags
     * of ReflectionProperty.
     *
     * @var string[]
     */
    protected $properties = [];

    /**
     * Class name
     *
     * @var string
     */
    protected $name = null;

    /**
     * Generator for code that extends a parent class
     *
     * @param ReflectionClass $parent
     */
    public function __construct(ReflectionClass $parent)
    {
        $this->parent = $parent;
        $this->regenerateName();
    }

    public function __clone()
    {
        // Regenerate on all new instances
        $this->regenerateName();
    }

    /**
     * Adds method to an immutable clone of this generator
     *
     * @param string $name Method name
     * @param callable|string $callback Callback that has args ($args, $next),
     * or string for literal code. Methods are called first-in-first-out
     * and can call `$next(...$args)` to delegate. The closure will be bound to the
     * current object, so you can use `$this` inside it.
     * @return static Class generator with this method
     */
    public function addMethod($name, $callback)
    {
        $clone = clone $this;
        if (is_string($callback)) {
            $clone->methods[$name] = $callback;
        } elseif (is_callable($callback)) {
            if (!isset($clone->methods[$name])) {
                $clone->methods[$name] = [];
            }
            $clone->methods[$name][] = $callback;
        } else {
            throw new InvalidArgumentException("Invalid callback type");
        }
        return $clone;
    }

    /**
     * Adds interface to an immutable clone of this generator
     *
     * @param ReflectionClass|string $interface
     * @return $this
     */
    public function addInterface($interface)
    {
        if (is_string($interface)) {
            $interface = new ReflectionClass($interface);
        }
        $clone = clone $this;
        $clone->interfaces[$interface->getName()] = $interface;
        return $clone;
    }

    /**
     * Add property to an immutable clone of this generator
     *
     * @param string $name
     * @param int $flags ReflectionProperty flags
     * @return ProxyGenerator
     */
    public function addProperty($name, $flags = ReflectionProperty::IS_PROTECTED)
    {
        $clone = clone $this;
        $clone->properties[$name] = $flags;
        return $clone;
    }

    /**
     * Get code for this class
     *
     * @param array $args
     * @return string
     */
    public function instance($args = [])
    {
        // Setup class proxy
        $proxyPatch = new ProxyClassPatch($this->interfaces, $this->properties, $this->methods);
        $doubler = new Doubler(new ProxyMirror, new ClassCreator(), new ProxyNameGenerator($this->name));
        $doubler->registerClassPatch($proxyPatch);

        /** @var Proxied $instance */
        $instance = $doubler->double($this->parent, $this->interfaces, $args);
        $proxy = $instance->proxy();

        // Register callbacks on instance
        foreach ($this->methods as $name => $details) {
            // Skip code blocks
            if (is_string($details)) {
                continue;
            }
            $proxy->setMethodChain($name, $details);
        }
        return $instance;
    }

    /**
     * Generate deterministic name for proxied class
     *
     * @return string The new name
     */
    protected function regenerateName()
    {
        // Seed by list of proxied properties
        $seed = json_encode([
            $this->parent->getName(),
            array_keys($this->methods),
            array_keys($this->interfaces),
            array_keys($this->properties),
        ]);

        // Build new name based on seed and base of parent class
        $suffix = substr(sha1($seed), 0, 7);
        return $this->name = $this->parent->getShortName() . '_' . $suffix;
    }
}
