<?php

require_once("CommonTools.php");

// Let's do a little hack. We need the configuration information now, because 
// we must get the DBinterface with the help of rootDir. So, we must 
// create configuration method in this point
$configurations = new Configurations();

// Connection $conn is made in DBInterface.php
require_once("../DBInterface.php");

// To the ageLessThan function
define("TIMEUNIT_DAYS", "days");
define("TIMEUNIT_MINUTES", "minutes");

// Warnings disabled
@MDB2::loadClass("MDB2_Driver_Datatype_Common", false);
@MDB2::loadClass("MDB2_Date", false);
@$MDB2Datatype = new MDB2_Driver_Datatype_Common();
$conn->loadModule('Datatype');


class Database{
	
	var $configurations;
	var $page;
	var $debugger;
	var $connection;
	
	function Database(){
		
		global $configurations, $page, $debugger;
		$this->configurations	 =& $configurations;
		$this->page			 	 	 =& $page;
		$this->debugger			 =& $debugger;
		
		/*
		Tämä kaikki on aika turhaa, kun otaxille siirryttäessä tein 
		tuon DBinterface.php tiedoston, jonka kautta kaikki tietokantaliikenne
		kulkee... 
		
		$connectionString = "dbname=".$this->configurations->DBDatabase
				." user=".$this->configurations->DBUsername
				." password=".$this->configurations->DBPassword;
				
		$this->debugger->debug("Trying connect to db with connection string: " .
				"$connectionString", "Database");

		$this->connection = pg_connect($connectionString);
		
		if(!$this->connection){
			$this->debugger->debug("Connecting database failed: " . pg_last_error(), "Database");
			$this->debugger->error("Connecting database failed!", "Database");
		} else {
			$this->debugger->debug("Database connection established", "Database");
		}
		*/
		
		// Sets the connection attribute to be the same as $conn variable defined in DBInterface
		global $conn;
		$this->connection = $conn;
		
		// Ilmomasiina uses fetch mode ASSOC, which means that the result array has 
		// textual id's like $resultrow['result_field'];
		$this->connection->setFetchMode(MDB2_FETCHMODE_ASSOC);
		
	}
	
	function doSelectQuery($sql, $types, $data){
		if($this->chechCorrectFormatToMDB2Arrays($sql, $types, $data)){
			return $this->doPrepareExecuteQuery($sql, $types, $data, MDB2_PREPARE_RESULT);
		} else {
			$this->debugger->error("Error in MDB2 prepare/execute arrays", "doSelectQuery");
		}
	}
	
	function doManipulationQuery($sql, $types, $data){
		if($this->chechCorrectFormatToMDB2Arrays($sql, $types, $data)){
			return $this->doPrepareExecuteQuery($sql, $types, $data, MDB2_PREPARE_MANIP);
		} else {
			$this->debugger->error("Error in MDB2 prepare/execute arrays", "doManipulationQuery");	
		}
	}
	
	/**
	 * Because the prepare/execute seems to suck a lot, this is the traditional 
	 * way to do the query
	 */
	function doQuery($sql){
		global $conn;
		$res =& $conn->query($sql);

		$this->debugger->debug($sql, "doQuery");
		
  		// Check if query failed
  		if(PEAR::isError($res)){
    		print $res->getDebugInfo();
    		die($res->getMessage());
  		} else {
    	return $res;
  		}
	}
	
	function doPrepareExecuteQuery($sql, $types, $data, $result_type){
		
		// Check result_type parameter
		if($result_type != MDB2_PREPARE_MANIP && $result_type != MDB2_PREPARE_RESULT){
			$this->debugger->error("Value of result_type must be either MDB2_PREPARE_MANIP or MDB2_PREPARE_RESULT" , "doQuery");
		}
		
		$mdb2 = $this->connection;
		
		$statement = $mdb2->prepare($sql, $types, $result_type);
		$result = $statement->execute($data);
		
		if (PEAR::isError($result)) {
			$this->debugger->debug($result->getDebugInfo(), "doQuery");
    		$this->debugger->error($result->getMessage(), "doQuery");
		} else {
			return $result;
		}
	}
	
