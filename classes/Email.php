<?php

/**
 * Luokka, joka kuvaa sähköpostiosoitetta. Tärkein toiminto on tuo osoitteen 
 * validointi.
 *
 * @author Mikko Koski (mikko.koski@tkk.fi)
 */

class Email{

	var $debugger;
	var $address;

	function Email($address){
		// Initializes the debugger
		CommonTools::initializeCommonObjects($this);

		if(!Email::isValidEmail($address)){
			$this->debugger->error("The email address is invalid");
		} else {
			$this->address = $address;
		}
	}

	function getAddress(){
		return $this->address;
	}


	/**
	 * Tarkastaa email-osoitteen oikeellisuuden. Napsastu Dave Childin 
	 * tekemästä koodipätkästä (http://www.phpit.net/code/valid-email/)
	 * 
	 * @param string $email email-osoite
	 * @param boolean onko email-osoite validi
	 */
	static function isValidEmail($email) {
		// First, we check that there's one @ symbol, and that the lengths are right
		if (!ereg("^[^@]{1,64}@[^@]{1,255}$", $email)) {
			// Email invalid because wrong number of characters in one section, or wrong number of @ symbols.
			return false;
		}

		// Split it into sections to make life easier
		$email_array = explode("@", $email);
		$local_array = explode(".", $email_array[0]);
		for ($i = 0; $i < sizeof($local_array); $i++) {
			 if (!ereg("^(([A-Za-z0-9!#$%&#038;'*+/=?^_`{|}~-][A-Za-z0-9!#$%&#038;'*+/=?^_`{|}~\.-]{0,63})|(\"[^(\\|\")]{0,62}\"))$", $local_array[$i])) {
				return false;
			}
		}	
		if (!ereg("^\[?[0-9\.]+\]?$", $email_array[1])) { // Check if domain is IP. If not, it should be valid domain name
			$domain_array = explode(".", $email_array[1]);
			if (sizeof($domain_array) < 2) {
					return false; // Not enough parts to domain
			}
			for ($i = 0; $i < sizeof($domain_array); $i++) {
				if (!ereg("^(([A-Za-z0-9][A-Za-z0-9-]{0,61}[A-Za-z0-9])|([A-Za-z0-9]+))$", $domain_array[$i])) {
					return false;
				}
			}
		}
		return true;
	} 

}
