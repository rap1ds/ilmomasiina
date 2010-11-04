<?php

/* Requirements */ 
require_once("classes/Configurations.php");
require_once("classes/Page.php");
require_once("classes/Database.php");
require_once("classes/Debugger.php");
require_once("classes/SignupGadget.php");
require_once("classes/CommonTools.php");

/* Implementations of the most critical classes */
$configurations		= new Configurations();
$page				= new Page(2);
$debugger			= new Debugger();
$database			= new Database();

/* The code */
// FIXME This should be post? Can make some annoying damage if some dummhead 
// changes it
$signupId = $request->getSignupId();

$signupGadget = SignupGadget::createSignupGadgetFromPost($signupId);

$signupGadget->updateToDatabase();

// FIXME It would be great to display some short message to user that the 
// update was done successfully
header("Location: " . $configurations->webRoot . "admin");
