<?php

/* Requirements */ 
require_once("classes/Configurations.php");
require_once("classes/Page.php");
require_once("classes/SignupGadgets.php");
require_once("classes/Debugger.php");
require_once("classes/SignupGadget.php");
require_once("classes/CommonTools.php");

/* Implementations of the most critical classes */
$configurations		= new Configurations();
$page				= new Page(1);
$debugger			= new Debugger();
$database			= new Database();

/* The code */
$signupGadgets = new SignupGadgets();
$signupGadgets->selectOpenGadgetsOrClosedDuringLastDays(30);

// Get all selected gadgets to array
$signupGadgets_array = $signupGadgets->getSignupGadgets();

$page->addContent("<h3 id=\"answers-title\">Katso ilmoittautumisten vastauksia</h3>");

// Print table headers
$page->addContent("<table id=\"signup-gadgets\">");
$page->addContent("<tr id=\"signup-header-row\">");
$page->addContent("<th id=\"signup-name-header\">Ilmo</th><th>Avautuu</th><th>Sulkeutuu</th><th>Tila</th>");
$page->addContent("</tr>");

foreach($signupGadgets_array as $gadget){
	$page->addContent("<tr class=\"answer-row\">");
	$page->addContent("<td class=\"signup-name\"><a href=\"" . $configurations->webRoot . "showanswers/".$gadget->getId()."\">".$gadget->getTitle()."</td>");
	$page->addContent("<td class=\"signup-opens\">".$gadget->getOpeningTime()."</td>");
	$page->addContent("<td class=\"signup-closes\">".$gadget->getClosingTime()."</td>");
		
	if($gadget->isOpen()){
		$page->addContent("<td class=\"open-close-state\"><span class=\"signup-open\">Auki</span></td>");
	} else if($gadget->willBeOpened()){
		$page->addContent("<td class=\"open-close-state\"><span class=\"signup-not-yet-open\">Ei vielä avautunut</td>\n");
	} else {
		$page->addContent("<td class=\"open-close-state\"><span class=\"signup-closed\">Sulkeutunut</td>\n");
	}
	
	$page->addContent("</tr>");
	
}

$page->addContent("</table>");

$page->printPage();

?>