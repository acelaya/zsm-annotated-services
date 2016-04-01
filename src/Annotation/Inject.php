<?php
namespace Acelaya\ZsmAnnotatedServices\Annotation;

use Doctrine\Common\Annotations\Annotation;

/**
 * Class Inject
 * @author
 * @link
 * @Annotation
 * @Target({"METHOD"})
 */
class Inject
{
    /**
     * @var array
     */
    private $services;

    /**
     * Inject constructor.
     * @param array $values
     */
    public function __construct(array $values)
    {
        $this->services = isset($values['value']) ? $values['value'] : [];
    }

    /**
     * @return array
     */
    public function getServices()
    {
        return $this->services;
    }
}
