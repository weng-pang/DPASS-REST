<?php
/**
 * Model.php
 * Generic Model
 * 
 * A standardised model template for all models
 * 
 * @author Weng Long Pang
 *
 */
class Model{
	protected $database;
	protected $statement;
	protected $logStatement;
	protected $app;
	
	protected $type;
	protected $description;
	
	function __construct($app){
		$this->database  = new Database();
		$this->app = $app;
		$this->statement = $this->database->getConnection();
		// Database Logging
		$this->logStatement = $this->statement->prepare(ADD_LOG_RECORD);
		//Pay attention to http://stackoverflow.com/questions/14566929/php-and-mysqli-cannot-pass-parameter-2-by-reference-in
		// The bindParam simply cannot handle 
		$key = ((is_null($this->app->request()->params('key'))? 0 :$this->app->request()->params('key')));
		$this->logStatement->bindParam('key',$key );
		$this->logStatement->bindParam('ip', $this->app->request->getIp());
	}
	
	function save(){
		// for database log
		//$this->database->getConnection()->beginTransaction();
		$this->logStatement->bindParam('type',$this->type);
		$this->logStatement->bindParam('description',$this->description);
		$this->logStatement->bindParam('time', date(FULL_DATE_FORMAT));
		$this->logStatement->execute();
		// for webserver log
		$this->app->log->info(LOG_HEADER.'IP:'.$this->app->request->getIp().','.$this->type.','.$this->description);
		//$this->database->getConnection()->commit();
	}
	
	function getDescription(){
		return $this->description;
	}
	
	function getType(){
		return $this->type;
	}
	
	function getDatabaseConnection(){
		return $this->database;
	}
}