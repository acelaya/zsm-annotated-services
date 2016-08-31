<?php
namespace AcelayaTest\ZsmAnnotatedServices\Factory\V2;

use Acelaya\ZsmAnnotatedServices\Factory\AbstractAnnotatedFactory;
use Acelaya\ZsmAnnotatedServices\Factory\V2\AnnotatedFactory;
use AcelayaTest\ZsmAnnotatedServices\Mock;
use Doctrine\Common\Cache\ArrayCache;
use PHPUnit_Framework_TestCase as TestCase;
use Zend\ServiceManager\ServiceManager;

class AnnotatedFactoryTest extends TestCase
{
    /**
     * @var AnnotatedFactory
     */
    private $factory;
    /**
     * @var ServiceManager
     */
    private $sm;

    public function setUp()
    {
        $this->factory = new AnnotatedFactory();
        $this->sm = new ServiceManager(['services' => [
            'serviceA' => 'foo_service',
            'serviceB' => ['bar_service'],
            'config' => [
                'foo' => [
                    'bar' => 'Hello World',
                ],
                'something' => [],
            ],
            'dotted.service.which.is.not.array' => new \stdClass(),
        ]]);
    }

    /**
     * @test
     */
    public function serviceIsCreated()
    {
        $instance = $this->factory->__invoke($this->sm, 'anything', Mock\Foo::class);
        $this->assertInstanceOf(Mock\Foo::class, $instance);
    }

    /**
     * @test
     */
    public function dependenciesAreInjected()
    {
        /** @var Mock\Foo $instance */
        $instance = $this->factory->__invoke($this->sm, 'anything', Mock\Foo::class);
        $this->assertEquals($this->sm->get('serviceA'), $instance->foo);
        $this->assertEquals($this->sm->get('serviceB'), $instance->bar);
        $this->assertEquals($this->sm->get('config')['foo']['bar'], $instance->helloWorld);
        $this->assertSame($this->sm->get('dotted.service.which.is.not.array'), $instance->dottedService);
    }

    /**
     * @test
     */
    public function annotationsAreCachedWhenCacheServiceExists()
    {
        // Create a cache service
        $cache = new ArrayCache();
        $class = new \ReflectionClass(ArrayCache::class);
        $property = $class->getProperty('data');
        $property->setAccessible(true);
        $this->sm->setService(AnnotatedFactory::CACHE_SERVICE, $cache);

        // Unset the shared annotation reader, so that it is created again
        $class = new \ReflectionClass(AbstractAnnotatedFactory::class);
        $annotationreader = $class->getProperty('annotationReader');
        $annotationreader->setAccessible(true);
        $annotationreader->setValue(null);

        $this->assertEmpty($property->getValue($cache));
        $this->factory->__invoke($this->sm, 'anything', Mock\Foo::class);
        $this->assertNotEmpty($property->getValue($cache));
    }

    /**
     * @test
     * @expectedException \Acelaya\ZsmAnnotatedServices\Exception\RuntimeException
     */
    public function tryingToCreateAnInvalidClassThrowsException()
    {
        $this->factory->__invoke($this->sm, 'anything', 'invalid');
    }

    /**
     * @test
     * @expectedException \Acelaya\ZsmAnnotatedServices\Exception\RuntimeException
     */
    public function tryingToCreateAClassWithoutInjectAnnotationThrowsException()
    {
        $this->factory->__invoke($this->sm, 'anything', Mock\Bar::class);
    }

    /**
     * @test
     */
    public function creatingObjectWithoutContructorJustReturnsNewInstance()
    {
        $instance = $this->factory->__invoke($this->sm, 'anything', \stdClass::class);
        $this->assertInstanceOf(\stdClass::class, $instance);
    }

    /**
     * @test
     * @expectedException \Acelaya\ZsmAnnotatedServices\Exception\RuntimeException
     */
    public function tryingToInjectInvalidServiceThrowsException()
    {
        $this->factory->__invoke($this->sm, 'anything', Mock\Baz::class);
    }

    /**
     * @test
     * @expectedException \Acelaya\ZsmAnnotatedServices\Exception\InvalidArgumentException
     */
    public function dependingOnAnArrayWithInvalidKeysThrowsException()
    {
        $this->factory->__invoke($this->sm, 'anything', Mock\FooBar::class);
    }
}
