<?php
namespace Core\Adapter\Authentication;

use Laminas\Authentication\Adapter\AdapterInterface;
use Laminas\Authentication\Result;
use Laminas\Crypt\Password\Bcrypt;
use Laminas\Db\Adapter\Adapter as DbAdapter;
use InvalidArgumentException;
use RuntimeException;
use Laminas\Db\Adapter\Driver\ResultInterface;
use Laminas\Db\ResultSet\ResultSet;
use Laminas\Db\Sql\Sql;
use Zend\Debug\Debug;

class AuthenticationAdapter implements AdapterInterface
{
    private $db;
    private $redis;
    private $username;
    private $password;
    private $container = [];
    private $config = [];
    /**
     * Sets username and password for authentication
     *
     * @return void
     */
    public function __construct($container, $config,$username = null, $password = null)
    {
        $this->container = $container;
        $this->config = $config;
        if(isset($this->config['db'])){
            $this->db = new DbAdapter($this->config['db']['adapters']['db-sys']);
        }
        // $this->db = $container->get('db-dual');
        // if(isset($this->config['redis'])){
        //     $this->redis = new DbAdapter($this->config['db']);
        // }
        $this->username = $username;
        $this->password = $password;
    }

    public function setUser($username = null, $password = null)
    {
        $this->username = $username;
        $this->password = $password;
    }

    public function setUsername($username = null)
    {
        $this->username = $username;
    }

    public function setPassword($password = null)
    {
        $this->password = $password;
    }

    private function getUser(){
        if($this->db===null)return null;
        if (!$this->db->getDriver()->getConnection()->isConnected()) {
          $this->db->getDriver()->getConnection()->connect();
        }
        $sql       = new Sql($this->db);
        $select    = $sql->select('_user');
        $select->where(['username = ?' => $this->username]);
        // Debug::dump($selectString = $sql->getSqlStringForSqlObject($select));die();
  
        $statement = $sql->prepareStatementForSqlObject($select);
        // Debug::dump($statement);die();
        $result    = $statement->execute();
        // Debug::dump($result);//die();
        if ($this->db->getDriver()->getConnection()->isConnected()) {
            $this->db->getDriver()->getConnection()->disconnect();
        }
        if (! $result instanceof ResultInterface || ! $result->isQueryResult()) {
            // Debug::dump($resultSet);die('fff');
            return null;
        }else{
            $resultSet = new ResultSet();
            $resultSet->initialize($result);
            return $resultSet->toArray();
        }
    }

    private function getPermission($id){
        if($this->db===null)return [];
        if (!$this->db->getDriver()->getConnection()->isConnected()) {
          $this->db->getDriver()->getConnection()->connect();
        }
        $sql = "CALL lam_sys.get_userpermission_byuid(:uid)";
        // die($sql);
        $statement = $this->db->createStatement($sql,["uid"=>$id]);
        // Debug::dump($statement);die();
        $result    = $statement->execute();
        // Debug::dump($result);die();
        // Debug::dump($result);//die();
        if ($this->db->getDriver()->getConnection()->isConnected()) {
            $this->db->getDriver()->getConnection()->disconnect();
        }
        if (! $result instanceof ResultInterface || ! $result->isQueryResult()) {
            // Debug::dump($resultSet);die('fff');
            return [];
        }else{
            $resultSet = new ResultSet();
            $resultSet->initialize($result);
            // Debug::dump($resultSet->toArray());die();
            return $resultSet->toArray();
        }
    }

    private function getRoles($id){
        if($this->db===null)return [];
        if (!$this->db->getDriver()->getConnection()->isConnected()) {
          $this->db->getDriver()->getConnection()->connect();
        }
        $sql = "CALL lam_sys.get_userrole_byuid(:uid)";
        // die($sql);
        $statement = $this->db->createStatement($sql,["uid"=>$id]);
        // Debug::dump($statement);die();
        $result    = $statement->execute();
        // Debug::dump($result);die();
        // Debug::dump($result);//die();
        if ($this->db->getDriver()->getConnection()->isConnected()) {
            $this->db->getDriver()->getConnection()->disconnect();
        }
        if (! $result instanceof ResultInterface || ! $result->isQueryResult()) {
            // Debug::dump($resultSet);die('fff');
            return [];
        }else{
            $resultSet = new ResultSet();
            $resultSet->initialize($result);
            // Debug::dump($resultSet->toArray());die();
            return $resultSet->toArray();
        }
    }

