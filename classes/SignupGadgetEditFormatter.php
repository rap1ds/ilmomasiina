<?php 
 
class SignupGadgetEditFormater{
	
	// FIXME Clean up this one! The action of the form should not be printed from the class
	// Check how the other gadget's are printed
	function getSignupGadgetEditInPrintableFormat($signupGadget = null, $edit = false){

                $configurations = CommonTools::getConfigurations();
		$debugger = CommonTools::getDebugger();
		$page 	  = CommonTools::getPage();
		
		// Parameter check
		if(is_a($signupGadget, "SignupGadget") || $signupGadget == null){
			// ok
		} else {
			$debugger->error("Parameter must be a SignupGadget or null", "getSignupGadgetEditInPrintableFormat");
		}
		
		// sets default values
		$id 			= -1;
		$title 			= "";
		$description	= "";
		$event_date 	= -1;
		$opens 			= -1;
		$closes 		= -1;
		$sendConfirmation = false;
		$confirmationMailMessage = "";
		$questions 		= array();
                $webRoot = $configurations->webRoot;
		
		// sets signupgadget specified values if gadget is not null
		if($signupGadget != null){
			$id 			= $signupGadget->getId();
			$title 			= $signupGadget->getTitle();
			$description 	= $signupGadget->getDescription();
			$event_date 	= $signupGadget->getEventDate();
			$opens 			= $signupGadget->getOpens();
			$closes 		= $signupGadget->getCloses();
			$questions		= $signupGadget->getAllQuestions();
			$sendConfirmation	= $signupGadget->getSendConfirmationMail();
			$confirmationMailMessage = $signupGadget->getConfirmationMailMessage();
		}
		
		$output = "";
		
		$output .= SignupGadgetEditFormater::formatHeader($edit, $id, $webRoot);
		$output .= SignupGadgetEditFormater::formatTitle($title);
		$output .= SignupGadgetEditFormater::formatDescription($description);
		$output .= SignupGadgetEditFormater::formatEventDate($event_date);
		$output .= SignupGadgetEditFormater::formatOpens($opens);
		$output .= SignupGadgetEditFormater::formatCloses($closes);
		$output .= SignupGadgetEditFormater::formatQuestions($questions);
		$output .= SignupGadgetEditFormater::formatSendConfirmation($sendConfirmation);
		$output .= SignupGadgetEditFormater::formatConfirmationMailMessage($confirmationMailMessage, $sendConfirmation);		
		$output .= SignupGadgetEditFormater::formatFooter();
		
		return $output;
		
	}
	
	function formatHeader($edit, $id, $webRoot){
		$return = "";
		$return .= "<h3 id=\"new-signup-title\">Luo uusi ilmo</h1>\n";
		$return .= "<p id=\"no-check-warning\">HUOM! Masiina ei tarkistele syöttämiäsi arvojen oikeellisuutta, joten ole huolellinen</p>\n";
		if($edit){
			$return .= "<form action=\"" . $webRoot . "admin/update/$id\" method=\"post\">\n";
		} else {
			$return .= "<form action=\"" . $webRoot . "admin/save\" method=\"post\">\n";
		}
		return $return;
	}
	
	function formatTitle($value){
		$return = "";
		$return .= "<div id=\"title-container\">";
		$return .= "<p id=\"signup-title-label\">Otsikko:</p>";
		$return .= "<input id=\"signup-title\" type=\"text\" name=\"title\" value=\"$value\"><br>\n";
		$return .= "</div>";
		return $return;
	}
	
	function formatDescription($value){
		$return = "";
		$return .= "<div id=\"description-container\">";
		$return .= "<p id=\"signup-description-label\">Kuvaus:</p>\n";
		$return .= "<textarea id=\"signup-description\" name=\"description\">$value</textarea>\n";
		$return .= "</div>";
		return $return;
	}
	
