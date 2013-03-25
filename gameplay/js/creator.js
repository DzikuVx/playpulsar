var xmlHttp;

var IE = document.all ? true : false;

if (!IE)
	document.captureEvents(Event.MOUSEMOVE);

document.onmousemove = getMouseXY;

var mouseX = 0;
var mouseY = 0;

/**
 * @param e
 * @returns {Boolean}
 */
function getMouseXY(e) {
	if (IE) {
		mouseX = event.clientX + document.body.scrollLeft;
		mouseY = event.clientY + document.body.scrollTop;
	} else {
		mouseX = e.pageX;
		mouseY = e.pageY;
	}
	if (mouseX < 0) {
		mouseX = 0;
	}
	if (mouseY < 0) {
		mouseY = 0;
	}

	return true;
}

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

function getXmlHttpObject() {
	var objXMLHttp = null;
	if (window.XMLHttpRequest) {
		objXMLHttp = new XMLHttpRequest();
	} else if (window.ActiveXObject) {
		objXMLHttp = new ActiveXObject("Microsoft.XMLHTTP");
	}
	return objXMLHttp;
}

function performAction(type, x, y) {
	xmlHttp = getXmlHttpObject();

	if (xmlHttp == null) {
		alert("Your browser does not support AJAX!");
		return;
	}

	var sendXML = '';

	if (type == 'system') {

		// Jesli podglad
		if (document.getElementById('mode').value == 'view') {
			sendXML = sendXML + "<system>"
					+ document.getElementById('system').value + "</system>";
			sendXML = sendXML + "<x>" + x + "</x>";
			sendXML = sendXML + "<y>" + y + "</y>";

			document.getElementById('sectorInfo').style.top = mouseY + "px";
			document.getElementById('sectorInfo').style.left = mouseX + "px";
			document.getElementById('sectorInfo').style.width = 320 + "px";

			var url = "../engine/ajax/remoteSectorInfo.php";
			url = url + "?sid=" + Math.random();
			xmlHttp.onreadystatechange = remotePortInfoChanged;
			xmlHttp.open("POST", url, true);
			xmlHttp.send(sendXML);
		}

		if (document.getElementById('mode').value == 'sectorAdd') {
			document.location = '?sectorAdd&sectorID='
					+ document.getElementById('sectorID').value + '&mode='
					+ document.getElementById('mode').value + '&system='
					+ document.getElementById('system').value + '&x=' + x
					+ '&y=' + y;
		}

		if (document.getElementById('mode').value == 'portAdd') {
			document.location = '?portAdd&portID='
					+ document.getElementById('portID').value + '&mode='
					+ document.getElementById('mode').value + '&system='
					+ document.getElementById('system').value + '&x=' + x
					+ '&y=' + y;
		}

	}

}

function remotePortInfoChanged() {
	if (xmlHttp.readyState == 4 || xmlHttp.readyState == "complete") {
		document.getElementById('sectorInfoTxt').innerHTML = xmlHttp.responseText;
		document.getElementById('sectorInfo').style.visibility = 'visible';
	}
}