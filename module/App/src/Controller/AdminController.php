<?php

/**
 * @see       https://github.com/laminas/laminas-mvc-skeleton for the canonical source repository
 * @copyright https://github.com/laminas/laminas-mvc-skeleton/blob/master/COPYRIGHT.md
 * @license   https://github.com/laminas/laminas-mvc-skeleton/blob/master/LICENSE.md New BSD License
 */

declare(strict_types=1);

namespace App\Controller;

use Laminas\Mvc\Controller\AbstractActionController;
use Laminas\View\Model\ViewModel;
use Laminas\Crypt\Password\Bcrypt;

class AdminController extends AbstractActionController{
    
    private $container;
    private $config;
    
    public function __construct($container, $config){
        $this->container = $container;
        $this->config = $config;
    }

    public function indexAction(){
        $this->layout()->setVariable('active_menu', "topmenu1");

        return [];
    }
    
    public function listuserAction(){
        $this->layout()->setVariable('active_menu', "topmenu1");
        $user_mdl = $this->container->get('App\Model\UserModel');
        $users = $user_mdl->getAllUser(false);
        $arr_keys = [];
        if(count($users)>0){
            $arr_keys = [""];
            $arr_tmp = array_keys($users[0]);
            foreach($arr_tmp as $k=>$v){
                if($v==="password")continue;
                $v_tmp = str_replace("_"," ",$v);
                $v_tmp = strtoupper($v_tmp);
                $arr_keys[$v] = $v_tmp;
            }
        }
        // d($users,$arr_keys);

        $csrfForm = $this->container->get('csrfForm');
        $csrf = $csrfForm->get('csrf')->getValue();
        return [
            'csrf' => $csrf,
            'users' => $users,
            'arr_keys' => $arr_keys
        ];
    }
    
    public function mgtuseraddAction(){
        $this->layout()->setVariable('active_menu', "topmenu1");
        $user_mdl = $this->container->get('App\Model\UserModel');
        $tblSchema = $user_mdl->getTableSchema();
        $opt = [
            'username'=>[
                'ICON'=> 'fas fa-user-shield text-default',
                'WEIGHT' => 1,
            ],
            'full_name'=>[
                'ICON'=> 'fas fa-user-tag text-default',
                'WEIGHT' => 2,
            ],
            'password'=>[
                'ICON'=> 'fas fa-user-lock text-default',
                'TYPE'=> 'password',
                'IS_BCRYPT' => true,
                'WEIGHT' => 3,
            ],
            'email'=>[
                'ICON'=> 'fas fa-at text-default',
                'TYPE'=> 'email',
                'WEIGHT' => 4,
            ],
            'status'=>[
                'ICON'=> 'fas fa-info-circle text-default',
                'TYPE'=> 'toggle',
                'ATTR' => [
                    'checked'=>true
                ],
                'WEIGHT' => 5,
            ],
            'redirect_route'=>[
                'ICON'=> 'fas fa-location-arrow text-default',
                'TYPE'=> 'select',
                'DATA'=>[
                    'TYPE'=>'model',
                    'MODEL'=>'App\Model\SysModel',
                    'FUNCTION'=>'selectAllRoute',
                    'PARAM'=>[]
                ],
                'WEIGHT' => 10,
            ],
            'redirect_param'=>[
                'ICON'=> 'fas fa-location-arrow text-default',
                'TYPE'=> 'textarea',
                'WEIGHT' => 11,
                'ADDLABEL' => ' (JSON Format)'
            ],
            'redirect_query'=>[
                'ICON'=> 'fas fa-location-arrow text-default',
                'TYPE'=> 'textarea',
                'WEIGHT' => 12,
                'ADDLABEL' => ' (JSON Format)'
            ],
            'redirect_url'=>[
                'ICON'=> 'fas fa-compass text-default',
                'TYPE'=> 'url',
                'WEIGHT' => 9,
            ],
            'main_role'=>[
                'ICON'=> 'fas fa-star text-default',
                'TYPE'=> 'select',
                'DATA'=>[
                    'TYPE'=>'model',
                    'MODEL'=>'App\Model\RoleModel',
                    'FUNCTION'=>'selectAllRole',
                    'PARAM'=>[]
                ],
                'WEIGHT' => 7,
            ],
            'main_ubis'=>[
                'ICON'=> 'fas fa-star text-default',
                'TYPE'=> 'select',
                'DATA'=>[
                    'TYPE'=>'model',
                    'MODEL'=>'App\Model\UbisModel',
                    'FUNCTION'=>'selectAllUbis',
                    'PARAM'=>[]
                ],
                'WEIGHT' => 8,
            ],
            'is_ldap'=>[
                'ICON'=> 'fas fa-user-check text-default',
                'TYPE'=> 'toggle',
                'WEIGHT' => 6,
            ],
        ];
        $csrfForm = $this->container->get('csrfForm');
        // !d($csrfForm->get('csrf')->getValue());die();
        $tblSchema['csrf'] = $this->DataGenerator()->createNewField('csrf',$csrfForm->get('csrf')->getValue());
        $tblSchema['csrf']['TYPE'] = "csrf";
        // !d($tblSchema['csrf']);die();

        $opt2 = ['pass_reset_token','pass_reset_date'];
        $schema = $this->DataGenerator()->schemaBuilder($tblSchema,$opt,$opt2);
        // !d($schema);die();
        // !d($this->getRequest());die();
        if($this->getRequest()->isPost()){
            $this->processAddUser($schema);
        }

        return [
            'tblSchema' => $schema
        ];
    }

    private function processAddUser($schema){
    	// $pFiles = $this->params()->fromFiles();
    	// $pHeader = $this->params()->fromHeader();
    	// $pRoute = $this->params()->fromRoute();
    	// $pQuery = $this->params()->fromQuery();
        $pPost = $this->params()->fromPost();
        // !d($pHeader,$pPost,$schema);die();
        // $par = ArrayUtils::merge($pPost, $pQuery);
        $ret = $this->DataGenerator()->schemaChecking($schema,$pPost);
        // !d($ret);die();
        // $this->flashMessenger()->addMessage('Message.');
        // $this->flashMessenger()->addMessage('Message 2.');
        // $this->flashMessenger()->addInfoMessage('Message Info.');
        // $this->flashMessenger()->addInfoMessage('Message Info 2.');
        // $this->flashMessenger()->addWarningMessage('Message Info.');
        // $this->flashMessenger()->addWarningMessage('Message Info 2.');
        // $this->flashMessenger()->addErrorMessage('Message Error.');
        // $this->flashMessenger()->addErrorMessage('Message Error 2.');
        // $this->flashMessenger()->addSuccessMessage('Message Success.');
        // $this->flashMessenger()->addSuccessMessage('Message Success 2.');
        if($ret['msg']==="VALID"){
            $valid = true;
            $logout = false;
            $data = $ret['data'];
            // !d($ret['data']);die();
            if($data['redirect_param']!=""){
                $arrtmp = json_decode($data['redirect_param']);
                if($arrtmp===false || $arrtmp===null){
                    $retmsg = "Please input valid REDIRECT PARAM value (in valid JSON format)";
                    $valid = false;
                }
            }
            
            if($valid && $data['redirect_query']!=""){
                $arrtmp = json_decode($data['redirect_query']);
                if($arrtmp===false || $arrtmp===null){
                    $retmsg = "Please input valid REDIRECT QUERY value (in valid JSON format)";
                    $valid = false;
                }
            }
            
            if($valid && isset($data['csrf'])){
                // !d($data['csrf']);die();
                $csrfForm = $this->container->get('csrfForm');
                $csrfForm->setData($data);
                // !d($data,$form->isValid());die();
                if(!$csrfForm->isValid()){
                    // $retmsg = "You can not inject this web!";
                    $valid = false;
                    // $this->flashMessenger()->addErrorMessage($retmsg);
                    $logout = true;
                }
            }else if(!isset($data['csrf'])){
                $valid = false;
                $logout = true;
            }

            if($valid){
                // $dbsys = $this->container->get('db-sys');
                $data['is_ldap'] = isset($data['is_ldap']) && $data['is_ldap']!="" ? 1 : 0;
                $data['status'] = isset($data['status']) && $data['status']!="" ? 1 : 0;
                $data['main_role'] = !isset($data['main_role']) || $data['main_role']==="" || $data['main_role']==="null" ? "GUEST" : $data['main_role'];
                $data['main_ubis'] = !isset($data['main_ubis']) || $data['main_ubis']==="" || $data['main_ubis']==="null" ? "GUEST" : $data['main_ubis'];
                $ret = $this->DataGenerator()->insertSQLExecute('db-sys','_user',$schema,$data);
                // !d($schema,$data,$ret);die();
                if ($ret['ret']) {
                    $this->flashMessenger()->addSuccessMessage('New user has been created.');
                    $this->redirect()->toRoute("admin/manage-user");
                }else{
                    $this->flashMessenger()->addErrorMessage('New user can not be created.');
                    $this->redirect()->toRoute("admin/manage-user/add-user");
                }
            }else{
                // !d($retmsg);die();
                if($logout){
                    $this->redirect()->toRoute("app/auth",['action'=>'logout']);
                }else{
                    $this->flashMessenger()->addInfoMessage($retmsg);
                    $this->redirect()->toRoute("admin/manage-user/add-user");
                }
            }
        }else{
            $this->flashMessenger()->addInfoMessage($ret['msg']);
            $this->redirect()->toRoute("admin/manage-user/add-user");
        }
    }
    
