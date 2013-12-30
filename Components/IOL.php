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

		$this->LastChecked($IOLMaster['id']);
		if($data)
		{
			$this->LastAvailable($IOLMaster['id']);
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

	public function Count()
	{
		return $this->db->GetValue("select count(*) from iolmasters");
	}

	public function ListIOLMasters()
	{
		$pdo = $this->db->Get("select * from iolmasters");
		return $pdo;
	}

	public function Add($id,$filepath, $notes)
	{
		$this->db->ExecPrepared("insert into iolmasters (id,filepath,notes) values (:id,:filepath,:notes)",array(":id" => $id, ":filepath" =>$filepath,":notes" =>$notes));
	}

	public function Update($id,$filepath,$notes)
	{
		$this->db->ExecPrepared("update iolmasters set filepath=:filepath, notes=:notes where id=:id",array(":id" => $id, ":filepath" =>$filepath,":notes" =>$notes));
	}

	public function LastChecked($id)
	{
		$this->db->ExecPrepared ("update iolmasters set lastchecked=now() where id=:id",array(":id" => $id));
	}

	public function LastAvailable($id)
	{
		$this->db->ExecPrepared ("update iolmasters set lastavailable=now() where id=:id",array(":id" => $id));
	}

	public function LastPolled()
	{
		return $this->db->GetValue ("select lastchecked from iolmasters order by lastchecked desc limit 0,1");
	}

	public function Reachable()
	{
		return $this->db->GetValue ("select count(*) from iolmasters where lastavailable >= lastchecked limit 0,1");
	}



}
