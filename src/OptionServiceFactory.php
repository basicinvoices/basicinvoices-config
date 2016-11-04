<?php
namespace BasicInvoices\Option;

use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;
use Interop\Container\ContainerInterface;
use Zend\Db\Adapter\AdapterInterface;

class OptionServiceFactory implements FactoryInterface
{
    /**
     * Create an object
     *
     * @param  ContainerInterface $container
     * @param  string             $requestedName
     * @param  null|array         $options
     * @return Option
     */
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        $adapter = $container->get(AdapterInterface::class);
        return new Option($adapter, 'options');
    }
    
    /**
     * Create service
     *
     * @param ServiceLocatorInterface $serviceLocator
     * @return Option
     */
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        return $this($serviceLocator, Option::class);
    }
}