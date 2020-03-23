<?php
namespace Core\Helper\View\notika;

use Laminas\View\Helper\AbstractHelper;
use Laminas\Authentication\AuthenticationService;
use Laminas\Session\SessionManager;
use Zend\Debug\Debug;

/**
 * This view helper class displays a menu bar.
 */

class Generator extends AbstractHelper {
    private $config;
    private $container;
    private $authService;
    private $sessionManager;
    private $vars;
    private $identity;
    private $data;
    
    public function __construct($container,$config){
        $this->container = $container;
        // Debug::dump($container);die();
        $this->config = $config;
        $this->authService = $container->get(AuthenticationService::class); 
        $this->sessionManager = $container->get(SessionManager::class);
    }

    public function setVars($vars){
        $this->vars = $vars;
    }

    public function setIdentity($identity){
        $this->identity = $identity;
    }

    public function setData($data){
        $this->data = $data;
    }

    public function renderTableForm(Array $schema,int $column = 1){
        $ret = '';
        // !d($schema);die();
        if(count($schema)>0){
            $idx = 1;
            $rowend = false;
            foreach($schema as $k=>$v){
                if($v['EXTRA']==='auto_increment' || $v['COLUMN_DEFAULT']==='current_timestamp()'
                || $v['COLUMN_DEFAULT']==='now()') continue;
                if($this->checkTableSchema($v)){
                    // Debug::dump($v);die();
                    $v_tmp = str_replace("_"," ",$v['COLUMN_NAME']);
                    $v_tmp = strtoupper($v_tmp);
                    $v_tmp2 = "";
                    // !d($v['COLUMN_NAME'],$v['IS_NULLABLE'],$v_tmp2);
                    if($v['IS_NULLABLE']==="NO" || $v['IS_NULLABLE']==='required' || $v['IS_NULLABLE']===false){
                        $v_tmp2 = "required";
                    }
                    // !d($v_tmp2);
                    $v_tmp3 = $v['ICON'] ?? "fas fa-info-circle";
                    $v_tmp4 = $v['TYPE'] ?? "text";
                    $v_tmp5 = $v['DEFAULT'] ?? "";
                    $v_tmp6 = $v['ADDLABEL'] ?? "";
                    $arr_tmp = $v['ATTR'] ?? [];
                    // !d($arr_tmp);//die();
                    $arr_tmp2 = [];
                    foreach ($arr_tmp as $k2=>$v2){
                        // !d($v2,is_bool($v2));//die();
                        if(is_bool($v2)){
                            if ($v2) {
                                  $arr_tmp2[] = $k2;
                            }
                        }else if(!is_array($v2)){
                            $arr_tmp2[] = $k2.'="'.$v2.'"';
                        }
                        // !d($arr_tmp2);//die();
                    }
                    $v_tmp7 = implode(' ', $arr_tmp2);
                    $v_tmp = $v['LABEL'] ?? $v_tmp;
                    // !d($v);//die();

                    if ($v_tmp4==='hidden' || $v_tmp4==='csrf') {
                        // !d($v_tmp5);die();
                        $ret.='<input '.$v_tmp7.' id="input-'.$v['COLUMN_NAME'].'" name="'.$v['COLUMN_NAME'].'" 
                        type="hidden" class="form-control" '.$v_tmp2.' value="'.$v_tmp5.'"/>';
                        continue;
                    }
                    
                    if($idx===1 || ($idx>$column && ($idx%$column===1))){
                        $ret .='<div class="row m-y-10 p-y-10">';
                        $rowend = false;
                    }
                    
                    $ret.='<div class="col-md-'.(12/$column).'">';
                    if($v_tmp4==='select'){
                        $data = [];
                        if(isset($v['DATA']) && isset($v['DATA']['TYPE'])){
                            if($v['DATA']['TYPE']==='model'){
                                // !d($v['DATA']);
                                try{
                                    $model = $this->container->get($v['DATA']['MODEL']);
                                    $exist = method_exists($model, $v['DATA']['FUNCTION']);
                                    $func = $v['DATA']['FUNCTION'];
                                    // !d($exist);die();
                                    if ($exist) {
                                        $par = ($v['DATA']['PARAM']) ?? [];
                                        $data = $model->{$func}($par,false);
                                    }
                                    
                                } catch (\Exception $e) {
                                } catch (\ArgumentCountError $e) {
                                }
                                // !d($v['DATA'],$data);die();
                                // $ret = $this->dataGenerator()->callModel($v['DATA']['MODEL'],$v['DATA']['FUNCTION']);
                                // !d($v,$ret);die();
                            }else if($v['DATA']['TYPE']==='array'){
                                $data = $v['DATA']['LIST'];
                            }
                        }
                        // !d($data,$v_tmp5);

                        $ret .= '<div class="nk-int-mk sl-dp-mn sm-res-mg-t-10">
                            <h2><i class="'.$v_tmp3.'"></i> '.$v_tmp.$v_tmp6.'</h2>
                        </div>
                        <div class="bootstrap-select fm-cmp-mg">
                            <select '.$v_tmp7.' id="input-'.$v['COLUMN_NAME'].'" name="'.$v['COLUMN_NAME'].'" 
                            class="selectpicker" data-live-search="true" '.$v_tmp2.'>';
                        foreach ($data as $k2 => $v2){
                            $select = "";
                            if($v2['val']===$v_tmp5)$select = "selected";
                            $tmpattr = [];
                            if(isset($v2['data']) && is_array($v2['data'])){
                                foreach ($v2['data'] as $k3=>$v3){
                                    $tmpattr[] = 'data-'.$k3.'="'.$v3.'"';
                                }
                            }
                            $ret .= '<option '.implode(" ",$tmpattr).' value="'.$v2['val'].'" '.$select.'>'.$v2['label'].'</option>';
                        }
                        $ret .= '</select>
                        </div>';
                    }else if($v_tmp4==='toggle'){
                        // if($v_tmp5==="")$v_tmp5 = "1";
                        $v_tmp5 = "1";
                        $ret .= '<div class="nk-int-mk sl-dp-mn sm-res-mg-t-10">
                            <h2><i class="'.$v_tmp3.'"></i> '.$v_tmp.$v_tmp6.'</h2>
                        </div>
                        <div class="toggle-select-act fm-cmp-mg">
                            <div class="nk-toggle-switch">
                                <label for="input-'.$v['COLUMN_NAME'].'" class="ts-label"></label>
                                <input '.$v_tmp7.' id="input-'.$v['COLUMN_NAME'].'" name="'.$v['COLUMN_NAME'].'" type="checkbox" hidden="hidden" value="'.$v_tmp5.'">
                                <label for="input-'.$v['COLUMN_NAME'].'" class="ts-helper"></label>
                            </div>
                        </div>';
                    }else if($v_tmp4==='content'){
                        // !d($v_tmp5);
                        $ret .= '<div class="nk-int-mk sl-dp-mn sm-res-mg-t-10">
                            <h2><i class="'.$v_tmp3.'"></i> '.$v_tmp.$v_tmp6.'</h2>
                        </div>
                        <div class="fm-cmp-mg">
                            <div '.$v_tmp7.' data-id="input-'.$v['COLUMN_NAME'].'" 
                            data-name="'.$v['COLUMN_NAME'].'" data-value="'.$v_tmp5.'" rows="3">'.$v_tmp5.'</div>
                        </div>';
                    }else if($v_tmp4==='textarea'){
                        // !d($v_tmp5);
                        $ret .= '<div class="nk-int-mk sl-dp-mn sm-res-mg-t-10">
                            <h2><i class="'.$v_tmp3.'"></i> '.$v_tmp.$v_tmp6.'</h2>
                        </div>
                        <div class="fm-cmp-mg">
                            <textarea '.$v_tmp7.' id="input-'.$v['COLUMN_NAME'].'" name="'.$v['COLUMN_NAME'].'" value="'.$v_tmp5.'"
                            class="form-control auto-size w-100" rows="3">'.$v_tmp5.'</textarea>
                        </div>';
                    }else{
                        $ret .= '<div class="nk-int-mk sl-dp-mn sm-res-mg-t-10">
                            <h2><i class="'.$v_tmp3.'"></i> '.$v_tmp.$v_tmp6.'</h2>
                        </div>
                        <div class="fm-cmp-mg">
                            <input '.$v_tmp7.' id="input-'.$v['COLUMN_NAME'].'" name="'.$v['COLUMN_NAME'].'" 
                            type="'.$v_tmp4.'" class="form-control" '.$v_tmp2.' value="'.$v_tmp5.'">
                        </div>';
                    }
                    
                    $ret .='</div>';
                    if($idx>=$column && $idx%$column===0){
                        $ret .='</div>';
                        $rowend = true;
                    }
                    $idx++;
                }
            }
            if(!$rowend){
                $ret .='</div>';
            }
        }

        return $ret;
    }

    public function checkTableSchema(Array $schema){
        return (array_key_exists('COLUMN_NAME',$schema) &&
        array_key_exists('IS_NULLABLE',$schema) && array_key_exists('DATA_TYPE',$schema) &&
        array_key_exists('COLUMN_DEFAULT',$schema) && array_key_exists('EXTRA',$schema));
    }
}