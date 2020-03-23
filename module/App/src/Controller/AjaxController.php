<?php
declare(strict_types=1);
namespace App\Controller;

use Laminas\Mvc\Controller\AbstractActionController;
use Zend\Debug\Debug;
use Laminas\View\Model\ViewModel; 
use Laminas\View\Model\JsonModel;
use Laminas\View\Model\FeedModel;
use Laminas\Feed\Writer\Feed;
use Laminas\Stdlib\ArrayUtils;

class AjaxController extends AbstractActionController
{
    private $container;
    private $config;
    
    public function __construct($container, $config){
        $this->container = $container;
        $this->config = $config;
    }

    public function generatorDeleteSQLAction(){
        $auth = $this->Auth();
        $identity = $auth->getIdentity();
        session_write_close();
    	$pHeader = $this->params()->fromHeader();
        $pPost = $this->params()->fromPost();
        // $par = ArrayUtils::merge($pPost, $pHeader);
        // Debug::dump($pPost);//die();
        // Debug::dump($pHeader);die();
        $ret = [
            "ret"=>false,
            'msg'=>'INVALID REQUEST',
            'data'=>[]
        ];

        if($this->getRequest()->isPost() && isset($pHeader['X-Csrf-Token']) && 
        isset($pHeader['X-Db-Conn']) && isset($pHeader['X-Db-Table'])){
            $data['csrf'] = $pHeader['X-Csrf-Token'];
            $csrfForm = $this->container->get('csrfForm');
            $csrfForm->setData($data);
            // !d($data,$csrfForm->isValid());die();
            // Debug::dump($csrfForm->isValid());die();
            if ($csrfForm->isValid()) {
                $csrfForm = $this->container->get('csrfForm');
                $newcsrf = $csrfForm->get('csrf')->getValue();
                $ret = $this->DataGenerator()->deleteSQLExecute($pHeader['X-Db-Conn'], $pHeader['X-Db-Table'], $pPost);
                $ret['csrf'] = $newcsrf;
            }else{
                $ret['msg']='INVALID CSRF';
            }
        }
        
        $viewModel = new JsonModel();
        $viewModel->setVariables($ret);
        return $viewModel;
    }

    public function callmodelAction()
    {
        $auth = $this->Auth();
        $identity = $auth->getIdentity();
        session_write_close();
        // Debug::dump($auth->getIdentity());die();
        // !d($this->params());die();
    	$pFiles = $this->params()->fromFiles();
    	$pHeader = $this->params()->fromHeader();
    	$pRoute = $this->params()->fromRoute();
    	$pQuery = $this->params()->fromQuery();
        $pPost = $this->params()->fromPost();
        // d($pFiles,$pHeader,$pRoute,$pQuery,$pPost);die();
        // !d($pRoute);
        $ret = [
            'ret'=>false,
            'msg'=>'Invalid Request',
            'data'=>[]
        ];

        if ($auth->isLogin() && isset($pRoute['app']) && isset($pRoute['model']) && isset($pRoute['func'])) {
            try {
                $app = $pRoute['app'];
                $mdl = $pRoute['model'];
                $func = $pRoute['func'];
                $cls = $app."\\Model\\".$mdl;
                // !d($cls,$func);//die();
                // $model = $this->container->get(\App\Model\MenuModel::class);
                $model = $this->container->get($cls);
                $exist = method_exists($model, $func);
                // !d($exist);
                if ($exist) {
                    $par = ArrayUtils::merge($pPost, $pQuery);
                    // !d($par);die();
                    $ret = [
                        'ret'=>true,
                        'msg'=>'Success Request',
                        'data'=>$model->{$func}($par)
                    ];
                }
            } catch (\Exception $e) {
            } catch (\ArgumentCountError $e) {
            }
        }
		// die('qqq');
        $viewModel = new JsonModel();
        // $viewModel->setVariable('items', $items);
        $viewModel->setVariables($ret);
        return $viewModel;
    }
}