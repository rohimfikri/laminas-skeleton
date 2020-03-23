<?php
namespace App\Model;

use InvalidArgumentException;
use RuntimeException;
use Laminas\Db\Adapter\AdapterInterface;
use Laminas\Db\Adapter\Driver\ResultInterface;
use Laminas\Db\ResultSet\ResultSet;
use Laminas\Db\Sql\Sql;
use Laminas\Authentication\AuthenticationService;
use Laminas\Session\SessionManager;
use Zend\Debug\Debug;
use Laminas\Stdlib\ArrayUtils;

class SysModel{
    private $config;
    private $container;
    private $authService;
    private $sessionManager;
    private $dbSys;
    private $dataCache;

    public function __construct($container,$config){
        $this->container = $container;
        $this->config = $config;
        $this->authService = $container->get(AuthenticationService::class); 
        $this->sessionManager = $container->get(SessionManager::class);
        $this->dbSys = $container->get("db-sys");
        $this->dataCache = $container->get("data-file");
    }

    private function extractRouteName(&$data,$parent,$route){
        foreach ($route as $k=>$v){
            $par = $parent.'/'.$k;
            if(isset($v['options']) && isset($v['options']['route']) && isset($v['options']['defaults'])
            && isset($v['options']['defaults']['action'])){
                if (!in_array($par, $data)) {
                    $data[] = $par;
                }
            }
            if(isset($v['child_routes']) && count($v['child_routes'])>0){
                $this->extractRouteName($data,$par,$v['child_routes']);
            }
        }
    }

    public function selectAllUbislevel($param = [], $fromcache = true){
        if(!isset($param['sql']))$param['sql'] = [];
        if(!isset($param['exclude']))$param['exclude'] = [];
        $method = str_replace(["\\","::"],"_",__METHOD__);
        $salt = "cache-data-".$method;
        $crypted1 = hash('sha1', $salt);
        $crypted2 = hash('sha1', json_encode($param['sql']));
        $key = $method.'_'.$crypted1.'_'.$crypted2;
        // !d($salt,$crypted1,$crypted2,$key);die();
        // !d($this->config['router']['routes']);die();
        $res = [];
        $res[] = [
            "val"=>"",
            "label"=>"NULL",
        ];
        if(isset($_GET['fromcache']) && ($_GET['fromcache']==='0' || $_GET['fromcache']==="false")) 
            $fromcache = false;
        if ($this->dataCache->hasItem($key) && $fromcache){
          $data = $this->dataCache->getItem($key);
          // Debug::dump($data);die("CACHE");
          $tmp = json_decode($data,true);
          foreach ($tmp as $k=>$v) {
              if (!in_array($v['code'], $param['exclude'])) {
                  $res[] = [
                      "val"=>$v['code'],
                      "label"=>$v['name'],
                  ];
              }
          }
          return $res;
        }else{
            $sql = "select code,name from _ubis_level where status = 1 order by code,name";

            $statement = $this->dbSys->createStatement($sql, $param['sql']);
            // Debug::dump($statement);die();
            $result    = $statement->execute();
            // Debug::dump($result);die();
            // Debug::dump($result);//die();
            if (! $result instanceof ResultInterface || ! $result->isQueryResult()) {
                // Debug::dump($resultSet);die('fff');
                return $res;
            } else {
                $resultSet = new ResultSet();
                $resultSet->initialize($result);
                // Debug::dump($resultSet->toArray());die();
                $tmp = $resultSet->toArray();
                $this->dataCache->removeItem($key);
                $this->dataCache->addItem($key, json_encode($tmp));
                foreach ($tmp as $k=>$v) {
                    if (!in_array($v['code'], $param['exclude'])) {
                        $res[] = [
                            "val"=>$v['code'],
                            "label"=>$v['name'],
                        ];
                    }
                }
                return $res;
            }
        }
    }

