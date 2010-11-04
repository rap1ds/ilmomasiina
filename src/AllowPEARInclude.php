<?php
/**
 * Tämä tiedosto asettaa polun osoittamaan PEARin paikalliseen
 * installaatioon siten, että PEAR-paketteja voidaan tuoda 
 * skriptiin require ja include komennoilla
 *
 * author: mikko koski
 */

// ini_set('include_path', '/usr/lib/php/PEAR' . PATH_SEPARATOR .
ini_set('include_path', dirname(__FILE__) . '/lib/MDB2-2.5.0b3' . PATH_SEPARATOR .
ini_get('include_path'));
