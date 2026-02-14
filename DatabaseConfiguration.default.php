<?php
/**
 * DatabaseConfiguration.php
 * 
 * This file provides all essential connection setting to the database.
 * 
 * Similar to the configuration file, it is NOT recommended to change any of settings unless it is:
 * - done in a non-production environment, and
 * - clear about the nature of modification
 * 
 * As of version 1.2.0, this file supports reading configuration from environment variables.
 * Environment variables take precedence over hardcoded defaults, allowing for flexible
 * deployment in containerized environments (Docker, Kubernetes, etc.).
 * 
 * Supported environment variables:
 * - DB_HOST: Database host address (default: 127.0.0.1)
 * - DB_NAME: Database name (default: dpass-lite)
 * - DB_USER: Database username (default: dpass-lite)
 * - DB_PASSWORD: Database password (default: dpass-lite)
 */

// Database Settings
// Priority: Environment variables > Hardcoded defaults

// Build connection string from environment variables or use defaults
$dbHost = getenv('DB_HOST') ?: '127.0.0.1';
$dbName = getenv('DB_NAME') ?: 'dpass-lite';
define('DB_CONNECTION_STRING', sprintf('mysql:host=%s;dbname=%s', $dbHost, $dbName));

// Database user
define('DB_USER', getenv('DB_USER') ?: 'dpass-lite');

// Database password
define('DB_PASSWORD', getenv('DB_PASSWORD') ?: 'dpass-lite');
