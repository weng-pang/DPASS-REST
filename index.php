<?php


require 'configuration.php';

\Slim\Slim::registerAutoloader();

$app = new Slim\Slim(
		array(
				'mode'=>'development',
				'log.enabled'=>true,
				'log.level' => \Slim\Log::DEBUG,
				
		));
$app->response->headers->set('Content-Type', 'application/json');

$keyCheck = new KeyCheck($app);
$record = new Record($app);
// Route information
$app->get('/','notAvailable');

// add record
// for a single entry
$app->post('/add','apiKeyCheck','addRecord');
// for a block of entries
$app->post('/addbatch','apiKeyCheck','addBatchRecord');
// view record - all request must be made in a json message
$app->post('/find','apiKeyCheck','findRecord');

$app->notFound('notAvailable');

$app->run();
// background configuration

// security check
function apiKeyCheck(){
	global $app;
	global $keyCheck;
	// security check
	$key = $app->request()->params('key');
	$keyCheck->validate($key);
	
}
// add a batach of records (specific count records only)
function addBatchRecord(){
	global $app;
	global $record;
	
	$records = json_decode($app->request()->params('content'),true);
	$results = array();
	// check the size of content
	if (sizeof($records) != BATCH_SIZE){
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

// procedure for not found methods
function notAvailable(){
	global $app;
	//$app->response->setStatus(501);
	
	$app->halt(501,'{"error":{"text":"Method Not Available"}}') ;
}