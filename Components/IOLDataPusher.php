<?php

include_once 'DataHelperMySQL.php';
include_once 'DataHelperAccess.php';

class IOLDataPusher {

	private $db;
	private $api;

	public function __construct($db,$api)
	{
		$this->db=$db;
		$this->api=$api;
	}

	public function GetAll()
	{
		$pdo = $this->db->Get("select * from ioldata");
		return $pdo;
	}

	public function GetReadingsInQueue()
	{
		$pdo = $this->db->Get("select * from ioldata where not checksum in (select checksum from ioldatapushlog)");
		return $pdo;
	}


	public function PushReading($iol_reading)
	{
		return $this->PushJsonToApi($this->toJSON($iol_reading));
	}

	public function PushJsonToApi($json)
	{
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $this->api);
		curl_setopt($ch, CURLOPT_POSTFIELDS, $json);

		$response = curl_exec($ch);
		$http_status = curl_getinfo($ch , CURLINFO_HTTP_CODE);
		return $http_status;
	}

	public function LogSuccessfulPush($checksum)
	{
		$this->db->ExecPrepared("insert into ioldatapushlog (checksum,datecreated) values (:checksum,now())",array(":checksum" => $checksum));
	}


	public function PushReadingsInQueue($bool_remove_successful_push_from_queue=true)
	{
		$iol_readings = $this->GetReadingsInQueue();
		foreach($iol_readings as $iol_reading){
			$status_code=$this->PushReading($iol_reading);
			if(in_array($status_code,array('201','302'))) {
				$this->LogSuccessfulPush($iol_reading['checksum']);
			}
		}


	}

	public static function toJSON($reading)
	{
		$record= unserialize ($reading['record']);
		$record['iol_machine_id']=$reading['id'];
		$record['iol_poll_id']=$reading['checksum'];
		return json_encode($record);
	}


}
