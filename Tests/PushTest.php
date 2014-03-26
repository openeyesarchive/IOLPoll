<?php

include_once '../Components/DataHelperMySQL.php';
include_once '../components/Install.php';
include_once '../Components/IOL.php';
include_once '../Components/IOLDataPusher.php';
include_once '../Config/config.php';

class PushTest extends PHPUnit_Framework_TestCase {
	private $db;
	private $install;
	private $config;
	public $iol;
	public $api;
	private $idp;

	protected function setUp()
	{
		$this->config=Config::Load();

		$this->db=new DataHelperMySQL($this->config['tests']['db']['connectionString'],$this->config['tests']['db']['username'],$this->config['tests']['db']['password']);

		$this->api = $this->config['tests']['api'];

		$this->idp = new IOLDataPusher($this->db,$this->api);

		$install = new Install($this->db);
		$this->install = $install;
		$this->install->RemoveTables('iolmasters_test');
		$this->install->SetUpDatabase('iolmasters_test');

		$iol = new IOL($this->db);
		$iol->add('sample','IOLSample.mdb','notes');
		$this->iol=$iol;


		$IOLMaster = $iol->get('sample');
		$readings = $iol->importall($IOLMaster);
        $this->iol->saveIOLReadings($IOLMaster,$readings);
	}

	public function  testGetIOLReadings()
	{
		$iol_readings = new IOLDataPusher($this->db);
		$readings = $iol_readings->GetAll();
		$this->assertTrue(count($readings)>0);
	}

	public function testPushFailMalformedJSON()
	{
		$http_status = $this->idp->PushJsonToAPI('x');
		$this->assertTrue($http_status==400);
	}

    public function testPushCorrectJSON()
    {

        $iolReadings = $this->idp->getAll();
        $iolReading = $iolReadings[0];
        $json = $this->idp->toJson($iolReading);

        $response = $this->idp->PushJsonToAPI($json);
        $http_status = $response['status'];

        $this->assertTrue($http_status==201);
    }

	public function testLogSuccessfulPush()
	{
		$this->idp->logSuccessfulPush('checksumtest');
		$record_exists = $this->db->GetValue("select * from ioldatapushlog where checksum='checksumtest'");
		$this->assertTrue($record_exists=='checksumtest');
	}

	public function testPushAndLogReadingToAPI()
	{
		$iol_readings = $this->idp->GetReadingsInQueue();
		$queue_count = count($iol_readings);
		$this->idp->PushReading($iol_readings[0]);
		$this->idp->logSuccessfulPush($iol_readings[0]['checksum']);
		$this->assertTrue(count($this->idp->GetReadingsInQueue())==$queue_count-1);
	}

	public function testPushAllReadingsToAPI()
	{
		$iol_readings = $this->idp->GetAll();
		foreach($iol_readings as $iol_reading){
			$http_status = $this->idp->PushReading($iol_reading);
			$this->assertTrue($http_status==201 || $http_status==302);
		}
	}

	public function testPushAllReadingsInQueue()
	{
		$this->idp->PushReadingsInQueue(true);
		$this->assertTrue(count($this->idp->GetReadingsInQueue())==0);
	}

	public function testGetReadingsToPush()
	{
		$iol_readings_to_push = $this->idp->GetReadingsInQueue();
		$this->assertTrue(count($iol_readings_to_push)>0);
	}

	protected function tearDown()
	{

	}
}
