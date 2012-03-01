/**
 * Iso JavaScript-h‰kkyr‰, joka tekee lomake-elementtien dynaamisen
 * lis‰ilyn sivulle.
 *
 * Tuottaa sivulle seuraavanlaiset elementit jokaisella createQuestionRow-
 * funktion kutsulla:
 *
 * <tr id="question_tr_[n]">
 * 	<td>
 * 		<p class="question_label">Kysymys</p>
 * 		<input type="text" name="question_[n]" class="form_question">
 * 	</td>
 * 	<td>
 * 		<p class="question_label">Kysymyksen tyyppi</p>
 * 		<select class="type_dropdown" name="type_[n]" class="form_type">
 *				<!-- 
 *				vaihtoehdot, values text, textarea, radio, checkbox
 *				dropdown
 *				-->
 *			</select>
 *		</td>
 *		<td id="options_td">
 *			<p class="question_label">Vaihtoehdot</p>
 *			<table id="options_table_[n]">
 *				<!-- ... ja t‰m‰ sitten riitt‰v‰n monta kertaa ... -->
 *				<tr>
 *					<td>
 *						<input class="option_input" type="text" name="options_[n]-[o]" class="form_option">
 *						<input type="button" name="addQuestionButton_[n]" value="Lis‰‰" name="options_add_button_[n]-[o]">
 *					</td>
 *				</tr>
 *				<!-- t‰h‰n loppuu "looppi" -->
 *			</table>
 *			<input type="hidden" id="options_num_[n]" name="options_num_[n]" value="[o]+1">
 *		</td>
 *		<td>
 *			<p class="question_label">Julkinen</p>
 *			<input name="public_[n]" type="checkbox" value="public">
 *		</td>
 *		<td>
 *			<p class="question_label">Pakollinen</p>
 *			<input name="required_[n]" type="checkbox" value="required">
 *		</td>
 *		<td>
 *			<p class="question_label">Muokkaa</p>
 *			<input type="button" value="Lis‰‰ kysymys">
 *		</td>
 * </tr>
 */

// Kierroslukulaskuri kyseisest‰ kysymyksest‰
var questions = 0;

function createQuestionRow(obj){
	createQuestionRowFromPresetValues(obj, null, null, null, null, null, -1)
}

/**
 * Luo uuden kysymyksen kun k‰ytt‰j‰ painaa ko. nappia
 */
