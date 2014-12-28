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
require 'DatabaseConfiguration.php';
require 'Database.php';

// Naming Settings
define('APP_NAME','DPASS-REST');

// Log Strings
//date_default_timezone_set('Asia/Macau');
$timeNow = new DateTime('now');
define('LOG_HEADER','['.date('D M d H:i:s'.substr((string)microtime(), 1, 7).' Y').'] ['.APP_NAME.'] ');
define('FULL_DATE_FORMAT','Y-m-d H:i:s');

// Bacth Settings
define('BATCH_SIZE',0);

// Record Settings
define('MAXIMUM_ENTRY_ID',4);
define('MAXIMUM_MACHINE_ID',999);
define('MAXIMUM_ID',9999);
define('IP_REGEX','/^\d{1,3}\.\d{1,3}\.\d{1,3}\.\d{1,3}\z/');

// SQL Strings
define('ADD_SINGLE_RECORD','INSERT INTO `records`(`id`, `datetime`, `machineid`, `entryid`, `ipaddress`, `portnumber`, `key`, `update`) VALUES (:id,:datetime,:machineid,:entryid,:ipaddress,:portnumber,:key,:update)');
define('FIND_API_KEY','SELECT * FROM `api_keys` where `key` = :key');
define('FIND_ENTRY_RECORDS','SELECT * FROM `records` WHERE (`id` >= :startid AND `id` <= :endid) AND (`machineid` >= :startmachineid AND `machineid` <= :endmachineid) AND `datetime` >= :starttime AND `datetime` <= :endtime');
define('FIND_SERIAL','SELECT * FROM `records` WHERE `serial` = :serial');
define('ADD_LOG_RECORD','INSERT INTO `log`( `key`, `ip`, `description`, `type`,`time`) VALUES (:key, :ip, :description, :type, :time)');
define('REVOKE_RECORD','UPDATE `records` SET `revoked` = 1,`update`=:update WHERE `serial` = :serial');
define('CHECK_UPDATES','SELECT `machineid`,MAX(`update`) AS "update" FROM `records` WHERE `revoked` = 0 GROUP BY `machineid`');
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
