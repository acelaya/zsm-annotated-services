<?php
namespace AcelayaTest\ZsmAnnotatedServices\Mock;

use Acelaya\ZsmAnnotatedServices\Annotation\Inject;

class Baz
{
    /**
     * Baz constructor.
     * @Inject({"invalid"})
     */
    public function __construct($foo)
    {
    }
}
