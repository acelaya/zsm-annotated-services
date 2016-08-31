<?php
namespace AcelayaTest\ZsmAnnotatedServices\Mock;

use Acelaya\ZsmAnnotatedServices\Annotation\Inject;

class FooBar
{
    /**
     * FooBar constructor.
     * @param $arg
     * @Inject({"config.something.invalid.something_else"})
     */
    public function __construct($arg)
    {
    }
}
