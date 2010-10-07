<?php
/**
 * Tämä funktio laittaa kaikki mahdolliset error viestit näkyviin.
 * Funktio on tehty debuggaustarkoitukseen sivuja siirrettäessa tml-palvelimelta 
 * otaxille. Normaalikäytössä tätä ei kannata kutsua.
 */
function enableErrorReports(){
	ini_set('display_errors','1');
	ini_set('display_startup_errors','1');
	error_reporting (E_ALL); 
}

enableErrorReports();

?>