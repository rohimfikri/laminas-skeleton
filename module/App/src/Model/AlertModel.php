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

class AlertModel{
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

    public function getUnreadAlertByUidByLayoutByModule($uid, $layout, $module,$fromcache = true){
        if($uid==="" || $uid===null)
            return [];

        // !d(__METHOD__,__FUNCTION__);die();
        $param = [
            'uid'=>$uid,
            'layout'=>$layout,
            'module'=>$module
        ];
        $method = str_replace(["\\","::"],"_",__METHOD__);
        $salt = "cache-data-".$method;
        $crypted1 = hash('sha1', $salt);
        $crypted2 = hash('sha1', json_encode($param));
        $key = $method.'_'.$crypted1.'_'.$crypted2;
        // !d($salt,$crypted1,$crypted2,$key);die();
        if(isset($_GET['fromcache']) && ($_GET['fromcache']==='0' || $_GET['fromcache']==="false")) 
            $fromcache = false;
        if ($this->dataCache->hasItem($key) && $fromcache){
          $data = $this->dataCache->getItem($key);
          // Debug::dump($data);die("CACHE");
          return json_decode($data,true);
        }else{
            $sql = "CALL lam_sys.get_userunreadalert_byuid_layout_module(:uid,:theme,:module)";
            // die($sql);
            $statement = $this->dbSys->createStatement($sql, ["theme"=>$layout,"uid"=>$uid,"module"=>$module]);
            // Debug::dump($statement);die();
            $result    = $statement->execute();
            // Debug::dump($result);die();
            // Debug::dump($result);//die();
            if (! $result instanceof ResultInterface || ! $result->isQueryResult()) {
                // Debug::dump($resultSet);die('fff');
                return [];
            } else {
                $resultSet = new ResultSet();
                $resultSet->initialize($result);
                // Debug::dump($resultSet->toArray());die();
                $tmp = $resultSet->toArray();
                $this->dataCache->removeItem($key);
                $this->dataCache->addItem($key, json_encode($tmp));
                return $tmp;
            }
        }
    }
}