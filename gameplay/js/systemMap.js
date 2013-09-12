/**
 * Klasa obsługi mapy systemu
 * @deprecated
 */
function systemMapClass() {

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

		Playpulsar.gameplay.execute('plotSet', null, null, null, null);

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
