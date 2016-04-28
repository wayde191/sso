<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
    
    include("ihuser.php");
    
    class Dota extends CI_Controller {
        
        function __construct()
        {
            parent::__construct();
            $this->load->model('ihsession','',TRUE);
            $this->load->model('ihfeedback', '', TRUE);
            $this->load->model('ihproud', '', TRUE);
            $this->ihuser = new Ihuser();
            $this->load->database();
        }
        
        public function index()
        {
        }
        
        public function checkSecretKey()
		{
            // test
            return TRUE;
        
            $key = isset($_POST['sCode']) ? $_POST['sCode'] : NULL;
            if($key != "ihakula.idota.scode") {
                return FALSE;
            }
            return TRUE;
		}
        
        public function getHeros() {
            
            if(!$this->checkSecretKey()){
				echo json_encode(array("status" => 1));
				return;
			}
                
            $herosArr = array();
            
            $this->load->database();
            $query = 'SELECT * FROM ih_dota_hero order by ID asc';
            $query = $this->db->query($query);
            foreach ($query->result() as $row)
            {
                $hero = array("id" => $row->id,
                              "name" => $row->name,
                              "short_name" => $row->short_name,
                              "en_name" => $row->en_name,
                              "type" => $row->type,
                              "dota1_icon_name" => $row->dota1_icon_name,
                              "dota2_icon_name" => $row->dota2_icon_name);
                
                array_push($herosArr, $hero);
            }
            
             echo json_encode(array("status" => 1, "data" => $herosArr));
            
        }
        
        public function getAllHeroRecommends() {
            
            if(!$this->checkSecretKey()){
				echo json_encode(array("status" => 1));
				return;
			}
            
            $allHeroRecom = array();
            $query = "SELECT * FROM `ih_dota_hero_item` WHERE author_id='-99'";
            $query = $this->db->query($query);
            foreach ($query->result() as $row) {
                $heroRecom = array("id" => $row->hero_id,
                                   "recommend" => $row->recommend,
                                   "pro_phase" => $row->pro_phase,
                                   "meta_phase" => $row->meta_phase,
                                   "ana_phase" => $row->ana_phase,
                                   "like_num" => $row->like_num,
                                   "flag" => $row->flag,
                                   "comments_num" => $row->comments_num,
                                   "hero_audio_url" => $row->hero_audio_url,
                                   "add_point_audio_url" => $row->add_point_audio_url,
                                   "hero_item_audio_url" => $row->hero_item_audio_url);
                array_push($allHeroRecom, $heroRecom);
                
            }
            
            echo json_encode(array("status" => 1, "data" => $allHeroRecom));
            
        }
        
        public function getHeroRecommends() {
            
            if(!$this->checkSecretKey()){
				echo json_encode(array("status" => 1));
				return;
			}
            
            $heroId = isset($_POST['heroId']) ? $_POST['heroId'] : NULL;
            
            if(!$heroId) {
                echo json_encode(array("status" => 0, "errorCode" => 1200));//not empty
                return;
            }
            
            $this->load->database();
            
            $heroRecom = NULL;
            $query = "SELECT * FROM `ih_dota_hero_item` WHERE author_id='-99' and hero_id='" . $heroId . "'";
            $query = $this->db->query($query);
            $row = $query->result();
            if(count($row)){
                $qCount = "SELECT count(id) as total from ih_dota_comment where article_id=" . $row[0]->id;
                $qCount = $this->db->query($qCount);
                $countString = $qCount->result();
                $countResult = (int)($countString[0]->total);
                
                $heroRecom = array("id" => $row[0]->hero_id,
                                  "recommend" => $row[0]->recommend,
                                  "pro_phase" => $row[0]->pro_phase,
                                  "meta_phase" => $row[0]->meta_phase,
                                  "ana_phase" => $row[0]->ana_phase,
                                  "like_num" => $row[0]->like_num,
                                  "unlike_num" => $row[0]->unlike_num,
                                  "flag" => $row[0]->flag,
                                  "comments_num" => $countResult,
                                  "hero_audio_url" => $row[0]->hero_audio_url,
                                  "add_point_audio_url" => $row[0]->add_point_audio_url,
                                  "hero_item_audio_url" => $row[0]->hero_item_audio_url);
                
            } else {
                echo json_encode(array("status" => 0, "errorCode" => 1201));//hero id error
                return;
            }
            
            echo json_encode(array("status" => 1, "data" => $heroRecom));
            
        }
        
        public function dolikeComment(){
            if(!$this->checkSecretKey()){
				echo json_encode(array("status" => 1));
				return;
			}
            
            $articleId = isset($_POST['commentId']) ? $_POST['commentId'] : NULL;
            
            if(!$articleId) {
                echo json_encode(array("status" => 0, "errorCode" => 1200));//not empty
                return;
            }
            
            $query = 'SELECT * FROM ih_dota_comment WHERE id="'. $articleId . '"';
            $query = $this->db->query($query);
            
            if (1 == count( $query->result())) {
                
                $queryFirstRow = array_shift($query->result());
                
                $userPlayedCount = $queryFirstRow->like_num;
                $userPlayedCount++;
                
                $sql = "UPDATE `ih_dota_comment` SET `like_num` = " . $userPlayedCount . " WHERE  `id` ='" . $articleId . "'";
                $this->db->query($sql);
                
                echo json_encode(array("status" => 1));
                return;
            }
            
            echo json_encode(array("status" => 0, "errorCode" => 1200));// empty hero
        }
        
        public function dolike(){
            if(!$this->checkSecretKey()){
				echo json_encode(array("status" => 1));
				return;
			}
            
            $articleId = isset($_POST['articleId']) ? $_POST['articleId'] : NULL;
            
            if(!$articleId) {
                echo json_encode(array("status" => 0, "errorCode" => 1200));//not empty
                return;
            }
            
            $query = 'SELECT * FROM ih_dota_hero_item WHERE id="'. $articleId . '"';
            $query = $this->db->query($query);
            
            if (1 == count( $query->result())) {
                
                $queryFirstRow = array_shift($query->result());
                
                $userPlayedCount = $queryFirstRow->like_num;
                $userPlayedCount++;
                
                $sql = "UPDATE `ih_dota_hero_item` SET `like_num` = " . $userPlayedCount . " WHERE  `id` ='" . $articleId . "'";
                $this->db->query($sql);
                
                echo json_encode(array("status" => 1));
                return;
            }
            
            echo json_encode(array("status" => 0, "errorCode" => 1200));// empty hero
        }
        
        public function dounlike(){
            if(!$this->checkSecretKey()){
				echo json_encode(array("status" => 1));
				return;
			}
            
            $articleId = isset($_POST['articleId']) ? $_POST['articleId'] : NULL;
            
            if(!$articleId) {
                echo json_encode(array("status" => 0, "errorCode" => 1200));//not empty
                return;
            }
            
            $query = 'SELECT * FROM ih_dota_hero_item WHERE id="'. $articleId . '"';
            $query = $this->db->query($query);
            
            if (1 == count( $query->result())) {
                
                $queryFirstRow = array_shift($query->result());
                
                $userPlayedCount = $queryFirstRow->unlike_num;
                $userPlayedCount++;
                
                $sql = "UPDATE `ih_dota_hero_item` SET `unlike_num` = " . $userPlayedCount . " WHERE  `id` ='" . $articleId . "'";
                $this->db->query($sql);
                
                echo json_encode(array("status" => 1));
                return;
            }
            
            echo json_encode(array("status" => 0, "errorCode" => 1200));// empty hero
        }
        
        public function uploadComment(){
            if(!$this->checkSecretKey()){
				echo json_encode(array("status" => 1));
				return;
			}
            
            $articleId = isset($_POST['articleId']) ? $_POST['articleId'] : NULL;
            $user_id = isset($_POST['user_id']) ? $_POST['user_id'] : 0;
            $parent_comment_id = isset($_POST['parent_comment_id']) ? $_POST['parent_comment_id'] : 0;
            $comment = isset($_POST['comment']) ? $_POST['comment'] : NULL;
            $comment_address = isset($_POST['comment_address']) ? $_POST['comment_address'] : NULL;
            
            date_default_timezone_set('Asia/Chongqing');
            $date = date('Y-m-d H:i:s');
            
            if(!$articleId || !$comment) {
                echo json_encode(array("status" => 0, "errorCode" => 1200));//not empty
                return;
            }
            
            $sql = "INSERT INTO  `ih_dota_comment` (
            `article_id` ,
            `user_id` ,
            `parent_comment_id` ,
            `comment` ,
            `comment_address` ,
            `create_date`
            )
            VALUES (
                    '$articleId', '$user_id', '$parent_comment_id', '$comment', '$comment_address', '$date'
                    )";
            
            $this->db->query($sql);
            
            echo json_encode(array("status" => 1));
        }
        
        public function getComments(){
            if(!$this->checkSecretKey()){
				echo json_encode(array("status" => 1));
				return;
			}
            
            $articleId = isset($_POST['articleId']) ? $_POST['articleId'] : NULL;
            $pageIndex = isset($_POST['pageIndex']) ? $_POST['pageIndex'] : 1;
            
            $rowsPerPage = isset($_POST['rowsPerPage']) ? $_POST['rowsPerPage'] : 10;
            $recordStartIndex = $rowsPerPage * ($pageIndex - 1);
            
            if(!$articleId || !$pageIndex) {
                echo json_encode(array("status" => 0, "errorCode" => 1200));//not empty
                return;
            }
            
            $query = "SELECT count(id) as total from ih_dota_comment where parent_comment_id='0' and article_id=" . $articleId;
            $query = $this->db->query($query);
            $countString = $query->result();
            $countResult = (int)($countString[0]->total);
            $totalPage = ceil($countResult / $rowsPerPage);
            
            $hottestPosts = array();
            if (0 == $recordStartIndex) {
                $query = "SELECT * from ih_dota_comment where parent_comment_id='0' and like_num>5 and article_id=" . $articleId . " order by like_num desc limit 0 , 10";
                $query = $this->db->query($query);
                foreach ($query->result() as $row) {
                    $queryChilden = "SELECT * from ih_dota_comment where parent_comment_id=" .$row->id. " and article_id=" . $articleId . " order by create_date asc";
                    $queryChild = $this->db->query($queryChilden);
                    $children = array();
                    foreach ($queryChild->result() as $childrow) {
                        $child = array("id" => $childrow->id,
                                           "article_id" => $childrow->article_id,
                                           "user_id" => $childrow->user_id,
                                           "parent_comment_id" => $childrow->parent_comment_id,
                                           "comment" => $childrow->comment,
                                           "like_num" => $childrow->like_num,
                                           "comment_address" => $childrow->comment_address,
                                           "create_date" => $childrow->create_date);
                        array_push($children, $child);
                    }
                    
                    $heroRecom = array("id" => $row->id,
                                       "article_id" => $row->article_id,
                                       "user_id" => $row->user_id,
                                       "parent_comment_id" => $row->parent_comment_id,
                                       "comment" => $row->comment,
                                       "like_num" => $row->like_num,
                                       "comment_address" => $row->comment_address,
                                       "create_date" => $row->create_date,
                                       "children" => $children);
                    array_push($hottestPosts, $heroRecom);
                }
            }
            
            $latestPosts = array();
            // 11 ~ 20
            $query = "SELECT * FROM ih_dota_comment where parent_comment_id='0' and article_id=". $articleId ." order by create_date desc limit " . $recordStartIndex . ',' . $rowsPerPage . ';';
            $query = $this->db->query($query);
            foreach ($query->result() as $row)
            {
            
            $queryChilden = "SELECT * from ih_dota_comment where parent_comment_id=" .$row->id. " and article_id=" . $articleId . " order by create_date asc";
            $queryChild = $this->db->query($queryChilden);
            $children = array();
            foreach ($queryChild->result() as $childrow) {
                $child = array("id" => $childrow->id,
                               "article_id" => $childrow->article_id,
                               "user_id" => $childrow->user_id,
                               "parent_comment_id" => $childrow->parent_comment_id,
                               "comment" => $childrow->comment,
                               "like_num" => $childrow->like_num,
                               "comment_address" => $childrow->comment_address,
                               "create_date" => $childrow->create_date);
                array_push($children, $child);
            }
            
            $heroRecom = array("id" => $row->id,
                               "article_id" => $row->article_id,
                               "user_id" => $row->user_id,
                               "parent_comment_id" => $row->parent_comment_id,
                               "comment" => $row->comment,
                               "like_num" => $row->like_num,
                               "comment_address" => $row->comment_address,
                               "create_date" => $row->create_date,
                               "children" => $children);
            array_push($latestPosts, $heroRecom);
            }
            
            echo json_encode(array("status" => 1, "totalPage" => $totalPage, "hottestComments" => $hottestPosts, "latestComments" => $latestPosts));
            
        }
        
        public function getUserHeroRecommends() {
            
            if(!$this->checkSecretKey()){
				echo json_encode(array("status" => 1));
				return;
			}
            
            $heroId = isset($_POST['heroId']) ? $_POST['heroId'] : NULL;
            // test
            $heroId = 112;
            
            $pageIndex = isset($_POST['pageIndex']) ? $_POST['pageIndex'] : NULL;
            // test
            $pageIndex = 1;
            
            $rowsPerPage = isset($_POST['rowsPerPage']) ? $_POST['rowsPerPage'] : 1;
            $recordStartIndex = $rowsPerPage * ($pageIndex - 1);
            
            if(!$heroId || !$pageIndex) {
                echo json_encode(array("status" => 0, "errorCode" => 1200));//not empty
                return;
            }

            $this->load->database();
            
            $query = "SELECT count(id) as total from ih_dota_hero_item where author_id!='-99' and hero_id=" . $heroId;
            $query = $this->db->query($query);
            $countString = $query->result();
            $countResult = (int)($countString[0]->total);
            $totalPage = ceil($countResult / $rowsPerPage);
            
            $hottestPosts = array();
            if (0 == $recordStartIndex) {
                $query = "SELECT * from ih_dota_hero_item where author_id!='-99' and hero_id=" . $heroId . " order by like_num desc limit 0 , 10";
                $query = $this->db->query($query);
                foreach ($query->result() as $row) {
                    $heroRecom = array("id" => $row->id,
                                       "recommend" => $row->recommend,
                                       "pro_phase" => $row->pro_phase,
                                       "meta_phase" => $row->meta_phase,
                                       "ana_phase" => $row->ana_phase,
                                       "like_num" => $row->like_num,
                                       "flag" => $row->flag,
                                       "comments_num" => $row->comments_num,
                                       "hero_audio_url" => $row->hero_audio_url,
                                       "add_point_audio_url" => $row->add_point_audio_url,
                                       "hero_item_audio_url" => $row->hero_item_audio_url);
                    array_push($hottestPosts, $heroRecom);
                }
            }
            
            $latestPosts = array();
            // 11 ~ 20
            $query = "SELECT * FROM ih_dota_hero_item where author_id!='-99' and hero_id=". $heroId ." order by create_date desc limit " . $recordStartIndex . ',' . $rowsPerPage . ';';
            $query = $this->db->query($query);
            foreach ($query->result() as $row)
            {
                $heroRecom = array("id" => $row->id,
                                   "recommend" => $row->recommend,
                                   "pro_phase" => $row->pro_phase,
                                   "meta_phase" => $row->meta_phase,
                                   "ana_phase" => $row->ana_phase,
                                   "like_num" => $row->like_num,
                                   "flag" => $row->flag,
                                   "comments_num" => $row->comments_num,
                                   "hero_audio_url" => $row->hero_audio_url,
                                   "add_point_audio_url" => $row->add_point_audio_url,
                                   "hero_item_audio_url" => $row->hero_item_audio_url,
                                   "create_date" => $row->create_date);
                array_push($latestPosts, $heroRecom);
            }
            
            echo json_encode(array("status" => 1, "totalPage" => $totalPage, "hottestPosts" => $hottestPosts, "latestPosts" => $latestPosts));
        }
        
        public function getItemsAndSkills() {
            
            if(!$this->checkSecretKey()){
				echo json_encode(array("status" => 1));
				return;
			}
            $this->load->database();
            
            $herosArr = array();
            $query = 'SELECT * FROM ih_dota_item order by ID asc';
            $query = $this->db->query($query);
            foreach ($query->result() as $row)
            {
                $hero = array("id" => $row->id,
                              "name" => $row->name,
                              "short_name" => $row->short_name,
                              "price" => $row->price,
                              "purchase_address" => $row->purchase_address,
                              "dota1_icon_name" => $row->dota1_icon_name,
                              "dota2_icon_name" => $row->dota2_icon_name,
                              "ref" => $row->ref);
                
                array_push($herosArr, $hero);
            }
            
            $skillsArr = array();
            $query = 'SELECT * FROM ih_dota_skill order by ID asc';
            $query = $this->db->query($query);
            foreach ($query->result() as $row)
            {
                $hero = array("id" => $row->id,
                              "hero_id" => $row->hero_id,
                              "name" => $row->name,
                              "hot_key" => $row->hot_key,
                              "description" => $row->description,
                              "dota1_icon_name" => $row->dota1_icon_name,
                              "dota2_icon_name" => $row->dota2_icon_name);
                
                array_push($skillsArr, $hero);
            }
            
            echo json_encode(array("status" => 1, "data" => array("items" => $herosArr, "skills" => $skillsArr)));
            
        }
        
        public function saveAudio($audioName) {
            $folder = "/var/www/images/dota/audio/";
            $audioName = 'voice_file_' . $audioName;
            
            date_default_timezone_set('Asia/Chongqing');
            $storedFileName = $folder . date("YmdHis");
            
            if (is_uploaded_file($_FILES[$audioName]['tmp_name']))  {
                if (move_uploaded_file($_FILES[$audioName]['tmp_name'], $storedFileName)) {
                    return $storedFileName;
                } else {
                    return NULL;
                };
            } else {
                return NULL;
            };
        }
        
        public function uploadUserRecommend() {
            
            // Use http://freegeoip.net/json/ , in front end, if user not allow address, we use this one
            // If no address from front end, server we will use the following one
            // $location = file_get_contents('http://freegeoip.net/json/'.$_SERVER['REMOTE_ADDR']);
            // print_r($location);
            
            if(!$this->checkSecretKey()){
                echo json_encode(array("status" => 0, "errorCode" => 1108)); //scode error
                return;
            }
            
//            if(1 == $this->ihsession->refreshSession()){
                $heroId = isset($_POST['heroId']) ? $_POST['heroId'] : NULL;
                $userId = isset($_POST['userId']) ? $_POST['userId'] : 0;
                
                $flag = isset($_POST['flag']) ? $_POST['flag'] : 0;
                
                $recommend = isset($_POST['recommend']) ? $_POST['recommend'] : '';
                $pro_phase = isset($_POST['pro_phase']) ? $_POST['pro_phase'] : '';
                $meta_phase = isset($_POST['meta_phase']) ? $_POST['meta_phase'] : '';
                $ana_phase = isset($_POST['ana_phase']) ? $_POST['ana_phase'] : '';
                
                if(!$recommend || ($pro_phase && $meta_phase && $ana_phase)) {
                    // recomment or item cannot empty at same time
                    echo json_encode(array("status" => 0, "errorCode" => 1170));
                    return;
                }
                
                $add_point_comment = isset($_POST['add_point_comment']) ? $_POST['add_point_comment'] : NULL;
                $hero_pro_item_comment = isset($_POST['hero_pro_item_comment']) ? $_POST['hero_pro_item_comment'] : NULL;
                $hero_meta_item_comment = isset($_POST['hero_meta_item_comment']) ? $_POST['hero_meta_item_comment'] : NULL;
                $hero_ana_item_comment = isset($_POST['hero_ana_item_comment']) ? $_POST['hero_ana_item_comment'] : NULL;
                
                
                $add_point_audio_name = isset($_POST['recommend_add_point_audio_name']) ? $_POST['recommend_add_point_audio_name'] : NULL;
                $hero_item_audio_name = isset($_POST['recomment_hero_item_audio_name']) ? $_POST['recomment_hero_item_audio_name'] : NULL;
                
                $addPointAudioName = NULL;
                if ( $add_point_audio_name != NULL ) {
                   $addPointAudioName = $this->saveAudio($add_point_audio_name);
                    if($addPointAudioName == NULL){
                        echo json_encode(array("status" => 0, "errorCode" => 1188)); //upload error
                        return;
                    }
                }
                
                $heroItemAudioName = NULL;
                if ( $hero_item_audio_name != NULL ) {
                    $heroItemAudioName = $this->saveAudio($hero_item_audio_name);
                    if($heroItemAudioName == NULL){
                        echo json_encode(array("status" => 0, "errorCode" => 1188)); //upload error
                        return;
                    }
                }
            
                date_default_timezone_set('Asia/Chongqing');
                $date = date('Y-m-d H:i:s');
            
                $sql = "INSERT INTO  `ih_dota_hero_item` (
                    `hero_id` ,
                    `author_id` ,
                    `recommend` ,
                    `pro_phase` ,
                    `meta_phase` ,
                    `ana_phase` ,
                    `flag` ,
                    `add_point_audio_url` ,
                    `add_point_comment` ,
                    `hero_item_audio_url` ,
                    `hero_pro_item_comment` ,
                    `hero_meta_item_comment` ,
                    `hero_ana_item_comment` ,
                    `create_date`
                )
                VALUES (
                        '$heroId', '$userId', '$recommend', '$pro_phase', '$meta_phase', '$ana_phase', '$flag', '$addPointAudioName', '$add_point_comment', '$heroItemAudioName', '$hero_pro_item_comment', '$hero_meta_item_comment', '$hero_ana_item_comment', '$date'
                        )";
                
                $this->db->query($sql);
            
                echo json_encode(array("status" => 1));