    public function mgtusereditAction(){
        $this->layout()->setVariable('active_menu', "topmenu1");
    	$pRoute = $this->params()->fromRoute();
    	$pHeader = $this->params()->fromHeader();
        // d($pRoute,$pHeader);//die();
        $user_mdl = $this->container->get('App\Model\UserModel');
        $tblSchema = $user_mdl->getTableSchema();
        $user = $user_mdl->getUserById(['id'=> $pRoute['uid']],false);
        // !d($user);//die();
        $opt = [
            'uid'=>[
                'ICON'=> 'fas fa-key text-default',
                'DEFAULT' => $user['id'],
                'WEIGHT' => 0,
                'ATTR' => [
                    'readonly'=>true
                ],
                'IS_NULLABLE'=>false
            ],
            'username'=>[
                'ICON'=> 'fas fa-user-shield text-default',
                'DEFAULT' => $user['username'],
                'WEIGHT' => 1,
            ],
            'full_name'=>[
                'ICON'=> 'fas fa-user-tag text-default',
                'DEFAULT' => $user['full_name'],
                'WEIGHT' => 1,
            ],
            'old_password'=>[
                'ICON'=> 'fas fa-user-lock text-default',
                'TYPE'=> 'password',
                'IS_BCRYPT' => true,
                'LABEL' => 'OLD PASSWORD',
                'WEIGHT' => 3,
                'IS_NULLABLE'=>true
            ],
            'password'=>[
                'ICON'=> 'fas fa-user-lock text-default',
                'TYPE'=> 'password',
                'IS_BCRYPT' => true,
                'LABEL' => 'NEW PASSWORD',
                'WEIGHT' => 4,
                'IS_NULLABLE'=>true
            ],
            'email'=>[
                'ICON'=> 'fas fa-at text-default',
                'TYPE'=> 'email',
                'DEFAULT' => $user['email'],
                'WEIGHT' => 2,
            ],
            'status'=>[
                'ICON'=> 'fas fa-info-circle text-default',
                'TYPE'=> 'toggle',
                'ATTR' => [
                    'checked'=>($user['status']==="1")?true:false
                ],
                'DEFAULT' => $user['status'],
                'WEIGHT' => 5,
            ],
            'redirect_route'=>[
                'ICON'=> 'fas fa-location-arrow text-default',
                'DEFAULT' => $user['redirect_route'],
                'TYPE'=> 'select',
                'WEIGHT' => 10,
                'DATA'=>[
                    'TYPE'=>'model',
                    'MODEL'=>'App\Model\SysModel',
                    'FUNCTION'=>'selectAllRoute',
                    'PARAM'=>[]
                ],
            ],
            'redirect_param'=>[
                'ICON'=> 'fas fa-location-arrow text-default',
                'TYPE'=> 'textarea',
                'ADDLABEL' => ' (JSON Format)',
                'DEFAULT' => $user['redirect_param'],
                'WEIGHT' => 11,
            ],
            'redirect_query'=>[
                'ICON'=> 'fas fa-location-arrow text-default',
                'TYPE'=> 'textarea',
                'ADDLABEL' => ' (JSON Format)',
                'DEFAULT' => $user['redirect_query'],
                'WEIGHT' => 12,
            ],
            'redirect_url'=>[
                'ICON'=> 'fas fa-compass text-default',
                'TYPE'=> 'url',
                'DEFAULT' => $user['redirect_url'],
                'WEIGHT' => 9,
            ],
            'main_role'=>[
                'ICON'=> 'fas fa-star text-default',
                'TYPE'=> 'select',
                'DATA'=>[
                    'TYPE'=>'model',
                    'MODEL'=>'App\Model\RoleModel',
                    'FUNCTION'=>'selectAllRole',
                    'PARAM'=>[]
                ],
                'DEFAULT' => $user['main_role'],
                'WEIGHT' => 7,
            ],
            'main_ubis'=>[
                'ICON'=> 'fas fa-star text-default',
                'TYPE'=> 'select',
                'DATA'=>[
                    'TYPE'=>'model',
                    'MODEL'=>'App\Model\UbisModel',
                    'FUNCTION'=>'selectAllUbis',
                    'PARAM'=>[]
                ],
                'DEFAULT' => $user['main_ubis'],
                'WEIGHT' => 8,
            ],
            'is_ldap'=>[
                'ICON'=> 'fas fa-user-check text-default',
                'TYPE'=> 'toggle',
                'ATTR' => [
                    'checked'=>($user['is_ldap']==="1")?true:false
                ],
                'DEFAULT' => $user['is_ldap'],
                'WEIGHT' => 6,
            ],
        ];
        $csrfForm = $this->container->get('csrfForm');
        $tblSchema['csrf'] = $this->DataGenerator()->createNewField('csrf',$csrfForm->get('csrf')->getValue());
        $tblSchema['csrf']['TYPE'] = "csrf";
        $tblSchema['csrf']['IS_NULLABLE'] = false;
        $tblSchema['uid'] = $this->DataGenerator()->createNewField('uid',$user['id']);
        $tblSchema['uid']['TYPE'] = "text";
        $tblSchema['uid']['IS_NULLABLE'] = false;
        $tblSchema['old_pass'] = $this->DataGenerator()->createNewField('old_pass',$user['password']);
        $tblSchema['old_pass']['TYPE'] = "hidden";
        $tblSchema['old_pass']['IS_NULLABLE'] = false;
        $tblSchema['old_password'] = $this->DataGenerator()->createNewField('old_password');

        $opt2 = ['pass_reset_token','pass_reset_date'];
        $schema = $this->DataGenerator()->schemaBuilder($tblSchema,$opt,$opt2);
        // d($schema);//die();
        if($this->getRequest()->isPost()){
            $this->processEditUser($schema);
        }

        return [
            'tblSchema' => $schema
        ];
    }

    private function processEditUser($schema){
        $pPost = $this->params()->fromPost();
        // !d($pPost);//die();
        // $par = ArrayUtils::merge($pPost, $pQuery);
        $ret = $this->DataGenerator()->schemaChecking($schema,$pPost);
        $setField = $this->DataGenerator()->getUpdateField($schema);
        // !d($setField);die();
        // !d($schema);die();
        if($ret['msg']==="VALID"){
            $valid = true;
            $logout = false;
            $validuser = true;
            $data = $ret['data'];
            // !d($ret['data']);die();
            if(!isset($data['uid']) || $data['uid']===""){
                $valid = false;
                $retmsg = "Sorry we can't find user without valid UID";
                $validuser = false;
            }

            if($data['redirect_param']!=""){
                $arrtmp = json_decode($data['redirect_param']);
                if($arrtmp===false || $arrtmp===null){
                    $retmsg = "Please input valid REDIRECT PARAM value (in valid JSON format)";
                    $valid = false;
                }
            }
            
            if($valid && $data['redirect_query']!=""){
                $arrtmp = json_decode($data['redirect_query']);
                if($arrtmp===false || $arrtmp===null){
                    $retmsg = "Please input valid REDIRECT QUERY value (in valid JSON format)";
                    $valid = false;
                }
            }
            
            if($valid && isset($data['password']) && $data['password']!="" 
            && isset($data['old_password']) && isset($data['old_pass'])){
                // !d($data);//die();
                $bcrypt = new Bcrypt();
                if (!$bcrypt->verify($data['old_password'], $data['old_pass'])) {
                    $valid = false;
                    $retmsg = "Please input valid OLD PASSWORD (if you want to change password)";
                }
            }else if($valid && isset($data['password']) && $data['password']!="" 
            && !isset($data['old_password'])){
                $valid = false;
                $retmsg = "Please input valid OLD PASSWORD (if you want to change password)";
            }else if($valid && isset($data['password']) && $data['password']!="" 
            && !isset($data['old_pass'])){
                $valid = false;
                $retmsg = "Sorry we can't find user without valid UID";
                $validuser = false;
            }else if($valid && isset($data['password']) && $data['password']===""){
                // unset($setField['password']);
                // die("qqq");
                $setField = array_diff($setField, ['password']);
                // !d($setField);die();
            }
            
            if($valid && isset($data['csrf'])){
                // !d($data['csrf']);die();
                $csrfForm = $this->container->get('csrfForm');
                $csrfForm->setData($data);
                // !d($data,$csrfForm->isValid());die();
                if(!$csrfForm->isValid()){
                    // $retmsg = "You can not inject this web!";
                    $valid = false;
                    // $this->flashMessenger()->addErrorMessage($retmsg);
                    $logout = true;
                }
            }else if(!isset($data['csrf'])){
                $valid = false;
                $logout = true;
            }

            // !d($valid,$logout,$retmsg);die();
            if($valid){
                // $dbsys = $this->container->get('db-sys');
                $cond = [
                    'id'=>$data['uid']
                ];

                $set = array_filter(
                    $data,
                    function ($key) use ($setField) {
                        return in_array($key, $setField);
                    },
                    ARRAY_FILTER_USE_KEY
                );

                $set['is_ldap'] = isset($set['is_ldap']) && $set['is_ldap']!="" ? 1 : 0;
                $set['status'] = isset($set['status']) && $set['status']!="" ? 1 : 0;
                $set['main_role'] = !isset($set['main_role']) || $set['main_role']==="" || $set['main_role']==="null" ? "GUEST" : $set['main_role'];
                $set['main_ubis'] = !isset($set['main_ubis']) || $set['main_ubis']==="" || $set['main_ubis']==="null" ? "GUEST" : $set['main_ubis'];
                // !d($set,$data);die();
                // !d($setField,$cond,$set,$data);die();
                $ret = $this->DataGenerator()->updateSQLExecute('db-sys','_user',$cond,$set);
                // !d($ret);die();
                if ($ret['ret']) {
                    $this->flashMessenger()->addSuccessMessage('User (UID:'.$data['uid'].') has been updated.');
                    $this->redirect()->toRoute("admin/manage-user/edit-user",["uid"=>$data['uid']]);
                }else{
                    $this->flashMessenger()->addErrorMessage('User (UID:'.$data['uid'].') can not be updated.');
                    $this->redirect()->toRoute("admin/manage-user/edit-user",["uid"=>$data['uid']]);
                }
            }else{
                // !d($retmsg);die();
                if($logout){
                    $this->redirect()->toRoute("app/auth",['action'=>'logout']);
                }else if(!$validuser){
                    $this->flashMessenger()->addInfoMessage($retmsg);
                    $this->redirect()->toRoute("admin/manage-user");
                }else{
                    $this->flashMessenger()->addInfoMessage($retmsg);
                    $this->redirect()->toRoute("admin/manage-user/edit-user",["uid"=>$data['uid']]);
                }
            }
        }else{
            $this->flashMessenger()->addInfoMessage($ret['msg']);
            $this->redirect()->toRoute("admin/manage-user/add-user");
        }
    }
    
