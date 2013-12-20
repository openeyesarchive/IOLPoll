<?php
/**
 * Created by JetBrains PhpStorm.
 * User: Administrator
 * Date: 12/19/13
 * Time: 6:08 PM
 * To change this template use File | Settings | File Templates.
 */


include '../DataHelperMySQL.php';

class DatabaseTest extends PHPUnit_Framework_TestCase {

	public function testListIOLMasters(){
		$dh = new DataHelperMySQL('iolmasters','root','');
		$pdo = $dh->Get("select * from iolmasters");
		$this->AssertTrue(count($pdo) > 0);
	}


}
