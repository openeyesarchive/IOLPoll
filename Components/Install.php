<?php

include_once 'DataHelperMySQL.php';

class Install {

	private $db;

	public function __construct($db)
	{
		$this->db=$db;
	}

	public function SetUpDatabase()
	{
		$sql = "CREATE TABLE `iolmasters` (
				`id` VARCHAR(50) NOT NULL,
				`filepath` VARCHAR(255) NOT NULL,
				`notes` TEXT NOT NULL,
				`lastchecked` DATE NULL,
				`lastavailable` DATE NULL
				)
				COLLATE='utf16_bin'
				ENGINE=InnoDB;";

		$this->db->ExecNoneQuery($sql);

		$sql = "CREATE TABLE `ioldata` (
				`id` VARCHAR(50) NOT NULL COLLATE 'utf16_bin',
				`checksum` VARCHAR(50) NOT NULL COLLATE 'utf16_bin',
				`record` BLOB NOT NULL,
				`dateadded` DATE NULL DEFAULT NULL,
				PRIMARY KEY (`checksum`)
				)
				COLLATE='utf16_bin'
				ENGINE=InnoDB;";

		$this->db->ExecNoneQuery($sql);
	}

	public function RemoveTables()
	{
		$sql = 'DROP TABLE IF EXISTS iolmasters';
		$this->db->ExecNoneQuery($sql);
		$sql = 'DROP TABLE IF EXISTS ioldata';
		$this->db->ExecNoneQuery($sql);
	}
}


