<?php

require_once("../classes/ErrorReportEnabler.php");
require_once("../classes/ConfirmationMail.php");
require_once("../classes/Email.php");
require_once("../classes/SignupGadget.php");
require_once("../classes/Question.php");

/* Requirements */ 
require_once("../classes/Configurations.php");
require_once("../classes/Page.php");
require_once("../classes/Debugger.php");
require_once("../classes/Database.php");
require_once("../classes/CommonTools.php");
require_once("../classes/User.php");
require_once("../classes/SignupGadget.php");
require_once("../classes/Answer.php");
require_once("../classes/UserAnswers.php");

/* Implementations of the most critical classes */
$configurations		= new Configurations();
$page				= new Page();
$debugger			= new Debugger();
$database			= new Database();

// THIS TEST DOES NOT WORK!!!

function testMailSender(){

	$signupGadget = new SignupGadget(123, "Testi-ilmo!", 
		"Testaillaan vahvistusviestin lähettämistä!", 
		1234634, 46477832, 49477832);

	$nimiQuestion = new Question("Nimi", "text", array(), true, true, 123, 555);
	$emailQuestion = new Question("Email", "email", array(), false, true, 123, 556);

	$signupGadget->addQuestion($nimiQuestion);
	$signupGadget->addQuestion($emailQuestion);

	$answers = array(999 =>
			new UserAnswers(array(
				555 => new Answer("Mikko Koski", 999, $nimiQuestion),
				556 => new Answer("mikko.koski@tkk.fi", 999, $emailQuestion)	
				),
				999,
				1) 
			);

	$signupGadget->setAnswers($answers);

	$message = "Kiitos ilmoittautumisestasi ISOsitseille!\n"
		. "\n"
		. "Maksa sitsit pikimmiten seuraavasti:\n"
		. "Tilinumero: 15346-3568342\n"
		. "Saajan nimi: Athene\n"
		. "Hinta: 50e\n";

	$confirmationMail = new ConfirmationMail($message, new Email("mikko.koski@tkk.fi"), 123, 999);
	foreach(explode("\n", $confirmationMail->getMessage()) as $line){
		print $line . "<br />\n";
	}

	$confirmationMail->send();
	
}

testMailSender();

?>
