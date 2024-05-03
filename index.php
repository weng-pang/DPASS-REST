<?php
/**
 * --------------------------------------------------
 * DPASS Restful Service
 * DPASS Rest
 * --------------------------------------------------
 * 
 * 
 * 
 * @author Weng Long Pang
 * @copyright KATS 2015
 * @property Valentine Flower Limited, Macao SAR
 * @version 1.3
 * 
 */
header("Access-Control-Allow-Origin: *");
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

// security check
function apiKeyCheck(){
	global $app;
	global $keyCheck;
	// security check
	$key = $app->request()->params('key');
	$keyCheck->validate($key);

}
// Call controller here
require 'Controller/Controller.php';

$app->notFound('notAvailable');

$app->run();
// procedure for not found methods
function notAvailable(){
	global $app;	
	$app->halt(405,'{"error":{"text":"Method Not Allowed"}}') ;
}