function createQuestionRowFromPresetValues(obj, question, type, options, public, required, id){
	// Uusi kysymystaulukkoon lis‰tt‰v‰ rivi
	var row = document.createElement("tr");
	
	// Rivin id (ei taida olla kovinkaan hyˆdyllinen)
	row.setAttribute("id", "question_tr_" + questions);

	// Riviin lis‰tt‰v‰ td-elementti, joka sis‰lt‰‰ "Kysymys"-kysymyksen ja 
	// kysymyksen id:n tarvittaessa
	var td1 = document.createElement('td');
	td1.appendChild(createLabel("Kysymys"));
	td1.appendChild(createQuestion(question));
	td1.appendChild(createHiddenId(id));
	row.appendChild(td1);
	
	// Kysymyksen tyyppi
	td2 = document.createElement('td');
	td2.appendChild(createLabel("Kysymyksen tyyppi"));
	td2.appendChild(createType(questions, type));
	row.appendChild(td2);

	/* 
	 * Seuraavassa kokonaisuudessa luodaan vaihtoehtokysymyksille oma
	 * taulukko ja sijoitetaan se td-elementtiin, joka lopuksi sijoitetaan
	 * lis‰tt‰v‰‰n tr-elementtiin
	 */
	 
	// Luodaan kysymyslaatikko ja "lis‰‰"-nappi. Huomaa parametrit
	
	var optionsValue;
	if(issetOptionsValue(options)){
		optionsValue = options[0];
	} else {
		optionsValue = null;
	}
	
	var createOpt = createOptions(questions, 0, optionsValue);
	var createOptAdd = createOptionsAdd(questions, 0, optionsValue);
	
	// Luodaan sisempi td
	var td_inner = document.createElement('td');
	
	// Lis‰t‰‰n sisemp‰‰n td-elementtiin kysymyslaatikko ja nappi
	td_inner.appendChild(createOpt);
	td_inner.appendChild(createOptAdd);
	
	// Lis‰t‰‰n sisempi td sisemp‰‰n tr-elementtiin
	var tr_inner = document.createElement('tr');
	tr_inner.appendChild(td_inner);
	
	// Luodaan taulukko, jonne sisempi tr sijoitetaan
	var table_inner = document.createElement('table');
	
	// Asetetaan taulukolle id.
	// T‰m‰ id on t‰rke‰ sill‰ sen avulla lˆydet‰‰n oikea elementti, jonka
	// lapsielementeille tehd‰‰n tarvittaessa disabled/enabled muutoksia
	// jos kysymyksen tyyppi vaihtuu
	table_inner.setAttribute('id', 'options_table_' + questions);
	table_inner.appendChild(tr_inner);
	
	// Koko ‰skeinen roska taulukkoineen kaikkineen sijoitetaan ulompaan
	// td-elementtiin, joka sijoitetaan palautettavaan tr-elementtiin
	td3 = document.createElement('td');
	td3.appendChild(createLabel("Vaihtoehdot"));
	td3.appendChild(table_inner);
	td3.setAttribute('id', 'options_td');
	
	// Vaihtoehtojen lukum‰‰r‰
	td3.appendChild(createOptionsNum(questions));
	
	row.appendChild(td3);
	
	
	// Julkinen kysymys?
	td4 = document.createElement('td');
	td4.appendChild(createLabel("Julkinen"));
	td4.appendChild(createPublic(public));
	row.appendChild(td4);
	
	// Pakollinen kysymys?
	td5 = document.createElement('td');
	td5.appendChild(createLabel("Pakollinen"));
	td5.appendChild(createRequired(required));
	row.appendChild(td5);
	
	// "Uusi kysymys"-nappi
	td6 = document.createElement('td');
	td6.appendChild(createLabel("Muokkaa"));
	td6.appendChild(createNewQuestion(questions));
	row.appendChild(td6);


	// Muutetaan lopuksi nappi jota painettiin evil-napiksi
	if(obj != null){
		obj.value = "Poista";
		obj.onclick = function () { delQuestion(this); };
	}
	
	// Luodaan taulukko kaikille kysymyksille ja asetetaan ‰sken luodut
	// kyss‰rit sinne	
	var table = document.getElementById("question_table");
	table.appendChild(row);
	
	// Jos ennalta annettuja vaihtoehtoja oli, tulostetaan ne, tai ainakin 
	// tyhj‰ seuraava ruutu.
	// T‰‰ pit‰‰ olla t‰‰ll‰ alhaalla, jolloin luodut kyss‰rit on jo 
	// appendattu, jolloin getElementById toimii
	if(issetOptionsValue(options)){
		// K‰yd‰‰n kyss‰rit l‰pi, paitsi ekaa, joka on jo tulostettu
		for(var i = 1; options.length > i; i++){
			var previous = document.getElementById('options_button_' + questions + '-' + (i-1));
			addOption(previous, questions, i, options[i]);
		}
		
		// Loppuun tulostetaan vika tyhj‰
		// EI LUODAKAAN!
		// var previous = document.getElementById('options_button_' + questions + '-' + (i-1));
		// addOption(previous, questions, i);
	}
	
	// Kysymysten m‰‰r‰++
	questions++;
	
	// Lopuksi lis‰t‰‰n kysymysten m‰‰r‰‰ yhdell‰ laskuriin
	var num = document.getElementById('question_num');
	var value = parseInt(num.value) + 1;
	num.setAttribute("value", value);
	
}

function createQuestionNum(){
	var question = document.createElement("input");
	question.setAttribute("type", "hidden");
	question.setAttribute("name", 'question_num');
	question.setAttribute("id", 'question_num');
	question.setAttribute("value", 0);
	
	return question;
}

