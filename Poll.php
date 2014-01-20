<?php

include_once 'Components/DataHelperMySQL.php';
include_once 'Components/Install.php';
include_once 'Components/IOL.php';
include_once 'Config/config.php';

include 'Setup.php';

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
