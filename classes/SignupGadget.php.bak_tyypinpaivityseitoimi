<?php

require_once("Debugger.php");
require_once("Question.php");
require_once("Answer.php");
require_once("Database.php");
require_once("CommonTools.php");
require_once("Configurations.php");
require_once("UserAnswers.php");

class SignupGadget{
	
	var $debugger;
	var $configurations;
	var $database;
	var $isPrivate;				// If none of the questions is a public questions then true
	
	var $id;
	var $title;
	var $description;
	var $event_date;
	var $opens;
	var $closes;
	var $questions = array();	// NOTICE! Question ID works as index
	var $password;				// Disposable password
	var $send_confirmation;		// Boolean
	var $confirmation_message;	// String
	
	/**
	 * Answers 2D-array in following format
	 * ANSWERS[USERID]
	 *   ANSWERSBYUSER[QUESTIONID]
	 *     ANSWER()
	 *   ANSWERSBYUSER[position]
	 *     int position
	 * 
	 * NOTICE! If answers are sorted, the key start from zero and increases linearly
	 * so the user id is not the key anymore
	 */
	var $answers = array();		// NOTICE! User ID works as index
	
	/**
	 * Constructor for a signup gadget. If you are:
	 * a) Creating a fresh new gadget, ID value should be a negative
	 *    and other parameters should be set properly
	 * b) Getting gadget data from database by ID, ID should be set properly 
	 *    and other parameters are unimportant (not set)
	 * c) Creating a new gadget of which data you already know, all of the 
	 *    parameters should be set properly (this fits for editing gadget)
	 *
	 * The default value <!notset!> means that the title or description is not set.
	 * This is a bit dump way to do this but I couldn't figure out any better. 
	 * First, the <!notset!> value was relpaced by null, but it did not work if 
	 * user had not set the description (and then the description was empty string).
	 * PHP converts null value to empty string, so empty value and value which was 
	 * really null mixed up.
	 */
	function SignupGadget($id = -1, $title = "<!notset!>", $description = "<!notset!>", 
		$event_date = -1, $opens = -1, $closes = -1, $send_confirmation = false, $confirmation_message = "<!notset!>"){
			
		CommonTools::initializeCommonObjects($this);
		
		$this->debugger->debug("Trying to create a new signup gadget object " .
				"with parameters id: $id, title: $title, description: $description, " .
				"event_date: $event_date, opens: $opens, closes: $closes", "SignupGadget");
		
		// Situation a) - new gadget 
		if($id < 0 && $title != "<!notset!>" && $description != "<!notset!>" && $event_date >= 0
			&& $opens >= 0 && $closes >= 0 && $confirmation_message != "<!notset!>"){
			
			// Set field values
			$this->title		= $title;
			$this->description	= $description;
			$this->event_date	= $event_date;
			$this->opens		= $opens;
			$this->closes		= $closes;
			$this->send_confirmation = $send_confirmation;
			$this->confirmation_message = $confirmation_message;
			
			// Create disposable password
			$password 	 = CommonTools::generatePassword();
			$this->password = $password;
		} 
		
		// Situation b) - data from database by id
		else if($id >= 0 && $title == "<!notset!>" && $description == "<!notset!>"
			&& $event_date < 0 && $opens < 0 && $closes < 0 && $confirmation_message == "<!notset!>"){
			
			// Set id and the data from database
			$this->id			= $id;
			$this->selectSignupGadgetById();
			$this->selectQuestionsFromDatabase();
			
			// Get answers if needed
			if($this->isOpen() || $this->isClosed()){
				$this->selectAnswersFromDatabase();
			}
			
		}
		
		// Situation c) - data already got from database
		else if($id >= 0 && $title != "<!notset!>" && $description != "<!notset!>"
			&& $event_date >= 0 && $opens >= 0 && $closes >= 0 && $confirmation_message != "<!notset!>"){
			
			// Set field values
			$this->id			= $id;
			$this->title		= $title;
			$this->description	= $description;
			$this->event_date	= $event_date;
			$this->opens		= $opens;
			$this->closes		= $closes;
			$this->send_confirmation = $send_confirmation;
			$this->confirmation_message = $confirmation_message;
		}
		
		// Invalid parameters
		else{
			$this->debugger->debug("Invalid parameters for SignupGadget. " .
					"You should respect one of the three situations which " .
					"are listed on a constructor function", "SignupGadget");
			$this->debugger->error("Invalid parameters for SignupGadget", "SignupGadget");
		}
		
		$this->debugger->debug("Created new signup gadget with values: " .
				"$id, $title, $description, ".
				$this->getReadableEventdate().", ".
				$this->getReadableOpeningTime().", ".
				$this->getReadableClosingTime(), "SignupGadget");
				
		// Remove unconfirmed signups
		$this->removeUnconfirmedSignups();
	}
	
