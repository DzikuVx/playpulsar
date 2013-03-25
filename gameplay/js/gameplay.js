// Zmienna przechowująca ostatni czas resetu tur
var lastTurnsResetTime = -1;
var lastShipRepairTime = -1;

var mouseX = 0;
var mouseY = 0;

var fireWeaponsTimeout;

globalChat = new chatClass();

function setCenterable() {
	$("[centerable=true]")
			.each(
					function(tIndex) {

						$(this).css('top',
								Math.round(mouseY - ($(this).height() / 2)));
						$(this).css(
								'left',
								Math.round(($(window).width() / 2)
										- ($(this).width() / 2)));

					});
}

function basicPanelClass() {

	this.hide = function(name) {
		$("#" + name).slideUp("fast");
	};

	this.populate = function(xml, name) {
		var tString = '';
		var tAction = '';
		var tContent = '';

		tString = parseXmlValue(xml, name);
		tAction = parseXmlValue(tString, 'action');
		tContent = parseXmlValue(tString, 'content');

		if (tContent != "") {
			$("#" + name).html(tContent);
		}

		switch (tAction) {
		case "show":
			$("#" + name).show();
			break;

		case "hide":
			$("#" + name).hide();
			break;

		case "clear":
			$("#" + name).html('');
			break;

		case "clearAndHide":
			$("#" + name).hide();
			$("#" + name).html('');
			break;

		}

		return true;
	};

}

panel = new basicPanelClass();

miniMapPanel = new miniMapClass();
sectorPanel = new sectorPanelClass();
portInfoPanel = new portInfoPanelClass();
movePanel = new movePanelClass();
weaponsPanel = new weaponsPanelClass();
cargoPanel = new cargoPanelClass();
shortShipStatsPanel = new shortShipStatsPanelClass();
shortUserStatsPanel = new shortUserStatsPanelClass();
sectorShipsPanel = new sectorShipsPanelClass();
sectorResourcePanel = new sectorResourcePanelClass();
navigationPanel = new navigationPanelClass();
shipStatsPanel = new shipStatsPanelClass();
linksPanel = new linksPanelClass();
iconPanel = new iconPanelClass();
activeScanner = new activeScannerClass();
announcementPanel = new announcementPanelClass();

function bankClass() {

	this.deposit = function() {
		executeAction('bankDeposit', null, $('#bankDepositValue').val());
	};

	this.withdraw = function() {
		executeAction('bankWithdraw', null, $('#bankWithdrawValue').val());
	};

}

bank = new bankClass();

function userClass() {

	/**
	 * Edycja danych użytkownika
	 */
	this.editExecute = function() {

		if ($('#userName').val().length < 4) {
			alert("Name too short (min 4 chars)");
			return false;
		}

		if ($('#userName').val().length > 20) {
			alert("Name too long (max 20 chars)");
			return false;
		}

		if ($('#passwordA').val() != '' || $('#passwordB').val() != '') {

			if ($('#passwordA').val() != $('#passwordB').val()) {
				alert('Wrong password');
				return false;
			}

			if ($('#passwordA').val().length < 6) {
				alert("Password too short (min 6 chars)");
				return false;
			}
		}

		var sXml = '';
		sXml += '<userCountry>' + $('#userCountry').val() + '</userCountry>';
		sXml += '<userName>' + $('#userName').val() + '</userName>';
		sXml += '<passwordA>' + $('#passwordA').val() + '</passwordA>';
		sXml += '<passwordB>' + $('#passwordB').val() + '</passwordB>';
		sXml += '<userLanguage>' + $('#userLanguage').val() + '</userLanguage>';
		sXml += '<spamCheckbox>' + $('#spamCheckbox').attr('checked')
				+ '</spamCheckbox>';
		executeAction('accountSettingsExe', null, sXml, null, null);

	};

	this.newAbusement = function(userID) {
		executeAction('reportAbusementExe', null, $('#postText').val(), userID,
				null);
	};

}

user = new userClass();

