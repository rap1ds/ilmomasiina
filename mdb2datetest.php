<?php

require_once("../DBInterface.php");
require_once("../ErrorReportEnabler.php");
MDB2::loadFile("Date"); 

$sql = "SELECT (" . MDB2_Date::mdbNow() . " - " . MDB2_Date::unix2Mdbstamp(strtotime('Jan 18, 2007')) . ")";

print "<p>$sql</p>";

$result = query($conn, $sql);

print_r($result->fetchRow());

?>
