<?php
/**
 * Created by JetBrains PhpStorm.
 * User: Administrator
 * Date: 12/20/13
 * Time: 8:50 AM
 * To change this template use File | Settings | File Templates.
 */

include_once 'DataHelperMySQL.php';

class Install {

	public static function SetUpDatabase($database)
	{
		$dh = new DataHelperMySql($database,'root','');

		$sql = "CREATE TABLE `iolmasters` (
				`id` VARCHAR(50) NOT NULL,
				`filepath` VARCHAR(255) NOT NULL,
				`lastchecked` DATE NULL,
				`lastavailable` DATE NULL
				)
				COLLATE='utf16_bin'
				ENGINE=InnoDB;";

		$dh->ExecNoneQuery($sql);
	}

	public static function RemoveTables($database)
	{
		$dh = new DataHelperMySql($database,'root','');
		$sql = 'DROP TABLE iolmasters';
		$dh->ExecNoneQuery($sql);

	}


}
