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

    /**
     * Foo constructor.
     * @param $foo
     * @param $bar
     * @Inject({"serviceA", "serviceB"})
     */
    public function __construct($foo, $bar)
    {
        $this->foo = $foo;
        $this->bar = $bar;
    }
}
