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

	public function testListIOLMasters(){
		$pdo = $this->dh->Get("select * from iolmasters");
		$this->AssertTrue(count($pdo) > 0);
	}
}
