<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
    
    //http://127.0.0.1/api/index.php/nhweixin/
    
    class NHWeixin extends CI_Controller {
        
        function __construct()
        {
            parent::__construct();
            $this->token = 'ihakulaweixin';
            $this->rootUrl = 'http://localhost:8090/weixin/event/center';
        }
        
        public function index()
        {
            if (isset($_GET['echostr'])) {
                $this->valid();
            }else{
                $this->fetchResponseMsg();
            }
        }
        
        public function fetchResponseMsg(){
            $reqeustXml = $GLOBALS["HTTP_RAW_POST_DATA"];
            $keywork = "ihakula_northern_hemisphere";
            
            $data = array('ihakula_request' => $keywork, 'request_xml' => $reqeustXml);
            $res = $this->send_request($this->rootUrl, $data, 'POST');
            
            $this->logger('Request: ' . $reqeustXml);
            $this->logger('Response: ' . $res);
            
            echo $res;
        }
        
        private function logger($log_content)
        {
            $max_size = 10000;
            $log_filename = "/var/www/php/log.xml";
            if(file_exists($log_filename) and (abs(filesize($log_filename)) > $max_size)){unlink($log_filename);}
            file_put_contents($log_filename, date('Y-m-d H:i:s')." ".$log_content."\r\n", FILE_APPEND);
        }
        
        public function valid()
        {
            $echoStr = $_GET["echostr"];
            $this->logger($echoStr);
            if($this->checkSignature()){
                echo $echoStr;
                exit;
            }
        }
        
        private function checkSignature()
        {
            $signature = $_GET["signature"];
            $timestamp = $_GET["timestamp"];
            $nonce = $_GET["nonce"];
            
            $token = $this->token;
            $tmpArr = array($token, $timestamp, $nonce);
            sort($tmpArr, SORT_STRING);
            $tmpStr = implode( $tmpArr );
            $tmpStr = sha1( $tmpStr );
            
            if( $tmpStr == $signature ){
                return true;
            }else{
                return false;
            }
        }
        
        public function responseMsg()
        {
            $postStr = $GLOBALS["HTTP_RAW_POST_DATA"];
            $this->logger($postStr);
            if (!empty($postStr)){
                $postObj = simplexml_load_string($postStr, 'SimpleXMLElement', LIBXML_NOCDATA);
                $fromUsername = $postObj->FromUserName;
                $toUsername = $postObj->ToUserName;
                $keyword = trim($postObj->Content);
                $time = time();
                $textTpl = "<xml>
                <ToUserName><![CDATA[%s]]></ToUserName>
                <FromUserName><![CDATA[%s]]></FromUserName>
                <CreateTime>%s</CreateTime>
                <MsgType><![CDATA[%s]]></MsgType>
                <Content><![CDATA[%s]]></Content>
                <FuncFlag>0</FuncFlag>
                </xml>";
                if($keyword == "?" || $keyword == "�~_")
                {
                    $msgType = "text";
                    $contentStr = date("Y-m-d H:i:s",time()) . ' from wayde sun';
                    $resultStr = sprintf($textTpl, $fromUsername, $toUsername, $time, $msgType, $contentStr);
                    echo $resultStr;
                }
            }else{
                echo "";
                exit;
            }
        }
        
        /**
         * 发送HTTP请求
         *
         * @param string $url 请求地址
         * @param string $method 请求方式 GET/POST
         * @param string $refererUrl 请求来源地址
         * @param array $data 发送数据
         * @param string $contentType
         * @param string $timeout
         * @param string $proxy
         * @return boolean
         */
        public function send_request($url, $data, $method = 'GET', $timeout = 30, $proxy = false, $refererUrl = '') {
            $ch = null;
            if('POST' === strtoupper($method)) {
                $ch = curl_init();
                curl_setopt($ch,CURLOPT_URL, $url);//抓取指定网页
                curl_setopt($ch, CURLOPT_POST, 1);
                curl_setopt($ch, CURLOPT_HEADER,0 );
                curl_setopt($ch, CURLOPT_FRESH_CONNECT, 1);
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
                curl_setopt($ch, CURLOPT_FORBID_REUSE, 1);
                curl_setopt($ch, CURLOPT_TIMEOUT, $timeout);
                if ($refererUrl) {
                    curl_setopt($ch, CURLOPT_REFERER, $refererUrl);
                }
                
                if(is_string($data)){
                    curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
                } else {
                    curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($data));
                }
            } else if('GET' === strtoupper($method)) {
                if(is_string($data)) {
                    $real_url = $url. (strpos($url, '?') === false ? '?' : ''). $data;
                } else {
                    $real_url = $url. (strpos($url, '?') === false ? '?' : ''). http_build_query($data);
                }
                
                $ch = curl_init($real_url);
                curl_setopt($ch, CURLOPT_HEADER, 0);
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
                curl_setopt($ch, CURLOPT_TIMEOUT, $timeout);
                if ($refererUrl) {
                    curl_setopt($ch, CURLOPT_REFERER, $refererUrl);
                }
            } else {
                $args = func_get_args();
                return false;
            }
            
            if($proxy) {
                curl_setopt($ch, CURLOPT_PROXY, $proxy);
            }
            $ret = curl_exec($ch);
            $info = curl_getinfo($ch);
            $contents = array(
                              'httpInfo' => array(
                                                  'send' => $data,
                                                  'url' => $url,
                                                  'ret' => $ret,
                                                  'http' => $info,
                                                  )
                              );
            curl_close($ch);
            return $ret;
        }
        
    }
    
?>