    public function listroleAction(){
        $this->layout()->setVariable('active_menu', "topmenu1");
        $role_mdl = $this->container->get('App\Model\RoleModel');
        $roles = $role_mdl->getAllRole(false);
        $arr_keys = [];
        if(count($roles)>0){
            $arr_keys = [""];
            $arr_tmp = array_keys($roles[0]);
            foreach($arr_tmp as $k=>$v){
                $v_tmp = str_replace("_"," ",$v);
                $v_tmp = strtoupper($v_tmp);
                $arr_keys[$v] = $v_tmp;
            }
        }
        // d($roles,$arr_keys);die();

        $csrfForm = $this->container->get('csrfForm');
        $csrf = $csrfForm->get('csrf')->getValue();
        return [
            'csrf' => $csrf,
            'roles' => $roles,
            'arr_keys' => $arr_keys
        ];
    }
    
    public function mgtroleaddAction(){
        $this->layout()->setVariable('active_menu', "topmenu1");
        $role_mdl = $this->container->get('App\Model\RoleModel');
        $tblSchema = $role_mdl->getTableSchema();
        $opt = [
            'code'=>[
                'ICON'=> 'fas fa-key text-default',
                'WEIGHT' => 1,
            ],
            'name'=>[
                'ICON'=> 'fas fa-tags text-default',
                'WEIGHT' => 2,
            ],
            'status'=>[
                'ICON'=> 'fas fa-info-circle text-default',
                'TYPE'=> 'toggle',
                'ATTR' => [
                    'checked'=>true
                ],
                'WEIGHT' => 3,
            ],
            'redirect_route'=>[
                'ICON'=> 'fas fa-location-arrow text-default',
                'TYPE'=> 'select',
                'DATA'=>[
                    'TYPE'=>'model',
                    'MODEL'=>'App\Model\SysModel',
                    'FUNCTION'=>'selectAllRoute',
                    'PARAM'=>[]
                ],
                'WEIGHT' => 10,
            ],
            'redirect_param'=>[
                'ICON'=> 'fas fa-location-arrow text-default',
                'TYPE'=> 'textarea',
                'WEIGHT' => 11,
                'ADDLABEL' => ' (JSON Format)'
            ],
            'redirect_query'=>[
                'ICON'=> 'fas fa-location-arrow text-default',
                'TYPE'=> 'textarea',
                'WEIGHT' => 12,
                'ADDLABEL' => ' (JSON Format)'
            ],
            'redirect_url'=>[
                'ICON'=> 'fas fa-compass text-default',
                'TYPE'=> 'url',
                'WEIGHT' => 5,
            ],
        ];
        $csrfForm = $this->container->get('csrfForm');
        // !d($csrfForm->get('csrf')->getValue());die();
        $tblSchema['csrf'] = $this->DataGenerator()->createNewField('csrf',$csrfForm->get('csrf')->getValue());
        $tblSchema['csrf']['TYPE'] = "csrf";
        // !d($tblSchema['csrf']);die();
        $tblSchema['blank1'] = $this->DataGenerator()->createNewField('blank1','');
        $tblSchema['blank1']['TYPE'] = "content";
        $tblSchema['blank1']['IS_NULLABLE'] = true;
        $tblSchema['blank1']['WEIGHT'] = 4;
        $tblSchema['blank1']['ICON'] = "";
        $tblSchema['blank1']['LABEL'] = "";

        $opt2 = [];
        $schema = $this->DataGenerator()->schemaBuilder($tblSchema,$opt,$opt2);
        // !d($schema);die();
        // !d($this->getRequest());die();
        if($this->getRequest()->isPost()){
            $this->processAddRole($schema);
        }

        return [
            'tblSchema' => $schema
        ];
    }

    private function processAddRole($schema){
    	// $pFiles = $this->params()->fromFiles();
    	// $pHeader = $this->params()->fromHeader();
    	// $pRoute = $this->params()->fromRoute();
    	// $pQuery = $this->params()->fromQuery();
        $pPost = $this->params()->fromPost();
        // !d($pHeader,$pPost,$schema);die();
        // $par = ArrayUtils::merge($pPost, $pQuery);
        $ret = $this->DataGenerator()->schemaChecking($schema,$pPost);
        // !d($ret);die();
        // $this->flashMessenger()->addMessage('Message.');
        // $this->flashMessenger()->addMessage('Message 2.');
        // $this->flashMessenger()->addInfoMessage('Message Info.');
        // $this->flashMessenger()->addInfoMessage('Message Info 2.');
        // $this->flashMessenger()->addWarningMessage('Message Info.');
        // $this->flashMessenger()->addWarningMessage('Message Info 2.');
        // $this->flashMessenger()->addErrorMessage('Message Error.');
        // $this->flashMessenger()->addErrorMessage('Message Error 2.');
        // $this->flashMessenger()->addSuccessMessage('Message Success.');
        // $this->flashMessenger()->addSuccessMessage('Message Success 2.');
        if($ret['msg']==="VALID"){
            $valid = true;
            $logout = false;
            $data = $ret['data'];
            // !d($ret['data']);die();
            if($data['redirect_param']!=""){
                $arrtmp = json_decode($data['redirect_param']);
                if($arrtmp===false || $arrtmp===null){
                    $retmsg = "Please input valid REDIRECT PARAM value (in valid JSON format)";
                    $valid = false;
                }
            }
            
            if($valid && $data['redirect_query']!=""){
                $arrtmp = json_decode($data['redirect_query']);
                if($arrtmp===false || $arrtmp===null){
                    $retmsg = "Please input valid REDIRECT QUERY value (in valid JSON format)";
                    $valid = false;
                }
            }
            
            if($valid && isset($data['csrf'])){
                // !d($data['csrf']);die();
                $csrfForm = $this->container->get('csrfForm');
                $csrfForm->setData($data);
                // !d($data,$form->isValid());die();
                if(!$csrfForm->isValid()){
                    // $retmsg = "You can not inject this web!";
                    $valid = false;
                    // $this->flashMessenger()->addErrorMessage($retmsg);
                    $logout = true;
                }
            }else if(!isset($data['csrf'])){
                $valid = false;
                $logout = true;
            }

            if($valid){
                // $dbsys = $this->container->get('db-sys');
                // !d($schema,$data);die();
                $data['status'] = isset($data['status']) && $data['status']!="" ? 1 : 0;
                $ret = $this->DataGenerator()->insertSQLExecute('db-sys','_role',$schema,$data);
                // !d($ret);die();
                if ($ret['ret']) {
                    $this->flashMessenger()->addSuccessMessage('New role has been created.');
                    $this->redirect()->toRoute("admin/manage-role");
                }else{
                    $this->flashMessenger()->addErrorMessage('New role can not be created.');
                    $this->redirect()->toRoute("admin/manage-role/add-role");
                }
            }else{
                // !d($retmsg);die();
                if($logout){
                    $this->redirect()->toRoute("app/auth",['action'=>'logout']);
                }else{
                    $this->flashMessenger()->addInfoMessage($retmsg);
                    $this->redirect()->toRoute("admin/manage-role/add-role");
                }
            }
        }else{
            $this->flashMessenger()->addInfoMessage($ret['msg']);
            $this->redirect()->toRoute("admin/manage-role/add-role");
        }
    }
    
    public function mgtroleeditAction(){
        $this->layout()->setVariable('active_menu', "topmenu1");
    	$pRoute = $this->params()->fromRoute();
    	$pHeader = $this->params()->fromHeader();
        // d($pRoute,$pHeader);//die();
        $role_mdl = $this->container->get('App\Model\RoleModel');
        $tblSchema = $role_mdl->getTableSchema();
        $role = $role_mdl->getRoleByCode(['code'=> $pRoute['code']],false);
        // !d($role);//die();
        $opt = [
            'code'=>[
                'ICON'=> 'fas fa-key text-default',
                'DEFAULT' => $role['code'],
                'WEIGHT' => 0,
                'ATTR' => [
                    'readonly'=>true,
                    'primary_key'=>true
                ],
                'IS_NULLABLE'=>false
            ],
            'name'=>[
                'ICON'=> 'fas fa-tags text-default',
                'DEFAULT' => $role['name'],
                'WEIGHT' => 1,
            ],
            'status'=>[
                'ICON'=> 'fas fa-info-circle text-default',
                'TYPE'=> 'toggle',
                'ATTR' => [
                    'checked'=>($role['status']==="1")?true:false
                ],
                'DEFAULT' => $role['status'],
                'WEIGHT' => 2,
            ],
            'redirect_route'=>[
                'ICON'=> 'fas fa-location-arrow text-default',
                'DEFAULT' => $role['redirect_route'],
                'TYPE'=> 'select',
                'WEIGHT' => 10,
                'DATA'=>[
                    'TYPE'=>'model',
                    'MODEL'=>'App\Model\SysModel',
                    'FUNCTION'=>'selectAllRoute',
                    'PARAM'=>[]
                ],
            ],
            'redirect_param'=>[
                'ICON'=> 'fas fa-location-arrow text-default',
                'TYPE'=> 'textarea',
                'ADDLABEL' => ' (JSON Format)',
                'DEFAULT' => $role['redirect_param'],
                'WEIGHT' => 11,
            ],
            'redirect_query'=>[
                'ICON'=> 'fas fa-location-arrow text-default',
                'TYPE'=> 'textarea',
                'ADDLABEL' => ' (JSON Format)',
                'DEFAULT' => $role['redirect_query'],
                'WEIGHT' => 12,
            ],
            'redirect_url'=>[
                'ICON'=> 'fas fa-compass text-default',
                'TYPE'=> 'url',
                'DEFAULT' => $role['redirect_url'],
                'WEIGHT' => 9,
            ]
        ];
        $csrfForm = $this->container->get('csrfForm');
        $tblSchema['csrf'] = $this->DataGenerator()->createNewField('csrf',$csrfForm->get('csrf')->getValue());
        $tblSchema['csrf']['TYPE'] = "csrf";
        $tblSchema['csrf']['IS_NULLABLE'] = false;
        $tblSchema['blank1'] = $this->DataGenerator()->createNewField('blank1','');
        $tblSchema['blank1']['TYPE'] = "content";
        $tblSchema['blank1']['IS_NULLABLE'] = true;
        $tblSchema['blank1']['WEIGHT'] = 3;
        $tblSchema['blank1']['ICON'] = "";
        $tblSchema['blank1']['LABEL'] = "";

        $opt2 = [];
        // !d($opt);die();
        $schema = $this->DataGenerator()->schemaBuilder($tblSchema,$opt,$opt2);
        // !d($opt,$schema);die();
        if($this->getRequest()->isPost()){
            $this->processEditRole($schema);
        }

        return [
            'tblSchema' => $schema
        ];
    }