	function toString(){
		// TODO implementation
	}
	
	function getId(){
		return $this->id;
	}
	
	function getOpens(){
		return $this->opens;
	}
	
	function getCloses(){
		return $this->closes;
	}
	
	function getOpeningTime(){
		// FIXME Is this needed?
		return date("d.m.y \k\l\o H:i", $this->opens);
	}
	
	function getClosingTime(){
		// FIXME is this needed?
		return date("d.m.y \k\l\o H:i", $this->closes);
	}
	
	function setOpeningTime($time){
		$this->opens = $time;
	}
	
	function setClosingTime($time){
		$this->closes = $time;
	}
	
	/**
	 * Signup is open right now
	 */
	function isOpen(){
		return time() > $this->opens && time() < $this->closes;
	}
	
	/**
	 * Signup has been open and is closed already
	 */
	function isClosed(){
		return time() > $this->closes;
	}
	
	/**
	 * Signup is not yet opened
	 */
	function willBeOpened(){
		return !$this->isOpen() && !$this->isClosed();
	}
	
	function getTitle(){
		return $this->title;
	}
	
	function getDescription(){
		return $this->description;
	}
	
	function getEventDate(){
		return $this->event_date;
	}
	
	function getReadableOpeningTime(){
		return date("d.m.Y H:i", $this->opens);
	}
	
	function getReadableClosingTime(){
		return date("d.m.Y H:i", $this->closes);
	}
	
	function getReadableEventdate(){
		return date("d.m.Y H:i", $this->event_date);
	}
	
	function getPassword(){
		return $this->password;
	}

	function getSendConfirmation(){
		return $this->send_confirmation;
	}

	function getConfirmationMessage(){
		return $this->confirmation_message;
	}
	
	/**
	 * Checks that the parameter value is proper. This means that parameter 
	 * must be positive integer. The object can stand other parameters so 
	 * execution is stopped by error method if the parameter is illegal
	 */
	function checkPositiveIntParameter($parameter){
		if(is_int($parameter) && $parameter >= 0){
			return true;
		} else {
			$this->debugger-debug("Invalid parameter value: " . $parameter, "checkPositiveIntParameter");
			$this->debugger->error("Invalid parameter value", "checkPositiveIntParameter");
		}
	}
	
	/**
	 * If Id is set selects questions from database, creates a Question object 
	 * of every question and adds questions to questions field
	 */
	function selectQuestionsFromDatabase(){
		$questions = $this->selectQuestionsFromDatabaseAndReturnAsTable();
		foreach($questions as $question){
			$this->addQuestion($question);
			
			$this->debugger->debug("Added question \"".$question->getQuestion()."\" with id ".$question->getId(), "selectQuestionsFromDatabase");
		}
	}
	
