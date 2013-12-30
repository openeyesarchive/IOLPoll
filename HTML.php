<?php

include_once 'Components/DataHelperMySQL.php';
include_once 'Components/IOL.php';
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
			$lastchecked=new DateTime($master['lastchecked']);
			if(!$master['lastavailable']){
				echo '<img src="/img/unavailable.png"> ';
			}
			else if($lastavailable <= $lastchecked->sub(new DateInterval('PT720M'))) {
				echo '<img src="/img/offline.png"> ';
			}
			else {
				echo '<img src="/img/available.png">';
			}
			echo "<a href='/admin/viewiolmaster.php?id=".$master['id']."'>".$master['id']."</a><BR>";

		}
	}

	public static function PostNewIOLMaster($post)
	{
		$db=self::DB();
		$iol=new IOL($db);
		$iol->Add($post['id'],$post['path'],$post['notes']);
		header("location: /admin/viewiolmasters.php");
	}

	public static function ViewIOLMaster($id)
	{
		$db=self::DB();
		$iol = new IOL($db);
		$iolmaster=$iol->Get($id);
		echo "ID:".$iolmaster["id"]."<BR>";
		echo "Path:".$iolmaster["filepath"]."<BR>";
		echo "Lastchecked:".$iolmaster["lastchecked"]."<BR>";
		echo "Lastavailable:".$iolmaster["lastavailable"]."<BR><BR>";
		echo "Notes:".$iolmaster["notes"]."<BR><BR>";
		echo "<a href='/admin/editiolmaster.php?id=".$iolmaster['id']."'>Edit</a> ";
		echo "<a href='/admin/deleteiolmaster.php?id=".$iolmaster['id']."'>Delete</a><BR>";

	}

	public static function GetIOLMaster($id)
	{
		$db=self::DB();
		$iol = new IOL($db);
		return $iol->Get($id);
	}

	public static function DeleteIOLMaster($id)
	{
		$db=self::DB();
		$iol=new IOL($db);
		$iol->Delete($id);
		header("location: /admin/viewiolmasters.php");
	}

	public static function UpdateIOLMaster($post)
	{
		$db=self::DB();
		$iol=new IOL($db);
		$iol->Update($post['id'],$post['path'],$post['notes']);
		header("location: /admin/viewiolmaster.php?id=".$post['id']);
	}

	public static function Stats()
	{
		$db=self::DB();
		$iol=new IOL($db);
		$count=$iol->Count();
		$reachable=$iol->Reachable();
		$lastpolled=$iol->LastPolled();
		$unreachable=$iol->Unreachable();

		echo "$count IOL Masters in Database<br>";
		echo "Last polled $lastpolled<br>";
		echo "$reachable Online<br>";
		echo "$unreachable Unreachable<br>";
		$neverpolled = $count-$reachable-$unreachable;
		echo "$neverpolled Never responded<br>";
	}




}
?>

