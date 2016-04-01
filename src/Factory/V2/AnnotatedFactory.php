<?php
namespace Acelaya\ZsmAnnotatedServices\Factory\V2;

use Acelaya\ZsmAnnotatedServices\Factory\AbstractAnnotatedFactory;
use Interop\Container\ContainerInterface;

class AnnotatedFactory extends AbstractAnnotatedFactory
{
    public function __invoke(ContainerInterface $container, $canonicalName, $requestedName)
    {
        return $this->processDependenciesFromAnnotations($container, $requestedName);
    }
}
