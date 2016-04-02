<?php
namespace Acelaya\ZsmAnnotatedServices\Factory\V3;

use Acelaya\ZsmAnnotatedServices\Factory\AbstractAnnotatedFactory;
use Zend\ServiceManager\ServiceLocatorInterface;

class AnnotatedFactory extends AbstractAnnotatedFactory
{
    public function __invoke(ServiceLocatorInterface $container, $requestedName)
    {
        return $this->processDependenciesFromAnnotations($container, $requestedName);
    }
}