	/**
	 * Gets question data from database and return an array which contains the 
	 * questions
	 */
	function selectQuestionsFromDatabaseAndReturnAsTable(){
				// Check that id is set (and is above 0 (this should be every time when id is set))
		if(!(isset($this->id) && $this->id >= 0)){
			$this->debugger->error("Can't select questions from database if id is not set", "selectQuestionsFromDatabase");
		}
		
		$sql = "SELECT id, question, type, options, public, required FROM " .
				"ilmo_questions WHERE ilmo_id = ? ORDER BY id";
		
		$types = array('integer');
		$datas = array(intval($this->id));
				
		$result = $this->database->doSelectQuery($sql, $types, $datas);
		
		// Array to return
		$questions = array();
		
		while($row = $result->fetchRow(MDB2_FETCHMODE_ASSOC)){
			$id             = $row["id"];
			$questionString = $row["question"];
			$type           = $row["type"];
			$options        = CommonTools::sqlToArray($row["options"]);
			$public         = CommonTools::sqlToBoolean($row["public"]);
			$required       = CommonTools::sqlToBoolean($row["required"]);
			
			
			$question = new Question($questionString, $type, $options, $public, $required, $this->getId(), $id);
			array_push($questions, $question);
		}
		
		return $questions;
	}
	
	function selectAnswersFromDatabase(){
		// Gets the answers
		$sql_answers = 
			"SELECT answer, user_id, question_id FROM ilmo_answers WHERE (SELECT ilmo_id FROM " .
			"ilmo_questions WHERE id=question_id) = " . $this->getId() ." ORDER BY " .
			"user_id";
		   
		// Gets queue
		$sql_queue = 
			"SELECT id, confirmed FROM ilmo_users WHERE ilmo_id = ". $this->getId() .
			" ORDER BY id";
		
		$result_answers = $this->database->doQuery($sql_answers);
		$result_queue   = $this->database->doQuery($sql_queue);
		
		// If there is no answers, confirmed or unconfirmed, it is useless 
		// to go further
		if($result_queue->numRows() < 1){
			$this->debugger->debug("No answers found", "selectAnswersFromDatabase");
			$this->answers = array();
			return;
		} else {
			$this->debugger->debug("Found <b>" . $result_queue->numRows() . "</b> confirmed or unconfirmed answers", "selectAnswersFromDatabase");
		}
		
		// Let's make a table which includes information if the answer is 
		// confirmed or not
		$confirmed = array();
		while($row = $result_queue->fetchRow(MDB2_FETCHMODE_ASSOC)){
			$uid = $row["id"];
			$confirmed[$uid] = CommonTools::sqlToBoolean($row["confirmed"]);
		}
		
		// And then lets get the answers
		$answers = array();
		$answersByUser = array();
		$previousUID = -1;
		while($row = $result_answers->fetchRow(MDB2_FETCHMODE_ASSOC)){
			if($previousUID != -1 && $row["user_id"] != $previousUID){
				$answers[$previousUID] = $answersByUser;
				$answersByUser = array();
			}
			
			// Get parameters for Answer object's constructor
			$question = $this->getQuestionById($row["question_id"]);
			$answer = null;
			if($question->getType() == "checkbox"){
				$answer = CommonTools::sqlToArray($row["answer"]);
			} else {
				$answer = $row["answer"];
			}
			$userId = $row["user_id"];
			$answersByUser[$question->getId()] = new Answer($answer, $userId, $question);

			$previousUID = $row["user_id"];
		}
		
		// The last one
		// Do not add the last one if there is no confirmed answers at all
		if($previousUID != -1){
			$answers[$previousUID] = $answersByUser;
		}
		
		// Now the answers are in answers array. Let's put the unconfirmed 
		// to the array too
		foreach($confirmed as $user => $value){
			if($value == false){
				$answers[$user] = null;
			}
		}
		
		// Because the unconfirmed answers have been inserted last the 
		// array must be sorted by key
		ksort($answers);
		
		// Finally, lets put the positions to answers
		$position = 1;
		foreach($answers as $key => $answerByUser){
			if($answerByUser != null){
				$answers[$key] = new UserAnswers($answerByUser, $key, $position);
			} else {
				$answers[$key] = null;
			}
			$position++;
		}
		
		$this->answers = $answers;
	}
	
