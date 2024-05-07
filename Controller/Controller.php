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
	$tag_file = fopen("versions/tag", "r");
	$commit_file = fopen("versions/commit", "r");
	$output = array("version" => fgets($tag_file), "commit" => fgets($commit_file));
	echo json_encode($output);
	fclose($tag_file);
	fclose($commit_file);
}