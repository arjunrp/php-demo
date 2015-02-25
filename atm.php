<?php
	Class Banking{
		private $account;
		private $name;
		private $db;

		public static getAccountInfo(){}
		public static checkPin();

		public __construct($account,$name,$db){
			$this->account=$account;
			$this->name=$name;
			$this->db=$name;
		}
		public getBalance(){}
		public withdraw($withdrawAmount){}
		public changePin($newPin,$oldPin){}
		public accountStatement($count){}
		public addAccountActivity(){}
	}
