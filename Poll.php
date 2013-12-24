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

if(!$db->TableExists('iolmasters')){
	$install = new Install($db);
	$install->SetUpDatabase();
	die("\r\nTables created\r\n");
}

$iol = new IOL($db);
$IOLMasters = $iol->ListIOLMasters();
if(!$IOLMasters){
	echo "\r\nNothing to do. Add IOL Masters via web control panel.\r\n";
}

foreach($IOLMasters as $IOLMaster)
{
	if(file_exists($IOLMaster['filepath'])){
		echo "\r\nPolling data from ".$IOLMaster['id']."\r\n";
		IOL::PollData($IOLMaster);
	}
	else{
		IOL::LastChecked($IOLMaster);
	}
}
