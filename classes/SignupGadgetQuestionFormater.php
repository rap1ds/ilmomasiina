<?php

class SignupGadgetQuestionFormater{
	
	function getQuestionsInPrintableFormat($signupGadget, $userId = -1){
		$debugger = CommonTools::getDebugger();
		
		// Check parameter
		if(!is_a($signupGadget, "SignupGadget")){
			$debugger->error("Parameter must be a SignupGadget", "getQuestionsInPrintableFormat");
		}
		
		$questions = $signupGadget->getAllQuestions();	
	
		$debugger->debug("Question count: " . count($questions), "getQuestionsInPrintableFormat");
		$formatedQuestions = "";
		
		foreach($questions as $questionId => $question){
			$answerObject = null;
			if($userId >= 0){
				$answerObject = $signupGadget->getUserAnswerToQuestion($question, $userId);
			}
			
			// Gets answer
			$value = null;
			if($answerObject != null){
				$value = $answerObject->getAnswer();
			}
			$debugger->debug("Question type: " . $question->getType(), "getQuestionsInPrintableFormat");
			switch ($question->getType()) {
				case "text":
					$formatedQuestions .= SignupGadgetQuestionFormater::formatQuestionText($question, $value);
					break;
				case "email":
					$formatedQuestions .= SignupGadgetQuestionFormater::formatQuestionEmail($question, $value);
					break;
				case "textarea":
					$formatedQuestions .= SignupGadgetQuestionFormater::formatQuestionTextarea($question, $value);
					break;
				case "checkbox":
					$formatedQuestions .= SignupGadgetQuestionFormater::formatQuestionCheckbox($question, $value);
					break;
				case "radio":
					$formatedQuestions .= SignupGadgetQuestionFormater::formatQuestionRadio($question, $value);
					break;
				case "dropdown":
					$formatedQuestions .= SignupGadgetQuestionFormater::formatQuestionDropdown($question, $value);
					break;
				default:
					$this->debugger->error("Wrong typed question", "getQuestionsInPrintableFormat");
			}
		}
		
		return $formatedQuestions;
	}
	
	function formatQuestionText($question, $value = null){
		// Format value
		if($value == null){
			$value = "";
		}
		// Take care of entity coding
		// $value = htmlentities($value);
		
		$return = "";
		$return .= "<div class=\"question-container\">";
		$return .= "<p class=\"question-label\"><span>".$question->getQuestion()."</span>";
		if($question->getRequired()){ 
			$return .= " <span class=\"required\">*</span>"; 
		}
		$return .= "</p>";
		$return .= "<input class=\"question-text\" value=\"$value\" type=\"text\" name=\"".$question->getId()."\" />";
		$return .= "</div>";
		
		return $return;
	}

	function formatQuestionEmail($question, $value = null){
				// Format value
		if($value == null){
			$value = "";
		}
		// Take care of entity coding
		// $value = htmlentities($value);
		
		$return = "";
		$return .= "<div class=\"question-container\">";
		$return .= "<p class=\"question-label\"><span>".$question->getQuestion()."</span>";
		if($question->getRequired()){ 
			$return .= " <span class=\"required\">*</span>"; 
		}
		$return .= "</p>";
		$return .= "<input id=\"email\" class=\"question-text\" value=\"$value\" type=\"text\" onchange=\"emailChanged(this)\" name=\"".$question->getId()."\" />";
		$return .= " <span id=\"invalidemail\" style=\"color: red\"></span>"; 
		$return .= "</div>";
		
		return $return;
	}
	
	function formatQuestionTextarea($question, $value = null){
		// Format value
		if($value == null){
			$value = "";
		}
		// Take care of entity coding
		// $value = htmlentities($value);
		
		// FIXME ampersand to ampersand double coding
		
		$return = "";
		$return .= "<div class=\"question-container\">";
		$return .= "<p class=\"question-label\"><span>".$question->getQuestion()."</span>";
		if($question->getRequired()){ 
			$return .= " <span class=\"required\">*</span>"; 
		}
		$return .= "</p>";
		$return .= "<textarea class=\"question-textarea\" name=\"".$question->getId()."\">$value</textarea>";
		$return .= "</div>";
		
		return $return;
	}
	
	function formatQuestionRadio($question, $value = null){
		// Take care of entity coding
		$value = htmlentities($value);
		
		$return = "";
		$return .= "<div class=\"question-container\">";
		$return .= "<p class=\"question-label\"><span>".$question->getQuestion()."</span>";
		if($question->getRequired()){ 
			$return .= " <span class=\"required\">*</span>"; 
		}
		$return .= "</p>";

		$debugger = CommonTools::getDebugger();
		$debugger->debugVar($question->getOptions(), "options", "formatQuestionRadio");

		foreach($question->getOptions() as $option){
			$selected = "";
			if($option == $value){
				$selected = "checked=\"checked\"";
			}
			$return .= "<p class=\"question-radio-label\"><input class=\"question-radio\" type=\"radio\" name=\"".$question->getId()."\" size=\"30\" value=\"$option\" $selected />$option</p>";
		}
		$return .= "</div>";
		
		return $return;
	}
	
	function formatQuestionCheckbox($question, $value){
		
		$return = "";
		$return .= "<div class=\"question-container\">";
		$return .= "<p class=\"question-label\"><span>".$question->getQuestion()."</span>";
		if($question->getRequired()){ 
			$return .= " <span class=\"required\">*</span>"; 
		}
		$return .= "</p>";

		$i = 0;
		foreach($question->getOptions() as $option){
			$selected = "";
			
			$debugger = CommonTools::getDebugger();
			// $debugger->debug("Option: $option, inArray: " . in_array($option, $value), "questionCheckbox");
			$debugger->debugVar($value, "value", "checkBox");
			
			if(is_array($value) && in_array($option, $value)){
				$selected = "checked=\"checked\"";
			}
			
			$option = htmlentities($option);
			
			$return .= "<p class=\"question-checkbox-label\"><input class=\"question-checkbox\" type=\"checkbox\" name=\"".$question->getId()."-$i\" size=\"30\" value=\"$option\" $selected />$option</p>";
			$i++;
		}
		$return .= "</div>";
		
		return $return;
	}
	
	function formatQuestionDropdown($question, $value){
		$return = "";

		$return .= "<div>";
		$return .= "<p class=\"question-label\"><span>".$question->getQuestion()."</span>";
		if($question->getRequired()){ 
			$return .= " <span class=\"required\">*</span>"; 
		}
		$return .= "</p>";
		
		$return .= "<select class=\"question-dropdown\" name=\"".$question->getId()."\"/>";

		foreach($question->getOptions() as $option){
			$selected = "";
			if($option == $value){
				$selected = " selected=\"selected\"";
			}
			
						// Take care of entity coding
			$option = htmlentities($option);
			
			$return .= "  <option $selected>$option</option>\n";
		}

		$return .= "</select>\n";
		$return .= "</div>";
		
		return $return;
	}
}
 
?>

