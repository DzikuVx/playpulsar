/**
 * Maskowanie pól, wersja z liczbami
 * @param str
 * @param textbox
 * @param min
 * @param max
 */
function maskNumber(str, textbox, min, max) {
	var original;

	original = str;

	var valid = "0123456789";
	var temp;
	var new_str = "";

	for ( var i = 0; i < str.length; i++) {
		temp = "" + str.substring(i, i + 1);

		if (valid.indexOf(temp) != "-1") {
			new_str = new_str + temp;
		}
	}

	if (Math.round(new_str) < min)
		new_str = min;
	if (Math.round(new_str) > max)
		new_str = max;

	if (original != new_str) {
		textbox.value = new_str;
	}

}

function maskPlot(str, textbox) {
	var original;

	original = str;

	var valid = "0123456789";
	var temp;
	var new_str = "";

	for ( var i = 0; i < str.length; i++) {
		temp = "" + str.substring(i, i + 1);

		if (valid.indexOf(temp) != "-1") {
			new_str = new_str + temp;
		}
	}

	if (original != new_str) {
		textbox.value = new_str;
	}

}

/**
 * Maskowanie pól
 * @param str
 * @param textbox
 * @param maxlen
 * @param digit_only
 */
function mask(str, textbox, maxlen, digit_only) {
	var change;
	var original;

	change = 0;
	original = str;

	// Sprawdzenie, czy tylko liczby
	if (digit_only == 'simpleText') {
		var valid = "0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ_@";
		var temp;
		var new_str;
		new_str = "";

		for ( var i = 0; i < str.length; i++) {
			temp = "" + str.substring(i, i + 1);
			if (valid.indexOf(temp) != "-1") {
				// jesli jest liczba
				new_str = new_str + temp;
			}
		}
		str = new_str;
	}

	// Sprawdzenie, czy tylko znaki symboli na mobila
	if (digit_only == 'mediumText') {
		var valid = "/abcdefghijklmnopqrstuwvxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789+-_*.,;@|#$%&()[]{}";
		var temp;
		var new_str;
		new_str = "";

		for ( var i = 0; i < str.length; i++) {
			temp = "" + str.substring(i, i + 1);
			if (valid.indexOf(temp) != "-1") {
				// jesli jest liczba
				new_str = new_str + temp;
			}
		}
		str = new_str;
	}

	// Sprawdzenie, czy tylko znaki symboli na mobila
	if (digit_only == 'plText') {
		var valid = " \'\"/abcdefghijklmnopqrstuwvxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789+-_*.,;:@|#$%&()[]{}?!ąĄęĘśŚćĆżŻźŹłŁóÓńŃ/";
		var temp;
		var new_str;
		new_str = "";

		for ( var i = 0; i < str.length; i++) {
			temp = "" + str.substring(i, i + 1);
			if (valid.indexOf(temp) != "-1") {
				// jesli jest liczba
				new_str = new_str + temp;
			}
		}
		str = new_str;
	}

	if (digit_only == "digit_dot") {
		var valid = ".0123456789";
		var temp;
		var new_str;
		var is_dot;

		is_dot = 0;
		new_str = "";

		for ( var i = 0; i < str.length; i++) {
			temp = "" + str.substring(i, i + 1);
			// zamien , na .
			if (temp == ",") {
				temp = ".";
			}

			// sprawdz, czy jest juz drugi .
			if (temp == ".") {
				is_dot = is_dot + 1;

				if (is_dot > 1) {
					temp = "";
				}
			}

			if (valid.indexOf(temp) != "-1") {
				new_str = new_str + temp;
			}
		}
		str = new_str;
		if (str.length == 0) {
			str = "0";
		}
	}

	if (digit_only == "digit") {
		var valid = "0123456789";
		var temp;
		var new_str;
		var is_dot;

		is_dot = 0;
		new_str = "";

		for ( var i = 0; i < str.length; i++) {
			temp = "" + str.substring(i, i + 1);
			// zamien , na .
			if (temp == ",") {
				temp = ".";
			}

			// sprawdz, czy jest juz drugi .
			if (temp == ".") {
				is_dot = is_dot + 1;

				if (is_dot > 1) {
					temp = "";
				}
			}

			if (valid.indexOf(temp) != "-1") {
				new_str = new_str + temp;
			}
		}
		str = new_str;
		if (str.length == 0) {
			str = "0";
		}
	}

	if (digit_only == "digit_dot_null") {
		var valid = ".0123456789";
		var temp;
		var new_str;
		var is_dot;

		is_dot = 0;
		new_str = "";

		for ( var i = 0; i < str.length; i++) {
			temp = "" + str.substring(i, i + 1);
			// zamien , na .
			if (temp == ",") {
				temp = ".";
			}

			// sprawdz, czy jest juz drugi .
			if (temp == ".") {
				is_dot = is_dot + 1;

				if (is_dot > 1) {
					temp = "";
				}
			}

			if (valid.indexOf(temp) != "-1") {
				new_str = new_str + temp;
			}
		}
		str = new_str;

	}

	if (digit_only == "digit_null") {
		var valid = "0123456789";
		var temp;
		var new_str;
		var is_dot;

		is_dot = 0;
		new_str = "";

		for ( var i = 0; i < str.length; i++) {
			temp = "" + str.substring(i, i + 1);
			// zamien , na .
			if (temp == ",") {
				temp = ".";
			}

			// sprawdz, czy jest juz drugi .
			if (temp == ".") {
				is_dot = is_dot + 1;

				if (is_dot > 1) {
					temp = "";
				}
			}

			if (valid.indexOf(temp) != "-1") {
				new_str = new_str + temp;
			}
		}
		str = new_str;

	}

	if (digit_only == "digit_dot_null_minus") {
		var valid = ".-0123456789";
		var temp;
		var new_str;
		var is_dot;
		var is_minus;

		is_dot = 0;
		is_minus = 0;

		new_str = "";

		for ( var i = 0; i < str.length; i++) {
			temp = "" + str.substring(i, i + 1);
			// zamien , na .
			if (temp == ",") {
				temp = ".";
			}

			// sprawdz, czy jest juz drugi .
			if (temp == ".") {
				is_dot = is_dot + 1;

				if (is_dot > 1) {
					temp = "";
				}
			}

			if (temp == "-") {
				is_minus = is_minus + 1;

				if (is_minus > 1 || i > 0) {
					temp = "";
				}
			}

			if (valid.indexOf(temp) != "-1") {
				new_str = new_str + temp;
			}
		}
		str = new_str;

	}

	if (digit_only == "digit_null_minus") {
		var valid = "-0123456789";
		var temp;
		var new_str;
		var is_minus;
		
		is_minus = 0;

		new_str = "";

		for ( var i = 0; i < str.length; i++) {
			temp = "" + str.substring(i, i + 1);

			if (temp == "-") {
				is_minus = is_minus + 1;

				if (is_minus > 1 || i > 0) {
					temp = "";
				}
			}

			if (valid.indexOf(temp) != "-1") {
				new_str = new_str + temp;
			}
		}
		str = new_str;

	}

	// obciecie do maksymalnej dlugosci
	if (str.length > maxlen) {
		str = str.substring(0, maxlen);
	}

	if (original != str)
		change = 1;

	if (change == 1) {
		textbox.value = str;
	}

}