function allianceClass() {

	/*
	 * Wysłanie podania do sojuszu, wykonanie
	 */
	this.applySave = function(allianceID) {
		var sXml = '';
		sXml += '<text>' + $('#text').val() + '</text>';
		executeAction('allianceApplyExe', null, sXml, allianceID, null);
	};

	/**
	 * Zapisanie danych sojuszu
	 */
	this.editSave = function() {

		var sXml = '';

		sXml += '<allianceSymbol>' + $('#AllianceSymbol').val()
				+ '</allianceSymbol>';
		sXml += '<allianceName>' + $('#AllianceName').val() + '</allianceName>';
		sXml += '<allianceDescription>' + $('#AllianceDescription').val()
				+ '</allianceDescription>';
		sXml += '<allianceMotto>' + $('#AllianceMotto').val()
				+ '</allianceMotto>';

		executeAction('allianceEditExe', null, sXml, null, null);
	};

	/**
	 * Zapisanie nowej wiadomości na ścianie sojuszu
	 */
	this.newPostExecute = function() {

		var sXml = '';

		sXml += '<postText>' + $('#postText').val() + '</postText>';

		executeAction('alliancPostMessageExe', null, sXml, null, null);
	};

	/**
	 * Utworzenie nowego sojuszu
	 */
	this.newSave = function() {

		var sXml = '';

		sXml += '<allianceSymbol>' + $('#AllianceSymbol').val()
				+ '</allianceSymbol>';
		sXml += '<allianceName>' + $('#AllianceName').val() + '</allianceName>';
		sXml += '<allianceDescription>' + $('#AllianceDescription').val()
				+ '</allianceDescription>';
		sXml += '<allianceMotto>' + $('#AllianceMotto').val()
				+ '</allianceMotto>';

		executeAction('allianceNewExe', null, sXml, null, null);
	};

	/**
	 * Utworzenie nowego sojuszu
	 */
	this.setMemberRights = function(id) {

		var sXml = '';

		sXml += '<editValue>' + $('#editValue').attr('checked')
				+ '</editValue>';
		sXml += '<postValue>' + $('#postValue').attr('checked')
				+ '</postValue>';
		sXml += '<acceptValue>' + $('#acceptValue').attr('checked')
				+ '</acceptValue>';
		sXml += '<kickValue>' + $('#kickValue').attr('checked')
				+ '</kickValue>';
		sXml += '<cashValue>' + $('#cashValue').attr('checked')
				+ '</cashValue>';
		sXml += '<rankValue>' + $('#rankValue').attr('checked')
				+ '</rankValue>';
		sXml += '<relationsValue>' + $('#relationsValue').attr('checked')
				+ '</relationsValue>';

		executeAction('setAllianceRightExecute', null, sXml, id, null);
	};

	this.deposit = function() {
		executeAction('allianceDeposit', null, $('#allianceDepositValue').val());
	};

	this.cashout = function(id) {
		executeAction('allianceCashoutExe', null, $('#cashoutValue').val(), id);
	};

}

alliance = new allianceClass();

function parseXmlValue(xml, tag) {
	var out = "";
	var startMark;
	var endMark;
	var startPosition;
	var endPosition;

	startMark = "<" + tag + ">";
	endMark = "</" + tag + ">";

	startPosition = xml.search(startMark);
	endPosition = xml.search(endMark);
	if (startPosition != -1 && endPosition != -1) {
		out = xml.substr(startPosition + startMark.length, endPosition
				- startPosition - startMark.length);
	}
	return out;
}

function getXmlRpc(targetHtml, method, param0, param1, param2, param3) {

	progressBar.start();

	var sendXml;

	sendXml = '<?xml version="1.0"?>';
	sendXml += '<methodCall>';
	sendXml += '<methodName>' + method + '</methodName>';
	sendXml += '<params>';
	if (param0 != null) {
		sendXml += '<param>';
		sendXml += '<value>' + param0 + '</value>';
		sendXml += '</param>';
	}
	if (param1 != null) {
		sendXml += '<param>';
		sendXml += '<value>' + param1 + '</value>';
		sendXml += '</param>';
	}
	if (param2 != null) {
		sendXml += '<param>';
		sendXml += '<value>' + param2 + '</value>';
		sendXml += '</param>';
	}
	if (param3 != null) {
		sendXml += '<param>';
		sendXml += '<value>' + param3 + '</value>';
		sendXml += '</param>';
	}

	sendXml += '</params>';
	sendXml += '</methodCall>';

	$.post('engine/ajax/xmlrpc.php', sendXml, function(data) {

		if (targetHtml != null) {
			$('#' + targetHtml).html(parseXmlValue(data, 'value'));
			$('#' + targetHtml).show();
			$('#' + targetHtml).css('top', mouseY - 75);
			$('#' + targetHtml).css('left', mouseX - 100);
		}

		progressBar.stop();
	});

}

