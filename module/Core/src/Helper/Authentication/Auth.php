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
        if ($result->getCode()===Result::SUCCESS && ($rememberMe==="1" || $rememberMe=="on")) {
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
        if ($identity===null) {
            // throw new \Exception('The user is not logged in');
        }else{
            // $authAdapter = $this->authService->getAdapter();
            // $authAdapter->removeSessionData($identity);
        }

        // Remove identity from session. 
        $this->authService->clearIdentity();
    }

}