/**
 * Luo kysymyslaatikon ja palauttaa sen
 *
 * @param value kyss‰rin ennaltam‰‰ritetty arvo
 */
function createQuestion(value){
	var question = document.createElement("input");
	question.setAttribute("type", "text");
	
	// Asetetaan kyss‰ri, jos sellaiselle on jo olemassa arvo
	if (value != null) {
		question.setAttribute("value", value);
	}
	
	question.setAttribute("name", 'question_' + questions);
	
	// Asetetaan class-attribuutti. Kiitos IE:n vieraile sivulla:
	// http://www.quirksmode.org/bugreports/archives/2005/03/setAttribute_does_not_work_in_IE_when_used_with_th.html
	question.setAttribute((document.all ? 'className' : 'class'), 'form_question');
	
	return question;
}

/**
 * Luo piilotetun id-kent‰n. T‰t‰ tarvitaan jos halutaan muokata masiinaa, 
 * jolloin kysymyksen id:n tiet‰minen on oleellista. Muussa tapaksessa t‰m‰ 
 * on merkityksetˆn
 * 
 * @param id kysymyksen id. Voi olla mit‰ vaan, jos ei olla muokkaamassa masiinaa
 */
function createHiddenId(value){
	var hiddenInput = document.createElement("input");
	hiddenInput.setAttribute("type", "hidden");
	
	// Asetetaan kyss‰ri, jos sellaiselle on jo olemassa arvo
	hiddenInput.setAttribute("value", value);
	
	hiddenInput.setAttribute("name", 'id_' + questions);
	
	return hiddenInput;
}

/**
 * Luo pudotusvalikon kysymyksen tyypin valinnalle
 *
 * @param questions kysymyksen numero
 * @param value tyypin ennaltam‰‰ritetty arvo (text, textarea, radio, checkbox, 
 *              dropdown)
 */
function createType(questions, value){
	var select = document.createElement("select");
	select.setAttribute("name", "type_" + questions);
	
	// Tyypin vaihtuessa tarkistaan disabloidaanko vai enabloidaanko
	// vaihtoehtokysymykset
	select.onchange = function () { typeChanged(this, questions); };
	
	// Tekstiruutu
	var option = document.createElement("option");
	var optionText = document.createTextNode("Tekstiruutu");
	option.appendChild(optionText);
	option.setAttribute("value", "text");
	
	// Asetataan oletusvalinta, jos tarpeen
	if(value == "text"){
		option.setAttribute("selected", value)
	}
	select.appendChild(option);
	
	// S‰hkˆposti (ei tehd‰ jos yksi s‰hkˆposti on jo m‰‰ritelty)
	var option = document.createElement("option");
	var optionText = document.createTextNode("S‰hkˆposti");
	option.appendChild(optionText);
	option.setAttribute("value", "email");
	if(value == "email"){
		option.setAttribute("selected", value)
	}
	select.appendChild(option);

	// Tekstialue
	var option = document.createElement("option");
	var optionText = document.createTextNode("Tekstialue");
	option.appendChild(optionText);
	option.setAttribute("value", "textarea");
	if(value == "textarea"){
		option.setAttribute("selected", value)
	}
	select.appendChild(option);
	
	// Valintaruutu
	var option = document.createElement("option");
	var optionText = document.createTextNode("Valintaruutu (voi ruksata monta)");
	option.appendChild(optionText);
	option.setAttribute("value", "checkbox");
	if(value == "checkbox"){
		option.setAttribute("selected", value)
	}
	select.appendChild(option);
	
	// Valintanappi
	var option = document.createElement("option");
	var optionText = document.createTextNode("Valintanappi (voi valita yhden)");
	option.appendChild(optionText);
	option.setAttribute("value", "radio");
	if(value == "radio"){
		option.setAttribute("selected", value)
	}
	select.appendChild(option);
	
	// Pudotusvalikko
	var option = document.createElement("option");
	var optionText = document.createTextNode("Pudotusvalikko");
	option.appendChild(optionText);
	option.setAttribute("value", "dropdown");
	if(value == "dropdown"){
		option.setAttribute("selected", value)
	}
	select.appendChild(option);
	
	// Asetetaan class-attribuutti. Kiitos IE:n vieraile sivulla:
	// http://www.quirksmode.org/bugreports/archives/2005/03/setAttribute_does_not_work_in_IE_when_used_with_th.html
	select.setAttribute((document.all ? 'className' : 'class'), 'form_type');
	
	
	return select;
}