/**
 * @param action
 * @param subaction
 * @param value
 * @param id
 * @param auth
 */
function executeAction(action, subaction, value, id, auth) {

	if (action == 'productBuy') {
		value = $('#buy_' + id).val();
		if (value == '')
			value = 0;
	}

	if (action == 'productSell') {
		value = $('#sell_' + id).val();
		if (value == '')
			value = 0;
	}

	if (action == 'itemSell') {
		value = $('#item_sell_' + id).val();
		if (value == '')
			value = 0;
	}

	if (action == 'plotSet') {
		value = $('#plotSystem').val() + "/" + $('#plotX').val() + "/"
				+ $('#plotY').val();
	}

	var sendXML = '<?xml version="1.0"?>';
	sendXML = sendXML + "<userID>" + $('#userID').html() + "</userID>";
	sendXML = sendXML + "<action>" + action + "</action>";
	sendXML = sendXML + "<subaction>" + subaction + "</subaction>";
	sendXML = sendXML + "<value>" + value + "</value>";
	sendXML = sendXML + "<id>" + id + "</id>";
	sendXML = sendXML + "<auth>" + $('#authCode').html() + "</auth>";

	progressBar.start();

	$
			.post(
					'engine/engine.php',
					sendXML,
					function(data) {
						/*
						 * Wylogowanie
						 */
						tString = parseXmlValue(data, 'logout');
						if (tString == 'true') {
							document.location = 'index.php';
							return true;
						}

						miniMapPanel.populate(data);
						sectorPanel.populate(data);
						portInfoPanel.populate(data);
						movePanel.populate(data);
						weaponsPanel.populate(data);
						cargoPanel.populate(data);
						shortShipStatsPanel.populate(data);
						shortUserStatsPanel.populate(data);
						sectorShipsPanel.populate(data);
						sectorResourcePanel.populate(data);
						navigationPanel.populate(data);
						shipStatsPanel.populate(data);
						linksPanel.populate(data);
						iconPanel.populate(data);
						announcementPanel.populate(data);
						activeScanner.populate(data);

						panel.populate(data, 'newsAgencyPanel');

						/*
						 * tString = parseXmlValue(data, 'announcementPanel');
						 * if (tString != '&nbsp;') {
						 * $('#announcementPanel').show(); } else {
						 * $('#announcementPanel').hide(); }
						 * 
						 * if (tString != "") {
						 * $('#announcementPanel').html(tString); }
						 */

						tString = parseXmlValue(data, 'authCode');
						if (tString != "") {
							$('#authCode').html(tString);
						}

						tString = parseXmlValue(data, 'debugPanel');
						if (tString != "") {
							$('#debugPanel').html(tString);
						}

						tString = parseXmlValue(data, 'psDebug');
						if (tString != "") {
							$('#debugPanel').html(tString);
						}

						tString = parseXmlValue(data, 'portPanel');
						if (tString != "") {
							$('#portPanel').html(tString);
							if (tString == "&nbsp;") {
								document.getElementById('portPanel').style.display = "none";
								$('#portPanel').hide();
							} else {
								$('#portPanel').show();
							}
						}

						tString = parseXmlValue(data, 'actionPanel');
						if (tString != "") {
							$('#actionPanel').html(tString);
							if (tString == "&nbsp;") {
								$('#actionPanel').hide();
							} else {
								$('#actionPanel').show();
							}
						}

						if (trim($('#actionPanel').html()) == ''
								&& trim($('#portPanel').html()) == '') {
							$('#mainPanel').hide();
						} else {
							$('#mainPanel').show();
						}

						tString = parseXmlValue(data, 'combatScreen');
						if (tString != '') {
							$('#combatScreen').html(tString);
							$("#mainGameplay").slideUp("fast");
							$('#combatScreen').slideDown('fast');

							clearTimeout(fireWeaponsTimeout);
							fireWeaponsTimeout = setTimeout(
									"$('#fireButton').show(); $('#disengageButton').show(); $('#maydayButton').show();",
									$('#salvoInterval').val() * 1000);

						} else {
							$("#mainGameplay").slideDown("slow");
							$("#combatScreen").slideUp("slow");
						}
						progressBar.stop();
					});

}

function trim(str) {
	var tString;
	tString = str;
	tString = tString.replace(/^\s\s*/, '').replace(/\s\s*$/, '').replace(
			'&nbsp;', '');

	return tString;
}