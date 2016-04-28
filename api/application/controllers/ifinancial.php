<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
    
    // ErrorCode start with 1100
	
	include("ihuser.php");
    
    class Ifinancial extends CI_Controller {
        
        function __construct()
        {
            parent::__construct();
            $this->load->model('ihsession','',TRUE);
            $this->load->model('ihfeedback', '', TRUE);
            $this->load->model('ihproud', '', TRUE);
            $this->load->model('ihaccount', '', TRUE);
			$this->ihuser = new Ihuser();
			$this->load->database();
        }
        
        public function index()
        {
        }
		
		public function checkSecretKey()
		{
            $key = isset($_POST['sCode']) ? $_POST['sCode'] : NULL;
            if($key != "ihakula.ifinancial.scode") {
                return FALSE;
            }
			return TRUE;
		}
        
        public function getallmembers()
        {
            if(!$this->checkSecretKey()){
                echo json_encode(array("status" => 1));
                return;
            }
            
            $fundsArr = array();
            $query = "SELECT * FROM `ih_nh_member`";
            $query = $this->db->query($query);
            foreach ($query->result() as $row)
            {
                $fund = array( 'id' => $row->ID, 'name' => $row->name, 'phone' => $row->phone);
                array_push($fundsArr, $fund);
            }
            
            echo json_encode(array("status" => 1, "members" => $fundsArr));
        }
		
		public function getallfunds()
		{
			if(!$this->checkSecretKey()){
				echo json_encode(array("status" => 1));
				return;
			}
			
			$fundsArr = array();
			$query = "SELECT * FROM `ih_fund`";
            $query = $this->db->query($query);
            foreach ($query->result() as $row)
            {
                $fund = array( 'id' => $row->ID, 'name' => $row->name, 'code' => $row->code, 'manager' => $row->manager, 'owner' => $row->owner, 'type' => $row->type, 'color' => $row->color);
                array_push($fundsArr, $fund);
            }
			
			echo json_encode(array("status" => 1, "funds" => $fundsArr));
		}
		
		public function getpurchasedfunds()
		{
			if(!$this->checkSecretKey()){
				echo json_encode(array("status" => 1));
				return;
			}

			$uid = isset($_POST['user_id']) ? $_POST['user_id'] : 0;
            $token = isset($_POST['user_token']) ? $_POST['user_token'] : '';
			
			if('' == $uid){
				echo json_encode(array("status" => 0, "errorCode" => 1102)); //token must ready
                return;
			}

			date_default_timezone_set('Asia/Chongqing');
            $minDate = date("Y-m-d",time() - 7*24*60*60);
            $maxDate = date("Y-m-d",time() - 24*60*60);
        
			$today = date("Y/m/d 00:00:00");
			
            $fundYesterdayFinalMoneyArr = array();
            $yesterdayMoneyArr = array();
            $observedFundsArr = array();
            array_push($observedFundsArr, '1');
            array_push($observedFundsArr, '2');
        
			$query = "SELECT * FROM `ih_if_account` WHERE user_id='" . $uid . "'";
            $query = $this->db->query($query);
            $r = $query->result();
            if(count($r)){
                $purchased = $r[0]->all_money;
                $oFunds = explode(',', $purchased);
                for($i = 0; $i < count($oFunds); $i++) {
                    $obj = $oFunds[$i];
                    $keyvalue = explode(':', $obj);
                    if(0 != $keyvalue[1]){
                        array_push($fundYesterdayFinalMoneyArr, array($keyvalue[0] => $keyvalue[1]));
                        array_push($observedFundsArr, $keyvalue[0]);
                    }
                }
                
                // cash
                $purchased = $r[0]->purchased;
                $oFunds = explode(',', $purchased);
                for($i = 0; $i < count($oFunds); $i++) {
                    $obj = $oFunds[$i];
                    $keyvalue = explode(':', $obj);
                    array_push($yesterdayMoneyArr, array($keyvalue[0] => $keyvalue[1]));
                }
            }

			// select date, million_revenue from ih_funds where name = (select concat(name,'(',code,')') as fundname from ih_fund where `ID`='1') and `date` >= '2014-03-11' and `date` <= '2014-03-17' order by date asc;
        
			$observedFundsArr = array_unique($observedFundsArr);
			$fundSevenDayRate = array();
            foreach($observedFundsArr as $fid)
            {
				$query = "SELECT million_revenue FROM `ih_funds` where name = (select concat(name,'(',code,')') as fundname from ih_fund where `ID`=" .$fid. ") and `date` >='" .$minDate. "' and `date` <='" .$maxDate. "' ORDER BY date ASC";
                
				$query = $this->db->query($query);
				
				$r = $query->result();
				$revenuesArr = array();
				foreach($r as $row){
					array_push($revenuesArr, $row->million_revenue);
				}
				
				$fundSevenDayRate = $fundSevenDayRate + array($fid => $revenuesArr);
			}
			
			echo json_encode(array("status" => 1, "mindate" => $minDate, "maxdate" => $maxDate, "funds" => $fundSevenDayRate, "purchasedFunds" => $fundYesterdayFinalMoneyArr, "cash" => $yesterdayMoneyArr));
			
		}
		
		
        
// http://127.0.0.1/api/index.php/ifinancial/uploadtrade?sCode=ihakula.ifinancial.scode&user_id=88907&user_token=tokentoken&purchased=1:50000.0,2:60000.0
public function uploadtrade()
{
    if(!$this->checkSecretKey()){
        echo json_encode(array("status" => 1));
        return;
    }

    $uid = isset($_POST['user_id']) ? $_POST['user_id'] : 0;
    $token = isset($_POST['user_token']) ? $_POST['user_token'] : '';
    $type = isset($_POST['type']) ? $_POST['type'] : 'free';
    
    if('' == $uid){
        echo json_encode(array("status" => 0, "errorCode" => 1102)); //$uid must ready
        return;
    }
    
    $all = $_POST['all'];
    $funds = $_POST['purchased'];
    $fundsArr = explode(',', $funds);
    $validationCashStr = $funds;
    $validationDiscountCashStr = '';

    date_default_timezone_set('Asia/Chongqing');
    $date = date('Y-m-d H:i:s');
    $todaySureTime = date("Y-m-d 15:00:00");

    $query = "SELECT * FROM `ih_if_account` WHERE user_id='" . $uid . "'";
    $query = $this->db->query($query);
    $r = $query->result();

    $accountId = '';
    if(0 != count($r)){
        $purchased = $r[0]->all_money;
        $oldpurchased = $r[0]->purchased;
        $accountId = $r[0]->ID;
        
        if($purchased == $all){
            echo json_encode(array("status" => 0, "errorCode" => 1103)); //no updates
            return;
        }
        
        $validateValueArr = array();
        $discountValueArr = array();
        
        $pfundsArr = explode(',', $purchased);
        for($i = 0; $i < count($fundsArr); $i++){
            $notFound = TRUE;
            $fkeyvalue = explode(':', $fundsArr[$i]);
            for($j = 0; $j < count($pfundsArr); $j++){
                $pkeyvalue = explode(':', $pfundsArr[$j]);
                if($pkeyvalue[0] == $fkeyvalue[0]){
                    $notFound = FALSE;
                    $newpurchased = (float)$fkeyvalue[1] - (float)$pkeyvalue[1];
                    if($newpurchased > 0) {
                        $validateValue = '' . $pkeyvalue[0] . ':' . $newpurchased;
                        array_push($validateValueArr, $validateValue);
                    } elseif($newpurchased < 0) {
                        $discountValue = '' . $pkeyvalue[0] . ':' . $newpurchased;
                        
                        $oldvalueArr = explode(',', $oldpurchased);
                        for($k = 0; $k < count($oldvalueArr); $k++){
                            $oldkeyvalue = explode(':', $oldvalueArr[$k]);
                            if($oldkeyvalue[0] == $pkeyvalue[0]){
                                if($oldkeyvalue[1] > $pkeyvalue[1]){
                                    array_push($discountValueArr, $discountValue);
                                } else {
                                    array_push($validateValueArr, $discountValue);
                                }
                            }
                        }
                        
                    }
                }
            }
            if($notFound){
                $oldpurchased = $oldpurchased . ',' . $fkeyvalue[0] . ':0';
                array_push($validateValueArr, $fundsArr[$i]);
            }
        }
        
        $validationCashStr = join(",",$validateValueArr);
        $validationDiscountCashStr = join(",",$discountValueArr);
        
        // Update all money
        $accountuid = $r[0]->user_id;
        $updateuid = 0;
        if(0 != $accountuid){
            $updateuid = $accountuid;
        } else {
            $updateuid = $uid;
        }
        
        $ttoken = $r[0]->user_token;
        if($token != $ttoken && $token != ''){
            $ttoken = $token;
        }
        
        $sql = "UPDATE  `ih_if_account` SET `user_token` = '" . $ttoken ."', `purchased` = '" . $oldpurchased . "', ".   "`all_money` ='" . $all ."' WHERE  `user_id` ='" . $uid ."'";
        $this->db->query($sql);
        
        if (1 != $this->db->affected_rows()) {
            echo json_encode(array("status" => 0, "errorCode" => 1104)); //update failed
            return;
        }
        
        // update token uid
        if($token != ''){
            $query = "SELECT * FROM `ih_users_token` WHERE token='" . $token . "'";
            $query = $this->db->query($query);
            $r = $query->result();
            if(0 != count($r)){
                $duid = $r[0]->user_id;
                if(0 == $duid && 0!= $uid){
                    $sql = "UPDATE `ih_users_token` SET `user_id` = '" . $uid ."' WHERE  `token` ='" . $token ."'";
                    $this->db->query($sql);
                }
            }
        }
        
    } else {
        $initpurchasedstr = '';
        for($i = 0; $i < count($fundsArr); $i++){
            $f = $fundsArr[$i];
            $keyvalues = explode(':', $f);
            if($i != count($fundsArr) - 1) {
                $initpurchasedstr = $initpurchasedstr . $keyvalues[0] . ':0,';
            }else{
                $initpurchasedstr = $initpurchasedstr . $keyvalues[0] . ':0';
            }
        }
        
        $sql = "INSERT INTO  `ih_if_account` (
        `user_id` ,
        `user_token` ,
        `type` ,
        `purchased` ,
        `all_money` ,
        `date`
        )
        VALUES (
                '$uid', '$token', '$type', '$initpurchasedstr', '$funds', '$date'
                )";
        
        $res = $this->db->query($sql);
        
        $query="SELECT LAST_INSERT_ID()";
        $result=mysql_query($query);
        $rows=mysql_fetch_row($result);
        $accountId = $rows[0];
    }

    if('' != trim($validationDiscountCashStr)){
        $approveDate = date("Y-m-d",time() + 24*60*60);
        
        // if discount, we need to caculate today total income
        
        $sql = "INSERT INTO  `ih_if_certification_cash` (
        `account_id` ,
        `cash` ,
        `verified` ,
        `approve_date` ,
        `apply_date`
        )
        VALUES (
                '$accountId', '$validationDiscountCashStr', '0', '$approveDate', '$date'
                )";
        
        $res = $this->db->query($sql);
    }

    if('' != trim($validationCashStr)){
        $dayAfter = 0;
        if(strtotime($todaySureTime) > strtotime($date)){
            $dayAfter = 2;
            if (5 == date('w')){
                $dayAfter = 4; // 下周二
            } elseif (6 == date('w')){
                $dayAfter = 3; // 下周二
            }
        } else {
            $dayAfter = 3;
            if(4 == date('w')){
                $dayAfter = 5; // 周二
            } elseif(5 == date('w')){
                $dayAfter = 5; // 周三
            } elseif (6 == date('w')){
                $dayAfter = 4; // 周三
            }
        }
        
        $approveDate = date("Y-m-d",time() + 24*60*60*$dayAfter);;
        
            $sql = "INSERT INTO  `ih_if_certification_cash` (
            `account_id` ,
            `cash` ,
            `verified` ,
            `approve_date` ,
            `apply_date`
            )
            VALUES (
                    '$accountId', '$validationCashStr', '0', '$approveDate', '$date'
                    )";
            
            $res = $this->db->query($sql);
    }
    
    echo json_encode(array("status" => 1));
}

