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
        if($this->db==null)return null;
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
        if($this->db==null)return [];
        if (!$this->db->getDriver()->getConnection()->isConnected()) {
          $this->db->getDriver()->getConnection()->connect();
        }
        $sql = "CALL lam_sys.get_usermenu_byuid_layout_module(:uid,:theme,:module)";
        // die($sql);
        $statement = $this->db->createStatement($sql,["id"=>$id]);
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
            // $permission = $this->getPermission($user['id']);
            // Debug::dump($permission);die('ddd');
            // $this->restructureSession($user,$permission);
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

    private function restructureSession(&$user,$roles){
        //   Debug::dump($user);//die('ddd');
        //   Debug::dump($roles);die('ddd');
    
        $role = [];
        $permission = [];
        $layout = [];
        foreach ($roles as $key => $value) {
        $role[(int)$value['role_id']] = [
            'name'=>$value['role_name'],
            'redirect'=>$value['role_redirect']
        ];

        if(!isset($layout[$value['module_name']])){
            $layout[$value['module_name']] = [
                'layout' => $value['module_layout'],
                'controllers' => [] 
            ];
        }

        if(!isset($layout[$value['module_name']]['controllers'][$value['controller_name']])){
            $layout[$value['module_name']]['controllers'][$value['controller_name']] = [
                'layout' => $value['controller_layout'],
                'actions' => [] 
            ];
        }

        if(!isset($layout[$value['module_name']]['controllers'][$value['controller_name']]['actions'][$value['action_name']])){
            $layout[$value['module_name']]['controllers'][$value['controller_name']]['actions'][$value['action_name']] = [
                'layout' => $value['action_layout']
            ];
        }

        if($value['flayout']!=null && $value['flayout']!=''){
            if($value['fmodule_id']==$value['module_id'] && $value['fcontroller_id']=='0'){
                $layout[$value['module_name']]['layout'] = $value['flayout'];
            }

            if($value['fmodule_id']==$value['module_id'] && $value['fcontroller_id']==$value['controller_id'] && $value['faction_id']=='0'){
                $layout[$value['module_name']]['controllers'][$value['controller_name']]['layout'] = $value['flayout'];
            }

            if($value['fmodule_id']==$value['module_id'] && $value['fcontroller_id']==$value['controller_id'] && $value['faction_id']==$value['action_id']){
                $layout[$value['module_name']]['controllers'][$value['controller_name']]['actions'][$value['action_name']]['layout'] = $value['flayout'];
            }
        }

        if($value['fmodule_id']=='0' && !isset($permission['*'])){
            $permission['*'] = [
                '*' => ['*']
            ];
        }

        if(!isset($permission[$value['module_name']])){
            $permission[$value['module_name']] = [];
        }

        if($value['fcontroller_id']=='0' && !isset($permission[$value['module_name']]['*'])){
            $permission[$value['module_name']]['*'][] = '*';
        }

        if(!isset($permission[$value['module_name']][$value['controller_name']])){
            $permission[$value['module_name']][$value['controller_name']] = [];
        }

        if($value['faction_id']=='0' && !in_array('*',$permission[$value['module_name']][$value['controller_name']])){
            $permission[$value['module_name']][$value['controller_name']][] = '*';
        }

        if(!in_array($value['action_name'],$permission[$value['module_name']][$value['controller_name']])){
            $permission[$value['module_name']][$value['controller_name']][] = $value['action_name'];
        }
        }
        $user['roles'] = $role;
        $user['permission'] = $permission;
        $user['layout'] = $layout;
    //   Debug::dump($user);die('ddd');
    }

    public function removeSessionData($identity){
        $init_container = $this->container->get("container_init");
        // !d($identity,$init_container);die();
        if($this->db==null)return null;
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
}