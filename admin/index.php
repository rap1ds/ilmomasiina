<?php

/* Requirements */
require_once("classes/ErrorReportEnabler.php");
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

// Check if the search was performed
$searchString = CommonTools::GET('search');
$year = CommonTools::GET('year');

// Is the search performed?
if($searchString != null && $searchString != ""){
	$debugger->debug("Searching signup gadgets using search string $searchString", "index.php");
	$signupGadgets->selectSearchSignupGadget($searchString);
} 

// Is the year specified?
else if($year != null && $year != "") {
	$signupGadgets->selectSignupGadgetsByYear($year);
}

// If not, do the normal query
else {
	$signupGadgets->selectOpenGadgetsOrClosedDuringLastDays(7);
}

// Get all selected gadgets to array
$signupGadgets_array = $signupGadgets->getSignupGadgets();

// Print table headers
$page->addContent("<table id=\"signup-gadgets\">");
$page->addContent("<tr id=\"signup-header-row\">");
$page->addContent("<th id=\"signup-name-header\">Ilmo</th><th>Avautuu</th><th>Sulkeutuu</th><th>Tila</th><th>Muokkaa</th><th>Poista</th><th>Muuta tilaa</th>");
$page->addContent("</tr>");

foreach($signupGadgets_array as $gadget){
	$page->addContent("<tr class=\"answer-row\">");
	$page->addContent("<td class=\"signup-name\"><a href=\"showanswers/".$gadget->getId()."\">".$gadget->getTitle()."</td>");
	$page->addContent("<td class=\"signup-opens\">".$gadget->getOpeningTime()."</td>");
	$page->addContent("<td class=\"signup-closes\">".$gadget->getClosingTime()."</td>");
		
	if($gadget->isOpen()){
		$page->addContent("<td class=\"open-close-state\"><span class=\"signup-open\">Auki</span></td>");
	} else if($gadget->willBeOpened()){
		$page->addContent("<td class=\"open-close-state\"><span class=\"signup-not-yet-open\">Ei vielä avautunut</td>\n");
	} else {
		$page->addContent("<td class=\"open-close-state\"><span class=\"signup-closed\">Sulkeutunut</td>\n");
	}
	
	$page->addContent("<td class=\"signup-edit\"><a href=\"edit.php?signupid=".$gadget->getId()."\">[muokkaa]</a></td>");
	$page->addContent("<td class=\"signup-edit\"><a href=\"delete.php?signupid=".$gadget->getId()."\">[poista]</a></td>");
	
	$changeStateText = "";
	if($gadget->isOpen()){
		$changeStateText = "[sulje]";
	} else if($gadget->isClosed()){
		// TODO Implement this feature  $changeStateText = "avaa uudelleen";
	} else {
		$changeStateText = "[avaa]";
	}
	
	$page->addContent("<td class=\"signup-change-state\"><a href=\"changestate.php?signupid=".$gadget->getId()."\">$changeStateText</a></td>");
	
	$page->addContent("</tr>");
	
}

$page->addContent("</table>");

// Searchbox
$page->addContent("<br /><p><strong>Hae ilmoja</strong></p>");
$page->addContent("<form method=\"get\">");
$page->addContent("<input type=\"text\" name=\"search\">");
$page->addContent("<input type=\"submit\" value=\"Hae\">");
$page->addContent("</form><br /><br />");



$page->addContent("<p><strong>Näytä ilmot: </strong> <a href=\"?\">Uusimmat</a> ");

foreach($signupGadgets->getAllSignupGadgetYears() as $year){
	$page->addContent("<a href=\"?year=$year\">$year</a> ");
}

$page->addContent("</p><br />");

$page->addContent("<p><strong><a href=\"new\"> >> Luo uusi ilmomasiina </a></strong></p>");

$page->printPage();

?>
