<div class='container-fluid form-fluid'>
<form class='form' name='inputForm' id='inputForm' action='<?php echo CONFIG_URL; ?>config/editConfig' method='post' enctype='multipart/form-data'>

    <div class='row'>
        <div class='col-xs-12 marginTop text-center'>
            <button type='submit' class='btn btn-default btn-lg saveButton'>save updates</button>
        </div>
    </div>

    <!-- CMS configuration  ************************************************** -->
    <div class='row'>
        <fieldset>
        <legend>CMS Configuration</legend>
        <!-- cms_page_logo     ********************************************** -->
        <div class='form-group form-group-sm-margin col-xs-12 col-sm-11 col-sm-offset-1' style='margin-top:15px;margin-bottom:0;'>
            <label for='postedImage'><span class='btn btn-default btn-xs'>&nbsp;change logo&nbsp;</span></label>
            <input class='inputfile' type='file' onchange='return checkImage();' id='postedImage' name='postedImage'>
            <div class='help-block with-errors'></div>
        </div>

        <!-- site_name     ********************************** -->
        <div class='form-group col-xs-12 col-sm-5 col-sm-offset-1'>
            <label for='site_name' class='control-label'>title</label>
            <input name='site_name' type='text' data-error='enter title' class='form-control' value='<?php echo $config['site_name']; ?>'>
            <div class='help-block with-errors'></div>
        </div>

        <!-- date_format     ********************************************** -->
        <div class='form-group col-xs-6  col-sm-2'>
            <label for='date_format' class='control-label'>date format</label>
            <input name='date_format' type='text' pattern='^[dDjSFmMnYy ]*$' data-error='DjSF mMn Yy' class='form-control text-center'  value='<?php echo $config['date_format']; ?>'>
            <div class='help-block with-errors'></div>
        </div>

        <!-- site_timezone  the timezone for correct datetime values -->
        <div class='form-group col-xs-7 col-sm-3'>
            <label for='site_timezone' class='control-label'>timezone</label>
            <input name='site_timezone' type='text' data-checkTimezone data-error='invalid - for list of timezones see http://php.net/manual/en/timezones.php' class='form-control' value='<?php echo $config['site_timezone']; ?>'>
            <div class='help-block with-errors'></div>
        </div>
        </fieldset>

        <fieldset>

    <!-- CONTENT STRUCTURE  ****************************************** -->
        <legend>content structure</legend>
        <!--  Pages    *********************************************** -->
        <div class='form-group col-xs-11 col-sm-9 col-sm-offset-1'>
            <label for='pages' class='control-label'>pages</label>
            <select name='pages[]' multiple class='pages'>
                <option value='' disable></option>
                <?php
                foreach ($config['pages'] as $page) {
                    echo '<option value="' . $page . '" selected>' . $page . '</option>';
                } ?>
            </select>
        </div>

        <!-- Pages updatable by user -  on if user can update pages defined by Admin -->
        <div class='form-group col-xs-1 col-sm-2'>
            <input name='pages_updatable' id='pages_updatable' type='checkbox' class='form-control' value='1'
            <?php if ($config['pages_updatable'] == 1) echo ' checked'; ?>>
            <label for='pages_updatable' class='control-label text-right'>updatable<br></label>
       </div>

       <!--  Elements    *********************************************** -->
       <div class='form-group col-xs-11 col-sm-9 col-sm-offset-1'>
           <label for='elements' class='control-label'>elements</label>
           <select name='elements[]' multiple class='elements'>
               <option value='' disable></option>
               <?php
               foreach ($config['elements'] as $element) {
                   echo '<option value="' . $element . '" selected>' . $element . '</option>';
               } ?>
           </select>
       </div>

       <!-- Elements updatable by user -   on if user can update elements defined by Admin -->
       <div class='form-group col-xs-1 col-sm-2'>
           <input name='elements_updatable' id='elements_updatable' type='checkbox' class='form-control' value='1'
           <?php if ($config['elements_updatable'] == 1) echo ' checked'; ?>>
           <label for='elements_updatable' class='control-label text-right'>updatable<br></label>
      </div>

      </fieldset>
    </div>

    <!-- IMAGEs   ************************************************** -->
    <div class='row'>
        <fieldset>
        <legend>images</legend>

        <div class='col-xs-12 col-sm-2 col-sm-offset-1'>
            <!-- image_bg_crop     ********************************************** -->
                <div class='form-group col-xs-3 col-sm-12'>
                    <label for='image_bg_crop' class='control-label text-center'>cropping bg</label>
                    <input name='image_bg_crop' type='text' class='image_bg_crop' value='<?php echo $config['image_bg_crop']; ?>'>
                    <div class='help-block with-errors'></div>
                </div>

            <!-- image_quality     ********************************************** -->
                <div class='form-group col-xs-3 col-sm-12'>
                    <label for='image_quality' class='control-label'>quality</label>
                    <input name='image_quality' type='text' pattern='^[1-9][0-9]?$|^100$' data-error='1-100' class='form-control text-center' value='<?php echo $config['image_quality']; ?>'>
                    <div class='help-block with-errors'></div>
                </div>
        </div>

        <div class='col-xs-12 col-sm-9'>

            <!-- image_ratio_landscape     ********************************************** -->
            <div class='col-xs-12'>
                <div class='col-xs-2 form-group-heading text-right form-group-margin-top'>
                    landscape
                </div>

                <div class='col-xs-2 form-group'>
                    <input name='image_create_landscape' id='image_create_landscape' type='checkbox' class='form-control'
                    <?php if ($config['image_create_landscape']) echo ' checked'; ?>>
                    <label for='image_create_landscape' class='control-label pull-right'><br></label>
                </div>
                <div class='col-xs-3 form-group text-center'>
                    <label for='image_ratio_landscape' class='control-label '>aspect ratio</label>
                    <input name='image_ratio_landscape' type='text' pattern='[1-9]+\:[1-9]+' data-error='use format 99:99' class='form-control text-center'  value='<?php echo $config['image_ratio_landscape']; ?>'>
                    <div class='help-block with-errors'></div>
                </div>
                <div class='col-xs-3 form-group form-group-sm-margin text-center'>
                    <label for='image_width_landscape' class='control-label'>base width</label>
                    <input name='image_width_landscape' type='text' pattern='^[1-9][0-9]*$' data-error='numeric only' class='form-control text-center' value='<?php echo $config['image_width_landscape']; ?>'>
                    <div class='help-block with-errors'></div>
                </div>
                <div class='col-xs-2 form-group'>
                    <input name='image_sizes_landscape' id='image_sizes_landscape' type='checkbox' class='form-control'
                    <?php if ($config['image_sizes_landscape']) echo ' checked'; ?>>
                    <label for='image_sizes_landscape' class='control-label'>2 &amp; 3x<br></label>
                </div>
            </div>

            <!-- image_ratio_portrait     ********************************************** -->
           <div class='col-xs-12'>
                <div class='col-xs-2 form-group-heading text-right'>
                    portrait
                </div>
                <div class='col-xs-2 form-group'>
                    <input name='image_create_portrait' id='image_create_portrait' type='checkbox' class='form-control no-top-margin'
                    <?php if ($config['image_create_portrait']) echo ' checked'; ?>>
                    <label for='image_create_portrait' class='control-label pull-right'></label>
                </div>
                <div class='col-xs-3 form-group'>
                    <input name='image_ratio_portrait' type='text' pattern='[1-9]+\:[1-9]+' data-error='use format 99:99' class='form-control text-center'  value='<?php echo $config['image_ratio_portrait']; ?>'>
                    <div class='help-block with-errors'></div>
                </div>
                <div class='col-xs-3 form-group'>
                    <input name='image_width_portrait' type='text' pattern='^[1-9][0-9]*$' data-error='numeric only' class='form-control text-center' value='<?php echo $config['image_width_portrait']; ?>'>
                    <div class='help-block with-errors'></div>
                </div>
                <div class='col-xs-2 form-group'>
                    <input name='image_sizes_portrait' id='image_sizes_portrait' type='checkbox' class='form-control no-top-margin'
                    <?php if ($config['image_sizes_portrait']) echo ' checked'; ?>>
                    <label for='image_sizes_portrait' class='control-label'></label>
                </div>
            </div>

            <!-- image_ratio_panorama     ********************************************** -->
           <div class='col-xs-12'>
                <div class='col-xs-2 form-group-heading text-right'>
                    panorama
                </div>
                <div class='col-xs-2 form-group'>
                    <input name='image_create_panorama' id='image_create_panorama' type='checkbox' class='form-control no-top-margin'
                    <?php if ($config['image_create_panorama']) echo ' checked'; ?>>
                    <label for='image_create_panorama' class='control-label pull-right'></label>
                </div>
                <div class='col-xs-3 form-group'>
                    <input name='image_ratio_panorama' type='text' pattern='[1-9]+\:[1-9]+' data-error='use format 99:99' class='form-control text-center'  value='<?php echo $config['image_ratio_panorama']; ?>'>
                    <div class='help-block with-errors'></div>
                </div>
                <div class='col-xs-3 form-group'>
                    <input name='image_width_panorama' type='text' pattern='^[1-9][0-9]*$' data-error='numeric only' class='form-control text-center' value='<?php echo $config['image_width_panorama']; ?>'>
                    <div class='help-block with-errors'></div>
                </div>
                <div class='col-xs-2 form-group'>
                    <input name='image_sizes_panorama' id='image_sizes_panorama' type='checkbox' class='form-control no-top-margin'
                    <?php if ($config['image_sizes_panorama']) echo ' checked'; ?>>
                    <label for='image_sizes_panorama' class='control-label'></label>
                </div>
            </div>

            <!-- image_ratio_square     ********************************************** -->
           <div class='col-xs-12'>
                <div class='col-xs-2 form-group-heading text-right'>
                    square
                </div>
                <div class='col-xs-2 form-group'>
                    <input name='image_create_square' id='image_create_square' type='checkbox' class='form-control no-top-margin'
                    <?php if ($config['image_create_square']) echo ' checked'; ?>>
                    <label for='image_create_square' class='control-label pull-right'></label>
                </div>
                <div class='col-xs-3 form-group'>
                    <input name='image_ratio_square' type='text' pattern='[1-9]+\:[1-9]+' data-error='use format 99:99' class='form-control text-center'  value='<?php echo $config['image_ratio_square']; ?>'>
                    <div class='help-block with-errors'></div>
                </div>
                <div class='col-xs-3 form-group'>
                    <input name='image_width_square' type='text' pattern='^[1-9][0-9]*$' data-error='numeric only' class='form-control text-center' value='<?php echo $config['image_width_square']; ?>'>
                    <div class='help-block with-errors'></div>
                </div>
                <div class='col-xs-2 form-group'>
                    <input name='image_sizes_square' id='image_sizes_square' type='checkbox' class='form-control no-top-margin'
                    <?php if ($config['image_sizes_square']) echo ' checked'; ?>>
                    <label for='image_sizes_square' class='control-label'></label>
                </div>
            </div>

        </div>
        </fieldset>
    </div>

    <!-- AUTHENTICATION  ************************************************** -->
    <div class='row'>
        <fieldset>
        <legend>authentication</legend>

        <!-- remember_me_days     ********************************************** -->
        <div class='form-group col-xs-4 col-sm-2 col-sm-offset-1'>
            <label for='remember_me_days' class='control-label'>remember me<br>days</label>
            <input name='remember_me_days' type='text' pattern='^[1-9][0-9]*$' data-error='numeric only' class='form-control text-center' value='<?php echo $config['remember_me_days']; ?>'>
            <div class='help-block with-errors'></div>
        </div>

        <!-- ip_ban_minutes     ********************************************** -->
        <div class='form-group col-xs-4 col-sm-2'>
            <label for='ip_ban_minutes' class='control-label'>IP ban<br>minutes</label>
            <input name='ip_ban_minutes' type='text' pattern='^[1-9][0-9]*$' data-error='numeric only' class='form-control text-center' value='<?php echo $config['ip_ban_minutes']; ?>'>
            <div class='help-block with-errors'></div>
        </div>

        <!-- ip_ban_attempts     ********************************************** -->
        <div class='form-group col-xs-4 col-sm-2'>
            <label for='ip_ban_attempts' class='control-label'>IP ban<br>attempts</label>
            <input name='ip_ban_attempts' type='text' pattern='^[1-9][0-9]*$' data-error='numeric only' class='form-control text-center' value='<?php echo $config['ip_ban_attempts']; ?>'>
            <div class='help-block with-errors'></div>
        </div>

        <!-- bcrypt_cost     ********************************************** -->
        <div class='form-group col-xs-4 col-sm-2'>
            <label for='bcrypt_cost' class='control-label'>bcrypt<br>cost</label>
            <input name='bcrypt_cost' type='text' pattern='^([1-9]|1\d|20)$' data-error='1-20' class='form-control text-center' value='<?php echo $config['bcrypt_cost']; ?>'>
            <div class='help-block with-errors'></div>
        </div>

        <!-- password_reset_minutes     ********************************************** -->
        <div class='form-group col-xs-4 col-sm-2'>
            <label for='password_reset_minutes' class='control-label'>password reset<br>minutes</label>
            <input name='password_reset_minutes' type='text' pattern='^[1-9][0-9]*$' data-error='numeric only' class='form-control text-center' value='<?php echo $config['password_reset_minutes']; ?>'>
            <div class='help-block with-errors'></div>
        </div>

        </fieldset>
    </div>

    <!-- EMAIL settings  ************************************************** -->
    <div class='row'>
        <fieldset>
        <legend>email</legend>

        <!-- email_notifications_on      on if notifications by email are required -->
        <div class='form-group col-xs-6 col-sm-2 col-sm-offset-1'>
            <input name='email_notifications_on' id='email_notifications_on' type='checkbox' class='form-control'
            <?php if ($config['email_notifications_on']) echo ' checked'; ?>>
            <label for='email_notifications_on' class='control-label'>send user emails<br></label>
       </div>

        <!-- site_email - to send activation and password reset emails -->
        <div class='form-group col-xs-12 col-sm-5'>
            <label for='site_email' class='control-label'>site email</label>
            <input name='site_email' type='email' data-error='invalid email' class='form-control' value='<?php echo $config['site_email']; ?>'>
            <div class='help-block with-errors'></div>
        </div>
    </div>

    <!-- SMTP settings  ************************************************** -->
    <div class='row'>
        <fieldset>
        <legend>smtp server</legend>
        <!-- smtp      on if SMTP is to be used, off if Mail is to be used -->
        <div class='form-group col-xs-6 col-sm-1 col-sm-offset-1'>
            <input name='smtp' id='smtp' type='checkbox' class='form-control' value='1'
            <?php if ($config['smtp'] == 1) echo ' checked'; ?>>
            <label for='smtp' class='control-label'>smtp<br></label>
       </div>

        <!-- smtp_auth      on if the SMTP server requires authentication -->
        <div class='form-group col-xs-6 col-sm-2'>
            <input name='smtp_auth' id='smtp_auth' type='checkbox' class='form-control' value='1'
            <?php if ($config['smtp_auth'] == 1) echo ' checked'; ?>>
            <label for='smtp_auth' class='control-label'>authentication<br></label>
       </div>

        <!-- smtp_security  0 for no encryption, tls for TLS encryption, ssl for SSL encryption -->
        <div class='form-group col-xs-6 col-sm-2'>
            <label for='smtp_security' class='control-label'>encryption</label>
            <select class='form-control' name='smtp_security'>
                  <option value='0' <?php if($config['smtp_security'] =='0') echo 'selected="selected"';?>>none</option>
                  <option value='tls' <?php if($config['smtp_security'] =='tls') echo 'selected="selected"';?>>TLS</option>
                  <option value='ssl' <?php if($config['smtp_security'] =='ssl') echo 'selected="selected"';?>>SSL</option>
            </select>
            <div class='help-block with-errors'></div>
       </div>

        <!-- smtp_port      port for the SMTP server -->
        <div class='form-group col-xs-6 col-sm-2'>
            <label for='smtp_port' class='control-label'>port</label>
            <input name='smtp_port' type='text' pattern='^[1-9][0-9]*$' data-error='invalid port' class='form-control text-center' value='<?php echo $config['smtp_port']; ?>'>
            <div class='help-block with-errors'></div>
       </div>

        <!-- smtp_host      hostname of the SMTP server -->
        <div class='form-group col-xs-12 col-sm-3'>
            <label for='smtp_host' class='control-label'>hostname</label>
            <input name='smtp_host' type='text' data-error='invalid host' class='form-control' value='<?php echo $config['smtp_host']; ?>'>
            <div class='help-block with-errors'></div>
       </div>

        <!-- smtp_username  username for the SMTP server -->
        <div class='form-group col-xs-12 col-sm-5 col-sm-offset-1'>
            <label for='smtp_username' class='control-label'>username</label>
            <input name='smtp_username' type='text' data-error='invalid smtp username' class='form-control' value='<?php echo $config['smtp_username']; ?>'>
            <div class='help-block with-errors'></div>
       </div>

        <!-- smtp_password  the password for the SMTP server -->
        <div class='form-group col-xs-12 col-sm-5'>
            <label for='smtp_password' class='control-label'>password</label>
            <input name='smtp_password' type='text' data-error='invalid password' class='form-control' value='<?php echo $config['smtp_password']; ?>'>
            <div class='help-block with-errors'></div>
       </div>

        <!-- ACTION BUTTONS ********************************************* -->
        <div class='col-xs-12 text-center'>
            <button type='submit' class='btn btn-default btn-lg saveButton'>save updates</button>
        </div><br>
        </fieldset>
    </div>