	function formatDateTimeSelect($namePrefix, $title, $value = -1){
		$day 	= -1;
		$month 	= -1;
		$year 	= -1;
		$hour	= -1;
		$minutes = -1;
		
		if($value > 0){
			$date 	= getdate($value);
			$day 	= $date["mday"];
			$month 	= $date["mon"];
			$year	= $date["year"];
			$hour 	= $date["hours"];
			$minutes = $date["minutes"];
		}
		
		$debugger = CommonTools::getDebugger();
		$debugger->debug("Day $day, Month $month, Year $year, Hour $hour, Minutes $minutes", "formatDateTimeSelect");
		
		$return = "";
		$return .= "<div class=\"date-select-container\">";
		$return .= "<p class=\"date-select-title\">$title</p>\n";
		$return .= "<select name=\"".$namePrefix."day\">\n";
		$return .= SignupGadgetEditFormater::getDayOptions($day);
		$return .= "</select>\n";
		$return .= "<select name=\"".$namePrefix."month\">\n";
		$return .= SignupGadgetEditFormater::getMonthOptions($month);
		$return .= "</select>\n";
		$return .= "<select name=\"".$namePrefix."year\">\n";
		$return .= SignupGadgetEditFormater::getYearOptions($year);
		$return .= "</select>\n";
		$return .= "<span>klo: </span>\n";
		$return .= "<select name=\"".$namePrefix."hour\">\n";
		$return .= SignupGadgetEditFormater::getHourOptions($hour);
		$return .= "</select>\n";
		$return .= "<select name=\"".$namePrefix."minutes\">\n";
		$return .= SignupGadgetEditFormater::getMinuteOptions(5, $minutes);
		$return .= "</select>\n\n";
		$return .= "</div>";
		return $return;
	}
	
	function formatEventDate($value){
		return SignupGadgetEditFormater::formatDateTimeSelect("event_", "Tapahtuman ajankohta:", $value);
	}
	
	function formatOpens($value){
		return SignupGadgetEditFormater::formatDateTimeSelect("opens_", "Ilmoittautuminen aukeaa:", $value);
	}
	
	function formatCloses($value){
		return SignupGadgetEditFormater::formatDateTimeSelect("closes_", "Ilmoittautuminen sulkeutuu:", $value);
	}
	
	function formatQuestions($value, $id = -1){
		// TODO Hehe, funny bug. If many questions are made and then removed drops
		// about one pixel lower
		
		$return = "";
		$return .= "<!-- JavaScript muuttaa tämän arvoa aina kun kysymys lisätään -->\n";
		$return .= "<input id=\"question_num\" type=\"hidden\" name=\"question_num\" value=\"0\">\n";
		$return .= "<table id=\"question_table\">\n";
  		$return .= "<!-- \n";
  		$return .= "Internet Explorer vaatii toimiakseen tämän tbodyn\n";
  		$return .= "tr-elementin lisääminen suoraan table-elementin perään ei onnistu \n";
  		$return .= "IE:llä ilman tbody-elementtiä siinä välissä \n";
  		$return .= "-->\n";
  		$return .= "<tbody>\n";
  		
  		// Ok, here comes the shitty part...
  		$currentPos = 0;
		foreach($value as $question){
			// Get the questions
			$id = $question->getId();
			$questionJS = $question->getQuestionForJS();
			$typeJS = $question->getTypeForJS();
			$optionsJS = $question->getOptionsForJS();
			$publicJS = $question->getPublicForJS();
			$requiredJS = $question->getRequiredForJS();
		
			// Gets previous button if one exists
			$previousButton = null;
			if($currentPos != 0){
				$previousButton = "document.getElementById('addQuestionButton_" . ($currentPos -1) . "')";
			} else {
				$previousButton = "null";
			}
			
			if($id < 0){
				$id = "-1";
			}
		
			// Adds new questions
			$return .= "<script>createQuestionRowFromPresetValues($previousButton, '$questionJS', '$typeJS', $optionsJS, $publicJS, $requiredJS, $id);</script>\n";
			$currentPos++;
		}
		
		// Add an empty question row if no questions are set before
		if($currentPos == 0){
			$return .= "<script>createQuestionRow(null);</script>\n";
		}
  		
  		$return .= "</tbody>\n";
		$return .= "</table>\n";
		return $return;
	}

	function formatSendConfirmation($send_confirmation){
		$return = "";
		$checked = "";
		if($send_confirmation){
			$checked = "checked=\"checked\"";
		}
		// confirmationMailCheckboxChanged is defined in question_script.js
		$return = "<p><input type=\"checkbox\" name=\"send_confirmation\" value=\"true\" $checked onclick=\"confirmationMailChechboxChanged(this)\"> Lähetä vahvistusviesti ilmoittautuneille</p>";
		return $return;
	}

