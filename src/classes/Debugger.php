<?php

class Debugger{
	
	var $debugMode;
	var $configurations;
	var $page;
	var $debugNum;
	
	
	function Debugger(){
		global $configurations, $page;
		$this->configurations	 =& $configurations;
		$this->page			 	 =& $page;
		$this->debugNum			 = 1;
		$this->debugMode		 = $configurations->debugMode;
		
		if($this->debugMode){
			$this->enableErrorReports();
		}
	}
	
	/**
	 * Tämä funktio laittaa kaikki mahdolliset error viestit näkyviin.
	 * Funktio on tehty debuggaustarkoitukseen sivuja siirrettäessa tml-palvelimelta 
	 * otaxille. Normaalikäytössä tätä ei kannata kutsua.
    */
	function enableErrorReports(){
		ini_set('display_errors','1');
		ini_set('display_startup_errors','1');
		error_reporting (E_ALL);
	}
	
	/**
	 * @param debug message
	 * @param function, method, class, page or whatever to specify where 
	 * the debug came from
	 */
	function debug($debugMessage, $invoker){
		// Check parameters
		if(gettype($invoker) != "string" || $invoker == ""){
			$this->error("debug: Set the invoker name");
		}
		
		if($this->configurations->debugMode){
			$this->page->addDebug("<tr><td><b>[".$this->getAndIncreaseNum() . "]</b></td><td><b>$invoker</b></td><td>" . $debugMessage . "</td></tr>");
		}
	}
	
	function debugVar($var, $variableName, $invoker){
		// Check parameters
		if(gettype($variableName) != "string" || $variableName == ""){
			$this->error("debugVar: Set the variable name");
		}
		
		$type = gettype($var);
		
		$value = "";
		if($type == "boolean"){
			if($var == true){
				$value = "true";
			} else {
				$value = "false";
			}
		} else if($type == "integer"){
			$value = "$var";
		} else if($type == "double"){
			$value = "$var";
		} else if($type == "string"){
			$value = "$var";
		} else if($type == "array"){
			foreach($var as $key => $arrayValue){
				$value .= "[$key] $arrayValue";
			}
		} else if($type == "object"){
			$value .= "Class: " . get_class($var) . " Vars: ";
			
			foreach(get_object_vars($var) as $key => $arrayValue){
			 	$value .= "[$key] ". gettype($arrayValue)." ";
			}
		} else if($type == "resource"){
			$value .= "Resource type: " . get_resource_type($var);
		} else if($type == "NULL"){
			$value .= "NULL";
		}
		
		$message = "<b>Variable $variableName</b> type: $type value: $value";
		$this->debug($message, $invoker);
	}
	
	function error($errorMessage, $invoker){
		$error = "";
		if($this->debugMode){
			// Show invoker if in debug mode
			$error .= "<b>$invoker</b>: ";
		}
		$this->page->error($error . $errorMessage);
	}
	
	function getAndIncreaseNum(){
		return $this->debugNum++;
	}
	
	/**
	 * Stop here and print the debugging output
	 */
	function stop(){
		$this->page->printPage();
		die();
	}
	
	function listDefinedPostAndGetVars($invoker){
		global $_POST, $_GET;
		foreach($_POST as $key => $value){
			$this->debug("POST[\"$key\"]: " . $value, "$invoker");
		}
		foreach($_GET as $key => $value){
			$this->debug("GET[\"$key\"]: " . $value, "$invoker");
		}
	}
	
}
 
?>
