<?php

class SignupGadgetAnswerFormater{
	
	function getAnswersInPrintableFormat($signupGadget, $adminMode = false){
		$debugger = CommonTools::getDebugger();
		
		// Check parameter
		if(!is_a($signupGadget, "SignupGadget")){
			$debugger->error("Parameter must be a SignupGadget", "getAnswersInPrintableFormat");
		}
		
		// If signup is a private signup (none of the questions is public)
		$debugger->debug("isPrivate: " . $signupGadget->isPrivate(), "getAnswersInPrintableFormat");
		if($signupGadget->isPrivate() && $adminMode == false){
			return "<p class=\"private-signup\">Tämän ilmomasiinan vastaukset eivät ole julkisia</p>";
		}
		
		$answers = null;
		$questions = null;
		$questionCount = 0;
		if($adminMode){
			$questions = $signupGadget->getAllQuestions();
			$questionCount = $signupGadget->getAllQuestionCount();
		} else {
			$questions = $signupGadget->getPublicQuestions();
			$questionCount = $signupGadget->getPublicQuestionCount();
		}
		
		$answers = $signupGadget->getAllAnswersByUsers();
		$debugger->debug("Answer count (public: $adminMode): " . $questionCount, "getAnswersInPrintableFormat");
		$formatedAnswers = "";
 		
 		$position = 1;
 		
 		$formatedAnswers .= SignupGadgetAnswerFormater::formatHeader($questions, $signupGadget->getId(), $adminMode);
 		
 		$debugger->debugVar($answers, "answers", "getAnswersInPrintableFormat");
 		
 		// If there is no answers, no need to print any
 		if(count($answers) > 0){
 			foreach($answers as $id => $answer){
 				if($answer != null){
 					// Confirmed question
 					$formatedAnswers .= SignupGadgetAnswerFormater::formatConfirmedAnswerRow($answer, $questions, $position, $adminMode);
 				} else {
 					// Unconfirmed question
 					$formatedAnswers .= SignupGadgetAnswerFormater::formatUnconfirmedAnswerRow($questionCount, $adminMode);
 				}
 				$position++;
 			}
 		}
 		
 		$formatedAnswers .= SignupGadgetAnswerFormater::formatFooter();
 		
 		return $formatedAnswers;
	}
	
	function formatHeader($questions, $signupId, $adminMode){
		
		$return  = "<table id=\"answers-table\">\n";
		$return .= "<thead>\n";
		$return .= "<tr id=\"answers-header-row\">";
		$return .= "<th id=\"answer-position-header\"><a href=\"".$_SERVER['PHP_SELF']."?signupid=".$signupId."\">Sija</a></th>";
		foreach($questions as $question){
			$return .= "<th class=\"question-header\"><a href=\"".$_SERVER['PHP_SELF']."?signupid=".$signupId."&sort=".$question->getId()."\">".$question->getQuestion()."</a></th>";
		}
		if($adminMode){
			$return .= "<th id=\"answer-edit-header-empty\"></th>";
			$return .= "<th id=\"answer-delete-header-empty\"></th>";
		}
		
		$return .= "</tr>\n";
		$return .= "</thead>\n";
		$return .= "<tbody>\n";
		return $return;
	}
	
	function formatConfirmedAnswerRow($answersByUser, $questions, $position, $adminMode){
		$rowClass = SignupGadgetAnswerFormater::getRowClass();
		
		$return  = "<tr class=\"$rowClass\">";
		
		$debugger = CommonTools::getDebugger();
		$debugger->debugVar($answersByUser, "answerByUser", "formatConfirmedAnswerRow");
		
		$return .= "<td class=\"answer-position\">".$answersByUser->getPosition()."</td>";
		$lastAnswer = null;		// Could be any answer, not only last answer
		
		foreach($questions as $question){
			// Gets answer to questin
			$answerObject = $answersByUser->getAnswerToQuestion($question->getId());
			if(is_a($answerObject, "Answer")){
				$return .= "<td class=\"answer\">".$answerObject->getReadableAnswer()."</td>";
				$lastAnswer = $answerObject;
			} else {
				// Joku virheilmotus tähänkö?
			}
		}
		
		if($adminMode && $lastAnswer != null){
			$return .= "<td class=\"edit-answer\"><a href=\"edit.php?userid=".$lastAnswer->getUserId()."&signupid=".$lastAnswer->getSignupId()."\">[muokkaa]</a></td>";
	 		$return .= "<td class=\"delete-answer\"><a href=\"delete.php?userid=".$lastAnswer->getUserId()."&signupid=".$lastAnswer->getSignupId()."\">[poista]</a></td>";
		}
	 	$return .= "</tr>\n";
		return $return;
	}
	
	function formatUnconfirmedAnswerRow($count, $adminMode){
		$rowClass = SignupGadgetAnswerFormater::getRowClass();
	
		$return  = "<tr class=\"$rowClass\">";
		$return .= "<td class=\"answer-position\">-</td>";
		for($i = 0; $i < $count; $i++){
			$return .= "<td class=\"unconfirmed-answer-empty\"></td>";
		}
		if($adminMode){
			$return .= "<td class=\"unconfirmed-answer-edit-empty\"></td>";
	 		$return .= "<td class=\"unconfirmed-answer-delete-empty\"></td>";
		}
		$return .= "</tr>\n";
		return $return;
	}
	
	function formatFooter(){
		$return  = "</tbody>\n";
		$return .= "</table>\n";
		
		return $return;
	}
	
	function getRowClass(){
		STATIC $count = 0;
		$count++;
		if($count % 2 == 0){
			return "answer-row-dark";
		} else {
			return "answer-row-light";
		}
	}
}
	
?>