	function formatConfirmationMailMessage($value, $send_confirmation){
		$disabled = "";
		if(!$send_confirmation){
			$disabled = "disabled=\"disabled\"";
		}
		
		$return = "";
		$return .= "<div id=\"mailmessage-container\">";
		$return .= "<p id=\"mailmessage-label\">Vahvistusviesti:";
		// Tätä ei ehkä tarvita? $return .= "<span style=\"font-weight: normal\">(<a href=\"#\">Viestin esikatselu</a>)</span>";
		$return .= "</p>\n";
		$return .= "<textarea id=\"mailmessage\" name=\"mailmessage\" $disabled>$value</textarea>\n";
		$return .= "</div>";
		return $return;
	}
	
	function formatFooter(){
		$return = "";
		$return .= "<input type=\"submit\">\n";
		$return .= "</form>\n";
		return $return;
	}

	function getDayOptions($selected = -1){
		$selected = SignupGadgetEditFormater::checkSelected($selected, 1, 31);
		if($selected < 0){
			$selected = date('j');
		}
		$return = "";
		for($i = 1; $i <= 31; $i++){
			if($i == $selected){
				$return .= "<option selected=\"selected\" name=\"$i\">$i</option>\n";
			} else {
				$return .= "<option name=\"$i\">$i</option>\n";
			}
		}
		return $return;
	}

	function getMonthOptions($selected = -1){
		$selected = SignupGadgetEditFormater::checkSelected($selected, 1, 12);
		if($selected < 0){
			$selected = date('n');
		}
		$return = "";
		for($i = 1; $i <= 12; $i++){
			if($i == $selected){
				$return .= "<option selected=\"selected\" name=\"$i\">$i</option>\n";
			} else {
				$return .= "<option name=\"$i\">$i</option>\n";
			}
		}
		return $return;
	}

	function getYearOptions($selected = -1){
		$start = 2007;
		$end = date('Y') + 5; 		// print five years forward from current years
		
		$selected = SignupGadgetEditFormater::checkSelected($selected, $start, $end);
		if($selected < 0){
			$selected = date('Y');
		}
	
		$return = "";
		for($i = $start; $i <= $end; $i++){
			if($i == $selected){
				$return .= "<option selected=\"selected\" name=\"$i\">$i</option>\n";
			} else {
				$return .= "<option name=\"$i\">$i</option>\n";
			}
		}
		return $return;
	}


	function getHourOptions($selected = -1){
		$selected = SignupGadgetEditFormater::checkSelected($selected, 0, 23);
		if($selected < 0){
			$selected = 12; 	// sets the default value to midday
		}
	
		$return = "";
		for($i = 0; $i < 24; $i++){
			if($i == $selected){
				$return .= "<option selected=\"selected\" name=\"$i\">$i</option>\n";
			} else {
				$return .= "<option name=\"$i\">$i</option>\n";
			}
		}
		return $return;
	}

	function getMinuteOptions($interval, $selected = -1){
		// Check interval
		if($interval <= 0 || $interval >=60 ){
			die('Bad interval');
		}	
		$selected = SignupGadgetEditFormater::checkSelected($selected, 0, 60, $interval);
		if($selected < 0){
			$selected = 0;
		}
		$return = "";
		for($i = 0; $i < 60; $i = $i + $interval){
			$minute = $i;	// Printable value
			if($i < 10){
				// Add missing zero
				$minute = "0" . $minute;
			}
			if($i == $selected){
				$return .= "<option selected=\"selected\" name=\"$minute\">$minute</option>\n";
			} else {
				$return .= "<option name=\"$minute\">$minute</option>\n";
			}
		}
		return $return;
	}

	/**
	 * If interval is used, there may occur situation where user gives an 
	 * value which goes between two interval value. The method checks these 
	 * kind of situations and returns value which is rounded to the nearest 
	 * interval value
	 */
	function checkSelected($selected, $start, $end, $interval = 1){
		// check that interval is in limit
		if($selected < $start){
			return -1;
		} else if ($selected > $end){
			return -1;
		}
		
		for($i = $start; $i < $selected; $i = $i + $interval){
			if($i == $selected){
				return $selected;
			}
		}
		
		// round to the nearest interval
		if (abs($i - $interval - $selected) < abs($i - $selected)){
			return $i - $interval;
		} else {
			return $i;
		}
	}
}

?>
