<?php

namespace TractorCow\ClassProxy;

use ReflectionClass;
use TractorCow\ClassProxy\Generators\ExtendsClassGenerator;

class ProxyFactory
{
    /**
     * Base class being proxified
     *
     * @var ReflectionClass
     */
    protected $class;

    /**
     * ProxyFactory constructor.
     *
     * @param string $class
     */
    public function __construct($class)
    {
        $this->class = new ReflectionClass($class);
    }

    /**
     * Create new proxy builder for the given class
     *
     * @param string $class
     * @return static
     */
    public static function create($class)
    {
        return new static($class);
    }

    public function getProxyClassName()
    {
        // Proxy convention is shortname_sha
        $sha = substr(sha1($this->class->getName()), 0, 7);
        return $this->class->getShortName() . '_' . $sha;
    }

    public function getProxyCodePath()
    {
        $hash = substr(sha1(__FILE__), 0, 7);
        return sys_get_temp_dir()
            . DIRECTORY_SEPARATOR
            . "proxyfactory_{$hash}"
            . DIRECTORY_SEPARATOR
            . $this->getProxyClassName()
            . '.php';
    }

    /**
     * Ensure a proxy code exists and is registered for this current proxy
     *
     * @return string
     */
    protected function ensureCode()
    {
        $proxyPath = $this->getProxyCodePath();
        if (!file_exists($proxyPath)) {
            $code = $this->generateCode();
            if (!is_dir(dirname($proxyPath))) {
                mkdir(dirname($proxyPath), 0755, true);
            }
            file_put_contents($proxyPath, $code);
        }
        // exists
        safeRequire($proxyPath);
        return $this->getProxyClassName();
    }

    /**
     * Generate code for this scaffold
     */
    protected function generateCode()
    {
        $name = $this->getProxyClassName();
        $generator = new ExtendsClassGenerator($this->class, $name);
        return $generator->__toString();
    }

    /**
     * Return instance of scaffolded object
     *
     * @return string
     */
    public function instance()
    {
        $classname = $this->ensureCode();
        $instance = new $classname;
        // @todo - instance specific proxies
        return $instance;
    }
}

/**
 * Safely require a classfile
 */
function safeRequire()
{
    require_once(func_get_arg(0));
}
