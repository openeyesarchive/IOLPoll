<?php

include_once 'DataHelperMySQL.php';
include_once 'DataHelperAccess.php';

class IOL {

	private $db;

	public function __construct($db)
	{
		$this->db=$db;
	}

	public function PollData($IOLMaster)
	{
		$db = new DataHelperAccess($IOLMaster['filepath'],'', 'Meditec');

		$data = $db->GetSQL("select * from patientdata");

		foreach($data as $row){
			$record=serialize($row);
			$checksum = sha1($record);
			$prep = $this->db->prepare("insert into ioldata (id,checksum,record,dateadded) values (:id,:checksum,:record,now())");

			//this will fail silently if the checksum is already in the database
			$prep->execute(array(":id" => $IOLMaster['id'], ":checksum" =>$checksum, ":record"=>$record));

		}

		$this->LastChecked($IOLMaster);
		if($data)
		{
			$this->LastAvailable($IOLMaster);
		}



	}

	public function Get($id)
	{
		$pdo = $this->db->Prepare("select * from iolmasters where id=:id");
		$pdo->execute(array(':id'=>$id));
		$res=$pdo->fetch();
		return $res;
	}

	public function Delete($id)
	{
		$pdo = $this->db->Prepare("delete from iolmasters where id=:id");
		$pdo->execute(array(':id'=>$id));
	}

	public function ListIOLMasters()
	{
		$pdo = $this->db->Get("select * from iolmasters");
		return $pdo;
	}

	public function Add($id,$filepath)
	{
		$this->db->ExecPrepared("insert into iolmasters (id,filepath) values (:id,:filepath)",array(":id" => $id, ":filepath" =>$filepath));
	}

	public function LastChecked($IOLMaster)
	{
		$this->db->ExecPrepared ("update iolmasters set lastchecked=now() where id=:id",array(":id" => $IOLMaster['id']));
	}

	public function LastAvailable($IOLMaster)
	{
		$this->db->ExecPrepared ("update iolmasters set lastavailable=now() where id=:id",array(":id" => $IOLMaster['id']));
	}



}
