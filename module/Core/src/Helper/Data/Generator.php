<?php
namespace Core\Helper\Data;

use Laminas\Mvc\Controller\Plugin\AbstractPlugin;
use Laminas\Authentication\AuthenticationService;
use Laminas\Session\SessionManager;
use Laminas\Crypt\Password\Bcrypt;
use Zend\Debug\Debug;
use Laminas\Stdlib\ArrayUtils;

/**
 * This view helper class displays a menu bar.
 */

class Generator extends AbstractPlugin
{
    private $config;
    private $container;
    private $authService;
    private $sessionManager;
    
    public function __construct($container, $config)
    {
        $this->container = $container;
        $this->config = $config;
        $this->authService = $container->get(AuthenticationService::class);
        $this->sessionManager = $container->get(SessionManager::class);
    }

    public function getUpdateField($tblSchema){
        $schema = [];
        foreach($tblSchema as $k=>$v){
            if($v['EXTRA']==='auto_increment' || (isset($v['ATTR']) && isset($v['ATTR']['primary_key']) && $v['ATTR']['primary_key']) || 
            $v['COLUMN_DEFAULT']==='current_timestamp()' || $v['COLUMN_DEFAULT']==='now()' ||
            (isset($v['IS_ADD']) && $v['IS_ADD'])) continue;
            $schema[] = $v['COLUMN_NAME'];
        }
        // !d($schema);die();
        return $schema;
    }

    public function schemaBuilder($tblSchema,$opt,$opt2){
        $schema = [];
        foreach($tblSchema as $k=>$v){
            if(in_array($v['COLUMN_NAME'],$opt2))continue;
            $schema[$k] = $v;
            if(isset($opt[$v['COLUMN_NAME']])){
                if(isset($opt[$v['COLUMN_NAME']]['DEFAULT'])){
                    $schema[$k]['DEFAULT'] = $opt[$v['COLUMN_NAME']]['DEFAULT'];
                }
                if(isset($opt[$v['COLUMN_NAME']]['ATTR'])){
                    $schema[$k]['ATTR'] = $opt[$v['COLUMN_NAME']]['ATTR'];
                }
                if(isset($opt[$v['COLUMN_NAME']]['ICON'])){
                    $schema[$k]['ICON'] = $opt[$v['COLUMN_NAME']]['ICON'];
                }
                if(isset($opt[$v['COLUMN_NAME']]['TYPE'])){
                    $schema[$k]['TYPE'] = $opt[$v['COLUMN_NAME']]['TYPE'];
                }
                if(isset($opt[$v['COLUMN_NAME']]['DATA'])){
                    $schema[$k]['DATA'] = $opt[$v['COLUMN_NAME']]['DATA'];
                }
                if(isset($opt[$v['COLUMN_NAME']]['ADDLABEL'])){
                    $schema[$k]['ADDLABEL'] = $opt[$v['COLUMN_NAME']]['ADDLABEL'];
                }
                if(isset($opt[$v['COLUMN_NAME']]['LABEL'])){
                    $schema[$k]['LABEL'] = $opt[$v['COLUMN_NAME']]['LABEL'];
                }
                if(isset($opt[$v['COLUMN_NAME']]['ADDCLASS'])){
                    $schema[$k]['ADDCLASS'] = $opt[$v['COLUMN_NAME']]['ADDCLASS'];
                }
                if(isset($opt[$v['COLUMN_NAME']]['CLASS'])){
                    $schema[$k]['CLASS'] = $opt[$v['COLUMN_NAME']]['CLASS'];
                }
                if(isset($opt[$v['COLUMN_NAME']]['IS_ADD'])){
                    $schema[$k]['IS_ADD'] = $opt[$v['COLUMN_NAME']]['IS_ADD'];
                }
                if(isset($opt[$v['COLUMN_NAME']]['IS_BCRYPT'])){
                    $schema[$k]['IS_BCRYPT'] = $opt[$v['COLUMN_NAME']]['IS_BCRYPT'];
                }
                if(isset($opt[$v['COLUMN_NAME']]['WEIGHT'])){
                    $schema[$k]['WEIGHT'] = $opt[$v['COLUMN_NAME']]['WEIGHT'];
                }
                if(isset($opt[$v['COLUMN_NAME']]['IS_NULLABLE'])){
                    $schema[$k]['IS_NULLABLE'] = $opt[$v['COLUMN_NAME']]['IS_NULLABLE'];
                }
            }
        }
        // !d($schema);die();
        usort($schema, function($a, $b) {
            $a['WEIGHT'] = ($a['WEIGHT'])??0;
            $b['WEIGHT'] = ($b['WEIGHT'])??0;
            return $a['WEIGHT'] <=> $b['WEIGHT'];
        });
        return $schema;
    }

