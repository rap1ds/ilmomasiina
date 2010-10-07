<?php

/* Requirements */ 
require_once("../../classes/Configurations.php");
require_once("../../classes/Page.php");
require_once("../../classes/Debugger.php");
require_once("../../classes/Database.php");
require_once("../../classes/SignupGadget.php");
require_once("../../classes/CommonTools.php");
require_once("../../classes/User.php");
require_once("../../classes/SignupGadgetQuestionFormater.php");

/* Implementations of the most critical classes */
$configurations		= new Configurations();
$page				= new Page(2);
$debugger			= new Debugger();
$database			= new Database();

/* Variables from outside */
$userId = CommonTools::GET("userid");
$signupId = CommonTools::GET("signupid");
$signupGadget = new SignupGadget($signupId);

/* The code */

$page->addContent("<form id=\"kysymykset\" method=\"post\" action=\"save.php\">");
$page->addContent("<input type=\"hidden\" name=\"userid\" value=\"".$userId."\">");
$page->addContent("<input type=\"hidden\" name=\"signupid\" value=\"".$signupId."\">");
$page->addContent(
		SignupGadgetQuestionFormater::getQuestionsInPrintableFormat($signupGadget, $userId));
$page->addContent("<input type=\"hidden\" name=\"question_ids\" value=\"".$signupGadget->getFormatedQuestionIds()."\">");
$page->addContent("<input id=\"vahvista\" type=\"submit\" value=\"Tallenna muutokset\">");
$page->addContent("</form><br>");
$page->addContent("<form id=\"peru\" method=\"post\" action=\"show.php?userid=$userId\">");
$page->addContent("<input id=\"peru\" type=\"submit\" value=\"Peruuta\">");
$page->addContent("</form>");

$page->printPage();

?>
