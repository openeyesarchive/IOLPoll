<?php

include_once 'Components/DataHelperMySQL.php';
include_once 'Components/Install.php';
include_once 'Components/IOL.php';
include_once 'Config/config.php';

$config=Config::Load();

$con=$config['live']['db']['connectionString'];
$username=$config['live']['db']['username'];
$password=$config['live']['db']['password'];

if(!DataHelperMySQL::CheckPermissions($con,$username,$password)){
	die("\r\nDatabase not ready. Check config.\r\n");
}

$db=new DataHelperMySQL($con,$username,$password);

$iol = new IOL($db);

if(!$db->TableExists('iolmasters')){
	$install = new Install($db);
	$install->SetUpDatabase();
	$iol->log("Tables created");
	die();
}

$iol->log("Starting Poll");
$IOLMasters = $iol->ListIOLMasters();
if(!$IOLMasters){
	$iol->log("Nothing to do. Add IOL Masters via web control panel");
}

foreach($IOLMasters as $IOLMaster)
{
	$iol->log("Polling data from ".$IOLMaster['id']);
	if(file_exists($IOLMaster['filepath'])){
		$iol->PollData($IOLMaster);
	}
	else{
		$iol->log($IOLMaster['id']." Unreachable");
		$iol->LastChecked($IOLMaster['id']);
	}
}

$iol->log("Finished Poll");
