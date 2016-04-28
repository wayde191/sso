<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
    
    // ErrorCode start with 900
    
    class Ihuser extends CI_Controller {
        
        function __construct()
        {
            parent::__construct();
            $this->load->model('ihsession','',TRUE);
        }
        
        public function index()
        {
        }
        
        public function isUserLoggedIn()
        {
            $this->ihsession->refreshSession();
			$check = isset($_SESSION['ih_login_username']) ? $_SESSION['ih_login_username'] : NULL;
            if(isset($check)) {
                $session = mysql_query("select user_email from ih_users where user_email='$check' ");
				$row = mysql_fetch_array($session);
				$login_session=$row['user_email'];
				if(!isset($login_session)){
				echo("1no");
					return FALSE;
				} else {
				echo("yes");
					return TRUE;
				}
            } else {
			echo("no");
				return FALSE;
			}
        }
		
		public function logout()
		{
			session_start();
			setcookie(session_name(), session_id(), -1, '/');
			session_unset();
			if(session_destroy()){
				echo json_encode(array("status" => 1));
			} else {
				echo json_encode(array("status" => 0));
			}
		}
        
// http://127.0.0.1/api/index.php/ihuser/login?ihakulaID=%22haha%22&password=%22lala%22
// http://127.0.0.1/api/index.php/ihuser/login?ihakulaID=a&password=1

        public function login()
		{
			$username = isset($_POST['ihakulaID']) ? $_POST['ihakulaID'] : NULL;
            $password = isset($_POST['password']) ? $_POST['password'] : NULL;
            
            if(!$username || !$password) {
                echo json_encode(array("status" => 0, "errorCode" => 900));//not empty
                return;
            }
        
            $this->load->helper('security');
            $pwdMD5Str = do_hash($password, 'md5');
            
            $this->load->database();
        
            $query = 'SELECT * FROM ih_users WHERE user_email="'. $username . '"';
            $query = $this->db->query($query);
            
            if (1 == count( $query->result())) {
                $query = 'SELECT * FROM ih_users WHERE user_email="'. $username . '" and user_pass="' . $pwdMD5Str .'"';
                $query = $this->db->query($query);
                
                if (1 == count( $query->result())) {
                    $queryFirstRow = array_shift($query->result());
                    
                    $userLoggedInCount = $queryFirstRow->user_login_times;
                    $userLoggedInCount++;
                    
                    date_default_timezone_set('Asia/Chongqing');
                    $date = '"'. date('Y-m-d H:i:s') . '"';
                    
                    $sql = "UPDATE `ih_users` SET `user_login_times` = " . $userLoggedInCount . ", `user_lasttime_login` = ". $date ." WHERE  `user_email` ='" . $username . "'";
                    $this->db->query($sql);
                    
                    $this->ihsession->refreshSession();
                    $_SESSION['ih_login_username'] = $username;
                    
                    echo json_encode(array("status" => 1, "user" => array("email" => $queryFirstRow->user_email, "id" => $queryFirstRow->ID, "group_id" => $queryFirstRow->group_id, "name" => $queryFirstRow->user_nickname, "role" => $queryFirstRow->role, "platform" => $queryFirstRow->platform, "sex" => $queryFirstRow->sex, "avatar" => $queryFirstRow->avatar, "registeredTime" => $queryFirstRow->user_registered, "latestLoggedinTime" => $queryFirstRow->user_lasttime_login, "phone" => $queryFirstRow->phone)));
                    
                } else {
                    echo json_encode(array("status" => 0, "errorCode" => 909));//password not exist
                }
            } else {
                echo json_encode(array("status" => 0, "errorCode" => 910));//email not exist
            }
            
            return;
        }
        
// http://127.0.0.1/api/index.php/ihuser/register?ihakulaID=aa&password=1&confirmPwd=1

        public function register(){
			
            $nickname = isset($_POST['nickname']) ? $_POST['nickname'] : NULL;
            $username = isset($_POST['ihakulaID']) ? $_POST['ihakulaID'] : NULL;
            $password = isset($_POST['password']) ? $_POST['password'] : NULL;
			$confirmPwd = isset($_POST['confirmPwd']) ? $_POST['confirmPwd'] : NULL;
            
            if(!$nickname || !$username || !$password || !$confirmPwd) {
                echo json_encode(array("status" => 0, "errorCode" => 900));//not empty
                return;
            }else if($password != $confirmPwd){
                echo json_encode(array("status" => 0, "errorCode" => 901));//not equal
                return;
            }
            
            $this->load->database();
            
            $query = 'SELECT * FROM ih_users WHERE user_email="'. $username . '"';
            $query = $this->db->query($query);
            
            if (1 == count( $query->result())) {
                echo json_encode(array("status" => 0, "errorCode" => 902));//email exist
                return;
            }
            
            date_default_timezone_set('Asia/Chongqing');
            $date = '"'. date('Y-m-d H:i:s') . '"';
            $this->load->helper('security');
            $pwdMD5Str = do_hash($password, 'md5');
            
            $sql = "INSERT INTO  `ih_users` (
            `user_nickname` ,
            `user_email` ,
            `user_pass` ,
            `user_registered` ,
            `user_lasttime_login`
            )
            VALUES (
                    '$nickname', '$username', '$pwdMD5Str', $date, $date
                    )";
            
            $this->db->query($sql);
            
            if (1 == $this->db->affected_rows()) {
                echo json_encode(array("status" => 1));
            } else {
                echo json_encode(array("status" => 0, "errorCode" => 903));//insert error
            }
            
            return;
        }
    }