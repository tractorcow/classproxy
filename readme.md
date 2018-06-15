# Partial proxy class builder

[![Build Status](https://travis-ci.org/tractorcow/classproxy.svg?branch=master)](https://travis-ci.org/tractorcow/classproxy)
[![SilverStripe supported module](https://img.shields.io/badge/silverstripe-supported-0071C4.svg)](https://www.silverstripe.org/software/addons/silverstripe-commercially-supported-module-list/)

Dynamically scaffold proxy classes that actually extend the class being proxied,
allowing them to be used in type-strict applications.

No it's not prophecy because this is designed for partial proxies, not testing.

## Examples

```php
// Create a proxy creator
$proxy = ProxyFactory::create(DataBase::class)
    ->addMethod('connect', function ($args, $next) use ($logger) {
        $logger->log("Connecting to server " . $args[0]['server'];
        return $next(...$args);
    });
    
// Generate instance of our proxy
$instance = $proxy->instance();
assert($instance instanceof Database); // Yep!

// Connects to underlying database, logging the call
$instance->connect([
    'server' => 'localhost',
    'user' => 'root'
]);
```
