<?php

class DataHelperMySQL {

	private $db;

	public function __construct($database,$username,$password)
	{
		try {
			$db = new PDO("mysql:host=localhost;dbname=$database", $username, $password);
			$this->db=$db;
		} catch (PDOException $e) {
			print "Error!: " . $e->getMessage();
			die();
		}
		catch (Exception $e){
			print "Error!: " . $e->getMessage();
			die();
		}

	}

	public function Prepare($sql)
	{
		return $this->db->prepare($sql);
	}

	public function Get($sql)
	{
		return $this->db->query($sql)->fetchAll();
	}

	public function ExecNoneQuery($sql)
	{
		try {
			if(!$query = $this->db->query($sql)){
				print "Error in query";
				die(var_export($this->db->errorinfo(), TRUE));
			}
		} catch (PDOException $e) {
			print "Error!: " . $e->getMessage();
			die();
		}
		catch (Exception $e){
			print "Error!: " . $e->getMessage();
			die();
		}
		return $query->execute();
	}
}

