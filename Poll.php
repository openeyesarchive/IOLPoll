<?php
include_once 'Components/DataHelperMySQL.php';
include_once 'Components/Install.php';
include_once 'Components/IOL.php';
include_once 'Config/config.php';

include 'Setup.php';

$iol->log("Starting Poll");

foreach($IOLMasters as $IOLMaster)
{
    if(isset($IOLMaster['lastavailable'])){
    $readings = $iol->importRecent($IOLMaster);
    }
    else {
    $readings = $iol->importAll($IOLMaster);
    }

    if(isset($readings)){
        $iol->saveIOLReadings($IOLMaster,$readings);
    }

    $iol->logAvailableStatus($IOLMaster['id'],isset($readings));
}

$iol->log("Finished Poll");