    private function getUbis($id){
        if($this->db===null)return [];
        if (!$this->db->getDriver()->getConnection()->isConnected()) {
          $this->db->getDriver()->getConnection()->connect();
        }
        $sql = "CALL lam_sys.get_userubis_byuid(:uid)";
        // die($sql);
        $statement = $this->db->createStatement($sql,["uid"=>$id]);
        // Debug::dump($statement);die();
        $result    = $statement->execute();
        // Debug::dump($result);die();
        // Debug::dump($result);//die();
        if ($this->db->getDriver()->getConnection()->isConnected()) {
            $this->db->getDriver()->getConnection()->disconnect();
        }
        if (! $result instanceof ResultInterface || ! $result->isQueryResult()) {
            // Debug::dump($resultSet);die('fff');
            return [];
        }else{
            $resultSet = new ResultSet();
            $resultSet->initialize($result);
            // Debug::dump($resultSet->toArray());die();
            return $resultSet->toArray();
        }
    }

    public function authenticate(){
        $user = $this->getUser();
        if (empty($user) || is_null($user)) {
            return new Result(
                Result::FAILURE_IDENTITY_NOT_FOUND,
                null,
                ['Invalid credentials.']);
        }

        $user = $user[0];
        // Debug::dump($user);//die('ddd');
        if ($user['status']!=1) {
            return new Result(
                Result::FAILURE,
                null,
                ['User not active.']);
        }

        $bcrypt = new Bcrypt();
        $passwordHash = $user['password'];
        $passwordHash2 = $bcrypt->create($this->password);
        // Debug::dump($passwordHash);
        // Debug::dump($passwordHash2);//die('ddd');

        if ($bcrypt->verify($this->password, $passwordHash)) {
            $roles = $this->getRoles($user['id']);
            $ubis = $this->getUbis($user['id']);
            $permission = $this->getPermission($user['id']);
            // Debug::dump($permission);//die('ddd');
            $this->restructureSession($user,$roles,$ubis,$permission);
            // Debug::dump($user);die('ddd');
            return new Result(
                    Result::SUCCESS,
                    $user,
                    ['Authenticated successfully.']);
        }else{
            return new Result(
                Result::FAILURE_CREDENTIAL_INVALID,
                null,
                ['Invalid credentials.']);
        }
    }

