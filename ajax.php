<?php
include_once'./classes/db.php';
include_once'./classes/atm.php';
$db = new Database('atm','root','root','localhost');
$dbo = $db->connect();

$response = array();
$response['success'] = false;
$response['message'] = '';

if(!isset($_POST['id'])){
	$response['message'] = 'Invalid Request';
	json_encode($response);
	exit;
}
$id = $_POST['id'];
switch($id){
	case 1:{
		/*
			Login handler
			Accepts account no and pin, validate it, set session varaibles
		*/
		if(!isset($_POST['account']) || !isset($_POST['account'])){
			$response['message'] = 'Invalid Request - login';
			break;
		}
		$account = $_POST['account'];
		$result = Banking::checkPin($dbo,$account,$_POST['pin']);
		if($result['success']===false){
			$response = $result;
			break;
		}
		$details = Banking::getAccountInfo($dbo,$account);
		if($details['success']===false){
			$response = $details;
			break;
		}
		if($details['blocked']===true){
			$response['message'] = 'Your account is blocked !!';
			break;
		}
		session_start();
		$_SESSION['account'] = $account;
		$_SESSION['name'] = $details['name'];
		$_SESSION['types'] = $details['accounts'];
		$response['success'] = true;
		$response['message'] = 'Welcome '.ucwords($details['name']).', Please wait';
		break;
	}
	default:{
		$response['message']='Invalid ID';
	}
}
echo json_encode($response);
