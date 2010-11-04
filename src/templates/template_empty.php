<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">

<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">

<head>
<meta http-equiv="Content-Type" content="text/html;charset=iso-8859-15" />
<title><?php /* Page title */ $this->printTitle(); ?></title>
<link href="<?php $this->printDirLevel() ?>templates/template_default.css" rel="stylesheet" type="text/css" />
<?php /* Custom header for page */ $this->printHeader(); ?>
</head>

<body id="signup-gadget">
  <div id="before-container-div"></div>
  <div id="container">
    <div id="header">
      <h1 id="ilmomasiina-title"><span>Ilmomasiina 2.0</span></h1>
    </div>
    <div id="guild">
      <h2 id="athene-guild"><span>Informaatioverkostojen kilta Athene</span></h2>
    </div>
    <div id="error">
<?php /* Prints error message here */ $this->printError(); ?>
	</div>
    <div id="main">
<?php /* Prints page content */ $this->printContent(); ?>
    </div>
    <div id="contact">
      <p id="admin-email"><span>Epäonnistumisen sattuessa ota yhteys osoitteeseen <?php /* Prints address to where user should contant in case of signup failure */ $this->printAdminEmail(); ?></span></p>
    </div>
    <div id="debug">
<?php /* Prints debugging messages if debug mode is on */ $this->printDebug(); ?>
    </div>
  </div>
  <div id="after-container-div"></div>

  <div id="extra-div-1"></div>
  <div id="extra-div-2"></div>
  <div id="extra-div-3"></div>
  <div id="extra-div-4"></div>
  <div id="extra-div-5"></div>

</body>
</html>