	function toString(){
		if(!$this->connection){
			return "Not connected to database";
		} else {
			return "Connected to database";
		}
	}
	
	/**
	 * Does the same thing than Postgre's age() function. age() function 
	 * counts the interval between two dates. This function was done because 
	 * the Ilmomasiina used lots of age() function when it was in TML server
	 *
	 * @param  date1
	 * @param  date2
	 * @return Returns the age in seconds of date1 and date2 in the MDB2 format. 
	 *         The result can be used directly in the MDB2 sql query string
	 */
	function age2MDB2($date1, $date2){
		return " " . $date2 . " - " . $date1 . " ";
	}

	function ageMoreThan($date, $age, $timeunit){
		// Creates UNIX timestamp
		$now = time();
		
		$interval = $now;
		
		// Add age in days
		if($timeunit == TIMEUNIT_DAYS){
			$interval = $now - $age * 60 * 60 * 24;
		} 
		else if($timeunit == TIMEUNIT_MINUTES){
			$interval = $now - $age * 60;
		}
		else {
			$this->debugger->error("Invalid timeunit");
		}
		
		return " " . $date . " < '" . MDB2_Date::unix2Mdbstamp($interval) . "' ";
	}
	
	function ageLessThan($date, $age, $timeunit){
		// Creates UNIX timestamp
		$now = time();
		
		$interval = $now;
		
		// Add age in days
		if($timeunit == TIMEUNIT_DAYS){
			$interval = $now - $age * 60 * 60 * 24;
		} 
		else if($timeunit == TIMEUNIT_MINUTES){
			$interval = $now - $age * 60;
		}
		else {
			$this->debugger->error("Invalid timeunit");
		}
		
		return " " . $date . " > '" . MDB2_Date::unix2Mdbstamp($interval) . "' ";
	}
	
	function chechCorrectFormatToMDB2Arrays($sql, $types, $datas){
		// Chech the count of question marks in the sql statement
		$countOfQuestionMarks = substr_count($sql, '?');
		
		// The count of question marks should be same as the size of $types and $data
		if($countOfQuestionMarks != count($types) || $countOfQuestionMarks != count($datas)){
			return false;
		}
		
		
		// Finally let's chech that the $types and the real types of $data are the same
		for($i = 0; $i < count($types); $i++){
			$data = $datas[$i];
			$type = $types[$i];
			
			if($type == 'integer' && is_int($data)){
				// OK!
			} else if($type == 'text' && is_string($data)){
				// OK!
			} else {
				// Either the $type is totally wrong or the type of the $data is not the same as $type
				return false;
			}
		}
		return true;
	}
	
	function isInYear($year, $field){
		// The value of $year is got from get variable, so we must chech that it 
		// is really a integer with correct value
		if(!is_int(intval($year)) || $year < 2000){
			$this->debugger->error("The year value is not correct", "isInYear");
		}
		
		$yearStarts = mktime(0, 0, 0, 1, 1, $year);
		$yearEnds = mktime(0, 0, 0, 1, 1, $year + 1);
		
		return " $field >= '" . MDB2_Date::unix2Mdbstamp($yearStarts) . "' AND $field < '" . MDB2_Date::unix2Mdbstamp($yearEnds) . "' ";
		
	}
	
	function lastInserted($table, $field){
		global $conn;
		return $conn->lastInsertId($table, $field);
	}
	
	function fieldIsInArray($values, $field){
		$whereclause = "";
		
		$isFirst = true;
		foreach($values as $value){
			if($isFirst == false){
				$whereclause .= " OR ";
			}
			$whereclause .= $field . " = " . $value . " ";
			$isFirst = false;
		}
		return $whereclause;
	}
	
}
 
?>
