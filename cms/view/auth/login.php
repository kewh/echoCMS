<?php
/**
 * view for auth/login
 *
 * @since 1.0.0
 * @author Keith Wheatley
 * @package echocms
 */
?>
        <div class='col-lg-6 col-lg-offset-3 col-sm-8 col-sm-offset-2 vertical-space-lg'>
            <form id='inputForm' class='form-horizontal' name='login' action='<?php echo CONFIG_URL; ?>auth/index' method='post'>

                <div class='form-group'>
                    <label for='inputEmail' class='col-sm-2 control-label'>email</label>
                    <div class='col-sm-10'>
                        <input name='email' type='email' class='form-control' data-error='invalid email'>
                        <div class='help-block with-errors'></div>
                    </div>
                </div>
                <div class='form-group'>
                    <label for='inputPassword' class='col-sm-2 control-label'>password</label>
                    <div class='col-sm-10'>
                        <input type='password' required data-minlength='6' data-error='invalid password' class='form-control' name='password'>
                        <div class='help-block with-errors'></div>

                    </div>
                </div>
                <div class='form-group'>
                    <div class='col-sm-12'><br>
                        <input name='rememberMe' id='rememberMe' type='checkbox' class='form-control' <?php if ($rememberMe) echo ' checked'; ?>>
                        <label for='rememberMe' class='control-label'>Remember me &nbsp;</label>
                        <button type='submit' class='btn btn-default pull-right'>login</button>
                    </div>
                </div>
                <div class='form-group'>
                    <div class='col-sm-12'>
                    <?php if ($result['error']) { ?>
                        <a class='pull-right' href='<?php echo CONFIG_URL; ?>auth/requestReset'>forgotten password?</a>
                    <?php } ?>
                    </div>
                </div>
            </form>
        </div>
<script src='<?php echo CONFIG_URL; ?>assets/js/validator.min.js'></script>
<script>
$(document).ready(function() {
    $('#inputForm').validator();
});
</script>
