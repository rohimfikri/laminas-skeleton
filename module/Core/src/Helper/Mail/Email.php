<?php
namespace Core\Helper\Mail;

use Laminas\Mvc\Controller\Plugin\AbstractPlugin;
use Laminas\Mail\Message;
use Laminas\Mail\Transport\Smtp as SmtpTransport;
use Laminas\Mail\Transport\SmtpOptions;
use Laminas\Mime\Message as MimeMessage;
use Laminas\Mime\Mime;
use Laminas\Mime\Part as MimePart;
use Zend\Debug\Debug;

/**
 * This view helper class displays a menu bar.
 */

class Email extends AbstractPlugin 
{
    private $config;
    private $container;

    private $smtptransport = null;
    public $host = "securemailgwjt6.telkom.co.id";
    public $auth = 'login';
    public $username = '402762';
    public $password = 'Ip5d19tw4';
    public $port = 4004;
    
    public function __construct($container,$config)
    {
        $this->container = $container;
        $this->config = $config;
        return $this->reconfigSMTP();
    }

    private function reconfigSMTP(){
        $ret = [
            'ret'=>false,
            'msg'=>'invalid request'
        ];
        try{
            $options   = new SmtpOptions([  
                'name'              => $this->host,
                'host'              => $this->host,
                'port'              => $this->port,
                'connection_class'  => $this->auth,
                'connection_config' => [
                    'username' => $this->username,
                    'password' => $this->password,
                    //ssl: either the value ssl or tls
                    // 'ssl'=>?
                    //Port 25 is the default used for non-SSL connections, 465 for SSL, and 587 for TLS
                    // 'port'=>?
                ],
            ]);
            $this->smtptransport = new SmtpTransport();
            $this->smtptransport->setOptions($options);
            $ret = [
                'ret'=>true,
                'msg'=>'success'
            ];
        }catch(Exception $e){
            $ret['msg'] = $e->getMessage();
        }
        return $ret;
    }

    public function setSMTP($host,$port,$auth,$username,$password){
        $this->host = $host;
        $this->port = $port;
        $this->auth = $auth;
        $this->username = $username;
        $this->password = $password;
        return $this->reconfigSMTP();
    }

