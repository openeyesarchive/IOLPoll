<?php
include_once 'Components/DataHelperMySQL.php';
include_once 'Components/Install.php';
include_once 'Components/IOLDataPusher.php';
include_once 'Config/config.php';

include 'Setup.php';

$idp=new IOLDataPusher($db,$api);
$idp->PushReadingsInQueue(true);
