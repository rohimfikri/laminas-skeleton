<?php
namespace Core\Helper\Authentication;

use Laminas\Mvc\Controller\Plugin\AbstractPlugin;
use Laminas\Authentication\AuthenticationService;
use Laminas\Session\SessionManager;
use Laminas\Authentication\Result;
use Zend\Debug\Debug;

/**
 * This view helper class displays a menu bar.
 */

class Auth extends AbstractPlugin 
{
    private $config;
    private $container;
    private $authService;
    private $sessionManager;
    
    public function __construct($container,$config)
    {
        $this->container = $container;
        $this->config = $config;
        $this->authService = $container->get(AuthenticationService::class); 
        $this->sessionManager = $container->get(SessionManager::class);
    }

    public function isLogin(){
        return (($this->authService->getIdentity()!=null) || ($this->authService->hasIdentity()));
    }

    public function getIdentity(){
        return $this->authService->getIdentity();
    }

    /**
     * Performs a login attempt. If $rememberMe argument is true, it forces the session
     * to last for one month (otherwise the session expires on one hour).
     */
    public function login($username, $password, $rememberMe)
    {
        // Debug::dump($username);//die();
        // Debug::dump($password);//die();
        // Debug::dump($rememberMe);die();
        // Check if user has already logged in. If so, do not allow to log in
        // twice.
        if ($this->authService->getIdentity()!=null) {
            // throw new \Exception('Already logged in');
            // Debug::dump($this->authService->getIdentity());die();
            return $this->authService->getIdentity();
        }

        // Authenticate with login/password.
        $authAdapter = $this->authService->getAdapter();
        // Debug::dump($authAdapter);die();
        $authAdapter->setUsername($username);
        $authAdapter->setPassword($password);
        // Debug::dump($authAdapter);die();
        $result = $this->authService->authenticate();
        // !d($result);die();

        // If user wants to "remember him", we will make session to expire in
        // one month. By default session expires in 1 hour (as specified in our
        // config/global.php file).
        if ($result->getCode()==Result::SUCCESS && ($rememberMe=="1" || $rememberMe=="on")) {
            // Session cookie will expire in 1 month (30 days).
            $this->sessionManager->rememberMe(_REMEMBER_ME_);
        }

        return $result->getIdentity();
    }

    /**
     * Performs user logout.
     */
    public function logout()
    {
        // Allow to log out only when user is logged in.
        $identity = $this->authService->getIdentity();
        if ($identity==null) {
            // throw new \Exception('The user is not logged in');
        }else{
            // $authAdapter = $this->authService->getAdapter();
            // $authAdapter->removeSessionData($identity);
        }

        // Remove identity from session.
        $this->authService->clearIdentity();
    }

