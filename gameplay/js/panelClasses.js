function basePanelClass() {

	this.name = '';

	this.hide = function() {
		$("#" + this.name).hide();
	};

	this.show = function() {
		$("#" + this.name).show();
	};

	this.clear = function() {
		$("#" + this.name).html('');
	};

	this.clearAndHide = function() {
		$("#" + this.name).hide();
		$("#" + this.name).html('');
	};

	this.populate = function(xml) {
		var tString = '';
		var tAction = '';
		var tContent = '';

		tString = parseXmlValue(xml, this.name);
		tAction = parseXmlValue(tString, 'action');
		tContent = parseXmlValue(tString, 'content');

		if (tContent != "") {
			$("#" + this.name).html(tContent);
		}

		switch (tAction) {
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
}

function miniMapClass() {
	this.name = 'miniMap';
}
miniMapClass.prototype = new basePanelClass();

function weaponsPanelClass() {
	this.name = 'weaponsPanel';
}
weaponsPanelClass.prototype = new basePanelClass();

function activeScannerClass() {
	this.name = 'activeScanner';

	this.populate = function(xml) {
		var tString = '';
		var tAction = '';
		var tContent = '';
		tString = parseXmlValue(xml, this.name);
		tAction = parseXmlValue(tString, 'action');
		tContent = parseXmlValue(tString, 'content');

		if (tContent != "") {
			$("#activeScannerContent").html(tContent);
		}

		switch (tAction) {
		case "show":
			this.show();
			break;

		case "hide":
			this.hide();
			break;
		}

		return true;
	};

	this.show = function() {
		$("#mainGameplay").hide();
		$("#systemMap").hide();
		$("#activeScanner").show();
	};

	this.hide = function() {
		$("#mainGameplay").show();
		$("#activeScanner").hide();
	};

}
activeScannerClass.prototype = new basePanelClass();

function shipStatsPanelClass() {
	this.name = 'shipStatsPanel';
}
shipStatsPanelClass.prototype = new basePanelClass();

function linksPanelClass() {
	this.name = 'linksPanel';
}
linksPanelClass.prototype = new basePanelClass();

function announcementPanelClass() {
	this.name = 'announcementPanel';
}
announcementPanelClass.prototype = new basePanelClass();