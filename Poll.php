<?php

include '/Components/IOL.php';
$IOLMasters = IOL::ListIOLMasters();

foreach($IOLMasters as $IOLMaster)
{
	if(file_exists($IOLMaster['filepath'])){
		echo "Polling data from ".$IOLMaster['id']."\r\n";
		IOL::PollData($IOLMaster);
	}
	else{
		IOL::Unavailable($IOLMaster);
	}
}
