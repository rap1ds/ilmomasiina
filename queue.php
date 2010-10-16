<?php

/* Requirements */ 
require_once("classes/Configurations.php");
require_once("classes/Page.php");
require_once("classes/Debugger.php");
require_once("classes/SignupGadget.php");
require_once("classes/Database.php");
require_once("classes/CommonTools.php");
require_once("classes/User.php");

/* Implementations of the most critical classes */
$configurations		= new Configurations();
$page				= new Page(1);
$debugger			= new Debugger();
$database			= new Database();

/* The code */

// Chech the id
$signupid = $request->getSignupId();
$action = $request->getAction();

if($signupid == null || !is_int(intval($signupid)) || $signupid < 0){
	// Id is not an integer
	header("Location: " . $configurations->webRoot);
}

/* Action: Confirm old signup */
if($action == "confirmold"){
	// Redirect user to confirmation form of the old signup
	$user = new User($signupid);
	header("Location: " . $configurations->webRoot . "confirm/".$user->getOldSignupId());
} 

/* Action: Cancel old signup */
else if($action == "continueandcancelold"){
	$user = new User($signupid);
	$user->cancelUnconfirmedSignupAndRefreshSession();
	header("Location: " . $configurations->webRoot . "confirm/".$user->getNewSignupId());
} 

/* Action: Create fresh new session */

else {

	// Check that signup is open
	$newSignupGadget = new SignupGadget($signupid);
	$user = null;
	if($newSignupGadget->isOpen()){
		$user = new User($signupid);
	} else {
		$debugger->error("Ilmoittautuminen ei ole avoinna.", "queue.php");
	}
	
	if($user->getUnconfirmedSignupExists()){
	
		$debugger->debug("Unconfirmed signup exists", "queue.php");
	
		if($user->getUnconfirmedSignupIsNotTheSameAsThis()){
	
			$debugger->debug("Unconfirmed signup exists, but it is not this one", "queue.php");
	
			$signupgadget = new SignupGadget($user->getOldSignupId());
	
			$page->addContent("<p><b>Huom!</b> Olet ilmoittautunut jo ilmomasiinassa <b>" .
					$signupgadget->getTitle() ."</b>, muttet ole vahvistanut ilmoittautumista. " .
					"Ennen kuin voit ilmoittautua toiseen ilmomasiinaan sinun pitää vahvistaa tai peruuttaa" .
					" aikasemmat vahvistamattomat ilmoittautumiset.</p>");
			$page->addContent("<p>Valitse mitä haluat tehdä:</p>");
			$page->addContent("<p> >> <a href=\"" . $configurations->webRoot . "continueandcancelold/".$user->getNewSignupId()."\">Peruuta aiempi vahvistamaton ilmoittautuminen ja " .
					"siirry eteenpäin</a></p>");
			$page->addContent("<p> >> <a href=\"" . $configurations->webRoot . "confirmold/".$user->getOldSignupId()."\">Siirry vahvistamaan aiempi ilmoittautuminen</a></p>");
			$page->printPage();
		} else {
			header("Location: " . $configurations->webRoot . "confirm/".$user->getNewSignupId());
		}
	} else {
		header("Location: " . $configurations->webRoot . "confirm/".$user->getNewSignupId());
	}
}
