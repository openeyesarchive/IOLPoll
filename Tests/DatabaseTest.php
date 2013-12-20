<?php

include_once '../DataHelperMySQL.php';
include_once  '../Install.php';
include_once  '../IOL.php';

class DatabaseTest extends PHPUnit_Framework_TestCase {

	private $dh;

	protected function setUp()
	{
		$this->dh=new DataHelperMySQL('iolmasters_test','root','');
		Install::SetUpDatabase('iolmasters_test');
		$iol = new IOL($this->dh);
		$iol->Add('testiol','\\unreachable\unreachable.mdb');
	}

	protected function tearDown()
	{
		Install::RemoveTables('iolmasters_test');
	}

	public function testListIOLMasters(){
		$pdo = $this->dh->Get("select * from iolmasters");
		$this->AssertTrue(count($pdo) > 0);
	}


}
