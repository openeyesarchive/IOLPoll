<?php
/**
 * Created by JetBrains PhpStorm.
 * User: Administrator
 * Date: 12/20/13
 * Time: 8:50 AM
 * To change this template use File | Settings | File Templates.
 */

include_once '/Components/DataHelperMySQL.php';
include_once '/Config/config.php';

class Install {

	private $dh;

	public function __construct($dh)
	{
		$this->dh=$dh;
	}

	public function SetUpDatabase()
	{
		$sql = "CREATE TABLE `iolmasters` (
				`id` VARCHAR(50) NOT NULL,
				`filepath` VARCHAR(255) NOT NULL,
				`lastchecked` DATE NULL,
				`lastavailable` DATE NULL
				)
				COLLATE='utf16_bin'
				ENGINE=InnoDB;";

		$this->dh->ExecNoneQuery($sql);
	}

	public function RemoveTables()
	{
		$sql = 'DROP TABLE iolmasters';
		$this->dh->ExecNoneQuery($sql);
	}


}
