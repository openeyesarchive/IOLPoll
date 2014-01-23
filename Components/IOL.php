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
		$data = $this->GetAccessData($IOLMaster);

		foreach($data as $row){
			$reading=serialize($row);
			$checksum = sha1($reading);
			$this->SaveIolReading($IOLMaster['id'],$reading,$checksum);
		}

		$this->LogAvailableStatus($IOLMaster['id'],$data);
	}

	public function GetAccessData($IOLMaster)
	{
		$db = new DataHelperAccess($IOLMaster['filepath'],'', 'Meditec');
		return $db->GetSQL("select * from patientdata");
	}

	public function SaveIolReading($id, $reading, $checksum)
	{
		$prep = $this->db->prepare("insert into ioldata (id,checksum,record,dateadded) values (:id,:checksum,:record,now())");
		if($prep->execute(array(":id" => $id, ":checksum" =>$checksum, ":record"=>$reading))){
			$this->log("$id-$checksum Added");
		}
		else{
			if($prep->errorinfo()[1]=1062){
				$this->log("$id-$checksum Already in database");
			}
			else{
				$this->log("Unknown error: ".$prep->errorinfo()[2]);
			}
		}
	}


	public function LogAvailableStatus($id,$data_available)
	{
		$this->LastChecked($id);

		if($data_available){
			$this->LastAvailable($id);
		}
		else{
			$this->log("No data available");
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

	public function Unreachable()
	{
		return $this->db->GetValue ("select count(*) from iolmasters where lastavailable <= Date_Add(lastchecked,interval -720 minute) limit 0,1");
	}

	public function Log($message)
	{
		$this->db->ExecPrepared("insert into iolpolllog (message,datecreated) values (:message,now())",array(":message" => $message));
		echo "\r\n$message\r\n";
	}

	public function GetLog()
	{
		$pdo = $this->db->Get("select * from iolpolllog order by id desc");
		return $pdo;
	}



}
