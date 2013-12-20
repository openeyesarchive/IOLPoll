<?php


include 'DataHelperMySQL.php';
include 'DataHelperAccess.php';

class IOL {

	public static function PollData($IOLMaster)
	{
		$dh = new DataHelperAccess($IOLMaster['filepath'],'', 'Hic2003Jack');
		$data = $dh->GetSQL("select * from patient");
		var_dump($data);
	}

	public static function ListIOLMasters()
	{
		$dh = new DataHelperMySQL('iolmasters','root','');
		$pdo = $dh->Get("select * from iolmasters");
		return $pdo;
	}

	public function Unavailable($IOLMaster)
	{

	}



}