    /**
     * This is a simple access control filter. It is able to restrict unauthorized
     * users to visit certain pages.
     *
     * This method uses the 'access_filter' key in the config file and determines
     * whenther the current visitor is allowed to access the given controller action
     * or not. It returns true if allowed; otherwise false.
     */
    public function authAccess($controllerName, $actionName)
    {
        // Debug::dump($this->config);
        // Debug::dump($controllerName);//die();
        if(isset($this->config['controllers']['aliases'][$controllerName]))
          $controllerName = $this->config['controllers']['aliases'][$controllerName];
        // Debug::dump($controllerName);//die();
        $tmp = explode("\\", $controllerName);
        // Debug::dump($tmp);//die();
        // Debug::dump($actionName);die();
        if($tmp[0]==_PUBLIC_MODULE_)return true;
        // $identity = $this->authService->getIdentity();
        // Debug::dump($identity['permission']);die();
        // Debug::dump($this->authService->hasIdentity());die();
        // Determine mode - 'restrictive' (default) or 'permissive'. In restrictive
        // mode all controller actions must be explicitly listed under the 'access_filter'
        // config key, and access is denied to any not listed action for unauthorized users.
        // In permissive mode, if an action is not listed under the 'access_filter' key,
        // access to it is permitted to anyone (even for not logged in users.
        // Restrictive mode is more secure and recommended to use.

        // Debug::dump($this->config);die();
        $accessfilter = $this->config["access_filter"];
        $mode = isset($accessfilter['options']['mode'])?$accessfilter['options']['mode']:'restrictive';
        if ($mode!='restrictive' && $mode!='permissive')
            throw new \Exception('Invalid access filter mode (expected either restrictive or permissive mode');
        // Debug::dump($controllerName);//die();
        // Debug::dump($accessfilter);die();

        if (isset($accessfilter['controllers'][$controllerName])) {
            $items = $accessfilter['controllers'][$controllerName];
            // Debug::dump($items);die();
            foreach ($items as $item) {
                $actionList = $item['actions'];
                $allow = $item['allow'];
                // Debug::dump($actionList);//die();
                // Debug::dump($allow);die();
                if (is_array($actionList) && in_array($actionName, $actionList) ||
                    $actionList=='*') {
                    if ($allow=='*')
                        return true; // Anyone is allowed to see the page.
                    else if ($allow=='%' && $this->authService->hasIdentity())
                        return true;
                    else if ($allow=='@' && $this->authService->hasIdentity()) {
                        // die('qqq');
                        return $this->checkPermission($controllerName, $actionName); // Only authenticated user is allowed to see the page.
                    } else {
                        return false; // Access denied.
                    }
                }
            }
        }
        return $this->checkPermission($controllerName, $actionName);



        // Debug::dump($mode);die();
        // In restrictive mode, we forbid access for unauthorized users to any
        // action not listed under 'access_filter' key (for security reasons).
        // if ($mode=='restrictive' && !$this->authService->hasIdentity())
        return false;

        // Permit access to this page.
        // return true;
    }

    private function checkPermission($controllerName, $actionName){
        if ($this->authService->getIdentity()==null)return false;
        $tmp = explode("\\", $controllerName);
        if(count($tmp)!=3)return false;
        $controller = str_replace("Controller", "", $tmp[2]);

        $identity = $this->authService->getIdentity();
        // Debug::dump($controller);
        // Debug::dump($actionName);
        // Debug::dump($identity);
        // Debug::dump($identity['permission']);die();

        $permission = $identity['permission'];
        // Debug::dump($tmp);
        // Debug::dump($permission);//die();
        // Debug::dump($tmp);//die();
 // && in_array($actionName,$permission[$tmp[0]][$tmp[2]]))
        // Debug::dump($permission[$tmp[0]]);//die();
        // Debug::dump(isset($permission[$tmp[0]]));//die();
        // Debug::dump((isset($permission[$tmp[0]][$controller]) && in_array($actionName,$permission[$tmp[0]][$controller]))
        // ||
        // (isset($permission['*']['*']) && in_array('*',$permission['*']['*']))
        // ||
        // (isset($permission['*'][$controller]) && in_array('*',$permission['*'][$controller]))
        // ||
        // (isset($permission[$tmp[0]][$controller]) && in_array('*',$permission[$tmp[0]][$controller]))
        // ||
        // (isset($permission['*']['*']) && in_array($actionName,$permission['*']['*']))
        // ||
        // (isset($permission['*'][$controller]) && in_array($actionName,$permission['*'][$controller]))
        // ||
        // (isset($permission[$tmp[0]][$controller]) && in_array($actionName,$permission[$tmp[0]][$controller])));die();
        return (
            (isset($permission[$tmp[0]][$controller]) && in_array($actionName,$permission[$tmp[0]][$controller]))
            ||
            (isset($permission['*']['*']) && in_array('*',$permission['*']['*']))
            ||
            (isset($permission['*'][$controller]) && in_array('*',$permission['*'][$controller]))
            ||
            (isset($permission[$tmp[0]][$controller]) && in_array('*',$permission[$tmp[0]][$controller]))
            ||
            (isset($permission['*']['*']) && in_array($actionName,$permission['*']['*']))
            ||
            (isset($permission['*'][$controller]) && in_array($actionName,$permission['*'][$controller]))
            ||
            (isset($permission[$tmp[0]][$controller]) && in_array($actionName,$permission[$tmp[0]][$controller]))
        );
    }

}