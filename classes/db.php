<?php
/**
*
* Class to handle database connection
* @author    Arjun RP
*/
class Database{
	private $dbname;
	private $username;
	private $password;
	private $host;
	private $db;

	public function __construct($dbname,$username,$password,$host){
		$this->db = false;
		$this->dbname = $dbname;
		$this->username = $username;
		$this->password = $password;
		$this->host = $host;
	}
	public function connect(){
		$this->db = new mysqli($this->host,$this->username,$this->password,$this->dbname);
		if($this->db->connect_errno){
			$this->db=false;
		}
		else{
			$this->db->set_charset('utf-8');
			$this->db->query("SET time_zone = '+5:30'");
		}
		return $this->db;
	}
	public function __destruct(){
		if($this->db!==false){
			$this->db->close();
		}
	}
}