public function getAccountIdByToken($token)
{
    $query = "SELECT * FROM `ih_if_account` WHERE user_token='" . $token . "'";
    $query = $this->db->query($query);
    $r = $query->result();
    
    $accountId = NULL;
    if(0 != count($r)){
        $accountId = $r[0]->ID;
    }
    
    return $accountId;
}

public function getAccountIdByUID($uid)
{
    $query = "SELECT * FROM `ih_if_account` WHERE user_id='" . $uid . "'";
    $query = $this->db->query($query);
    $r = $query->result();

    $accountId = NULL;
    if(0 != count($r)){
        $accountId = $r[0]->ID;
    }

    return $accountId;
}
        
public function gethisincome()
{
    if(!$this->checkSecretKey()){
        echo json_encode(array("status" => 0, "errorCode" => 1108)); //scode error
        return;
    }
    
    $uid = isset($_POST['user_id']) ? $_POST['user_id'] : '';
    $token = isset($_POST['user_token']) ? $_POST['user_token'] : '';
    
    if('' == $uid){
        echo json_encode(array("status" => 0, "errorCode" => 1102)); //$uid must ready
        return;
    }
    
    $accountId = $this->getAccountIdByUID($uid);
    $query = "SELECT * FROM  `ih_if_income` WHERE account_id =". $accountId . " ORDER BY DATE DESC LIMIT 30";
    $query = $this->db->query($query);
    $rows = $query->result();
    $incomesDic = array();
    
    $curdate = NULL;
    $dayIncome = 0.0;
    foreach($rows as $row){
        if($curdate == NULL){
            $curdate = $row->date;
        }
        
        $rowdate = $row->date;
        if ($rowdate != $curdate){
            $incomesDic = $incomesDic + array($curdate => $dayIncome);
            
            $curdate = $rowdate;
            $dayIncome = (float)$row->income;
        } else {
            $dayIncome += (float)$row->income;
        }
    }
    // last one
    $incomesDic = $incomesDic + array($curdate => $dayIncome);
    
    $query = "SELECT SUM( income ) as totalIncome FROM  `ih_if_income` WHERE account_id = " . $accountId;
    $query = $this->db->query($query);
    $r = $query->result();
    $totalIncome = 0.0;
    if(0 != count($r)){
        $totalIncome = (float)($r[0]->totalIncome);
    }
    echo json_encode(array("status" => 1, "pageIndex" => 1, "totalPageNum" => 1, "incomes" => $incomesDic, "totalIncome" => $totalIncome));
}

