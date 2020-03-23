<?php
namespace Core\Helper\View\notika;

use Laminas\View\Helper\AbstractHelper;
use Laminas\Authentication\AuthenticationService;
use Laminas\Session\SessionManager;
use Zend\Debug\Debug;

/**
 * This view helper class displays a menu bar.
 */

class TopMenu extends AbstractHelper {
    private $config;
    private $container;
    private $authService;
    private $sessionManager;
    private $vars;
    private $identity;
    private $topMenu = [];
    private $menu1= [];
    private $menu2= [];
    private $menu3= [];
    private $topMenuKey = [];
    private $menu1Key= [];
    private $menu2Key= [];
    private $menu3Key= [];
    
    public function __construct($container,$config){
        $this->container = $container;
        $this->config = $config;
        $this->authService = $container->get(AuthenticationService::class); 
        $this->sessionManager = $container->get(SessionManager::class);
    }

    public function reloadMenu($fromcache = true){
        // !d($this->identity,$this->vars);die();
        $layout = $this->vars['layout'] ?? "";
        $layout = str_replace("-layout","",$layout);
        $module = $this->vars['module'] ?? "";
        $uid = $this->identity['id'] ?? "";
        if($uid!="" && $uid!=null){
            $MenuModel = $this->container->get(\App\Model\MenuModel::class);
            $menus = $MenuModel->getMenuByUidByLayoutByModule($uid,$layout,$module,$fromcache);
            // !d($menus);//die();
            if(count($menus)>0){
                foreach($menus as $v){
                    if($v['parent']==="0" && !in_array($v['id'],$this->topMenuKey)){
                        if(($v['url']!=null && $v['url']!="") || ($v['route']!=null && $v['route']!="")){
                            $this->topMenuKey[] = $v['id'];
                            $par = json_encode($v['param'],true);
                            $qry = json_encode($v['query'],true);
                            $this->topMenu["topmenu".$v['id']] = [
                                "title"=>$v['title'],
                                "link"=>$v['url'],
                                "route_link"=>$v['route'],
                                "route_param"=>(is_array($par))?$par:[],
                                "route_query"=>(is_array($qry))?$qry:[],
                                "icon"=>$v['icon']
                            ];
                        }
                    }else if(($v['parent']===null || $v['parent']==="") && $v['url']==="#"
                    && !in_array($v['id'],$this->topMenuKey) && !in_array($v['id'],$this->menu1Key)){
                        $this->menu1Key[] = $v['id'];
                        $this->menu1["topmenu".$v['id']] = [
                            "title"=>$v['title'],
                            "link"=>"topmenu".$v['id'],
                            "route_link"=>"",
                            "route_param"=>[],
                            "route_query"=>[],
                            "icon"=>$v['icon']
                        ];
                    }else if(in_array($v['parent'],$this->menu1Key)
                    && !in_array($v['id'],$this->topMenuKey) && !in_array($v['id'],$this->menu1Key)
                    && !in_array($v['id'],$this->menu2Key)){
                        if (($v['url']!=null && $v['url']!="") || ($v['route']!=null && $v['route']!="")) {
                            $this->menu2Key[] = $v['id'];
                            $par = [];
                            $qry = [];
                            $hasChild = true;
                            $link = "topmenu".$v['id'];
                            if($v['url']!="#"){
                                $par = json_encode($v['param'],true);
                                $qry = json_encode($v['query'],true);
                                $par = (is_array($par))?$par:[];
                                $qry = (is_array($qry))?$qry:[];
                                $link = $v['url'];
                                $hasChild = false;
                            }
                            $this->menu2["topmenu".$v['parent']][] = [
                                "title"=>$v['title'],
                                "link"=>$link,
                                "route_link"=>$v['route'],
                                "route_param"=>$par,
                                "route_query"=>$qry,
                                "hasChild"=>$hasChild,
                                "icon"=>$v['icon']
                            ];
                        }
                    }else if(in_array($v['parent'],$this->menu2Key)
                    && !in_array($v['id'],$this->topMenuKey) && !in_array($v['id'],$this->menu1Key)
                    && !in_array($v['id'],$this->menu2Key) && !in_array($v['id'],$this->menu3Key)){
                        if (($v['url']!=null && $v['url']!="") || ($v['route']!=null && $v['route']!="")) {
                            $this->menu3Key[] = $v['id'];
                            $par = json_encode($v['param'],true);
                            $qry = json_encode($v['query'],true);
                            $par = (is_array($par))?$par:[];
                            $qry = (is_array($qry))?$qry:[];
                            $link = $v['url'];
                            $this->menu3["topmenu".$v['parent']][] = [
                                "title"=>$v['title'],
                                "link"=>$link,
                                "route_link"=>$v['route'],
                                "route_param"=>$par,
                                "route_query"=>$qry,
                                "icon"=>$v['icon']
                            ];
                        }
                    }
                }
            }
        }

        // !d($this->topMenuKey,$this->topMenu);
    }

