/**
 * Klasa obsługi mapy systemu
 */

function systemMapClass() {

	/**
	 * Pobranie mapy systemu z serwera i wyświetlenie
	 * 
	 * @param systemID
	 */
	this.show = function(systemID) {
		if (systemID == null)
			systemID = 'null';

		var sendXML = "";
		sendXML = sendXML + "<userID>" + $('#userID').html() + "</userID>";
		sendXML = sendXML + "<systemID>" + systemID + "</systemID>";

		$.post('engine/ajax/systemMap.php', sendXML, function(data) {

			$('#systemMap').css('top', 10 + 'px');
			$('#systemMap').css('height', $(window).height() - 30 + 'px');
			$('#systemMap').css('left', 10 + 'px');
			$('#systemMap').css('width', $(window).width() - 30 + 'px');

			$('#systemMapContent').html(parseXmlValue(data, 'content'));

			$("#mainGameplay").slideUp("fast");
			$("#systemMap").slideDown("fast");
		});
	};

	/**
	 * Ukrycie panelu mapy
	 */
	this.hide = function() {
		panel.hide('remoteSectorInfo');
		$("#mainGameplay").slideDown("fast");
		$("#systemMap").slideUp("fast");
	};

	/**
	 * Ustawienie punktu nawigacyjnego
	 * 
	 * @param System
	 * @param X
	 * @param Y
	 */
	this.plot = function(System, X, Y) {
		$('#plotSystem').val(System);
		$('#plotX').val(X);
		$('#plotY').val(Y);

		executeAction('plotSet', null, null, null, null);

		this.hide();
		panel.hide('remoteSectorInfo');

		return true;
	};

	/**
	 * Pobranie danych o sektorze
	 * 
	 * @param System
	 * @param X
	 * @param Y
	 */
	this.sectorInfo = function(System, X, Y) {
		var sendXML = "<system>" + System + "</system>";
		sendXML = sendXML + "<x>" + X + "</x>";
		sendXML = sendXML + "<y>" + Y + "</y>";
		sendXML = sendXML + "<userID>" + $('#userID').html() + "</userID>";

		$('#remoteSectorInfo').css('top', mouseY + "px");
		$('#remoteSectorInfo').css('left', mouseX + "px");
		$('#remoteSectorInfo').css('width', 320 + "px");

		$.post('engine/ajax/sectorInfo.php', sendXML, function(data) {
			panel.populate(data, 'remoteSectorInfo');
		});
	};

}

/*
 * Inicjuj obiekt
 */
systemMap = new systemMapClass();