    private function processEditRole($schema){
        $pPost = $this->params()->fromPost();
        // !d($pPost);//die();
        // $par = ArrayUtils::merge($pPost, $pQuery);
        $ret = $this->DataGenerator()->schemaChecking($schema,$pPost);
        $setField = $this->DataGenerator()->getUpdateField($schema);
        // !d($setField);die();
        // !d($schema);die();
        if($ret['msg']==="VALID"){
            $valid = true;
            $logout = false;
            $validcode = true;
            $data = $ret['data'];
            // !d($ret['data']);die();
            if(!isset($data['code']) || $data['code']===""){
                $valid = false;
                $retmsg = "Sorry we can't find role without valid CODE";
                $validcode = false;
            }

            if($data['redirect_param']!=""){
                $arrtmp = json_decode($data['redirect_param']);
                if($arrtmp===false || $arrtmp===null){
                    $retmsg = "Please input valid REDIRECT PARAM value (in valid JSON format)";
                    $valid = false;
                }
            }
            
            if($valid && $data['redirect_query']!=""){
                $arrtmp = json_decode($data['redirect_query']);
                if($arrtmp===false || $arrtmp===null){
                    $retmsg = "Please input valid REDIRECT QUERY value (in valid JSON format)";
                    $valid = false;
                }
            }
            
            if($valid && isset($data['csrf'])){
                // !d($data['csrf']);die();
                $csrfForm = $this->container->get('csrfForm');
                $csrfForm->setData($data);
                // !d($data,$csrfForm->isValid());die();
                if(!$csrfForm->isValid()){
                    // $retmsg = "You can not inject this web!";
                    $valid = false;
                    // $this->flashMessenger()->addErrorMessage($retmsg);
                    $logout = true;
                }
            }else if(!isset($data['csrf'])){
                $valid = false;
                $logout = true;
            }

            // !d($valid,$logout,$retmsg);die();
            if($valid){
                // $dbsys = $this->container->get('db-sys');
                $cond = [
                    'code'=>$data['code']
                ];

                $set = array_filter(
                    $data,
                    function ($key) use ($setField) {
                        return in_array($key, $setField) ;
                    },
                    ARRAY_FILTER_USE_KEY
                );

                $set['status'] = isset($set['status']) && $set['status']!="" ? 1 : 0;
                // !d($set,$data);die();
                // !d($cond,$set,$data);die();
                $ret = $this->DataGenerator()->updateSQLExecute('db-sys','_role',$cond,$set);
                // !d($cond,$set,$ret);die();
                if ($ret['ret']) {
                    $this->flashMessenger()->addSuccessMessage('Role (Code:'.$data['code'].') has been updated.');
                    $this->redirect()->toRoute("admin/manage-role/edit-role",["code"=>$data['code']]);
                }else{
                    $this->flashMessenger()->addErrorMessage('Role (Code:'.$data['code'].') can not be updated.');
                    $this->redirect()->toRoute("admin/manage-role/edit-role",["code"=>$data['code']]);
                }
            }else{
                // !d($retmsg);die();
                if($logout){
                    $this->redirect()->toRoute("app/auth",['action'=>'logout']);
                }else if(!$validcode){
                    $this->flashMessenger()->addInfoMessage($retmsg);
                    $this->redirect()->toRoute("admin/manage-role");
                }else{
                    $this->flashMessenger()->addInfoMessage($retmsg);
                    $this->redirect()->toRoute("admin/manage-role/edit-role",["code"=>$data['code']]);
                }
            }
        }else{
            $this->flashMessenger()->addInfoMessage($ret['msg']);
            $this->redirect()->toRoute("admin/manage-role/add-role");
        }
    }
    
    public function listubisAction(){
        $this->layout()->setVariable('active_menu', "topmenu1");
        $ubis_mdl = $this->container->get('App\Model\UbisModel');
        $ubis = $ubis_mdl->getAllUbis(false);
        $arr_keys = [];
        if(count($ubis)>0){
            $arr_keys = [""];
            $arr_tmp = array_keys($ubis[0]);
            foreach($arr_tmp as $k=>$v){
                $v_tmp = str_replace("_"," ",$v);
                $v_tmp = strtoupper($v_tmp);
                $arr_keys[$v] = $v_tmp;
            }
        }
        // d($ubis,$arr_keys);die();

        $csrfForm = $this->container->get('csrfForm');
        $csrf = $csrfForm->get('csrf')->getValue();
        return [
            'csrf' => $csrf,
            'ubis' => $ubis,
            'arr_keys' => $arr_keys
        ];
    }
    
    public function mgtubisaddAction(){
        $this->layout()->setVariable('active_menu', "topmenu1");
        $ubis_mdl = $this->container->get('App\Model\UbisModel');
        $tblSchema = $ubis_mdl->getTableSchema();
        $opt = [
            'parent'=>[
                'ICON'=> 'fas fa-project-diagram text-default',
                'TYPE'=> 'select',
                'DATA'=>[
                    'TYPE'=>'model',
                    'MODEL'=>'App\Model\SysModel',
                    'FUNCTION'=>'selectAllUbis',
                    'PARAM'=>[]
                ],
                'WEIGHT' => 0,
            ],
            'level'=>[
                'ICON'=> 'fas fa-layer-group text-default',
                'TYPE'=> 'select',
                'DATA'=>[
                    'TYPE'=>'model',
                    'MODEL'=>'App\Model\SysModel',
                    'FUNCTION'=>'selectAllUbislevel',
                    'PARAM'=>[]
                ],
                'WEIGHT' => 0,
            ],
            'code'=>[
                'ICON'=> 'fas fa-key text-default',
                'WEIGHT' => 1,
            ],
            'name'=>[
                'ICON'=> 'fas fa-tags text-default',
                'WEIGHT' => 2,
            ],
            'status'=>[
                'ICON'=> 'fas fa-info-circle text-default',
                'TYPE'=> 'toggle',
                'ATTR' => [
                    'checked'=>true
                ],
                'WEIGHT' => 3,
            ],
            'redirect_route'=>[
                'ICON'=> 'fas fa-location-arrow text-default',
                'TYPE'=> 'select',
                'DATA'=>[
                    'TYPE'=>'model',
                    'MODEL'=>'App\Model\SysModel',
                    'FUNCTION'=>'selectAllRoute',
                    'PARAM'=>[]
                ],
                'WEIGHT' => 10,
            ],
            'redirect_param'=>[
                'ICON'=> 'fas fa-location-arrow text-default',
                'TYPE'=> 'textarea',
                'WEIGHT' => 11,
                'ADDLABEL' => ' (JSON Format)'
            ],
            'redirect_query'=>[
                'ICON'=> 'fas fa-location-arrow text-default',
                'TYPE'=> 'textarea',
                'WEIGHT' => 12,
                'ADDLABEL' => ' (JSON Format)'
            ],
            'redirect_url'=>[
                'ICON'=> 'fas fa-compass text-default',
                'TYPE'=> 'url',
                'WEIGHT' => 5,
            ],
        ];
        $csrfForm = $this->container->get('csrfForm');
        // !d($csrfForm->get('csrf')->getValue());die();
        $tblSchema['csrf'] = $this->DataGenerator()->createNewField('csrf',$csrfForm->get('csrf')->getValue());
        $tblSchema['csrf']['TYPE'] = "csrf";
        // !d($tblSchema['csrf']);die();
        $tblSchema['blank1'] = $this->DataGenerator()->createNewField('blank1','');
        $tblSchema['blank1']['TYPE'] = "content";
        $tblSchema['blank1']['IS_NULLABLE'] = true;
        $tblSchema['blank1']['WEIGHT'] = 4;
        $tblSchema['blank1']['ICON'] = "";
        $tblSchema['blank1']['LABEL'] = "";

        $opt2 = [];
        $schema = $this->DataGenerator()->schemaBuilder($tblSchema,$opt,$opt2);
        // !d($schema);die();
        // !d($this->getRequest());die();
        if($this->getRequest()->isPost()){
            $this->processAddUbis($schema);
        }

        return [
            'tblSchema' => $schema
        ];
    }

