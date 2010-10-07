<?php

require_once("Database.php");
require_once("SignupGadget.php");

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
		
		$sql = "SELECT id, opens, closes, title, description, eventdate " .
			   "FROM ilmo_masiinat WHERE date_part('year', closes) = $year ORDER BY id DESC";
	
		$result = $database->doQuery($sql);
		
		// Adds gadgets to signupGadgets variable
		while($row = pg_fetch_array($result)){
			$singleGadget = new SignupGadget($row[id], $row[title], $row[description], strtotime($row[eventdate]), strtotime($row[opens]), strtotime($row[closes]));
			
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
		
		$sql = "SELECT id, opens, closes, title, description, eventdate " .
			   "FROM ilmo_masiinat WHERE age(localtimestamp, closes) < interval '$days days' ORDER BY id DESC";
		
		$this->debugger->debugVar($this->database, "database", "selectOpenGadgetsOrClosedDuringLastDays");
			   
		$result = $this->database->doQuery($sql);
		
		// Adds gadgets to signupGadgets variable
		while($row = pg_fetch_array($result)){
			$singleGadget = new SignupGadget($row['id'], $row['title'], $row['description'], strtotime($row['eventdate']), strtotime($row['opens']), strtotime($row['closes']));
			
			array_push($this->signupGadgets, $singleGadget);
		}		
	}

	function selectSearchSignupGadget($keyword){
		// Split search line into keywords
		$keywords = split(" ", $keyword);

		// clear previous entry forms
		$this->removeAll();

		// let's make the search string to sql query
		$whereclause = "WHERE";

		$isFirst = true;
		foreach($keywords ar $word){
			if(isFirst){
				$whereclause = $whereclause . " title LIKE $word";
			} else {
				$whereclasue = $whereclause . " OR title LIKE $word";
			}
		}

		$sql = "SELECT id, opens, closes, title, description, eventdate " . 
			   "FROM ilmo_masiinat $whereclause ORDER BY id DESC";

		$result = $this->database->doQuery($sql);

		// Adds gadgets to signupGadgets variable
                while($row = pg_fetch_array($result)){
                        $singleGadget = new SignupGadget($row['id'], $row['title'], $row['description'], 
			strtotime($row['eventdate$

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
		$sql = "SELECT date_part('year', closes) as year FROM ilmo_masiinat ORDER by closes";
				
		$result = $database->doQuery($sql);
		
		$years = array();
		
		while($row = pg_fetch_array($result)){
			if(!in_array($row[year], $years)){
				array_push($years, $row[year]);
			}
		}
	}
	
}

?>
