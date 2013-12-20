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
		try {
			if(!$query = $this->db->query($sql)){
				echo "Error in query";
				die(var_export($this->db->errorinfo(), TRUE));
			}
		} catch (PDOException $e) {
			print "Error!: " . $e->getMessage();
			die();
		}
		catch (Exception $e){
			echo "Error!: " . $e->getMessage();
			die();
		}
		return $query->fetchAll();
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
			die();
		}
		catch (Exception $e){
			print "Error!: " . $e->getMessage();
			die();
		}
		return $return;
	}
}