    public function schemaChecking($tblSchema,$data){
        // !d($tblSchema,$data);die();
        $ret = [
            "msg"=>"VALID",
            "data"=>$data
        ];
        // !d($tblSchema);die();
        foreach($tblSchema as $k=>$v){
            if($v['EXTRA']==='auto_increment' || $v['COLUMN_DEFAULT']=='current_timestamp()'
            || $v['COLUMN_DEFAULT']==='now()') continue;
            if(($v['IS_NULLABLE']==="NO" || $v['IS_NULLABLE']==='required' || $v['IS_NULLABLE']===false)
            && !isset($data[$v['COLUMN_NAME']])){
                if($v['TYPE']==="toggle" || $v['TYPE']==="checkbox" || $v['TYPE']==="radio"){
                    $ret['data'][$v['COLUMN_NAME']] = "";
                }else{
                    $v_tmp = str_replace("_", " ", $v['COLUMN_NAME']);
                    $v_tmp = strtoupper($v_tmp);
                    $ret['msg'] = "Please input valid ".$v_tmp." value";
                    break;
                }
            }else{
                if(isset($v['TYPE']) && $v['TYPE']==="password" && $data[$v['COLUMN_NAME']]!="" && $v['IS_BCRYPT']){
                    $bcrypt = new Bcrypt();
                    $passwordHash = $bcrypt->create($data[$v['COLUMN_NAME']]);
                    $ret['data'][$v['COLUMN_NAME']] = $passwordHash;
                }
            }
        }
        // !d($ret);die();

        return $ret;
    }

    public function deleteSQLExecute($dbconn,$table,$data){
        // !d($tblSchema,$data);die();
        $db = $this->container->get($dbconn);
        $ret = [
            "msg"=>"FAILED",
            "ret"=>false
        ];
        $field = [];
        $sqlList = ['desc'];
        foreach($data as $k=>$v){
            $tmp = (in_array($k,$sqlList))?'`'.$k.'`':$k;
            $field[] = $tmp.'=:'.$k;
        }
        $sql = "DELETE FROM ".$table." WHERE ".implode(" AND ", $field);
        // die($sql);
        try{
            $stmt = $db->createStatement($sql,$data);
            // !d($stmt);die();
            $result = $stmt->execute(); // die("ok");
            // !d($result);die();
            if (!$result->valid()) {
                return [
                    "ret" => false,
                    "affected_row" => 0,
                    "generated_value" => 0,
                    "dbconn"=>$dbconn,
                    "dbtable"=>$table,
                    "data" => $data,
                    "msg" => "FAILED DELETE ".$table
                ];
            } else {
                return [
                    "ret" => true,
                    "affected_row" => $result->getAffectedRows(),
                    "generated_value" => $result->getGeneratedValue(),
                    "dbconn"=>$dbconn,
                    "dbtable"=>$table,
                    "data" => $data,
                    "msg" => "SUCCESS DELETE ".$table
                ];
            }
        } catch (\Exception $e) {
            return [
                "ret" => false,
                "affected_row" => 0,
                "generated_value" => 0,
                "dbconn"=>$dbconn,
                "dbtable"=>$table,
                "data" => $data,
                "msg" => $e->getMessage()
            ];
        }

        return $ret;
    }

    public function updateSQLExecute($dbconn,$table,$cond,$set){
        // !d($tblSchema,$data);die();
        $db = $this->container->get($dbconn);
        $ret = [
            "msg"=>"FAILED",
            "ret"=>false
        ];
        $field1 = [];
        $field2 = [];
        $sqlList = ['desc'];
        foreach($cond as $k=>$v){
            $tmp = (in_array($k,$sqlList))?'`'.$k.'`':$k;
            $field1[] = $tmp.'=:'.$k;
        }
        foreach($set as $k=>$v){
            $set[$k] = ($v==="")?null:$v;
            $tmp = (in_array($k,$sqlList))?'`'.$k.'`':$k;
            $field2[] = $tmp.'=:'.$k;
        }
        $data = ArrayUtils::merge($cond, $set);
        $sql = "UPDATE ".$table." SET ".implode(", ", $field2)." WHERE ".implode(" AND ", $field1);
        // !d($sql,$data);//die();
        try{
            $stmt = $db->createStatement($sql,$data);
            // !d($stmt);die();
            $result = $stmt->execute(); // die("ok");
            // !d($result);//die();
            if (!$result->valid()) {
                return [
                    "ret" => false,
                    "affected_row" => $result->getAffectedRows(),
                    "generated_value" => $result->getGeneratedValue(),
                    "dbconn"=>$dbconn,
                    "dbtable"=>$table,
                    "data" => [
                        'data'=>$data,
                        'cond'=>$cond
                    ],
                    "msg" => "FAILED UPDATE ".$table
                ];
            }else{
                return [
                    "ret" => true,
                    "affected_row" => $result->getAffectedRows(),
                    "generated_value" => $result->getGeneratedValue(),
                    "dbconn"=>$dbconn,
                    "dbtable"=>$table,
                    "data" => [
                        'data'=>$data,
                        'cond'=>$cond
                    ],
                    "msg" => "SUCCESS UPDATE ".$table
                ];
            }
        } catch (\Exception $e) {
            return [
                "ret" => false,
                "affected_row" => 0,
                "generated_value" => 0,
                "dbconn"=>$dbconn,
                "dbtable"=>$table,
                "data" => [
                    'data'=>$data,
                    'cond'=>$cond
                ],
                "msg" => $e->getMessage()
            ];
        }

        return $ret;
    }

