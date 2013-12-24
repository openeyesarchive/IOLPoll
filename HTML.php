<?php

include_once 'Components/DataHelperMySQL.php';
include_once 'Config/config.php';

class HTML {

	public static function DB()
	{
		$config=Config::Load();
		$con=$config['live']['db']['connectionString'];
		$username=$config['live']['db']['username'];
		$password=$config['live']['db']['password'];
		return new DataHelperMySQL($con,$username,$password);
	}

	public static function ViewIOLMasters()
	{
		$db=self::DB();
		$masters = $db->Get("select * from iolmasters");

		foreach($masters as $master){
			$isup = new DateTime("now");
			$isup->sub(new DateInterval('PT48H'));
			$lastavailable=new DateTime($master['lastavailable']);
			if(!$master['lastavailable']){
				echo '<img src="/img/unavailable.png"> ';
			}
			else if(($lastavailable > $isup)) {
				echo '<img src="/img/available.png"> ';
			}
			else {
				echo '<img src="/img/offline.png">';
			}
			echo "<a href='/admin/viewiolmaster.php?id=".$master['id']."'>".$master['id']."</a><BR>";

		}
	}
}
