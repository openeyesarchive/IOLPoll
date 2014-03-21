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
				`lastchecked` DATETIME NULL,
				`lastavailable` DATETIME NULL
				)
				COLLATE='utf8_bin'
				ENGINE=InnoDB;";

		$this->db->ExecNoneQuery($sql);

        $sql = "CREATE TABLE `ioluptime` (
				`id` VARCHAR(50) NOT NULL,
				`checked` DATETIME NULL,
				`isavailable` TINYINT(1) NULL
				)
				COLLATE='utf8_bin'
				ENGINE=InnoDB;";

        $this->db->ExecNoneQuery($sql);

		$sql = "CREATE TABLE `ioldata` (
				`id` VARCHAR(50) NOT NULL COLLATE 'utf16_bin',
				`checksum` VARCHAR(50) NOT NULL COLLATE 'utf16_bin',
				`record` BLOB NOT NULL,
				`dateadded` DATETIME NULL DEFAULT NULL,
				PRIMARY KEY (`checksum`)
				)
				COLLATE='utf8_bin'
				ENGINE=InnoDB;";

		$this->db->ExecNoneQuery($sql);

		$sql = "CREATE TABLE `iolpolllog` (
				`id` INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,
				`message` TEXT NOT NULL,
				`datecreated` DATETIME NULL DEFAULT NULL,
				PRIMARY KEY (`id`)
				)
				COLLATE='utf8_bin'
				ENGINE=InnoDB;";

		$this->db->ExecNoneQuery($sql);

		$sql = "CREATE TABLE `ioldatapushlog` (
				`checksum` VARCHAR(50) NOT NULL COLLATE 'utf16_bin',
				`datecreated` DATETIME NULL DEFAULT NULL,
				PRIMARY KEY (`checksum`)
				)
				COLLATE='utf8_bin'
				ENGINE=InnoDB;";

		$this->db->ExecNoneQuery($sql);
	}

	public function RemoveTables()
	{
		$sql = 'DROP TABLE IF EXISTS iolmasters';
		$this->db->ExecNoneQuery($sql);
		$sql = 'DROP TABLE IF EXISTS ioldata';
		$this->db->ExecNoneQuery($sql);
		$sql = 'DROP TABLE IF EXISTS iolpolllog';
		$this->db->ExecNoneQuery($sql);
		$sql = 'DROP TABLE IF EXISTS ioldatapushlog';
		$this->db->ExecNoneQuery($sql);
        $sql = 'DROP TABLE IF EXISTS ioluptime';
        $this->db->ExecNoneQuery($sql);
	}
}