    public function sendMessage($subject = "",$from = [],$to = [], $msg = "",$htmlMarkup = "", $attach = [],$cc = [], $bcc = [], $replyto = [], 
    $encode = "UTF-8", $headers = []){
        $ret = [
            'ret'=>false,
            'msg'=>'invalid request',
            'data'=>null
        ];

        $_from = [];
        foreach($from as $k=>$v){
            if(filter_var($v, FILTER_VALIDATE_EMAIL)){
                $_from[$k] = $v;
            }
        }
        if(count($_from)<=0){
            $_from['Dashboard IPTV'] = "no_reply@telkom.co.id";
        }

        $_to = [];
        foreach($to as $k=>$v){
            if(filter_var($v, FILTER_VALIDATE_EMAIL)){
                $_to[$k] = $v;
            }
        }

        $_cc = [];
        foreach($cc as $k=>$v){
            if(filter_var($v, FILTER_VALIDATE_EMAIL)){
                $_cc[$k] = $v;
            }
        }

        $_bcc = [];
        foreach($bcc as $k=>$v){
            if(filter_var($v, FILTER_VALIDATE_EMAIL)){
                $_bcc[$k] = $v;
            }
        }

        $_replyto = [];
        foreach($replyto as $k=>$v){
            if(filter_var($v, FILTER_VALIDATE_EMAIL)){
                $_replyto[$k] = $v;
            }
        }

        $_attach = [];
        foreach($attach as $v){
            // Debug::dump($v);die();
            if(isset($v['filepath']) && isset($v['filename']) && isset($v['filetype'])){
                if(file_exists($v['filepath'])){
                    $_attach[] = $v;
                }
            }
        }
        // Debug::dump($_attach);die();

        if($subject=="" || $subject==null || count($_to)<=0 || 
        (($msg == "" || $msg == null) && ($htmlMarkup == "" || $htmlMarkup == null))){
            $ret['msg'] = "invalid param";
        }else{
            try{
                $message = new Message();
                $message->setSubject($subject);
                $message->setEncoding($encode);
                foreach($_from as $k=>$v){
                    if(!(is_numeric($k))){
                        $message->addFrom($v,$k);
                    }else{
                        $message->addFrom($v);
                    }
                }
                foreach($_to as $k=>$v){
                    if(!(is_numeric($k))){
                        $message->addTo($v,$k);
                    }else{
                        $message->addTo($v);
                    }
                }
                foreach($_cc as $k=>$v){
                    if(!(is_numeric($k))){
                        $message->addCc($v,$k);
                    }else{
                        $message->addCc($v);
                    }
                }
                foreach($_bcc as $k=>$v){
                    if(!(is_numeric($k))){
                        $message->addBcc($v,$k);
                    }else{
                        $message->addBcc($v);
                    }
                }
                foreach($_replyto as $k=>$v){
                    if(!(is_numeric($k))){
                        $message->addReplyTo($v,$k);
                    }else{
                        $message->addReplyTo($v);
                    }
                }
                foreach($headers as $k=>$v){
                    $message->getHeaders()->addHeaderLine($k,$v);
                }
                // $message->setBody($msg);
                $text           = new MimePart($msg);
                $text->type     = Mime::TYPE_TEXT;
                $text->charset  = $encode;
                $text->encoding = Mime::ENCODING_QUOTEDPRINTABLE;

                $html           = new MimePart($htmlMarkup."<br>");
                $html->type     = Mime::TYPE_HTML;
                $html->charset  = $encode;
                $html->encoding = Mime::ENCODING_QUOTEDPRINTABLE;

                $body = new MimeMessage();
                $body->setParts([$text, $html]);

                // $contentPart[] = new MimePart($content->generateMessage());

                foreach($_attach as $v){
                    $tmpattach             = new MimePart(fopen($v['filepath'], 'r'));
                    $tmpattach->type        = $v['filetype'];
                    $tmpattach->filename    = $v['filename'];
                    $tmpattach->disposition = Mime::DISPOSITION_ATTACHMENT;
                    $tmpattach->encoding    = Mime::ENCODING_BASE64;
                    // $contentPart[] = $tmpattach;
                    $body->addPart($tmpattach);
                }
                // Debug::dump($body);die();
                // $body = new MimeMessage();
                // $body->setParts($contentPart);
                $message->setBody($body);

                // $contentTypeHeader = $message->getHeaders()->get('Content-Type');
                // if(count($_attach)>0){
                //     $contentTypeHeader->setType('multipart/related');
                // }else{
                //     $contentTypeHeader->setType('multipart/alternative');
                // }

                $ret['msg'] = "failed send message";
                $ret['data'] = $message->toString();
                $this->smtptransport->send($message);
                $ret['ret'] = true;
                $ret['msg'] = "success send message";
            }catch(Exception $e){
                $ret['msg'] = $e->getMessage();
            }
        }

        return $ret;
    }