    public function setVars($vars){
        $this->vars = $vars;
    }

    public function setIdentity($identity){
        $this->identity = $identity;
    }

    public function getTopMenu(){
        $ret = $this->topMenu;
        // $ret = [
        //     [
        //         "title"=>"To Google",
        //         "link"=>"https://www.google.com",
        //         "route_link"=>"",
        //         "route_param"=>[],
        //         "route_query"=>[],
        //         "icon"=>"fa fa-sign-out"
        //     ],
        //     [
        //         "title"=>"To Facebook",
        //         "link"=>"https://www.facebook.com",
        //         "route_link"=>"",
        //         "route_param"=>[],
        //         "route_query"=>[],
        //         "icon"=>"fa fa-sign-out"
        //     ],
        //     [
        //         "title"=>"To App Summary",
        //         "link"=>"",
        //         "route_link"=>"admin",
        //         "route_param"=>[],
        //         "route_query"=>[],
        //         "icon"=>"notika-icon notika-house"
        //     ]
        // ];

        return $ret;
    }

    public function getMenu1(){
        $ret = $this->menu1;
        // $ret = [
        //     [
        //         "title"=>"Menu 1",
        //         "link"=>"menu1",
        //         "icon"=>"notika-icon notika-app"
        //     ],
        //     [
        //         "title"=>"Menu 2",
        //         "link"=>"menu2",
        //         "icon"=>"fa fa-sign-out"
        //     ],
        //     [
        //         "title"=>"Menu 3",
        //         "link"=>"menu3",
        //         "icon"=>"notika-icon notika-app"
        //     ],
        // ];

        return $ret;
    }

    public function getMenu2(){
        $ret = $this->menu2;
        // $ret = [
        //     "menu1"=>[
        //         [
        //             "title"=>"Menu 1A",
        //             "link"=>"https://www.google.com",
        //             "route_link"=>"",
        //             "route_param"=>[],
        //             "route_query"=>[],
        //             "icon"=>"fa fa-sign-out"
        //         ],
        //         [
        //             "title"=>"Menu 1B",
        //             "link"=>"https://www.facebook.com",
        //             "route_link"=>"",
        //             "route_param"=>[],
        //             "route_query"=>[],
        //             "icon"=>"fa fa-sign-out"
        //         ]
        //     ],
        //     "menu2"=>[
        //         [
        //             "title"=>"Menu 2A",
        //             "link"=>"https://www.facebook.com",
        //             "route_link"=>"",
        //             "route_param"=>[],
        //             "route_query"=>[],
        //             "icon"=>"fa fa-sign-out"
        //         ]
        //     ],
        //     "menu3"=>[
        //         [
        //             "title"=>"Menu 3A",
        //             "link"=>"menu3A",
        //             "hasChild"=>true,
        //             "icon"=>"notika-icon notika-house"
        //         ],
        //         [
        //             "title"=>"Menu 3B",
        //             "link"=>"https://www.facebook.com",
        //             "route_link"=>"",
        //             "route_param"=>[],
        //             "route_query"=>[],
        //             "icon"=>"fa fa-sign-out"
        //         ]
        //     ]
        // ];

        return $ret;
    }

    public function getMenu3(){
        $ret = $this->menu3;
        // $ret = [
        //     "menu3A"=>[
        //         [
        //             "title"=>"Menu 3A1",
        //             "link"=>"",
        //             "route_link"=>"admin",
        //             "route_param"=>[],
        //             "route_query"=>[],
        //             "icon"=>"notika-icon notika-house"
        //         ],
        //         [
        //             "title"=>"Menu 3A2",
        //             "link"=>"https://www.facebook.com",
        //             "route_link"=>"",
        //             "route_param"=>[],
        //             "route_query"=>[],
        //             "icon"=>"fa fa-sign-out"
        //         ]
        //     ]
        // ];

        return $ret;
    }
}