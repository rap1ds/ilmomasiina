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
$page				= new Page();
$debugger			= new Debugger();
$database			= new Database();

/* The code */

// Chech the id
$signupid = CommonTools::GET('signupid');
$action = CommonTools::GET('action');

if($signupid == null || !is_int(intval($signupid)) || $signupid < 0){
	// Id is not an integer
	header("Location: index.php");
}

/* Action: Confirm old signup */
if($action == "confirmold"){
	// Redirect user to confirmation form of the old signup
	$user = new User($signupid);
	header("Location: confirm.php?signupid=".$user->getOldSignupId());
} 

/* Action: Cancel old signup */
else if($action == "continueandcancelold"){
	$user = new User($signupid);
	$user->cancelUnconfirmedSignupAndRefreshSession();
	header("Location: confirm.php?signupid=".$user->getNewSignupId());
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
	
	/* BEGIN - MARATHON 2008 PUUKOTUS! Saa poistaa */
	$rajoitettuId = 173;
	$rajoitettuAika = mktime(19, 0, 0, 10, 2, 2008, 1);
	
	if(time() < $rajoitettuAika && $signupid == $rajoitettuId){
		// Ollaan ajalla, jolloin ilmo ei ole auki kaikille
		$ip = getenv("REMOTE_ADDR");
		
		if($ip != "130.233.44.37" && $ip != "130.233.44.38" && $ip != "130.233.44.36"){
			$debugger->error("Ilmoittautuminen on auki vain OLOhuoneen koneilla. Julkiseksi ilmoittautuminen muuttuu klo 19.00");
		}
	}

	/* END - MARATHON 2008 PUUKOTUS! Saa poistaa */
	
	if($user->getUnconfirmedSignupExists()){
	
		$debugger->debug("Unconfirmed signup exists", "queue.php");
	
		if($user->getUnconfirmedSignupIsNotTheSameAsThis()){
	
			$debugger->debug("Unconfirmed signup exists, but it is not this one", "queue.php");
	
			$signupgadget = new SignupGadget($user->getOldSignupId());
	
			$page->addContent("<p><b>Huom!</b> Olet ilmoittautunut jo ilmomasiinassa <b>" .
					$signupgadget->getTitle() ."</b>, muttet ole vahvistanut ilmoittautumista. " .
					"Ennen kuin voit ilmoittautua toiseen ilmomasiinaan sinun pit‰‰ vahvistaa tai peruuttaa" .
					" aikasemmat vahvistamattomat ilmoittautumiset.</p>");
			$page->addContent("<p>Valitse mit‰ haluat tehd‰:</p>");
			$page->addContent("<p> >> <a href=\"queue.php?action=continueandcancelold&signupid=".$user->getNewSignupId()."\">Peruuta aiempi vahvistamaton ilmoittautuminen ja " .
					"siirry eteenp‰in</a></p>");
			$page->addContent("<p> >> <a href=\"queue.php?action=confirmold&signupid=".$user->getOldSignupId()."\">Siirry vahvistamaan aiempi ilmoittautuminen</a></p>");	
			$page->printPage();
		} else {
			header("Location: confirm.php?signupid=".$user->getNewSignupId());
		}
	} else {
		header("Location: confirm.php?signupid=".$user->getNewSignupId());
	}
}
?>
