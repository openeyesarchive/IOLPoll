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

	public function getAll()
	{
		$pdo = $this->db->get("select * from ioldata");
		return $pdo;
	}

	public function getReadingsInQueue()
	{
		$pdo = $this->db->get("select * from ioldata where not checksum in (select checksum from ioldatapushlog)");
		return $pdo;
	}


	public function pushReading($iol_reading)
	{
		return $this->pushJsonToApi($this->toJSON($iol_reading));
	}

	public function pushJsonToApi($json)
	{
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $this->api);
		curl_setopt($ch, CURLOPT_POSTFIELDS, $json);

		$response = curl_exec($ch);
		$http_status = curl_getinfo($ch , CURLINFO_HTTP_CODE);
		return $http_status;
	}

	public function logSuccessfulPush($checksum)
	{
		$this->db->execPrepared("insert into ioldatapushlog (checksum,datecreated) values (:checksum,now())",array(":checksum" => $checksum));
	}


	public function pushReadingsInQueue($bool_remove_successful_push_from_queue=true)
	{
		$iol_readings = $this->getReadingsInQueue();
		foreach($iol_readings as $iol_reading){
			$status_code=$this->pushReading($iol_reading);
			if(in_array($status_code,array('201','302'))) {
				$this->logSuccessfulPush($iol_reading['checksum']);
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
