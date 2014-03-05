<?php

include_once 'Components/DataHelperMySQL.php';
include_once 'Components/IOL.php';
include_once 'Config/config.php';


class HTML {

	public static function db()
	{
		$config=Config::load();
		$con=$config['live']['db']['connectionString'];
		$username=$config['live']['db']['username'];
		$password=$config['live']['db']['password'];
		return new DataHelperMySQL($con,$username,$password);
	}

	public static function viewIOLMasters()
	{
		$db=self::db();
		$masters = $db->get("select * from iolmasters");

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

	public static function postNewIOLMaster($post)
	{
		$db=self::db();
		$iol=new IOL($db);
		$iol->add($post['id'],$post['path'],$post['notes']);
		header("location: /admin/viewiolmasters.php");
	}

	public static function viewIOLMaster($id)
	{
		$db=self::db();
		$iol = new IOL($db);
		$iolmaster=$iol->get($id);
		echo "ID:".$iolmaster["id"]."<BR>";
		echo "Path:".$iolmaster["filepath"]."<BR>";
		echo "Lastchecked:".$iolmaster["lastchecked"]."<BR>";
		echo "Lastavailable:".$iolmaster["lastavailable"]."<BR><BR>";
		echo "Notes:".$iolmaster["notes"]."<BR><BR>";
		echo "<a href='/admin/editiolmaster.php?id=".$iolmaster['id']."'>Edit</a> ";
		echo "<a href='/admin/deleteiolmaster.php?id=".$iolmaster['id']."'>Delete</a><BR>";

	}

	public static function getIOLMaster($id)
	{
		$db=self::db();
		$iol = new IOL($db);
		return $iol->get($id);
	}

	public static function deleteIOLMaster($id)
	{
		$db=self::db();
		$iol=new IOL($db);
		$iol->delete($id);
		header("location: /admin/viewiolmasters.php");
	}

	public static function updateIOLMaster($post)
	{
		$db=self::db();
		$iol=new IOL($db);
		$iol->update($post['id'],$post['path'],$post['notes']);
		header("location: /admin/viewiolmaster.php?id=".$post['id']);
	}

	public static function stats()
	{
		$db=self::db();
		$iol=new IOL($db);
		$count=$iol->count();
		$reachable=$iol->reachable();
		$lastpolled=$iol->lastPolled();
		$unreachable=$iol->unreachable();

		echo "$count IOL Masters in Database<br>";
		echo "Last polled $lastpolled<br>";
		echo "$reachable Online<br>";
		echo "$unreachable unreachable<br>";
		$neverpolled = $count-$reachable-$unreachable;
		echo "$neverpolled Never responded<br>";
	}

	public static function viewPollLog()
	{
		$db=self::db();
		$iol=new IOL($db);
		$log = $iol->getLog();

		foreach($log as $logitem){
			echo $logitem['datecreated'].' '.$logitem['message'].'<br>';
		}
	}



}
?>

