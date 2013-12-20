<?php

include_once 'Components/DataHelperMySQL.php';
include_once  'Install.php';
include_once 'Components/IOL.php';
include_once 'Config/config.php';

$config=Config::Load();
$db=new DataHelperMySQL($config['live']['db']['connectionString'],$config['live']['db']['username'],$config['live']['db']['password']);

$iol = new IOL($db);
$IOLMasters = $iol->ListIOLMasters();

if(!$IOLMasters){
	echo "\r\nDatabase missing\r\n";
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
