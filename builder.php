<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>JSONme</title>

    <link rel="stylesheet" href="assets/js/jstree/themes/default/style.min.css" />
    <link rel="stylesheet" href="assets/css/main.css" />

</head>
<body>
    <div class="container">
        <div class="logo no-svg"></div>
        <h1>Build your JSON structure</h1>
        <p class="instructions">
            - Drag and drop the components to create your JSON structure.<br/>
            - Click on the label icon to lock it and mark it as unique. A unique component will be used as an ID and regroup the children components in an array.
        </p>
    <?php
        /** Error reporting */
        error_reporting(E_ALL);
        ini_set('display_errors', TRUE);
        ini_set('display_startup_errors', TRUE);
        ini_set("memory_limit","512M");
        date_default_timezone_set('America/Montreal');
        define('EOL',(PHP_SAPI == 'cli') ? PHP_EOL : '<br />');
        require_once './vendor/PHPExcel/PHPExcel.php';

        if(isset($_FILES['excel'])){
            $upload_dir = 'documents/tmp/';
            $fileName = basename($_FILES['excel']['name']);
            $uploadfile = $upload_dir.$fileName;
            $message =  "Error uploading the file";

            switch( $_FILES['excel']['error'] ) {
                case UPLOAD_ERR_OK:
                    $message = false;
                    break;
                case UPLOAD_ERR_INI_SIZE:
                case UPLOAD_ERR_FORM_SIZE:
                    $max_size = -1;

                    if ($max_size < 0) {
                        // Start with post_max_size.
                        $max_size = ini_get('post_max_size');

                        // If upload_max_size is less, then reduce. Except if upload_max_size is
                        // zero, which indicates no limit.
                        $upload_max = ini_get('upload_max_filesize');
                        if ($upload_max > 0 && $upload_max < $max_size) {
                            $max_size = $upload_max;
                        }
                    }

                    if($_FILES['excel']['error'] == UPLOAD_ERR_FORM_SIZE){
                        $message .= ' - form limit';
                    }

                    $message .= ' - file too large (limit of '.$max_size.' bytes).';
                    break;
                case UPLOAD_ERR_PARTIAL:
                    $message .= ' - file upload was not completed.';
                    break;
                case UPLOAD_ERR_NO_FILE:
                    $message .= ' - zero-length file uploaded.';
                    break;
                default:
                    $message .= ' - internal error #'.$_FILES['excel']['error'];
                    break;
            }

            if( !$message ) {
                if( !is_uploaded_file($_FILES['excel']['tmp_name']) ) {
                    $message = 'Error uploading file - unknown error.';
                } else {
                    if( !move_uploaded_file($_FILES['excel']['tmp_name'], $uploadfile) ) { // No error supporession so we can see the underlying error.
                        $message = 'Error uploading file - could not save upload (this will probably be a permissions problem in '.$uploadfile.')';
                    } else {
                        $message = 'File uploaded okay.';
                    }
                }
            }

            echo $message;

            // Get Excel file
            $objPHPExcel = PHPExcel_IOFactory::load($upload_dir.$fileName);

            // Create a tree for each Sheet
            $scriptTree = '';
            $nbSheets = $objPHPExcel->getSheetCount();
            for($i = 0; $i < $nbSheets; $i++){
                $objPHPExcel->setActiveSheetIndex($i);
                $worksheet = $objPHPExcel->getActiveSheet();
                $rows = $worksheet->getHighestRow();
                echo('<h2>Sheet '.$i.'</h2><div id="xlstree'.$i.'"></div>');
                // Form to convert this Sheet
                echo('<form id="formJson'.$i.'" method="post" action="jsoner.php">');//jsoner.php
                echo('<input type="hidden" id="json'.$i.'" name="json" value="" />');
                echo('<input type="hidden" name="xls_file" value="'.$upload_dir.$fileName.'" />');
                echo('<input type="hidden" name="sheet" value="'.$i.'" />');
                echo('<label for="line_start">First row to parse in the Sheet: </label>');
                echo('<input type="number" name="line_start" value="1" /><br/>');
                echo('<input type="hidden" name="line_max" value="'.($rows-1).'" />');
                echo('<input id="sheet_'.$i.'" type="submit" value="Convert this sheet" />');
                echo('</form><hr/>');

                // JavaScript generator
                $scriptTree .= '$(\'#xlstree'.$i.'\').jstree({ \'core\' : { "check_callback" : true, \'data\' : [';

                $row = 3;
                $lastColumn = $worksheet->getHighestColumn();
                $lastColumn++;
                $headerList = '';
                for ($column = 'A'; $column != $lastColumn; $column++) {
                    $cell = $worksheet->getCell($column.$row);

                    $d = $cell->getCalculatedValue();
                    if($d != ""){
                        if($headerList != ''){
                            $headerList .= ',';
                        }
                        $headerList .= '{ "id": "'.$column.'",';
                        $headerList .= ' "text": "'.$d.'" }';
                    }

                }
                $scriptTree .= $headerList;

                $scriptTree .= '] }, "checkbox": { "three_state": false }, "contextmenu": { "select_node": false }, "plugins" : [ "contextmenu", "dnd", "state", "checkbox", "wholerow" ] });'."\n";
                $scriptTree .= "$('#sheet_$i').on('click', function(e){ e.preventDefault(); $('#json$i').val(JSON.stringify($.jstree.reference('#xlstree$i').get_json())); $('#formJson$i').submit(); });\n";
            }
        }
    ?>
    </div>

    <script src="assets/js/libs/jquery.js"></script>
    <script src="assets/js/jstree/jstree.min.js"></script>
    <script type="text/javascript">
        $(document).ready(function(){
            $(function () {
                <?php echo($scriptTree); ?>
            });
        });
    </script>
</body>
</html>