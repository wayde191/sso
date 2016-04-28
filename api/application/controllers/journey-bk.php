<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
    
    // ErrorCode start with 2000
    //http://localhost/Journey/api/index.php/journey/feedback?platform=ios&os=ios6&device=iphone6&description=好像有这么个事
    //http://localhost/Journey/api/index.php/journey/callme?platform=ios&os=ios6&device=iphone6&address=好像有这么个事&number=18610101010&times=5
    
class Journey extends CI_Controller {
    
    public function index()
    {
        $this->load->view('welcome_message');
    }
    
    // 2011：表示打电话的次数不能为空
    // 2001：服务器忙，请重试
    public function callme(){
        
        $platform = $_POST['platform'];
        $os = $_POST['os'];
        $device = $_POST['device'];
        $address = $_POST['address'];
        $number = $_POST['number'];
        $service_number = $_POST['service_number'];
        $times = $_POST['times'];
        $versionNumber = $_POST['version_number'];
        
        if(!$times) {
            echo json_encode(array("status" => 0, "errorCode" =>2011));
            return;
        }
        
        date_default_timezone_set('Asia/Chongqing');
        $date = date('Y-m-d H:i:s');
        
        $this->load->database();
        
        $sql = "INSERT INTO  `ihj_call_record` (
        `platform` ,
        `os` ,
        `device` ,
        `address` ,
        `number` ,
        `service_number` ,
        `times` ,
        `version_number` ,
        `date`
        )
        VALUES (
                '$platform', '$os', '$device', '$address', '$number', '$service_number', '$times', '$versionNumber', '$date'
                )";
        
        $this->db->query($sql);
        
        if (1 == $this->db->affected_rows()) {
            echo json_encode(array("status" => 1));
        } else {
            echo json_encode(array("status" => 0, "errorCode" =>2001));
        }
        
    }
    
    // 2010：表示反馈意见不能为空
    // 2001：服务器忙，请重试
    public function feedback(){
        
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
        
        $sql = "INSERT INTO  `ihj_feedback` (
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
    
    
    
}