	function deleteFromDatabase(){
		// Deletes signup gadget info
		$sql = "DELETE FROM ilmo_masiinat WHERE id = " . $this->id;
		
		$this->database->doQuery($sql);
		
		// Deletes answers
		
		// First get question id's
		$sql = "SELECT id FROM ilmo_questions WHERE ilmo_id = " . $this->id;
		
		$result = $this->database->doQuery($sql);
			
		// Put question id's into array
		$question_ids = array();
		while($row = $result->fetchRow()){
			array_push($question_ids, $row['id']);
		}
		
		$sql = "DELETE FROM ilmo_answers WHERE " . Database::fieldIsInArray($question_ids, 'question_id');
		$this->database->doQuery($sql);


		// Deletes questions
		$sql = "DELETE FROM ilmo_questions WHERE ilmo_id = " . $this->id;
		$this->database->doQuery($sql);
		
		// Deletes users
		$sql = "DELETE FROM ilmo_users WHERE ilmo_id = " . $this->id;
		$this->database->doQuery($sql);
	}
	
	function insertToDatabase(){
		
		// Change time format
		$event_sql	 = CommonTools::timeToSql($this->event_date);
		$opens_sql	 = CommonTools::timeToSql($this->opens);
		$closes_sql	 = CommonTools::timeToSql($this->closes);
		$send_confirmation_sql = CommonTools::booleanToSql($this->send_confirmation);

		// FIXME pitäisköhän confirmation_messagen quotet escapettaa. ( \' ?)
		
		// Save information to database
		$sql = "INSERT INTO ilmo_masiinat " . 
	    "(title, description, opens, closes, eventdate, password, send_confirmation, confirmation_message) " . 
	    "VALUES ('$this->title', '$this->description', '$opens_sql', '$closes_sql', '$event_sql', '$this->password', '$send_confirmation_sql', '$this->confirmation_message')";
	    
	   // Do query
		
		$this->database->doQuery($sql);
		
		/*
		// Save information to database
		$sql = "INSERT INTO ilmo_masiinat " . 
	    "(title, description, opens, closes, eventdate, password) " . 
	    "VALUES (?, ?, '$opens_sql', '$closes_sql', '$event_sql', '$this->password')";
	    
	   // Do query
		
		$types = array('text', 'text');
		$datas = array($this->title, $this->description);
		
		$this->database->doManipulationQuery($sql, $types, $datas); */
		
		$id = $this->database->lastInserted('ilmo_masiinat', 'id');
		
	    // Save the questions to databased
	    
		foreach($this->questions as $question){
			 // Change options array to sql format
			
			$questionString = $question->getQuestion();
			$type = $question->getType();
			$options = CommonTools::arrayToSql($question->getOptions());
			$public = CommonTools::booleanToSql($question->getPublic());
			$required = CommonTools::booleanToSql($question->getRequired());

			$sql = "INSERT INTO ilmo_questions " . 
	   			"(ilmo_id, question, type, options, public, required) " . 
	   			"VALUES ($id, '$questionString', '$type', '$options', $public, $required)";
	   		
	   		// do query
			$this->database->doQuery($sql);
		}
	}

	function setAnswers($answers){		
		// die("This function <b>setAnswers</b> is only for debugging. If you really are debugging, comment this \"die\" line.");
		$this->answers = $answers;
	}

