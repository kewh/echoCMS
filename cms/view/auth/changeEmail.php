<?php
/**
 * view for auth/changeEmail
 *
 * @since 1.0.0
 * @author Keith Wheatley
 * @package echocms
 */
?>
        <div class='col-lg-6 col-lg-offset-3 col-sm-8 col-sm-offset-2 vertical-space-lg'>
            <h4 class='text-center'>change your email address </h4><br/>
            <form id= 'inputForm' class='form-horizontal' name='login' action='<?php echo CONFIG_URL; ?>auth/changeEmail' method='post'>

                <div class='form-group'>
                    <label for='newEmail' class='col-sm-2 control-label'>new email</label>
                    <div class='col-sm-10'>
                        <input name='newEmail' type='email' class='form-control' data-error='invalid email' >
                        <div class='help-block with-errors'></div>
                    </div>
                </div>

                <div class='form-group'>
                    <label for='password' class='col-sm-2 control-label'>password</label>
                    <div class='col-sm-10'>
                        <input type='password' class='form-control' name='password'>
                    </div>
                </div>

                <div class='form-group'>
                    <div class='col-sm-12'>
                        <button type='submit' class='btn btn-default pull-right'>Submit</button>
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
