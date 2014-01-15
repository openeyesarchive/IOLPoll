<?php

include_once 'DataHelperMySQL.php';
include_once 'DataHelperAccess.php';

class IOLReading {

	private $db;

	public function __construct($db)
	{
		$this->db=$db;
	}

	public function GetAll()
	{
		$pdo = $this->db->Get("select * from ioldata");
		return $pdo;
	}

	public static function toJSON($reading)
	{
		$record= unserialize ($reading['record']);
		$record['iol_machine_id']=$reading['id'];
		$record['iol_poll_id']=$reading['checksum'];
		return json_encode($record);
	}
}
