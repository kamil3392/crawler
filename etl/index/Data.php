<?php
include_once 'Sql.php';

class Data {
	private $sql;
	
	public function __construct(){
		$this->sql= Sql::getInstance();
		$this->sql->selectDB('etl');
	}
	
	public function getAll() {
		$this->sql->execute("SELECT * FROM jobs");
		return $this->sql->fetchAll();
	}

	public function clearTable() {
		$this->sql->execute("TRUNCATE TABLE jobs");
		return $this->sql->fetchAll();
	}
}