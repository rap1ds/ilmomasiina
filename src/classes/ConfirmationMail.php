<?php

require_once("ErrorReportEnabler.php");
require_once("Email.php");
require_once("SignupGadget.php");

/**
 * Luokka, jonka avulla lähetetään ilmoittautumisvahvistusviesti
 * käyttäjälle.
 *
 *
 * @author Mikko Koski (mikko.koski@tkk.fi)
 *
 * HUOM! EI vielä käytössä (13.3.2008) -mikko
 */
class ConfirmationMail{

	var $debugger;	// CommonTools::initializeCommonObjects ei toimisi, jos tämä olisi private
	
	private $message;
	private $email;
	private $signupGadget;
	private $answers;

	/**
	 * Luo uuden käyttäjälle lähetettävän vahvistusviestin
	 * @param string message Viesti käyttäjälle
	 * @param string email Käyttäjän email
	 * @param SignupGadget signupgadget Tiedot ilmosta
	 * @param array answers vastaukset masiinaan
	 */
	public function ConfirmationMail($message, Email $email, $signupGadget, $answers = null){
		CommonTools::initializeCommonObjects($this);

		$this->message = $message;
		$this->email = $email;
		$this->signupGadget = $signupGadget;
		$this->answers = $answers;

		if(!is_a($email, 'Email')){
			$this->debugger->error("email parameter must be an instance of Email class");
		}	

		// Tulostetaan ilmoittautumisen vahvistus vain jos käyttäjän id on annettu
		if($answers != null){
			$questions = $this->signupGadget->getAllQuestions();

			$this->message .= "\n\n*** Ilmoittautumisesi tiedot ***\n\n";

			foreach($answers as $answer){
				// Gets answer to question
				if(is_a($answer, "Answer")){
					$this->message .= $answer->getQuestion()->getQuestion() . ": " . $answer->getReadableAnswer() . "\n";
				} else {
					// Tähän joku virhe?
				}
			}
		}
	}

	/**
	 * Lähettää käyttäjälle vahvistusviestin ilmoittautumisesta
	 *
	 */
	public function send(){	
		$email = $this->email->getAddress();
		$subject = "Ilmoittautumisen vahvistus: ". $this->signupGadget->getTitle();
		$message = $this->message;
		$from = "From: Ilmomasiina <ilmomasiina@ik.tky.fi.invalid>";

		// Just quick testing die("Email: $email, Subject: $subject, Message: $message, $from");

		mail($email, $subject, $message, $from);
	}

	public function getMessage(){
		return $this->message;
	}
}

?>
