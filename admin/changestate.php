<?php

/* Requirements */ 
require_once("../classes/Configurations.php");
require_once("../classes/Page.php");
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
$signupGadget = new SignupGadget($signupId);

if($signupGadget->isOpen()){
	$signupGadget->setClosingTime(mktime());
	$signupGadget->updateToDatabase();
	header("Location: index.php");
} else if($signupGadget->isClosed()){
	// TODO implement open again feature
} else {
	$signupGadget->setOpeningTime(mktime());
	$signupGadget->updateToDatabase();
	header("Location: index.php");
}
?>