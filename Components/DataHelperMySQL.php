<?php

class DataHelperMySQL {

	private $db;

	public function __construct($con,$username,$password)
	{
		try {
			$db = new PDO($con, $username, $password);
			$this->db=$db;
		} catch (PDOException $e) {
			print "Error!: " . $e->getMessage();
			throw($e);
		}
		catch (Exception $e){
			print "Error!: " . $e->getMessage();
			throw($e);
		}

	}

	public function Prepare($sql)
	{
		try {
			if(!$prep = $this->db->prepare($sql)){
				echo "Error in query";
				throw new Exception(var_export($this->db->errorinfo(), TRUE));
			}
		}
		catch (Exception $e){
			print "Error!: " . $e->getMessage();
			throw($e);
		}
		return $prep;
	}

	public function Get($sql)
	{
		try {
			if(!$query = $this->db->query($sql)){
				echo "Error in query";
				throw new Exception(var_export($this->db->errorinfo(), TRUE));
			}
		} catch (PDOException $e) {
			print "Error!: " . $e->getMessage();
			throw($e);
		}
		catch (Exception $e){
			echo "Error!: " . $e->getMessage();
			throw($e);
		}
		return $query->fetchAll();
	}

	public function GetValue($sql)
	{
		$get = $this->Get($sql);
		return $get[0][0];
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
			throw($e);
		}
		catch (Exception $e){
			print "Error!: " . $e->getMessage();
			throw($e);
		}
		return $query->execute();
	}

	public function ExecPrepared($sql,$values)
	{
		try {
			$prep = $this->db->prepare($sql);
			if(!$return = $prep->execute($values)){
				print "Error in query";
				die(var_export($this->db->errorinfo(), TRUE));
			}
		} catch (PDOException $e) {
			print "Error!: " . $e->getMessage();
			throw($e);
		}
		catch (Exception $e){
			print "Error!: " . $e->getMessage();
			throw($e);
		}
		return $return;
	}

	public static function CheckPermissions($con,$username,$password)
	{
		try {
			$db = new PDO($con, $username, $password);
		} catch(Exception $e){
			echo $e->getMessage();
			return false;
		}
		return true;
	}

	public function TableExists($table)
	{
		$results = $this->db->query("SHOW TABLES LIKE '$table'");
		if($results->rowCount()>0) return true;
		return false;
	}
}