	function updateToDatabase(){
		
		// Convert timestamps to sql format
		$event_sql = CommonTools::timeToSql($this->event_date);
		$opens = CommonTools::timeToSql($this->opens);
		$closes = CommonTools::timeToSql($this->closes);
		$send_confirmation_sql = CommonTools::booleanToSql($this->send_confirmation);

		// Save the updated info to database
		$sql = "UPDATE ilmo_masiinat SET " . 
	    "title='$this->title' , " .
	    "description='$this->description', opens='$opens' , eventdate='$event_sql', " .
	    "closes='$closes', send_confirmation='$send_confirmation_sql', confirmation_message='$this->confirmation_message' WHERE id='$this->id'";
		 
	    $this->database->doQuery($sql);
		 
	    // Updates the questions
	    
	    $oldQuestions = $this->selectQuestionsFromDatabaseAndReturnAsTable();
	    
	    // Remove questions that have been removed from the gadget
	    $questionIdsToRemove = $this->getQuestionIdsToRemove($oldQuestions);
		if(count($questionIdsToRemove) > 0){
			$questionsToRemoveSqlArray = CommonTools::idArrayToSql($questionIdsToRemove);
			
			$sql = "DELETE FROM ilmo_answers WHERE " . Database::fieldIsInArray($questionIdsToRemove, 'question_id');
			$this->database->doQuery($sql, array(), array());

			$sql = "DELETE FROM ilmo_questions WHERE " . Database::fieldIsInArray($questionIdsToRemove, 'id');
			$this->database->doQuery($sql, array(), array());
		}
		
		
		// FIXME The questions database update should be moved to Question class
		
		// Update questions that have been changed
		$questionsToUpdateArray = $this->getQuestionIdsToUpdate($oldQuestions);
		foreach($questionsToUpdateArray as $questionId){
			$question = $this->questions[$questionId];
			
			$questionString 	= $question->getQuestion();
			$type				= $question->getType();
			$options 			= CommonTools::arrayToSql($question->getOptions());
			
			$public 			= CommonTools::booleanToSql($question->getPublic());
			$required 			= CommonTools::booleanToSql($question->getRequired());
			
			$sql = "UPDATE ilmo_questions SET " . 
	   			"question='$questionString', type='$type', options='$options', public=$public, required=$required WHERE id=$questionId";
		
			$this->database->doQuery($sql);
		}
		
		// Adds new questions
		$questionsToAddArray = $this->getNewQuestionToAdd();
		foreach($questionsToAddArray as $questionToAdd){
			$questionToAdd->insertToDatabase();
		}
	}
	
	function getQuestionIdsToRemove($oldQuestions){
		$toBeRemoved = array();
		foreach($oldQuestions as $oldQuestion){
			$oldId = $oldQuestion->getId();
			if(!array_key_exists($oldId, $this->questions)){
				$this->debugger->debug("Question id $oldId will be removed", "getQuestionIdsToRemove");
				array_push($toBeRemoved, $oldId);
			}
		}
		$this->debugger->debug("Found " . count($toBeRemoved) . " questions to be removed", "getQuestionIdsToRemove");
		return $toBeRemoved;
	}
	
	function getNewQuestionToAdd(){
		$toBeAdded = array();
		foreach($this->questions as $key => $value){
			if(!($value->getId() >= 0)){
				array_push($toBeAdded, $value);
			}
		}
		$this->debugger->debug("Found " . count($toBeAdded) . " questions to be added", "getNewQuestionToAdd");
		return $toBeAdded;
	}
	
	function getQuestionIdsToUpdate($oldQuestions){
		$toBeUpdated = array();
		foreach($oldQuestions as $oldQuestion){
			$id = $oldQuestion->getId();
			if(isset($this->questions[$id])){
				$newQuestion = $this->questions[$id];
				if(!$newQuestion->equals($oldQuestion)){
					array_push($toBeUpdated, $id);
					$this->debugger->debug("Question id ".$oldQuestion->getQuestion()." will be updated", "getQuestionIdsToRemove");
				}
			}
		}
		$this->debugger->debug("Found " . count($toBeUpdated) . " questions to be updated", "getNewQuestionToAdd");
		return $toBeUpdated;
	}
	
	function selectSignupGadgetById(){
		// Gets data from database
		$sql = "SELECT opens, closes, title, description, eventdate, password, send_confirmation, confirmation_message FROM " .
				"ilmo_masiinat WHERE id = $this->id ";
		
		$result = $this->database->doQuery($sql);
		
		$this->debugger->debug("The query selected " . $result->numRows() . " rows", "selectSignupGadgetById");
		
		// Check that got one and only one result
		if($result->numRows() == 1){
			$row = $result->fetchRow(MDB2_FETCHMODE_ASSOC);
		
			// Set values to signup gadget object
			$this->opens = strtotime($row['opens']);
			$this->closes = strtotime($row['closes']);
			$this->title = $row['title'];
			$this->description = $row['description'];
			$this->eventdate = strtotime($row['eventdate']);
			$this->password = $row['password'];
			$this->send_confirmation = CommonTools::sqlToBoolean($row['send_confirmation']);
			$this->confirmation_message = $row['confirmation_message'];			

			$this->debugger->debug("Data for signup gadget (ID $this->id) found from database" , "selectSignupGadgetById");
			
		} else {
			$this->debugger->error("Couln't find a signup by ID " . $this->id, "selectSignupGadgetById");
		}
	}
	
