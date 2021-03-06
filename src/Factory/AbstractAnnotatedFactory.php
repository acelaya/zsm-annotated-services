<?php
namespace Acelaya\ZsmAnnotatedServices\Factory;

use Acelaya\ZsmAnnotatedServices\Annotation\Inject;
use Acelaya\ZsmAnnotatedServices\Exception;
use Acelaya\ZsmAnnotatedServices\Exception\InvalidArgumentException;
use Doctrine\Common\Annotations\AnnotationReader;
use Doctrine\Common\Annotations\AnnotationRegistry;
use Doctrine\Common\Annotations\CachedReader;
use Doctrine\Common\Annotations\Reader;
use Doctrine\Common\Cache\Cache;
use Interop\Container\Exception\ContainerException;
use Interop\Container\Exception\NotFoundException;
use Zend\ServiceManager\ServiceLocatorInterface;

abstract class AbstractAnnotatedFactory
{
    const CACHE_SERVICE = 'Acelaya\ZsmAnnotatedServices\Cache';

    /**
     * @var Reader
     */
    private static $annotationReader;

    protected function processDependenciesFromAnnotations(ServiceLocatorInterface $container, $serviceName)
    {
        if (! class_exists($serviceName)) {
            throw new Exception\RuntimeException(sprintf(
                'Annotated factories can only be used with services that are identified by their FQCN. ' .
                'Provided "%s" service name is not a valid class.',
                $serviceName
            ));
        }

        $annotationReader = $this->createAnnotationReader($container);
        $refClass = new \ReflectionClass($serviceName);
        $constructor = $refClass->getConstructor();
        if ($constructor === null) {
            return new $serviceName();
        }

        /** @var Inject $inject */
        $inject = $annotationReader->getMethodAnnotation($constructor, Inject::class);
        if ($inject === null) {
            throw new Exception\RuntimeException(sprintf(
                'You need to use the "%s" annotation in "%s" constructor so that the "%s" can create it.',
                Inject::class,
                $serviceName,
                static::class
            ));
        }

        $services = [];
        foreach ($inject->getServices() as $serviceKey) {
            $parts = explode('.', $serviceKey);

            // Even when dots are found, try to find a service with the full name
            // If it is not found, then assume dots are used to get part of an array service
            if (count($parts) > 1 && ! $container->has($serviceKey)) {
                $serviceKey = array_shift($parts);
            } else {
                $parts = [];
            }

            if (! $container->has($serviceKey)) {
                throw new Exception\RuntimeException(sprintf(
                    'Defined injectable service "%s" could not be found in container.',
                    $serviceKey
                ));
            }

            $service = $container->get($serviceKey);
            $services[] = empty($parts) ? $service : $this->readKeysFromArray($parts, $service);
        }

        return new $serviceName(...$services);
    }

    /**
     * @param ServiceLocatorInterface $container
     * @return Reader
     * @throws NotFoundException
     * @throws ContainerException
     * @throws \InvalidArgumentException
     */
    private function createAnnotationReader(ServiceLocatorInterface $container)
    {
        if (self::$annotationReader !== null) {
            return self::$annotationReader;
        }

        AnnotationRegistry::registerLoader('class_exists');

        if (! $container->has(self::CACHE_SERVICE)) {
            return self::$annotationReader = new AnnotationReader();
        } else {
            /** @var Cache $cache */
            $cache = $container->get(self::CACHE_SERVICE);
            return self::$annotationReader = new CachedReader(new AnnotationReader(), $cache);
        }
    }

    /**
     * @param array $keys
     * @param array|\ArrayAccess $array
     * @return mixed|null
     * @throws InvalidArgumentException
     */
    private function readKeysFromArray(array $keys, $array)
    {
        $key = array_shift($keys);

        // When one of the provided keys is not found, thorw an exception
        if (! isset($array[$key])) {
            throw new InvalidArgumentException(sprintf(
                'The key "%s" provided in the dotted notation could not be found in the array service',
                $key
            ));
        }

        $value = $array[$key];
        if (! empty($keys) && (is_array($value) || $value instanceof \ArrayAccess)) {
            $value = $this->readKeysFromArray($keys, $value);
        }

        return $value;
    }
}
