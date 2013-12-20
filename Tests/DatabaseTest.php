<?php

include_once '../Components/DataHelperMySQL.php';
include_once  '../Install.php';
include_once '../Components/IOL.php';
include_once '../Config/config.php';

class DatabaseTest extends PHPUnit_Framework_TestCase {
	private $db;
	private $install;

	protected function setUp()
	{
		$config=Config::Load();
		$this->db=new DataHelperMySQL($config['tests']['db']['connectionString'],$config['tests']['db']['username'],$config['tests']['db']['password']);

		$install = new Install($this->db);

		$this->install = $install;
		$this->install->RemoveTables('iolmasters_test');
		$this->install->SetUpDatabase('iolmasters_test');

		$iol = new IOL($this->db);
		$iol->Add('testiol','\\unreachable\unreachable.mdb');
		$iol->Add('sample','IOLSample.mdb');
	}


	protected function tearDown()
	{

	}

	public function testListIOLMasters()
	{
		$iol= new IOL($this->db);
		$IOLMasters = $iol->ListIOLMasters();
		$this->AssertTrue(is_array($IOLMasters));
		$this->AssertTrue(count($IOLMasters) > 0);
	}

	public function testDBListIOLMasters(){
		$pdo = $this->db->Get("select * from iolmasters");
		$this->AssertTrue(count($pdo) > 0);
	}

	public function testPollShouldBeUnreachableIOLMasters()
	{
		$iol= new IOL($this->db);
		$IOLMasters =$iol->ListIOLMasters();

		$reachable=true;
		foreach($IOLMasters as $IOLMaster)
		{
			if(!file_exists($IOLMaster['filepath'])){
				$reachable=false;
			}
		}
		$this->AssertFalse($reachable);
	}

	public function testPollShouldBeSomeReachableIOLMasters()
	{
		$iol= new IOL($this->db);
		$IOLMasters =$iol->ListIOLMasters();

		$reachable=false;
		foreach($IOLMasters as $IOLMaster)
		{
			if(file_exists($IOLMaster['filepath'])){
				$reachable=true;
			}
		}
		$this->AssertTrue($reachable);
	}

	public function testSampleDB()
	{
		$db = new DataHelperAccess('IOLSample.mdb','', 'Meditec');
		$data = $db->GetSQL("select * from patientdata");
		$this->AssertTrue(count($data) > 0);
	}

	public function testPullDataFromReachableIOLMaster()
	{
		$iol= new IOL($this->db);
		$IOLMasters =$iol->ListIOLMasters();

		foreach($IOLMasters as $IOLMaster)
		{
			if(file_exists($IOLMaster['filepath'])){
				$iol->PollData($IOLMaster);
			}
			else
			{
				$iol->LastChecked($IOLMaster);
			}
		}

		$data=$this->db->Get("select * from ioldata");
		$this->AssertTrue(count($data) > 0);

		$data=$this->db->Get("select * from iolmasters where lastavailable is not null");
		$this->AssertTrue(count($data) > 0);

		$data=$this->db->Get("select * from iolmasters where lastavailable is null");
		$this->AssertTrue(count($data) > 0);

		$data=$this->db->Get("select * from iolmasters where lastchecked is not null");
		$this->AssertTrue(count($data)==2);

	}





}
