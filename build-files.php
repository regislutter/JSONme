<?php

/** Error reporting */
error_reporting(E_ALL);
ini_set('display_errors', TRUE);
ini_set('display_startup_errors', TRUE);
date_default_timezone_set('America/Montreal');

define('EOL',(PHP_SAPI == 'cli') ? PHP_EOL : '<br />');

require_once './vendor/PHPExcel/PHPExcel.php';
require_once 'libs/import-files.php';

/**
 * Import Files
 */
$import = new ImportFiles();
$import->init();
$import->parseExcel();
$import->buildJson();


