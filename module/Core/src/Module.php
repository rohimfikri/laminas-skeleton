<?php

/**
 * @see       https://github.com/laminas/laminas-mvc-skeleton for the canonical source repository
 * @copyright https://github.com/laminas/laminas-mvc-skeleton/blob/master/COPYRIGHT.md
 * @license   https://github.com/laminas/laminas-mvc-skeleton/blob/master/LICENSE.md New BSD License
 */

declare(strict_types=1);

namespace Core;
use Laminas\Mvc\ModuleRouteListener;
use Laminas\Mvc\MvcEvent;
use Laminas\Session\Container;
use Laminas\Authentication\AuthenticationService;
use Laminas\Http\Request as HttpRequest;
use Laminas\Console\Request as ConsoleRequest;
use Laminas\Mvc\Controller\AbstractActionController;
use Zend\Debug\Debug;
use voku\helper\AntiXSS;

class Module
{
    public function onBootstrap(MvcEvent $e)
    {
        $eventManager = $e->getApplication()->getEventManager();
        $sharedEventManager = $eventManager->getSharedManager();
        $eventManager->attach('render', [$this, 'onRender'], 100);
        $eventManager->attach('finish', [$this, 'onFinish'], 100);

        $sharedEventManager->attach(
          AbstractActionController::class,
          MvcEvent::EVENT_DISPATCH,
          [$this, 'shareDispatchAAC'], 100);


        $sharedEventManager->attach(
            '*',
            MvcEvent::EVENT_RENDER_ERROR,
            [$this, 'onRenderError'], 100);
          
        // die("qqq");
        $moduleRouteListener = new ModuleRouteListener();
        $moduleRouteListener->attach($eventManager);
        $this->bootstrapSession($e);
    }


    public function onRenderError(MvcEvent $event) {
        // Debug::dump($event->getResult());die();

        $viewModel = $event->getViewModel();
        // Debug::dump(get_class($viewModel));die();
        if (get_class($viewModel) === "Laminas\View\Model\ViewModel") {
            $viewModel->setTemplate('layout/blank');
        }
        // die('qqq');
    }
    
    public function bootstrapSession($e)
    {
        $serviceManager = $e->getApplication()->getServiceManager();
        // ob_start();
        // !d($_SESSION);
        $session = $serviceManager->get(\Laminas\Session\SessionManager::class);
        // session_start();
        // !d($session,$_SESSION);
        // !d($session->sessionExists(),$session->getId());
        $session->start();
        // !d($session->sessionExists(),$session->isValid(),$session->getId());
        // !d($session->getName());
        // Container::setDefaultManager($session);

        $container = $serviceManager->get('container_init');
        // $container->setDefaultManager($session);
        // !d($container->getIterator(),'AAA');
        // !d($container->offsetExists("init"));

        // d($container->getManager(),$container->init);die();
        $request = $serviceManager->get('Request');
        if ($container->offsetExists("init")) {
            // !d($auth);
            $container->__set("sess_id", $session->getId());
            $container->__set("remoteAddr", $request->getServer()->get('REMOTE_ADDR'));
            $container->__set("httpUserAgent", $request->getServer()->get('HTTP_USER_AGENT'));
            $container->__set("lastRequest", date("Y-m-d H:i:s"));
            // die("QQQ");
            return;
        }


        $session->regenerateId(true);
        $container->__set("init", 1);
        $container->__set("sess_id", $session->getId());
        $container->__set("remoteAddr", $request->getServer()->get('REMOTE_ADDR'));
        $container->__set("httpUserAgent", $request->getServer()->get('HTTP_USER_AGENT'));
        $container->__set("lastRequest", date("Y-m-d H:i:s"));
        // !d($container->getIterator(),'BBB');

        $config = $serviceManager->get('Config');
        // !d($config);die();
        if (! isset($config['session_manager'])) {
            return;
        }

        $sessionConfig = $config['session_manager'];
        // !d($sessionConfig);die();

        if (! isset($sessionConfig['validators'])) {
            return;
        }

        $chain   = $session->getValidatorChain();
        // !d($sessionConfig['validators']);die();

        foreach ($sessionConfig['validators'] as $validator) {
            // !d($validator,);//die();
            switch ($validator) {
                case \Laminas\Session\Validator\HttpUserAgent::class:
                    // !d($container->httpUserAgent);die();
                    $validator = new $validator($container->httpUserAgent);
                    break;
                case \Laminas\Session\Validator\RemoteAddr::class:
                    $validator  = new $validator($container->remoteAddr);
                    break;
                default:
                    $validator = new $validator();
                    break;
            }

            $chain->attach('session.validate', array($validator, 'isValid'));
        }
        // die();
        // !d($session->sessionExists(),$session->isValid(),$session->getId());
    }

