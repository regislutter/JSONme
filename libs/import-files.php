<?php

class ImportFiles
{
	//TODO CHANGE FILE NAME AND HighestRow
	public $excelName 		= "documents/files.xlsx";
	public $highestRow 		= 3;
	public $objPHPExcel 	= null;
	public $workSheet 		= null;
	public $filesArray      = array();

	function init() {
		echo "Initilizing\r\n";
		$this->objPHPExcel = PHPExcel_IOFactory::load($this->excelName);
		$this->workSheet = $this->objPHPExcel->getActiveSheet(0);
    }

    // déclaration des méthodes
    public function parseExcel(){
		echo "building all categories\r\n";
		for ($row = 2; $row <= $this->highestRow; ++$row) {
			$aVal = $this->workSheet->getCell("A$row")->getCalculatedValue();
			//$bVal = $this->workSheet->getCell("B$row")->getCalculatedValue();
			$this->filesArray[] = $aVal;
		}
    }

    public function buildJson() {
		$file = fopen("../www/data/files.json", "w");
		fwrite($file, json_encode($this->filesArray));
    }
}