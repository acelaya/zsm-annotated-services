<?php
namespace AcelayaTest\ZsmAnnotatedServices\Mock;

use Acelaya\ZsmAnnotatedServices\Annotation\Inject;

/**
 * Class Foo
 * @author
 * @link
 */
class Foo
{
    public $foo;
    public $bar;
    public $helloWorld;
    public $dottedService;

    /**
     * Foo constructor.
     * @param $foo
     * @param $bar
     * @param $helloWorld
     * @param $dottedService
     * @Inject({"serviceA", "serviceB", "config.foo.bar", "dotted.service.which.is.not.array"})
     */
    public function __construct($foo, $bar, $helloWorld, $dottedService)
    {
        $this->foo = $foo;
        $this->bar = $bar;
        $this->helloWorld = $helloWorld;
        $this->dottedService = $dottedService;
    }
}
