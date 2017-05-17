<?php
/**
 * view for auth/changePassword
 *
 * @since 1.0.0
 * @author Keith Wheatley
 * @package echocms
 */
?>
        <div class='col-lg-6 col-lg-offset-3 col-sm-8 col-sm-offset-2 vertical-space-md'>
            <h4 class='text-center'>change your password</h4><br/>
            <form id='inputForm' class='form-horizontal' name='login' action='<?php echo CONFIG_URL; ?>auth/changePassword' method='post'>

                <div class='form-group'>
                    <label for='password' class='col-sm-2 control-label'>current</label>
                    <div class='col-sm-10'>
                        <input type='password' required data-minlength='6' data-error='invalid password' class='form-control' name='password' placeholder='password'>
                        <div class='help-block with-errors'></div>
                    </div>
                </div>

                <div class='form-group'>
                  <label for='newPassword' class='col-sm-2 control-label'>new</label>
                    <div class='col-sm-10'>
                        <input type='password' data-passwordValidate class='form-control' id='newPassword' name='newPassword'>
                        <div class='progress'>
                            <div id='password-strength-bar' class='progress-bar progress-bar-striped active'></div>
                        </div>
                    </div>
                </div>

                <div class='form-group'>
                  <label for='repeatPassword' class='col-sm-2 control-label'>repeat</label>
                    <div class='col-sm-10'>
                        <input type='password' data-match='#newPassword' data-match-error='passwords do not match' class='form-control' name='repeatPassword' placeholder='repeat password'>
                        <div class='help-block with-errors'></div>
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
<script src='https://cdnjs.cloudflare.com/ajax/libs/zxcvbn/1.0/zxcvbn.min.js'></script>
<script src='<?php echo CONFIG_URL; ?>assets/js/passwordValidator.js'></script>
