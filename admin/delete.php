<?php

/* Requirements */ 
require_once("../classes/Configurations.php");
require_once("../classes/Page.php");
require_once("../classes/SignupGadgets.php");
require_once("../classes/Debugger.php");
require_once("../classes/SignupGadget.php");
require_once("../classes/CommonTools.php");

/* Implementations of the most critical classes */
$configurations		= new Configurations();
$page				= new Page(1);
$debugger			= new Debugger();
$database			= new Database();

/* The code */
$signupId = CommonTools::GET("signupid");
$action = CommonTools::GET("action");
$signupGadget = new SignupGadget($signupId);

if($action == "confirmed"){
	$signupGadget->deleteFromDatabase();
	header("Location: index.php");
} else {
	$page->addContent("<h1>Poista ilmomasiina</h1>");
	$page->addContent("<p>Haluatko varmasti poistaa seuraavan ilmomasiinan:</p>");
	$page->addContent("<p><b>".$signupGadget->getTitle()."</b></p>");
	$page->addContent("<p>".$signupGadget->getDescription()."</p>");
	$page->addContent("<p><a href=\"delete.php?action=confirmed&signupid=$signupId\">Joo</a> <a href=\"index.php\">Ei</a></p>");
	$page->printPage();
}
?>