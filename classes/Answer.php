<?php

class Answer{
	var $debugger;
	var $question;
	var $answer;		// Array
	var $signupId;
	var $database;
	var $userId;
	
	function Answer($answer, $userId, $question){
		CommonTools::initializeCommonObjects($this);
		
		// Check parameters
		if(!is_a($question, "Question")){
			$this->debugger->error("Answer constructor: Question parameter must be object of class Question", "Answer");
		}
		
		if($question->getType() == "checkbox"){
			if(!is_array($answer)){
				$this->debugger->error("Answer constructor: If question is checkbox question answer parameter must be an array", "Answer");
			}
		}
		
		$this->question = $question;
		$this->signupId = $question->getSignupId();
		$this->userId   = $userId;
		$this->answer   = $answer;
	}
	
	function isRequired(){
		return $this->question->getRequired();
	}
	
	function getAnswer(){
		return $this->answer;
	}
	
	/**
	 * This is same as getAnswer to except it returns checkbox array variable 
	 * in nice human readable format
	 */
	function getReadableAnswer(){
		// Check if answer is array
		if(is_array($this->answer)){
			$commaSeparatedValues = "";
			foreach($this->answer as $answerValue){
				$commaSeparatedValues .= $answerValue . ", ";
			}
			// Removes the lass comma and empty space
			$commaSeparatedValues = substr($commaSeparatedValues, 0, -2);
			return $commaSeparatedValues;
		} else {
			return $this->answer;
		}
	}
	
	function isEmpty(){
		if($this->question->getType() == "checkbox"){
			if(count($this->answer) <= 0){
				return true;
			} else {
				return false;
			}
		} else {
			if(trim($this->answer) == ""){
				return true;
			} else {
				return false;
			}
		}
	}
	
	function getSignupId(){
		return $this->signupId;
	}
	
	function getUserId(){
		return $this->userId;
	}
	
	function getQuestion(){
		return $this->question;
	}
	
	function insertToDatabase(){
		$answer = null;
		if($this->question->getType() == "checkbox"){
			$answer = CommonTools::arrayToSql($this->answer);
		} else {
			$answer = htmlentities($this->answer, ENT_QUOTES);
		}
		
		$sql = "INSERT INTO ilmo_answers (question_id, answer, user_id) VALUES " .
				"(".$this->question->getId().", '".$answer."', ".$this->userId.")";
		$this->database->doQuery($sql);
	}
	
	function updateToDatabase(){
		$answer = null;
		if($this->question->getType() == "checkbox"){
			$answer = CommonTools::arrayToSql($this->answer);
		} else {
			$answer = htmlentities($this->answer, ENT_QUOTES);
		}
		$question_id = $this->question->getId();
		$user_id = $this->getUserId();
		
		$sql = "UPDATE ilmo_answers SET answer='$answer' WHERE " .
				"question_id='$question_id' AND user_id='$user_id'";
		$this->database->doQuery($sql);
	}
	
	function toString(){
		$str = "";
		$str .= "<b>SignupId: </b>" . $this->signupId . " ";
		$str .= "<b>Question: </b>" . $this->question->getQuestion() . " ";
		$str .= "<b>Answer: </b>" . $this->answer . " ";
		return $str;
	}
}
 
?>
