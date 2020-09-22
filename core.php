<?php
// core.php - El fitxer més elemental, estimat Watson

// Get settings
require(__DIR__.'/config.php');

// Set composer
require(__DIR__.'/vendor/autoload.php');

// Set timezone and locale accordingly
date_default_timezone_set('Europe/Madrid');

// Connect to the DB
$con = new PDO('mysql:host='.$conf['db']['host'].';dbname='.$conf['db']['database'].';charset=utf8mb4', $conf['db']['user'], $conf['db']['password']);

// Session settings
session_set_cookie_params([
  'lifetime' => 0,
  'path' => ($conf['path'] ?? '/'),
  'httponly' => true
]);
session_start();
