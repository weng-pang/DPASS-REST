<?php
/**
 * DatabaseConfiguration.php
 * 
 * This file provides all essential conntection setting to the datase.
 * 
 * Similar to the configuration file, it is NOT recommended to change any of settings unless it is:
 * - done in a non-production environment, and
 * - clear about the nature of modification
 * 
 */
// Database Settings
define('DB_CONNECTION_STRING', 'mysql:host=localhost;dbname=dpass-lite');
define('DB_USER','dpass-lite');
define('DB_PASSWORD','dpass-lite');
