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

function sectorPanelClass() {
	this.name = 'sectorPanel';
}
sectorPanelClass.prototype = new basePanelClass();

function portInfoPanelClass() {
	this.name = 'portInfoPanel';
}
portInfoPanelClass.prototype = new basePanelClass();

function movePanelClass() {
	this.name = 'movePanel';
}
movePanelClass.prototype = new basePanelClass();

function weaponsPanelClass() {
	this.name = 'weaponsPanel';
}
weaponsPanelClass.prototype = new basePanelClass();

function cargoPanelClass() {
	this.name = 'cargoPanel';
}
cargoPanelClass.prototype = new basePanelClass();

function shortShipStatsPanelClass() {
	this.name = 'shortShipStatsPanel';
}
shortShipStatsPanelClass.prototype = new basePanelClass();

function shortUserStatsPanelClass() {
	this.name = 'shortUserStatsPanel';
}
shortUserStatsPanelClass.prototype = new basePanelClass();

function sectorShipsPanelClass() {
	this.name = 'sectorShipsPanel';
}
sectorShipsPanelClass.prototype = new basePanelClass();

function sectorResourcePanelClass() {
	this.name = 'sectorResourcePanel';
}
sectorResourcePanelClass.prototype = new basePanelClass();

function navigationPanelClass() {
	this.name = 'navigationPanel';
}
navigationPanelClass.prototype = new basePanelClass();

function activeScannerClass() {
	this.name = 'activeScanner';

	this.show = function() {
		$('#' + this.name).css('top', 20 + 'px');
		$('#' + this.name).css('height', $(window).height() - 50 + 'px');
		$('#' + this.name).css('left', Math.round(($(window).width() - 900) /2 )+ 'px');
		$('#' + this.name).css('width', 900);
		$('#' + this.name).show();
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

function iconPanelClass() {
	this.name = 'iconPanel';
}
iconPanelClass.prototype = new basePanelClass();

function announcementPanelClass() {
	this.name = 'announcementPanel';
}
announcementPanelClass.prototype = new basePanelClass();