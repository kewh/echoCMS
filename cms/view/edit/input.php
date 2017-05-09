<?php
/**
 * view for edit/input
 *
 * @since 1.0.1
 * @author Keith Wheatley
 * @package echocms\edit
 */
?>
<!-- INPUT FORM  ****************************************************** -->
<div class='container-fluid'>
<form class='form-horizontal' name='inputForm' id='inputForm' action='<?php echo CONFIG_URL; ?>edit/input' method='post' enctype='multipart/form-data'>

<?php if (isset( $_POST['cropId']) && $_POST['cropId'] != ''){ ?>
    <input type='hidden' id='mx1' name='mx1' value='<?php echo $images['mx1'];?>' >
    <input type='hidden' id='mx2' name='mx2' value='<?php echo $images['mx2'];?>' >
    <input type='hidden' id='my1' name='my1' value='<?php echo $images['my1'];?>' >
    <input type='hidden' id='my2' name='my2' value='<?php echo $images['my2'];?>' >

    <input type='hidden' id='lx1' name='lx1' value='<?php echo $images['lx1'];?>' >
    <input type='hidden' id='lx2' name='lx2' value='<?php echo $images['lx2'];?>' >
    <input type='hidden' id='ly1' name='ly1' value='<?php echo $images['ly1'];?>' >
    <input type='hidden' id='ly2' name='ly2' value='<?php echo $images['ly2'];?>' >

    <input type='hidden' id='px1' name='px1' value='<?php echo $images['px1'];?>' >
    <input type='hidden' id='px2' name='px2' value='<?php echo $images['px2'];?>' >
    <input type='hidden' id='py1' name='py1' value='<?php echo $images['py1'];?>' >
    <input type='hidden' id='py2' name='py2' value='<?php echo $images['py2'];?>' >

    <input type='hidden' id='sx1' name='sx1' value='<?php echo $images['sx1'];?>' >
    <input type='hidden' id='sx2' name='sx2' value='<?php echo $images['sx2'];?>' >
    <input type='hidden' id='sy1' name='sy1' value='<?php echo $images['sy1'];?>' >
    <input type='hidden' id='sy2' name='sy2' value='<?php echo $images['sy2'];?>' >
<?php } ?>
    <input type='hidden' name='cropId' class='cropId' value=''>
    <input type='hidden' name='deleteImageId' class='deleteImageId' value=''>
    <input type='hidden' name='deleteDownload' class='deleteDownload' value=''>
    <input type='hidden' name='date' class='date'>
    <br>

    <!-- ACTION BUTTONS ********************************************* -->
    <div class='col-sm-11' style='margin-top:10px;'>
        <div class='text-center'>
            <button type='button' class='btn btn-default btn-xs saveButton'>save draft</button>
            <button type='button' class='btn btn-default btn-xs publishButton'>&nbsp;&nbsp; publish &nbsp;&nbsp;</button>
            <button type='button' class='btn btn-default btn-xs offlineButton'>take offline</button>
        </div>
        <br><br>
    </div>

    <!-- ITEM  ************************************************** -->
    <div class='col-xs-12 form-group-margin'>
        <div class='col-sm-5'>

            <!-- Status   ********************************************** -->
            <div class='form-group'>
                <label for='dateDisplay' class='col-sm-3 col-md-2 control-label'>status</label>
                <div class='col-sm-9 col-md-10'>
                    <span class='text-primary text-uppercase status <?php echo $class; ?>'><strong><?php echo $item['status']; ?>&nbsp;&nbsp;&nbsp;</strong></span>
                </div>
            </div>

            <!-- Date   ********************************************** -->
            <div class='form-group'>
                <label for='dateDisplay' class='col-sm-3 col-md-2 control-label'>date</label>
                <div class='col-sm-9 col-md-10'>
                    <input type='text' name='dateDisplay' class='dateDisplay col-xs-6 col-sm-8 col-md-6' value='<?php echo date( 'j M Y', strtotime( $item['date'])); ?>'>
                </div>
            </div>

            <!--  Page   ********************************************* -->
            <div class='form-group'>
                <label for='page' class='col-sm-3 col-md-2 control-label'>page</label>
                <div class='col-sm-9 col-md-10'>
                    <select name='page' required='required' class='page'>
                        <option value=''></option>
                        <?php
                        foreach ($pages as $page) {
                            echo '
                        <option value="' . $page . '"';
                            if ($page == $item['page'])
                                echo ' selected';
                            echo '>' . $page . '</option>';
                        } ?>
                    </select>
                </div>
            </div>

            <!--  Element    ********************************************* -->
            <div class='form-group'>
                <label for='element' class='col-sm-3 col-md-2 control-label'>element</label>
                <div class='col-sm-9 col-md-10'>
                    <select name='element' class='element'>
                        <option value=''></option>
                        <?php
                        foreach ($elements as $element) {
                            echo '
                        <option value="' . $element . '"';
                            if ($element == $item['element'])
                                echo ' selected';
                            echo '>' . $element . '</option>';
                        } ?>
                    </select>
                </div>
            </div>

            <!--  Tags    *********************************************** -->
            <div class='form-group form-group-margin'>
                <label for='tags' class='col-sm-3 col-md-2 control-label'>tags</label>
                <div class='col-sm-9 col-md-10'>
                    <input name='tags' type='text' class='tags'>
                </div>
            </div>


            <!-- Heading     ********************************************** -->
            <div class='form-group'>
                <label for='heading' class='col-sm-3 col-md-2 control-label'>heading</label>
                <div class='col-sm-9 col-md-10'>
                    <textarea class='heading' name='heading'><?php echo $item['heading']; ?></textarea>
                    <span class='textareaCounter counter1 pull-right'></span>
                </div>
            </div>

            <!-- Caption   ******************************************** -->
            <div class='form-group'>
                <label for='caption' class='col-sm-3 col-md-2 control-label'>caption</label>
                <div class='col-sm-9 col-md-10'>
                    <textarea class='caption' name='caption'><?php echo $item['caption']; ?></textarea>
                    <span class='textareaCounter counter2 pull-right'></span><br>
                </div>
            </div>

            <!-- File Download  **************************************** -->
            <?php
            if ($item['download_src'] == null)
                 $isDL = false;
            else $isDL = true;
            ?>

            <div class='form-group'>
                <label class='col-sm-3 col-md-2 control-label'>download</label>
                <div class='col-sm-9 col-md-10'>
                    <input type='file' name='download' onchange='return checkDownload();' id='download' class='inputfile' />
                    <label for='download'>
                        <span class='btn btn-default btn-xs'><?php if ($isDL) echo ' replace '; else echo ' add download '; ?></span>
                    </label>
                    <?php if ($isDL) echo "
                    <button type='button' class='btn btn-default btn-xs deleteDownload'> delete </button>
                    <textarea class='downloadTitle' name='downloadTitle'>" . $item['download_name'] . "</textarea>";
                    ?>
                    <span class='textareaCounter counter3 pull-right'></span>

                </div>
            </div>
        </div>

        <!-- TEXT        ************************************************** -->
        <div class='col-sm-7' style='padding-right:16px;'>
            <textarea id='textContent' name='text' rows='16'>
                <?php echo $item['text']; ?>
            </textarea>
        </div>
    </div>

    <!-- Add an image button  **************************************** -->
    <div class='col-xs-12'>
        <div class='form-group scrollTo col-sm-5'>
            <label for='postedImage' class='col-sm-3 col-md-2 control-label'>images</label>
            <div class='col-sm-9 col-md-10'>
                <input type='file' onchange='return checkImage();' id='postedImage' name='postedImage' class='inputfile' size='20' />
                <label for='postedImage'>
                    <span class='btn btn-default btn-xs'> add an image </span>
                </label>
            </div>
        </div>
    </div>

    <!-- IMAGES  ****************************************************** -->
    <div class='col-xs-12'>
        <input class='imageSorted' id='imageSorted' type='hidden' name='imageSorted' value='false'>
        <div class='sortable'>
            <?php
            $i = 0;
            foreach ( $item['images'] as $image ) {
            ?>
            <div class='col-xs-6 col-sm-3 col-md-2 imageMargins sortThis'>
                <img class='img-responsive' style='width: 100%;' src='<?php echo CONFIG_URL;?>content/images/thumbnail/<?php echo $image['src'];?>?nocache=<?php time();?>' alt='' >
                <input class='imgSeq' id='seq<?php echo $i;?>' type='hidden' name='imgSeq<?php echo $i;?>' value='<?php echo $i;?>' >
                <div class='buttons imageButtons imageMargins text-center'>
                    <button type='button' class='btn btn-default btn-xs cropImage'   data-id='<?php echo $i;?>'>&nbsp;crop&nbsp;</button>
                    <button type='button' class='btn btn-default btn-xs deleteImage' data-id='<?php echo $i;?>'>delete</button>
                    <?php if (count($item['images']) > 1) { ?>
                        <span class='glyphicon glyphicon-knight pull-right large '>&nbsp;</span>
                    <?php } ?>
                </div>
            </div>
            <?php
                $i = $i + 1;
            } ?>
        </div>
    </div>