    // public function registerRenderStrategy(MvcEvent $e) {
    //     $app = $e->getTarget();
    //     $locator = $app->getServiceManager();
    //     $view = $locator->get('Zend\View\View');
    //     $jsonStrategy = $locator->get('ViewJsonStrategy');
    //     $feedStrategy = $locator->get('ViewFeedStrategy');

    //     // Attach strategy, which is a listener aggregate, at high priority
    //     $jsonStrategy->attach($view->getEventManager(), 100);
    //     $feedStrategy->attach($view->getEventManager(), 100);
    // }
    
    public function loadCacheView(MvcEvent $e) {
        $app = $e->getApplication();
        $route = $e->getRouteMatch()->getMatchedRouteName();
        // Debug::dump($route);die();
        $sm = $app->getServiceManager();
        // Debug::dump($sm);die();
        $cache = $sm->get('view-file');
        // Debug::dump($cache);die();
        $post = $e->getRequest()->getPost()->toArray();
        $query = $e->getRequest()->getQuery()->toArray();
        // Debug::dump($post);
        // Debug::dump($query);//die('qqq');
        $join = array_merge($post, $query);
        // Debug::dump($join);//die('qqq');
        // Debug::dump(json_encode($join));//die('qqq');
        $salt = "cache-view-".$route;
        // Debug::dump($salt);//die('qqq');
        $crypted = hash('sha1', json_encode($join).$salt);
        // Debug::dump($crypted);die('qqq');
        $key = 'route-cache-'.$route.'-'.$crypted;
        $key = str_replace('/', '-', $key);
        $key = str_replace('$', '+', $key);
        $key = str_replace('.', '_', $key);
        // Debug::dump($key);die('hhh');
        $routes = $sm->get('config')['router']['routes'];
        // Debug::dump($cache->hasItem($key));die('qqq');
        if ($cache->hasItem($key) && isset($routes[$route]) && isset($routes[$route]['cached']) && $routes[$route]['cached']) {
            // Handle response
            $content = $cache->getItem($key);
            // Debug::dump($content);die('ssss');
            $response = $e->getResponse();
            $response->setContent($content);

            return $response;
        }
    }

