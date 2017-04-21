    <div class='col-lg-6 col-lg-offset-3 col-sm-8 col-sm-offset-2 vertical-space-lg'>
        <h4 class='text-center'>activate your acccount</h4><br/>
        <form class='form-horizontal' name='login' action='<?php echo CONFIG_URL; ?>auth/activate' method='post'>
        
            <div class='form-group'>
                <label for='key' class='col-sm-2 control-label'>activation key</label>
                <div class='col-sm-10'>
                    <input class='form-control' name='key'>
                </div>
            </div>
        
            <div class='form-group'>
                <button type='submit' class='btn btn-default pull-right'>Submit</button>
            </div>

            <div class='form-group'>
                <a class='pull-right' href='<?php echo CONFIG_DIR; ?>auth/resendActivation'>resendActivation email?</a>
            </div>
    
        </form>
    </div>