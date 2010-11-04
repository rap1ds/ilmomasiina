<?php

require_once("Database.php");
require_once("SignupGadget.php");

MDB2::loadFile("Date");

/**
 * This class provides functions to sign-up gadgets (Ilmomasiina) from 
 * database. Gadgets can be searched by different methods eg. all 
 * gadgets, gadgets that are open etc.
 */
class SignupGadgets{
	
	var $signupGadgets;
	var $database;
	var $debugger;
	
	function SignupGadgets(){
		CommonTools::initializeCommonObjects($this);
		$this->signupGadgets = array();
	}
	
	/**
	 * Selects signupGadgets which are closed in a selected year
	 */
	function selectSignupGadgetsByYear($year){
		// TODO parameter check
		
		// clears the previous entry forms
		$this->removeAll();	
		
		$sql = "SELECT id, opens, closes, title, description, eventdate, send_confirmation, confirmation_message " .
			   "FROM ilmo_masiinat WHERE " . Database::isInYear($year, 'closes') ." ORDER BY id DESC";
	
		$result = $this->database->doSelectQuery($sql, array(), array());
		
		// Adds gadgets to signupGadgets variable
		while($row = $result->fetchRow(MDB2_FETCHMODE_ASSOC)){
			$singleGadget = new SignupGadget($row['id'], $row['title'], $row['description'], strtotime($row['eventdate']), strtotime($row['opens']), strtotime($row['closes']), 
					CommonTools::sqlToBoolean($row['send_confirmation']), $row['confirmation_message']);
			array_push($this->signupGadgets, $singleGadget);
		}
	}
	
	/**
	 * Selects gadgets which are open or have been closed in a past days
	 */
	function selectOpenGadgetsOrClosedDuringLastDays($days){
		// TODO parameter check
		
		// clears the previous entry forms
		$this->removeAll();
		
		$sql = "SELECT id, opens, closes, title, description, eventdate, send_confirmation, confirmation_message " .
			   "FROM ilmo_masiinat WHERE " . $this->database->ageLessThan("closes", 7, TIMEUNIT_DAYS) . " ORDER BY id DESC";
		
		$this->debugger->debugVar($this->database, "database", "selectOpenGadgetsOrClosedDuringLastDays");
		$result = $this->database->doSelectQuery($sql, array(), array());
		
		// Adds gadgets to signupGadgets variable
		while($row = $result->fetchRow(MDB2_FETCHMODE_ASSOC)){
			$singleGadget = new SignupGadget($row['id'], $row['title'], $row['description'], strtotime($row['eventdate']), strtotime($row['opens']), strtotime($row['closes']),
					CommonTools::sqlToBoolean($row['send_confirmation']), $row['confirmation_message']);
			array_push($this->signupGadgets, $singleGadget);
		}		
	}

	function selectSearchSignupGadget($keyword){
		// Split search line into keywords
		$keywords = split(" ", $keyword);

		// clear previous entry forms
		$this->removeAll();

		$whereclause = "WHERE";

		$isFirst = true;
		
		foreach($keywords as $word){
			if($isFirst){
				$whereclause = $whereclause . " title LIKE '%$word%'";
				$isFirst = false;
			} else {
				// I'm not sure if AND is better than OR...
				$whereclause = $whereclause . " AND title LIKE '%$word%'";
			}
		}

		$sql = "SELECT id, opens, closes, title, description, eventdate, send_confirmation, confirmation_message " . 
			   "FROM ilmo_masiinat $whereclause ORDER BY id DESC";
				
		/* print_r(get_class_methods($whereclause));
		print($whereclause->getDebugInfo());
		die(); */
				
		$result = $this->database->doSelectQuery($sql, array(), array());

		// Adds gadgets to signupGadgets variable
                while($row = $result->fetchRow(MDB2_FETCHMODE_ASSOC)){
                        $singleGadget = new SignupGadget($row['id'], $row['title'], $row['description'], 
			strtotime($row['eventdate']), strtotime($row['opens']), strtotime($row['closes']),
			CommonTools::sqlToBoolean($row['send_confirmation']), $row['confirmation_message']);

                        array_push($this->signupGadgets, $singleGadget);
                } 
	}
	
	/**
	 * Gets gadgets which have selected from database. If no 
	 * signup gadgets are selected return an empty array
	 * 
	 * @return selected gadget or empty array
	 */
	function getSignupGadgets(){
		return $this->signupGadgets;
	}
	
	/**
	 * Removes all gadgets selected from database
	 */
	function removeAll(){
		$this->signupGadgets = array();
	}
	
	/**
	 * Gets all years (in format YYYY) where have been signup gadgets
	 */
	function getAllSignupGadgetYears(){
		$sql = "SELECT closes FROM ilmo_masiinat ORDER by closes DESC";
				
		$result = $this->database->doSelectQuery($sql, array(), array());
		
		$years = array();
		
		while($row = $result->fetchRow(MDB2_FETCHMODE_ASSOC)){
			// $row[closes] is now in format 2008-01-31 12:00:00
			// With the help of strtotime the time can be converted to unix timestamp
			$unixTimestamp = strtotime($row['closes']);
			
			// Get the year
			$year = date('Y', $unixTimestamp);
			
			if(!in_array($year, $years)){
				array_push($years, $year);
			}
		}
		return $years;
	}
	
}

?>