    private function restructureSession(&$user,$roles,$unit,$permission){
        //   Debug::dump($user);//die('ddd');
        //   Debug::dump($roles);//die('ddd');
        // Debug::dump($permission);//die('ddd');
    
        $role = [];
        $ubis = [];
        $accessModule = [];
        $accessRoute = [];
        $user['mainrole_data'] = [];
        foreach ($roles as $key => $value) {
            $is_main = 0;
            if ($value['code']===$user['main_role']) {
                $is_main = 1;
                $user['mainrole_data'] = $value;
            }
            $value['is_main'] =$is_main;
            $role[$value['code']] = $value;
        }
        
        // usort($role, function($a, $b) {
        //     $a['is_main'] = ($a['is_main'])??0;
        //     $b['is_main'] = ($b['is_main'])??0;
        //     return $a['is_main'] <=> $b['is_main'];
        // });
        $mainrole = $user['main_role'] ?? 'GUEST';
        if(!isset($role[$mainrole]))$user['main_role'] = 'GUEST';
        // Debug::dump($role);die('ddd');

        $user['mainubis_data'] = [];
        foreach ($unit as $key => $value) {
            $is_main = 0;
            if ($value['code']===$user['main_ubis']) {
                $is_main = 1;
                $user['mainubis_data'] = $value;
            }
            $value['is_main'] =$is_main;
            $ubis[$value['code']] = $value;
        }
        
        // usort($ubis, function($a, $b) {
        //     $a['is_main'] = ($a['is_main'])??0;
        //     $b['is_main'] = ($b['is_main'])??0;
        //     return $a['is_main'] <=> $b['is_main'];
        // });
        $mainubis = $user['main_ubis'] ?? 'GUEST';
        if(!isset($ubis[$mainubis]))$user['main_ubis'] = 'GUEST';

        foreach ($permission as $key => $value) {
            // !d($key,$value);die();
            $layout = $value['layout']===null ? 'blank' : $value['layout'];
            // if(!isset($accessLayout[$layout])){
            //     $accessLayout[$layout] = [];
            // }

            $module = $value['module_name']===null || $value['module_name']==="0" ? '*' : $value['module_name'];
            if(!isset($accessModule[$module])){
                $accessModule[$module] = [];
                $accessLayout[$module] = [];
            }

            $controller = $value['control_name']===null || $value['control_name']==="0" ? '*' : $value['control_name'];
            if(!isset($accessModule[$module][$controller])){
                $accessModule[$module][$controller] = [];
                $accessLayout[$module][$controller] = [];
            }

            $action = $value['act_name']===null || $value['act_name']==="0" ? '*' : $value['act_name'];
            if(!in_array($action,$accessModule[$module][$controller])){
                $accessModule[$module][$controller][] = $action;
                $accessLayout[$module][$controller][$action] = $layout;
            }

            $route = $value['route'];
            if($route!==null && $route!=='' && !in_array($route,$accessRoute)){
                $accessRoute[] = $route;
                $accessLayout[$route] = $layout;
            }
        }
        // !d($role,$accessLayout,$accessRoute);die('ddd');
        $user['roles'] = $role;
        $user['ubis'] = $ubis;
        $user['accessRoute'] = $accessRoute;
        $user['accessModule'] = $accessModule;
        $user['accessLayout'] = $accessLayout;
        //   Debug::dump($user);die('ddd');
    }

    public function removeSessionData($identity){
        $init_container = $this->container->get("container_init");
        // !d($identity,$init_container);die();
        if($this->db===null)return null;
        if (!$this->db->getDriver()->getConnection()->isConnected()) {
            $this->db->getDriver()->getConnection()->connect();
        }
        try{
        $sql       = new Sql($this->db);
        $select    = $sql->delete(_SESSION_TABLE_);
        $select->where([
            '`id` = ?' => $init_container->sess_id,
            '`uag` = ?' => $init_container->httpUserAgent,
            '`ip` = ?' => $init_container->remoteAddr,
            '`uid` = ?' => $identity['id']]);
        // Debug::dump($selectString = $sql->getSqlStringForSqlObject($select));//die();
    
        // Debug::dump($this->db->getDriver()->getConnection()->isConnected());//die();
        // $statement = $sql->prepareStatementForSqlObject($select);
        $statement = $this->db->createStatement($selectString);
        // Debug::dump($statement);//die();
        $result    = $statement->execute();
        // $resultSet = new ResultSet();
        // $resultSet->initialize($result);
        // Debug::dump($result);//die();
        // Debug::dump($resultSet->toArray());
        }catch(Exception $e){
            // Debug::dump($e->getMessage());
        }
        // die();
        if ($this->db->getDriver()->getConnection()->isConnected()) {
            $this->db->getDriver()->getConnection()->disconnect();
        }
        if (! $result instanceof ResultInterface || $result->getAffectedRows()<1) {
            return [
                "ret"=>false,
                "affected_row"=>0
            ];
        }else{
            return [
                "ret"=>true,
                "affected_row"=>$result->getAffectedRows()
            ];
        } 
    }

