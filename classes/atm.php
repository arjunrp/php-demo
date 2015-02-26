<?php
/**
*  Class to handle banking operations related to an account or ATM
*  @Author Arjun RP
*
**/

Class Banking{
	/*
	* The account number of a customer
	* @var string
	* @access private;
	*/
    private $account;

	/*
	* The name of a customer
	* @var string
	* @access private;
	*/
	private $name;

	/*
	* The database object
	* @var Object
	* @access private;
	*/
	private $db;

    /*
	* Get Account holder name and account status.
    * @param  string $accountno The account no of card holder.
	* @param  Object $db Database object
    * @access  public
	* @retun  Array - name,status
    * @static
    */
	public static function getAccountInfo($db,$accountno){
		$accountno  = $db->real_escape_string($accountno);
		$accounttypes = $db->query("SELECT type FROM account_type WHERE account='".$accountno."'");
		$result = $db->query("SELECT name,blocked FROM account
							LEFT JOIN locked_account ON account.account_number = locked_account.account
							WHERE account.account_number='".$accountno."'");
		if($result===false || $accounttypes===false){
			return array('success'=>false,'message'=>'Query error');
		}
		if($result->num_rows!==1){
			return array('success'=>false,'message'=>'No such account');
		}
		if($accounttypes->num_rows===0){
			return array('success'=>false,'message'=>'Account details incomplete, No account type');
		}
		$row = $result->fetch_assoc();
		$types = array();
		while($r = $accounttypes->fetch_row()){
			array_push($types,$r[0]);
		}
		$result->close();
		if($row['blocked']==='Y'){
			$row['blocked']=true;
		}
		else{
			$row['blocked']=false;
		}
		return array('success'=>true,'name'=>$row['name'],'blocked'=>$row['blocked'],'accounts'=>$types);
	}

	/*
	* Check if the given pin is matching to the accountno
	* Password hashing uses MD5, only for demo
	* @param  Object $db Database object
    * @param  string $accountno  The account no of card holder.
	* @param  string $pin  The pin no to check.
    * @access  public
	* @return  bool
    * @static
    */
	public static function checkPin($db,$accountno,$pin){
		if(preg_match('/^\d{4}$/',$pin)==false){
			return array('success'=>false,'message'=>'Pin should be a 4 digit number');
		}
		if($accountno===''){
			return array('success'=>false,'message'=>'Account No is invalid');
		}
		$accountno = $db->real_escape_string($accountno);
		$result = $db->query("SELECT pin FROM account WHERE account_number='".$accountno."'");
		if($result===false){
			return array('success'=>false,'message'=>'Query error');
		}
		if($result->num_rows!==1){
			return array('success'=>false,'message'=>'No such account');
		}
		$row = $result->fetch_assoc();
		$result->close();
		if($row['pin']===md5($pin)){
			return array('success'=>true,'message'=>'');
		}
		else{
			return array('success'=>false,'message'=>'Your PIN is wrong');
		}
	}


	/*
	* Constructer - Initialize account no,name and db object
    * @param  string $accountno  Account no of card holder.
	* @param  string $name  Name of card holder.
	* @param  object $pin  Database connection object
    * @access  public
    */
	public function __construct($account,$name,$db){
		$this->account=$account;
		$this->name=$name;
		$this->db=$db;
	}

	/*
	* Return the balence based on specified account type
    * @param  string $type  Account type - 'S' SAVINGS or F FIXED or C CURRENT
    * @return float
	* @access  public
    */
	public function getBalance($type){
		if($type!=='S' && $type!=='F' && $type!=='C'){
			return array('success'=>false,'message'=>'Invalid account type');
		}
		$balance = $this->db->query("SELECT amount FROM account_type
								WHERE type = '".$type."' AND account = '".$this->account."'");
		if($balance===false){
			return array('success'=>false,'message'=>'Query error');
		}
		if($balance->num_rows!=1){
			return array('success'=>false,'message'=>'Invalid account or account type');
		}
		return array('success'=>true,'message'=>(int)$balance->fetch_row()[0]);


	}

	/*
	* Withdraw specified amout from account
	* @param  float  $amount  Amount to be withdrawed
    * @param  string $type  Account type - S SAVINGS or F FIXED or C CURRENT
	* @return  bool
    * @access  public
    */
	public function withdraw($amount,$type){
		if($type!=='S' && $type!=='F' && $type!=='C'){
			return array('success'=>false,'message'=>'Invalid account type');
		}

		$balance = $this->getBalance($type)['message'];
		if($amount<=0){
			return array('success'=>false,'message'=>'Invalid amount '.$amount);
		}
		if(($balance-$amount) < 0){
			return array('success'=>false,'message'=>'Your account does not have sufficient balance');
		}
		if($this->db->query("START TRANSACTION")===false){
			return array('success'=>false,'message'=>'Failed to start transaction');
		}
		$result = $this->db->query("UPDATE account_type SET amount=amount-".$amount."
									WHERE account='".$this->account."' AND type='".$type."'");
		$transaction = $this->addAccountActivity("Rs ".$amount." withdrawed");

		if($result===false){
			$this->db->query("ROLLBACK");
			return array('success'=>false,'message'=>'Cannot withdraw amount');

		}
		if($transaction['success']===false){
			$this->db->query("ROLLBACK");
			return $transaction;
		}
		if($this->db->query("COMMIT")===false){
			return array('success'=>false,'message'=>'Transaction Failed!');
		}
		return array('success'=>true,
					 'message'=>'Rs '.$amount.' withdrawed, Your current balance is '.$this->getBalance($type)['message']
					);
	}

    /*
	* Change pin no of an account
	* @param  string  $newPin  New pin for the account
	* @return  bool
    * @access  public
    */
	public function changePin($newPin){
		if(preg_match('/^\d{4}$/',$newPin)==false){
			return array('success'=>false,'message'=>'Pin should be a 4 digit number');
		}
		if($this->db->query("START TRANSACTION")===false){
			return array('success'=>false,'message'=>'Failed to start transaction');
		}
		$result = $this->db->query("UPDATE account SET pin = md5('".$newPin."')
									WHERE account_number='".$this->account."'");
		$transaction = $this->addAccountActivity("Pin Changed");
		if($result===false){
			$this->db->query("ROLLBACK");
			return array('success'=>false,'message'=>'Query error');
		}
		if($transaction['success']===false){
			$this->db->query("ROLLBACK");
			return $transaction;
		}
		if($this->db->query("COMMIT")===false){
			return array('success'=>false,'message'=>'Transaction Failed!');
		}
		return array('success'=>true,'message'=>'Pin changed !!');
	}

	/*
	* Add account activity
    * @param  string $desc  Activity description
	* @return  bool
    * @access  public
    */
	public function addAccountActivity($details){
		$atm = $_SERVER['REMOTE_ADDR'];
		$details = $this->db->real_escape_string($details);

		$result = $this->db->query("INSERT INTO transactions VALUES('',
									'".$this->account."',
									now(),
									'".$atm."',
									'".$details."'
									)");

		if($result===false){
			return array('success'=>false,'message'=>'Failed to log transaction');
		}
		return array('success'=>true,'message'=>'');
	}


	/*
	* Get the details about the account
	* @param  int  $count Number of last transactions needed
	* @return  Array of strings
    * @access  public
    */
	public function accountStatement($count){
		$count=(int)$count;
		$result=$this->db->query("SELECT transaction_id AS id,DATE_FORMAT(time,'%e %b %y, %r') AS moment,atm,description
									FROM transactions WHERE account='".$this->account."' ORDER BY time DESC LIMIT ".$count);
		echo $this->db->error;
		if($result===false){
			return array('success'=>false,'message'=>'Failed to get transactions');
		}
		return array('success'=>true,'message'=>$result->fetch_all(MYSQLI_ASSOC));
	}

}
