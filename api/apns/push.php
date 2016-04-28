<?php
	//$deviceToken = "b7a84749ee0ba16fae2f81eba538ca756b013abfe774b91894fac6367c7f7eeb";
	$deviceToken = "b92367d8 c6966edd 138dca90 3811d658 8a3fc4b8 999c33a4 692db5b2 5ee77e40";
//    $deviceToken = "575089de 921b923f 6c95b0cc 3ad1bf0f 9e105c98 677f137a 29e4ae05 2be6712e";
	//$deviceToken = '6c5f0a71be3afd7a74166d4d11b75edfa5d8a9ef';
	echo $deviceToken.'<br>';

//'08cc4452 b50d6556 cb8f2734 7171f420 9bbafdb3 63c98ce3 50c0a8af 93d23ca5';
  
	$body = array("aps" => array("alert" => 'message', "badge" => 9999, "sound"=>'default'));
  
	$ctx = stream_context_create();
	$pem = dirname(__FILE__) . '/' . 'dis_ifno.pem';
    echo $pem;
    
	$pass = 'ihakula';
	stream_context_set_option($ctx, "ssl", "local_cert", $pem);
	stream_context_set_option($ctx, 'ssl', 'passphrase', $pass);
    
	//gateway.sandbox.push.apple.com:2195 //gateway.push.apple.com:2195
	$fp = stream_socket_client("ssl://gateway.push.apple.com:2195", $err, $errstr, 60, STREAM_CLIENT_CONNECT, $ctx);
    
	if (!$fp) {
		print "Failed to connect $err $errstrn <br>";
		return;
	}
	print "Connection OK <br>";
	$payload = json_encode($body);
	$msg = chr(0) . pack("n",32) . pack("H*", str_replace(' ', '', $deviceToken)) . pack("n",strlen($payload)) . $payload;
	print "sending message :" . $payload . '<br>';
	fwrite($fp, $msg);
	fclose($fp);
    
    function send_feedback_request() {
        //connect to the APNS feedback servers
        //make sure you're using the right dev/production server & cert combo!
        $stream_context = stream_context_create();
        stream_context_set_option($stream_context, 'ssl', 'local_cert', '/path/to/my/cert.pem');
        $apns = stream_socket_client('ssl://feedback.push.apple.com:2196', $errcode, $errstr, 60, STREAM_CLIENT_CONNECT, $stream_context);
        if(!$apns) {
            echo "ERROR $errcode: $errstr\n";
            return;
        }
        
        $feedback_tokens = array();
        //and read the data on the connection:
        while(!feof($apns)) {
            $data = fread($apns, 38);
            if(strlen($data)) {
                $feedback_tokens[] = unpack("N1timestamp/n1length/H*devtoken", $data);
            }
        }
        fclose($apns);
        return $feedback_tokens;
    }
?>