	function addQuestion($question){
		// Check parameter
		if(!is_a($question, "Question")){
			$this->debugger->error("New question must be of class Question", "addQuestion", "addQuestion");
		}
		
		// Let's add the question. The question index would be good index to 
		// array too, but if user is saving newly created signup gadget the 
		// question don't have an id. So, use id only if it is different from -1
		if($question->getId() >= 0){
			$this->questions[$question->getId()] = $question;
			$this->debugger->debug("New question ".$question->getQuestion()." added to signup gadget with id " . $question->getId(), "addQuestion");
		} else {
			array_push($this->questions, $question);
			$this->debugger->debug("New question ".$question->getQuestion()." added to signup gadget with no id", "addQuestion");
		}
		
		$this->updateIsPrivate($question);
	}
	
	function updateIsPrivate($addedQuestion){
		// Is isPrivate set?
		if(isset($this->isPrivate)){
			// Is set
			if($this->isPrivate == true){
				if($addedQuestion->getPublic() == true){
					$this->isPrivate = false;
					$this->debugger->debug("isPrivate set to: false", "updateIsPrivate");
				}
			}
		} else {
			// Is not set
			if($addedQuestion->getPublic() == true){
				$this->isPrivate = false;
				$this->debugger->debug("isPrivate set to: false" , "updateIsPrivate");
			} else {
				$this->isPrivate = true;
				$this->debugger->debug("isPrivate set to: true" , "updateIsPrivate");
			}
		}
	}
	
	function getAllQuestions(){
		return $this->questions;
	}
	
	function getPublicQuestions(){
		$publicQuestions = array();
		foreach($this->getAllQuestions() as $key => $question){
			if($question->getPublic() == true){
				$publicQuestions[$key] = $question;
			}
		}
		return $publicQuestions;
	}
	
	function getFormatedQuestionIds(){
		$questionIds = "";
		foreach($this->getAllQuestions() as $question){
			$type = $question->getType();
			if($type == "text" || $type == "textarea"){
				$questionIds .= $question->getId() . ",";
			} else {
				for($i = 0; $i < count($question->options); $i++){
					$questionIds .= $question->getId() . "-" . $i . ",";
				}
			}
		}
		$questionIds = substr($questionIds, 0, -1);
		return $questionIds;
	}
	
	function getAllAnswersByUsers(){
		return $this->answers;
	}

	
	function getQuestionById($id){
		if(isset($this->questions[$id])){
			return $this->questions[$id];
		} else {
			$this->debugger->error("Question id $id does not exist", "getQuestionsById");
		}
	}
	
	function getAllQuestionCount(){
		return count($this->getAllQuestions());
	}
	
	function getPublicQuestionCount(){
		return count($this->getPublicQuestions());
	}
	
	function removeUnconfirmedSignups(){
		global $MDB2Datatype;
		
		// print_r(get_class_methods(get_class($MDB2Datatype)));
		
		// Removes unconfirmed and old signups
		
		/*
		 * I commented out the few lines below. Why the old signups are deleted?
		 * -mkoski
		 */
		// $sql = "DELETE FROM ilmo_users WHERE ilmo_users.confirmed = 0 AND " . 
		// $this->database->ageLessThan("time", $this->configurations->signupTime, TIMEUNIT_MINUTES) . 
		//	" OR (SELECT closes FROM ilmo_masiinat WHERE ilmo_users.ilmo_id = id) < '" . MDB2_Date::mdbNow() . "'";

		$sql = "DELETE FROM ilmo_users WHERE ilmo_users.confirmed = 0 AND " . 
		      $this->database->ageMoreThan("time", $this->configurations->signupTime, TIMEUNIT_MINUTES);
		$this->database->doQuery($sql);
	}
	
