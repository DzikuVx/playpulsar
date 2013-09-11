var mouseX = 0;
var mouseY = 0;

/**
 * @type int
 */
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
/*
panel = new basicPanelClass();

miniMapPanel = new miniMapClass();
sectorPanel = new sectorPanelClass();
portInfoPanel = new portInfoPanelClass();
movePanel = new movePanelClass();
shortShipStatsPanel = new shortShipStatsPanelClass();
shortUserStatsPanel = new shortUserStatsPanelClass();
sectorShipsPanel = new sectorShipsPanelClass();
sectorResourcePanel = new sectorResourcePanelClass();
shipStatsPanel = new shipStatsPanelClass();
iconPanel = new iconPanelClass();
activeScanner = new activeScannerClass();
announcementPanel = new announcementPanelClass();
*/
function bankClass() {

	this.deposit = function() {
		Playpulsar.gameplay.execute('bankDeposit', null, $('#bankDepositValue').val());
	};

	this.withdraw = function() {
		Playpulsar.gameplay.execute('bankWithdraw', null, $('#bankWithdrawValue').val());
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
		Playpulsar.gameplay.execute('accountSettingsExe', null, sXml, null, null);

	};

	this.newAbusement = function(userID) {
		Playpulsar.gameplay.execute('reportAbusementExe', null, $('#postText').val(), userID,
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
		Playpulsar.gameplay.execute('allianceApplyExe', null, sXml, allianceID, null);
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

		Playpulsar.gameplay.execute('allianceEditExe', null, sXml, null, null);
	};

	/**
	 * Zapisanie nowej wiadomości na ścianie sojuszu
	 */
	this.newPostExecute = function() {

		var sXml = '';

		sXml += '<postText>' + $('#postText').val() + '</postText>';

		Playpulsar.gameplay.execute('alliancPostMessageExe', null, sXml, null, null);
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

		Playpulsar.gameplay.execute('allianceNewExe', null, sXml, null, null);
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

		Playpulsar.gameplay.execute('setAllianceRightExecute', null, sXml, id, null);
	};

	this.deposit = function() {
		Playpulsar.gameplay.execute('allianceDeposit', null, $('#allianceDepositValue').val());
	};

	this.cashout = function(id) {
		Playpulsar.gameplay.execute('allianceCashoutExe', null, $('#cashoutValue').val(), id);
	};

}

alliance = new allianceClass();

/**
 * @deprecated
 * @param xml
 * @param tag
 * @returns {String}
 */
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
			$('#' + targetHtml).prepend('<button class="close" style="margin: 0.5em;" title="x" onclick="$(this).parent().hide()"><i class="icon-white icon-remove" /></i></button>');
		}

		progressBar.stop();
	});

}

var wasInCombat = false;

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

	progressBar.start();
	
	$.post(
		'engine/engine.php',
		sendXML,
		function(data) {
			tString = parseXmlValue(data, 'logout');
			if (tString == 'true') {
				document.location = 'index.php';
				return true;
			}

			miniMapPanel.populate(data);
			sectorPanel.populate(data);
			portInfoPanel.populate(data);
			shortShipStatsPanel.populate(data);
			shortUserStatsPanel.populate(data);
			shipStatsPanel.populate(data);
			iconPanel.populate(data);
			announcementPanel.populate(data);
			activeScanner.populate(data);

			tString = parseXmlValue(data, 'debugPanel');
			if (tString != "") {
				$('#debugPanel').html(tString);
			}

			tString = parseXmlValue(data, 'psDebug');
			if (tString != "") {
				$('#debugPanel').html(tString);
			}

			if (trim($('#sectorShipsPanel').html()) == ''
					&& trim($('#sectorResourcePanel').html()) == '') {
				$('#primaryPanel').hide();
			} else {
				$('#primaryPanel').show();
			}

			tString = parseXmlValue(data, 'combatScreen');
			if (tString != '') {

				wasInCombat = true;

				$('#combatScreen').html(tString);
				$("#mainGameplay").hide();
				$('#combatScreen').show();

				clearTimeout(fireWeaponsTimeout);
				fireWeaponsTimeout = setTimeout(
						"$('#fireButton').show(); $('#disengageButton').show(); $('#maydayButton').show();",
						$('#salvoInterval').val() * 1000);

			} else {
				if (wasInCombat) {
					wasInCombat = false;
					$("#mainGameplay").show();
					$("#combatScreen").hide();
				}
			}

			$('.knob').knob();

			progressBar.stop();
		});

}

function trim(str) {
	var tString;

	if (str) {

		tString = str;
		tString = tString.replace(/^\s\s*/, '').replace(/\s\s*$/, '').replace(
				'&nbsp;', '');

		return tString;
	} else {
		return '';
	}
}

var Playpulsar = Playpulsar || {};
var Panel = Panel || {};

Panel.Factory = (function () {
	
	var self = {},
		panels = [];
	
	self.createPanel = function(className) {
		
		var myClass;
		
		if (!panels[className]) {
			myClass 			= window.Panel[className];
			panels[className] 	= new myClass();
		}

		return panels[className];
		
	};
	
	return self;
})();