    public function selectAllMenu($param = [], $fromcache = true){
        if(!isset($param['sql']))$param['sql'] = [];
        if(!isset($param['exclude']))$param['exclude'] = [];
        $method = str_replace(["\\","::"],"_",__METHOD__);
        $salt = "cache-data-".$method;
        $crypted1 = hash('sha1', $salt);
        $crypted2 = hash('sha1', json_encode($param['sql']));
        $key = $method.'_'.$crypted1.'_'.$crypted2;
        // !d($salt,$crypted1,$crypted2,$key);die();
        // !d($this->config['router']['routes']);die();
        $res = [];
        $res[] = [
            "val"=>"",
            "label"=>"NULL",
        ];
        if(isset($_GET['fromcache']) && ($_GET['fromcache']==='0' || $_GET['fromcache']==="false")) 
            $fromcache = false;
        if ($this->dataCache->hasItem($key) && $fromcache){
          $data = $this->dataCache->getItem($key);
          // Debug::dump($data);die("CACHE");
          $tmp = json_decode($data,true);
          foreach ($tmp as $k=>$v) {
              if (!in_array($v['code'], $param['exclude'])) {
                  $res[] = [
                      "val"=>$v['code'],
                      "label"=>$v['name'],
                  ];
              }
          }
          return $res;
        }else{
            $sql = "select id,title from _menu where status=1 and (route = '' or route is null) and url = '#' order by module,layout,title,route,id,priority";

            $statement = $this->dbSys->createStatement($sql, $param['sql']);
            // Debug::dump($statement);die();
            $result    = $statement->execute();
            // Debug::dump($result);die();
            // Debug::dump($result);//die();
            if (! $result instanceof ResultInterface || ! $result->isQueryResult()) {
                // Debug::dump($resultSet);die('fff');
                return $res;
            } else {
                $resultSet = new ResultSet();
                $resultSet->initialize($result);
                // Debug::dump($resultSet->toArray());die();
                $tmp = $resultSet->toArray();
                $this->dataCache->removeItem($key);
                $this->dataCache->addItem($key, json_encode($tmp));
                foreach ($tmp as $k=>$v) {
                    if (!in_array($v['id'], $param['exclude'])) {
                        $res[] = [
                            "val"=>$v['id'],
                            "label"=>$v['title'],
                        ];
                    }
                }
                return $res;
            }
        }
    }

    public function selectAllModule($param = [], $fromcache = true){
        if(!isset($param['sql']))$param['sql'] = [];
        if(!isset($param['exclude']))$param['exclude'] = [];
        $method = str_replace(["\\","::"],"_",__METHOD__);
        $salt = "cache-data-".$method;
        $crypted1 = hash('sha1', $salt);
        $crypted2 = hash('sha1', json_encode($param['sql']));
        $key = $method.'_'.$crypted1.'_'.$crypted2;
        // !d($salt,$crypted1,$crypted2,$key);die();
        // !d($this->config['router']['routes']);die();
        $res = [];
        $res[] = [
            "val"=>"",
            "label"=>"NULL",
        ];
        if(isset($_GET['fromcache']) && ($_GET['fromcache']==='0' || $_GET['fromcache']==="false")) 
            $fromcache = false;
        if ($this->dataCache->hasItem($key) && $fromcache){
          $data = $this->dataCache->getItem($key);
          // Debug::dump($data);die("CACHE");
          $tmp = json_decode($data,true);
          foreach ($tmp as $k=>$v) {
              if (!in_array($v['code'], $param['exclude'])) {
                  $res[] = [
                      "val"=>$v['code'],
                      "label"=>$v['name'],
                  ];
              }
          }
          return $res;
        }else{
            $sql = "select id,name from _module  where status = 1 and id!=0 order by name,id";

            $statement = $this->dbSys->createStatement($sql, $param['sql']);
            // Debug::dump($statement);die();
            $result    = $statement->execute();
            // Debug::dump($result);die();
            // Debug::dump($result);//die();
            if (! $result instanceof ResultInterface || ! $result->isQueryResult()) {
                // Debug::dump($resultSet);die('fff');
                return $res;
            } else {
                $resultSet = new ResultSet();
                $resultSet->initialize($result);
                // Debug::dump($resultSet->toArray());die();
                $tmp = $resultSet->toArray();
                $this->dataCache->removeItem($key);
                $this->dataCache->addItem($key, json_encode($tmp));
                foreach ($tmp as $k=>$v) {
                    if (!in_array($v['id'], $param['exclude'])) {
                        $res[] = [
                            "val"=>$v['id'],
                            "label"=>$v['name'],
                        ];
                    }
                }
                return $res;
            }
        }
    }

