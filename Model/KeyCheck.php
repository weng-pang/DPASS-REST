<?php
//require_once 'configuration.php';
//require_once 'Database.php';

/**
 * KeyCheck.php
 * API key checker
 * 
 * All access to this programe must be validated by means of checking the API key.
 * This process will check all access constrains comply for use of this service.
 * 
 * @author Weng Long Pang
 * @throws NotAuthorizedException
 */
class NotAuthorizedException extends Exception{};

class KeyCheck extends Model{
	
	function __construct($app){
		parent::__construct($app);
		
	}
	
	function validate($key){
		try{// Pay attention to http://stackoverflow.com/questions/14566929/php-and-mysqli-cannot-pass-parameter-2-by-reference-in
			$this->type = 'CHECK_KEY';
			//$this->description = 'KEY:'.$key;
			$statement = $this->database->getConnection()->prepare(FIND_API_KEY);
			// Check configurations
			$configurationStatement = $this->database->getConnection()->prepare(OBTAIN_SETTING);
			$configurationStatement->execute();
			$result = $configurationStatement->fetchAll();
			//var_dump($result);
			foreach ($result as $setting){
				if (array_search('enabled', $setting)){
					if (!$setting['setting']){
						throw new NotAuthorizedException('Service Closed');
					}
				}
				if (array_search('https_only', $setting)){
					//var_dump($setting['setting']);
					if ($setting['setting']){
						if ($this->app->getScheme() != 'https')
							throw new NotAuthorizedException('Secured Connection Only');
					}
				}
			}
			// check for key access
			if (is_null($key))
				throw new NotAuthorizedException('Key Not Provided.');
			$statement->bindParam('key',$key);
			$statement->execute();
			$result = $statement->fetchAll();
			//var_dump($result);
			$notValid = sizeof($result) <> 1 ;
			$expired = 0;
			$revoked = 0;
			if (!$notValid){
				$expired = time() - strtotime($result[0]['expire']) > 0;
				$revoked = $result[0]['revoked'];
			}
			$this->description .= ',NOT_VALID:'.($notValid ? 'true' : 'false').',EXPIRED:'.($expired ? 'true' : 'false').',REVOKED:'.($revoked ? 'true' : 'false');
			parent::save();
			if ($notValid){
				throw new NotAuthorizedException('Illegal Key Found.');
			} else {
				// check for expiry
				if ($expired)
					throw new NotAuthorizedException('Key Expired.');
				// check for revoke
				if ($revoked)
					throw new NotAuthorizedException('Key Revoked.');
				//return $result;
				return true;
			}
		} catch (Exception $e){
			$this->description .= ','.$e->getMessage();
			parent::save();
			$this->app->halt(NOT_AUTHORISED,'{"error":{"procedure":"key check","text":"'.$e->getMessage().'"}}');
		}
	}
}
