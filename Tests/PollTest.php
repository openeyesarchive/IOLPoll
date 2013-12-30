<?php

include_once '../Components/DataHelperMySQL.php';
include_once '../components/Install.php';
include_once '../Components/IOL.php';
include_once '../Config/config.php';

class PollTest extends PHPUnit_Framework_TestCase {
	private $db;
	private $install;
	private $config;
	public $iol;

	protected function setUp()
	{
		$this->config=Config::Load();

		$this->db=new DataHelperMySQL($this->config['tests']['db']['connectionString'],$this->config['tests']['db']['username'],$this->config['tests']['db']['password']);

		$install = new Install($this->db);

		$this->install = $install;
		$this->install->RemoveTables('iolmasters_test');
		$this->install->SetUpDatabase('iolmasters_test');

		$iol = new IOL($this->db);
		$iol->Add('testiol','\\unreachable\unreachable.mdb','notes');
		$iol->Add('sample','IOLSample.mdb','notes');
		$this->iol=$iol;
	}


	protected function tearDown()
	{

	}

	public function testGetIOLMaster()
	{
		$iolmaster = $this->iol->Get('sample');
		$this->AssertTrue($iolmaster['filepath']=='IOLSample.mdb');
	}

	public function testDeleteIOLMaster()
	{

		$this->iol->Add('delme','test','notes');
		$this->iol->Delete('delme');
		$iolmaster = $this->iol->Get('delme');
		$this->AssertFalse($iolmaster);
	}

	public function testUpdateIOLMaster()
	{
		$this->iol->Add('update','test','notes');
		$this->iol->Update('update','newvalue','newnotes');
		$iolmaster = $this->iol->Get('update');
		$this->AssertTrue($iolmaster['filepath']=='newvalue');
		$this->AssertTrue($iolmaster['notes']=='newnotes');
	}


	public function testCheckPermissionsShouldFail()
	{
		$permissions=DataHelperMySQL::CheckPermissions('mysql:host=localhost;dbname=doesntexist','withexception','orinvalidpassword');
		$this->AssertFalse($permissions);
	}

	public function testCheckPermissionsShouldSucceed()
	{
		$permissions=DataHelperMySQL::CheckPermissions($this->config['tests']['db']['connectionString'],$this->config['tests']['db']['username'],$this->config['tests']['db']['password']);
		$this->AssertTrue($permissions);
	}

	public function testCheckInstalledShouldFail()
	{
		$table = $this->db->TableExists('fail');
		$this->AssertFalse($table);
	}

	public function testCheckInstalledShouldSucceed()
	{
		$table = $this->db->TableExists('iolmasters');
		$this->AssertTrue($table);
	}

	public function testListIOLMasters()
	{
		$IOLMasters = $this->iol->ListIOLMasters();
		$this->AssertTrue(is_array($IOLMasters));
		$this->AssertTrue(count($IOLMasters) > 0);
	}

	public function testStatsGetIOLCount(){
		$this->AssertTrue($this->iol->Count() == 2);
	}

	public function testStatsGetReachable(){
		$this->iol->LastChecked('sample');
		$this->iol->LastAvailable('sample');
		$reachable=$this->iol->Reachable();
		$this->AssertTrue($reachable == 1);
	}

	public function testStatsUnReachable(){
		$this->iol->LastChecked('sample');
		$this->db->ExecNoneQuery("update iolmasters set lastavailable=DATE_SUB(NOW(), INTERVAL 720 MINUTE) where id='sample'");
		$unreachable=$this->iol->Unreachable();
		$this->AssertTrue($unreachable == 1);
	}

	public function testStatsLastPolled(){
		$start = new DateTime();
		$this->iol->LastChecked('sample');
		$lastpolled = new DateTime($this->iol->LastPolled());
		$this->AssertTrue($start <= $lastpolled);
	}

	public function testDBListIOLMasters(){
		$pdo = $this->db->Get("select * from iolmasters");
		$this->AssertTrue(count($pdo) > 0);
	}

	public function testPollShouldBeUnreachableIOLMasters()
	{
		$IOLMasters =$this->iol->ListIOLMasters();

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
		$IOLMasters =$this->iol->ListIOLMasters();

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
		$IOLMasters =$this->iol->ListIOLMasters();

		foreach($IOLMasters as $IOLMaster)
		{
			if(file_exists($IOLMaster['filepath'])){
				$this->iol->PollData($IOLMaster);
			}
			else
			{
				$this->iol->LastChecked($IOLMaster['id']);
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