    private function processAddUbis($schema){
    	// $pFiles = $this->params()->fromFiles();
    	// $pHeader = $this->params()->fromHeader();
    	// $pRoute = $this->params()->fromRoute();
    	// $pQuery = $this->params()->fromQuery();
        $pPost = $this->params()->fromPost();
        // !d($pHeader,$pPost,$schema);die();
        // $par = ArrayUtils::merge($pPost, $pQuery);
        $ret = $this->DataGenerator()->schemaChecking($schema,$pPost);
        // !d($ret);die();
        // $this->flashMessenger()->addMessage('Message.');
        // $this->flashMessenger()->addMessage('Message 2.');
        // $this->flashMessenger()->addInfoMessage('Message Info.');
        // $this->flashMessenger()->addInfoMessage('Message Info 2.');
        // $this->flashMessenger()->addWarningMessage('Message Info.');
        // $this->flashMessenger()->addWarningMessage('Message Info 2.');
        // $this->flashMessenger()->addErrorMessage('Message Error.');
        // $this->flashMessenger()->addErrorMessage('Message Error 2.');
        // $this->flashMessenger()->addSuccessMessage('Message Success.');
        // $this->flashMessenger()->addSuccessMessage('Message Success 2.');
        if($ret['msg']==="VALID"){
            $valid = true;
            $logout = false;
            $data = $ret['data'];
            // !d($ret['data']);die();
            if($data['redirect_param']!=""){
                $arrtmp = json_decode($data['redirect_param']);
                if($arrtmp===false || $arrtmp===null){
                    $retmsg = "Please input valid REDIRECT PARAM value (in valid JSON format)";
                    $valid = false;
                }
            }
            
            if($valid && $data['redirect_query']!=""){
                $arrtmp = json_decode($data['redirect_query']);
                if($arrtmp===false || $arrtmp===null){
                    $retmsg = "Please input valid REDIRECT QUERY value (in valid JSON format)";
                    $valid = false;
                }
            }
            
            if($valid && isset($data['csrf'])){
                // !d($data['csrf']);die();
                $csrfForm = $this->container->get('csrfForm');
                $csrfForm->setData($data);
                // !d($data,$form->isValid());die();
                if(!$csrfForm->isValid()){
                    // $retmsg = "You can not inject this web!";
                    $valid = false;
                    // $this->flashMessenger()->addErrorMessage($retmsg);
                    $logout = true;
                }
            }else if(!isset($data['csrf'])){
                $valid = false;
                $logout = true;
            }

            if($valid){
                // $dbsys = $this->container->get('db-sys');
                // !d($schema,$data);die();
                $data['status'] = isset($data['status']) && $data['status']!="" ? 1 : 0;
                $ret = $this->DataGenerator()->insertSQLExecute('db-sys','_ubis',$schema,$data);
                // !d($ret);die();
                if ($ret['ret']) {
                    $this->flashMessenger()->addSuccessMessage('New ubis has been created.');
                    $this->redirect()->toRoute("admin/manage-ubis");
                }else{
                    $this->flashMessenger()->addErrorMessage('New ubis can not be created.');
                    $this->redirect()->toRoute("admin/manage-ubis/add-ubis");
                }
            }else{
                // !d($retmsg);die();
                if($logout){
                    $this->redirect()->toRoute("app/auth",['action'=>'logout']);
                }else{
                    $this->flashMessenger()->addInfoMessage($retmsg);
                    $this->redirect()->toRoute("admin/manage-ubis/add-ubis");
                }
            }
        }else{
            $this->flashMessenger()->addInfoMessage($ret['msg']);
            $this->redirect()->toRoute("admin/manage-ubis/add-ubis");
        }
    }
    
    public function mgtubiseditAction(){
        $this->layout()->setVariable('active_menu', "topmenu1");
    	$pRoute = $this->params()->fromRoute();
    	$pHeader = $this->params()->fromHeader();
        // d($pRoute,$pHeader);//die();
        $ubis_mdl = $this->container->get('App\Model\UbisModel');
        $tblSchema = $ubis_mdl->getTableSchema();
        $ubis = $ubis_mdl->getUbisByCode(['code'=> $pRoute['code']],false);
        // !d($ubis);//die();
        $opt = [
            'code'=>[
                'ICON'=> 'fas fa-key text-default',
                'DEFAULT' => $ubis['code'],
                'WEIGHT' => 0,
                'ATTR' => [
                    'readonly'=>true,
                    'primary_key'=>true
                ],
                'IS_NULLABLE'=>false
            ],
            'parent'=>[
                'ICON'=> 'fas fa-project-diagram text-default',
                'DEFAULT' => $ubis['parent'],
                'TYPE'=> 'select',
                'DATA'=>[
                    'TYPE'=>'model',
                    'MODEL'=>'App\Model\SysModel',
                    'FUNCTION'=>'selectAllUbis',
                    'PARAM'=>['exclude'=>[$ubis['code']]]
                ],
                'WEIGHT' => 1,
            ],
            'level'=>[
                'ICON'=> 'fas fa-layer-group text-default',
                'DEFAULT' => $ubis['level'],
                'TYPE'=> 'select',
                'DATA'=>[
                    'TYPE'=>'model',
                    'MODEL'=>'App\Model\SysModel',
                    'FUNCTION'=>'selectAllUbislevel',
                    'PARAM'=>['exclude'=>[$ubis['code']]]
                ],
                'WEIGHT' => 1,
            ],
            'name'=>[
                'ICON'=> 'fas fa-user-shield text-default',
                'DEFAULT' => $ubis['name'],
                'WEIGHT' => 1,
            ],
            'status'=>[
                'ICON'=> 'fas fa-info-circle text-default',
                'TYPE'=> 'toggle',
                'ATTR' => [
                    'checked'=>($ubis['status']==="1")?true:false
                ],
                'DEFAULT' => $ubis['status'],
                'WEIGHT' => 2,
            ],
            'redirect_route'=>[
                'ICON'=> 'fas fa-location-arrow text-default',
                'DEFAULT' => $ubis['redirect_route'],
                'TYPE'=> 'select',
                'WEIGHT' => 10,
                'DATA'=>[
                    'TYPE'=>'model',
                    'MODEL'=>'App\Model\SysModel',
                    'FUNCTION'=>'selectAllRoute',
                    'PARAM'=>[]
                ],
            ],
            'redirect_param'=>[
                'ICON'=> 'fas fa-location-arrow text-default',
                'TYPE'=> 'textarea',
                'ADDLABEL' => ' (JSON Format)',
                'DEFAULT' => $ubis['redirect_param'],
                'WEIGHT' => 11,
            ],
            'redirect_query'=>[
                'ICON'=> 'fas fa-location-arrow text-default',
                'TYPE'=> 'textarea',
                'ADDLABEL' => ' (JSON Format)',
                'DEFAULT' => $ubis['redirect_query'],
                'WEIGHT' => 12,
            ],
            'redirect_url'=>[
                'ICON'=> 'fas fa-compass text-default',
                'TYPE'=> 'url',
                'DEFAULT' => $ubis['redirect_url'],
                'WEIGHT' => 9,
            ]
        ];
        $csrfForm = $this->container->get('csrfForm');
        $tblSchema['csrf'] = $this->DataGenerator()->createNewField('csrf',$csrfForm->get('csrf')->getValue());
        $tblSchema['csrf']['TYPE'] = "csrf";
        $tblSchema['csrf']['IS_NULLABLE'] = false;
        $tblSchema['blank1'] = $this->DataGenerator()->createNewField('blank1','');
        $tblSchema['blank1']['TYPE'] = "content";
        $tblSchema['blank1']['IS_NULLABLE'] = true;
        $tblSchema['blank1']['WEIGHT'] = 3;
        $tblSchema['blank1']['ICON'] = "";
        $tblSchema['blank1']['LABEL'] = "";

        $opt2 = [];
        // !d($opt);die();
        $schema = $this->DataGenerator()->schemaBuilder($tblSchema,$opt,$opt2);
        // !d($opt,$schema);die();
        if($this->getRequest()->isPost()){
            $this->processEditUbis($schema);
        }

        return [
            'tblSchema' => $schema
        ];
    }

    private function processEditUbis($schema){
        $pPost = $this->params()->fromPost();
        // !d($pPost);//die();
        // $par = ArrayUtils::merge($pPost, $pQuery);
        $ret = $this->DataGenerator()->schemaChecking($schema,$pPost);
        $setField = $this->DataGenerator()->getUpdateField($schema);
        // !d($setField);die();
        // !d($schema);die();
        if($ret['msg']==="VALID"){
            $valid = true;
            $logout = false;
            $validcode = true;
            $data = $ret['data'];
            // !d($ret['data']);die();
            if(!isset($data['code']) || $data['code']===""){
                $valid = false;
                $retmsg = "Sorry we can't find ubis without valid CODE";
                $validcode = false;
            }

            if($data['redirect_param']!=""){
                $arrtmp = json_decode($data['redirect_param']);
                if($arrtmp===false || $arrtmp===null){
                    $retmsg = "Please input valid REDIRECT PARAM value (in valid JSON format)";
                    $valid = false;
                }
            }
            
            if($valid && $data['redirect_query']!=""){
                $arrtmp = json_decode($data['redirect_query']);
                if($arrtmp===false || $arrtmp===null){
                    $retmsg = "Please input valid REDIRECT QUERY value (in valid JSON format)";
                    $valid = false;
                }
            }
            
            if($valid && isset($data['csrf'])){
                // !d($data['csrf']);die();
                $csrfForm = $this->container->get('csrfForm');
                $csrfForm->setData($data);
                // !d($data,$csrfForm->isValid());die();
                if(!$csrfForm->isValid()){
                    // $retmsg = "You can not inject this web!";
                    $valid = false;
                    // $this->flashMessenger()->addErrorMessage($retmsg);
                    $logout = true;
                }
            }else if(!isset($data['csrf'])){
                $valid = false;
                $logout = true;
            }

            // !d($valid,$logout,$retmsg);die();
            if($valid){
                // $dbsys = $this->container->get('db-sys');
                $cond = [
                    'code'=>$data['code']
                ];

                $set = array_filter(
                    $data,
                    function ($key) use ($setField) {
                        return in_array($key, $setField) ;
                    },
                    ARRAY_FILTER_USE_KEY
                );

                $set['status'] = isset($set['status']) && $set['status']!="" ? 1 : 0;
                // !d($set,$data);die();
                // !d($cond,$set,$data);die();
                $ret = $this->DataGenerator()->updateSQLExecute('db-sys','_ubis',$cond,$set);
                // !d($cond,$set,$ret);die();
                if ($ret['ret']) {
                    $this->flashMessenger()->addSuccessMessage('Ubis (Code:'.$data['code'].') has been updated.');
                    $this->redirect()->toRoute("admin/manage-ubis/edit-ubis",["code"=>$data['code']]);
                }else{
                    $this->flashMessenger()->addErrorMessage('Ubis (Code:'.$data['code'].') can not be updated.');
                    $this->redirect()->toRoute("admin/manage-ubis/edit-ubis",["code"=>$data['code']]);
                }
            }else{
                // !d($retmsg);die();
                if($logout){
                    $this->redirect()->toRoute("app/auth",['action'=>'logout']);
                }else if(!$validcode){
                    $this->flashMessenger()->addInfoMessage($retmsg);
                    $this->redirect()->toRoute("admin/manage-ubis");
                }else{
                    $this->flashMessenger()->addInfoMessage($retmsg);
                    $this->redirect()->toRoute("admin/manage-ubis/edit-ubis",["code"=>$data['code']]);
                }
            }
        }else{
            $this->flashMessenger()->addInfoMessage($ret['msg']);
            $this->redirect()->toRoute("admin/manage-ubis/add-ubis");
        }
    }
    
