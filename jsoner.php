<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>JSONme</title>

    <link rel="stylesheet" href="assets/css/main.css" />
</head>
<body>

    <?php

    error_reporting(E_ALL);
    ini_set('display_errors', TRUE);
    ini_set('display_startup_errors', TRUE);
    ini_set("memory_limit","512M");
    date_default_timezone_set('America/Montreal');

    define('EOL',(PHP_SAPI == 'cli') ? PHP_EOL : '<br />');

    require_once './vendor/PHPExcel/PHPExcel.php';
    require_once 'libs/import.php';

    if(isset($_POST['json'])){
        $jsonStr = json_decode($_POST['json']);

        $import = new Import();

        $import->buildStructure($jsonStr);
        $import->setExcelName($_POST['xls_file']);
        $import->setSheet($_POST['sheet']);
        $import->setMax($_POST['line_max']);

        try{
            $import->init();

            $import->parseVerticalExcel($_POST['line_start']);
            $import->setJsonName("output.json");
            $import->exportJson(isset($_POST['geolocation']) && $_POST['geolocation'] == 'true');
        }catch(Exception $ex){
            echo($ex->getMessage());
        }
    }

    ?>
</body>
</html>