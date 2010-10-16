<?php

/* Requirements */ 
require_once("classes/Configurations.php");
require_once("classes/Page.php");
require_once("classes/SignupGadgets.php");
require_once("classes/Debugger.php");
require_once("classes/SignupGadget.php");
require_once("classes/CommonTools.php");
require_once("classes/SignupGadgetAnswerFormater.php");

/* Implementations of the most critical classes */
$configurations		= new Configurations();
$page				= new Page(2);
$debugger			= new Debugger();
$database			= new Database();

// Create gadget and get the data from database
$signupId = $request->getSignupId();
$sort = CommonTools::GET("sort");
$signupGadget = new SignupGadget($signupId);
$signupGadget->sortAnswers($sort);

// Prints title and description
$page->addContent("<h1>" . $signupGadget->getTitle() . "</h1>");
$page->addContent("<i>" . CommonTools::newlineToBr($signupGadget->getDescription()) . "</i>");

// Check the state of signup (open/close/not yet open)
if($signupGadget->isOpen()){	
	$page->addContent("<p class=\"signupOpen\">Ilmoittautuminen on auki</p>");
	$page->addContent(SignupGadgetAnswerFormater::getAnswersInPrintableFormat($signupGadget, true));
	$page->addContent("<p class=\"csv-output\"><a href=\"" . $configurations->webRoot . "csvoutput/$signupId\">Vastaukset csv-muodossa</a></p>");
} else if ($signupGadget->isClosed()){
	$page->addContent("<p class=\"signupClosed\">Ilmoittautuminen on sulkeutunut</p>");
	$page->addContent(SignupGadgetAnswerFormater::getAnswersInPrintableFormat($signupGadget, true));
	$page->addContent("<p class=\"csv-output\"><a href=\"" . $configurations->webRoot . "csvoutput/$signupId\">Vastaukset csv-muodossa</a></p>");
} else {
	$page->addContent("<p class=\"signupNotYetOpen\">Ilmoittautuminen on avautuu " . $signupGadget->getReadableOpeningTime() ."</p>");
}

$page->printPage();