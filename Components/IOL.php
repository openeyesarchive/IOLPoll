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
		$data = $dh->GetSQL("select * from patientdata");

		foreach($data as $row){
			$record=serialize($row);
			$checksum = sha1($record);
			$prep = $this->dh->prepare("insert into ioldata (id,checksum,record,dateadded) values (:id,:checksum,:record,now())");
			
			//this will fail silently if the checksum is already in the database
			$prep->execute(array(":id" => $IOLMaster['id'], ":checksum" =>$checksum, ":record"=>$record));

		}

	}

	public function ListIOLMasters()
	{
		$pdo = $this->dh->Get("select * from iolmasters");
		return $pdo;
	}

	public function Add($id,$filepath)
	{
		$prep = $this->dh->prepare("insert into iolmasters (id,filepath) values (:id,:filepath)");
		$prep->execute(array(":id" => $id, ":filepath" =>$filepath));
	}

	public function Unavailable($IOLMaster)
	{

	}



}
