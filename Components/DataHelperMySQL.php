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

	public function prepare($sql)
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

	public function get($sql)
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

	public function getValue($sql)
	{
		$get = $this->get($sql);
		if(!isset($get[0]))return null;
		return $get[0][0];
	}

	public function getSingle($sql)
	{
		$get = $this->get($sql);
		if(!isset($get[0]))return null;
		return $get[0];
	}

	public function execNoneQuery($sql)
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

	public function execPrepared($sql,$values)
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

	public static function checkPermissions($con,$username,$password)
	{
		try {
			$db = new PDO($con, $username, $password);
		} catch(Exception $e){
			echo $e->getMessage();
			return false;
		}
		return true;
	}

	public function tableExists($table)
	{
		$results = $this->db->query("SHOW TABLES LIKE '$table'");
		if($results->rowcount()>0) return true;
		return false;
	}
}

