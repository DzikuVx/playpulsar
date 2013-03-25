function chatClass() {

	this.timer;

	this.lastID = 0;

	this.getFileName = 'engine/ajax/globalChat.php';
	this.sendFileName = 'engine/ajax/globalChatSend.php';

	this.textFieldId = '#globalChatPanelTxt';
	this.inputFieldId = '#globalChatInput';

	this.objectName = 'globalChat';

	this.start = function() {
		clearTimeout(this.timer);
		this.timer = setTimeout(this.objectName + '.get()', 30000);
	};

	this.submitListener = function (e) {
		var keynum = null;
		var keychar;

		if (window.event) {
			keynum = e.keyCode;
		} else if(e.which)  {
			keynum = e.which;
		}

		keychar = String.fromCharCode(keynum);
		if (keychar == "\r") {
			this.send();
		}
		
	};
	
	this.get = function() {

		var objectName = this.objectName;
		var textFieldId = this.textFieldId;
		var lastId = this.lastID;

		var thisObject = this;
		
		$.post(this.getFileName, {
			lastID : this.lastID
		}, function(data) {

			if (data.State == 1) {

				var tString = '';

				if (Math.round(data.LastID) > Math.round(lastId)) {
					lastId = data.LastID;
				}

				for ( var tIndex = 0; tIndex < data.Count; tIndex++) {

					tString = '<div>' + data.Data[tIndex] + '</div>'
							+ $(textFieldId).html();

					$(textFieldId).html(tString);
				}

			}

			thisObject.lastID = lastId;
			eval(objectName + '.start()');

		}, 'json');
	};

	this.send = function() {
		var tValue;
		tValue = $(this.inputFieldId).val();

		var objectName = this.objectName;

		if (tValue == '' || tValue == ' ') {
			return false;
		}

		$(this.inputFieldId).val('');
		progressBar.start();
		
		$.post(this.sendFileName, {
			text : tValue
		}, function(data) {
			progressBar.stop();
			eval(objectName + '.get()');
		});
	};

}