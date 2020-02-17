<?php
namespace Core\Factory\Session;
use Interop\Container\ContainerInterface;
use Interop\Container\Exception\ContainerException;
use Laminas\ServiceManager\Exception\ServiceNotCreatedException;
use Laminas\ServiceManager\Exception\ServiceNotFoundException;
use Laminas\ServiceManager\Factory\FactoryInterface;
use Laminas\Session\Config\SessionConfig;
use Laminas\Session\Container;
use Laminas\Session\SessionManager;

use Laminas\Db\TableGateway\TableGateway;
use Core\SaveHandler\Session\MainSaveHandler;
use Core\SaveHandler\Session\MainSaveHandlerOptions;

use Laminas\Cache\StorageFactory;
use Laminas\Session\SaveHandler\Cache;

class SessionManagerFactory implements FactoryInterface
{
    /**
     * Create an object
     *
     * @param  ContainerInterface $container
     * @param  string $requestedName
     * @param  null|array $options
     *
     * @return object
     * @throws ServiceNotFoundException if unable to resolve the service.
     * @throws ServiceNotCreatedException if an exception is raised when
     *     creating a service.
     * @throws ContainerException if any other error occurs
     */
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        $config = $container->get('config');
        // $container = $container->get(Container::class);
        // !d($config['session_manager']);die('qqq');
        // !d($config['session_manager'],$container);die('qqq');
        if (!isset($config['session_manager'])) {
            $sessionManager = new SessionManager();
            Container::setDefaultManager($sessionManager);
            return $sessionManager;
        }
        $session = $config['session_manager'];
        // Debug::dump($session);die('qqq');

        // create session config if exists in global configuration
        $sessionConfig = null;
        if (isset($session['config'])) {
            $class = isset($session['config']['class'])
                ?  $session['config']['class']
                : SessionConfig::class;

            $options = isset($session['config']['options'])
                ?  $session['config']['options']
                : [];
            // Debug::dump($options);

            $sessionConfig = new $class();
            $sessionConfig->setOptions($options);
        }
        // Debug::dump($sessionConfig);die('qqq');

        // create session storage if exists in global configuration
        $sessionStorage = null;
        if (isset($session['storage'])) {
            $class = isset($session['storage'])
                ?  $session['storage']
                : Laminas\Session\Storage\SessionArrayStorage::class;
            $sessionStorage = new $class();
        }
        // Debug::dump($sessionStorage);die('qqq');

        $sessionSaveHandler = null;
        if (isset($session['save_handler'])) {
            // class should be fetched from service manager
            // since it will require constructor arguments
            $sessionSaveHandler = $container->get($session['save_handler']);
        }else if(_SESSION_SAVEHANDLER_=="DB"){
            $dbadapter = $container->get('db-sys');
            $tableGateway = new TableGateway(_SESSION_TABLE_, $dbadapter);
            $sessionSaveHandler  = new MainSaveHandler($container,$tableGateway, new MainSaveHandlerOptions());
        }else if(_SESSION_SAVEHANDLER_=="FILE"){
            $config = $container->get('Config');
            // Debug::dump($config['cache']);die();
            $adapter = $config['caches']['session-file']['adapter'];
            // Debug::dump($adapter);//die();
            $cache = StorageFactory::factory([
                'adapter' => $adapter
            ]);
            // Debug::dump($cache);die();

            $sessionSaveHandler = new Cache($cache);
        }
        // Debug::dump($sessionSaveHandler);die('qqq');
        
        $sessionValidator = isset($session['validators'])
            ?  $session['validators']
            : [];
        // Debug::dump($sessionValidator);die('qqq');
        
        // optional get a save handler and store it into SessionManager (currently null)
        $sessionManager = new SessionManager(
            $sessionConfig,
            $sessionStorage,
            $sessionSaveHandler,
            $sessionValidator
        );
        // Debug::dump($sessionManager);die();
        
        Container::setDefaultManager($sessionManager);
        return $sessionManager;
    }
}