<?php
/**
 * --------------------------------------------------
 * DPASS Restful Service
 * DPASS Rest
 * Record.php
 * --------------------------------------------------
 * Record.php handles logics in relation to records
 *
 *
 * @author Weng Long Pang
 * @copyright KATS 2015
 * @property Valentine Flower Limited, Macao SAR
 * @version 1.0
 *
 */
// Route Table
// add record
// for a single entry
$app->post('/add','apiKeyCheck','addRecord');
// for a block of entries
$app->post('/addbatch','apiKeyCheck','addBatchRecord');
// view record - all request must be made in a json message
$app->post('/find','apiKeyCheck','findRecord');
// revoke ('delete') a record
$app->post('/revoke','apiKeyCheck','revokeRecord');
// check for update records
$app->post('/check','apiKeyCheck','checkUpdates');
// check for staff latest record
$app->post('/check_profile','apiKeyCheck','checkProfileUpdate');
// approve a record
$app->post('/approve','apiKeyCheck','approveRecord');
// revoke an approval
$app->post('/disapprove','apiKeyCheck','disapproveRecord');

// background configuration


// add a batach of records (specific count records only)
function addBatchRecord(){
	global $app;
	global $record;

	$records = json_decode($app->request()->params('content'),true);
	$results = array();
	// check the size of content
	if (sizeof($records) != BATCH_SIZE && BATCH_SIZE){
		$app->halt(BAD_REQUEST,'{"error":{"procedure":"add batch records","text":"Specific Batch Size Required. COUNT:'.sizeof($records).'"}}');
	}
	$record->getDatabaseConnection()->getConnection()->beginTransaction();
	foreach($records as $content){
		//var_dump($content);
		$results[] = $record->add($content);

	}
	$record->getDatabaseConnection()->getConnection()->commit();
	echo json_encode($results);

}

// add a single record
function addRecord(){
	global $app;
	global $record;

	$request = $app->request();
	$content = json_decode($request->params('content'),true);
	$record->getDatabaseConnection()->getConnection()->beginTransaction();
	echo json_encode($record->add($content));
	$record->getDatabaseConnection()->getConnection()->commit();
}

// find record according to given search parameters.
function findRecord(){
	global $app;
	global $record;

	$request = $app->request();
	$content = json_decode($request->params('content'),true);
	//var_dump($content);
	$record->getDatabaseConnection()->getConnection()->beginTransaction();
	echo json_encode($record->find($content));
	$record->getDatabaseConnection()->getConnection()->commit();
}
/**
 * Revoke a record
 *
 *
 */
function revokeRecord(){
	global $app;
	global $record;

	$request = $app->request();
	$content = json_decode(($request->params('content')),true);
	$record->getDatabaseConnection()->getConnection()->beginTransaction();
	echo json_encode($record->revoke($content));
	$record->getDatabaseConnection()->getConnection()->commit();
}

/**
 * Check updates
 *
 * This method gives the latest date of each machine id
 */
function checkUpdates(){
	global $app;
	global $record;

	$request = $app->request();
	//$content = json_decode(($request->params('content')),true);
	$record->getDatabaseConnection()->getConnection()->beginTransaction();
	echo json_encode($record->checkUpdates());
	$record->getDatabaseConnection()->getConnection()->commit();
}

/**
 * Look for latest staff entry (delayed)
 * This looks for the last entry of each staff, however it is subject to machine udpates.
 *
 */
function checkProfileUpdate(){
	global $app;
	global $record;

	$request = $app -> request();
	$record->getDatabaseConnection()->getConnection()->beginTransaction();
	echo json_encode($record -> checkRecords(LATEST_STAFF_ENTRIES));
	$record->getDatabaseConnection()->getConnection()->commit();
}

/**
 * Check latest machine report status
 *
 *
 */
function checkComputerReports(){
	global $app;
	global $record;

	$request = $app -> request();
	$record->getDatabaseConnection()->getConnection()->beginTransaction();
	echo json_encode($record -> checkRecords(COMPUTER_REPORTS));
	$record->getDatabaseConnection()->getConnection()->commit();
}


function approveRecord(){

}

function disapproveRecord(){

}