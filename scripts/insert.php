<?php
header('Access-Control-Allow-Origin: *');

header('Access-Control-Allow-Methods: GET, POST');

header("Access-Control-Allow-Headers: X-Requested-With");

header('Content-type: application/json');

$product_id = $_POST["product_id"];
$serial_no = $_POST["serial_no"];
$full_name = $_POST["full_name"];
$email = $_POST["email"];


/********* for kaydence co server********/
//$db_name = 'kaydenc1_ms';
//$db_user = 'kaydenc1_ms';
//$db_pass = 'P@55w0rd';
//$db_host = 'localhost';

/********* for brandage server********/

$db_name = 'activat2_register';
$db_user = 'activat2_kay';
$db_pass = 'P@55w0rd';
$db_host = 'localhost';

/********* for local server********/

//$db_name = 'microsoft';
//$db_user = 'root';
//$db_pass = '';
//$db_host = 'localhost';



//creatre connection object

$to = $email;
$subject = "Your Registration | Activate Windows";
	
$mysqli = new mysqli($db_host, $db_user, $db_pass, $db_name);

function check_exists($field, $value){
	$db_name = 'microsoft';
	$db_user = 'root';
	$db_pass = '';
	$db_host = 'localhost';
	
	$mysqli = new mysqli($db_host, $db_user, $db_pass, $db_name);
	$query = "SELECT * FROM activations WHERE $field = '$value' ";
	$result = $mysqli->query($query);
	$count = mysqli_num_rows($result);
	
	if($count > 0){
		return false;
	}else{
		//soes not exist
		return true;
	}
}

if($mysqli->connect_error){
    printf('Connection Failed', $mysqli->connect_error);
	$response_array['status'] = 'error';
	$response_array['error_reason'] = 'No database connection';
	echo json_encode($response_array);
	return false;
}else{
	if($product_id && $serial_no && $full_name && $email){
		//check email and check serial number with a function
		if(check_exists('email', $email)){
			if(check_exists('serial_no', $serial_no)){
				if(check_exists('product_id', $product_id)){
					$query = "INSERT INTO `activations` (product_id, serial_no, full_name, email) VALUES ('$product_id', '$serial_no', '$full_name', '$email')";
					$doInsert = $mysqli->query($query) or die($mysqli->error.__LILNE__);

					if($doInsert){

						$userId = $mysqli->insert_id;
						$paddedId = sprintf("%05d", $userId);


						//set content-type when sending HTML email
						$headers = "MIME-Version: 1.0" . "\r\n";
						$headers .= "Content-type:text/html;charset=UTF-8" . "\r\n";

						// More headers
						$headers .= 'From: <info@activateyourworld.com.ng/>' . "\r\n";
						$headers .= 'Bcc: info@kaydenceco.com.ng' . "\r\n";

						$mail_body = '<html><head></head><body><table width="550" align="center"><tbody><tr><td class="mail-header"><img src="http://brandstolife.com/windowsactivation/img/Windows10_logo.png" alt="microsoft-logo" height="34"></td></tr><tr><td class="mail-body"><div class="main-content"><h3>Microsoft Windows Activation</h3><p>Hello '.$full_name.'</p><p>Thank You for activating your windows</p><p>Your registration ID is <strong>'.$paddedId.'</strong> </p></div></td></tr><tr><td class="mail-footer"></td></tr></tbody></table></body></html>';

						$done = mail($to, $subject, $mail_body, $headers);

						if($done){
							$response_array['status']= 'success';
							$response_array['userId']= $paddedId;				
							echo json_encode($response_array);
						}

					}else{
						$response_array['status'] = 'error';
						echo json_encode($response_array);
						return;
					}
				}else{
					$response_array['status'] = 'error';
					$response_array['error_reason'] = 'product Id already registered';
					echo json_encode($response_array);
				}
			}else{
				$response_array['status'] = 'error';
				$response_array['error_reason'] = 'Serial Number already registered';
				echo json_encode($response_array);
			}
		}else{
			$response_array['status'] = 'error';
			$response_array['error_reason'] = 'Email already registered';
			die(json_encode($response_array));
		}
	}else{
		$response_array['status'] = 'error';
		$response_array['error_reason'] = 'all fields are required';
		echo json_encode($response_array);
	}
}

?>