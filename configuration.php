<?php
/**
 * configuration.php
 * Critical Configuration Area
 * 
 * All of essential operaetion files, and settings, must be stored here.
 * 
 * Do NOT change any settings, unless it is:
 * - done in a non-production environment, and
 * - clear about the nature of modification
 * 
 */
// Operation Files
require 'Slim/Slim.php';
require 'Model/Model.php';
require 'Model/KeyCheck.php';
require 'Model/Record.php';
require 'database.php';

// Naming Settings
define('APP_NAME','DPASS-REST');

// Log Strings
$timeNow = new DateTime('now');
define('LOG_HEADER','['.date('D M d H:i:s'.substr((string)microtime(), 1, 7).' Y').'] ['.APP_NAME.'] ');

// Database Settings
define('DB_CONNECTION_STRING', 'mysql:host=localhost;dbname=dpass-lite');
define('DB_USER','dpass-lite');
define('DB_PASSWORD','dpass-lite');

// Bacth Settings
define('BATCH_SIZE',100);

// Record Settings
define('MAXIMUM_ENTRY_ID',4);
define('MAXIMUM_MACHINE_ID',999);
define('MAXIMUM_ID',9999);
define('IP_REGEX','/^\d{1,3}\.\d{1,3}\.\d{1,3}\.\d{1,3}\z/');

// SQL Strings
define('ADD_SINGLE_RECORD','INSERT INTO `records`(`id`, `datetime`, `machineid`, `entryid`, `ipaddress`, `portnumber`, `key`) VALUES (:id,:datetime,:machineid,:entryid,:ipaddress,:portnumber,:key)');
define('FIND_API_KEY','SELECT * FROM `api_keys` where `key` = :key');
define('FIND_ENTRY_RECORDS','SELECT * FROM `records` WHERE (`id` >= :startid AND `id` <= :endid) AND (`machineid` >= :startmachineid AND `machineid` <= :endmachineid) AND `datetime` >= :starttime AND `datetime` <= :endtime');
define('ADD_LOG_RECORD','INSERT INTO `log`( `key`, `ip`, `description`, `type`) VALUES (:key, :ip, :description, :type)');
define('OBTAIN_SETTING','SELECT * FROM `configurations`');

// Find Parameters
define('MAXIMUM_PARAMETER',4);
define('MINIMUM_REQUIRE',1);
define('ABSOLUTE_MINIMUM',1);
define('ABSOLUTE_MAXIMUM',9999999999);

// ERROR 
define('GENERIC_ERROR',500);
define('BAD_REQUEST',400);
define('NOT_AUTHORISED',403);