	function isPrivate(){
		return $this->isPrivate;
	}
	
	/**
	 * Gets answers by given user. Returns an UserAnswer object.
	 */
	function getAnswerByUser($userId){
		if(array_key_exists($userId, $this->answers)){
			return $this->answers[$userId];
		} else {
			$this->debugger->error("Invalid user id (UserId: $userId)", "getAnswerByUser");
		}
	}
	
	/**
	 * Returns answer to the question by selected user
	 * 
	 * @param Question $question Question to get answer for
	 * @param int $userId Users id
	 */
	function getUserAnswerToQuestion($question, $userId){
		$answersByUser = $this->getAnswerByUser($userId);
		return $answersByUser->getAnswerToQuestion($question);
	}
	
	/**
	 * Deletes answers of given user id
	 */
	function deleteAnswersByUserFromDatabase($userId){
		$userAnswer = $this->getAnswerByUser($userId);
		$userAnswer->deleteFromDatabase();
	}
	
	function sortAnswers($questionId = -1, $reverse = false){
    	if($questionId <= 0){
    		$this->debugger->debug("Answers not sorted", "sortAnswer");
    		return;
    	}
    	
    	$records = $this->getAllAnswersByUsers();
    	$hash = array();
   
   		foreach($records as $userId => $answersByUser){
   			
   			$answerObject = $answersByUser->getAnswerToQuestion($questionId);
   			$hashKey = $answerObject->getAnswer().$userId;
   			$hash[$hashKey] = $answersByUser;
   		}
   
    	($reverse)? krsort($hash) : ksort($hash);
   
    	$records = array();
   
    	foreach($hash as $record){
        	$records []= $record;
    	}
   
    	$this->answers = $records;
    	$this->sorted = true;
    	
    	$this->debugger->debug("Array sorted by question " . $questionId, "sortAnswers");
	}
	
