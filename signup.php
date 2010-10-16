<?php

/* Requirements */ 
require_once("classes/Configurations.php");
require_once("classes/Page.php");
require_once("classes/Debugger.php");
require_once("classes/SignupGadget.php");
require_once("classes/Database.php");
require_once("classes/CommonTools.php");
require_once("classes/SignupGadgetAnswerFormater.php");

/* Implementations of the most critical classes */
$configurations		= new Configurations();
$page				= new Page(1);
$debugger			= new Debugger();
$database			= new Database();

/* The code */

// Chech the id
$signupid = $request->getSignupId();
$sort = CommonTools::GET('sort');

if($signupid == null || !is_int(intval($signupid)) || $signupid < 0){
	// Id is not an 
	header("Location: " . $configurations->webRoot);
}

// Create gadget and get the data from database
$signupGadget = new SignupGadget($signupid);
$signupGadget->sortAnswers($sort);

// Prints title and description
$page->addContent("<div id=\"signup-info\">");
$page->addContent("<h3 id=\"signup-title\"><span>" . $signupGadget->getTitle() . "</span></h3>");
$page->addContent("<p id=\"signup-description\"><span>" . CommonTools::newlineToBr($signupGadget->getDescription()) . "</span></p>");

// Check the state of signup (open/close/not yet open)
if($signupGadget->isOpen()){

	$page->addContent("<p id=\"signup-open\"><span>Ilmoittautuminen on auki</span></p>");
	$page->addContent("</div>");
	
	$page->addContent("<form id=\"signup-button-form\" method=\"get\" action=\"" . $configurations->webRoot . "queue/" . $signupid . "\">");
	// $page->addContent("<input type=\"hidden\" name=\"signupid\" value=\"$signupid\" />");
	$page->addContent("<input id=\"signup-button\" value=\"Ilmoittaudu\" type=\"submit\" />");
	$page->addContent("</form>");
	
	$page->addContent("<div id=\"answers-container\">");
	$page->addContent(SignupGadgetAnswerFormater::getAnswersInPrintableFormat($signupGadget));
	$page->addContent("</div>");

} else if ($signupGadget->isClosed()){
	$page->addContent("<p class=\"signup-close\"><span>Ilmoittautuminen on sulkeutunut</span></p>");
	$page->addContent("</div>");

	$page->addContent(SignupGadgetAnswerFormater::getAnswersInPrintableFormat($signupGadget));

} else {
	$page->addContent("<p class=\"signup-not-yet-open\"><span>Ilmoittautuminen avautuu " . $signupGadget->getReadableOpeningTime() ."</span></p>");
	
	$page->addContent("<p class=\"questions-on-signup-info\">Ilmoittautumisessa tullaan kysymään seuraavat kysymykset: ");
	$page->addContent("<ul>");
	foreach($signupGadget->getAllQuestions() as $question){
		$questionString = "";
		$questionString .= $question->getQuestion();
		$type = $question->getType();
		if($type == "radio" || $type == "checkbox" || $type == "dropdown"){
			$questionString .= " (" . CommonTools::arrayToReadableFormat($question->getOptions()) . ")";
		}
		$page->addContent("<li>$questionString</li>");
	}
	$page->addContent("</ul>");
	$page->addContent("</div>");
	
}



$page->printPage();

/* Functions */