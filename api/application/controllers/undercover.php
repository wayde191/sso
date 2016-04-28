<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
    
    // http://localhost/api/index.php/undercover/wordstotalnumber
    // http://localhost/api/index.php/undercover/getwords?startId=1
    
    class Undercover extends CI_Controller {
        
        public function index()
        {
            $this->load->view('welcome_message');
        }
        
        public function insertnewwords(){
            
            $key = $_POST['undercoverKey'];
            if($key != "f37d8eee76c0c03a69050ab7ca7bace3") {
                return;
            }
            
        	$jsonStr = $_POST['newWords'];
            $lan = isset($_POST['lan']) ? $_POST['lan']:NULL;
            $tableName = 'ih_undercover_words';
            if($lan){
                $tableName = 'ih_undercover_words_' . $lan;
            }
            $newWords = json_decode($jsonStr);
            
            $this->load->database();
            
            foreach ($newWords as $word)
            {
                $query = 'SELECT count(id) as total from ' . $tableName . ' WHERE word1="' . $word[0] . '" and word2="' . $word[1] . '"';
                $query = $this->db->query($query);
                $count1 = $query->result();
                $query = 'SELECT count(id) as total from ' . $tableName . ' WHERE word1="' . $word[1] . '" and word2="' . $word[0] . '"';
                $query = $this->db->query($query);
                $count2 = $query->result();
                
                if(0 == $count1[0]->total && 0 == $count2[0]->total) {
                    $sql = "INSERT INTO  `$tableName` (
                    `word1` ,
                    `word2` ,
                    `type`
                    )
                    VALUES (
                            '$word[0]', '$word[1]', 'unused'
                            )";
                    
                    $this->db->query($sql);
                }
            }
        }
        
        public function getappid(){
            
            echo json_encode(array("status" => 1, "appId" => "670832253"));
        }
        
        public function wordstotalnumber(){
            
            $lan = isset($_POST['lan']) ? $_POST['lan']:NULL;
            $tableName = 'ih_undercover_words';
            if($lan){
                $tableName = 'ih_undercover_words_' . $lan;
            }
            
            $this->load->database();
            $query = 'SELECT count(id) as total from ' . $tableName;
            $query = $this->db->query($query);
            $countString = $query->result();
            $countResult = (int)($countString[0]->total);
            
            echo json_encode(array("status" => 1, "totalNumber" => $countResult));
        }
        
        // 2011：表示打电话的次数不能为空
        // 2001：服务器忙，请重试
        public function getwords(){
            
            $key = $_POST['undercoverKey'];
            if($key != "f37d8eee76c0c03a69050ab7ca7bace3") {
                echo json_encode(array("status" => 1, "words" => array()));
                return;
            }
            
            $startId = $_POST['startId'];
            $lan = isset($_POST['lan']) ? $_POST['lan']:NULL;
            $tableName = 'ih_undercover_words';
            if($lan){
                $tableName = 'ih_undercover_words_' . $lan;
            }
            
            $this->load->database();
            
            $query = 'SELECT * FROM ' .$tableName. ' limit ' . $startId .',100';
            $query = $this->db->query($query);
            
            $words = array();
            
            foreach ($query->result() as $row)
            {
                $word = array('id' => $row->ID, 'word1' => $row->word1, 'word2' => $row->word2, 'type' => $row->type);
                array_push($words, $word);
            }
            
            echo json_encode(array("status" => 1, "words" => $words));
            
        }
        
        // 2010：表示反馈意见不能为空
        // 2001：服务器忙，请重试
        public function feedback(){
            
            $key = $_POST['undercoverKey'];
            if($key != "f37d8eee76c0c03a69050ab7ca7bace3") {
                echo json_encode(array("status" => 1));
                return;
            }
            
            $platform = $_POST['platform'];
            $os = $_POST['os'];
            $device = $_POST['device'];
            $description = $_POST['description'];
            
            if(!$description) {
                echo json_encode(array("status" => 0, "errorCode" =>2010));
                return;
            }
            
            date_default_timezone_set('Asia/Chongqing');
            $date = date('Y-m-d H:i:s');
            
            $this->load->database();
            
            $sql = "INSERT INTO  `ih_undercover_feedback` (
            `platform` ,
            `os` ,
            `device` ,
            `description` ,
            `date`
            )
            VALUES (
                    '$platform', '$os', '$device', '$description', '$date'
                    )";
            
            $this->db->query($sql);
            
            if (1 == $this->db->affected_rows()) {
                echo json_encode(array("status" => 1));
            } else {
                echo json_encode(array("status" => 0, "errorCode" =>2001));
            }
            
        }
        
        public function uploadtoken() {
            $key = $_POST['undercoverKey'];
            if($key != "f37d8eee76c0c03a69050ab7ca7bace3") {
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
            
            if(!$token) {
                echo json_encode(array("status" => 0, "errorCode" =>2010));
                return;
            }
            
            date_default_timezone_set('Asia/Chongqing');
            $date = date('Y-m-d H:i:s');
            
            $this->load->database();
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
                        '$app', '$platform', '$os', '$device', '$version', '$language', '$token', '1', '$date', '$date'
                        )";
                
                $this->db->query($sql);
            }
            
            echo json_encode(array("status" => 1));
        }
        
        public function getproud(){
            $tasksArr = array();
            $this->load->database();
            
            $lan = isset($_POST['lan']) ? $_POST['lan']:NULL;
            $tableName = 'ih_products';
            if($lan){
                $tableName = 'ih_products_' . $lan;
            }
            
            $query = 'SELECT * FROM '. $tableName;
            $query = $this->db->query($query);
            foreach ($query->result() as $row)
            {
                $award = array( 'ID' => $row->ID, 'name' => $row->name, 'description' => $row->description, 'keywords' => $row->keywords, 'logo_url' => $row->logo_url, 'qrcode_url' => $row->qrcode_url, 'download_url' => $row->download_url, 'price' => $row->price, 'version' => $row->version, 'os' => $row->os, 'time' => $row->time);
                array_push($tasksArr, $award);
            }
            
            
            echo json_encode(array("status" => 1, "data" => $tasksArr));
            
            
        }
    }