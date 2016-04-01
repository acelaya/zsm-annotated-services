<?php
namespace AcelayaTest\ZsmAnnotatedServices\Factory\V2;

use Acelaya\ZsmAnnotatedServices\Factory\V2\AnnotatedFactory;
use AcelayaTest\ZsmAnnotatedServices\Mock\Foo;
use PHPUnit_Framework_TestCase as TestCase;
use Zend\ServiceManager\ServiceManager;

class AnnotatedFactoryTest extends TestCase
{
    /**
     * @var AnnotatedFactory
     */
    private $factory;

    public function setUp()
    {
        $this->factory = new AnnotatedFactory();
    }

    /**
     * @test
     */
    public function serviceIsCreatedAndDependenciesAreInjected()
    {
        $sm = new ServiceManager(['services' => [
            'serviceA' => 'foo_service',
            'serviceB' => ['bar_service'],
        ]]);
        $instance = $this->factory->__invoke($sm, 'anything', Foo::class);
        $this->assertInstanceOf(Foo::class, $instance);
    }
}