</form>
</div>
<script src='http://code.jquery.com/ui/1.12.1/jquery-ui.min.js'></script>
<script src='//cdn.tinymce.com/4/tinymce.min.js'></script>
<script src='<?php echo CONFIG_URL; ?>assets/js/tinymce/plugins/lorumipsum/plugin.min.js'></script>
<script src='<?php echo CONFIG_URL; ?>assets/js/selectize.min.js'></script>
<script src='<?php echo CONFIG_URL; ?>assets/js/jquery.simplyCountable.js'></script>
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
                $('.modal-text').text('Image is too big to load, size is ' + sizeMB + 'MB but maximum allowed by server is ' + maxSizeMB + 'MB');
                $('.modalInfo').modal('show');
                document.forms.inputForm.postedImage.value = null;
                return false;
            }
        }
        else
        {
            $('.modal-text').text('Unable to check your file size with this browser but maximum allowed by the server is: ' + maxSizeMB +'MB');
            $('.modalInfo').modal('show');
        }
        // check for correct file type
        var img = document.forms.inputForm.postedImage.value;
        var ext = img.toLowerCase().split('.').slice(-1);
        if (img !== '' && ext != 'jpg' && ext != 'JPG' && ext != 'jpeg' && ext != 'JPEG' && ext != 'png' && ext != 'PNG' && ext != 'gif' && ext != 'GIF')
            {
                $('.modal-text').text('The selected image file is not a jpg, png or gif');
                $('.modalInfo').modal('show');

                document.forms.inputForm.postedImage.value = null;
                return false;
            }
        else
        {
            if (size === 0) sizeMB = '';
            else sizeMB = sizeMB + 'MB ';
            $('.modal-text').text('Adding '+ sizeMB + 'image');
            $('.modalAction').modal('show');
        }
    }

