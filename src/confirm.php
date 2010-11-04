<?php

/* Requirements */ 
require_once("classes/Configurations.php");
require_once("classes/Page.php");
require_once("classes/Debugger.php");
require_once("classes/Database.php");
require_once("classes/SignupGadget.php");
require_once("classes/CommonTools.php");
require_once("classes/User.php");
require_once("classes/SignupGadgetQuestionFormater.php");

/* Implementations of the most critical classes */
$configurations		= new Configurations();
$page				= new Page(1);
$debugger			= new Debugger();
$database			= new Database();

/* Variables from outside */
$signupId = $request->getSignupId();

/* The code */

$signupGadget = new SignupGadget($signupId);
$user = new User($signupId);

if($user->unconfirmedSignupExists){
	if($user->unconfirmedSignupIsNotTheSameAsThis){
		$debugger->error("Olet ilmoittautunut johonkin toiseen ilmomasiinaan", "confirm.php");
	} else {
		// Nice! User is signed up to this signup form!
	}
} else {
	$debugger->error("Et ole vielä ilmoittautunut tai ilmoittautumisesi on vanhentunut. Ilmoittautumisen vahvistaminen epäonnistui.", "confirm.php");
}

$page->addHeader("<script type=\"text/javascript\" src=\"javascript/jquery.js\"></script>");
$page->addHeader("<script type=\"text/javascript\" src=\"javascript/email_validation.js\"></script>");

$page->addContent("<div id=\"signup-position-info-div\">");
$page->addContent("<p id=\"signup-position-info\"><span>Olet sijalla <strong>".$user->getPosition()."</strong>. Edessäsi on <strong>" .
		$user->getUnconfirmedSignupsBefore() . "</strong> vahvistamatonta ilmoittautumista. Ilmoittautumisaikasi oli <strong>" .
		$user->getReadableSignupTime() . "</strong> ja aikaa ilmoittautumisen vahvistamiseen sinulla on <strong>" . $user->getSignupTimeLeftInMinutes() . "</strong> minuuttia</span></p>");
$page->addContent("</div>");

$page->addContent("<div id=\"questions\">");
$page->addContent("<form id=\"questions-form\" method=\"post\" action=\"". $configurations->webRoot . "save\">");
$page->addContent("<input type=\"hidden\" name=\"signupid\" value=\"$signupId\">");
$page->addContent("<input type=\"hidden\" name=\"userid\" value=\"".$user->getId()."\">");
$page->addContent(
		SignupGadgetQuestionFormater::getQuestionsInPrintableFormat($signupGadget));
$page->addContent("<input type=\"hidden\" name=\"question_ids\" value=\"".$signupGadget->getFormatedQuestionIds()."\">");
$page->addContent("<input id=\"confirm-button\" type=\"submit\" value=\"Vahvista\">");
$page->addContent("</form>");

$page->addContent("<form id=\"cancel-form\" method=\"post\" action=\"". $configurations->webRoot . "cancel\">");
$page->addContent("<input id=\"cancel-button\" type=\"submit\" value=\"Peru ilmoittautuminen\">");
$page->addContent("</form>");
$page->addContent("</div>");

$page->printPage();

?>
