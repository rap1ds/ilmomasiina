<?php

/* Requirements */ 
require_once("classes/Configurations.php");
require_once("classes/Page.php");
require_once("classes/Debugger.php");
require_once("classes/Database.php");
require_once("classes/CommonTools.php");
require_once("classes/User.php");

/* Implementations of the most critical classes */
$configurations		= new Configurations();
$page				= new Page();
$debugger			= new Debugger();
$database			= new Database();

/* Cancel old signup */
$user = new User(-1);
$user->cancelUnconfirmedSignup();

header("Location: " . $configurations->webRoot);

?>