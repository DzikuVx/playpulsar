//FIXME refactor this bullshit
/**
 * @param {String} str
 * @param {Object} textbox
 * @param {int} min
 * @param {int} max
 */
function maskNumber(str, textbox, min, max) {
	var valid = "0123456789",
        temp,
        new_str = '',
        i;

	for (i = 0; i < str.length; i++) {
		temp = "" + str.substring(i, i + 1);

		if (valid.indexOf(temp) != "-1") {
			new_str = new_str + temp;
		}
	}

	if (Math.round(new_str) < min) {
		new_str = min;
    }

	if (Math.round(new_str) > max) {
		new_str = max;
    }

	if (str != new_str) {
		textbox.value = new_str;
	}

}

/**
 * @param {String} str
 * @param {Object} textbox
 */
function maskPlot(str, textbox) {
    var original,
        valid = "0123456789",
        temp,
        new_str = '',
        i;

    original = str;

	for (i = 0; i < str.length; i++) {
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
 * @param {String} str
 * @param {Object} textbox
 * @param {int} maxlen
 * @param {int} digit_only
 */
function mask(str, textbox, maxlen, digit_only) {
	var change = 0,
        original = str,
        temp,
        new_str = '',
        valid,
        is_dot = 0,
        is_minus = 0,
        i;

	// Sprawdzenie, czy tylko liczby
	if (digit_only == 'simpleText') {
		valid = "0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ_@";

		for (i = 0; i < str.length; i++) {
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
		valid = "/abcdefghijklmnopqrstuwvxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789+-_*.,;@|#$%&()[]{}";

		for (i = 0; i < str.length; i++) {
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
		valid = " \'\"/abcdefghijklmnopqrstuwvxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789+-_*.,;:@|#$%&()[]{}?!ąĄęĘśŚćĆżŻźŹłŁóÓńŃ/";

		for (i = 0; i < str.length; i++) {
			temp = "" + str.substring(i, i + 1);
			if (valid.indexOf(temp) != "-1") {
				// jesli jest liczba
				new_str = new_str + temp;
			}
		}
		str = new_str;
	}

	if (digit_only == "digit_dot") {
		valid = ".0123456789";

		for (i = 0; i < str.length; i++) {
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
		valid = "0123456789";

		for (i = 0; i < str.length; i++) {
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
        valid = ".0123456789";

		for (i = 0; i < str.length; i++) {
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
		valid = "0123456789";

		for (i = 0; i < str.length; i++) {
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
        valid = ".-0123456789";

		for (i = 0; i < str.length; i++) {
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
		valid = "-0123456789";

		for (i = 0; i < str.length; i++) {
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

	if (str.length > maxlen) {
		str = str.substring(0, maxlen);
	}

	if (original != str)
		change = 1;

	if (change == 1) {
		textbox.value = str;
	}
}