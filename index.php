<?php

/* Requirements */
require_once("classes/ErrorReportEnabler.php");
require_once("classes/Configurations.php");
require_once("classes/Request.php");
require_once("classes/AdminPasswordProtector.php");

require_once("classes/Configurations.php");
require_once("classes/Page.php");
require_once("classes/SignupGadgets.php");
require_once("classes/Debugger.php");
require_once("classes/SignupGadget.php");
require_once("classes/CommonTools.php");

$configurations = new Configurations();

// Bootstrapper

$requestedURI = $_SERVER['REQUEST_URI'];

if (strpos($requestedURI, $configurations->webRoot) !== 0) {
    die("Probably wrong webRoot: " . $configurations->webRoot);
}

$request = new Request(substr($requestedURI, strlen($configurations->webRoot)));

if ($request->isError()) {

    $page = new Page();
    $debugger = new Debugger();
    $database = new Database();

    $page = new Page();
    $page->title = "Ilmomasiina";
    $page->addContent("<p>Virheellinen url</p>");
    $page->printPage();
} else {

    if ($request->isAdmin()) {

        $passwordProtector = new AdminPasswordProtector();

        if ($passwordProtector->authenticate() !== true) {
            // Needs authentication
            die();
        }

        switch ($request->getAction()) {
            case "showanswers":
                require_once 'admin/answers/show.php';
                break;
            case "editanswer":
                require_once 'admin/answers/edit.php';
                break;
            case "updateanswer":
                require_once 'admin/answers/save.php';
                break;
            case "new":
                require_once 'admin/new.php';
                break;
            case "save":
                require_once 'admin/save.php';
                break;
            case "update":
                require_once 'admin/save_edit.php';
                break;
            case "edit":
                require_once 'admin/edit.php';
                break;
            case "delete":
                require_once 'admin/delete.php';
                break;
            case "delete-confirmed":
                require_once 'admin/delete.php';
                break;
            case "deleteanswer":
                require_once 'admin/answers/delete.php';
                break;
            case "changestate":
                require_once 'admin/changestate.php';
                break;
            default:
                require_once 'admin/index.php';
        }
    } else {

        switch ($request->getAction()) {
            case "signup":
                require_once 'signup.php';
                break;
            case "queue":
            case "confirmold":
            case "continueandcancelold":
                require_once 'queue.php';
                break;
            case "confirm":
                require_once 'confirm.php';
                break;
            case "save":
                require_once 'save.php';
                break;
            case "cancel":
                require_once 'cancel.php';
                break;
            case "answers":
                require_once 'answers/index.php';
                break;
            case "showanswers":
                require_once 'answers/show.php';
                break;
            case "csvoutput":
                require_once 'answers/csv_output.php';
                break;
            default:

// Implementations of the most critical classes
// $configurations                 = new Configurations();
                $page = new Page();
                $debugger = new Debugger();
                $database = new Database();

// The code
                $signupGadgets = new SignupGadgets();
                $signupGadgets->selectOpenGadgetsOrClosedDuringLastDays(7);

// Get all selected gadgets to array
                $signupGadgets_array = $signupGadgets->getSignupGadgets();

//Set the default title
                $page->title = "Ilmomasiina";

// Print table headers
                $page->addContent("<table id=\"signup-gadgets\">");
                $page->addContent("<tr id=\"signup-header-row\">");
                $page->addContent("<th id=\"signup-name-header\">Ilmo</th><th id=\"signup-opens-header\">Avautuu</th><th id=\"signup-closes-header\">Sulkeutuu</th><th id=\"open-close-state-header\">Tila</th>");
                $page->addContent("</tr>");

                foreach ($signupGadgets_array as $gadget) {
                    $page->addContent("<tr class=\"answer-row\">");
                    $page->addContent("<td class=\"signup-name\"><a href=\"signup/" . $gadget->getId() . "\">" . $gadget->getTitle() . "</a></td>");
                    $page->addContent("<td class=\"signup-opens\">" . $gadget->getOpeningTime() . "</td>");
                    $page->addContent("<td class=\"signup-closes\">" . $gadget->getClosingTime() . "</td>");

                    if ($gadget->isOpen()) {
                        $page->addContent("<td class=\"open-close-state\"><span class=\"signup-open\">Auki</span></td>");
                    } else if ($gadget->willBeOpened()) {
                        $page->addContent("<td class=\"open-close-state\"><span class=\"signup-not-yet-open\">Ei vielä avautunut</td>\n");
                    } else {
                        $page->addContent("<td class=\"open-close-state\"><span class=\"signup-closed\">Sulkeutunut</td>\n");
                    }

                    $page->addContent("</tr>");
                }

                $page->addContent("</table>");

                $page->printPage();

                break;
        }
    }
}