</form>
</div>

<script src='<?php echo CONFIG_URL; ?>assets/js/validator.min.js'></script>
<script src='<?php echo CONFIG_URL; ?>assets/js/tinycolor-0.9.15.min.js'></script>
<script src='<?php echo CONFIG_URL; ?>assets/js/pick-a-color-1.2.3.min.js'></script>
<script src='<?php echo CONFIG_URL; ?>assets/js/selectize.min.js'></script>
<script>

// Check Posted Image
function checkImage()
    {
        var maxSize = '<?php echo $postMaxSize; ?>';
        maxSizeMB = maxSize / 1048576;
        var maxSizeMB = maxSizeMB.toFixed(2);
        var size = 0;
        var sizeMB = 0;
        // check file size (if the HTML5 Files API is supported)
        if (window.File && window.FileReader && window.FileList && window.Blob)
        {
            size = document.getElementById('postedImage').files[0].size;
            sizeMB = size / 1048576;
            sizeMB = sizeMB.toFixed(2);
            if (size >= maxSize)
            {
                alert ('Image is too big to load, size is ' + sizeMB + 'MB but maximum allowed by server is ' + maxSizeMB + 'MB');
                document.forms.inputForm.postedImage.value = null;
                return false;
            }
        }
        else
            {alert ('Unable to check your file size with this browser but maximum allowed by the server is: ' + maxSizeMB +'MB');
            }
        // check for correct file type
        var img = document.forms.inputForm.postedImage.value;
        var ext = img.toLowerCase().split('.').slice(-1);
        if (img !== '' && ext != 'jpg' && ext != 'JPG' && ext != 'jpeg' && ext != 'JPEG' && ext != 'png' && ext != 'PNG' && ext != 'gif' && ext != 'GIF')
            {
                alert('The selected image file is not a jpg, png or gif');
                document.forms.inputForm.postedImage.value = null;
                return false;
            }
        else
        {
            if (size === 0) sizeMB = '';
            else sizeMB = sizeMB + 'MB ';
            $('.modal-text').text('Adding '+ sizeMB + 'image');
            $('.modal').modal('show');
            $('#inputForm').submit();
        }
    }


