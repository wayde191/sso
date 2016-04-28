<?php
Class Ihfeedback extends CI_Model
{
    public function feedback(){
        
        $uid = isset($_POST['userId']) ? $_POST['userId'] : 0;
        $token = isset($_POST['token']) ? $_POST['token'] : '';
        
        $appname = $_POST['appname'];
        $appversion = $_POST['appversion'];
        $platform = isset($_POST['platform']) ? $_POST['platform'] : 'unrelizeable';
        $os = isset($_POST['os']) ? $_POST['os'] : 'unrelizeable';
        $device = isset($_POST['device']) ? $_POST['device'] : 'unrelizeable';
        $description = $_POST['description'];
        
        if(!$description) {
            echo json_encode(array("status" => 0, "errorCode" =>2010));
            return;
        }
        
        date_default_timezone_set('Asia/Chongqing');
        $date = date('Y-m-d H:i:s');
        
        $this->load->database();
        
        $sql = "INSERT INTO  `ih_feedback` (
        `user_id` ,
        `user_token` ,
        `appname` ,
        `appversion` ,
        `platform` ,
        `os` ,
        `device` ,
        `description` ,
        `date`
        )
        VALUES (
                '$uid', '$token', '$appname', '$appversion', '$platform', '$os', '$device', '$description', '$date'
                )";
        
        $this->db->query($sql);
        
        if (1 == $this->db->affected_rows()) {
            echo json_encode(array("status" => 1));
        } else {
            echo json_encode(array("status" => 0, "errorCode" =>2001));
        }
        
    }
}
    ?>