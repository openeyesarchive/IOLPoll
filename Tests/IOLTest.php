<?php
/**
 * Created by JetBrains PhpStorm.
 * User: Administrator
 * Date: 12/19/13
 * Time: 5:35 PM
 * To change this template use File | Settings | File Templates.
 */

include '../IOL.php';
include '../DataHelperAccess.php';

class IOLTest extends PHPUnit_Framework_TestCase {

	public function testListIOLMasters()
	{
		$IOLMasters = IOL::ListIOLMasters();

		$this->AssertTrue(is_array($IOLMasters));
		$this->AssertTrue(count($IOLMasters) > 0);
	}

	public function testPollData()
	{
		$IOLMasters = IOL::ListIOLMasters();
		foreach($IOLMasters as $IOLMaster)
		{
			$dh = new DataHelperAccess($IOLMaster['filepath'],'', 'Hic2003Jack');
			$data = $dh->GetSQL("select * from patient");
			$this->AssertTrue(count($data) > 0);
		}
	}
}
