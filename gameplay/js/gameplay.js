var mouseX = 0;
var mouseY = 0;

/**
 * @type int
 */
var fireWeaponsTimeout;

globalChat = new chatClass();

function setCenterable() {
	$("[centerable=true]").each(
		function (tIndex) {
			$(this).css('top', Math.round(mouseY - ($(this).height() / 2)));
			$(this).css('left', Math.round(($(window).width() / 2) - ($(this).width() / 2)));
		});
}

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

			announcementPanel.populate(data);

			tString = parseXmlValue(data, 'debugPanel');
			if (tString != "") {
				$('#debugPanel').html(tString);
			}

			tString = parseXmlValue(data, 'psDebug');
			if (tString != "") {
				$('#debugPanel').html(tString);
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

		});
}

var Playpulsar = Playpulsar || {};

Playpulsar.notifications = (function () {
	
	var self = {};
	
	self.push = function (notification) {
		$.pnotify({
			text : notification.text,
			type : notification.type
		});
	}
	
	return self;
})();

Playpulsar.gameplay = (function () {
	
	var self = {};
	
	self.AuthCode = 0;
	
	self.sectorInfo = function(System, X, Y) {

		$('#remoteSectorInfo').css('top', mouseY + "px");
		$('#remoteSectorInfo').css('left', mouseX + "px");
		$('#remoteSectorInfo').css('width', 320 + "px");

		//TODO refactor
		$.post('engine/ajax/sectorInfo.php', {
			System: System,
			X: X,
			Y: Y
		}, function(data) {
			$('#remoteSectorInfo').html(data).show();
		});
	};
	
	self.systemMap = function(systemID) {
		self.execute('systemMap', systemID);
	}
	
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
			panelObject,
			notificationIndex;
		
		/*
		 * Process variables
		 */
		if (data.variables) {
			console.log('Process variables ', data.variables);
			
			if (data.variables.AuthCode) {
				Playpulsar.gameplay.AuthCode = data.variables.AuthCode;
			}
			
		}
		
		if (data.notifications) {
			console.log('Process notifications ', data.notifications);
			
			for (notificationIndex in data.notifications) {
				if (data.notifications.hasOwnProperty(notificationIndex)) {
					Playpulsar.notifications.push(data.notifications[notificationIndex]);
				}
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