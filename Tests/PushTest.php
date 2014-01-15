<?php

include_once '../Components/DataHelperMySQL.php';
include_once '../components/Install.php';
include_once '../Components/IOL.php';
include_once '../Components/IOLReading.php';
include_once '../Config/config.php';

class PushTest extends PHPUnit_Framework_TestCase {
	private $db;
	private $install;
	private $config;
	public $iol;
	public $api;

	protected function setUp()
	{

		$this->config=Config::Load();


		$this->db=new DataHelperMySQL($this->config['tests']['db']['connectionString'],$this->config['tests']['db']['username'],$this->config['tests']['db']['password']);

		$this->api = $this->config['tests']['api'];

		$install = new Install($this->db);

		$this->install = $install;
		$this->install->RemoveTables('iolmasters_pushtest');
		$this->install->SetUpDatabase('iolmasters_pushtest');

		$iol = new IOL($this->db);
		$iol->Add('sample','IOLSample.mdb','notes');
		$this->iol=$iol;


		$IOLMaster = $iol->Get('sample');
		$iol->PollData($IOLMaster);


	}

	public function  testGetIOLReadings()
	{
		$iol_readings = new IOLReading($this->db);
		$readings = $iol_readings->GetAll();
		$this->AssertTrue(count($readings)>0);
	}

	public function testPushToApi()
	{
		$data = $this->db->Get("select * from ioldata");
		foreach($data as $iol_reading){

			$json_data = IOLReading::toJSON($iol_reading);

			$ch = curl_init();
			curl_setopt($ch, CURLOPT_URL, $this->api);
			curl_setopt($ch, CURLOPT_POSTFIELDS, $json_data);

			$response = curl_exec($ch);
			$http_status = curl_getinfo($ch , CURLINFO_HTTP_CODE);
			$this->AssertTrue($http_status==201 || $http_status==302);
			break;
		}
	}

	protected function tearDown()
	{

	}
}
