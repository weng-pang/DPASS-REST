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
class IllegalCheckRequestException extends Exception{};

class Record extends Model{	
	private $id;
	private $dateTime;
	private $machineId;
	private $entryId;
	private $ipAddress;
	private $portNumber;
	private $updateDate;
	
	private $minimum = ABSOLUTE_MINIMUM;
	private $maximum = ABSOLUTE_MAXIMUM;
	
	function __construct($app){
		parent::__construct($app);
		$this->updateDate = date(FULL_DATE_FORMAT);
	}
	/**
	 * add 
	 * This function adds a single record to the database
	 * The input content must conform with a specific content in order to add a recotd successfully
	 * 
	 * 
	 * @param json $content
	 * @throws IllegalContentException
	 * @return json
	 */
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

            $key = $this->app->request()->params('key');

			$statement->bindParam('id',$content['id']);
			$statement->bindParam('datetime',$content['dateTime']);
			$statement->bindParam('machineid',$content['machineId']);
			$statement->bindParam('entryid', $content['entryId']);
			$statement->bindParam('ipaddress', $content['ipAddress']);
			$statement->bindParam('portnumber', $content['portNumber']);
			$statement->bindParam('update', $this->updateDate);
			$statement->bindParam('key', $key);
			
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
	/**
	 * Find 
	 * This function finds records under a range of criterion using json message
	 * json message may consist of the following objectives :
	 * for a specific id
	 * for a specific machine
	 * for a specific time
	 * at least one of the criteria must be provided (exception may be thrown)
	 * , however any of absence of data is regarded as wildcard
	 * 
	 * @param json $content
	 * @throws IllegalContentException
	 * @return json:
	 */
	function find($content){
		$parameterCount = MAXIMUM_PARAMETER;
		$this->type = 'FIND_RECORD';
//		try{
			$statement = $this->statement->prepare(FIND_ENTRY_RECORDS);
			if (is_null($content))
				throw new IllegalContentException('Please Check Input Parameters ');
			// // check id
			if (isset($content['id']) && is_integer($content['id'])){
				$statement->bindParam('startid',$content['id']);
				$statement->bindParam('endid',$content['id']);
			} else {
				// invalid or empty id used, use "full range"
				$statement->bindParam('startid',$this->minimum);
				$statement->bindParam('endid',$this->maximum);
				$parameterCount--;
			}
			// check machine id
			if (isset($content['machineId']) && is_integer($content['machineId'])){
				$statement->bindParam('startmachineid',$content['machineId']);
				$statement->bindParam('endmachineid',$content['machineId']);
			} else {
				// invalid or empty id used, use "full range"
				$statement->bindParam('startmachineid',$this->minimum);
				$statement->bindParam('endmachineid',$this->maximum);
				$parameterCount--;
			}
			// check record period
			// give a default variable for the application
			if (!isset($content['startTime'])){
				$content['startTime'] = ABSOLUTE_MINIMUM;
                $parameterCount--;
			}
			if (!isset($content['endTime'])){
				$content['endTime'] = ABSOLUTE_MAXIMUM - 1000;
                $parameterCount--;
			}
        $startTime = date(FULL_DATE_FORMAT_SEARCH, strtotime($content['startTime']));
        $endTime = date(FULL_DATE_FORMAT_SEARCH, strtotime($content['endTime'])); echo $endTime;
        $statement->bindParam('starttime', $startTime);
        $statement->bindParam('endtime', $endTime);
        
			// arrange database log	
			$this->type = 'FIND_RECORD';
			$this->description =  'ID:'.(isset($content['id']) ? $content['id']: '-');
			$this->description .= ',MACHINE:'.(isset($content['machineId']) ? $content['machineId']: '-');
			$this->description .= ',START:';
			$this->description .= ($content['startTime'] >= ABSOLUTE_MINIMUM) ? date(FULL_DATE_FORMAT_SEARCH, strtotime($content['startTime'])):'-';
			$this->description .= ',END:';
			$this->description .= ($content['endTime'] <= ABSOLUTE_MAXIMUM) ? date(FULL_DATE_FORMAT_SEARCH, (strtotime($content['endTime']))): '-';
			$this->description .= ',PARAMETER:'.$parameterCount;
			parent::save(); echo $this->description;
			if ($parameterCount < MINIMUM_REQUIRE){
				throw new IllegalContentException('Insufficient Query Content');
			}
			$statement->execute();
			return $statement->fetchAll(PDO::FETCH_ASSOC);
		
//		} catch (Exception $e){
//			$this->database->getConnection()->rollBack();
//			$this->description .= ','.$e->getMessage();
//			parent::save();
//			$this->app->halt(BAD_REQUEST,'{"error":{"procedure":"find records","text":"'.$e->getMessage().'"}}');
//		}
	}
	
