/*
 * Przykład użycia:
 * progressBar.start();
 *	progressBar.stop();
 * 
 * <div id="wait-window" title="Proszę czekać" style="display: none;">
 * <p></p>
 * </div>
 * 
 */

progressBar = {};

progressBar.counter = 0;
progressBar.timer = null;

progressBar.update = function() {
 
	progressBar.counter = progressBar.counter + 2;
	if (progressBar.counter > 100) {
		progressBar.counter = 0;
	}

	$('#wait-window p').progressbar("value", progressBar.counter);
};

progressBar.start = function() {
	$('#wait-window').dialog( {
		modal : true,
		resizable : false,
		draggable : false
	});
	$('#wait-window p').progressbar();

	progressBar.timer = setInterval("eval('progressBar.update();')", 20);
};

progressBar.stop = function() {
	$('#wait-window').dialog('close');
	clearInterval(progressBar.timer);
	progressBar.counter = 0;
};