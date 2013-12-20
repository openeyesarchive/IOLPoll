<?php

include_once '../Components/DataHelperMySQL.php';
include_once  '../Install.php';
include_once '../Components/IOL.php';
include_once '../Config/config.php';

class DatabaseTest extends PHPUnit_Framework_TestCase {
	private $dh;
	private $install;

	protected function setUp()
	{
		$config=Config::Load();
		$this->dh=new DataHelperMySQL($config['tests']['db']['connectionString'],$config['tests']['db']['username'],$config['tests']['db']['password']);

		$install = new Install($this->dh);
		$this->install = $install;
		$this->install->SetUpDatabase('iolmasters_test');

		$iol = new IOL($this->dh);
		$iol->Add('testiol','\\unreachable\unreachable.mdb');
	}

	protected function tearDown()
	{
		$this->install->RemoveTables('iolmasters_test');
	}

	public function testListIOLMasters()
	{
		$iol= new IOL($this->dh);
		$IOLMasters = $iol->ListIOLMasters();
		$this->AssertTrue(is_array($IOLMasters));
		$this->AssertTrue(count($IOLMasters) > 0);
	}

	public function testDBListIOLMasters(){
		$pdo = $this->dh->Get("select * from iolmasters");
		$this->AssertTrue(count($pdo) > 0);
	}

	public function testPollShouldBeUnreachableIOLMasters()
	{
		$iol= new IOL($this->dh);
		$IOLMasters =$iol->ListIOLMasters();

		foreach($IOLMasters as $IOLMaster)
		{
			if(file_exists($IOLMaster['filepath'])){
				die('should not be reachable');
			}
			else{
				$iol->Unavailable($IOLMaster);
			}
		}
	}

	public function testSampleDB()
	{
		$dh = new DataHelperAccess('IOLSample.mdb','', 'Meditec');
		$data = $dh->GetSQL("select * from patientdata");
		$this->AssertTrue(count($data) > 0);
	}
}
