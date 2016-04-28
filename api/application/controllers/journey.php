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

    
   
    public function getproduct(){
    

    
        $tasksArr = array();
        //$totalPage = ceil($countResult / $rowsPerPage);
        $this->load->database();
        
        // 11 ~ 20
//        $query = 'SELECT * FROM ihj_products;';
        $query = 'SELECT * FROM ihj_products order by ID;';
        
        $query = $this->db->query($query);
        foreach ($query->result() as $row)
        {
            $award = array( 'ID' => $row->ID, 'title' => $row->title, 'description' => $row->description, 'image' => $row->image, 'time' => $row->time);
            array_push($tasksArr, $award);
            
        }
        
        
        echo json_encode(array("status" => 1, "data" => $tasksArr));

    
    }
    
    public function getad(){

        $tasksArr = array();
        //$totalPage = ceil($countResult / $rowsPerPage);
        $this->load->database();
        
        // 11 ~ 20
        $query = 'SELECT * FROM ihj_journey_pic;';
        $query = $this->db->query($query);
        foreach ($query->result() as $row)
        {
            $award = array( 'ID' => $row->ID, 'title' => $row->title, 'country' => $row->country, 'image' => $row->image, 'county' => $row->county, 'province' => $row->province, 'name' => $row->name);
            array_push($tasksArr, $award);
            
        }
        
        
        echo json_encode(array("status" => 1, "data" => $tasksArr));
    }
    
    public function getgoods(){
        
       $product_id = $_POST['product_id'];
       // $description = $_POST['description'];
        
        $tasksArr = array();
        //$totalPage = ceil($countResult / $rowsPerPage);
        $this->load->database();
        
        // 11 ~ 20
      //    $query = 'SELECT count(id) as total from wp_scrum_task where project_id=' . $projectID;
        $query = 'SELECT * FROM ihj_goods where product_id =' . $product_id . ' and status!="disable"';
        $query = $this->db->query($query);
        foreach ($query->result() as $row)
        {
            $award = array( 'id' => $row->id, 'title' => $row->title, 'price' => $row->price, 'product_id' => $row->product_id, 'web_name' => $row->web_name);
            array_push($tasksArr, $award);
            
        }
        
        
        echo json_encode(array("status" => 1, "data" => $tasksArr));
        
        
    }
    public function getproud(){
        
        
        
        $tasksArr = array();
        //$totalPage = ceil($countResult / $rowsPerPage);
        $this->load->database();
        
        // 11 ~ 20
        $query = 'SELECT * FROM ih_products;';
        $query = $this->db->query($query);
        foreach ($query->result() as $row)
        {
            $award = array( 'ID' => $row->ID, 'name' => $row->name, 'description' => $row->description, 'keywords' => $row->keywords, 'logo_url' => $row->logo_url, 'qrcode_url' => $row->qrcode_url, 'download_url' => $row->download_url, 'price' => $row->price, 'version' => $row->version, 'os' => $row->os, 'time' => $row->time);
            array_push($tasksArr, $award);
            
        }
        
        
        echo json_encode(array("status" => 1, "data" => $tasksArr));
        
        
    }


}