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
    public $dottedArrayAccess;

    /**
     * Foo constructor.
     * @param $foo
     * @param $bar
     * @param $helloWorld
     * @param $dottedService
     * @param $dottedArrayAccess
     * @Inject({
     *     "serviceA",
     *     "serviceB",
     *     "config.foo.bar",
     *     "dotted.service.which.is.not.array",
     *     "config.dotted.array.access"
     * })
     */
    public function __construct($foo, $bar, $helloWorld, $dottedService, $dottedArrayAccess)
    {
        $this->foo = $foo;
        $this->bar = $bar;
        $this->helloWorld = $helloWorld;
        $this->dottedService = $dottedService;
        $this->dottedArrayAccess = $dottedArrayAccess;
    }
}