/**
 * Luo "Julkinen?"-kysymyksen
 *
 * @param public boolean alkuarvo, eli onko valmiiksi valittu
 */
function createPublic(public){
	var question = document.createElement("input");
	question.setAttribute("type", "checkbox");
	question.setAttribute("name", 'public_' + questions);
	question.setAttribute("value", "public");
	
	// Asetetaan valituksi tarpeen mukaan
	if(public == true){
		question.setAttribute("checked", "checked");
	}
	
	return question;
}

/**
 * Luo "Pakollinen"-kysymyksen
 *
 * @param public boolean alkuarvo, eli onko valmiiksi valittu
 */ 
function createRequired(required){
	var question = document.createElement("input");
	question.setAttribute("type", "checkbox");
	question.setAttribute("name", 'required_' + questions);
	question.setAttribute("value", "required");
	
	// Asetetaan valituksi tarpeen mukaan
	if(required == true){
		question.setAttribute("checked", "checked");
	}
	
	return question;
}

/**
 * Luo vaihtoehtokysymyslaatikon. Vaihtoehdon nimest‰ tulee muotoa
 * "options_3-2", jossa ensimm‰inen numero tarkoittaa kysymyksen numeroa ja 
 * toinen numero tarkoittaa vaihtoehdon numeroa
 * 
 * @param questions kuinka mones kysymys on kyseess‰
 * @param options kuinka mones vaihtoehto
 */
function createOptions(questions, options, value){
	var question = document.createElement("input");
	question.setAttribute("type", "text");
	question.setAttribute("name", 'options_' + questions + '-' + options);
	
	// Asetetaan arvo, jos sellanen on annettu. Seuraavan lauseen eka osa 
	// m‰‰ritt‰‰ onko kyseess‰ taulukko, koska javascriptiss‰ ei ole valmista 
	// isArray() -funktiota
	var doNotDisable = false;
	if(value != null){
		question.setAttribute("value", value);
		
		// Ensimm‰inen valinta disabloidaan defaulttina, poistetaan t‰m‰
		// koska kysymys on ennalta asetettu
		doNotDisable = true;
	}
	
	// Tarkistetaan onko ensimm‰inen vaihtoehto
	// Jos kyseess‰ on ensimm‰inen vaihtoehto on oletuksena "Tekstirivi", 
	// jolloin vaihtoehdon on syyt‰ olla disabloitu, muutoin enabloitu
	if(options == 0 && doNotDisable == false){
		question.setAttribute('disabled', 'disabled');
	}
	
	// Asetetaan class-attribuutti. Kiitos IE:n vieraile sivulla:
	// http://www.quirksmode.org/bugreports/archives/2005/03/setAttribute_does_not_work_in_IE_when_used_with_th.html
	question.setAttribute((document.all ? 'className' : 'class'), 'form_option');
	
	return question;
}

function createOptionsNum(question){
	var question = document.createElement("input");
	question.setAttribute("type", "hidden");
	question.setAttribute("name", 'optionsnum_' + questions);
	question.setAttribute("id", 'optionsnum_' + questions);
	question.setAttribute("value", 1);
	
	return question;
}

/**
 * Luo napin, jolla voi lis‰t‰ vaihtoehtokysymyksi‰
 *
 * @param questions kuinka mones kysymys on kyseess‰
 * @param options kuinka mones vaihtoehto
 */
