function checkLoginSubmit(e) {
	var keynum = null;
	var keychar;

	if (window.event) {
		keynum = e.keyCode;
	} else if (e.which) {
		keynum = e.which;
	}
	keychar = String.fromCharCode(keynum);
	if (keychar == "\r") {
		document.forms['login'].submit();
	}
}

function clearSearch() {
	$('#searchText').val('');
	document.searchForm.submit();
}

function checkSubmit(formType) {

	var tGo = true;

	switch (formType) {

	default:
		break;
	}

	if (tGo) {
		document.myForm.submit();
	} else {
		errorDialog('Error', 'Please fill required fields');
		return false;
	}

}

function errorDialog(title, text, onClose) {
	$('#mainDialog').attr('title', title);
	$('#mainDialogText').html(text);

	$("#mainDialog").dialog({
		modal : true,
		buttons : {
			Ok : function() {
				if (onClose != null) {
					eval(onClose);
				}
				$(this).dialog('close');
			}
		}
	});

}

function playerClass() {

	this.msgSend = function() {
		document.forms['myForm'].submit();
	};

	this.addWeapon = function() {
		document.forms['myForm'].submit();
	};

	this.addEquipment = function() {
		document.forms['myForm'].submit();
	};

	this.giveRookie = function() {

		if ($('[name=value]').val() != '') {
			document.forms['myForm'].submit();
		}
	};

	this.giveTraxium = function() {

		if ($('[name=value]').val() != '') {
			document.forms['myForm'].submit();
		}
	};

	this.gameplayMessage = function() {

		document.forms['myForm'].submit();
	};

}

player = new playerClass();