<?php
Class Ihproud extends CI_Model
{
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
?>