	/**
	 * Creates a new signup gadget directly from POST variables from previous 
	 * page. If you are creating totally new signup gadget you must not change 
	 * the value of the id, but if you are going to edit previously existing 
	 * signup gadget you should set proper value to id parameter
	 * 
	 * @param id ID of the signup gadget. If you are creating a totally new 
	 * signup gadget, ignore this parameter.
	 */
	function createSignupGadgetFromPost($id = -1){
	
		// Gets data from POST variables
		$question_num   = CommonTools::POST('question_num');
		$title          = CommonTools::POST('title');
		$description    = CommonTools::POST('description');

		$event_day      = CommonTools::POST('event_day');
		$event_month    = CommonTools::POST('event_month');
		$event_year     = CommonTools::POST('event_year');
		$event_hour     = CommonTools::POST('event_hour');
		$event_minutes  = CommonTools::POST('event_minutes');
	
		$opens_day      = CommonTools::POST('opens_day');
		$opens_month    = CommonTools::POST('opens_month');
		$opens_year     = CommonTools::POST('opens_year');
		$opens_hour     = CommonTools::POST('opens_hour');
		$opens_minutes  = CommonTools::POST('opens_minutes');
	
		$closes_day     = CommonTools::POST('closes_day');
		$closes_month   = CommonTools::POST('closes_month');
		$closes_year    = CommonTools::POST('closes_year');
		$closes_hour    = CommonTools::POST('closes_hour');
		$closes_minutes = CommonTools::POST('closes_minutes');

		$send_confirmation = CommonTools::POST('send_confirmation');
		
		// Convert string value to boolean
		if($send_confirmation == "true"){
			$send_confirmation = true;
		} else {
			$send_configuration = false;
		}
		$confirmation_message = CommonTools::POST('mailmessage');
		
		// Make timestamps
		$event_date = mktime($event_hour, $event_minutes, 0, $event_month, 
						$event_day, $event_year);
		$opens = mktime($opens_hour, $opens_minutes, 0, $opens_month, 
						$opens_day, $opens_year);		
		$closes = mktime($closes_hour, $closes_minutes, 0, $closes_month, 
						$closes_day, $closes_year);
		
		// Create signup gadget with or without id
		$signupGadget = null;
		if($id < 0){
			$signupGadget = new SignupGadget(-1, $title, $description, $event_date, $opens, $closes, $send_confirmation, $confirmation_message);				
		} else {
			$signupGadget = new SignupGadget($id, $title, $description, $event_date, $opens, $closes, $send_confirmation, $confirmation_message);
		}
	
		/*
		 * Then find the questions. The question count is known, and they are in 
		 * increasing order, but there might be removed numbers if user has 
		 * removed questions while filling the form (for example 1,2,5,7,9...)
		 */
		 
		// Is email-field already set
		$hasEmail = false;
		 
		for($i = 0, $questions_founded = 0; $questions_founded < $question_num; $i++){
			// Is the question set? 
			if(!isset($_POST["question_$i"])){
				continue;
			} 
			
			// The question is set, but is it empty?
			/* *** BUG FIX 23.09.07 - mtkoski3 *** */
			else if($_POST["question_$i"] == ""){
				$questions_founded++;
				continue;
			} else {
			
				// Get the question
				$questionId		= CommonTools::POST("id_$i");
				$question 		= CommonTools::POST("question_$i");		// Question
				$type 			= CommonTools::POST("type_$i");			// Question type
				$options_num 	= CommonTools::POST("optionsnum_$i");	// Count of options
				$public 		= CommonTools::POST("public_$i");		// Is it public?
				$required 		= CommonTools::POST("required_$i");		// Is it required?
			
				$public   = CommonTools::checkboxToBoolean($public);
				$required = CommonTools::checkboxToBoolean($required);

				if($type == "email"){
					if($hasEmail){
						// Not more than one email is allowed
						$debugger = CommonTools::getDebugger();
						$debugger->error("Vain yksi sähköpostikenttä on sallittu");
					} else {
						// If confirmation mail is enabled, email is required
						if($send_confirmation){
							$required = true;
						}
						$hasEmail = true;
					}
				}
		
				// Let's get the selected options of multiple choice questions
				$options = array();
				if($options_num > 0 && $type != "text" && $type != "textarea" && $type != "email"){
					for($j = 0, $options_founded = 0; $options_founded < $options_num; $j++){
					
						// Option can be empty if user has removed options while
						// filling the form
						if(!isset($_POST["options_$i-$j"])){
							continue;
						}

						// Ok, option founded but is it empty?
						/* *** BUG FIX 23.09.07 - mtkoski3 *** */
						else if($_POST["options_$i-$j"] == ""){
							$options_founded++;
							continue;

						} else {
							// Found an option!
							$option = CommonTools::POST("options_$i-$j");
							array_push($options, $option);
							$options_founded++;
						}
					}
				} // Options
			
				// All information of the current question has been get, so 
				// let's put the question to signupGadget
				if($id >= 0 && $questionId >= 0){
					$questionObject = new Question($question, $type, $options, $public, $required, $id, $questionId);
				} else if($id >= 0) {
					$questionObject = new Question($question, $type, $options, $public, $required, $id);
				} else {
					$questionObject = new Question($question, $type, $options, $public, $required);
				}
				$signupGadget->addQuestion($questionObject);
			
				// Increase the question found variable
				$questions_founded++;
			}
		} // questions

		// If confirmation mail is enabled, email must be asked from the user
		if($signupGadget->getSendConfirmationMail() && !$hasEmail){
			$debugger = CommonTools::getDebugger();
			$debugger->error("Sähköpostikenttä pitää olla käytössä, jos haluat lähettää käyttäjälle vahvistusviestin");
		}
		
		return $signupGadget;
	}

	function getSendConfirmationMail(){
		return $this->send_confirmation;
	}

	function getConfirmationMailMessage(){
		return $this->confirmation_message;
	}

}
?>
