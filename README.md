# Zend\ServiceManager Annotated Services

[![Build Status](https://travis-ci.org/acelaya/zsm-annotated-services.svg?branch=master)](https://travis-ci.org/acelaya/zsm-annotated-services)
[![Code Coverage](https://scrutinizer-ci.com/g/acelaya/zsm-annotated-services/badges/coverage.png?b=master)](https://scrutinizer-ci.com/g/acelaya/zsm-annotated-services/?branch=master)
[![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/acelaya/zsm-annotated-services/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/acelaya/zsm-annotated-services/?branch=master)
[![Latest Stable Version](https://poser.pugx.org/acelaya/zsm-annotated-services/v/stable.png)](https://packagist.org/packages/acelaya/zsm-annotated-services)
[![Total Downloads](https://poser.pugx.org/acelaya/zsm-annotated-services/downloads.png)](https://packagist.org/packages/acelaya/zsm-annotated-services)
[![License](https://poser.pugx.org/acelaya/zsm-annotated-services/license.png)](https://packagist.org/packages/acelaya/zsm-annotated-services)

If you are tired of defining lots of factories in your projects just to fetch some dependencies from the ServiceManager and the create a new service instance that gets those dependencies injected, try this.

It is a component that allows to define how dependency injection has to be performed with Zend\ServiceManager via annotations.

### Installation

Install this component with composer.

    composer require acelaya/zsm-annotated-services

### Basic usage

The traditional process is that you need to create a factory for each new service. Maybe sometimes you can reuse certain factories or abstract factories, but it is not the usual case.

```php
namespace Acelaya;

use Interop\Container\ContainerInterface;

class MyFactory
{
    public funciton __invoke(ContainerInterface $container, $requestedName)
    {
        $foo = $container->get(Foo::class);
        $bar = $container->get('bar');

        return new MyService($foo, $bar);
    }
}
```

With this component you just need to add a simple annotation to your service constructor with the services that need to be fetched from the ServiceManager and injected.

```php
namespace Acelaya;

use Acelaya\ZsmAnnotatedServices\Annotation\Inject;

class MyService
{
    /**
     * @Inject({Foo::class, "bar"})
     */
    public function __construct($foo, $bar)
    {
        // [...]
    }

    // [...]
}
```

And then, register the service with one of the provided factories (There is one factory that's used with Zend\ServiceManager 2 and another that's used with Zend\ServiceManager 3)

```php
use Acelaya\MyService;
use Acelaya\ZsmAnnotatedServices\Factory\V3\AnnotatedFactory;
use Zend\ServiceManager\ServiceManager;

$sm = new ServiceManager([
    'factories' => [
        MyService::class => AnnotatedFactory::class,
    ],
]);
```

You just need to replace `Acelaya\ZsmAnnotatedServices\Factory\V3\AnnotatedFactory` by `Acelaya\ZsmAnnotatedServices\Factory\V2\AnnotatedFactory` if you are using the v2 ServiceManager.

### Cache

That looks cool, but processing annotations takes time. If you use this approach with several services, you will see your application's performance reduced.

That's why this library allows to use Doctrine\Cache adapters in order to cache the result of processing annotations.

First install the cache component.

    composer require doctrine/cache

Then register another service which returns a `Doctrine\Common\Cache\Cache` instance with the key `Acelaya\ZsmAnnotatedServices\Factory\AbstractAnnotatedFactory::CACHE_SERVICE` (or just "Acelaya\ZsmAnnotatedServices\Cache", which is the value of the constant).

By doing this, your annotations will be processed and cached, improving performance for subsequent requests.

### Dot notation for array services

When you need to inject just one part of a service which contains an array, you can use the dot notation, where the first part is the service name and the rest of the parts are the keys to fetch from the array.

For example, imagine this services specification:

```php
use Acelaya\MyService;
use Acelaya\ZsmAnnotatedServices\Factory\V3\AnnotatedFactory;
use Zend\ServiceManager\ServiceManager;

$sm = new ServiceManager([
    'services' => [
        'config' => [
            'mail' => [
                'smtp' => [
                    // [...]
                ],
                'from' => 'foo@bar.com',
                'subject' => 'Welcome!',
            ],
            'logger' => [
                'file' => '/var/log/my_log.log'
            ],
        ],
    ],
    'factories' => [
        MyService::class => AnnotatedFactory::class,
    ],
]);
```

And this service with the `@Inject` annotation:

```php
namespace Acelaya;

use Acelaya\ZsmAnnotatedServices\Annotation\Inject;

class MyService
{
    /**
     * @Inject({"config.mail.from"})
     */
    public function __construct($from)
    {
        // The value of $from will be 'foo@bar.com'
    }
}
```

The injectable service is defined by **config.mail.from**. In this case, the `AnnotatedFactory` will assume that the service name is **config**, and that it contains an associative array. Then, it will use the rest of the dotted parts as nested keys in that array, and finally get the last value and inject it in the service.