    public function authAccess($authService, $matchedRoute, $controller, $actionName,&$viewModel)
    {
        // Debug::dump($this->config);
        // Debug::dump($controllerName);
        // Debug::dump($actionName);
        // Debug::dump($viewModel);
        // die('fff');
        if(isset($this->config['controllers']['aliases'][$controller]))
          $controller = $this->config['controllers']['aliases'][$controller];
        // Debug::dump($controllerName);//die();
        // $tmp = explode("\\", $controllerName);
        // Debug::dump($tmp);//die();
        $tmp = explode("\\", $controller);
        if(count($tmp)!=3)return false;
        $controllerName = str_replace("Controller", "", $tmp[2]);
        $moduleName = $tmp[0];
        $identity = $authService->getIdentity();

        // Debug::dump($identity);
        // Debug::dump($moduleName);
        // Debug::dump($controllerName);
        // Debug::dump($actionName);
        // Debug::dump($viewModel);
        // Debug::dump($GLOBALS['PUBLIC_CONTROLLER']);
        // Debug::dump(isset($GLOBALS['PUBLIC_CONTROLLER'][$controllerName]));
        // Debug::dump(in_array('*',$GLOBALS['PUBLIC_CONTROLLER'][$controllerName]));
        // die();
        if(isset($GLOBALS['PUBLIC_CONTROLLER'][$controller]) && 
        in_array($actionName,$GLOBALS['PUBLIC_CONTROLLER'][$controller])){
            if (isset($identity['accessLayout'][$moduleName][$controllerName][$actionName])) {
                $viewModel->setTemplate('layout/'.$identity['accessLayout'][$moduleName][$moduleName][$controllerName][$actionName]);
            }else if(isset($identity['accessLayout'][$moduleName][$controllerName]['*'])){
                $viewModel->setTemplate('layout/'.$identity['accessLayout'][$moduleName][$controllerName]['*']);
            }else if(isset($identity['accessLayout'][$moduleName]['*']['*'])){
                $viewModel->setTemplate('layout/'.$identity['accessLayout'][$moduleName]['*']['*']);
            }else if(isset($identity['accessLayout']['*']['*']['*'])){
                $viewModel->setTemplate('layout/'.$identity['accessLayout']['*']['*']['*']);
            }else if(isset($identity['accessLayout'][$moduleName]['*'])){
                $viewModel->setTemplate('layout/'.$identity['accessLayout'][$moduleName]['*']);
            }else if(isset($identity['accessLayout']['*']['*'])){
                $viewModel->setTemplate('layout/'.$identity['accessLayout']['*']['*']);
            }else if(isset($identity['accessLayout']['*'])){
                $viewModel->setTemplate('layout/'.$identity['accessLayout']['*']);
            }
            return true;
        }else if(isset($GLOBALS['PUBLIC_CONTROLLER'][$controller]) && 
        in_array('*',$GLOBALS['PUBLIC_CONTROLLER'][$controller])){
            if (isset($identity['accessLayout'][$moduleName][$controllerName][$actionName])) {
                $viewModel->setTemplate('layout/'.$identity['accessLayout'][$moduleName][$moduleName][$controllerName][$actionName]);
            }else if(isset($identity['accessLayout'][$moduleName][$controllerName]['*'])){
                $viewModel->setTemplate('layout/'.$identity['accessLayout'][$moduleName][$controllerName]['*']);
            }else if(isset($identity['accessLayout'][$moduleName]['*']['*'])){
                $viewModel->setTemplate('layout/'.$identity['accessLayout'][$moduleName]['*']['*']);
            }else if(isset($identity['accessLayout']['*']['*']['*'])){
                $viewModel->setTemplate('layout/'.$identity['accessLayout']['*']['*']['*']);
            }else if(isset($identity['accessLayout'][$moduleName]['*'])){
                $viewModel->setTemplate('layout/'.$identity['accessLayout'][$moduleName]['*']);
            }else if(isset($identity['accessLayout']['*']['*'])){
                $viewModel->setTemplate('layout/'.$identity['accessLayout']['*']['*']);
            }else if(isset($identity['accessLayout']['*'])){
                $viewModel->setTemplate('layout/'.$identity['accessLayout']['*']);
            }
            return true;
        }
        // $identity = $this->authService->getIdentity();
        // Debug::dump($identity['permission']);die();
        // Debug::dump($this->authService->hasIdentity());die();
        // Determine mode - 'restrictive' (default) or 'permissive'. In restrictive
        // mode all controller actions must be explicitly listed under the 'access_filter'
        // config key, and access is denied to any not listed action for unauthorized users.
        // In permissive mode, if an action is not listed under the 'access_filter' key,
        // access to it is permitted to anyone (even for not logged in users.
        // Restrictive mode is more secure and recommended to use.

        // Debug::dump($this->config["access_filter"]);die();
        $accessfilter = $this->config["access_filter"] ?? [];
        // Debug::dump($accessfilter);die();
        $mode = $accessfilter['options']['mode']??'restrictive';
        if ($mode!='restrictive' && $mode!='permissive')
            throw new \Exception('Invalid access filter mode (expected either restrictive or permissive mode');
        // Debug::dump($controllerName);//die();
        // Debug::dump($accessfilter);die();
        if (isset($accessfilter['controllers'][$controller])) {
            $items = $accessfilter['controllers'][$controller];
            // Debug::dump($items);die();
            foreach ($items as $item) {
                $actionList = $item['actions'];
                $allow = $item['allow'];
                // Debug::dump($actionList);//die();
                // Debug::dump($allow);die();
                if (is_array($actionList) && in_array($actionName, $actionList) ||
                    $actionList==='*') {
                    if ($allow==='*')
                        return true; // Anyone is allowed to see the page.
                    else if ($allow==='%' && $authService->hasIdentity())
                        return true;
                    else if ($allow==='@' && $authService->hasIdentity()) {
                        // die('qqq');
                        return $this->checkPermission($authService,$matchedRoute, $moduleName,$controllerName, $actionName,$viewModel); // Only authenticated user is allowed to see the page.
                    } else {
                        return false; // Access denied.
                    }
                }
            }
        }
        return $this->checkPermission($authService,$matchedRoute, $moduleName, $controllerName, $actionName,$viewModel);



        // Debug::dump($mode);die();
        // In restrictive mode, we forbid access for unauthorized users to any
        // action not listed under 'access_filter' key (for security reasons).
        // if ($mode=='restrictive' && !$this->authService->hasIdentity())
        return false;

        // Permit access to this page.
        // return true;
    }