    public function insertSQLExecute($dbconn,$table,$tblSchema,$data){
        // !d($tblSchema,$data);die();
        $db = $this->container->get($dbconn);
        $ret = [
            "msg"=>"FAILED",
            "ret"=>false
        ];
        $field = [];
        $value1 = [];
        $value2 = [];
        $sqlList = ['desc'];
        foreach($tblSchema as $k=>$v){
            if(isset($data[$v['COLUMN_NAME']]) && (!isset($v['IS_ADD']) || !$v['IS_ADD'])){
                $field[] = (in_array($v['COLUMN_NAME'],$sqlList))?'`'.$v['COLUMN_NAME'].'`':$v['COLUMN_NAME'];
                $data[$v['COLUMN_NAME']] = ($data[$v['COLUMN_NAME']]==="")?null:$data[$v['COLUMN_NAME']];
                $value1[$v['COLUMN_NAME']] = $data[$v['COLUMN_NAME']];
                $value2[] = ':'.$v['COLUMN_NAME'];
            }
        }
        $sql = "INSERT INTO ".$table." (".implode(", ", $field).") VALUES (".implode(", ", $value2).")";
        // die($sql);
        try{
            $stmt = $db->createStatement($sql,$value1);
            // !d($stmt);die();
            $result = $stmt->execute(); // die("ok");
            // !d($result,$result->valid());die();
            if (!$result->valid()) {
                return [
                    "ret" => false,
                    "affected_row" => 0,
                    "generated_value" => 0,
                    "dbconn"=>$dbconn,
                    "dbtable"=>$table,
                    "data" => $value1,
                    "msg" => "FAILED INSERT INTO ".$table
                ];
            } else {
                return [
                    "ret" => true,
                    "affected_row" => $result->getAffectedRows(),
                    "generated_value" => $result->getGeneratedValue(),
                    "dbconn"=>$dbconn,
                    "dbtable"=>$table,
                    "data" => $value1,
                    "msg" => "SUCCESS INSERT INTO ".$table
                ];
            }
        } catch (\Exception $e) {
            return [
                "ret" => false,
                "affected_row" => 0,
                "generated_value" => 0,
                "dbconn"=>$dbconn,
                "dbtable"=>$table,
                "data" => $value1,
                "msg" => $e->getMessage()
            ];
        }

        return $ret;
    }

    public function checkTableSchema(Array $schema){
        return (array_key_exists('COLUMN_NAME',$schema) &&
        array_key_exists('IS_NULLABLE',$schema) && array_key_exists('DATA_TYPE',$schema) &&
        array_key_exists('COLUMN_DEFAULT',$schema) && array_key_exists('EXTRA',$schema));
    }

    public function createNewField($name,$default_value = "",$is_null = true,$data_type = "varchar",$extra = ""){
        return [
            'COLUMN_NAME'=>$name,
            'IS_NULLABLE'=> $is_null,
            'DATA_TYPE'=> $data_type,
            'DEFAULT'=> $default_value,
            'COLUMN_DEFAULT'=> "",
            'EXTRA'=> $extra,
            'IS_ADD'=>true
        ];
    }

    public function callModel($model,$func,$par=[]){
        $ret = [
            'ret'=>false,
            'msg'=>'Invalid Request',
            'data'=>[]
        ];
        try {
            $model = $this->container->get($cls);
            $exist = method_exists($model, $func);
            // !d($exist);
            if ($exist) {
                $ret = [
                    'ret'=>true,
                    'msg'=>'Success Request',
                    'data'=>$model->{$func}($par)
                ];
            }
        } catch (\Exception $e) {
        } catch (\ArgumentCountError $e) {
        }

        return $ret;
    }
}