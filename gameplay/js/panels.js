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
				
			case 'clearIfRendered':
				if (obj.rendered && obj.content.length === 0) {
					this.clear();
				} 
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

Panel.Icons = function () {
	this.domSelector = '#iconPanel';
};
Panel.Icons.prototype = new Panel.Base();

Panel.Debug = function () {
	this.domSelector = '#debugPanel';
};
Panel.Debug.prototype = new Panel.Base();


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
		
		return this;
	};
	
};
Panel.Action.prototype = new Panel.Base();

Panel.PortAction = function () {
	this.domSelector = '#portPanel';
};
Panel.PortAction.prototype = new Panel.Action();

Panel.Overlay = function () {
	this.domSelector = '#overlayPanel';
	
	this.populate = function(obj) {

		if (!obj.rendered) {
			return this;
		}

		if (obj.params && obj.params.closer !== undefined && obj.params.closer === false) {
			this.getDomObject().find('.close:first').hide();
		}else {
			this.getDomObject().find('.close:first').show();
		}
		
		$('#overlayPanelContent').html(obj.content);
		
		$("#mainGameplay").hide();
		this.show();
		
		return this;
	};	
	
	this.hide = function() {
		$('#remoteSectorInfo').hide();
		$("#mainGameplay").show();
		this.getDomObject().hide();
		this.visible = false;
	};
	
};
Panel.Overlay.prototype = new Panel.Base();
