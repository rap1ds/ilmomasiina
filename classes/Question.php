<?php

class Question{
	var $debugger;
	var $database;
	
	var $question;
	var $type;
	var $options;
	var $public;
	var $required;
	var $signupId;
	var $id;
	
	function Question($question, $type, $options, $public, $required, $signupId = -1, $id = -1){
		CommonTools::initializeCommonObjects($this);
		
		// Check parameters
		if(!is_array($options)){
			$this->debugger->error("Question constructor: Options parameter must be an array", "Question");
		}
		
		// Check parameters
		$allowed_types = array('text', 'textarea', 'radio', 'dropdown', 'checkbox', 'email');
		if(!in_array($type, $allowed_types)){
			$this->debugger->error("Question constructor: Question has wrong type attribute", "Question");	
		}
		
		if(!is_bool($public)){
			$this->debugger->error("Question constructor: public must be boolean type", "Question");
		}
		
		if(!is_bool($required)){
			$this->debugger->error("Question constructor: required must be boolean type", "Question");
		}
		
		$this->question = $question;
		$this->type     = $type;
		$this->options  = $options;
		$this->public   = $public;
		$this->required = $required;
		$this->signupId = $signupId;
		$this->id       = $id;
		
		$this->debugger->debug("Created new Question: Id: $id Question: $question, Type: $type, Public: $public, Required: $required", "Question");
		if(count($options) > 0){
			$this->debugger->debugVar($options, "options", "Question");
		}
		// $this->debugger->error("Voi homo");
		
	}
	
	function getQuestion(){
		return $this->question;
	}
	
	function getType(){
		return $this->type;	
	}
	
	function getOptions(){
		return $this->options;
	}
	
	function getPublic(){
		return $this->public;
	}
	
	function getRequired(){
		return $this->required;
	}
	
	function getSignupId(){
		return $this->signupId;
	}
	
	function getId(){
		return $this->id;
	}
	
	/* THESE ARE FOR JAVA SCRIPT */
	
	function getQuestionForJS(){
		return $this->question;
	}
	
	function getTypeForJS(){
		return $this->type;
	}
	
	function getOptionsForJS(){
		$javaScriptArray = "new Array(";
		for($i = 0; $i < count($this->options); $i++){
			if($i == 0){
				// Eka
				$javaScriptArray .= "'" . $this->options[$i] . "'";
			} else {
				$javaScriptArray .= ", '" . $this->options[$i] . "'";
			}
		}
		$javaScriptArray .= ")";
		return $javaScriptArray;
	}
	
	function getPublicForJS(){
		if($this->getPublic() == true){
			return "true";
		} else {
			return "false";
		}
	}
	
	function getRequiredForJS(){
		if($this->getRequired() == true){
			return "true";
		} else {
			return "false";
		}
	}
	
	function insertToDatabase(){	 
		 // Change options array to sql format
		$id = $this->getSignupId();
		$questionString = $this->getQuestion();
		$type = $this->getType();
		$options = CommonTools::arrayToSql($this->getOptions());
		$public = CommonTools::booleanToSql($this->getPublic());
		$required = CommonTools::booleanToSql($this->getRequired());

		$sql = "INSERT INTO ilmo_questions " . 
	   		"(ilmo_id, question, type, options, public, required) " . 
	   		"VALUES " .
	   		"($id, '$questionString', '$type', '$options', " .
	   		"$public, $required)";
	   		
	   	// do query
		$this->database->doQuery($sql);
	}
	
	function equals($question){
		if(!is_a($question, "Question")){
			$this->debugger->error("Parameter must be Question class", "equals");
		}
		
		if($this->getId() == $question->getId()
			&& $this->getOptions() == $question->getOptions()
			&& $this->getPublic() == $question->getPublic()
			&& $this->getQuestion() == $question->getQuestion()
			&& $this->getRequired() == $question->getRequired()
			&& $this->getSignupId() == $question->getSignupId()
			&& $this->getType() == $question->getType()){
				return true;
		} else {
			return false;
		}
	}
}
 
?>
