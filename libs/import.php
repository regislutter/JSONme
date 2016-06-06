<?php

class Import
{
	//TODO CHANGE FILE NAME AND max
	public $excelName 		= "documents/import.xlsx";
	public $jsonName		= "json/import.json";
	public $sheet 			= 0;
	public $max 			= 0;
	public $structure       = null;
	public $objPHPExcel 	= null;
	public $workSheet 		= null;
	public $dataXls			= null;
	public $json			= null;

	public $aValues 		= array();
	public $bValues 		= array();
	public $dValues 		= array();
	public $eValues 		= array();
	public $fValues 		= array();
	public $gValues 		= array();
	public $hValues 		= array();
	public $jValues 		= array();
	public $kValues 		= array();
	public $lValues 		= array();
	public $categories 		= array();
	public $map 			= array();
	public $questions 		= array();
	public $files			= array();
	public $solutions 		= array('INVIVA' 	=> array(),
									'Coverdale' 	=> array(),
									'Elizabeth' 	=> array());

	public $currentAValue 	= null;
	public $currentBValue 	= null;
	public $currentDValue 	= null;
	public $currentEValue 	= null;
	public $currentFValue 	= null;
	public $currentGValue 	= null;
	public $currentHValue 	= null;
	public $currentJValue 	= null;
	public $currentKValue 	= null;
	public $currentLValue 	= null;

	public function init() {
//		echo "Starting export...<br/>";
		try{
//			$reader = new PHPExcel_Reader_Excel5();
//			if(!$reader->canRead('./'.$this->excelName)){
//				echo('Can\'t read file!<br/>');
//			}
			$this->objPHPExcel 	= PHPExcel_IOFactory::load('./'.$this->excelName);
			$this->objPHPExcel->setActiveSheetIndex($this->sheet);
			$this->workSheet 	= $this->objPHPExcel->getActiveSheet($this->sheet);
		}catch(Exception $ex){
			throw $ex;
		}
    }

	public function setExcelName($fileName){
		$this->excelName = $fileName;
	}

	public function setJsonName($fileName){
		$this->jsonName = "json/".$fileName;
	}

    public function setSheet($sheet){
    	$this->sheet = $sheet;
    }

    public function setMax($max){
    	$this->max = $max;
    }

    public function getName(){
    	return $this->name;
    }

    public function setName($name){
    	$this->name = $name;
    }

	public function buildStructure($json){
		$this->structure = new stdClass();
		$this->structureLevel($this->structure, $json);
	}
	public function structureLevel(&$str, $json){
		foreach($json as $item){
			$label = $item->text;

			if(isset($item->children) && !empty($item->children)){ // If the item has children
				$obj = new stdClass();
				if(isset($item->id)){ // If the item has a column
					$obj->label = $item->id;
					if($item->state->selected == true){ // If the item is unique
						$obj->id = $item->id;
					}
				}
				$this->structureLevel($obj, $item->children); // Loop on item's children

				$str->$label = $obj;
			}else if(isset($item->id)){
				$str->$label = $item->id;
			}
		}
	}

	/**
	 * Function to parse Excel files with vertical template
	 * @param int $firstRow First row to parse
	 */
	public function parseVerticalExcel($firstRow = 2){
		if(!isset($this->dataXls)){
			$this->dataXls = [];
		}
		for($row = $firstRow; $row <= $this->max; $row++){
			$this->forLevel($this->dataXls, $row, $this->structure, true);
		}
//		var_dump(json_encode($this->dataXls));
	}

