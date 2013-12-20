<?php

class DataHelperAccess {

	private $con;

	public function __construct($filename,$username,$password)
	{
		$con=odbc_connect("Driver={Microsoft Access Driver (*.mdb)};Dbq=$filename", $username, $password);
		$this->con=$con;
	}

	public function GetSQL($sql)
	{
		$data = array();

		$result=odbc_exec($this->con,$sql);

		if($result){
			while ($row = odbc_fetch_array($result)){
				$data[] = $row;
			}
		}
		return $data;
	}


	public function GetTables()
	{
		$result = odbc_tables($this->con);
		$tables = array();
		while (odbc_fetch_row($result))
			array_push($tables, odbc_result($result, "TABLE_NAME"));
		return $tables;
	}
}