// Check Posted Download File
function checkDownload()
    {
        var maxSize = '<?php echo $postMaxSize; ?>';
        maxSizeMB = maxSize / 1048576;
        maxSizeMB = maxSizeMB.toFixed(2);
        var size = 0;
        var sizeMB = 0;
        // check file size (if the HTML5 Files API is available)
        if (window.File && window.FileReader && window.FileList && window.Blob)
        {
            size = document.getElementById('download').files[0].size;
            sizeMB = size / 1048576;
            sizeMB = sizeMB.toFixed(2);
            if ( size >= maxSize)
            {
                $('.modal-text').text('File is too big to load, size is ' + sizeMB + 'MB but maximum allowed by server is ' + maxSizeMB + 'MB');
                $('.modalInfo').modal('show');
                document.forms.inputForm.download.value = null;
                return false;
            }
        }
        else
        {
            $('.modal-text').text('Unable to check your file size with this browser but maximum allowed by the server is: ' + maxSizeMB + 'MB');
            $('.modalInfo').modal('show');
        }

        // check for correct file type
        var img = document.forms.inputForm.download.value;
        var ext = img.toLowerCase().split('.').slice(-1);
        if (img !== '' && ext != 'pdf' && ext != 'PDF')
            {
                $('.modal-text').text('The selected file is not a PDF');
                $('.modalInfo').modal('show');
                document.forms.inputForm.download.value = null;
                return false;
            }
        else
        {
            if (size === 0) sizeMB = '';
            else sizeMB = sizeMB + 'MB ';
            $('.modal-text').text('Adding '+ sizeMB + ' download file');
            $('.modalAction').modal('show');
        }
    }