//            }
        }
        
        // Backup =================================================
        public function getItems() {
            
            if(!$this->checkSecretKey()){
				echo json_encode(array("status" => 1));
				return;
			}
            
            $herosArr = array();
            
            $this->load->database();
            $query = 'SELECT * FROM ih_dota_item order by ID asc';
            $query = $this->db->query($query);
            foreach ($query->result() as $row)
            {
                $hero = array("id" => $row->id,
                              "name" => $row->name,
                              "short_name" => $row->short_name,
                              "price" => $row->price,
                              "purchase_address" => $row->purchase_address,
                              "dota1_icon_name" => $row->dota1_icon_name,
                              "dota2_icon_name" => $row->dota2_icon_name,
                              "ref" => $row->ref);
                
                array_push($herosArr, $hero);
            }
            
            echo json_encode(array("status" => 1, "data" => $herosArr));
            
        }
        
        public function getSkills() {
            
            if(!$this->checkSecretKey()){
				echo json_encode(array("status" => 1));
				return;
			}
            
            $herosArr = array();
            
            $this->load->database();
            $query = 'SELECT * FROM ih_dota_skill order by ID asc';
            $query = $this->db->query($query);
            foreach ($query->result() as $row)
            {
                $hero = array("id" => $row->id,
                              "hero_id" => $row->hero_id,
                              "name" => $row->name,
                              "hot_key" => $row->hot_key,
                              "description" => $row->description,
                              "dota1_icon_name" => $row->dota1_icon_name,
                              "dota2_icon_name" => $row->dota2_icon_name);
                
                array_push($herosArr, $hero);
            }
            
            echo json_encode(array("status" => 1, "data" => $herosArr));
            
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
