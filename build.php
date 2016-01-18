<?php

/** Error reporting */
error_reporting(E_ALL);
ini_set('display_errors', TRUE);
ini_set('display_startup_errors', TRUE);
ini_set("memory_limit","512M");
date_default_timezone_set('America/Montreal');

define('EOL',(PHP_SAPI == 'cli') ? PHP_EOL : '<br />');

require_once './vendor/PHPExcel/PHPExcel.php';
require_once 'libs/import.php';

$excels = array(
	'INVIVA'	=> array('sheet' => 0,
					  	 'max'	 => 80),
	'Coverdale'	=> array('sheet' => 1,
					  	 'max'	 => 69),
	'Elizabeth'	=> array('sheet' => 2,
					  	 'max'	 => 46),
);

/**
 * Import It
 */
$import = new Import();

$import->setExcelName("MasterClinicList.xlsx");
$import->setSheet(0);
$import->setMax(80);
$import->init();
$structure = new stdClass();
$structure->provinces = new stdClass();
$structure->provinces->id = "E";
$structure->provinces->cities = new stdClass();
$structure->provinces->cities->id = "D";
$structure->provinces->cities->label = "D";
$structure->provinces->cities->locations = new stdClass();
$structure->provinces->cities->locations->label = "A";
$structure->provinces->cities->locations->street = "B";
$structure->provinces->cities->locations->app = "C";
$structure->provinces->cities->locations->city = "F";

$import->parseVerticalExcel($structure, 4);

$import->setSheet(1);
$import->setMax(69);
$import->init();

$structure = new stdClass();
$structure->provinces = new stdClass();
$structure->provinces->id = "F";
$structure->provinces->cities = new stdClass();
$structure->provinces->cities->id = "E";
$structure->provinces->cities->label = "E";
$structure->provinces->cities->locations = new stdClass();
$structure->provinces->cities->locations->label = "A";
$structure->provinces->cities->locations->street = "C";
$structure->provinces->cities->locations->app = "D";
$structure->provinces->cities->locations->city = "H";

$import->parseVerticalExcel($structure, 4);

//$import->setSheet(2);
//$import->setMax(14);
//$import->init();
// 16 - 25
// 27 - 36
// 38 - 46

$import->setJsonName("clinic.json");
$import->exportJson();


