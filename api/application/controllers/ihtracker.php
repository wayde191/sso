<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
    
    include("ihuser.php");
    
    class Ihtracker extends CI_Controller {
        
        function __construct()
        {
            parent::__construct();
            $this->load->model('ihsession','',TRUE);
            $this->load->model('ihproud', '', TRUE);
            $this->load->model('ihfeedback', '', TRUE);
            $this->ihuser = new Ihuser();
            $this->load->database();
        }
        
        public function index()
        {
        }
        
        public function checkSecretKey()
        {
            $key = isset($_POST['sCode']) ? $_POST['sCode'] : NULL;
            if($key != "ihakula.tracker.scode") {
                return FALSE;
            }
            return TRUE;
        }
        
        public function uploadTrack(){
            if(!$this->checkSecretKey()){
                echo json_encode(array("status" => 1));
                return;
            }
            
            $user_id = isset($_POST['user_id']) ? $_POST['user_id'] : 0;
            $uuid = isset($_POST['uuid']) ? $_POST['uuid'] : NULL;
            $appKey = isset($_POST['appKey']) ? $_POST['appKey'] : NULL;
            $version = isset($_POST['version']) ? $_POST['version'] : NULL;
            $eventName = isset($_POST['eventName']) ? $_POST['eventName'] : NULL;
            $eventLevel = isset($_POST['eventLevel']) ? $_POST['eventLevel'] : NULL;
            $language = isset($_POST['language']) ? $_POST['language'] : NULL;
            $platform = isset($_POST['platform']) ? $_POST['platform'] : NULL;
            $os = isset($_POST['os']) ? $_POST['os'] : NULL;
            $device = isset($_POST['device']) ? $_POST['device'] : NULL;
            
            date_default_timezone_set('Asia/Chongqing');
            $date = date('Y-m-d H:i:s');
            
            if(!$appKey || !$eventName) {
                echo json_encode(array("status" => 0, "errorCode" => 1200));//not empty
                return;
            }
            
            $sql = "INSERT INTO  `ih_tracker` (
            `user_id` ,
            `app_key` ,
            `unique_uid` ,
            `platform` ,
            `os` ,
            `device` ,
            `version` ,
            `language` ,
            `event_name` ,
            `event_level` ,
            `date`
            )
            VALUES (
                    '$user_id', '$appKey', '$uuid', '$platform', '$os','$device','$version','$language','$eventName','$eventLevel', '$date'
                    )";
            
            $this->db->query($sql);
            
            echo json_encode(array("status" => 1));
        }
        
        // Users ==================================================================
        public function uploadtoken() {
            
            if(!$this->checkSecretKey()){
                echo json_encode(array("status" => 1));
                return;
            }
            
            $app = $_POST['app'];
            $platform = $_POST['platform'];
            $os = $_POST['os'];
            $device = $_POST['device'];
            $version = $_POST['version'];
            $language = $_POST['language'];
            $token = $_POST['token'];
            $userId = isset($_POST['userId']) ? $_POST['userId'] : 0;
            
            if(!$token) {
                echo json_encode(array("status" => 0, "errorCode" =>2010));
                return;
            }
            
            date_default_timezone_set('Asia/Chongqing');
            $date = date('Y-m-d H:i:s');
            
            $query = 'SELECT * FROM ih_users_token WHERE token="'. $token . '"';
            $query = $this->db->query($query);
            
            if (1 == count( $query->result())) {
                
                $queryFirstRow = array_shift($query->result());
                
                $userPlayedCount = $queryFirstRow->times;
                $userPlayedCount++;
                
                $sql = "UPDATE `ih_users_token` SET `times` = " . $userPlayedCount . ", `date` = '". $date ."' WHERE  `token` ='" . $token . "'";
                $this->db->query($sql);
                
            } else {
                $sql = "INSERT INTO  `ih_users_token` (
                `app` ,
                `user_id` ,
                `platform` ,
                `os` ,
                `device` ,
                `version` ,
                `language` ,
                `token` ,
                `times` ,
                `date` ,
                `registed_date`
                )
                VALUES (
                        '$app', '$userId', '$platform', '$os', '$device', '$version', '$language', '$token', '1', '$date', '$date'
                        )";
                
                $this->db->query($sql);
            }
            
            echo json_encode(array("status" => 1));
        }
        
        // User Model
        public function logout()
        {
            if(!$this->checkSecretKey()){
                echo json_encode(array("status" => 0, "errorCode" => 1108)); //scode error
                return;
            }
            $this->ihuser->logout();
        }
        
        public function login()
        {
            if(!$this->checkSecretKey()){
                echo json_encode(array("status" => 0, "errorCode" => 1108)); //scode error
                return;
            }
            
            $this->ihuser->login();
        }
        
        public function register(){
            if(!$this->checkSecretKey()){
                echo json_encode(array("status" => 0, "errorCode" => 1108)); //scode error
                return;
            }
            
            $this->ihuser->register();
        }
        
        public function feedback(){
            
            if(!$this->checkSecretKey()){
                echo json_encode(array("status" => 0, "errorCode" => 1108)); //scode error
                return;
            }
            
            $this->ihfeedback->feedback();
        }
        
        public function getproud(){
            if(!$this->checkSecretKey()){
                echo json_encode(array("status" => 0, "errorCode" => 1108)); //scode error
                return;
            }
            
            $this->ihproud->getproud();
        }
        
    }
    
    ?>