// Array of valid timezones used to check for valid form input
    var $timezones = [
    <?php
        $comma = ' ';
        foreach ($timezones as $timezone) {
            echo $comma . '"' . $timezone . '"';
            $comma = ', ';
        }
    ?>
    ];

$(document).ready(function() {

// Store values of checkboxes in local storage, to keep state if page is reloaded
    var checkboxValues = JSON.parse(localStorage.getItem('checkboxValues')) || {};
    $.each(checkboxValues, function(key, value) {
      $('#' + key).prop('checked', value);
    });
    $('input:checkbox').on('change', function(){
      $('input:checkbox').each(function(){
        checkboxValues[this.id] = this.checked;
      });
      localStorage.setItem('checkboxValues', JSON.stringify(checkboxValues));
    });


// Form validation ==================================================
    $('#inputForm').validator({
        custom: {
            checkTimezone: function($el) {
                if ( $.inArray($el.val(), $timezones) === -1) {
                  return true; //error
                }
            }
        }
    });

// Set up color picker for image cropping background ================
    $('.image_bg_crop').pickAColor({
        showSpectrum            : false,
        showAdvanced            : false,
        showSavedColors         : false,
        showHexInput            : false
    });
    $('.pickAColor').on('change', function (){
        var $newColor = $(this).val();
        $(this).val($newColor);
    });

  //  SET UP PAGES SELECTION
      $('.pages').selectize({
          persist: false,
          createOnBlur: true,
          create: true,
          hideSelected: true,
          placeholder:'select multiple options or add new',
          maxItems: 99
      });

  //  SET UP ELEMENTS SELECTION
      $('.elements').selectize({
          persist: false,
          createOnBlur: true,
          create: true,
          hideSelected: true,
          placeholder:'select multiple options or add new',
          maxItems: 99
      });
});
</script>
