<?php
namespace AcelayaTest\ZsmAnnotatedServices\Factory\V2;

use Acelaya\ZsmAnnotatedServices\Factory\V2\AnnotatedFactory;
use AcelayaTest\ZsmAnnotatedServices\Mock\Bar;
use AcelayaTest\ZsmAnnotatedServices\Mock\Baz;
use AcelayaTest\ZsmAnnotatedServices\Mock\Foo;
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
        ]]);
    }

    /**
     * @test
     */
    public function serviceIsCreated()
    {
        $instance = $this->factory->__invoke($this->sm, 'anything', Foo::class);
        $this->assertInstanceOf(Foo::class, $instance);
    }

    /**
     * @test
     */
    public function dependenciesAreInjected()
    {
        /** @var Foo $instance */
        $instance = $this->factory->__invoke($this->sm, 'anything', Foo::class);
        $this->assertEquals($this->sm->get('serviceA'), $instance->foo);
        $this->assertEquals($this->sm->get('serviceB'), $instance->bar);
    }

    /**
     * @test
     */
    public function annotationsAreCachedWhenCacheServiceExists()
    {
        $cache = new ArrayCache();
        $class = new \ReflectionClass(ArrayCache::class);
        $property = $class->getProperty('data');
        $property->setAccessible(true);
        $this->sm->setService(AnnotatedFactory::CACHE_SERVICE, $cache);

        $this->assertEmpty($property->getValue($cache));
        $this->factory->__invoke($this->sm, 'anything', Foo::class);
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
        $this->factory->__invoke($this->sm, 'anything', Bar::class);
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
        $this->factory->__invoke($this->sm, 'anything', Baz::class);
    }
}
