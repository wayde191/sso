<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
    
    //http://127.0.0.1/api/index.php/Youku/
    //code=8651b00cb152fcb8547496577cc69de6&state=xyz&response_type=login
    
    class Youku extends CI_Controller {
        
        function __construct()
        {
            parent::__construct();
        }
        
        public function index()
        {
            $code = isset($_GET['code']) ? $_GET['code'] : NULL;
            $state = isset($_GET['state']) ? $_GET['state'] : NULL;
            $response_type = isset($_GET['response_type']) ? $_GET['response_type'] : NULL;
            
            $res = NULL;
            if (!isset($_GET['code'])) {
                $res = "<h1>404: Not Found</h1>";
            }else{
                $res =
                "<h1>Thoughtworks ...</h1>" .
                "<p>Code:" . $code ."</p>" .
                "<p>State:" . $state ."</p>" .
                "<p>Response Type:" . $response_type ."</p>";
            }
            
            echo $res;
        }
    }
    
?>
