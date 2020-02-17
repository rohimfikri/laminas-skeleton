<?php
namespace Core\Factory\Authentication;

use Interop\Container\ContainerInterface;
use Laminas\Authentication\AuthenticationService;
use Laminas\ServiceManager\Factory\FactoryInterface;
use Laminas\Session\SessionManager;
use Laminas\Authentication\Storage\Session as SessionStorage;
use Core\Adapter\Authentication\AuthenticationAdapter;
use Zend\Debug\Debug;

/**
 * The factory responsible for creating of authentication service.
 */
class AuthenticationServiceFactory implements FactoryInterface
{
    /**
     * This method creates the Zend\Authentication\AuthenticationService service
     * and returns its instance.
     */
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        $sessionManager = $container->get(SessionManager::class);
        $authStorage = new SessionStorage(_APP_ALIAS_.'_AUTH', 'authentication', $sessionManager);
        $authAdapter = $container->get(AuthenticationAdapter::class);
        // var_dump($sessionManager);die();
        // d($sessionManager,$authAdapter);die();
        // Debug::dump($authAdapter);die();
        // Create the service and inject dependencies into its constructor.
        $class = $requestedName;
        return new $class($authStorage, $authAdapter);
    }
}