    public function sendMessageLoop($subject = "",$from = [],$to = [], $msg = "",$htmlMarkup = "", $attach = [],$cc = [], $bcc = [], $replyto = [], 
    $encode = "UTF-8", $headers = []){
        $ret = [
            'ret'=>false,
            'msg'=>'invalid request',
            'data'=>null
        ];

        $_from = [];
        foreach($from as $k=>$v){
            if(filter_var($v, FILTER_VALIDATE_EMAIL)){
                $_from[$k] = $v;
            }
        }
        if(count($_from)<=0){
            $_from['Dashboard IPTV'] = "no_reply@telkom.co.id";
        }

        $_to = [];
        foreach($to as $k=>$v){
            if(filter_var($v, FILTER_VALIDATE_EMAIL)){
                $_to[$k] = $v;
            }
        }

        $_cc = [];
        foreach($cc as $k=>$v){
            if(filter_var($v, FILTER_VALIDATE_EMAIL)){
                $_cc[$k] = $v;
            }
        }

        $_bcc = [];
        foreach($bcc as $k=>$v){
            if(filter_var($v, FILTER_VALIDATE_EMAIL)){
                $_bcc[$k] = $v;
            }
        }

        $_replyto = [];
        foreach($replyto as $k=>$v){
            if(filter_var($v, FILTER_VALIDATE_EMAIL)){
                $_replyto[$k] = $v;
            }
        }

        $_attach = [];
        foreach($attach as $v){
            // Debug::dump($v);die();
            if(isset($v['filepath']) && isset($v['filename']) && isset($v['filetype'])){
                if(file_exists($v['filepath'])){
                    $_attach[] = $v;
                }
            }
        }
        // Debug::dump($_attach);die();

        if($subject=="" || $subject==null || count($_to)<=0 || 
        (($msg == "" || $msg == null) && ($htmlMarkup == "" || $htmlMarkup == null))){
            $ret['msg'] = "invalid param";
        }else{
            try{
                $message = new Message();
                $message->setSubject($subject);
                $message->setEncoding($encode);
                foreach($_from as $k=>$v){
                    if(!(is_numeric($k))){
                        $message->addFrom($v,$k);
                    }else{
                        $message->addFrom($v);
                    }
                }
                foreach($_cc as $k=>$v){
                    if(!(is_numeric($k))){
                        $message->addCc($v,$k);
                    }else{
                        $message->addCc($v);
                    }
                }
                foreach($_bcc as $k=>$v){
                    if(!(is_numeric($k))){
                        $message->addBcc($v,$k);
                    }else{
                        $message->addBcc($v);
                    }
                }
                foreach($_replyto as $k=>$v){
                    if(!(is_numeric($k))){
                        $message->addReplyTo($v,$k);
                    }else{
                        $message->addReplyTo($v);
                    }
                }
                foreach($headers as $k=>$v){
                    $message->getHeaders()->addHeaderLine($k,$v);
                }
                // $message->setBody($msg);
                $text           = new MimePart($msg);
                $text->type     = Mime::TYPE_TEXT;
                $text->charset  = $encode;
                $text->encoding = Mime::ENCODING_QUOTEDPRINTABLE;

                $html           = new MimePart($htmlMarkup."<br>");
                $html->type     = Mime::TYPE_HTML;
                $html->charset  = $encode;
                $html->encoding = Mime::ENCODING_QUOTEDPRINTABLE;

                $body = new MimeMessage();
                $body->setParts([$text, $html]);

                // $contentPart[] = new MimePart($content->generateMessage());

                foreach($_attach as $v){
                    $tmpattach             = new MimePart(fopen($v['filepath'], 'r'));
                    $tmpattach->type        = $v['filetype'];
                    $tmpattach->filename    = $v['filename'];
                    $tmpattach->disposition = Mime::DISPOSITION_ATTACHMENT;
                    $tmpattach->encoding    = Mime::ENCODING_BASE64;
                    // $contentPart[] = $tmpattach;
                    $body->addPart($tmpattach);
                }
                // Debug::dump($body);die();
                // $body = new MimeMessage();
                // $body->setParts($contentPart);
                $message->setBody($body);

                // $contentTypeHeader = $message->getHeaders()->get('Content-Type');
                // if(count($_attach)>0){
                //     $contentTypeHeader->setType('multipart/related');
                // }else{
                //     $contentTypeHeader->setType('multipart/alternative');
                // }

                $ret['msg'] = "failed send message";
                $ret['data'] = $message->toString();

                foreach($_to as $k=>$v){
                    $tmpmsg = clone $message;
                    if(!(is_numeric($k))){
                        $tmpmsg->addTo($v,$k);
                    }else{
                        $tmpmsg->addTo($v);
                    }
                    $this->smtptransport->send($message);
                    $ret[$v]['ret'] = true;
                    $ret[$v]['msg'] = "success send message";
                }
                $ret['ret'] = true;
                $ret['msg'] = "success send message";
            }catch(Exception $e){
                $ret['msg'] = $e->getMessage();
            }
        }

        return $ret;
    }

}