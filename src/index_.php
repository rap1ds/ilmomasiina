<?php

/* Requirements */ 
require_once("classes/Configurations.php");
require_once("classes/Page.php");
require_once("classes/SignupGadgets.php");
require_once("classes/Debugger.php");
require_once("classes/SignupGadget.php");

/* Implementations of the most critical classes */
$configurations		= new Configurations();
$page				= new Page();
$debugger			= new Debugger();
$database			= new Database();

/* The code */
$signupGadgets = new SignupGadgets();
$signupGadgets->selectOpenGadgetsOrClosedDuringLastDays(30);

// Get all selected gadgets to array
$signupGadgets_array = $signupGadgets->getSignupGadgets();

// Print table headers
$page->addContent("<table class=\"signupGadgetsTable\">");
$page->addContent("<tr>");
$page->addContent("<th>Ilmo</th><th>Avautuu</th><th>Sulkeutuu</th><th>Tila</th>");
$page->addContent("</tr>");

foreach($signupGadgets_array as $gadget){
	$page->addContent("<tr>");
	$page->addContent("<td><a href=\"queue.php?id=".$gadget->getId()."\">".$gadget->getTitle()."</td>");
	$page->addContent("<td>".$gadget->getOpeningTime()."</td>");
	$page->addContent("<td>".$gadget->getClosingTime()."</td>");
	
	if($gadget->isOpen()){
		$page->addContent("<td><span class=\"signUpOpen\">Auki</span></td>");
	} else if($gadget->willBeOpened()){
		$page->addContent("<td><span class=\"signUpWillBeOpened\">Ei vielä avautunut</td>\n");
	} else {
		$page->addContent("<td><span class=\"signUpClosed\">Sulkeutunut</td>\n");
	}
	
	$page->addContent("</tr>");
	
}
$page->addContent("</table>");

$page->printPage();

?>
