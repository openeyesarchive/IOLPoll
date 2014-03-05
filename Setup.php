<?php
$config=Config::Load();

$con=$config['live']['db']['connectionString'];
$username=$config['live']['db']['username'];
$password=$config['live']['db']['password'];

$api = $config['live']['api'];

if(!DataHelperMySQL::checkPermissions($con,$username,$password)){
	die("\r\nDatabase not ready. Check config.\r\n");
}

$db=new DataHelperMySQL($con,$username,$password);

$iol = new IOL($db);

if(!$db->TableExists('iolmasters')){
	$install = new Install($db);
	$install->SetUpDatabase();
	$iol->log("Tables created. Relaunch.");
	die();
}

$IOLMasters = $iol->ListIOLMasters();
if(!$IOLMasters){
	$iol->log("Nothing to do. Add IOL Masters via web control panel");
}
