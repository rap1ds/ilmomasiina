<?php

class CsvFormater{
	
	function getAnswersInCsvFormat($signupGadget){
		$answersByUsersArray = $signupGadget->getAllAnswersByUsers();
		$questions = $signupGadget->getAllQuestions();
		
		// Print questions
		print "Sija;";
		foreach($questions as $question){
			print $question->getQuestion() . ";";
		}
		
		print "\n";
		
		foreach($answersByUsersArray as $userId => $answersByUser){
		
			// Unconfirmed?
			if($answersByUser == null){
				print "-\r\n"; // Adds windows style line delimeter
				continue;
			} 
		
			$answerRow = "";
			$answerRow .= $answersByUser->getPosition() . ";";
		
			// Print answers
			foreach($questions as $questionId => $question){

						$answerObject = $answersByUser->getAnswerToQuestion($questionId);
					
						$answerString = "";
						if($question->getType() == "checkbox"){
							$answerString = CommonTools::arrayToReadableFormat($answerObject->getAnswer());
						} else {
							$answerString = $answerObject->getAnswer();
						}
					
						$answerString = html_entity_decode($answerString, ENT_QUOTES);
					
						// Remove new line characters
						$answerString = stripslashes(str_replace("\r\n", "", $answerString));	// Windows
						$answerString = stripslashes(str_replace("\n", "", $answerString));	// Unix
						
						// Because semicolon is the line delimeter, rudely replace all the semicolons to commas
						$answerString = stripslashes(str_replace(";", ",", $answerString));
						
						$answerRow .= $answerString . ";";
			}
			// Remove the last semicolon
			$answerRow = substr($answerRow, 0, -1);
		
			print "$answerRow\r\n"; // Adds windows style line delimeter
		}
	}
}
?>
