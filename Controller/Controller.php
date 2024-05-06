<?php
/**
 * --------------------------------------------------
 * DPASS Restful Service
 * DPASS Rest
 * Controller.php
 * --------------------------------------------------
 * Controller.php redirects all functional program logics to relevant file
 * 
 * 
 * @author Weng Long Pang
 * @copyright KATS 2015
 * @property Valentine Flower Limited, Macao SAR
 * @version 1.0
 * 
 */

require 'RecordController.php';

$app->get('/version','showVersion');

/**
 * showVersion()
 * Display version and commit info of this application.
 * 
 */
function showVersion() {
	$output = array("version" => readfile("version/tag"), "commit" => readfile("ersion/commit"));
	echo json_encode($output);
}