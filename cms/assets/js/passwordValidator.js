   /*
    * echocms - set up zxcvbn password strength estimator and link to form validator
	*
    */
$(document).ready(function() {
    $('#inputForm').validator({
        delay: 1000,
        custom: {
          passwordValidate: function($el) {
            if ($('#password-strength-bar').hasClass('progress-bar-danger')) return true;
          }
        }
    });

    $('#newPassword')
        .focusin(function() {
            $('#password-strength-bar').show();
        })
        .focusout(function() {
            if (!$('#password-strength-bar').hasClass('progress-bar-danger')) $('#password-strength-bar').hide();
        });

	UpdatePasswordStrengthBar();
	$('#newPassword').keyup(function (event) {
        UpdatePasswordStrengthBar();
	});

    function UpdatePasswordStrengthBar() {
        var $bar = '#password-strength-bar';
        var $password = $('#newPassword').val();
        if ($password) {
            var $strength = zxcvbn($password);
            if ($strength.score == 0) {
                $($bar).removeClass('progress-bar-warning progress-bar-success')
                        .addClass('progress-bar-danger')
                        .html('very weak').css('width', '15%');
            }
            else if ($strength.score == 1) {
                $($bar).removeClass('progress-bar-danger progress-bar-success')
                        .addClass('progress-bar-danger')
                        .html('weak').css('width', '30%');
            }
            else if ($strength.score == 2) {
                $($bar).removeClass('progress-bar-danger progress-bar-success')
                        .addClass('progress-bar-danger')
                        .html('weak').css('width', '40%');
            }
            else if ($strength.score == 3) {
                $($bar).removeClass('progress-bar-danger progress-bar-warning')
                        .addClass('progress-bar-success')
                        .html('average').css('width', '60%');
            }
            else if ($strength.score == 4) {
                $($bar).removeClass('progress-bar-danger progress-bar-warning')
                        .addClass('progress-bar-success')
                        .html('strong').css('width', '100%');
            }
        }
        else {
            $($bar).css('width', '0%');
        }
    }
});