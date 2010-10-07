<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">

<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
<head>
	<meta http-equiv="Content-Type" content="text/html;charset=iso-8859-15" />
	<title><?php $this->printTitle(); ?></title>
	<style type="text/css">
	body, html{
		margin: 0%;
		padding: 0%;
		height: 100%;
	}
	body{
		font-family: Trebuchet MS;
		font-size: 10pt;
		backgorund-color: White;
	}
	
	.form_element_grid td{
		padding: 0.5em 1.5em;
	}
	#questions td {
		vertical-align: top;
	}
	span.required{
		font-size: 100%;
		color: Red;
	}
	
	table.answertable{
	}
	
	table.answertable th{
		background-color: #9cb6f2;
	}
	
	table.answertable tr.light{
		background-color: #eff0f2;
	}
	
	table.answertable tr.dark{
		background-color: #d8e0f2;
	}
	
	h1#logo{
		background-image: url("<?php $this->printDirLevel() ?>templates/images/ilmo_logo.png");
		margin-bottom: 5%;
		width: 335px;
		height: 42px;
	}
	
	h1#logo span{
		display: none;	
	}
	
	div#top{
		width: 100%;
		text-align: right;
		border-bottom-style: solid;
		border-bottom-width: 1px;
		border-bottom-color: #DDDDDD;
		margin-bottom: 5%;
	}
	
	h1#athenelogo{
		background-image: url("<?php $this->printDirLevel() ?>templates/images/athene.png");
		background-position: right;
		background-repeat: no-repeat;
		margin-bottom: 2%;
		margin-right: 3%;
		width: 100%;
		height: 42px;
	}
	
	h1#athenelogo span{
		display: none;	
	}
	
	th{
		text-align: left;
	}
	
	h1{
		font-size: 110%;
	}
	
	input#ilmoittaudu_button{
		margin: 2% 0%;
	}
	
	.auki{
		color: Green;
		font-weight: bold;
	}
	
	.eivielaauki{
		font-weight: bold;
	}
	
	.sulkeutunut{
		color: Red;
		font-weight: bold;
	}
	
	table#gridlayout{
		width: 100%;
	}
	
	td#center{
		width: 80%;
		padding: 0% 3% 3% 3%;
	}
	
	td#leftshadow, td#rightshadow{
		width: 10%;
		background-repeat: repeat-y;
	}

	td#leftshadow{
		background-image: url("<?php print $this->printDirLevel() ?>templates/images/bg_left.png");
		background-repeat: repeat-y;
		background-position: right;
	}
	
	td#rightshadow{
		background-image: url("<?php print $this->printDirLevel() ?>templates/images/bg_right.png");
		background-repeat: repeat-y;
		background-position: left;
	}
	
	td#center{
		width: 80%;
	}
	
	div#container{
		padding: 10px 50px 0px 20px;
		margin-bottom: 2%;
		height: 100%;
		width: 70%;
		background-image: url("<?php print $this->printDirLevel() ?>templates/images/bg_right.png");
		background-repeat: repeat-y;
		background-position: right;
	}
	
	h2#ilmoittautuneet{
		margin-top: 5%;
	}
	
	form#kysymykset{
		margin-top: 5%;
	}
	
	span.kysymys{
		font-weight: bold;
	}
	
	input.text{
		width: 50%;
	}
	
	textarea{
		width: 50%;
		height: 10em;
	}
	
	#vahvista{
		margin-top: 3%;
	}
	
	div.debug{
		border-color: Black;
		border-style: dashed;
		border-width: 2px;
		background-color: #EEEEEE;
		color: Red;
		padding: 1%;
	}
	
	
	</style>

	<?php $this->printHeader(); ?>
  
	
</head>

<body>

<!-- Asemoidaan taulukoilla kjäh kjäh -->
<table id="gridlayout">
<tr>
<td id="leftshadow"></td>
<td id="center">
<div id="top">
	<h1 id="athenelogo"><span>Informaatioverkostojen kilta Athere</span></h1>
</div>

<h1 id="logo"><span>Ilmomasiina 2.0</span></h1>

<?php $this->printError(); ?>

<?php $this->printContent(); ?>

<?php $this->printDebug(); ?>

</div>

<br><br><br><br>
<p>Epäonnistumisen sattuessa ota yhteyttä killan tiedottajaan, tiedottaja ätt athene.fi</p>

</td>
<td id="rightshadow"></td>
</td>
</table>

</body>
</html>