public function getconfirmdeposit(){
    if(!$this->checkSecretKey()){
        echo json_encode(array("status" => 0, "errorCode" => 1108)); //scode error
        return;
    }
    
    $uid = isset($_POST['user_id']) ? $_POST['user_id'] : '';
    $token = isset($_POST['user_token']) ? $_POST['user_token'] : '';
    $fid = isset($_POST['fund_id']) ? $_POST['fund_id'] : '';
    
    if('' == $uid){
        echo json_encode(array("status" => 0, "errorCode" => 1102)); //$uid must ready
        return;
    }
    if('' == $fid){
        echo json_encode(array("status" => 0, "errorCode" => 1103)); //fund id must ready
        return;
    }
    
    $accountId = $this->getAccountIdByUID($uid);
    $query = "SELECT * FROM  `ih_if_certification_cash` WHERE account_id =". $accountId . " and verified = 0 ORDER BY approve_date ASC";
    $query = $this->db->query($query);
    $rows = $query->result();
    $nonConfirmArr = array();
    
    foreach($rows as $row){
        $cash = $row->cash;
        $keyvalues = explode(':', $cash);
        $amount = $keyvalues[1];
        array_push($nonConfirmArr, array("apply_date" => $row->apply_date, "approve_date" => $row->approve_date, "amount" => $amount));
    }
    
    $query = "SELECT purchased FROM  `ih_if_account` WHERE ID = " . $accountId;
    $query = $this->db->query($query);
    $r = $query->result();
    $confirmed = "0.0";
    if(0 != count($r)){
        $purchased = $r[0]->purchased;
        $keyvalues = explode(',', $purchased);
        foreach($keyvalues as $fund){
            $fkeyvalue = explode(':', $fund);
            if($fkeyvalue[0] == $fid){
                $comfirmed = $fkeyvalue[1];
                break;
            }
        }
    }
    
    echo json_encode(array("status" => 1, "records" => $nonConfirmArr, "confirmed" => $comfirmed));
}
        
