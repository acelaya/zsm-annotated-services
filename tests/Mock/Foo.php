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

    /**
     * Foo constructor.
     * @param $foo
     * @param $bar
     * @param $helloWorld
     * @Inject({"serviceA", "serviceB", "config.foo.bar"})
     */
    public function __construct($foo, $bar, $helloWorld)
    {
        $this->foo = $foo;
        $this->bar = $bar;
        $this->helloWorld = $helloWorld;
    }
}
