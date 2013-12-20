<?php

class DataHelperMySQL {

private $db;

public function __construct($database,$username,$password)
{
	$db = new PDO("mysql:host=localhost;dbname=$database", $username, $password);
	$this->db=$db;
}

public function Get($sql)
{
	return $this->db->query($sql)->fetchAll();
}
}

