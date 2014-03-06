<?php

include_once 'DataHelperMySQL.php';
include_once 'DataHelperAccess.php';

class IOL {

	private $db;

	public function __construct($db)
	{
		$this->db=$db;
	}

    public function saveIolReadings($IOLMaster,$readings)
    {
        foreach($readings as $row){
            $reading=serialize($row);
            $checksum = sha1($reading);
            $this->saveIolReading($IOLMaster['id'],$reading,$checksum);
        }
    }

	public function importAll($IOLMaster)
	{
        if(file_exists($IOLMaster['filepath'])){
		$db = new DataHelperAccess($IOLMaster['filepath'],'', 'Meditec');
		return $db->getSQL("select * from patientdata");
        }
	}

    public function importRecent($IOLMaster)
    {
        if(file_exists($IOLMaster['filepath'])){
            $db = new DataHelperAccess($IOLMaster['filepath'],'', 'Meditec');
            $date = new DateTime($IOLMaster['lastavailable']);
            $date->modify("-1 Day");
            $accessStyleDate = $date->format('m-d-Y');
            return $db->getSQL("select * from patientdata where measurement_date > #".$accessStyleDate."#");
        }
    }

	public function saveIolReading($id, $reading, $checksum)
	{
		$prep = $this->db->prepare("insert into ioldata (id,checksum,record,dateadded) values (:id,:checksum,:record,now())");
		if($prep->execute(array(":id" => $id, ":checksum" =>$checksum, ":record"=>$reading))){
			$this->log("$id-$checksum Added");
		}
		else{
            $error = $prep->errorinfo();
			if($error[1]=1062){
				$this->log("$id-$checksum Already in database");
			}
			else{
				$this->log("Unknown error: ".$error[2]);
			}
		}
	}


	public function logAvailableStatus($id,$data_available)
	{

        $this->logUptime($id,$data_available);
		$this->lastChecked($id);

		if($data_available){
			$this->lastAvailable($id);
		}
		else{
			$this->log("No data available");
		}
	}

    public function logUptime($id,$available)
    {
        $this->db->execPrepared("insert into ioluptime (id,checked,available) values (:id,:checked,:available)",array(":id" => $id, ":checked" => date('Y-m-d H:i:s'),":available" =>$available));
    }

    public function uptimeStats($id,$startDate,$endDate)
    {
        $pdo = $this->db->Prepare("select * from ioluptime where id=:id and checked >= :startDate and checked <= :endDate order by checked");
        $pdo->execute(array(':id'=>$id,':startDate'=>$startDate,':endDate'=>$endDate));
        $res=$pdo->fetchAll();
        return $res;
    }

	public function get($id)
	{
		$pdo = $this->db->Prepare("select * from iolmasters where id=:id");
		$pdo->execute(array(':id'=>$id));
		$res=$pdo->fetch();
		return $res;
	}

	public function delete($id)
	{
		$pdo = $this->db->prepare("delete from iolmasters where id=:id");
		$pdo->execute(array(':id'=>$id));
	}

	public function count()
	{
		return $this->db->getValue("select count(*) from iolmasters");
	}

	public function listIOLMasters()
	{
		$pdo = $this->db->get("select * from iolmasters");
		return $pdo;
	}

	public function add($id,$filepath, $notes)
	{
		$this->db->execPrepared("insert into iolmasters (id,filepath,notes) values (:id,:filepath,:notes)",array(":id" => $id, ":filepath" =>$filepath,":notes" =>$notes));
	}

	public function update($id,$filepath,$notes)
	{
		$this->db->execPrepared("update iolmasters set filepath=:filepath, notes=:notes where id=:id",array(":id" => $id, ":filepath" =>$filepath,":notes" =>$notes));
	}

	public function lastChecked($id)
	{
		$this->db->execPrepared ("update iolmasters set lastchecked=now() where id=:id",array(":id" => $id));
	}

	public function lastAvailable($id)
	{
		$this->db->execPrepared ("update iolmasters set lastavailable=now() where id=:id",array(":id" => $id));
	}

	public function lastPolled()
	{
		return $this->db->getValue ("select lastchecked from iolmasters order by lastchecked desc limit 0,1");
	}

	public function reachable()
	{
		return $this->db->getValue ("select count(*) from iolmasters where lastavailable >= lastchecked limit 0,1");
	}

	public function unreachable()
	{
		return $this->db->getValue ("select count(*) from iolmasters where lastavailable <= Date_Add(lastchecked,interval -720 minute) limit 0,1");
	}

	public function log($message)
	{
		$this->db->execPrepared("insert into iolpolllog (message,datecreated) values (:message,now())",array(":message" => $message));
		echo "\r\n$message\r\n";
	}

	public function getLog()
	{
		$pdo = $this->db->get("select * from iolpolllog order by id desc");
		return $pdo;
	}



}
