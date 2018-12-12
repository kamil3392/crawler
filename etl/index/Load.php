<?php
include_once 'Sql.php';

class Load {
  private $sql;
  
  public function __construct(){
	$this->sql = Sql::getInstance();
    $this->sql->selectDB('etl');
  }   	
	
  public function load() {	

  $jsonContents = file_get_contents('transform.json');
  $jsonDecodeData = json_decode($jsonContents, true);


  foreach($jsonDecodeData as $item) {
	$querySelect = "SELECT id FROM jobs WHERE title like '".$item['title']."' and company_name like '".$item['company_name']."'";
	$querySelectResult = $this->sql->fetch($querySelect);

	if($querySelectResult == null) {
		$queryInsert = "INSERT INTO jobs (title, location, price, company_name, kind, number, position, position_level) 
						VALUES ('".$item['title']."' , '".$item['location']."', '".$item['price']."', '".$item['company_name']."',
						'".$item['kind']."', '".$item['number']."', '".$item['position']."', '".$item['position_level']."')";
		$this->sql->execute($queryInsert);
	} else {
		$queryUpdate = "UPDATE jobs SET location = '".$item['location']."', price = '".$item['price']."', 
										kind = '".$item['kind']."', number='".$item['number']."', 
										position='".$item['position']."',position_level= '".$item['position_level']."' 
										WHERE id = '".$querySelectResult["id"]."'";
		$this->sql->execute($queryUpdate);								
	}
  }
  }
}