    public function listubislevelAction(){
        $this->layout()->setVariable('active_menu', "topmenu1");
        $ubislevel_mdl = $this->container->get('App\Model\UbislevelModel');
        $ubislevel = $ubislevel_mdl->getAllUbislevel(false);
        $arr_keys = [];
        if(count($ubislevel)>0){
            $arr_keys = [""];
            $arr_tmp = array_keys($ubislevel[0]);
            foreach($arr_tmp as $k=>$v){
                $v_tmp = str_replace("_"," ",$v);
                $v_tmp = strtoupper($v_tmp);
                $arr_keys[$v] = $v_tmp;
            }
        }
        // d($ubislevel,$arr_keys);die();

        $csrfForm = $this->container->get('csrfForm');
        $csrf = $csrfForm->get('csrf')->getValue();
        return [
            'csrf' => $csrf,
            'ubislevel' => $ubislevel,
            'arr_keys' => $arr_keys
        ];
    }
    
    public function mgtubisleveladdAction(){
        $this->layout()->setVariable('active_menu', "topmenu1");
        $ubislevel_mdl = $this->container->get('App\Model\UbislevelModel');
        $tblSchema = $ubislevel_mdl->getTableSchema();
        $opt = [
            'code'=>[
                'ICON'=> 'fas fa-key text-default',
                'WEIGHT' => 1,
            ],
            'name'=>[
                'ICON'=> 'fas fa-tags text-default',
                'WEIGHT' => 2,
            ],
            'status'=>[
                'ICON'=> 'fas fa-info-circle text-default',
                'TYPE'=> 'toggle',
                'ATTR' => [
                    'checked'=>true
                ],
                'WEIGHT' => 3,
            ]
        ];
        $csrfForm = $this->container->get('csrfForm');
        // !d($csrfForm->get('csrf')->getValue());die();
        $tblSchema['csrf'] = $this->DataGenerator()->createNewField('csrf',$csrfForm->get('csrf')->getValue());
        $tblSchema['csrf']['TYPE'] = "csrf";
        // !d($tblSchema['csrf']);die();
        $tblSchema['blank1'] = $this->DataGenerator()->createNewField('blank1','');
        $tblSchema['blank1']['TYPE'] = "content";
        $tblSchema['blank1']['IS_NULLABLE'] = true;
        $tblSchema['blank1']['WEIGHT'] = 4;
        $tblSchema['blank1']['ICON'] = "";
        $tblSchema['blank1']['LABEL'] = "";

        $opt2 = [];
        $schema = $this->DataGenerator()->schemaBuilder($tblSchema,$opt,$opt2);
        // !d($schema);die();
        // !d($this->getRequest());die();
        if($this->getRequest()->isPost()){
            $this->processAddUbislevel($schema);
        }

        return [
            'tblSchema' => $schema
        ];
    }

    private function processAddUbislevel($schema){
    	// $pFiles = $this->params()->fromFiles();
    	// $pHeader = $this->params()->fromHeader();
    	// $pRoute = $this->params()->fromRoute();
    	// $pQuery = $this->params()->fromQuery();
        $pPost = $this->params()->fromPost();
        // !d($pHeader,$pPost,$schema);die();
        // $par = ArrayUtils::merge($pPost, $pQuery);
        $ret = $this->DataGenerator()->schemaChecking($schema,$pPost);
        // !d($ret);die();
        // $this->flashMessenger()->addMessage('Message.');
        // $this->flashMessenger()->addMessage('Message 2.');
        // $this->flashMessenger()->addInfoMessage('Message Info.');
        // $this->flashMessenger()->addInfoMessage('Message Info 2.');
        // $this->flashMessenger()->addWarningMessage('Message Info.');
        // $this->flashMessenger()->addWarningMessage('Message Info 2.');
        // $this->flashMessenger()->addErrorMessage('Message Error.');
        // $this->flashMessenger()->addErrorMessage('Message Error 2.');
        // $this->flashMessenger()->addSuccessMessage('Message Success.');
        // $this->flashMessenger()->addSuccessMessage('Message Success 2.');
        if($ret['msg']==="VALID"){
            $valid = true;
            $logout = false;
            $data = $ret['data'];
            // !d($ret['data']);die();
            
            if($valid && isset($data['csrf'])){
                // !d($data['csrf']);die();
                $csrfForm = $this->container->get('csrfForm');
                $csrfForm->setData($data);
                // !d($data,$form->isValid());die();
                if(!$csrfForm->isValid()){
                    // $retmsg = "You can not inject this web!";
                    $valid = false;
                    // $this->flashMessenger()->addErrorMessage($retmsg);
                    $logout = true;
                }
            }else if(!isset($data['csrf'])){
                $valid = false;
                $logout = true;
            }

            if($valid){
                // $dbsys = $this->container->get('db-sys');
                // !d($schema,$data);die();
                $data['status'] = isset($data['status']) && $data['status']!="" ? 1 : 0;
                $ret = $this->DataGenerator()->insertSQLExecute('db-sys','_ubis_level',$schema,$data);
                // !d($ret);die();
                if ($ret['ret']) {
                    $this->flashMessenger()->addSuccessMessage('New ubislevel has been created.');
                    $this->redirect()->toRoute("admin/manage-ubislevel");
                }else{
                    $this->flashMessenger()->addErrorMessage('New ubislevel can not be created.');
                    $this->redirect()->toRoute("admin/manage-ubislevel/add-ubislevel");
                }
            }else{
                // !d($retmsg);die();
                if($logout){
                    $this->redirect()->toRoute("app/auth",['action'=>'logout']);
                }else{
                    $this->flashMessenger()->addInfoMessage($retmsg);
                    $this->redirect()->toRoute("admin/manage-ubislevel/add-ubislevel");
                }
            }
        }else{
            $this->flashMessenger()->addInfoMessage($ret['msg']);
            $this->redirect()->toRoute("admin/manage-ubislevel/add-ubislevel");
        }
    }
    
    public function mgtubisleveleditAction(){
        $this->layout()->setVariable('active_menu', "topmenu1");
    	$pRoute = $this->params()->fromRoute();
    	$pHeader = $this->params()->fromHeader();
        // d($pRoute,$pHeader);//die();
        $ubislevel_mdl = $this->container->get('App\Model\UbislevelModel');
        $tblSchema = $ubislevel_mdl->getTableSchema();
        $ubislevel = $ubislevel_mdl->getUbislevelByCode(['code'=> $pRoute['code']],false);
        // !d($ubislevel);//die();
        $opt = [
            'code'=>[
                'ICON'=> 'fas fa-key text-default',
                'DEFAULT' => $ubislevel['code'],
                'WEIGHT' => 0,
                'ATTR' => [
                    'readonly'=>true,
                    'primary_key'=>true
                ],
                'IS_NULLABLE'=>false
            ],
            'name'=>[
                'ICON'=> 'fas fa-tags text-default',
                'DEFAULT' => $ubislevel['name'],
                'WEIGHT' => 1,
            ],
            'status'=>[
                'ICON'=> 'fas fa-info-circle text-default',
                'TYPE'=> 'toggle',
                'ATTR' => [
                    'checked'=>($ubislevel['status']==="1")?true:false
                ],
                'DEFAULT' => $ubislevel['status'],
                'WEIGHT' => 2,
            ]
        ];
        $csrfForm = $this->container->get('csrfForm');
        $tblSchema['csrf'] = $this->DataGenerator()->createNewField('csrf',$csrfForm->get('csrf')->getValue());
        $tblSchema['csrf']['TYPE'] = "csrf";
        $tblSchema['csrf']['IS_NULLABLE'] = false;
        $tblSchema['blank1'] = $this->DataGenerator()->createNewField('blank1','');
        $tblSchema['blank1']['TYPE'] = "content";
        $tblSchema['blank1']['IS_NULLABLE'] = true;
        $tblSchema['blank1']['WEIGHT'] = 3;
        $tblSchema['blank1']['ICON'] = "";
        $tblSchema['blank1']['LABEL'] = "";

        $opt2 = [];
        // !d($opt);die();
        $schema = $this->DataGenerator()->schemaBuilder($tblSchema,$opt,$opt2);
        // !d($opt,$schema);die();
        if($this->getRequest()->isPost()){
            $this->processEditUbislevel($schema);
        }

        return [
            'tblSchema' => $schema
        ];
    }

    private function processEditUbislevel($schema){
        $pPost = $this->params()->fromPost();
        // !d($pPost);//die();
        // $par = ArrayUtils::merge($pPost, $pQuery);
        $ret = $this->DataGenerator()->schemaChecking($schema,$pPost);
        $setField = $this->DataGenerator()->getUpdateField($schema);
        // !d($setField);die();
        // !d($schema);die();
        if($ret['msg']==="VALID"){
            $valid = true;
            $logout = false;
            $validcode = true;
            $data = $ret['data'];
            // !d($ret['data']);die();
            if(!isset($data['code']) || $data['code']===""){
                $valid = false;
                $retmsg = "Sorry we can't find ubislevel without valid CODE";
                $validcode = false;
            }
            
            if($valid && isset($data['csrf'])){
                // !d($data['csrf']);die();
                $csrfForm = $this->container->get('csrfForm');
                $csrfForm->setData($data);
                // !d($data,$csrfForm->isValid());die();
                if(!$csrfForm->isValid()){
                    // $retmsg = "You can not inject this web!";
                    $valid = false;
                    // $this->flashMessenger()->addErrorMessage($retmsg);
                    $logout = true;
                }
            }else if(!isset($data['csrf'])){
                $valid = false;
                $logout = true;
            }

            // !d($valid,$logout,$retmsg);die();
            if($valid){
                // $dbsys = $this->container->get('db-sys');
                $cond = [
                    'code'=>$data['code']
                ];

                $set = array_filter(
                    $data,
                    function ($key) use ($setField) {
                        return in_array($key, $setField) ;
                    },
                    ARRAY_FILTER_USE_KEY
                );

                $set['status'] = isset($set['status']) && $set['status']!="" ? 1 : 0;
                // !d($set,$data);die();
                // !d($cond,$set,$data);die();
                $ret = $this->DataGenerator()->updateSQLExecute('db-sys','_ubis_level',$cond,$set);
                // !d($cond,$set,$ret);die();
                if ($ret['ret']) {
                    $this->flashMessenger()->addSuccessMessage('Ubislevel (Code:'.$data['code'].') has been updated.');
                    $this->redirect()->toRoute("admin/manage-ubislevel/edit-ubislevel",["code"=>$data['code']]);
                }else{
                    $this->flashMessenger()->addErrorMessage('Ubislevel (Code:'.$data['code'].') can not be updated.');
                    $this->redirect()->toRoute("admin/manage-ubislevel/edit-ubislevel",["code"=>$data['code']]);
                }
            }else{
                // !d($retmsg);die();
                if($logout){
                    $this->redirect()->toRoute("app/auth",['action'=>'logout']);
                }else if(!$validcode){
                    $this->flashMessenger()->addInfoMessage($retmsg);
                    $this->redirect()->toRoute("admin/manage-ubislevel");
                }else{
                    $this->flashMessenger()->addInfoMessage($retmsg);
                    $this->redirect()->toRoute("admin/manage-ubislevel/edit-ubislevel",["code"=>$data['code']]);
                }
            }
        }else{
            $this->flashMessenger()->addInfoMessage($ret['msg']);
            $this->redirect()->toRoute("admin/manage-ubislevel/add-ubislevel");
        }
    }
    