function createOptionsAdd(questions, options, value){
	var button = document.createElement("input");
	button.setAttribute("type", "button");
	button.setAttribute("value", "Lis‰‰");
	button.setAttribute("id", 'options_button_' + questions + '-' + options);
		
	// Tarkastetaan onko value asetettu oikein
	var doNotDisable = false;
	if (value != null){

		// Ensimm‰inen valinta disabloidaan defaulttina, poistetaan t‰m‰
		// koska kysymys on ennalta asetettu
		doNotDisable = true;
	}
	
	// Tarkistetaan onko ensimm‰inen vaihtoehto
	// Jos kyseess‰ on ensimm‰inen vaihtoehto on oletuksena "Tekstirivi", 
	// jolloin vaihtoehdon on syyt‰ olla disabloitu, muutoin enabloitu
	if(options == 0 && doNotDisable == false){
		button.setAttribute('disabled', 'disabled');
	}
	
	// Jos nappia painetaan k‰ynnistet‰‰n addOption-metodi, jolla lis‰t‰‰n
	// uusi kysymys
	button.onclick = function () { addOption(this, questions, (options + 1)); };
	return button;
}

/**
 * Lis‰‰ uuden vaihtoehtokysymyksen. Vaihtoehtokysymys lis‰t‰‰n taulukon
 * 'options_table_' + questions alle.
 *
 * @param obj nappi, jota painettiin
 * @param questions kysymyksen numero
 * @param options vaihtoehdon numero
 * @param value vaihtoehdon ennalta m‰‰r‰tty arvo
 */
function addOption(obj, questions, options, value){

	// Haetaan is‰nt‰elementit, joiden alle uusi vaihtoehtokysymys lyk‰t‰‰n
	var table = document.getElementById('options_table_' + questions);
	var tr = document.createElement('tr');
	
	// Vaihdetaan lis‰‰-namiska poista-nappiin vaihtoehdon lis‰‰misen j‰lkeen
	obj.setAttribute("value", "Poista");
	obj.onclick = function () { delOption(this, questions); };
	
	// Luodaan td johon kyss‰ri sijoitetaan
	var td = document.createElement('td');
	
	// Luodaan kysymysboxi ja nappi
	td.appendChild(createOptions(questions, options, value));
	td.appendChild(createOptionsAdd(questions, options, value));
	
	// Lis‰t‰‰n tavarat paikoilleen
	tr.appendChild(td);
	table.appendChild(tr);
	
	// Lopuksi lis‰t‰‰n vaihtoehtojen m‰‰r‰‰ yhdell‰ laskuriin
	var num = document.getElementById('optionsnum_' + questions);
	var value = parseInt(num.value) + 1;
	num.setAttribute("value", value);
}

/**
 * Poistaa kyseisen napin ja vaihtoehtokysymyksen sek‰ niiden taulukko-
 * elementit
 *
 * @param obj nappi jota painettiin. 
 */
function delOption(obj, questions){
	// Nappi
	var input1 = obj;
	
	// Kysymysboxi
	var input2 = obj.previousSibling;
	
	// Taulukon solu, joka poistetaan
	var td = obj.parentNode;
	
	// Rivi, joka poistetaan
	var tr = td.parentNode;
	
	// Is‰nt‰taulukko
	var table = tr.parentNode;
	
	// Poisto
	td.removeChild(input1);
	td.removeChild(input2);
	tr.removeChild(td);
	table.removeChild(tr);
	
	// Lopuksi v‰hennet‰‰n vaihtoehtojen m‰‰r‰‰ yhdell‰ laskuriin
	var num = document.getElementById('optionsnum_' + questions);
	var value = parseInt(num.value) - 1;
	num.setAttribute("value", value);
	
}

/**
 * Luo kysymykselle p-t‰gin sis‰‰n kuvauksen kysymyksest‰
 *
 * @param text kuvaus kysymyksest‰
 */
function createLabel(text){
	var p = document.createElement('p');
	
	// Asetetaan class-attribuutti. Kiitos IE:n vieraile sivulla:
	// http://www.quirksmode.org/bugreports/archives/2005/03/setAttribute_does_not_work_in_IE_when_used_with_th.html
	p.setAttribute((document.all ? 'className' : 'class'), 'question_label');
	
	var content = document.createTextNode(text);
	p.appendChild(content);
	return p;
}

