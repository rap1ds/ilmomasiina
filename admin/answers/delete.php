<?php

/* Requirements */ 
require_once("../../classes/Configurations.php");
require_once("../../classes/Page.php");
require_once("../../classes/Debugger.php");
require_once("../../classes/Database.php");
require_once("../../classes/SignupGadget.php");
require_once("../../classes/CommonTools.php");
require_once("../../classes/User.php");

/* Implementations of the most critical classes */
$configurations		= new Configurations();
$page				= new Page(2);
$debugger			= new Debugger();
$database			= new Database();

/* Variables from outside */
$userId = CommonTools::GET("userid");
$signupId = CommonTools::GET("signupid");

/* The code */

$signupId = CommonTools::GET("signupid");
$signupGadget = new SignupGadget($signupId);
$signupGadget->deleteAnswersByUserFromDatabase($userId);

header("Location: show.php?signupid=$signupId");

?>
