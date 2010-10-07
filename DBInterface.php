<?php

/**
 * Tässä tiedostossa hoidetaan tietokantayhteydenmuodostaminen, sekä 
 * tietokantakyselyt. Aiemmin (tml:n aikaan eli ennen 2007) Athenen sivut 
 * pohjautuivat pääosin PostgreSQL tietokantaan. Tämä DBInterface tiedosto 
 * sai siis alkunsa kun sivuja siirrettiin otaxille, jossa ei ollut kuin
 * MySQL kanta.
 *
 * Tietokantajutut on toteutettu Pear-paketin MDB2 paketilla. Kannattaa
 * jatkossakin käyttää Pear-paketteja jos niistä vain jotain hyötyä on 
 * sillä se säästää pyörän uudelleen keksimistä
 *
 * MDB2 dokumentaatio: 
 * http://pear.php.net/manual/en/package.database.mdb2.php 
 *
 * created: 28.10.2007
 * author: Mikko Koski (mikko.koski@tkk.fi)
 */

define("DBTYPE", "mysql");    // Tietokannan tyyppi (esim mysql, pgsql)
define("USERNAME", "");       // Kannan käyttäjänimi
define("PASSWORD", "");       // Salasana
define("HOST", "localhost");  // Osoite tietokannan hostiin
define("DATABASE", "");       // Tietokannan nimi

define("DEBUG", "1");

// ----- ÄLÄ MUOKKAA TÄSTÄ ETEENPÄIN JOSSET TIEDÄ MITÄ TEET ------ ////////////
 

// Haetaan tarvittava PEAR-paketti
// require_once 'AllowPEARInclude.php';
require_once 'MDB2.php';

$dsn = array(
    'phptype'  => DBTYPE,
    'username' => USERNAME,
    'password' => PASSWORD,
    'hostspec' => HOST,
    'database' => DATABASE,
);

$options = array(
    'debug' => DEBUG,
	 'log_line_break' => "\n\t"
);

$conn =& MDB2::factory($dsn, $options);

if (PEAR::isError($conn)) {
    // FIXME Vaihda tähän jotain muuta
    die($conn->getMessage());
}

/**
 * Funktio tietokantakutsun tekemiseen
 */
function query($conn, $query_string){
  $res =& $conn->query($query_string);

  // Check if query failed
  if(PEAR::isError($res)){
    print $res->getDebugInfo();
    die($res->getMessage());
  } else {
    return $res;
  }
}
