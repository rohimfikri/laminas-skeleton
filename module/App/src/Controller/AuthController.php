<?php

/**
 * @see       https://github.com/laminas/laminas-mvc-skeleton for the canonical source repository
 * @copyright https://github.com/laminas/laminas-mvc-skeleton/blob/master/COPYRIGHT.md
 * @license   https://github.com/laminas/laminas-mvc-skeleton/blob/master/LICENSE.md New BSD License
 */

declare(strict_types=1);

namespace App\Controller;

use Laminas\Mvc\Controller\AbstractActionController;
use Core\Form\CsrfForm;
use Laminas\Form\Element\Hidden as HiddenForm;
use Laminas\View\Model\ViewModel;
use Laminas\Session\Container;
use Laminas\Authentication\AuthenticationService;
use Zend\Debug\Debug;

class AuthController extends AbstractActionController{
    private $container;
    private $config;
    
    public function __construct($container, $config){
        $this->container = $container;
        $this->config = $config;
    }

    private function defaultRedirect(){
        $user = $this->identity();
        if (isset($user['redirect_url']) && !is_null($user['redirect_url']) && $user['redirect_url']!=""){
            return $this->redirect()->toUrl($user['redirect_url']);
        }else if (isset($user['redirect_route']) && !is_null($user['redirect_route']) && $user['redirect_route']!=""){
            $router = $this->container->get('Router');
            if ($router->hasRoute($user['redirect_route'])) {
                $param = (isset($user['redirect_param']) && !is_null($user['redirect_param']) && $user['redirect_param']!="")?$user['redirect_route']:"{}";
                $query = (isset($user['redirect_query']) && !is_null($user['redirect_query']) && $user['redirect_query']!="")?$user['redirect_query']:"{}";
                $param = json_decode($param,true);
                $query = json_decode($query,true);
                $param = (is_array($param))?$param:[];
                $query = (is_array($query))?$query:[];
                return $this->redirect()->toRoute($user['redirect_route'],$param,$query);
            }
        }else {
            if (isset($user['main_role']) && !is_null($user['main_role'])){
                $mainrole = $user['main_role'];
                if (isset($user['roles'][$mainrole]) && !is_null($user['roles'][$mainrole])){
                    $role = $user['roles'][$mainrole];
                    if (isset($role['redirect_url']) && !is_null($role['redirect_url']) && $role['redirect_url']!=""){
                        return $this->redirect()->toUrl($role['redirect_url']);
                    }else if (isset($role['redirect_route']) && !is_null($role['redirect_route']) && $role['redirect_route']!=""){
                        $router = $this->container->get('Router');
                        if ($router->hasRoute($role['redirect_route'])) {
                            $param = (isset($role['redirect_param']) && !is_null($role['redirect_param']) && $role['redirect_param']!="")?$role['redirect_route']:"{}";
                            $query = (isset($role['redirect_query']) && !is_null($role['redirect_query']) && $role['redirect_query']!="")?$role['redirect_query']:"{}";
                            $param = json_decode($param,true);
                            $query = json_decode($query,true);
                            $param = (is_array($param))?$param:[];
                            $query = (is_array($query))?$query:[];
                            return $this->redirect()->toRoute($role['redirect_route'],$param,$query);
                        }
                    }
                }
            }
        }
        
        return $this->redirect()->toRoute('app');
    }