$(document).ready(function() {

<?php if (!empty($_SESSION['scrollToImages'])) { ?>
    $('html, body').animate({
        scrollTop: $('.scrollTo').offset().top
    }, 'slow');
<?php } ?>

// Image Drag & Drop sequencing using jQuery-UI
    $( '.sortable' ).sortable({
        items: '.sortThis',
        cursor: 'move',
        update: function( event, ui ) {}
    });
    $( '.sortable' ).on( 'sortupdate', function( event, ui ) {
        $('.imageSorted').val('true');
        $( '.imgSeq' ).each(function( index ) {
            $(this).val(index);
        });
    });

//  SET UP TEXTAREA COUNTERS
  $('.heading').simplyCountable({
  counter: '.counter1',
        strictMax: true,
        maxCount: 255
  });

  $('.caption').simplyCountable({
  counter: '.counter2',
        strictMax: true,
        maxCount: 255
  });

  $('.downloadTitle').simplyCountable({
  counter: '.counter3',
        strictMax: true,
        maxCount: 255
  });

// SET UP TEXTAREA EDIT
    tinymce.init({
        content_css : '/cms/assets/css/echocms.css',
        plugins : 'lorumipsum advlist autolink code fullscreen paste preview link lists table textcolor',
        paste_as_text: true,
        selector: '#textContent',
        menubar : false,
        statusbar : false,
        toolbar: 'styleselect forecolor | indent outdent | undo redo | bullist numlist |  link unlink | table code lorumipsum fullscreen',
        relative_urls: true,
        branding: false
     });

//  SET UP PAGE SELECTION
    $('.page').selectize({
        persist: false,
        createOnBlur: true,
        hideSelected: true,
        maxItems: 1,
<?php if ($config['pages_updatable']) { ?>
        create: true,
        placeholder:'select or add 1 option'
<?php } else { ?>
        placeholder:'select 1 option'
<?php } ?>

    });

//  SET UP SECTION SELECTION
    $('.element').selectize({
        persist: false,
        createOnBlur: true,
        hideSelected: true,
        maxItems: 1,
<?php if ($config['elements_updatable']) { ?>
        create: true,
        placeholder:'select or add 1 option'
<?php } else { ?>
        placeholder:'select 1 option'
<?php } ?>
    });


//  SET UP TAGS
<?php
    $options = $items = null;
    foreach ($tags as $tag) {
        $options .= '{ label: "' .$tag.'",value: "' .$tag. '"},';
        if (in_array ($tag, $item['tags']))
            $items .= '"' .$tag.'",';
    }
?>
    $('.tags').selectize({
        persist: false,
        createOnBlur: true,
        create: true,
        hideSelected: true,
        placeholder:'select and/or add multiple options',
        valueField: 'value',
        labelField: 'label',
        options: [<?php echo $options;?>],
        items: [<?php echo $items;?>],
        maxItems: 99
    });

// SET UP DATE
    $('.dateDisplay').datepicker({
        dateFormat: 'd M yy',
        defaultDate: '<?php echo date( 'j M Y', strtotime( $_SESSION['item']['date'])); ?>',
        setDate: '<?php echo date( 'j M Y', strtotime( $_SESSION['item']['date'])); ?>'
    });

// EVENT HANDLERS ====================================================

// Counters
    $('.counter1').addClass('hide');
    $('.heading').on({
        keyup: function() {
            if ( $('.counter1').html() < 20)
                {
                   $('.counter1').addClass('show').removeClass('hide');
                }
            else{
                   $('.counter1').addClass('hide').removeClass('show');
                }
        }
    });

    $('.counter2').addClass('hide');
    $('.caption').on({
        keyup: function() {
            if ( $('.counter2').html() < 20)
                {
                   $('.counter2').addClass('show').removeClass('hide');
                }
            else{
                   $('.counter2').addClass('hide').removeClass('show');
                }
        }
    });

    $('.counter3').addClass('hide');
    $('.downloadTitle').on({
        keyup: function() {
            if ( $('.counter3').html() < 20)
                {
                   $('.counter3').addClass('show').removeClass('hide');
                }
            else{
                   $('.counter3').addClass('hide').removeClass('show');
                }
        }
    });

// Button Actions
    $('.modalAction').on('shown.bs.modal', function () {
        $('#inputForm').submit();
    });
    $('.saveButton').on({
        click: function(){
            $('#inputForm').attr('action', '<?php echo CONFIG_URL; ?>edit/update/save');
            $('.modal-text').text('Saving draft item');
            $('.modalAction').modal('show');
        }
    });
    $('.publishButton').on({
        click: function(){
            $('#inputForm').attr('action', '<?php echo CONFIG_URL; ?>edit/update/publish');
            $('.modal-text').text('Preparing item to go live');
            $('.modalAction').modal('show');
        }
    });
    $('.offlineButton').on({
        click: function(){
            $('#inputForm').attr('action', '<?php echo CONFIG_URL; ?>edit/update/offline');
            $('.modal-text').text('Taking item offline');
            $('.modalAction').modal('show');
        }
    });

    $('.cropImage').on({
        click: function(){
            $('.cropId').val( $(this).data('id') );
            $('#inputForm').attr('action', '<?php echo CONFIG_URL; ?>edit/image/' + $(this).data('id') );
            $('.modal-text').text('Preparing to crop image');
            $('.modalAction').modal('show');
        }
    });

    $('.deleteImage').on({
        click: function(){
            $('.deleteImageId').val( $(this).data('id'));
            $('.modal-text').text('Deleting image');
            $('.modalAction').modal('show');
        }
    });

    $('.deleteDownload').on({
        click: function(){
            $('.deleteDownload').val( 'deleteDownload' );
            $('.modal-text').text('Deleting download file');
            $('.modalAction').modal('show');
        }
    });
});
</script>
