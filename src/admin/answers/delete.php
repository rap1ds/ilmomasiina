<?php

/* Requirements */ 
require_once("classes/Configurations.php");
require_once("classes/Page.php");
require_once("classes/Debugger.php");
require_once("classes/Database.php");
require_once("classes/SignupGadget.php");
require_once("classes/CommonTools.php");
require_once("classes/User.php");

/* Implementations of the most critical classes */
$configurations		= new Configurations();
$page				= new Page(3);
$debugger			= new Debugger();
$database			= new Database();

/* Variables from outside */
$userId = CommonTools::GET("userid");
$signupId = $request->getSignupId();

/* The code */

$signupGadget = new SignupGadget($signupId);
$signupGadget->deleteAnswersByUserFromDatabase($userId);

header("Location: " . $configurations->webRoot . "admin/showanswers/$signupId");