    public function shareDispatchAAC(MvcEvent $e) {
        $application = $e->getApplication();
        $servicesManager = $application->getServiceManager();
        $config = $servicesManager->get('Config');
        // Debug::dump($config);die();
        $request = $e->getRequest();
        $pPath = $request->getUri()->getPath();
        $foundXSS = false;
        $antiXss = new AntiXSS();
        $harmless_string = $antiXss->xss_clean($pPath);
        $foundXSS = $antiXss->isXssFound();

        $pQuery = $request->getQuery()->toArray();
        if (!$foundXSS) {
            foreach ($pQuery as $key => $value) {
                $antiXss = new AntiXSS();
                $harmless_string = $antiXss->xss_clean($key);
                $foundXSS = $antiXss->isXssFound();
                if ($foundXSS) {
                    break;
                }
                $antiXss = new AntiXSS();
                $harmless_string = $antiXss->xss_clean($value);
                $foundXSS = $antiXss->isXssFound();
                if ($foundXSS) {
                    break;
                }
            }
        }
        
        $pPost = $request->getPost()->toArray();
        if (!$foundXSS) {
            foreach ($pPost as $key => $value) {
                $antiXss = new AntiXSS();
                $harmless_string = $antiXss->xss_clean($key);
                $foundXSS = $antiXss->isXssFound();
                if ($foundXSS) {
                    break;
                }
                $antiXss = new AntiXSS();
                $harmless_string = $antiXss->xss_clean($value);
                $foundXSS = $antiXss->isXssFound();
                if ($foundXSS) {
                    break;
                }
            }
        }

        $pHeaders = $request->getHeaders()->toArray();
        $pServer = $request->getServer()->toArray();
        $pEnv = $request->getEnv()->toArray();
        // !d($request);
        // !d($pPath,$pQuery,$pPost,$pHeaders,$pServer,$pEnv,$foundXSS);die();
        if($foundXSS){
            $auth = $servicesManager->get(AuthenticationService::class);
            $user = [];
            if($auth->hasIdentity()){ 
                $session = $servicesManager->get(\Laminas\Session\SessionManager::class);
                $tmp = $auth->getIdentity();
                $sess_id = null;
                if($session->sessionExists()){
                    $sess_id = $session->getId();
                }
                $user = [
                    'id'=>$tmp['id'],
                    'username'=>$tmp['username'],
                    'full_name'=>$tmp['full_name'],
                    'session_id'=>$sess_id
                ];
            }
            // $auth->clearIdentity();
            $response = $e->getResponse();
            $response->setStatusCode(HTTP_UNAUTHORIZED);
            $response->setContent("UNAUTHORIZED");
            // die(APP_PATH);
            $data = [
                'path'=>$pPath,
                'query'=>$pQuery,
                'post'=>$pPost,
                'header'=>$pHeaders,
                'server'=>$pServer,
                'env'=>$pEnv,
                'user'=>$user,
            ];
            // $format = '%timestamp%||%priorityName%||%priority%||%message%' . PHP_EOL;
            $format = '%timestamp%|%priorityName%|%message%';
            $formatter = new \Laminas\Log\Formatter\Simple($format);
            $writer = new \Laminas\Log\Writer\Stream(APP_PATH.'/log/'.date('Ymd').'-xss.log');
            $writer->setFormatter($formatter);
            $logger = new \Laminas\Log\Logger();
            $logger->addWriter($writer);

            $logger->alert(json_encode($data));
            $logger = null;
    
            return $response;
        }else if ($request instanceof HttpRequest) {
            $controller = $e->getTarget();
            $query = $request->getQuery('module', '');
            $uri = $request->getUri();
            $matchedRoute = $e->getRouteMatch();
            $viewModel = $e->getViewModel();
            // Debug::dump($request);
            // Debug::dump($matchedRoute);die();
            // Debug::dump($viewModel);die();
            $controllerName = $matchedRoute->getParam('controller', null);
            $actionName = $matchedRoute->getParam('action', null);
            // Debug::dump($controllerName);
            // Debug::dump($actionName);die();
            // Convert dash-style action name to camel-case.
            if ($actionName!=null) {
                  $actionName = str_replace('-', '', lcfirst(ucwords($actionName, '-')));
            }
            if ($query === '') {
                $module = substr($controllerName, 0, strpos($controllerName, '\\'));
            } else {
                $module = $query;
            }
            $isAuthPage = $controllerName === "App\Controller\AuthController";
            $isIndexPage = $controllerName === "App\Controller\IndexController";

            $auth = $servicesManager->get(AuthenticationService::class);
            // !d($isIndexPage,$isAuthPage,$auth->hasIdentity());
            if(!$isIndexPage && !$isAuthPage && !$auth->hasIdentity()){
                if($request->isXmlHttpRequest()){
                    $response = $e->getResponse();
                    $response->setStatusCode(HTTP_UNAUTHORIZED);
                    $response->setContent("UNAUTHORIZED");
            
                    return $response;
                }else{
                    // Make the URL relative (remove scheme, user info, host name and port)
                    // to avoid redirecting to other domain by a malicious user.
                    $uri->setScheme(null)->setHost(null)->setPort(null)->setUserInfo(null);
                    $redirectUrl = $uri->toString();

                    // Redirect the user to the "Login" page.
                    return $controller->redirect()->toRoute('app/auth', ['action' => 'login'],
                        ['query' => ['redirectUrl' => $redirectUrl, 'module' => $module]]);
                }
            }else if($isIndexPage || $isAuthPage){
                $this->loadCacheView($e);
            }else{
                $authAdapter = $auth->getAdapter();
                $identity = $auth->getIdentity();
                // !d($auth);
                // !d($identity,$controllerName,$actionName);die();
                // !d($authAdapter->authAccess($auth,$controllerName,$actionName));die();
                if (!$authAdapter->authAccess($auth,$matchedRoute,$controllerName,$actionName,$viewModel)) {
                  // Debug::dump($module);die('a');
                //   !d($request->getHeader('referer')->uri()->getPath());die();
                  $islogin =(($auth->getIdentity()!=null) || ($auth->hasIdentity()));
                    // !d($islogin,$request->getHeader('referer'));die();
                  if ($islogin && $request->getHeader('referer')!==false && $request->getHeader('referer')->uri()->getPath()!==''
                  && $request->getHeader('referer')->uri()->getPath()!==null) {
                    // !d($request->getHeader('referer'));die('aaa');
                    return $controller->redirect()->toRoute('app/auth', ['action' => 'noauth'],
                      ['query' => ['backUrl' => $request->getHeader('referer')->uri()->getPath(), 'module' => $module]]);
                  } else if ($islogin) {
                    // Debug::dump($auth->getIdentity());die('qqq');
                    $identity = $auth->getIdentity();
                    // !d($identity);die();
                    $redirectUrl = $identity['redirect_url'] ?? "/";
                    if ($identity['redirect_url'] === null || $identity['redirect_url'] === '/'
                    || $identity['redirect_url'] === ''){
                        $redirectRoute = $identity['redirect_route'] ?? "";
                        $redirect = $controller->url()->fromRoute("app");
                        // Debug::dump($redirectRoute);die('qqq');
                        if ($identity['redirect_url'] !== null || $identity['redirect_url'] !== ''){
                            $redirectParam = $identity['redirect_param'] ?? "{}";
                            $redirectQuery = $identity['redirect_query'] ?? "{}";
                            try {
                                $redirect = $controller->url()->fromRoute($redirectRoute, $redirectParam, ['query' => $redirectQuery]);
                            }catch(Exception $e){
                                $redirect = $controller->url()->fromRoute("app");
                            }catch(\Exception $e){
                                $redirect = $controller->url()->fromRoute("app");
                            }
                            // Debug::dump($redirect);die('qqq');

                            // return $controller->redirect()->toRoute($redirectRoute, $redirectParam,
                            //   ['query' => ['backUrl' => $redirect, 'module' => $module]]);
                        }
                    }
                    // Debug::dump($redirect);die('qqq');
                    return $controller->redirect()->toRoute('app/auth', ['action' => 'noauth'],
                      ['query' => ['backUrl' => $redirect, 'module' => $module]]);
                  } else {
                    // Remember the URL of the page the user tried to access. We will
                    // redirect the user to that URL after successful login.
                    $uri = $event->getApplication()->getRequest()->getUri();
                    // Make the URL relative (remove scheme, user info, host name and port)
                    // to avoid redirecting to other domain by a malicious user.
                    $uri->setScheme(null)->setHost(null)->setPort(null)->setUserInfo(null);
                    $redirectUrl = $uri->toString();
          
                    // Redirect the user to the "Login" page.
                    return $controller->redirect()->toRoute('front/auth', ['action' => 'login'],
                      ['query' => ['redirectUrl' => $redirectUrl, 'module' => $module]]);
                  }
                } else {
                    // die('aaa');
                    // Debug::dump($viewModel);die('xxx');
                    $this->loadCacheView($e);
                }
            }
        }
    }

