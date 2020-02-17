<?php
namespace Core\Factory;

use Interop\Container\ContainerInterface;
use Zend\ServiceManager\Factory\FactoryInterface;
use Zend\Authentication\AuthenticationService;
use Zend\Debug\Debug;

class MainFactory implements FactoryInterface
{
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        $config = $container->get('Config');
        $authService = $container->get(AuthenticationService::class);
        $class = $requestedName;
        return new $class($container, $config);
    }
}
