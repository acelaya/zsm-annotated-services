<?php
namespace AcelayaTest\ZsmAnnotatedServices\Factory\V3;

use Acelaya\ZsmAnnotatedServices\Factory\V3\AnnotatedFactory;
use AcelayaTest\ZsmAnnotatedServices\Mock\Foo;
use PHPUnit_Framework_TestCase as TestCase;
use Zend\ServiceManager\ServiceManager;

class AnnotatedFactoryTest extends TestCase
{
    /**
     * @var AnnotatedFactory
     */
    protected $factory;

    /**
     * @test
     */
    public function serviceIsCreated()
    {
        $this->factory = new AnnotatedFactory();
        $sm = new ServiceManager(['services' => [
            'serviceA' => 'foo_service',
            'serviceB' => ['bar_service'],
            'config' => [
                'foo' => [
                    'bar' => 'Hello World',
                ],
                'dotted' => new \ArrayObject([
                    'array' => new \ArrayObject([
                        'access' => 'dotted array access',
                    ], \ArrayObject::ARRAY_AS_PROPS),
                ]),
            ],
            'dotted.service.which.is.not.array' => new \stdClass(),
        ]]);

        $instance = $this->factory->__invoke($sm, Foo::class);
        $this->assertInstanceOf(Foo::class, $instance);
    }
}