    public function setLayoutTitle(MvcEvent $event) {
        // Getting the view helper manager from the application service manager
        $viewHelperManager = $event->getApplication()->getServiceManager()->get('ViewHelperManager');
        // Debug::dump($viewHelperManager);die();
        $routeMatch = $event->getRouteMatch();
        $menuTitle = $routeMatch->getParam('title', null);
        $viewModel = $event->getViewModel();
        $viewModel->setVariable('menuTitle', $menuTitle);

        // Getting the headTitle helper from the view helper manager
        $headTitleHelper = $viewHelperManager->get('headTitle');
        $headTitleHelper->setSeparator(' | ');
        $headTitleHelper->append(_APP_NAME_);
        $controllerName = $routeMatch->getParam('controller', null);
        $moduleName = substr($controllerName, 0, strpos($controllerName, '\\'));
        $headTitleHelper->append(($menuTitle!=null)?$menuTitle:$moduleName);
    }

    public function onRender(MvcEvent $event) {
        // $this->registerRenderStrategy($event);
        // !d($event->getRequest() instanceof HttpRequest);die();
        $request = $event->getRequest();
        if ($request instanceof HttpRequest) {
            $routeMatch = $event->getRouteMatch();
            // !d($event->getRequest());
            $viewModel = $event->getViewModel();
            $controllerName = null;
            if ($routeMatch != null) {
                $controllerName = $routeMatch->getParam('controller', null);
            }
            // !d($routeMatch,$controllerName);die();
            if ($routeMatch != null && $controllerName!=null) {
                // !d($viewModel);
                if (get_class($viewModel) === "Laminas\View\Model\ViewModel") {
                    // !d($controllerName);die();
                    // !d($routeMatch->getMatchedRouteName(),$routeMatch->getParams(),$request->getQuery());
                    $viewModel->setVariable('route_name', $routeMatch->getMatchedRouteName());
                    $viewModel->setVariable('route_param', $routeMatch->getParams());
                    $viewModel->setVariable('route_query', $request->getQuery()->toArray());
                    $moduleName = substr($controllerName, 0, strpos($controllerName, '\\'));
                    $this->setLayoutTitle($event);
                    $actionName = $routeMatch->getParam('action', null);
                    // !d($moduleName,$controllerName,$actionName);die();
                    // $menuTitle = $routeMatch->getParam('title', null);
                    // !d($menuTitle);
                    $viewModel->setVariable('module', $moduleName);
                    $viewModel->setVariable('controller', $controllerName);
                    $viewModel->setVariable('action', $actionName);
                    // $viewModel->setVariable('menuTitle', $menuTitle);
                    $application = $event->getApplication();
                    // $servicesManager = $application->getServiceManager();
                    // $auth = $servicesManager->get(AuthenticationService::class);
                    // $identity = $auth->getIdentity();
                    $layout = $viewModel->getTemplate();
                    $layout = explode("/", $layout);
                    // !d($viewModel,$layout);die();

                    if (count($layout) > 0) {
                        $module = strtolower($moduleName);
                        $layout = $layout[count($layout) - 1];
                        // !d($module,$layout,$viewModel->getChildren());die();
                        foreach($viewModel->getChildren() as $key => $value) {
                            if ($value->captureTo() === "content") {
                                $template_ori = $value->getTemplate();
                                $template_tmp = str_replace($module.'/', $module.'/'.$layout.'/', $template_ori);
                                $filepath = APP_PATH.'/module/'.$moduleName.'/view/'.$template_tmp.'.phtml';
                                $layout = $layout.'-layout';
                                // !d($layout);
                                $layoutpath = APP_PATH.'/view/layout/'.$layout.'.phtml';
                                // !d($template_ori,$template_tmp,$filepath);
                                // !d(_DEFAULT_THEME_,$filepath,file_exists($filepath));die();
                                if(file_exists($layoutpath) && file_exists($filepath)){
                                    $value->setTemplate($template_tmp);
                                    $viewModel->setTemplate($layout);
                                }else{
                                    $template_tmp = str_replace($module.'/', $module.'/'._DEFAULT_THEME_.'/', $template_ori);
                                    $filepath = APP_PATH.'/module/'.$moduleName.'/view/'.$template_tmp.'.phtml';
                                    $layout = _DEFAULT_THEME_.'-layout';
                                    $layoutpath = APP_PATH.'/view/layout/'.$layout.'.phtml';
                                    // !d($filepath,file_exists($filepath),$layoutpath,file_exists($layoutpath));
                                    if(file_exists($layoutpath) && file_exists($filepath)){
                                        $value->setTemplate($template_tmp);
                                        $viewModel->setTemplate($layout);
                                    }
                                }
                                break;
                            }
                        }
                        // !d($viewModel,$layout);die();
                        $viewModel->setVariable('layout', $layout);
                    }
                }
            }
            // die('qqq');
            $response = $event->getResponse();
            $response->getHeaders()->addHeaderLine('X-Frame-Options', 'sameorigin');
            $response->getHeaders()->addHeaderLine('X-XSS-Protection', '1');
            $response->getHeaders()->addHeaderLine('X-Content-Type-Options', 'nosniff');
            // Debug::dump($response);die();
            // !d($request->getHeaders(),$response->getHeaders());die();
            if ($response->getStatusCode() != 200) {
                // $viewModel = $event->getViewModel();
                $viewModel->setTemplate('layout/blank-layout');
            }
            // die('aaa');
        }elseif($event->getRequest() instanceof ConsoleRequest) {

        }
    }