    public function listmenuAction(){
        $this->layout()->setVariable('active_menu', "topmenu1");
        $menu_mdl = $this->container->get('App\Model\MenuModel');
        $menus = $menu_mdl->getAllMenu(false);
        $arr_keys = [];
        if(count($menus)>0){
            $arr_keys = [""];
            $arr_tmp = array_keys($menus[0]);
            foreach($arr_tmp as $k=>$v){
                $v_tmp = str_replace("_"," ",$v);
                $v_tmp = strtoupper($v_tmp);
                $arr_keys[$v] = $v_tmp;
            }
        }
        // d($menus,$arr_keys);

        $csrfForm = $this->container->get('csrfForm');
        $csrf = $csrfForm->get('csrf')->getValue();
        return [
            'csrf' => $csrf,
            'menus' => $menus,
            'arr_keys' => $arr_keys
        ];
    }
    
    public function mgtmenuaddAction(){
        $this->layout()->setVariable('active_menu', "topmenu1");
        $menu_mdl = $this->container->get('App\Model\MenuModel');
        $tblSchema = $menu_mdl->getTableSchema();
        $opt = [
            'module'=>[
                'ICON'=> 'fas fa-project-diagram text-default',
                'TYPE'=> 'select',
                'DATA'=>[
                    'TYPE'=>'model',
                    'MODEL'=>'App\Model\SysModel',
                    'FUNCTION'=>'selectAllModulename',
                    'PARAM'=>[]
                ],
                'WEIGHT' => 1,
            ],
            'layout'=>[
                'ICON'=> 'fas fa-layer-group text-default',
                'TYPE'=> 'select',
                'DATA'=>[
                    'TYPE'=>'array',
                    'LIST'=>[
                        ['val'=>'notika','label'=>'NOTIKA'],['val'=>'hogo','label'=>'HOGO'],['val'=>'','label'=>'DEFAULT']
                    ]
                ],
                'WEIGHT' => 2,
            ],
            'title'=>[
                'ICON'=> 'fas fa-tags text-default',
                'WEIGHT' => 3,
            ],
            'icon'=>[
                'ICON'=> 'fas fa-icons text-default',
                'TYPE'=> 'select',
                'DATA'=>[
                    'TYPE'=>'model',
                    'MODEL'=>'App\Model\SysModel',
                    'FUNCTION'=>'selectAllFontAwesome',
                    'PARAM'=>[]
                ],
                'WEIGHT' => 8,
            ],
            'parent'=>[
                'ICON'=> 'fas fa-project-diagram text-default',
                'TYPE'=> 'select',
                'DATA'=>[
                    'TYPE'=>'model',
                    'MODEL'=>'App\Model\SysModel',
                    'FUNCTION'=>'selectAllMenu',
                    'PARAM'=>[]
                ],
                'WEIGHT' => 5,
            ],
            'status'=>[
                'ICON'=> 'fas fa-info-circle text-default',
                'TYPE'=> 'toggle',
                'ATTR' => [
                    'checked'=>true
                ],
                'WEIGHT' => 6,
            ],
            'priority'=>[
                'ICON'=> 'fas fa-sort-numeric-up text-default',
                'TYPE'=> 'select',
                'DATA'=>[
                    'TYPE'=>'array',
                    'LIST'=>[
                        ['val'=>1,'label'=>1],['val'=>2,'label'=>2],['val'=>3,'label'=>3],
                        ['val'=>4,'label'=>4],['val'=>5,'label'=>5],['val'=>6,'label'=>6],['val'=>7,'label'=>7],
                        ['val'=>8,'label'=>8],['val'=>9,'label'=>9],['val'=>10,'label'=>10]
                    ]
                ],
                'WEIGHT' => 7,
            ],
            'desc'=>[
                'ICON'=> 'fas fa-info-circle text-default',
                'TYPE'=> 'textarea',
                'WEIGHT' => 4,
            ],
            'route'=>[
                'ICON'=> 'fas fa-location-arrow text-default',
                'TYPE'=> 'select',
                'DATA'=>[
                    'TYPE'=>'model',
                    'MODEL'=>'App\Model\SysModel',
                    'FUNCTION'=>'selectAllRoute',
                    'PARAM'=>[]
                ],
                'WEIGHT' => 10,
            ],
            'param'=>[
                'ICON'=> 'fas fa-location-arrow text-default',
                'TYPE'=> 'textarea',
                'WEIGHT' => 11,
                'ADDLABEL' => ' (JSON Format)'
            ],
            'query'=>[
                'ICON'=> 'fas fa-location-arrow text-default',
                'TYPE'=> 'textarea',
                'WEIGHT' => 12,
                'ADDLABEL' => ' (JSON Format)'
            ],
            'url'=>[
                'ICON'=> 'fas fa-compass text-default',
                // 'TYPE'=> 'url',
                'WEIGHT' => 9,
            ],
        ];
        $csrfForm = $this->container->get('csrfForm');
        // !d($csrfForm->get('csrf')->getValue());die();
        $tblSchema['csrf'] = $this->DataGenerator()->createNewField('csrf',$csrfForm->get('csrf')->getValue());
        $tblSchema['csrf']['TYPE'] = "csrf";
        // !d($tblSchema['csrf']);die();

        $opt2 = [];
        $schema = $this->DataGenerator()->schemaBuilder($tblSchema,$opt,$opt2);
        // !d($schema);die();
        // !d($this->getRequest());die();
        if($this->getRequest()->isPost()){
            $this->processAddMenu($schema);
        }

        return [
            'tblSchema' => $schema
        ];
    }

    private function processAddMenu($schema){
        $pPost = $this->params()->fromPost();
        // !d($pHeader,$pPost,$schema);die();
        // $par = ArrayUtils::merge($pPost, $pQuery);
        $ret = $this->DataGenerator()->schemaChecking($schema,$pPost);
        // !d($ret);die();
        if($ret['msg']==="VALID"){
            $valid = true;
            $logout = false;
            $data = $ret['data'];
            // !d($ret['data']);die();
            if($data['param']!=""){
                $arrtmp = json_decode($data['param']);
                if($arrtmp===false || $arrtmp===null){
                    $retmsg = "Please input valid PARAM value (in valid JSON format)";
                    $valid = false;
                }
            }
            
            if($valid && $data['query']!=""){
                $arrtmp = json_decode($data['query']);
                if($arrtmp===false || $arrtmp===null){
                    $retmsg = "Please input valid QUERY value (in valid JSON format)";
                    $valid = false;
                }
            }
            
            if($valid && isset($data['csrf'])){
                // !d($data['csrf']);die();
                $csrfForm = $this->container->get('csrfForm');
                $csrfForm->setData($data);
                // !d($data,$form->isValid());die();
                if(!$csrfForm->isValid()){
                    // $retmsg = "You can not inject this web!";
                    $valid = false;
                    // $this->flashMessenger()->addErrorMessage($retmsg);
                    $logout = true;
                }
            }else if(!isset($data['csrf'])){
                $valid = false;
                $logout = true;
            }

            if($valid){
                // $dbsys = $this->container->get('db-sys');
                $data['status'] = isset($data['status']) && $data['status']!="" ? 1 : 0;
                $ret = $this->DataGenerator()->insertSQLExecute('db-sys','_menu',$schema,$data);
                // !d($schema,$data,$ret);die();
                if ($ret['ret']) {
                    $this->flashMessenger()->addSuccessMessage('New menu has been created.');
                    $this->redirect()->toRoute("admin/manage-menu");
                }else{
                    $this->flashMessenger()->addErrorMessage('New menu can not be created.');
                    $this->redirect()->toRoute("admin/manage-menu/add-menu");
                }
            }else{
                // !d($retmsg);die();
                if($logout){
                    $this->redirect()->toRoute("app/auth",['action'=>'logout']);
                }else{
                    $this->flashMessenger()->addInfoMessage($retmsg);
                    $this->redirect()->toRoute("admin/manage-menu/add-menu");
                }
            }
        }else{
            $this->flashMessenger()->addInfoMessage($ret['msg']);
            $this->redirect()->toRoute("admin/manage-menu/add-menu");
        }
    }
    
