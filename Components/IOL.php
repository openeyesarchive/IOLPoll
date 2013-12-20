<?php


include_once 'DataHelperMySQL.php';
include_once 'DataHelperAccess.php';

class IOL {

	private $dh;

	public function __construct($dh)
	{
		$this->dh=$dh;
	}

	public function PollData($IOLMaster)
	{
		$dh = new DataHelperAccess($IOLMaster['filepath'],'', 'Meditec');
		$data = $this->dh->GetSQL("select * from patient");
		var_dump($data);
	}

	public function ListIOLMasters()
	{
		$pdo = $this->dh->Get("select * from iolmasters");
		return $pdo;
	}

	public function Add($id,$filepath)
	{
		$prep = $this->dh->prepare("insert into iolmasters (id,filepath) values (':id',':filepath')");
		$prep->execute(array(":id" => $id, ":filepath" =>$filepath));
	}

	public function Unavailable($IOLMaster)
	{

	}



}
