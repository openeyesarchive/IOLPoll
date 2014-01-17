<?php
include_once 'Components/DataHelperMySQL.php';
include_once 'Components/Install.php';
include_once 'Components/IOLDataPusher.php';
include_once 'Config/config.php';

$config=Config::Load();

$con=$config['live']['db']['connectionString'];
$username=$config['live']['db']['username'];
$password=$config['live']['db']['password'];

$api = $config['live']['api'];

if(!DataHelperMySQL::CheckPermissions($con,$username,$password)){
	die("\r\nDatabase not ready. Check config.\r\n");
}

$db=new DataHelperMySQL($con,$username,$password);

$idp=new IOLDataPusher($db,$api);

$idp->PushReadingsInQueue(true);