public function updateamount(){
    if(!$this->checkSecretKey()){
        echo json_encode(array("status" => 0, "errorCode" => 1108)); //scode error
        return;
    }
    
    $uid = isset($_POST['user_id']) ? $_POST['user_id'] : '';
    $token = isset($_POST['user_token']) ? $_POST['user_token'] : '';
    $fid = isset($_POST['fund_id']) ? $_POST['fund_id'] : '';
    $amount = isset($_POST['amount']) ? $_POST['amount'] : '';
    
    if('' == $uid){
        echo json_encode(array("status" => 0, "errorCode" => 1102)); //$uid must ready
        return;
    }
    if('' == $fid){
        echo json_encode(array("status" => 0, "errorCode" => 1103)); //fund id must ready
        return;
    }

    $query = "SELECT * FROM  `ih_if_account` WHERE user_id = '". $uid . "'";
    $query = $this->db->query($query);
    $r = $query->result();
    
    if(0 != count($r)){
        $purchased = $r[0]->purchased;
        $keyvalues = explode(',', $purchased);
        for($i = 0; $i < count($keyvalues); $i++){
            $fund = $keyvalues[$i];
            $fkeyvalue = explode(':', $fund);
            if($fkeyvalue[0] == $fid){
                $modifiedAmount = $fkeyvalue[0] . ":" . $amount;
                $keyvalues[$i] = $modifiedAmount;
                break;
            }
        }
        $modifiedAmountStr = join(",", $keyvalues);
        
        $purchased = $r[0]->all_money;
        $keyvalues = explode(',', $purchased);
        for($i = 0; $i < count($keyvalues); $i++){
            $fund = $keyvalues[$i];
            $fkeyvalue = explode(':', $fund);
            if($fkeyvalue[0] == $fid){
                $modifiedAmount = $fkeyvalue[0] . ":" . $amount;
                $keyvalues[$i] = $modifiedAmount;
                break;
            }
        }
        $modifiedAllMoneyStr = join(",", $keyvalues);
        
        $sql = "UPDATE `ih_if_account` SET `purchased` = '" . $modifiedAmountStr . "', `all_money` = '" . $modifiedAllMoneyStr . "' WHERE  `user_token` ='" . $token . "'";
        $this->db->query($sql);
        
        $accountId = $this->getAccountIdByToken($token);
        $query = "UPDATE `ih_if_certification_cash` SET `verified` = 1 WHERE  `account_id` ='" . $accountId . "' and verified = 0";
        $query = $this->db->query($query);
        $this->db->query($sql);
        
    } else{
        echo json_encode(array("status" => 0, "errorCode" => 1104)); //can't found
        return;
    }
    
    echo json_encode(array("status" => 1, "amount" => $amount));
}
        
