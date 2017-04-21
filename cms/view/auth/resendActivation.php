        <div class='col-lg-6 col-lg-offset-3 col-sm-8 col-sm-offset-2 vertical-space-lg'>
            <h4 class='text-center'>resend activation key</h4><br/>
            <form class='form-horizontal' name='login' action='<?php echo CONFIG_URL; ?>auth/resendActivation' method='post'>

                <div class='form-group'>
                    <label for='email' class='col-sm-2 control-label'>email</label>
                    <div class='col-sm-10'>
                        <input type='email' class='form-control' data-error='invalid email' name='email'>
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
<script>
$(document).ready(function() {
    $('#inputForm').validator();
});
</script>