    public function loginAction(){
        // Debug::dump($this->params()->fromRoute());
        // Debug::dump($this->params()->fromQuery());die('qqq');
        $isLoginError = false;
        $msg = 'Please sign in';
        $redirectUrl = (string)$this->params()->fromQuery('redirectUrl', '');
        $auth = $this->Auth();
        if (strlen($redirectUrl)>2048) {
            throw new \Exception("Too long redirectUrl argument passed");
            $isLoginError = true;
        }else{
            if($auth->isLogin()){
                if(empty($redirectUrl)) {
                    return $this->defaultRedirect();
                } else {
                    $this->redirect()->toUrl($redirectUrl);
                }
            }
        }
        $form = new CsrfForm();
        $redirect_url = new HiddenForm('redirect_url');
        $redirect_url->setValue($redirectUrl);
        $form->add($redirect_url);
        
        $wait = 0;
        $login_container = $this->container->get('container_login');
        // !d($login_container->__isset("start_wait"));
        if($login_container->__isset("start_wait")){
            $start_wait = $login_container->__get("start_wait");
            $currentime = time();
            $wait = $currentime-$start_wait;
            // !d($start_wait,$wait);
            if($wait>=_LOGIN_WAIT_){
                $wait = 0;
                $login_container->__unset("start_wait");
                $login_container->__unset("try");
            }else{
                $wait = _LOGIN_WAIT_ - $wait;
            }
        }
        $trylogin = 1;
        if($this->getRequest()->isPost() && $wait==0) {
            // !d($this->config);die();
            // $sess_manager = $this->container->get(\Laminas\Session\SessionManager::class);   
            // !d($sess_manager);die();
            // !d($login_container->__isset("try"),$login_container->__get("try_wait"));
            if($login_container->__isset("try")){
                $trylogin = $login_container->__get("try")+1;
            }
            // $login_container->__set("try",2);
            // !d($login_container->__isset("try_wait"));
            // $sess_manager = $login_container->getManager();
            // d($login_container,$sess_manager,$sess_manager->getSaveHandler()
            // ,$sess_manager->getStorage(),$sess_manager->sessionExists(),$sess_manager->getId());die();
            $data = $this->params()->fromPost();
            $form->setData($data);
            $isvalid = $form->isValid();
            // !d($data, $isvalid, $form->getMessages(), $form->hasValidated());//die();
            if($isvalid){
                $user = $auth->login($data['username'], $data['password'], ($data['remember_me'])??"off");
                // !d($user,$auth->isLogin());die();
                if($auth->isLogin()){
                    $wait = 0;
                    $login_container->__unset("start_wait");
                    $login_container->__unset("try");
                    $init_container = $this->container->get("container_init");
                    $init_container->__set("uid", $auth->getIdentity()['id']);
                    if(empty($redirectUrl)) {
                        return $this->defaultRedirect();
                    } else {
                        return $this->redirect()->toUrl($redirectUrl);
                    }
                }else{
                    $isLoginError = true;
                    $msg = "Please input valid data";
                    $login_container->__set("try",$trylogin);
                }
            }else{
                $isLoginError = true;
                $msg = "Please input valid data";
                $login_container->__set("try",$trylogin);
            }
            
            if($trylogin>_MAX_LOGIN_TRY_){
                $isLoginError = false;
                $wait = _LOGIN_WAIT_;
                $currentime = time();
                $login_container->__set("start_wait",$currentime);
            }

            
            // $session = $container->get(SessionManager::class);
        }
        // else if($this->getRequest()->isGet()){
        //     $trylogin = 1;
        //     $wait = 0;
        //     $login_container->__unset("start_wait");
        //     $login_container->__unset("try");
        // }

        $view = new ViewModel([
            'form' => $form,
            'wait' => $wait,
            'try' => $trylogin,
            'title' => _APP_NAME_,
            'msg' => $msg,
            'isLoginError' => $isLoginError,
            'redirectUrl' => $redirectUrl
        ]);

        $view->setTemplate("login-1");
        $view->setTerminal(true);
        return $view;
    }


    public function logoutAction(){
        $module = (string)$this->params()->fromQuery('module', '');
        $auth = $this->Auth();
        $auth->logout();
        if($module==""){
            return $this->redirect()->toRoute('app/auth', ['action'=>'login']);
        }else{
            return $this->redirect()->toRoute('app/auth', ['action'=>'login'], ['query' => ['module'=>$module]]);
        }
    }
}
