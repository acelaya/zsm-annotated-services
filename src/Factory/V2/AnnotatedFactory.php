<?php
namespace Acelaya\ZsmAnnotatedServices\Factory\V2;

use Acelaya\ZsmAnnotatedServices\Factory\AbstractAnnotatedFactory;
use Zend\ServiceManager\ServiceLocatorInterface;

class AnnotatedFactory extends AbstractAnnotatedFactory
{
    public function __invoke(ServiceLocatorInterface $container, $canonicalName, $requestedName)
    {
        return $this->processDependenciesFromAnnotations($container, $requestedName);
    }
}
