function checkRegisterForm() {

	var error = false;

	$('.control-group').removeClass('error');
	$('.control-group').removeClass('success');
	$('.error-help').remove();
	
	if ($('#login').val().length < 4) {
		$('#login').parent().append('<p class="help-block error-help">Too short (min 4 chars)</a>');
		$('#login').parent().parent().addClass('error');
		error = true;
	}
	else {
		$('#login').parent().parent().addClass('success');
	}

	if ($('#name').val().length < 4) {
		$('#name').parent().append('<p class="help-block error-help">Too short (min 4 chars)</a>');
		$('#name').parent().parent().addClass('error');
		error = true;
	}
	else {
		$('#name').parent().parent().addClass('success');
	}
	
	if ($('#email').val().length < 4) {
		$('#email').parent().append('<p class="help-block error-help">Too short (min 4 chars)</a>');
		$('#email').parent().parent().addClass('error');
		error = true;
	}
	else {
		$('#email').parent().parent().addClass('success');
	}
	
	if ($('#passwordA').val().length < 6) {
		$('#passwordA').parent().append('<p class="help-block error-help">Password too short (min 6 chars)</a>');
		$('#passwordA').parent().parent().addClass('error');
		error = true;
	}
	else {
		$('#passwordA').parent().parent().addClass('success');
	}

	if ($('#passwordA').val() != $('#passwordB').val()) {
		$('#passwordB').parent().append('<p class="help-block error-help">Given passwords have to be identical</a>');
		$('#passwordB').parent().parent().addClass('error');
		error = true;
	}
	else {
		$('#passwordB').parent().parent().addClass('success');
	}

	if ($('#agreeement').prop('checked') == false) {
		$('#agreeement').parent().append('<p class="help-block error-help">You have to agree to Terms Of Service (TOS) before registering</a>');
		$('#agreeement').parent().parent().addClass('error');
		
		error = true;
	}
	else {
		$('#agreeement').parent().parent().addClass('success');
	}

	if (!error) {
		document.getElementById('registerForm').submit();
	} else {
		return false;
	}
}

function fbLoginCallback(response) {

	if (response.authResponse) {
		$('[name=useFacebookID]').val('true');
		$('[name=login]').val('');
		$('[name=password]').val('');
		document.forms["loginForm"].submit();
	} else {
		/*
		 * Użytkownik nie jest zalogowany...
		 */
		alert("You are not logged in on Facebook, or you have not authorized Pulsar Online");
	}
}