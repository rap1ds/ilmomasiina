<?php

require_once("../classes/CsvFormater.php");
require_once("../classes/Configurations.php");
require_once("../classes/Page.php");
require_once("../classes/SignupGadgets.php");
require_once("../classes/Debugger.php");
require_once("../classes/SignupGadget.php");
require_once("../classes/CommonTools.php");

/* Implementations of the most critical classes */
$configurations		= new Configurations();
$page				= new Page(2);
$debugger			= new Debugger();
$database			= new Database();

$signupId = CommonTools::GET("signupid");
$signupGadget = new SignupGadget($signupId);

header('Content-type: text/csv');
header('Content-Disposition: attachment; filename="ilmo'.$signupId.'.csv"');
print(CsvFormater::getAnswersInCsvFormat($signupGadget));
 
?>
