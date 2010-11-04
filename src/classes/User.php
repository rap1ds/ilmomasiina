<?php

// FIXME Add session timeout features

class User{
	
	var $configurations;
	var $signupid;
	var $oldSignupid = 0;
	var $newSignupid;
	var $sessionId;
	var $database;
	var $unconfirmedSignupExists;
	var $unconfirmedSignupIsNotTheSameAsThis;
	var $position = -1;
	var $unconfirmedSignups = -1;
	var $debugger;
	var $userId;
	var $signupTime;
	
	// FIXME The logic with the signup id is not very clear.
	function User($newSignupid){
		CommonTools::initializeCommonObjects($this);
		
		@session_start();	// Throws notice if session started already
		$this->sessionId = session_id();
		$this->newSignupid = $newSignupid;
		$this->unconfirmedSignupExists = false;
		$this->selectSessionDataFromDatabase();
		
		// TODO Somehow I feel that this class has not been made at its best way
		// Maybe I should check that better later
		
		// Check if there is unconfirmed signups for this session and start 
		// new session if not already started
		if($this->getUnconfirmedSignupExists()){
			if($this->getOldSignupId() == $this->newSignupid){
				// User hasn't confirm the current signup	
				$this->unconfirmedSignupIsNotTheSameAsThis = false;
				$this->selectPositionFromDatabase();
			} else {
				// User hasn't confirm some other signup
				$this->unconfirmedSignupIsNotTheSameAsThis = true;
			}
		} else if ($this->newSignupid >= 0) {
			// Not any old confirmed signups. This means also that the 
			// session is new, because session handler newer generates two 
			// same session id's (well, it may generate two identical id's
			// but that's very very rare)
			
			// FIXME SOMETHING FAILS HERE!
			
			$this->insertSessionDataToDatabase();
			
		}
	}
	
	function getSessionExistsAlready(){
		return $this->sessionExistsAlready;
	}
	
	function getUnconfirmedSignupExists(){
		return $this->unconfirmedSignupExists;
	}
	
	function getUnconfirmedSignupIsNotTheSameAsThis(){
		return $this->unconfirmedSignupIsNotTheSameAsThis;
	}
	
	function insertSessionDataToDatabase(){
		// Stores session data to database.
		$sql = "INSERT INTO ilmo_users (ilmo_id, id_string, confirmed, time) VALUES ($this->newSignupid, '$this->sessionId', 0, '". MDB2_Date::mdbNow(). "')";
		
		$this->database->doQuery($sql);
		
	}
	
	function selectSessionDataFromDatabase(){
		$sql = "SELECT id, ilmo_id, id_string, confirmed, time FROM ilmo_users WHERE id_string = '$this->sessionId'";
		
		$result = $this->database->doQuery($sql);
		
		if($result->numRows() > 0){
		$row = $result->fetchRow(MDB2_FETCHMODE_ASSOC);
		
		// print_r($row);
		
			$this->userId = $row['id'];
			$this->oldSignupid = $row['ilmo_id'];
			$this->sessionId = $row['id_string'];
			$this->signupTime = strtotime($row['time']);
			$this->unconfirmedSignupExists = 
				!CommonTools::sqlToBoolean($row['confirmed']);	// row[confirmed] should never get false value
												     			// because the session is unset after confirmation															// so the sessionId should be something new
		}
	}
	
	function getOldSignupId(){
		return $this->oldSignupid;
	}
	
	function getNewSignupId(){
		return $this->newSignupid;
	}
	
	function getSessionId(){
		return $this->sessionId;
	}
	
	function getReadableSignupTime(){
		return date("H:i:s", $this->signupTime);
	}
	
	function getSignupTimeLeftInMinutes(){
		$end = $this->signupTime + $this->configurations->signupTime * 60;
		$interval = $end - time();
		$interval = getdate($interval);
		return $interval['minutes'];
	}
	
	function cancelUnconfirmedSignupAndRefreshSession(){
		$this->cancelUnconfirmedSignup();
		
		// Unset session and create a new session
		session_destroy();
		session_start();
		session_regenerate_id();
		$this->sessionId = session_id();
		$this->insertSessionDataToDatabase();
	}
	
	function cancelUnconfirmedSignup(){
		// Delete database entry
		$sql = "DELETE FROM ilmo_users WHERE id_string = '" . $this->getSessionId() . "' AND confirmed = 0";
		$this->database->doQuery($sql);
	}
	
	/**
	 * Gets user's position from database
	 */
	function selectPositionFromDatabase(){
		$sql = "SELECT ilmo_id, id_string, confirmed FROM ilmo_users WHERE " .
		 "ilmo_id = $this->newSignupid ORDER BY time";
	
		$result = $this->database->doQuery($sql);
		
		$position = 1;
		$unconfirmedSignups = 0;
		$foundIdFromQueue = false;
		
		// Tries to find current users
		while($row = $result->fetchRow(MDB2_FETCHMODE_ASSOC)){
			$this->debugger->debug("id_string: " . $row['id_string'] . ", sessionId: " . $this->sessionId, "selectPositionFromDatabase");
			if($row['id_string'] == $this->sessionId){
				// User's id found!
				$foundIdFromQueue = true;
				break;
			} else {
				$position++;
				if(CommonTools::sqlToBoolean($row['confirmed']) == false){
					$unconfirmedSignups++;
				}
			}
		}
		
		// Sets position
		if($foundIdFromQueue){
			$this->position = $position;
			$this->unconfirmedSignups = $unconfirmedSignups;
		} else {
			$this->position = -1;
			$this->unconfirmedSignups = -1;	
		}
	}
	
	function getId(){
		return $this->userId;
	}
	
	/**
	 * Returns users position on the queue. Method works only if user 
	 * has alreade signed up on current signup form.
	 */
	function getPosition(){
		return $this->position;
	}
	
	/**
	 * Returns count of unconfirmed signups which are before user on the queue. 
	 * Method works only if user has alreade signed up on current signup form.
	 */
	function getUnconfirmedSignupsBefore(){
		return $this->unconfirmedSignups;
	}
	
	function setConfirmed($confirmed){

		global $conn;

		$confirmedSql = "";
		if($confirmed){
			$confirmedSql = "TRUE";
		} else {
			$confirmedSql = "FALSE";
		}
		$sql = "UPDATE ilmo_users SET confirmed = $confirmedSql WHERE id=$this->userId";
		$result = $this->database->doQuery($sql);
		
		// Always check that result is not an error
		if (PEAR::isError($result)) {
   			die($result->getMessage());
		}
		
		/* Tämä meni rikki kun Otax päivitti PHP:n 5:teen. En osannut korjata... kommentoin... huoh @ me
		$num = 0;
		if ($conn->getOption('result_buffering')) {
    			$num = $result->numRows();
		} else {
    			// 'cannot get number of rows in the result set when "result_buffering" is disabled';
		}

		die($num);

		// Debugging
		if($num == 1){
			$this->debugger->debug("User confirmed status changed to $confirmedSql", "setConfirmed");
		} else {
			$this->debugger->debug("Failed to change users confirmed status or 'result buffering' is disabled. Affected rows: " + $num, "setConfirmed");
		} */
	}
	
	function destroySessionId(){
		session_regenerate_id();
		$this->debugger->debug("Session id destroyed", "destroySessionId");
		unset($this);
	}
}

?>