    public function selectAllModulename($param = [], $fromcache = true){
        if(!isset($param['sql']))$param['sql'] = [];
        if(!isset($param['exclude']))$param['exclude'] = [];
        $method = str_replace(["\\","::"],"_",__METHOD__);
        $salt = "cache-data-".$method;
        $crypted1 = hash('sha1', $salt);
        $crypted2 = hash('sha1', json_encode($param['sql']));
        $key = $method.'_'.$crypted1.'_'.$crypted2;
        // !d($salt,$crypted1,$crypted2,$key);die();
        // !d($this->config['router']['routes']);die();
        $res = [];
        $res[] = [
            "val"=>"",
            "label"=>"NULL",
        ];
        if(isset($_GET['fromcache']) && ($_GET['fromcache']==='0' || $_GET['fromcache']==="false")) 
            $fromcache = false;
        if ($this->dataCache->hasItem($key) && $fromcache){
          $data = $this->dataCache->getItem($key);
          // Debug::dump($data);die("CACHE");
          $tmp = json_decode($data,true);
          foreach ($tmp as $k=>$v) {
              if (!in_array($v['name'], $param['exclude'])) {
                  $res[] = [
                      "val"=>$v['name'],
                      "label"=>$v['name'],
                  ];
              }
          }
          return $res;
        }else{
            $sql = "select id,name from _module  where status = 1 and id!=0 order by name,id";

            $statement = $this->dbSys->createStatement($sql, $param['sql']);
            // Debug::dump($statement);die();
            $result    = $statement->execute();
            // Debug::dump($result);die();
            // Debug::dump($result);//die();
            if (! $result instanceof ResultInterface || ! $result->isQueryResult()) {
                // Debug::dump($resultSet);die('fff');
                return $res;
            } else {
                $resultSet = new ResultSet();
                $resultSet->initialize($result);
                // Debug::dump($resultSet->toArray());die();
                $tmp = $resultSet->toArray();
                $this->dataCache->removeItem($key);
                $this->dataCache->addItem($key, json_encode($tmp));
                foreach ($tmp as $k=>$v) {
                    if (!in_array($v['name'], $param['exclude'])) {
                        $res[] = [
                            "val"=>$v['name'],
                            "label"=>$v['name'],
                        ];
                    }
                }
                return $res;
            }
        }
    }

    public function selectAllUbis($param = [], $fromcache = true){
        if(!isset($param['sql']))$param['sql'] = [];
        if(!isset($param['exclude']))$param['exclude'] = [];
        $method = str_replace(["\\","::"],"_",__METHOD__);
        $salt = "cache-data-".$method;
        $crypted1 = hash('sha1', $salt);
        $crypted2 = hash('sha1', json_encode($param['sql']));
        $key = $method.'_'.$crypted1.'_'.$crypted2;
        // !d($salt,$crypted1,$crypted2,$key);die();
        // !d($this->config['router']['routes']);die();
        $res = [];
        $res[] = [
            "val"=>"",
            "label"=>"NULL",
        ];
        if(isset($_GET['fromcache']) && ($_GET['fromcache']==='0' || $_GET['fromcache']==="false")) 
            $fromcache = false;
        if ($this->dataCache->hasItem($key) && $fromcache){
          $data = $this->dataCache->getItem($key);
          // Debug::dump($data);die("CACHE");
          $tmp = json_decode($data,true);
          foreach ($tmp as $k=>$v) {
              if (!in_array($v['code'], $param['exclude'])) {
                  $res[] = [
                      "val"=>$v['code'],
                      "label"=>$v['name'],
                  ];
              }
          }
          return $res;
        }else{
            $sql = "select code,name from _ubis where status = 1 order by code,name";

            $statement = $this->dbSys->createStatement($sql, $param['sql']);
            // Debug::dump($statement);die();
            $result    = $statement->execute();
            // Debug::dump($result);die();
            // Debug::dump($result);//die();
            if (! $result instanceof ResultInterface || ! $result->isQueryResult()) {
                // Debug::dump($resultSet);die('fff');
                return $res;
            } else {
                $resultSet = new ResultSet();
                $resultSet->initialize($result);
                // Debug::dump($resultSet->toArray());die();
                $tmp = $resultSet->toArray();
                $this->dataCache->removeItem($key);
                $this->dataCache->addItem($key, json_encode($tmp));
                foreach ($tmp as $k=>$v) {
                    if (!in_array($v['code'], $param['exclude'])) {
                        $res[] = [
                            "val"=>$v['code'],
                            "label"=>$v['name'],
                        ];
                    }
                }
                return $res;
            }
        }
    }

