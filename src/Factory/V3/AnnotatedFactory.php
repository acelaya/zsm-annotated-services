<?php
namespace Acelaya\ZsmAnnotatedServices\Factory\V3;

use Acelaya\ZsmAnnotatedServices\Factory\AbstractAnnotatedFactory;
use Interop\Container\ContainerInterface;

class AnnotatedFactory extends AbstractAnnotatedFactory
{
    public function __invoke(ContainerInterface $container, $requestedName)
    {
        return $this->processDependenciesFromAnnotations($container, $requestedName);
    }
}