    public function disconnectDB($db = array(), $serviceLocator) {
        // Debug::dump($db);die();
        if (count($db) > 0) {
            foreach($db as $key => $value) {
                $dbadapter = $serviceLocator->get($key);
                // Debug::dump($dbadapter);die();
                if ($dbadapter->getDriver()->getConnection()->isConnected()) {
                $dbadapter->getDriver()->getConnection()->disconnect();
                }
            }
        }
    }

    public function setCacheView(MvcEvent $e) {
        $app = $e->getTarget();
        $route = $e->getRouteMatch()->getMatchedRouteName();
        // Debug::dump($route);//die();
        // Debug::dump($e->getRouteMatch());
        $sm = $app->getServiceManager();
        // Debug::dump($sm);die();
        $routes = $sm->get('config')['router']['routes'];
        // Debug::dump($routes);//die();
        $cache = $sm->get('view-file');
        $post = $e->getRequest()->getPost()->toArray();
        $query = $e->getRequest()->getQuery()->toArray();
        // Debug::dump($post);
        // Debug::dump($query);//die('qqq');
        $join = array_merge($post, $query);
        // Debug::dump($join);//die('qqq');
        // Debug::dump(json_encode($join));//die('qqq');
        // $blockCipher = new BlockCipher(new Openssl(['algo' => 'aes']));
        $salt = "cache-view-".$route;
        // Debug::dump($salt);die('qqq');
        // for($i=strlen($salt);$i<22;$i++){
        //   $salt .= $i;
        //   if(strlen($salt)>=22)break;
        // }
        // Debug::dump($salt);die('qqq');
        // $blockCipher->setKey($salt);
        // $crypted = $blockCipher->encrypt(json_encode($join));
        $crypted = hash('sha1', json_encode($join).$salt);
        // Debug::dump($crypted);die('qqq');
        $key = 'route-cache-'.$route.'-'.$crypted;
        $key = str_replace('/', '-', $key);
        $key = str_replace('$', '+', $key);
        $key = str_replace('.', '_', $key);
        // Debug::dump($key);die('qqq');
        if (!$cache->hasItem($key) && isset($routes[$route]) && isset($routes[$route]['cached']) && $routes[$route]['cached']) {
            // Debug::dump($routes[$route]);die();
            $app = $e->getTarget();
            // $viewModel = $e->getViewModel();
            // Debug::dump($viewModel);die('qqq');
            // Debug::dump($e->getRequest());die('qqq');
            // $ViewRenderer = $sm->get('ViewRenderer');
            // $html = $ViewRenderer->render($viewModel);
            // Debug::dump($html);
            $response = $app->getResponse();
            $html = $response->getContent();
            // var_dump($html);die('qq');
            $cache->addItem($key, $html);
        }
    }

