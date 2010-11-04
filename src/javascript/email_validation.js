/**
 * Tämän javascriptin avulla tsekataan email-osoitteen validius.
 * Tarkistus ei tietääkseni toimi ihan täydellisesti, taitaa hyväksyä esim.
 * osoitteen mikko.koski@tkk. 
 */
 
function emailChanged(obj){
	
	if(echeck(obj.value) == true){
		setInnerText("invalidemail", "");
	} else {
		setInnerText("invalidemail", "Virheellinen sähköpostiosoite");
	}
}

/**
 * Funktion avulla tekstisisällön muuttaminen onnistuu kätsästi yli selainrajojen
 * url: http://www.thescripts.com/forum/thread149896.html
 */
function setInnerText (elementId, text) {
	var element;
	if (document.getElementById) {
		element = document.getElementById(elementId);
	}
	else if (document.all) {
		element = document.all[elementId];
	}
	if (element) {
		if (typeof element.textContent != 'undefined') {
			element.textContent = text;
		}
		else if (typeof element.innerText != 'undefined') {
			element.innerText = text;
		}
		else if (typeof element.removeChild != 'undefined') {
			while (element.hasChildNodes()) {
				element.removeChild(element.lastChild);
			}
		element.appendChild(document.createTextNode(text)) ;
		}
	}
}

/**
 * DHTML email validation script. Courtesy of SmartWebby.com (http://www.smartwebby.com/dhtml/)
 */

function echeck(str) {

		var at="@"
		var dot="."
		var lat=str.indexOf(at)
		var lstr=str.length
		var ldot=str.indexOf(dot)
		if (str.indexOf(at)==-1){
		   return false
		}

		if (str.indexOf(at)==-1 || str.indexOf(at)==0 || str.indexOf(at)==lstr){
		   return false
		}

		if (str.indexOf(dot)==-1 || str.indexOf(dot)==0 || str.indexOf(dot)==lstr){
		    return false
		}

		 if (str.indexOf(at,(lat+1))!=-1){
		    return false
		 }

		 if (str.substring(lat-1,lat)==dot || str.substring(lat+1,lat+2)==dot){
		    return false
		 }

		 if (str.indexOf(dot,(lat+2))==-1){
		    return false
		 }
		
		 if (str.indexOf(" ")!=-1){
		    return false
		 }

 		 return true					
}