public function getannualrate(){
    if(!$this->checkSecretKey()){
        echo json_encode(array("status" => 0, "errorCode" => 1108)); //scode error
        return;
    }
    
    $fid = isset($_POST['fund_id']) ? $_POST['fund_id'] : '';

    if('' == $fid){
        echo json_encode(array("status" => 0, "errorCode" => 1103)); //fund id must ready
        return;
    }
    
    date_default_timezone_set('Asia/Chongqing');
    $minDate = date("Y-m-d",time() - 30*24*60*60);
    $maxDate = date("Y-m-d",time() - 24*60*60);
    
    $query = "SELECT sevenday_revenue FROM `ih_funds` where name = (select concat(name,'(',code,')') as fundname from ih_fund where `ID`=" .$fid. ") and `date` >='" .$minDate. "' and `date` <='" .$maxDate. "' ORDER BY date ASC";
    
    $query = $this->db->query($query);
    
    $r = $query->result();
    $revenuesArr = array();
    foreach($r as $row){
        array_push($revenuesArr, $row->sevenday_revenue);
    }
    
    echo json_encode(array("status" => 1, "rates" => $revenuesArr, "mindate" => $minDate, "maxdate" => $maxDate));
}
		
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
        
        // account Model
        public function getFields() {
            if(!$this->checkSecretKey()){
                echo json_encode(array("status" => 0, "errorCode" => 1108)); //scode error
                return;
            }
            
            $this->ihaccount->getFields();
        }
        
        public function addRecord() {
            if(!$this->checkSecretKey()){
                echo json_encode(array("status" => 0, "errorCode" => 1108)); //scode error
                return;
            }
            
            if(1 == $this->ihsession->refreshSession()){
                $this->ihaccount->addRecord();
            } else {
                echo json_encode(array("status" => 0, "errorCode" => 1110)); //user session time out
                return;
            }
        }
        
        public function getAnalyseYears(){
            if(!$this->checkSecretKey()){
                echo json_encode(array("status" => 0, "errorCode" => 1108)); //scode error
                return;
            }
            
            if(1 == $this->ihsession->refreshSession()){
                $this->ihaccount->getAnalyseYears();
            } else {
                echo json_encode(array("status" => 0, "errorCode" => 1110)); //user session time out
                return;
            }
        }
        
        public function getAnalyse() {
            if(!$this->checkSecretKey()){
                echo json_encode(array("status" => 0, "errorCode" => 1108)); //scode error
                return;
            }
            
            if(1 == $this->ihsession->refreshSession()){
                $this->ihaccount->getAnalyse();
            } else {
                echo json_encode(array("status" => 0, "errorCode" => 1110)); //user session time out
                return;
            }
        }
        
        public function getGroupRecords(){
            if(!$this->checkSecretKey()){
                echo json_encode(array("status" => 0, "errorCode" => 1108)); //scode error
                return;
            }
            
            if(1 == $this->ihsession->refreshSession()){
                $this->ihaccount->getGroupRecords();
            } else {
                echo json_encode(array("status" => 0, "errorCode" => 1110)); //user session time out
                return;
            }
        }
        
    }