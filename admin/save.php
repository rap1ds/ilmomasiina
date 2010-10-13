<?php

/* Requirements */ 
require_once("classes/Configurations.php");
require_once("classes/Page.php");
require_once("classes/Debugger.php");
require_once("classes/SignupGadget.php");
require_once("classes/CommonTools.php");

/* Implementations of the most critical classes */
$configurations		= new Configurations();
$page						= new Page(1);
$debugger				= new Debugger();
$database				= new Database();

/* The code */
$signupGadget = SignupGadget::createSignupGadgetFromPost();
$signupGadget->insertToDatabase();

$password = $signupGadget->getPassword();
$linkToAnswers = $configurations->webRoot . "answers/";

$page->addContent("<h3 id=\"successful\">Ilmomasiina luotiin onnistuneesti</h3>");
$page->addContent("<p>Ulkopuoliset käyttäjät voivat tarvittaessa katsoa ilmoittautumisen " .
		"vastauksia osoitteesta <a href=\"$linkToAnswers\">$linkToAnswers</a> " .
		"masiinakohtaisella salasanalla. </p>");
$page->addContent("<p>Salasana tälle masiinalle on <strong>$password</strong>");
$page->addContent("<p><a href=\"". $configurations->webRoot . "\">Palaa etusivulle</a></p>");
$page->printPage();

?>