    public function selectAllFontAwesome($fromcache = true){
        $param = [
        ];
        $method = str_replace(["\\","::"],"_",__METHOD__);
        $salt = "cache-data-".$method;
        $crypted1 = hash('sha1', $salt);
        $crypted2 = hash('sha1', json_encode($param));
        $key = $method.'_'.$crypted1.'_'.$crypted2;
        // !d($salt,$crypted1,$crypted2,$key);die();
        // !d($this->config['router']['routes']);die();
        if(isset($_GET['fromcache']) && ($_GET['fromcache']==='0' || $_GET['fromcache']==="false")) 
            $fromcache = false;
        if ($this->dataCache->hasItem($key) && $fromcache){
          $data = $this->dataCache->getItem($key);
          // Debug::dump($data);die("CACHE");
          return json_decode($data,true);
        }else{
            $catfile = APP_PATH.DS.'public'.DS.'bower_components'.DS.'font-awesome'.DS.'metadata'.DS.'categories.yml';
            // die($catfile);
            $catyml = file_get_contents($catfile);
            $cat = yaml_parse($catyml);
            $sponsorfile = APP_PATH.DS.'public'.DS.'bower_components'.DS.'font-awesome'.DS.'metadata'.DS.'sponsors.yml';
            $sponsoryml = file_get_contents($sponsorfile);
            $sponsor = yaml_parse($sponsoryml);
            $data = ArrayUtils::merge($cat,$sponsor);
            // !d($cat,$sponsor,$data);die();

            $res = [];
            $icon = [];
            $res[] = [
                "val"=>"",
                "label"=>"NULL",
            ];
            foreach ($data as $k=>$v) {
                if (isset($v['icons'])) {
                    foreach ($v['icons'] as $k2=>$v2) {
                        if (!in_array($v2, $icon)) {
                            $icon[] = $v2;
                            $res[] = [
                                "val"=>'fas fa-'.$v2,
                                "label"=> $v2,
                                "data"=>[
                                    'icon'=>"fas fa-".$v2
                                ]
                            ];
                        }
                    }
                }
            }
            // !d($res);die();
            $this->dataCache->removeItem($key);
            $this->dataCache->addItem($key, json_encode($res));
            return $res;
        }
    }

    public function selectAllRoute($fromcache = true){
        $param = [
        ];
        $method = str_replace(["\\","::"],"_",__METHOD__);
        $salt = "cache-data-".$method;
        $crypted1 = hash('sha1', $salt);
        $crypted2 = hash('sha1', json_encode($param));
        $key = $method.'_'.$crypted1.'_'.$crypted2;
        // !d($salt,$crypted1,$crypted2,$key);die();
        // !d($this->config['router']['routes']);die();
        if(isset($_GET['fromcache']) && ($_GET['fromcache']==='0' || $_GET['fromcache']==="false")) 
            $fromcache = false;
        if ($this->dataCache->hasItem($key) && $fromcache){
          $data = $this->dataCache->getItem($key);
          // Debug::dump($data);die("CACHE");
          return json_decode($data,true);
        }else{
            $routes = $this->config['router']['routes'];
            $data = [];
            foreach ($routes as $k=>$v){
                if(isset($v['options']) && isset($v['options']['route']) && isset($v['options']['defaults'])
                && isset($v['options']['defaults']['action'])){
                    if (!in_array($k, $data)) {
                        $data[] = $k;
                    }
                }
                if(isset($v['child_routes']) && count($v['child_routes'])>0){
                    $this->extractRouteName($data,$k,$v['child_routes']);
                }
            }
            // !d($data);die();  

            $res = [];
            $res[] = [
                "val"=>"",
                "label"=>"NULL",
            ];
            foreach ($data as $k=>$v) {
                $res[] = [
                    "val"=>$v,
                    "label"=>$v,
                ];
            }
            // !d($res);die();
            $this->dataCache->removeItem($key);
            $this->dataCache->addItem($key, json_encode($res));
            return $res;
        }
    }
}