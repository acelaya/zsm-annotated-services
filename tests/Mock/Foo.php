<?php
namespace AcelayaTest\ZsmAnnotatedServices\Mock;

/**
 * Class Foo
 * @author
 * @link
 */
class Foo
{
    /**
     * Foo constructor.
     * @param $foo
     * @param $bar
     * @\Acelaya\ZsmAnnotatedServices\Annotation\Inject({AnnotationReader::class, "serviceB"})
     */
    public function __construct($foo, $bar)
    {

    }
}
