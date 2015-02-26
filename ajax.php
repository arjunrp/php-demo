<?php
function session(){
	session_start();
	if(empty($_SESSION['account'])){
		return false;
	}
	session_regenerate_id(true);
	return true;
}
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

		$banking = new Banking($_SESSION['account'],$_SESSION['name'],$dbo);
		$banking->addAccountActivity("Accessed banking portal using ATM card");
		$response['success'] = true;
		$response['message'] = 'Welcome '.ucwords($details['name']).', Please wait';
		break;
	}
	case 2:{
		/* Handler for managing account statement queries, request with the count of transactions required */
		if(session()===false){
			$response['message']="Your session is invalid !!";
			break;
		}

		if(isset($_POST['count'])){
			$count=(int) $_POST['count'];
			if($count<=0 || $count>100){
				$response['message']="Count value should be between 1-100";
				break;
			}
		}
		else{
			$count = 5;
		}
		$account = new Banking($_SESSION['account'],$_SESSION['name'],$dbo);
		$response = $account->accountStatement($count);
		break;
	}
	case 3:{
		/* Handler for managing pin change, request with the old and new pin */
		if(session()===false){
			$response['message']="Your session is invalid !!";
			break;
		}

		if(!isset($_POST['oldPin'])||!isset($_POST['newPin'])){
			$response['message']="Your request is invalid !!";
			break;
		}
		if($_POST['newPin']===$_POST['oldPin']){
			$response['message'] = 'Old and new pin are same one';
			break;
		}
		$result = Banking::checkPin($dbo,$_SESSION['account'],$_POST['oldPin']);
		if($result['success']===false){
			$response = $result;
			break;
		}



		$account = new Banking($_SESSION['account'],$_SESSION['name'],$dbo);
		$response = $account->changePin($_POST['newPin']);
		break;
	}
	case 4:{
		/* Handler for getting account balance, request with account type*/
		if(session()===false){
			$response['message']="Your session is invalid !!";
			break;
		}
		if(!isset($_POST['type'])){
			$response['message']="Your request is invalid !!";
			break;
		}
		$account = new Banking($_SESSION['account'],$_SESSION['name'],$dbo);
		$response = $account->getBalance($_POST['type']);
		break;
	}
	case 5:{
		/* Handler for withdrawing amount, request with amount and account type */
		if(session()===false){
			$response['message']="Your session is invalid !!";
			break;
		}
		if(!isset($_POST['account'])|| !isset($_POST['amount'])){
			$response['message']="Your request is invalid !!";
			break;
		}
		$account = new Banking($_SESSION['account'],$_SESSION['name'],$dbo);
		$response = $account->withdraw($_POST['amount'],$_POST['account']);
		break;
	}
	case 6:{
		/* Handler for logging out */
		if(session()===false){
			$response['message']="Your session is invalid !!";
			break;
		}
		session_unset();
		session_destroy();
		$response['success']=true;
		break;
	}
	default:{
		$response['message']='Invalid ID';
	}
}
echo json_encode($response);
