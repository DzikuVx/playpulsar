var app = require('http').createServer(handler);
var io = require('socket.io').listen(app);
var fs = require('fs');
var url = require('url');
var mysql = require('mysql').Client;

app.listen(1337);

var connection = new mysql();

connection.host = 'localhost';
connection.user = 'pulsar';
connection.password = 'pulsar';
connection.database = 'pulsar_chat';

// io.set('log level', 1);

function padStr(i) {
	return (i < 10) ? "0" + i : "" + i;
}

function getDate() {
	var temp = new Date();
	var dateStr = padStr(temp.getFullYear()) + padStr(1 + temp.getMonth())
			+ padStr(temp.getDate()) + padStr(temp.getHours())
			+ padStr(temp.getMinutes()) + padStr(temp.getSeconds());
	return dateStr;
}

/**
 * Main server handler
 * @param req
 * @param res
 */
function handler(req, res) {

	/**
	 * Message notifier
	 */
	try {

		var url_parts = url.parse(req.url, true);
		var query = url_parts.query;

		if (query['notify'] == 'message') {
			console.log('new broadcast message');
			io.sockets.emit('notify', {
				type : 'message',
				userID : query['userID']
			});
		}

	} catch (e) {

	}

	/**
	 * http server
	 */
	fs.readFile(__dirname + '/index.html', function(err, data) {
		if (err) {
			res.writeHead(500);
			return res.end('Error loading index.html');
		}

		res.writeHead(200);
		res.end(data);
	});
}

function padStr(i) {
	return (i < 10) ? "0" + i : "" + i;
}

function formatDate(date) {

	var retVal = '';

	retVal += padStr(date.getFullYear()) + '-' + padStr(1 + date.getMonth())
			+ '-' + padStr(1 + date.getDate());
	retVal += ' ' + padStr(date.getHours()) + ':'
			+ padStr(1 + date.getMinutes()) + ':' + padStr(date.getSeconds());

	return retVal;
}

/**
 * Handler socket.io
 */
io.sockets.on('connection', function(socket) {

	socket.on('chat', function(data) {

		try {

			var date = new Date();

			connection.query(
					"INSERT INTO chat_history(Date, AllianceID, Name, Text) "
							+ "VALUES('" + formatDate(date) + "',null,'"
							+ data['userName'] + "','" + data['text'] + "')",
					function(err, rows, fields) {

						// do nothing

					});

			io.sockets.emit('chat', {
				text : data['text'],
				userName : data['userName'],
				date : date.getTime()
			});

		} catch (e) {

		}

	});

});