    public function onFinish(MvcEvent $event) {
        // !d($event->getRequest() instanceof HttpRequest);die();
        if ($event->getRequest() instanceof HttpRequest) {
            $routeMatch = $event->getRouteMatch();
            $app = $event->getTarget();
            $response = $app->getResponse();
            // !d($routeMatch);die();
            // !d($response->getHeaders());die();
            if ($routeMatch != null) {
                // Debug::dump($app->getResponse());
                $serviceManager = $app->getServiceManager();
                // Debug::dump($serviceManager);die();
                $config = $serviceManager->get('config');
                // !d($config);die();
                $this->disconnectDB($config['db']['adapters'], $serviceManager);
                $db = $serviceManager->build(\Laminas\Db\Adapter\Adapter::class);
                // Debug::dump($db);die('www');
                if ($db->getDriver()->getConnection()->isConnected()) {
                    $db->getDriver()->getConnection()->disconnect();
                }

                $render = $event->getRequest()->getQuery('render', '');

                if ($render === "text") {
                    // Debug::dump($response->getHeaders());die();
                    $route = $routeMatch->getMatchedRouteName();
                    $route = str_replace('/', '-', $route);
                    $response->getHeaders()->addHeaderLine('Content-disposition', 'inline; filename="'.$route.'.txt"');
                    $response->getHeaders()->addHeaderLine('Content-Type', 'plain/text; charset=utf-8');
                    $html = $response->getContent();
                    // var_dump($html);die('fff');
                    $response->setContent($html);
                    // Debug::dump($response->getContent());die();
                    $response->send();
                }
                // Debug::dump($routeMatch);die();
                $this->setCacheView($event);
            }
        }
    }
    
    public function getConfig() : array
    {
        return include __DIR__ . '/../config/module.config.php';
    }
}
