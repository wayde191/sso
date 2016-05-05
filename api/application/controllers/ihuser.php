<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
    
    // ErrorCode start with 900
    
    class IhUser extends CI_Controller {
        
        function __construct()
        {
            parent::__construct();
            date_default_timezone_set('Asia/Chongqing');

            $this->load->helper('security');
            $this->load->model('IhSession', '', TRUE);
            $this->load->model('IhCode', '', TRUE);
            $this->load->database();

            $this->IhSession->start_session(30 * 24 * 60 * 60);
        }
        
        public function index()
        {
            echo "Hello, this is iHakula.com";
        }

        private function isUserLoggedIn($userId)
        {
            if(isset($userId) && isset($_SESSION['user_id']) && ($_SESSION['user_id'] == $userId)) {
                return TRUE;
            } else {
                return FALSE;
            }
        }

        private function getPostParameter($key)
        {
            return isset($_POST[$key]) ? $_POST[$key] : NULL;
        }

        private function getSignature($userId)
        {
            return do_hash($userId.IhCode::i_hakula_secret_key, 'md5');
        }

        private function checkSecretKey()
        {
            $sCode = $this->getPostParameter('sCode');
            return $sCode == IhCode::i_hakula_security_code ? TRUE : FALSE;
        }

        private function getUserInfo($userId)
        {
            $query = 'SELECT * FROM ih_users WHERE phone="'. $userId .'"';
            $query = $this->db->query($query);

            $queryFirstRow = array_shift($query->result());

            echo json_encode(array(
                "status" => IhCode::request_success,
                "user" => array(
                    "email" => $queryFirstRow->user_email,
                    "id" => $queryFirstRow->ID,
                    "group_id" => $queryFirstRow->group_id,
                    "name" => $queryFirstRow->user_nickname,
                    "role" => $queryFirstRow->role,
                    "platform" => $queryFirstRow->platform,
                    "sex" => $queryFirstRow->sex,
                    "avatar" => $queryFirstRow->avatar,
                    "registeredTime" => $queryFirstRow->user_registered,
                    "latestLoggedinTime" => $queryFirstRow->user_lasttime_login,
                    "phone" => $queryFirstRow->phone,
                    "token" => $this->getSignature($userId))));
        }

        private function checkUserExist($userId)
        {
            $query = 'SELECT * FROM ih_users WHERE phone="'. $userId . '"';
            $query = $this->db->query($query);
            return 1 == count( $query->result()) ? TRUE : FALSE;
        }

        private function updateUserLoggedInCounter($userLoggedInCount, $userId)
        {
            $date = '"'. date('Y-m-d H:i:s') . '"';
            $sql = "UPDATE `ih_users` SET `user_login_times` = " . $userLoggedInCount . ", `user_lasttime_login` = ". $date ." WHERE  `phone` ='" . $userId . "'";
            $this->db->query($sql);
        }

        public function login()
        {
            if(!$this->checkSecretKey()){
                echo json_encode(array(
                    "status" => IhCode::request_fails,
                    "errorCode" => IhCode::security_code_error));
                return;
            }

            $userId = $this->getPostParameter('ihakulaID');
            $password = $this->getPostParameter('password');
            $token = $this->getPostParameter('token');

            if($this->isUserLoggedIn($userId)) {
                if ($token == $this->getSignature($userId)) {
                    $this->getUserInfo($userId);
                } else {
                    echo json_encode(array(
                        "status" => IhCode::request_fails,
                        "errorCode" => IhCode::token_not_correct));
                }
            } else {
                $pwdMD5Str = do_hash($password, 'md5');

                if($this->checkUserExist($userId)){
                    $query = 'SELECT * FROM ih_users WHERE phone="'. $userId . '" and user_pass="' . $pwdMD5Str .'"';
                    $query = $this->db->query($query);

                    if (1 == count( $query->result())) {
                        $queryFirstRow = array_shift($query->result());

                        $userLoggedInCount = $queryFirstRow->user_login_times;
                        $userLoggedInCount++;
                        $this->updateUserLoggedInCounter($userLoggedInCount, $userId);
                        $_SESSION['user_id'] = $userId;

                        echo json_encode(array(
                            "status" => IhCode::request_success,
                            "user" => array(
                                "email" => $queryFirstRow->user_email,
                                "id" => $queryFirstRow->ID,
                                "group_id" => $queryFirstRow->group_id,
                                "name" => $queryFirstRow->user_nickname,
                                "role" => $queryFirstRow->role,
                                "platform" => $queryFirstRow->platform,
                                "sex" => $queryFirstRow->sex,
                                "avatar" => $queryFirstRow->avatar,
                                "registeredTime" => $queryFirstRow->user_registered,
                                "latestLoggedinTime" => $queryFirstRow->user_lasttime_login,
                                "phone" => $queryFirstRow->phone,
                                "token" => $this->getSignature($userId))));

                    } else {
                        echo json_encode(array(
                            "status" => IhCode::request_fails,
                            "errorCode" => IhCode::password_wrong));
                    }
                } else {
                    echo json_encode(array(
                        "status" => IhCode::request_fails,
                        "errorCode" => IhCode::user_not_exist));
                }
            }

            return;
        }

        //todo
		
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