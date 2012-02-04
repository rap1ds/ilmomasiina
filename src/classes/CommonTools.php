<?php

require_once("Configurations.php");
require_once("Page.php");
require_once("Debugger.php");
require_once("Database.php");

/**
 * Class whicincludes common functions used program wide. These tools are 
 * static function, so you can use them without making an instance of the class
 */
class CommonTools{
	
	function initializeCommonObjects(&$object){
		global $configurations, $page, $debugger, $database;
		
		if(CommonTools::property_exists($object, "configurations")){
			$object->configurations		= &$configurations;
		}
		if(CommonTools::property_exists($object, "page")){
			$object->page				= &$page;	
		}
		if(CommonTools::property_exists($object, "debugger")){
			$object->debugger			= &$debugger;
		}
		if(CommonTools::property_exists($object, "database")){
			$object->database			= &$database;
		}
	}
	
	/**
	 * Gets global Page object. Object must be initialized before calling this 
	 * method
	 */
	function getPage(){
		global $page;
		if($page != null){
			return $page;
		} else {
			die("Page object is not set");
		}
	}
	
	/**
	 * Gets global Debugger object. Object must be initialized before calling this 
	 * method. This method is very usefull if you want to debug inside static method, 
	 * because in the static method you can not use $this->debugger...
	 */
	function getDebugger(){
		global $debugger;
		if($debugger != null){
			return $debugger;
		} else {
			die("Debugger object is not set");
		}
	}
	
	/**
	 * Gets global Configurations object. Object must be initialized before calling this 
	 * method
	 */
	function getConfigurations(){
		global $configurations;
		if($configurations != null){
			return $configurations;
		} else {
			die("Database object is not set");
		}
	}
	
	/**
	 * Gets global Database object. Object must be initialized before calling this 
	 * method
	 */
	function getDatabase(){
		global $database;
		if($database != null){
			return $database;
		} else {
			die("Database object is not set");
		}
	}
	
	function GET($GETVariableName){
		if(isset($_GET[$GETVariableName])){
			// Return slashed value. Get values shold always be integers or simple
			// strings without quotes. Adding slashes helps with the sql injection 
			// security issue
			return addslashes($_GET[$GETVariableName]);
		} else {
			return null;
		}
	}
	
	function POST($POSTVariableName){
		// FIXME Check for SQL Injection!
		if(isset($_POST[$POSTVariableName])){
			return $_POST[$POSTVariableName];
		} else {
			return null;
		}
	}
	
	/**
	 * Converts checkboxes value to boolean. If checkbox is selected the 
	 * method return true, otherwise false
	 */
	function checkboxToBoolean($checkboxValue){
		if($checkboxValue != null){
			return true;
		} else {
			return false;
		}
	}
	
	function sqlToBoolean($boolean){
		if($boolean == "1" || $boolean == 1){			
			return true;
		} else {
			return false;
		}
	}
	
	function booleanToSql($boolean){
		if($boolean == true){
			return 1;
		} else if($boolean == false){
			return 0;
		} else {
			return 0;
		}
	}
	
	/**
	 * Converts PHP array to comma separated array format
	 */
	function arrayToSql($array){
		// Check parameters
		if(!is_array($array) || count($array) <= 0){
			// Empty array
			return "{}";
		} else {
			$output = "{";
		
			// Because the double quotes are the delimiter they must be replaced 
			// with html entity
			foreach($array as $value){
				$output .= htmlentities($value) . ",";
			}
			
			// FIXME All the data should be stored to database in entities
			// It shouldn't be done one by one in here!
			
			// Removes the lass comma and empty space
			$output = substr($output, 0, -1);
			$output = $output . "}";
			
			$debugger = CommonTools::getDebugger();
			$debugger->debug("arrayToString: " . $output, "arrayToString");
			return $output;
		}
	}
	
	/**
	 * Converts comma separated array format (used by database) to PHP array
	 * Example 
	 *   {"Value", "Value 2", "Value which inludes so called &quot;double 
	 *   quotes&quot;"} -> array
	 */
	function sqlToArray($string){
		// Checks parameters
		if($string == "" || $string == "{}"){
			// Empty array
			return array();
		} else {
			// Removes braces (aaltosulkeet)
			$tempString = substr($string, 1, -1);
			$array = explode(",", $tempString);
			
			// Remove double quotes if needed
			// SQL adds double quotes around value with two or more words, 
			// so get rid of them and replace entities to normal quotes
			foreach($array as $key => $value){
				// Remove double quotes if needed
				if(substr($value, 0, 1) == '"' && substr($value, -1, 1) == '"'){
					$value = substr($value, 1, -1);
					// There should be no more double quotes...
				}
				
				// Replace html double quote with a normal one
				$value = html_entity_decode($value);
				
				$array[$key] = $value;
			}
			
			return $array;
		}
	}
	
	function timeToSql($timestamp){
		return MDB2_Date::unix2Mdbstamp($timestamp);
	}
	
	/**
	 * Checks if the object has a given property
	 */
	function property_exists($obj, $property){
		$properties = get_object_vars($obj);
		if(array_key_exists($property, $properties)){
			return true;
		} else {
			return false;
		}
	}
	
	/**
	 * Converts array which includes Ids (positive integers) to sql query 
	 * format (which is (1, 2, 3, 5 ,7))
	 */
	function idArrayToSql($array){
		$arrayString = "(";
		$arrayString .= CommonTools::arrayToReadableFormat($array);
		$arrayString .= ")";
		return $arrayString;
	}
	
	function arrayToReadableFormat($array){
		$arrayString = "";
		$atLeastOneValueSet = false;
		foreach($array as $id){
			if($id >= 0){
				$arrayString .= "$id, ";
				$atLeastOneValueSet = true;
			} else {
				$debugger = CommonTools::getDebugger();
				$debugger->error("The array must include only positive integer values", "idArrayToSql");
			}
		}
		
		if($atLeastOneValueSet){
			// Remove last comma
			$arrayString = substr($arrayString, 0, -2);
		}
		return $arrayString;
	}
	
	function newlineToBr($string){
		return str_replace("\n", "<br />", $string);
	}
	
	/**
	 * Generates random password
	 * http://www.laughing-buddha.net/jon/php/password/
	 */
	function generatePassword ($length = 8){
		// start with a blank password
  		$password = "";

  		// define possible characters
  		$possible = "0123456789bcdfghjkmnpqrstvwxyzBCDFGHJKMNPQRSTVWYX"; 
    
  		// set up a counter
  		$i = 0; 
    
  		// add random characters to $password until $length is reached
  		while ($i < $length) { 

    		// pick a random character from the possible ones
    		$char = substr($possible, mt_rand(0, strlen($possible)-1), 1);
        
    		// we don't want this character if it's already in the password
    		if (!strstr($password, $char)) { 
      			$password .= $char;
      			$i++;
    		}
		}

		// done!
		return $password;
	}
}
 
?>
