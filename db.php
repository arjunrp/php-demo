<?php
class Database{
	private $dbname="atm";
	private $username="root";
	private $password="root";
	private $host="localhost";
	private $db=false;

	public function connect(){
		$this->db = new mysqli($this->host,$this->username,$this->password,$this->dbname);
		if($this->db->connect_errno){
			return false;
		}
		$this->db->set_charset('utf-8');
		return $db;
	}
}