	/**
	 * Parse each row with the structure
	 * @param $parent Data generated and pointer to parent element
	 * @param $row Row of the file to parse
	 * @param $structure Structure from this level of the tree to extract from the JSON
	 */
	public function forLevel(&$parent, $row, $structure, $firstLevel){
		$el = new stdClass();

		if($firstLevel){
			foreach($structure as $key => $val) { // For each variable
				$alreadyTop = false;
				if($val instanceof stdClass){
					foreach($parent as $item){
						if(isset($item->$key)){
							$el = $item;
							$alreadyTop = true;
							continue;
						}
					}

					if(!isset($el->$key)){
						$el->$key = isset($val->id)?[]:new stdClass(); // create array or object to contain data
					}
					$this->forLevel($el->$key, $row, $val, false); // and parse it's own variables
				}else if(is_string($val) && strlen($val) == 1){ // If it's a variable with a column name
					if($this->workSheet !== null){
						$value = $this->workSheet->getCell($val.$row)->getCalculatedValue();
						$el->$key = $value; // put the value in the current element
					}
				}
			}
			if(!$alreadyTop) {
				array_push($parent, $el); // Insert element in group
			}
			return;
		}

		$alreadyExist = false;
		if(is_array($parent)){
			foreach($parent as $item){
				// If the element with this id already exist
				if(isset($item->id) && $item->id ==  $this->workSheet->getCell($structure->id.$row)->getCalculatedValue()){
					$alreadyExist = true;
					$el = $item; // Existing item with them ID
				}
			}
		}

		foreach($structure as $key => $val) { // For each variable
			if($val instanceof stdClass){
				if(!isset($el->$key)){
					$el->$key = isset($val->id)?[]:new stdClass(); // create array or object to contain data
				}
				$this->forLevel($el->$key, $row, $val, false); // and parse it's own variables
			}else if(is_string($val) && strlen($val) == 1){ // If it's a variable with a column name
				if($this->workSheet !== null){
					$value = $this->workSheet->getCell($val.$row)->getCalculatedValue();
					$el->$key = $value; // put the value in the current element
				}
			}
		}

		if(is_array($parent) && !$alreadyExist){
			array_push($parent, $el); // Insert element in group
		}
	}

	public function exportJson($geocoding = false){
		if(!empty($geocoding)){
			$api_key = 'AIzaSyAD8b-WDnJUoSX4sBO0BjpTI_zqC2KC1qY';

			foreach($this->dataXls as $data){
				foreach($data as $province){
					$provinceId = $province->id;
					foreach($province->cities as $city){
						$cityLabel = $city->label;
						foreach($city->locations as $location){
							$address = '';
							if(!empty($location->street)){
								$address .= $location->street;
							}
							if(!empty($cityLabel)){
								if(!empty($address)){ $address .= ',+'; }
								$address .= $cityLabel;
							}
							if(!empty($location->postal_code)){
								if(!empty($address)){ $address .= '+'; }
								$address .= $location->postal_code;
							}
							if(!empty($provinceId)){
								if(!empty($address)){ $address .= ',+'; }
								$address .= $provinceId;
							}
							$address = str_replace(' ', '+', $address);

							$json = file_get_contents("https://maps.googleapis.com/maps/api/geocode/json?address=$address&key=$api_key");
							$data = json_decode($json);
							$location->lat = $data->results[0]->geometry->location->lat;
							$location->lng = $data->results[0]->geometry->location->lng;
						}
					}
				}
			}
		}

		$this->json = json_encode($this->dataXls);
		$file = fopen($_SERVER['DOCUMENT_ROOT'].'/'.$this->jsonName, "w");
		fwrite($file, $this->json);
		echo("<br/>Finished export, check the file: <strong>".$this->jsonName."</strong>");
	}

