<?php
/**
 * Record.php
 * Main model class for handling records
 * 
 * All records should pass through this model, this includes validation, and database access
 * 
 * @author Weng Long Pang
 * @throws IllegalContentException
 */
class IllegalContentException extends Exception{};

class Record extends Model{	
	private $id;
	private $dateTime;
	private $machineId;
	private $entryId;
	private $ipAddress;
	private $portNumber;
	
	private $minimum = ABSOLUTE_MINIMUM;
	private $maximum = ABSOLUTE_MAXIMUM;
	
	function __construct($app){
		parent::__construct($app);
	}
	
	function add($content){
		try{
			if (is_null($content))
				throw new IllegalContentException('Please Check Input Parameters ');
			// arrange database log
			$this->type = 'ADD_RECORD';
			$this->description =  'ID:'.$content['id'].',MACHINE:'.$content['machineId'].',TIME:'.date('Y-m-d H:i', strtotime($content['dateTime'])).',TYPE:'.$content['entryId'].',MACHINEIP:'.$content['ipAddress'];
			$statement = $this->statement->prepare(ADD_SINGLE_RECORD);
			if (!is_integer($content['id']) || $content['id'] > MAXIMUM_ID)
				throw new IllegalContentException('Incorrect ID:'.$content['id']);
			if (!is_integer($content['machineId']) || $content['machineId'] > MAXIMUM_MACHINE_ID)
				throw new IllegalContentException('Incorrect Machine ID:'.$content['machineId']);
			if (!is_integer($content['entryId']) || $content['entryId'] > MAXIMUM_ENTRY_ID)
				throw new IllegalContentException('Incorrect Entry ID:'.$content['entryId']);
			if (!strtotime($content['dateTime']))
				throw new IllegalContentException('Incorrect Date:'.$content['dateTime']);
			if (!preg_match(IP_REGEX, $content['ipAddress']))
				throw new IllegalContentException('Incorrect IP Address:'.$content['ipAddress']);
			$statement->bindParam('id',$content['id']);
			$statement->bindParam('datetime',$content['dateTime']);
			$statement->bindParam('machineid',$content['machineId']);
			$statement->bindParam('entryid',$content['entryId']);
			$statement->bindParam('ipaddress',$content['ipAddress']);
			$statement->bindParam('portnumber',$content['portNumber']);
			$statement->bindParam('update', date(FULL_DATE_FORMAT));
			$statement->bindParam('key', $this->app->request()->params('key'));
			
			parent::save();
			$statement->execute();
			return array('transactionId' => intval($this->database->getConnection()->lastInsertId()));
		
		} catch (Exception $e){
			$this->database->getConnection()->rollBack();
			$this->description .= ','.$e->getMessage();
			parent::save();
			$this->app->halt(BAD_REQUEST,'{"error":{"procedure":"add record","text":"'.$e->getMessage().'"}}');
		}
	}
	
	function find($content){
		$parameterCount = MAXIMUM_PARAMETER;
		try{
			$statement = $this->statement->prepare(FIND_ENTRY_RECORDS);
			if (is_null($content))
				throw new IllegalContentException('Please Check Input Parameters ');
			// json message may consist of the following objectives :
			// for a specific id
			// for a specific machine
			// for a specific time
			// one of the criteria must be provided, however any of absence of data is regarded as wildcard
			// check id
			if (is_integer($content['id'])){
				$statement->bindParam('startid',$content['id']);
				$statement->bindParam('endid',$content['id']);
			} else {
				// invalid or empty id used, use "full range"
				$statement->bindParam('startid',$this->minimum);
				$statement->bindParam('endid',$this->maximum);
				$parameterCount--;
			}
			// check machine id
			if (is_integer($content['machineId'])){
				$statement->bindParam('startmachineid',$content['machineId']);
				$statement->bindParam('endmachineid',$content['machineId']);
			} else {
				// invalid or empty id used, use "full range"
				$statement->bindParam('startmachineid',$this->minimum);
				$statement->bindParam('endmachineid',$this->maximum);
				$parameterCount--;
			}
			// check record period
			if (strtotime($content['startTime']) < strtotime($content['endTime'])){
				$statement->bindParam('starttime', date('Y-m-d H:i', strtotime($content['startTime'])));
				$statement->bindParam('endtime', date('Y-m-d H:i', strtotime($content['endTime'])));
			} else {
				$parameterCount = $parameterCount - 2;
			}
			// arrange database log	
			$this->type = 'FIND_RECORD';
			$this->description =  'ID:'.$content['id'].',MACHINE:'.$content['machineId'].',START:'.date('Y-m-d H:i', strtotime($content['startTime'])).',END:'.date('Y-m-d H:i', strtotime($content['endTime'])).',PARAMETER:'.$parameterCount;
			parent::save();
			if ($parameterCount < MINIMUM_REQUIRE){
				throw new IllegalContentException('Insufficient Query Content');
			}
			$statement->execute();
			return $statement->fetchAll();
		
		} catch (Exception $e){
			$this->database->getConnection()->rollBack();
			$this->description .= ','.$e->getMessage();
			parent::save();
			$this->app->halt(BAD_REQUEST,'{"error":{"procedure":"find records","text":"'.$e->getMessage().'"}}');
		}
	}
}