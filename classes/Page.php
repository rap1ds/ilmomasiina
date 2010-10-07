<?php
require_once("HTMLCodeCleaner.php");

/**
 * Class for printing page from templates
 */
class Page{
	
	var $template;
	var $content = "";
	var $header = "";
	var $debugUpper = "";
	var $debugLower = "";
	var $debugContent = "";
	var $configurations;
	var $title = "";
	var $errorOccured = false;
		
	function Page($dirLevel = 0, $title = "", $header = ""){
		global $configurations;
		
		$this->configurations 	=& $configurations;
		$this->template		 	= $this->configurations->template;
		$this->dirLevel			= $dirLevel;
		$this->title			= $title;
		$this->header			= $header;
		
		// Check dirlevel parameter
		if(!is_numeric($dirLevel)){
			$this->error("Illegal parameter dirLevel");
		}
	}
	
	function addContent($content){
		$this->content .= $content . "\n";
	}
	
	function addHeader($header){
		$this->header .= $header . "\n";
	}
	
	function getTitle(){
		return $this->title;
	}
	
	function getDirLevel(){
		$this->dirLevel;
	}
	
	function printTitle(){
		print $this->title;
	}
	
	function getDirLevelString(){
		$dirLevelString = "";
		
		for($i = 0; $i < $this->dirLevel; $i++){
			$dirLevelString .= "../";
		}
		
		return $dirLevelString;
	}
	
	function printDirLevel(){
		print $this->getDirLevelString();
	}
	
	function printContent(){
		if(!$this->errorOccured){
			// The line below would clean the HTML code, but it added also line 
			// breaks to the code. When line break is added between <textarea> opening
			// tag and </textarea> closing tag, it makes empty spaces to the 
			// text area. This would be easy to fix, if the cleaning code is 
			// changed so that it ignores textarea-tags. I'm too lazy to learn 
			// regexps, so I just disable the cleaning. -mkoski
			
			// TODO Enable code cleaning and fix the issue with textareas.
			// print HTMLCodeCleaner::clean($this->content);
			
			print($this->content);
		}
	}
	
	function printDebug(){
		if($this->configurations->debugMode){
			
			$debug = "";
			
			$debug .= "<br><br><div class=\"debug\">\n";
			$debug .= "<table>\n";
			$debug .= $this->debugContent;
			$debug .= "</table>\n";
			$debug .= "</div>";
			
			print HTMLCodeCleaner::clean($debug);
		}
	}
	
	function printError(){
		if($this->errorOccured){
			print $this->error;
		}
	}
	
	function error($errorMessage){
		$this->error = $errorMessage;
		$this->errorOccured = true;
		$this->printPage();
		die();
	}
	
	function printPage(){
		require($this->template);
		// require($this->getDirLevelString() . $this->template);
	}
	
	function printHeader(){
		print $this->header;
	}
	
	/*
	function printTemplateUpper(){
		require_once("../" . $this->templateUpper);
	}
	
	function printTemplateLower(){
		require_once("../" . $this->templateLower);
	}
	
	function printContent(){
		print $this->content;
	}
	
	function printDebug(){
		if($this->configurations->debugMode){
			print $this->debugUpper;
			print $this->debugContent;
			print $this->debugLower;
		}
	}
	
	function printPage(){
		$this->printTemplateUpper();
		$this->printContent();
		$this->printDebug();
		$this->printTemplateLower();
	}
	
	function initializeDebug(){
		$this->debugUpper .= "<br><br><div class=\"debug\">\n";
		$this->debugUpper .= "<b>DEBUG MESSAGES:</b><br>\n";
		$this->debugLower .= "</div>\n";
	}
	*/
	function addDebug($debugMessage){
		$this->debugContent .= $debugMessage . "\n";
	}
	
	function printAdminEmail(){
		print $this->configurations->adminEmail;
	}
	
}
 
?>
