# The only _real_ class proxy builder!

Dynamically scaffold proxy classes that actually extend the class being proxied,
allowing them to be used in type-strict applications.

## Examples

```php
$proxy = ProxyFactory::create(DataBase::class);
$instance = $proxy->instance();
assert($instance instanceof Database); // Yep!
```
