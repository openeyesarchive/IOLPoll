<?php

include_once '../Components/DataHelperMySQL.php';
include_once '../components/Install.php';
include_once '../Components/IOL.php';
include_once '../Config/config.php';

// cmdkey /add:iolm /user:IOLMaster /pass:Iolm_XP

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
		$iol->add('testiol','\\unreachable\unreachable.mdb','notes');
		$iol->add('sample','IOLSample.mdb','notes');
		$this->iol=$iol;
	}


	protected function tearDown()
	{

	}

	public function testGetIOLMaster()
	{
		$iolmaster = $this->iol->get('sample');
		$this->assertTrue($iolmaster['filepath']=='IOLSample.mdb');
	}

	public function testDeleteIOLMaster()
	{
		$this->iol->add('delme','test','notes');
		$this->iol->Delete('delme');
		$iolmaster = $this->iol->get('delme');
		$this->assertFalse($iolmaster);
	}

	public function testUpdateIOLMaster()
	{
		$this->iol->add('update','test','notes');
		$this->iol->update('update','newvalue','newnotes');
		$iolmaster = $this->iol->get('update');
		$this->assertTrue($iolmaster['filepath']=='newvalue');
		$this->assertTrue($iolmaster['notes']=='newnotes');
	}


	public function testcheckPermissionsShouldFail()
	{
		$permissions=DataHelperMySQL::checkPermissions('mysql:host=localhost;dbname=doesntexist','withexception','orinvalidpassword');
		$this->assertFalse($permissions);
	}

	public function testcheckPermissionsShouldSucceed()
	{
		$permissions=DataHelperMySQL::checkPermissions($this->config['tests']['db']['connectionString'],$this->config['tests']['db']['username'],$this->config['tests']['db']['password']);
		$this->assertTrue($permissions);
	}

	public function testCheckInstalledShouldFail()
	{
		$table = $this->db->tableExists('fail');
		$this->assertFalse($table);
	}

	public function testCheckInstalledShouldSucceed()
	{
		$table = $this->db->tableExists('iolmasters');
		$this->assertTrue($table);
	}

	public function testListIOLMasters()
	{
		$IOLMasters = $this->iol->listIOLMasters();
		$this->assertTrue(is_array($IOLMasters));
		$this->assertTrue(count($IOLMasters) > 0);
	}

	public function testStatsGetIOLcount(){
		$this->assertTrue($this->iol->count() == 2);
	}

	public function testStatsGetreachable(){
		$this->iol->LastChecked('sample');
		$this->iol->LastAvailable('sample');
		$reachable=$this->iol->reachable();
		$this->assertTrue($reachable == 1);
	}

	public function testStatsUnreachable(){
		$this->iol->LastChecked('sample');
		$this->db->ExecNoneQuery("update iolmasters set lastavailable=DATE_SUB(NOW(), INTERVAL 720 MINUTE) where id='sample'");
		$unreachable=$this->iol->unreachable();
		$this->assertTrue($unreachable == 1);
	}

	public function testStatslastPolled(){
		$start = new DateTime();
		$this->iol->LastChecked('sample');
		$lastpolled = new DateTime($this->iol->lastPolled());
		$this->assertTrue($start <= $lastpolled);
	}

	public function testDBListIOLMasters(){
		$pdo = $this->db->get("select * from iolmasters");
		$this->assertTrue(count($pdo) > 0);
	}

	public function testPollShouldBeunreachableIOLMasters()
	{
		$IOLMasters =$this->iol->listIOLMasters();

		$reachable=true;
		foreach($IOLMasters as $IOLMaster){
			if(!file_exists($IOLMaster['filepath'])){
				$reachable=false;
			}
		}
		$this->assertFalse($reachable);
	}

	public function testPollShouldBeSomereachableIOLMasters()
	{
		$IOLMasters =$this->iol->listIOLMasters();

		$reachable=false;
		foreach($IOLMasters as $IOLMaster)
		{
			if(file_exists($IOLMaster['filepath'])){
				$reachable=true;
			}
		}
		$this->assertTrue($reachable);
	}

	public function testSampledb()
	{
		$db = new DataHelperAccess('IOLSample.mdb','', 'Meditec');
		$data = $db->getSQL("select * from patientdata");
		$this->assertTrue(count($data) > 0);
	}

	public function testPullDataFromReachableIOLMaster()
	{
		$IOLMasters =$this->iol->listIOLMasters();

        foreach($IOLMasters as $IOLMaster)
        {
            $readings = $this->iol->importAll($IOLMaster);
            if(isset($readings)){
                $this->iol->saveIOLReadings($IOLMaster,$readings);
            }
            $this->iol->logAvailableStatus($IOLMaster['id'],isset($readings));
        }

		$data=$this->db->get("select * from ioldata");
		$this->assertTrue(count($data) > 0);

		$data=$this->db->get("select * from iolmasters where lastavailable is not null");
		$this->assertTrue(count($data) > 0);

		$data=$this->db->get("select * from iolmasters where lastavailable is null");
		$this->assertTrue(count($data) > 0);

		$data=$this->db->get("select * from iolmasters where lastchecked is not null");
		$this->assertTrue(count($data)==2);

	}


    public function testPollDataSinceLastAvailable()
    {
        $IOLMaster =$this->iol->get('sample');
        $IOLMaster['lastavailable']='2013-01-14 00:00:00';
        $readings = $this->iol->importRecent($IOLMaster);
        $this->assertTrue(count($readings)==304);
    }



	public function testLogMessages()
	{
		$this->iol->log('test message');
		$this->iol->log('test message 2');
		$log = $this->iol->GetLog();
		$this->assertTrue($log[0]['message']=='test message 2');
		$this->assertTrue($log[1]['message']=='test message');
	}







	}