    private function checkPermission($authService,$matchedRoute,$moduleName,$controllerName, $actionName,&$viewModel){
        if ($authService->getIdentity()===null)return false;

        $identity = $authService->getIdentity();
        // Debug::dump($matchedRoute);
        // Debug::dump($moduleName);
        // Debug::dump($controllerName);
        // Debug::dump($actionName);
        // Debug::dump($identity);die();
        $accessRoute = $identity['accessRoute'];
        $accessModule = $identity['accessModule'];
        // Debug::dump($accessModule);die();
        // !d($tmp,$matchedRoute->getMatchedRouteName(),$controllerName,$actionName,$identity);die();
        if(in_array($matchedRoute->getMatchedRouteName(),$accessRoute)){
            if(isset($identity['accessLayout'][$matchedRoute->getMatchedRouteName()]))$viewModel->setTemplate('layout/'.$identity['accessLayout'][$matchedRoute->getMatchedRouteName()]);
            return true;
        }else if(isset($accessModule[$moduleName]) && isset($accessModule[$moduleName][$controllerName]) && 
        in_array($actionName,$accessModule[$moduleName][$controllerName])){
            if (isset($identity['accessLayout'][$moduleName][$controllerName][$actionName])) {
                $viewModel->setTemplate('layout/'.$identity['accessLayout'][$moduleName][$moduleName][$controllerName][$actionName]);
            }else if(isset($identity['accessLayout'][$moduleName][$controllerName]['*'])){
                $viewModel->setTemplate('layout/'.$identity['accessLayout'][$moduleName][$controllerName]['*']);
            }else if(isset($identity['accessLayout'][$moduleName]['*']['*'])){
                $viewModel->setTemplate('layout/'.$identity['accessLayout'][$moduleName]['*']['*']);
            }else if(isset($identity['accessLayout']['*']['*']['*'])){
                $viewModel->setTemplate('layout/'.$identity['accessLayout']['*']['*']['*']);
            }else if(isset($identity['accessLayout'][$moduleName]['*'])){
                $viewModel->setTemplate('layout/'.$identity['accessLayout'][$moduleName]['*']);
            }else if(isset($identity['accessLayout']['*']['*'])){
                $viewModel->setTemplate('layout/'.$identity['accessLayout']['*']['*']);
            }else if(isset($identity['accessLayout']['*'])){
                $viewModel->setTemplate('layout/'.$identity['accessLayout']['*']);
            }
            return true;
        }else if(isset($accessModule[$moduleName]) && isset($accessModule[$moduleName][$controllerName]) && 
        in_array('*',$accessModule[$moduleName][$controllerName])){
            if(isset($identity['accessLayout'][$moduleName][$controllerName]['*'])){
                $viewModel->setTemplate('layout/'.$identity['accessLayout'][$moduleName][$controllerName]['*']);
            }else if(isset($identity['accessLayout'][$moduleName]['*']['*'])){
                $viewModel->setTemplate('layout/'.$identity['accessLayout'][$moduleName]['*']['*']);
            }else if(isset($identity['accessLayout']['*']['*']['*'])){
                $viewModel->setTemplate('layout/'.$identity['accessLayout']['*']['*']['*']);
            }else if(isset($identity['accessLayout'][$moduleName]['*'])){
                $viewModel->setTemplate('layout/'.$identity['accessLayout'][$moduleName]['*']);
            }else if(isset($identity['accessLayout']['*']['*'])){
                $viewModel->setTemplate('layout/'.$identity['accessLayout']['*']['*']);
            }else if(isset($identity['accessLayout']['*'])){
                $viewModel->setTemplate('layout/'.$identity['accessLayout']['*']);
            }
            return true;
        }else if(isset($accessModule[$moduleName]) && isset($accessModule[$moduleName]['*']) && 
        in_array('*',$accessModule[$moduleName]['*'])){
            if(isset($identity['accessLayout'][$moduleName]['*']['*'])){
                $viewModel->setTemplate('layout/'.$identity['accessLayout'][$moduleName]['*']['*']);
            }else if(isset($identity['accessLayout']['*']['*']['*'])){
                $viewModel->setTemplate('layout/'.$identity['accessLayout']['*']['*']['*']);
            }else if(isset($identity['accessLayout'][$moduleName]['*'])){
                $viewModel->setTemplate('layout/'.$identity['accessLayout'][$moduleName]['*']);
            }else if(isset($identity['accessLayout']['*']['*'])){
                $viewModel->setTemplate('layout/'.$identity['accessLayout']['*']['*']);
            }else if(isset($identity['accessLayout']['*'])){
                $viewModel->setTemplate('layout/'.$identity['accessLayout']['*']);
            }
            return true;
        }else if(isset($accessModule['*']) && isset($accessModule['*']['*']) && 
        in_array('*',$accessModule['*']['*'])){
            if(isset($identity['accessLayout']['*']['*']['*'])){
                $viewModel->setTemplate('layout/'.$identity['accessLayout']['*']['*']['*']);
            }else if(isset($identity['accessLayout']['*']['*'])){
                $viewModel->setTemplate('layout/'.$identity['accessLayout']['*']['*']);
            }else if(isset($identity['accessLayout']['*'])){
                $viewModel->setTemplate('layout/'.$identity['accessLayout']['*']);
            }
            return true;
        }else{
            return false;
        }

        // Debug::dump($identity['permission']);//die();

        // $permission = $identity['permission'];
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
        // return (
        //     (isset($permission[$tmp[0]][$controller]) && in_array($actionName,$permission[$tmp[0]][$controller]))
        //     ||
        //     (isset($permission['*']['*']) && in_array('*',$permission['*']['*']))
        //     ||
        //     (isset($permission['*'][$controller]) && in_array('*',$permission['*'][$controller]))
        //     ||
        //     (isset($permission[$tmp[0]][$controller]) && in_array('*',$permission[$tmp[0]][$controller]))
        //     ||
        //     (isset($permission['*']['*']) && in_array($actionName,$permission['*']['*']))
        //     ||
        //     (isset($permission['*'][$controller]) && in_array($actionName,$permission['*'][$controller]))
        //     ||
        //     (isset($permission[$tmp[0]][$controller]) && in_array($actionName,$permission[$tmp[0]][$controller]))
        // );
    }
}