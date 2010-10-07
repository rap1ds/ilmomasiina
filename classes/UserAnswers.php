<?php

class UserAnswers{
	var $debugger;
	var $database;
	
	var $userId;
	var $answers = array();		// Array
	var $position;
	
	function UserAnswers($answers, $userId = -1, $position = -1){
		CommonTools::initializeCommonObjects($this);
		$this->userId = $userId;
		$this->setAnswers($answers);
		$this->setPosition($position);
	}
	
	function setAnswers($answers){
		// Check parameters
		if(is_array($answers)){
			foreach($answers as $answer){
				if(!is_a($answer, "Answer")){
					$this->debugger->error("Answers must be implementations of Answer class", "setAnswers");
				} else {
					// Ok, set the answers
					$this->answers = $answers;
				}
			}
		} else if ($answers == null){
			// Null values are ok
		}
		else {
			$this->debugger->error("Answers must be in an array", "setAnswers");
		}
	}
	
	function getAllAnswers(){
		return $this->answers;
	}
	
	function getPublicAnswers(){
		$public = array();
		
		foreach($this->answers as $questionId => $answer){
			$question = $answer->getQuestion();
			if($question->getPublic() == true){
				$public[$questionId] = $answer;
			}
		}
		
		return $public;
	}
	
	function addAnswer($answer){
		if(is_a($answer, "Answer")){
			$question = $answer->getQuestion();
			$questionId = $question->getId();
			if(isset($this->answers[$questionId])){
				$this->debugger->error("Answer to question id " + $questionId + 
				" is already set");
			} else {
				$this->answers[$questionId] = $answer;
			}
		} else {
			$this->debugger->error("Answers must be implementations of Answer class to be added", "setAnswers");
		}
	}
	
	function getPosition(){
		return $this->position;
	}
	
	function setPosition($position){	
		// I don't now what the f... is wrong with this but i couldn't get this 
		// simple function to work..
		$this->position = $position;
		$this->debugger->debug("Position set to " . $position, "setPosition");
	}
	
	function getAnswerToQuestion($question){	
		// Makes the method to work both id and question parameter
		$questionId = -1;
		if(is_a($question, "Question")){
			$questionId = $question->getId();
		} else {
			$questionId = $question;
		}
		
		if(isset($this->answers[$questionId])){
			return $this->answers[$questionId];
		} else {
			return null;
		}
	}
	
	/**
	 * Deletes answers of given user id
	 */
	function deleteFromDatabase(){
		$sql = "DELETE FROM ilmo_users WHERE id = $this->userId";
		$this->database->doQuery($sql);

		$sql = "DELETE FROM ilmo_answers WHERE user_id = $this->userId";
		$this->database->doQuery($sql);
	}
	
	function getUserId(){
		return $this->userId;
	}
}
?>