/**
 * Funktio, jota kutsutaan kun kysymyksen tyyppi‰ on vaihdettu
 * Enabloi/disabloi vaihtoehtokysymykset riippuen kysymyksen tyypist‰
 * Tekstirivin ja tekstialueen kohdalla kysymykset disabloidaan, muulloin
 * enabloidaan.
 * Myˆs s‰hkˆpostin valinta enabloi/disabloi vahvistusviestiboxin.
 *
 * @param obj pudotusvalikko, jonka arvo muuttui
 * @param questions kysymyksen numero
 */
function typeChanged(obj, questions){
	
	// Hakee taulukon, joka sis‰lt‰‰ vaihtoehdot
	var table_inner = document.getElementById('options_table_' + questions);
	
	// Hakee vaihtoehtojen rivit (tr-elementit)
	var trs = table_inner.childNodes;
	
	// K‰y rivit l‰pi yksitellen
	for(var a = 0; a < trs.length; a++){
		
		// Ottaa rivin ensimm‰isen lapsielementin. Ei ole v‰li‰ onko juuri 
		// ensimm‰inen, sill‰ elementill‰ ei ole kuin yksi lapsi :P
		var td = trs[a].firstChild
		
		// Hakee td-elementin lapsielementit eli napin ja kysymysboxin
		var inputs = td.childNodes;
		
		// K‰sittelee napin ja kyss‰riboxin
		for(var b = 0; b < inputs.length; b++){
		
			// Haetaan toinen lapsista
			var input = inputs[b];
			
			// Muutetaan arvoa riippuen kysymyksen tyypist‰
			if(obj.value == "radio" || obj.value == "checkbox" || obj.value == "dropdown"){
				input.removeAttribute('disabled');
			} else {
				input.setAttribute('disabled', 'disabled');
			}
		}
	}
}

/**
 * Luo napin jota painamalla luodaan uusi kysymysrivi
 *
 * @param questions 
 */
function createNewQuestion(questions){
	var button = document.createElement("input");
	button.setAttribute("type", "button");
	button.setAttribute("id", "addQuestionButton_" + questions);
	button.setAttribute("value", "Lis‰‰ kysymys");
	
	// Jos nappia painetaan k‰ynnistet‰‰n createQuestionRow-metodi, 
	// jolla lis‰t‰‰n uusi kysymys
	button.onclick = function () { createQuestionRow(this); };

	return button;
}

/**
 * Poistaa kysymysrivin
 *
 * @param obj nappi, jota painettiin
 */	
function delQuestion(obj){
	// Nappi
	var input1 = obj;
	
	// Taulukon solu, joka poistetaan
	var td = obj.parentNode;
	
	// Rivi, joka poistetaan
	var tr = td.parentNode;
	
	// Is‰nt‰taulukko
	var table = tr.parentNode;
	
	// Poisto
	tr.removeChild(td);
	table.removeChild(tr);
	
	// Lopuksi v‰hennet‰‰n kysymysten m‰‰r‰‰ yhdell‰ laskuriin
	var num = document.getElementById('question_num');
	var value = parseInt(num.value) - 1;
	num.setAttribute("value", value);
}

/**
 * Tarkastaa onko vaihtoehdot annettu oikeassa muodossa, eli onko vaihtoehdot 
 * taulukossa ja onko vaihtoehtotaulukon pituus yli yksi
 *
 * @param value vaihtoehtotaulukko
 * @return joo tai ei
 */
function issetOptionsValue(value){
	if (value != null && value.constructor.toString().indexOf("Array") != -1 && 
			value.length > 0){
		return true;
	} else {
		return false;
	}
}

/**
 *
 */
function confirmationMailChechboxChanged(obj){
	if(obj.checked){
		document.getElementById("mailmessage").removeAttribute("disabled");
	} else {
		document.getElementById("mailmessage").disabled = "disabled";
	}
}
