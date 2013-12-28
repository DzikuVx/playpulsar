function checkRegisterForm() {

	var error = false,
        $login = $('#login'),
        $name = $('#name'),
        $passwordA = $('#passwordA'),
        $passwordB = $('#passwordB'),
        $agreement = $('#agreeement'),
        $email = $('#email');

	$('.control-group').removeClass('error').removeClass('success');
	$('.error-help').remove();
	
	if ($login.val().length < 4) {
        $login.parent().append('<p class="help-block error-help">Too short (min 4 chars)</a>');
        $login.parent().parent().addClass('error');
		error = true;
	} else {
        $login.parent().parent().addClass('success');
	}

	if ($name.val().length < 4) {
        $name.parent().append('<p class="help-block error-help">Too short (min 4 chars)</a>');
        $name.parent().parent().addClass('error');
		error = true;
	} else {
        $name.parent().parent().addClass('success');
	}
	
	if ($email.val().length < 4) {
        $email.parent().append('<p class="help-block error-help">Too short (min 4 chars)</a>');
        $email.parent().parent().addClass('error');
		error = true;
	} else {
        $email.parent().parent().addClass('success');
	}
	
	if ($passwordA.val().length < 6) {
        $passwordA.parent().append('<p class="help-block error-help">Password too short (min 6 chars)</a>');
        $passwordA.parent().parent().addClass('error');
		error = true;
	}
	else {
        $passwordA.parent().parent().addClass('success');
	}

    if ($passwordA.val() != $passwordB.val()) {
        $passwordB.parent().append('<p class="help-block error-help">Given passwords have to be identical</a>');
        $passwordB.parent().parent().addClass('error');
        error = true;
    } else {
        $passwordB.parent().parent().addClass('success');
    }

    if ($agreement.prop('checked') == false) {
        $agreement.parent().append('<p class="help-block error-help">You have to agree to Terms Of Service (TOS) before registering</a>');
        $agreement.parent().parent().addClass('error');
		error = true;
	} else {
        $agreement.parent().parent().addClass('success');
	}

	if (!error) {
		document.getElementById('registerForm').submit();
	}
}

function fbLoginCallback(response) {

    /** @namespace response.authResponse */
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