Panel.Base = function () {
	this.domSelector = '';
	this.$panel = null;
	
	this.visible;
	
	this.hide = function() {
		this.getDomObject().hide();
		this.visible = false;
	};

	this.show = function() {
		this.getDomObject().show();
		this.visible = true;
	};

	this.clear = function() {
		this.getDomObject().html('');
	};

	this.clearAndHide = function() {
		this.visible = false;
		this.getDomObject().hide();
		this.getDomObject().html('');
	};

	this.populate = function(obj) {

		if (obj.content != "") {
			this.getDomObject().html(obj.content);
		}

		switch (obj.action) {
			case "show":
				this.show();
				break;
	
			case "hide":
				this.hide();
				break;
	
			case "clear":
				this.clear();
				break;
				
			case "clearAndHide":
				this.clearAndHide();
				break;
		}

		return true;
	};

	this.getDomObject = function () {
		if (!this.$panel) {
			this.$panel = $(this.domSelector);
		}
		return this.$panel;
	}
	
};

Panel.Main = function () {
	this.domSelector = '#mainPanel';
};
Panel.Main.prototype = new Panel.Base();

Panel.Primary = function () {
	this.domSelector = '#primaryPanel';
};
Panel.Primary.prototype = new Panel.Base();

Panel.Move = function () {
	this.domSelector = '#movePanel';
};
Panel.Move.prototype = new Panel.Base();

Panel.Port = function () {
	this.domSelector = '#portInfoPanel';
};
Panel.Port.prototype = new Panel.Base();

Panel.SectorResources = function () {
	this.domSelector = '#sectorResourcePanel';
};
Panel.SectorResources.prototype = new Panel.Base();

Panel.SectorShips = function () {
	this.domSelector = '#sectorShipsPanel';
};
Panel.SectorShips.prototype = new Panel.Base();

Panel.PlayerStats = function () {
	this.domSelector = '#shortUserStatsPanel';
};
Panel.PlayerStats.prototype = new Panel.Base();

Panel.Sector = function () {
	this.domSelector = '#sectorPanel';
};
Panel.Sector.prototype = new Panel.Base();

Panel.ShortStats = function () {
	this.domSelector = '#shortShipStatsPanel';
};
Panel.ShortStats.prototype = new Panel.Base();

Panel.MiniMap = function () {
	this.domSelector = '#miniMap';
};
Panel.MiniMap.prototype = new Panel.Base();

Panel.Navigation = function () {
	this.domSelector = '#navigationPanel';
};
Panel.Navigation.prototype = new Panel.Base();

/*
 * Simple panels definition
 */
Panel.Action = function () {
	this.domSelector = '#actionPanel';
	
	this.populate = function(obj) {

		this.getDomObject().html(obj.content);

		if (obj.content == '' || obj.content == '&nbsp;') {
			this.hide();
		} else {
			this.show();
		}
		
		return true;
	};
	
};
Panel.Action.prototype = new Panel.Base();

Panel.PortAction = function () {
	this.domSelector = '#portPanel';
};
Panel.PortAction.prototype = new Panel.Action();

Playpulsar.gameplay = (function () {
	
	var self = {};
	
	self.AuthCode = 0;
	
	self.execute = function(action, subaction, value, id) {
		
		/*
		 * Prepare additional data
		 */
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
		
		var requestData = {};
		
		requestData.action 		= action;
		requestData.subaction 	= subaction;
		requestData.value 		= value;
		requestData.id 			= id;
		requestData.auth 		= self.AuthCode;
		
		$.ajax({
			  dataType: "json",
			  method: 'post',
			  url: 'engine/engine.php',
			  data: requestData,
			  success: self.processSuccess,
			  error: self.processFailure
			});
		
	}
	
	self.processSuccess = function (data, textStatus, jqXHR) {
		
		var panelName,
			panelData,
			panelObject;
		
		/*
		 * Process variables
		 */
		if (data.variables) {
			console.log('Process variables ', data.variables);
			
			if (data.variables.AuthCode) {
				Playpulsar.gameplay.AuthCode = data.variables.AuthCode;
			}
			
		}
		
		/*
		 * Process panels
		 */
		if (data.panels) {
			console.log('Process panels ', data.panels);
			
			for (panelName in data.panels) {
				if (data.panels.hasOwnProperty(panelName)) {
					
					panelData = data.panels[panelName];
					
					console.log(panelName, panelData);
					
					panelObject = Panel.Factory.createPanel(panelName);
					panelObject.populate(panelData);
					
				}
			}
			
			/*
			 * Process postpanel conditions
			 */
			if (!Panel.Factory.createPanel('PortAction').visible && !Panel.Factory.createPanel('Action').visible) {
				Panel.Factory.createPanel('Main').hide();
			} else {
				Panel.Factory.createPanel('Main').show();
			}

			if (!Panel.Factory.createPanel('SectorResources').visible && !Panel.Factory.createPanel('SectorShips').visible) {
				Panel.Factory.createPanel('Primary').hide();
			} else {
				Panel.Factory.createPanel('Primary').show();
			}
			
		}
		
		$('.knob').knob();
		
	}
	
	self.processFailure = function (data) {
		console.log('Failure', data);
	}
	
	return self;
})();