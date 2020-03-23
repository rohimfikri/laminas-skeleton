<?php
namespace Core\Helper\View\notika;

use Laminas\View\Helper\AbstractHelper;
use Laminas\Authentication\AuthenticationService;
use Laminas\Session\SessionManager;
use Zend\Debug\Debug;

/**
 * This view helper class displays a menu bar.
 */

class Notification extends AbstractHelper {
    private $config;
    private $container;
    private $authService;
    private $sessionManager;
    private $vars;
    private $identity;
    private $unread_inbox= [];
    private $unread_alert= [];
    private $unread_inboxKey= [];
    private $unread_alertKey= [];
    
    public function __construct($container,$config){
        $this->container = $container;
        $this->config = $config;
        $this->authService = $container->get(AuthenticationService::class); 
        $this->sessionManager = $container->get(SessionManager::class);
    }

    public function reloadNotif($fromcache = true){
        $this->reloadInbox($fromcache);
        $this->reloadAlert($fromcache);
    }

    public function reloadInbox($fromcache = true){
        // !d($this->identity,$this->vars);die();
        $layout = $this->vars['layout'] ?? "";
        $layout = str_replace("-layout","",$layout);
        $module = $this->vars['module'] ?? "";
        $uid = $this->identity['id'] ?? "";
        if($uid!="" && $uid!=null){
            $InboxModel = $this->container->get(\App\Model\InboxModel::class);
            $unreadInbox = $InboxModel->getUnreadInboxByUidByLayoutByModule($uid,$layout,$module,$fromcache);
            // !d($unreadInbox);die();
            if(count($unreadInbox)>0){
                foreach($unreadInbox as $v){
                    if(!in_array($v['id'],$this->unread_inboxKey)){
                        if(($v['url']!=null && $v['url']!="") || ($v['route']!=null && $v['route']!="")){
                            $this->unread_inboxKey[] = $v['id'];
                            $par = json_encode($v['param'],true);
                            $qry = json_encode($v['query'],true);
                            $this->unread_inbox["unread_inbox".$v['id']] = [
                                "title"=>substr($v['title'],0,30).((strlen($v['title'])>30)?'...':''),
                                "link"=>$v['url'],
                                "route_link"=>$v['route'],
                                "route_param"=>(is_array($par))?$par:[],
                                "route_query"=>(is_array($qry))?$qry:[],
                                "icon"=>$v['icon'],
                                "class"=>$v['class'],
                                "img"=>$v['img'],
                                "id"=>$v['id'],
                                "content"=>substr($v['content'],0,60).((strlen($v['title'])>60)?'...':'')
                            ];
                        }
                    }
                }
            }
        }
        // !d($this->unread_inbox);die();
        // die(json_encode($this->unread_inbox));
    }

    public function renderInbox($fromcache = true){
        $html = "";
        foreach ($this->unread_inbox as $v) {
            $link = $v['link'];
            if($link==="" || $link==="#"){
                if($v['route_link']!=""){
                    try{
                        $link = $this->url($v['route_link'],$v['route_param'],$v['route_query']);
                    }catch(LaminasException\RuntimeException $e){
                        $link = "#";
                    }
                }
            }

            $html = '
            <a href="'.$link.'">
                <div class="hd-message-sn '.$v['class'].'">
                    <div class="hd-message-img">';
                    if ($v['img']!="") {
                        $html.='<img src="'.$v['img'].'" alt=""/>';
                    }else if($v['icon']!=""){
                        $html.='<i class="'.$v['icon'].'" style="font-size:2.5rem;padding:0.5rem;"></i>';
                    }
                    $html.='</div>
                    <div class="hd-mg-ctn">
                        <h3>'.$v['title'].'</h3>
                        <p>'.$v['content'].'</p>
                    </div>
                </div>
            </a>';
        }
        return $html;
    }

    public function reloadAlert($fromcache = true){
        // !d($this->identity,$this->vars);die();
        $layout = $this->vars['layout'] ?? "";
        $layout = str_replace("-layout","",$layout);
        $module = $this->vars['module'] ?? "";
        $uid = $this->identity['id'] ?? "";
        if($uid!="" && $uid!=null){
            $AlertModel = $this->container->get(\App\Model\AlertModel::class);
            $unreadAlert = $AlertModel->getUnreadAlertByUidByLayoutByModule($uid,$layout,$module,$fromcache);
            // !d($unreadAlert);die();
            if(count($unreadAlert)>0){
                foreach($unreadAlert as $v){
                    if(!in_array($v['id'],$this->unread_alertKey)){
                        if(($v['url']!=null && $v['url']!="") || ($v['route']!=null && $v['route']!="")){
                            $this->unread_alertKey[] = $v['id'];
                            $par = json_encode($v['param'],true);
                            $qry = json_encode($v['query'],true);
                            $this->unread_alert["unread_inbox".$v['id']] = [
                                "title"=>substr($v['title'],0,30).((strlen($v['title'])>30)?'...':''),
                                "link"=>$v['url'],
                                "route_link"=>$v['route'],
                                "route_param"=>(is_array($par))?$par:[],
                                "route_query"=>(is_array($qry))?$qry:[],
                                "icon"=>$v['icon'],
                                "class"=>$v['class'],
                                "img"=>$v['img'],
                                "id"=>$v['id'],
                                "content"=>substr($v['content'],0,60).((strlen($v['title'])>60)?'...':'')
                            ];
                        }
                    }
                }
            }
        }
        // !d($this->unread_alert);die();
    }

    public function renderAlert($fromcache = true){
        $html = "";
        foreach ($this->unread_alert as $v) {
            $link = $v['link'];
            if($link==="" || $link==="#"){
                if($v['route_link']!=""){
                    try{
                        $link = $this->url($v['route_link'],$v['route_param'],$v['route_query']);
                    }catch(LaminasException\RuntimeException $e){
                        $link = "#";
                    }
                }
            }

            $html = '
            <a href="'.$link.'">
                <div class="hd-message-sn '.$v['class'].'">
                    <div class="hd-message-img">';
                    if ($v['img']!="") {
                        $html.='<img src="'.$v['img'].'" alt=""/>';
                    }else if($v['icon']!=""){
                        $html.='<i class="'.$v['icon'].'" style="font-size:2.5rem;padding:0.5rem;"></i>';
                    }
                    $html.='</div>
                    <div class="hd-mg-ctn">
                        <h3>'.$v['title'].'</h3>
                        <p>'.$v['content'].'</p>
                    </div>
                </div>
            </a>';
        }
        return $html;
    }

    public function setVars($vars){
        $this->vars = $vars;
    }

    public function setIdentity($identity){
        $this->identity = $identity;
    }

    public function getTotalUnreadInbox(){
        $ret = $this->unread_inbox;
        return count($ret);
    }

    public function getTotalUnreadAlert(){
        $ret = $this->unread_alert;
        return count($ret);
    }

    public function getUnreadInbox(){
        $ret = $this->unread_inbox;
        return $ret;
    }

    public function getUnreadAlert(){
        $ret = $this->unread_alert;
        return $ret;
    }
}