    public function mgtmenueditAction(){
        $this->layout()->setVariable('active_menu', "topmenu1");
    	$pRoute = $this->params()->fromRoute();
    	$pHeader = $this->params()->fromHeader();
        // d($pRoute,$pHeader);//die();
        $menu_mdl = $this->container->get('App\Model\MenuModel');
        $tblSchema = $menu_mdl->getTableSchema();
        $menu = $menu_mdl->getMenuById(['id'=> $pRoute['mid']],false);
        // !d($menu);//die();
        $opt = [
            'mid'=>[
                'ICON'=> 'fas fa-key text-default',
                'DEFAULT' => $menu['id'],
                'WEIGHT' => 0,
                'ATTR' => [
                    'readonly'=>true
                ],
                'IS_NULLABLE'=>false
            ],
            'module'=>[
                'ICON'=> 'fas fa-project-diagram text-default',
                'TYPE'=> 'select',
                'DEFAULT' => $menu['module'],
                'DATA'=>[
                    'TYPE'=>'model',
                    'MODEL'=>'App\Model\SysModel',
                    'FUNCTION'=>'selectAllModulename',
                    'PARAM'=>[]
                ],
                'WEIGHT' => 2,
            ],
            'layout'=>[
                'ICON'=> 'fas fa-layer-group text-default',
                'TYPE'=> 'select',
                'DEFAULT' => $menu['layout'],
                'DATA'=>[
                    'TYPE'=>'array',
                    'LIST'=>[
                        ['val'=>'notika','label'=>'NOTIKA'],['val'=>'hogo','label'=>'HOGO'],['val'=>'','label'=>'DEFAULT']
                    ]
                ],
                'WEIGHT' => 3,
            ],
            'title'=>[
                'ICON'=> 'fas fa-tags text-default',
                'DEFAULT' => $menu['title'],
                'WEIGHT' => 1,
            ],
            'icon'=>[
                'ICON'=> 'fas fa-icons text-default',
                'TYPE'=> 'select',
                'DEFAULT' => $menu['icon'],
                'DATA'=>[
                    'TYPE'=>'model',
                    'MODEL'=>'App\Model\SysModel',
                    'FUNCTION'=>'selectAllFontAwesome',
                    'PARAM'=>[]
                ],
                'WEIGHT' => 7,
            ],
            'parent'=>[
                'ICON'=> 'fas fa-project-diagram text-default',
                'TYPE'=> 'select',
                'DEFAULT' => $menu['parent'],
                'DATA'=>[
                    'TYPE'=>'model',
                    'MODEL'=>'App\Model\SysModel',
                    'FUNCTION'=>'selectAllMenu',
                    'PARAM'=>[]
                ],
                'WEIGHT' => 4,
            ],
            'status'=>[
                'ICON'=> 'fas fa-info-circle text-default',
                'TYPE'=> 'toggle',
                'ATTR' => [
                    'checked'=>($menu['status']==="1")?true:false
                ],
                'DEFAULT' => $menu['status'],
                'WEIGHT' => 6,
            ],
            'priority'=>[
                'ICON'=> 'fas fa-sort-numeric-up text-default',
                'TYPE'=> 'select',
                'DEFAULT' => $menu['priority'],
                'DATA'=>[
                    'TYPE'=>'array',
                    'LIST'=>[
                        ['val'=>1,'label'=>1],['val'=>2,'label'=>2],['val'=>3,'label'=>3],
                        ['val'=>4,'label'=>4],['val'=>5,'label'=>5],['val'=>6,'label'=>6],['val'=>7,'label'=>7],
                        ['val'=>8,'label'=>8],['val'=>9,'label'=>9],['val'=>10,'label'=>10]
                    ]
                ],
                'WEIGHT' => 5,
            ],
            'desc'=>[
                'ICON'=> 'fas fa-info-circle text-default',
                'TYPE'=> 'textarea',
                'DEFAULT' => $menu['desc'],
                'WEIGHT' => 8,
            ],
            'route'=>[
                'ICON'=> 'fas fa-location-arrow text-default',
                'TYPE'=> 'select',
                'DEFAULT' => $menu['route'],
                'DATA'=>[
                    'TYPE'=>'model',
                    'MODEL'=>'App\Model\SysModel',
                    'FUNCTION'=>'selectAllRoute',
                    'PARAM'=>[]
                ],
                'WEIGHT' => 10,
            ],
            'param'=>[
                'ICON'=> 'fas fa-location-arrow text-default',
                'TYPE'=> 'textarea',
                'DEFAULT' => $menu['param'],
                'WEIGHT' => 11,
                'ADDLABEL' => ' (JSON Format)'
            ],
            'query'=>[
                'ICON'=> 'fas fa-location-arrow text-default',
                'TYPE'=> 'textarea',
                'DEFAULT' => $menu['query'],
                'WEIGHT' => 12,
                'ADDLABEL' => ' (JSON Format)'
            ],
            'url'=>[
                'ICON'=> 'fas fa-compass text-default',
                // 'TYPE'=> 'url',
                'DEFAULT' => $menu['url'],
                'WEIGHT' => 9,
            ],
        ];
        $csrfForm = $this->container->get('csrfForm');
        $tblSchema['csrf'] = $this->DataGenerator()->createNewField('csrf',$csrfForm->get('csrf')->getValue());
        $tblSchema['csrf']['TYPE'] = "csrf";
        $tblSchema['csrf']['IS_NULLABLE'] = false;
        $tblSchema['mid'] = $this->DataGenerator()->createNewField('mid',$menu['id']);
        $tblSchema['mid']['TYPE'] = "text";
        $tblSchema['mid']['IS_NULLABLE'] = false;
        $tblSchema['blank1'] = $this->DataGenerator()->createNewField('blank1','');
        $tblSchema['blank1']['TYPE'] = "content";
        $tblSchema['blank1']['IS_NULLABLE'] = true;
        $tblSchema['blank1']['WEIGHT'] = 8;
        $tblSchema['blank1']['ICON'] = "";
        $tblSchema['blank1']['LABEL'] = "";

        $opt2 = [];
        $schema = $this->DataGenerator()->schemaBuilder($tblSchema,$opt,$opt2);
        // d($schema);//die();
        if($this->getRequest()->isPost()){
            $this->processEditMenu($schema);
        }

        return [
            'tblSchema' => $schema
        ];
    }

    private function processEditMenu($schema){
        $pPost = $this->params()->fromPost();
        // !d($pPost);//die();
        // $par = ArrayUtils::merge($pPost, $pQuery);
        $ret = $this->DataGenerator()->schemaChecking($schema,$pPost);
        $setField = $this->DataGenerator()->getUpdateField($schema);
        // !d($setField);die();
        // !d($schema);die();
        if($ret['msg']==="VALID"){
            $valid = true;
            $logout = false;
            $validmenu = true;
            $data = $ret['data'];
            // !d($ret['data']);die();
            if(!isset($data['mid']) || $data['mid']===""){
                $valid = false;
                $retmsg = "Sorry we can't find menu without valid ID";
                $validmenu = false;
            }

            if($data['param']!=""){
                $arrtmp = json_decode($data['param']);
                if($arrtmp===false || $arrtmp===null){
                    $retmsg = "Please input valid PARAM value (in valid JSON format)";
                    $valid = false;
                }
            }
            
            if($valid && $data['query']!=""){
                $arrtmp = json_decode($data['query']);
                if($arrtmp===false || $arrtmp===null){
                    $retmsg = "Please input valid QUERY value (in valid JSON format)";
                    $valid = false;
                }
            }
            
            if($valid && isset($data['csrf'])){
                // !d($data['csrf']);die();
                $csrfForm = $this->container->get('csrfForm');
                $csrfForm->setData($data);
                // !d($data,$csrfForm->isValid());die();
                if(!$csrfForm->isValid()){
                    // $retmsg = "You can not inject this web!";
                    $valid = false;
                    // $this->flashMessenger()->addErrorMessage($retmsg);
                    $logout = true;
                }
            }else if(!isset($data['csrf'])){
                $valid = false;
                $logout = true;
            }

            // !d($valid,$logout,$retmsg);die();
            if($valid){
                // $dbsys = $this->container->get('db-sys');
                $cond = [
                    'id'=>$data['mid']
                ];

                $set = array_filter(
                    $data,
                    function ($key) use ($setField) {
                        return in_array($key, $setField);
                    },
                    ARRAY_FILTER_USE_KEY
                );

                $set['status'] = isset($set['status']) && $set['status']!="" ? 1 : 0;
                // !d($set,$data);die();
                // !d($setField,$cond,$set,$data);die();
                // if($set['parent']=)
                $ret = $this->DataGenerator()->updateSQLExecute('db-sys','_menu',$cond,$set);
                // !d($ret);die();
                if ($ret['ret']) {
                    $this->flashMessenger()->addSuccessMessage('Menu (ID:'.$data['mid'].') has been updated.');
                    $this->redirect()->toRoute("admin/manage-menu/edit-menu",["mid"=>$data['mid']]);
                }else{
                    $this->flashMessenger()->addErrorMessage('Menu (ID:'.$data['mid'].') can not be updated.');
                    $this->redirect()->toRoute("admin/manage-menu/edit-menu",["mid"=>$data['mid']]);
                }
            }else{
                // !d($retmsg);die();
                if($logout){
                    $this->redirect()->toRoute("app/auth",['action'=>'logout']);
                }else if(!$validmenu){
                    $this->flashMessenger()->addInfoMessage($retmsg);
                    $this->redirect()->toRoute("admin/manage-menu");
                }else{
                    $this->flashMessenger()->addInfoMessage($retmsg);
                    $this->redirect()->toRoute("admin/manage-menu/edit-menu",["mid"=>$data['mid']]);
                }
            }
        }else{
            $this->flashMessenger()->addInfoMessage($ret['msg']);
            $this->redirect()->toRoute("admin/manage-menu/add-menu");
        }
    }
    
    public function listrolemenuAction(){
        $this->layout()->setVariable('active_menu', "topmenu1");
        $menu_mdl = $this->container->get('App\Model\MenuModel');
        $menus = $menu_mdl->getAllRoleMenu(false);
        $arr_keys = [];
        if(count($menus)>0){
            $arr_keys = [""];
            $arr_tmp = array_keys($menus[0]);
            foreach($arr_tmp as $k=>$v){
                $v_tmp = str_replace("_"," ",$v);
                $v_tmp = strtoupper($v_tmp);
                $arr_keys[$v] = $v_tmp;
            }
        }
        // d($menus,$arr_keys);

        $csrfForm = $this->container->get('csrfForm');
        $csrf = $csrfForm->get('csrf')->getValue();
        return [
            'csrf' => $csrf,
            'menus' => $menus,
            'arr_keys' => $arr_keys
        ];
    }
}
