<?php

/**
 * Database.php
 * Database Provider
 * 
 * The Class for providing data access to a database
 * 
 * @author Weng Long Pang
 * @throws PDOException
 */
class Database{
	
	private $connection;

	function __construct(){
		try{

			$this->connection = new PDO(DB_CONNECTION_STRING, DB_USER, DB_PASSWORD);
			$this->connection->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
			
		} catch (PDOException $e){
			echo 'ERROR: '. $e->getMessage();
		}
	}
	
	function getConnection(){
		return $this->connection;
	}
}