	/**
	 * check update
	 * This function returns the latest updated record from the database except revoked records
	 * It is primary used for information auditing to ensure all records are uploaded frequently
	 */
	function checkUpdates(){
		$this->type = 'CHECK_UPDATES';
		$this->description = 'Check for update from current database';
		try{
			$statement = $this->statement->prepare(CHECK_UPDATES);
			$statement->execute();
			return $statement->fetchAll(PDO::FETCH_ASSOC);
		} catch (Exception $e){
			$this->database->getConnection()->rollBack();
			$this->description .= ','.$e->getMessage();
			parent::save();
			$this->app->halt(BAD_REQUEST,'{"error":{"procedure":"check updates","text":"'.$e->getMessage().'"}}');
		}
	}
	
	/**
	 * check latest record
	 * This function returns the latest record entry with respect to each profile on the databaase.
	 * Important: The records are not realtime, they are updated by periodic uploads.
	 */
	function checkRecords($request){
		$this->type = 'CHECK_LATEST_RECORD';
		$this->description = 'Check for latest record upload';
		try{
			switch ($request){
				case LATEST_STAFF_ENTRIES:
					$statement = $this->statement->prepare(CHECK_LATEST_RECORD);
					break;
				case COMPUTER_REPORTS:
					$statement = $this->statement->prepare($statement); //TODO change this one
					break;
				case LATEST_STUDENT_ENTRIES:
					$statement = $this->statement->prepare($statement); //TODO this one as well
					break;
				default:
					throw IllegalCheckRequestException('This Request does not exist');
			}
			$statement->execute();
			return $statement->fetchAll(PDO::FETCH_ASSOC);
		} catch (Exception $e){
			$this->database->getConnection()->rollBack();
			$this->description .= ','.$e->getMessage();
			parent::save();
			$this->app->halt(BAD_REQUEST,'{"error":{"procedure":"check updates","text":"'.$e->getMessage().'"}}');
		}
	}
	
	/**
	 * revoke
	 * Attndance record may be revoked, under the authority given by a manager
	 * The record itself is to be marked as revoked, but a log record will cover enerything concerning the revoke of record in concern
	 * 
	 * @param array $content['serial']
     * @retun null
	 */
	function revoke($content){
		$this->type = 'REVOKE';
		$this->description =  'TRANSACTIONID:'.$content['serial'];
		try{
			if (is_null($content)) // the content must be provided
				throw new IllegalContentException('Please Provide the Transaction ID Number ');
			$statement = $this->statement->prepare(REVOKE_RECORD);
			$statement->bindParam('serial',$content['serial']);
			$statement->bindParam('update', $this->updateDate);
			parent::save();
			$statement->execute();
			// check for data integrity - no invalid serial number given here
			if ($statement->rowCount())
				return $content;
			throw new IllegalContentException('Please Provide a correct Transaction ID Number ');
		} catch (Exception $e){
			$this->database->getConnection()->rollBack();
			$this->description .= ','.$e->getMessage();
			parent::save();
			$this->app->halt(BAD_REQUEST,'{"error":{"procedure":"revoke record","text":"'.$e->getMessage().'"}}');
		}
	}

    /**
     * approve
     * Approve attendance record entry by staff
     *
     * @param $content
     */
    function approve($content){
        try{
            if (is_null($content))
                throw new IllegalContentException('Please Check Input Parameters ');
            // arrange database log
            $this->type = 'APPROVE_RECORD';
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

    /**
     * disapprove
     * Revoke attendance record entry by staff
     * @param $content
     */
    function disapprove($content){

    }
}