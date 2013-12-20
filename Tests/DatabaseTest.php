<?php

include '../DataHelperMySQL.php';
include '../Install.php';

class DatabaseTest extends PHPUnit_Framework_TestCase {

	protected function setUp()
	{
		Install::SetUpDatabase('iolmasters_test');
	}

	protected function tearDown()
	{
		Install::RemoveTables('iolmasters_test');
	}

	public function testListIOLMasters(){
		$dh = new DataHelperMySQL('iolmasters_test','root','');
		$pdo = $dh->Get("select * from iolmasters");
		$this->AssertTrue(count($pdo) > 0);
	}


}