    // déclaration des méthodes
    public function parseExcel(){
		echo "Building all categories\r\n";
		for ($row = 2; $row <= $this->max; ++$row) {
			$aVal = $this->workSheet->getCell("A$row")->getCalculatedValue();
			$bVal = $this->workSheet->getCell("B$row")->getCalculatedValue();
			$cVal = $this->workSheet->getCell("C$row")->getCalculatedValue();
			$dVal = $this->workSheet->getCell("D$row")->getCalculatedValue();
			$eVal = $this->workSheet->getCell("E$row")->getCalculatedValue();
			$fVal = $this->workSheet->getCell("F$row")->getCalculatedValue();
			$gVal = $this->workSheet->getCell("G$row")->getCalculatedValue();
			$hVal = $this->workSheet->getCell("H$row")->getCalculatedValue();
			$jVal = $this->workSheet->getCell("J$row")->getCalculatedValue();
			$kVal = $this->workSheet->getCell("K$row")->getCalculatedValue();
			$lVal = $this->workSheet->getCell("L$row")->getCalculatedValue();

			//var_dump($this->workSheet->getCell("K$row"));exit;

			//A
			if(!empty($aVal)) {
				$this->currentAValue = $aVal;
				$this->aValues[] = $this->currentAValue;
				if(!array_key_exists($this->currentAValue, $this->categories)) {
					$this->categories[$this->currentAValue] = array();
					$this->categories[$this->currentAValue]['documents'] = array();
				}
			}


			//B
			if(!empty($bVal)) {
				$this->currentBValue = $bVal;
				$this->bValues[] = $this->currentBValue;
				if(!array_key_exists($this->currentBValue, $this->categories[$this->currentAValue])) {
					$this->categories[$this->currentAValue][$this->currentBValue] = array();
					$this->categories[$this->currentAValue][$this->currentBValue]['documents'] = array();
				}
			}

			//C - Questions
			if(!empty($cVal) && !in_array($cVal, $this->questions)) {
				array_push($this->questions, $cVal);
			}

			//D
			if(!empty($dVal)) {
				$this->currentDValue = $dVal;
				$this->dValues[] = $this->currentDValue;
				if(!array_key_exists($this->currentDValue, $this->categories[$this->currentAValue][$this->currentBValue])) {
					$this->categories[$this->currentAValue][$this->currentBValue][$this->currentDValue] = array();
					$this->categories[$this->currentAValue][$this->currentBValue][$this->currentDValue]['documents'] = array();
				}
			}


			//E
			if(!empty($eVal)) {
				$this->currentEValue = $eVal;
				$this->eValues[] = $this->currentEValue;
				if(!array_key_exists($this->currentEValue, $this->categories[$this->currentAValue][$this->currentBValue][$this->currentDValue])) {
					$this->categories[$this->currentAValue][$this->currentBValue][$this->currentDValue][$this->currentEValue] = array();
					$this->categories[$this->currentAValue][$this->currentBValue][$this->currentDValue][$this->currentEValue]['documents'] = array();
				}
			}

			//F
			if(!empty($fVal)) {
				$this->currentFValue = $fVal;
				$this->fValues[] = $this->currentFValue;
				if(!array_key_exists($this->currentFValue, $this->categories[$this->currentAValue][$this->currentBValue][$this->currentDValue][$this->currentEValue])) {
					$this->categories[$this->currentAValue][$this->currentBValue][$this->currentDValue][$this->currentEValue][$this->currentFValue] = array();
					$this->categories[$this->currentAValue][$this->currentBValue][$this->currentDValue][$this->currentEValue][$this->currentFValue]['documents'] = array();
				}
			}

			//echo "line ". $row."- E - {$this->currentEValue} - F - {$this->currentFValue}\r\n";

			//g
			if(!empty($gVal)) {
				$this->currentGValue = $gVal;
				$this->gValues[] = $this->currentGValue;
				if(!array_key_exists($this->currentGValue, $this->categories[$this->currentAValue][$this->currentBValue][$this->currentDValue][$this->currentEValue][$this->currentFValue])) {
					$this->categories[$this->currentAValue][$this->currentBValue][$this->currentDValue][$this->currentEValue][$this->currentFValue][$this->currentGValue] = array();
					$this->categories[$this->currentAValue][$this->currentBValue][$this->currentDValue][$this->currentEValue][$this->currentFValue][$this->currentGValue]['documents'] = array();
				}
			}

			//H
			if(!empty($hVal)) {
				$this->currentHValue = $hVal;
				$this->hValues[] = $this->currentHValue;
				if(!array_key_exists($this->currentHValue, $this->categories[$this->currentAValue][$this->currentBValue][$this->currentDValue][$this->currentEValue][$this->currentFValue][$this->currentGValue])) {
					$this->categories[$this->currentAValue][$this->currentBValue][$this->currentDValue][$this->currentEValue][$this->currentFValue][$this->currentGValue][$this->currentHValue] = array();
					$this->categories[$this->currentAValue][$this->currentBValue][$this->currentDValue][$this->currentEValue][$this->currentFValue][$this->currentGValue][$this->currentHValue]['documents'] = array();
				}
			}

			//J
			if(!empty($jVal)) {
				$this->currentJValue = $jVal;
				$this->jValues[] = $this->currentJValue;
				if(!array_key_exists($this->currentJValue, $this->categories[$this->currentAValue][$this->currentBValue][$this->currentDValue][$this->currentEValue][$this->currentFValue][$this->currentGValue][$this->currentHValue])) {
					$this->categories[$this->currentAValue][$this->currentBValue][$this->currentDValue][$this->currentEValue][$this->currentFValue][$this->currentGValue][$this->currentHValue][$this->currentJValue] = array();
					$this->categories[$this->currentAValue][$this->currentBValue][$this->currentDValue][$this->currentEValue][$this->currentFValue][$this->currentGValue][$this->currentHValue][$this->currentJValue]['documents'] = array();
				}
			}

			//K
			if(!empty($kVal)) {
				$this->currentKValue = $kVal;
			}

			//L
			if(!empty($lVal)) {
				$this->currentLValue = $lVal;
			}

			//solution
			if(!empty($jVal)) {
				$this->currentJValue = $jVal;
				$this->jValues[] = $this->currentJValue;
				if(!array_key_exists($this->currentJValue, $this->solutions[$this->getName()])) {
					$this->solutions[$this->getName()][$this->currentJValue] = array();
					$this->solutions[$this->getName()][$this->currentJValue]['primer'] = null;
					$this->solutions[$this->getName()][$this->currentJValue]['tools'] = null;
				}

				if($this->solutions[$this->getName()][$this->currentJValue]['primer'] == null) {
					$this->solutions[$this->getName()][$this->currentJValue]['primer'] = trim($this->currentKValue);
				}

				if($this->solutions[$this->getName()][$this->currentJValue]['tools'] == null) {
					$tmp = explode(',', $this->currentLValue);
					$tmp = array_map('trim', $tmp);
					$this->solutions[$this->getName()][$this->currentJValue]['tools'] = $tmp;
				}
			}

			//echo $row. ": " . $this->currentJValue." ===== ". $this->currentKValue  ."\r\n";

			//Populate documents
			if (!empty($lVal)) {
				$files = $this->getTools($this->currentLValue);

		    	// UNCOMMENT THIS WHEN TRY TO DEBUG VLOOKUP
		    	// if ($this->currentLValue=="#N/A") {
		    	// 	var_dump($row);
		    	// 	var_dump($this->currentLValue);
		    	// }

				foreach ($files as $value) {
					if (!in_array($value, $this->files)) {
						//TODO decide what's in here
						array_push($this->files, $value);
					}
				}

				$this->populateDocuments($lVal, $this->categories[$this->currentAValue]['documents']);
				$this->populateDocuments($lVal, $this->categories[$this->currentAValue][$this->currentBValue]['documents']);
				$this->populateDocuments($lVal, $this->categories[$this->currentAValue][$this->currentBValue][$this->currentDValue]['documents']);
				$this->populateDocuments($lVal, $this->categories[$this->currentAValue][$this->currentBValue][$this->currentDValue][$this->currentEValue]['documents']);
				$this->populateDocuments($lVal, $this->categories[$this->currentAValue][$this->currentBValue][$this->currentDValue][$this->currentEValue][$this->currentFValue]['documents']);
				$this->populateDocuments($lVal, $this->categories[$this->currentAValue][$this->currentBValue][$this->currentDValue][$this->currentEValue][$this->currentFValue][$this->currentGValue]['documents']);
				$this->populateDocuments($lVal, $this->categories[$this->currentAValue][$this->currentBValue][$this->currentDValue][$this->currentEValue][$this->currentFValue][$this->currentGValue][$this->currentHValue]['documents']);
				$this->populateDocuments($lVal, $this->categories[$this->currentAValue][$this->currentBValue][$this->currentDValue][$this->currentEValue][$this->currentFValue][$this->currentGValue][$this->currentHValue][$this->currentJValue]['documents']);
			}

		}
    }

    public function buildJson() {
    	$std = new stdClass();
		$std->categories 	= $this->categories;
		$std->map 			= array();
		$std->questions 	= $this->questions;
		$std->solutions     = $this->solutions;
		$std->files     	= $this->files;

		//adding mapping
		foreach($this->map as $name => $array) {
			$std->map[$name] = $array;
		}

		//var_dump($std->solutions);

		$file = fopen("../www/data/categories.json", "w");
		fwrite($file, json_encode($std));
    }

    public function getTools($toolsString) {
    	$tools = array();
    	if (!empty($toolsString)) {
    		$tools = explode(',', $toolsString);
	    	foreach($tools as $key => $value){
	    		if (trim($value)!=''){
	    			$tools[$key] = trim($value);
	    		}
	    	}
    	}
    	return $tools;
    }

    public function populateDocuments($toolsString, &$where) {
    	$documents = $this->getTools($toolsString);
		foreach ($documents as $value) {
			if (!in_array($value, $where)) {
				array_push($where, $value);
			}
		}
    }
}