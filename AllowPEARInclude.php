<?php
/**
 * Tämä tiedosto asettaa polun osoittamaan PEARin paikalliseen
 * installaatioon siten, että PEAR-paketteja voidaan tuoda 
 * skriptiin require ja include komennoilla
 *
 * author: mikko koski
 */

ini_set('include_path', '/usr/lib/php/PEAR' . PATH_SEPARATOR . 
ini_get('include_path'));
?>
