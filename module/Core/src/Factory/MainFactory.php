<?php
namespace Core\Factory;

use Interop\Container\ContainerInterface;
use Laminas\ServiceManager\Factory\FactoryInterface;
use Laminas\Authentication\AuthenticationService;
use Zend\Debug\Debug;

class MainFactory implements FactoryInterface
{
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        $config = $container->get('Config');
        // $tmp = $container->get('\Core\Helper\Data\Generator');
        // !d($tmp);die();
        // $authService = $container->get(AuthenticationService::class);
        $class = $requestedName;
        return new $class($container, $config);
    }
}
