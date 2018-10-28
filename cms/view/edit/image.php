<?php
/**
 * view for edit/image
 *
 * @since 1.0.7
 * @author Keith Wheatley
 * @package echocms\edit
 */
?>
<div class='container-fluid'>

    <form class='form-horizontal' name='inputForm' id='inputForm' action='<?php echo CONFIG_URL; ?>edit/input' method='post' enctype='multipart/form-data'>

        <!-- HIDDEN INPUT FIELDS **************************** -->
        <input id='mx1' type='hidden' name='mx1' value='<?php echo $image['mx1'];?>' >
        <input id='mx2' type='hidden' name='mx2' value='<?php echo $image['mx2'];?>' >
        <input id='my1' type='hidden' name='my1' value='<?php echo $image['my1'];?>' >
        <input id='my2' type='hidden' name='my2' value='<?php echo $image['my2'];?>' >

        <input id='lx1' type='hidden' name='lx1' value='<?php echo $image['lx1'];?>' >
        <input id='lx2' type='hidden' name='lx2' value='<?php echo $image['lx2'];?>' >
        <input id='ly1' type='hidden' name='ly1' value='<?php echo $image['ly1'];?>' >
        <input id='ly2' type='hidden' name='ly2' value='<?php echo $image['ly2'];?>' >

        <input id='px1' type='hidden' name='px1' value='<?php echo $image['px1'];?>' >
        <input id='px2' type='hidden' name='px2' value='<?php echo $image['px2'];?>' >
        <input id='py1' type='hidden' name='py1' value='<?php echo $image['py1'];?>' >
        <input id='py2' type='hidden' name='py2' value='<?php echo $image['py2'];?>' >

        <input id='sx1' type='hidden' name='sx1' value='<?php echo $image['sx1'];?>' >
        <input id='sx2' type='hidden' name='sx2' value='<?php echo $image['sx2'];?>' >
        <input id='sy1' type='hidden' name='sy1' value='<?php echo $image['sy1'];?>' >
        <input id='sy2' type='hidden' name='sy2' value='<?php echo $image['sy2'];?>' >

        <input id='currentBgColor' type='hidden' name='currentBgColor' value='<?php echo $currentBgColor;?>'>

        <!-- Buttons  ************************************** -->
        <div class='col-xs-12 marginTop marginBottom'>
            <button class='btn btn-default btn-sm saveButton center-block'>confirm image settings</button>
        </div>

        <div class='col-xs-12 marginBottom'>

            <!-- Alt attribute text    *********************** -->
            <div class='col-xs-12 col-md-5 col-md-offset-1'>
                <label for='alt' class='control-label pull-left'>alt text </label>
                <textarea class='alt' name='alt'><?php echo $image['alt']; ?></textarea>
            </div>

            <!-- prime aspect ratio picker    *********************** -->
            <div class='col-xs-4 col-xs-offset-2 col-md-offset-0 col-md-2'>
                <label for='prime_aspect_ratio' class='control-label pull-left'>prime aspect ratio</label>
                <select class='form-control form-control-lg' name='prime_aspect_ratio'>
                <?php if ($image_create_landscape) {
                ?>
                  <option value='landscape' <?php if ($image['prime_aspect_ratio']=='landscape') echo 'selected'; ?>>landscape</option>
                <?php };
                      if ($image_create_portrait) {
                ?>
                  <option value='portrait' <?php if ($image['prime_aspect_ratio']=='portrait') echo 'selected'; ?>>portrait</option>
                <?php };
                      if ($image_create_panorama) {
                ?>
                  <option value='panorama' <?php if ($image['prime_aspect_ratio']=='panorama') echo 'selected'; ?>>panorama</option>
                <?php };
                      if ($image_create_square) {
                ?>
                  <option value='square' <?php if ($image['prime_aspect_ratio']=='square') echo 'selected'; ?>>square</option>
                <?php };
                ?>
                </select>
            </div>

            <!-- Background colour picker  ****************** -->
            <div class='col-xs-4 col-md-2'>
                <label for='pickAColor' class='control-label pull-left'>bg colour</label><br>
                <input type='text' value='<?php echo $currentBgColor;?>' name='pickAColor' class='pickAColor form-control pull-left'>
            </div>

        </div>

        <!-- IMAGE cropping areas ***************************** -->
        <div class='col-xs-12'>
<?php if ($image_create_panorama) {
?>
            <div class='sm-col-12 col-md-6'>
                <div class='imageMargins'>panorama
                    <img id='uncroppedImagePanorama' class='img-responsive' name='uncroppedImagePanorama'
                        src='<?php echo CONFIG_URL . 'content/images/uncropped/'. $image['src'];?>' alt=''>
                </div>
            </div>
<?php };
      if ($image_create_landscape) {
?>
            <div class='sm-col-12 col-md-6'>
                <div class='imageMargins'>landscape
                    <img id='uncroppedImageLand' class='img-responsive' name='uncroppedImageLand'
                        src='<?php echo CONFIG_URL . 'content/images/uncropped/'. $image['src'];?>' alt=''>
                </div>
            </div>
<?php };
      if ($image_create_portrait) {
?>
            <div class='sm-col-12 col-md-6'>
                <div class='imageMargins'>portrait
                    <img id='uncroppedImagePort' class='img-responsive' name='uncroppedImagePort'
                        src='<?php echo CONFIG_URL . 'content/images/uncropped/'. $image['src'];?>' alt=''>
                </div>
            </div>
<?php };
       if ($image_create_square) {
?>
            <div class='sm-col-12 col-md-6'>
                <div class='imageMargins'>square
                    <img id='uncroppedImageSquare' class='img-responsive' name='uncroppedImageSquare'
                        src='<?php echo CONFIG_URL . 'content/images/uncropped/'. $image['src'];?>' alt=''>
                </div>
            </div>
        </div>
 <?php }; ?>

        <!-- Buttons  ************************************** -->
        <div class='col-xs-12 marginTop marginBottom'>
            <button class='btn btn-default btn-sm saveButton center-block'>confirm image settings</button>
        </div>
    </form>
</div>
<script src='https://cdn.jsdelivr.net/gh/tapmodo/Jcrop@0.9.12/js/jquery.Jcrop.min.js'></script>
<script src='<?php echo CONFIG_URL; ?>assets/js/tinycolor-0.9.15.min.js'></script>
<script src='<?php echo CONFIG_URL; ?>assets/js/pick-a-color-1.2.3.min.js'></script>

<script>
    // Load data from PHP to JS variables
    var $mx1 =   '<?php echo $image["mx1"];?>';
    var $mx2 =   '<?php echo $image["mx2"];?>';
    var $my1 =   '<?php echo $image["my1"];?>';
    var $my2 =   '<?php echo $image["my2"];?>';
    var $lx1 =   '<?php echo $image["lx1"];?>';
    var $lx2 =   '<?php echo $image["lx2"];?>';
    var $ly1 =   '<?php echo $image["ly1"];?>';
    var $ly2 =   '<?php echo $image["ly2"];?>';
    var $px1 =   '<?php echo $image["px1"];?>';
    var $px2 =   '<?php echo $image["px2"];?>';
    var $py1 =   '<?php echo $image["py1"];?>';
    var $py2 =   '<?php echo $image["py2"];?>';
    var $sx1 =   '<?php echo $image["sx1"];?>';
    var $sx2 =   '<?php echo $image["sx2"];?>';
    var $sy1 =   '<?php echo $image["sy1"];?>';
    var $sy2 =   '<?php echo $image["sy2"];?>';
    var $width = '<?php echo $image["width"];?>';
    var $height= '<?php echo $image["height"];?>';

    var $image_ratio_panorama   = '<?php echo $image_ratio_panorama;?>';
    var $image_ratio_landscape  = '<?php echo $image_ratio_landscape;?>';
    var $image_ratio_portrait   = '<?php echo $image_ratio_portrait;?>';
    var $image_ratio_square     = '<?php echo $image_ratio_square;?>';
    var $image_bg_opacity       = '<?php echo $this->config["image_bg_opacity"];?>';

$(document).ready(function() {

    // IMAGE CROPPING
    // see http://stackoverflow.com/questions/13648162/using-jcrop-on-responsive-images

    var jcrop_api, boundx, boundy;

<?php if ($image_create_panorama) { ?>
    //  CROPPING - PANORAMA version of image
    $('#uncroppedImagePanorama').Jcrop({
        onSelect:   getCoordsPanorama,
        onChange:   getCoordsPanorama,
        setSelect:   [ $mx1, $my1, $mx2, $my2 ],
        aspectRatio: $image_ratio_panorama,
        allowSelect: false,
        bgColor: '<?php echo "#".$currentBgColor;?>',
        minSize: [100,100],
        handleOpacity: 0.9,
        bgOpacity: $image_bg_opacity,
        trueSize: [$width,$height]
        },function(){
            var bounds = this.getBounds();
            boundx = bounds[0];
            boundy = bounds[$image_ratio_panorama];
            apiPanorama = this;
    });
    function getCoordsPanorama(c)
    {
        if (parseInt(c.w) > 0){
            var rx = $width / c.w;
            var ry = $height / c.h;
            $('#uncroppedImageLand').css({
                width: Math.round(rx * boundx) + 'px',
                height: Math.round(ry * boundy) + 'px',
                marginLeft: '-' + Math.round(rx * c.x) + 'px',
                marginTop: '-' + Math.round(ry * c.y) + 'px'
            });
        }
        $('#mx1').val(Math.round(c.x));
        $('#my1').val(Math.round(c.y));
        $('#mx2').val(Math.round(c.x2));
        $('#my2').val(Math.round(c.y2));
    }
<?php };
      if ($image_create_landscape) {
?>
    //  CROPPING - LANDSCAPE VERSION OF IMAGE
    $('#uncroppedImageLand').Jcrop({
        onSelect:   getCoordsLand,
        onChange:   getCoordsLand,
        setSelect:   [ $lx1, $ly1, $lx2, $ly2 ],
        aspectRatio: $image_ratio_landscape,
        allowSelect: false,
        bgColor: '<?php echo "#".$currentBgColor;?>',
        minSize: [100,100],
        handleOpacity: 0.9,
        bgOpacity: $image_bg_opacity,
        trueSize: [$width,$height]
        },function(){
            var bounds = this.getBounds();
            boundx = bounds[0];
            boundy = bounds[$image_ratio_landscape];
            apiLand = this;
    });
    function getCoordsLand(c)
    {
        if (parseInt(c.w) > 0){
            var rx = $width / c.w;
            var ry = $height / c.h;
            $('#uncroppedImageLand').css({
                width: Math.round(rx * boundx) + 'px',
                height: Math.round(ry * boundy) + 'px',
                marginLeft: '-' + Math.round(rx * c.x) + 'px',
                marginTop: '-' + Math.round(ry * c.y) + 'px'
            });
        }
        $('#lx1').val(Math.round(c.x));
        $('#ly1').val(Math.round(c.y));
        $('#lx2').val(Math.round(c.x2));
        $('#ly2').val(Math.round(c.y2));
    }
<?php };
      if ($image_create_portrait) {
?>
    //  CROPPING - PORTRAIT VERSION OF IMAGE
    $('#uncroppedImagePort').Jcrop({
        onSelect:   getCoordsPort,
        onChange:   getCoordsPort,
        setSelect:   [ $px1, $py1, $px2, $py2 ],
        aspectRatio: $image_ratio_portrait,
        allowSelect: false,
        bgColor: '<?php echo "#".$currentBgColor;?>',
        minSize: [100,100],
        handleOpacity: 0.9,
        bgOpacity: $image_bg_opacity,
        trueSize: [$width,$height]
        },function(){
            var bounds = this.getBounds();
            boundx = bounds[0];
            boundy = bounds[$image_ratio_portrait];
            apiPort = this;
    });
    function getCoordsPort(c)
    {
        if (parseInt(c.w) > 0){
            var rx = $width / c.w;
            var ry = $height / c.h;

            $('#uncroppedImagePort').css({
                width: Math.round(rx * boundx) + 'px',
                height: Math.round(ry * boundy) + 'px',
                marginLeft: '-' + Math.round(rx * c.x) + 'px',
                marginTop: '-' + Math.round(ry * c.y) + 'px'
            });
        }
        $('#px1').val(Math.round(c.x));
        $('#py1').val(Math.round(c.y));
        $('#px2').val(Math.round(c.x2));
        $('#py2').val(Math.round(c.y2));
    }
<?php };
      if ($image_create_square) {
?>
    //  CROPPING - SQUARE VERSION OF IMAGE
    $('#uncroppedImageSquare').Jcrop({
        onSelect:   getCoordsSquare,
        onChange:   getCoordsSquare,
        setSelect:   [ $sx1, $sy1, $sx2, $sy2 ],
        aspectRatio: $image_ratio_square,
        allowSelect: false,
        bgColor: '<?php echo "#".$currentBgColor;?>',
        minSize: [100,100],
        handleOpacity: 0.9,
        bgOpacity: $image_bg_opacity,
        trueSize: [$width,$height]
        },function(){
            var bounds = this.getBounds();
            boundx = bounds[0];
            boundy = bounds[$image_ratio_square];
            apiSquare = this;
    });
    function getCoordsSquare(c)
    {
        if (parseInt(c.w) > 0){
            var rx = $width / c.w;
            var ry = $height / c.h;

            $('#uncroppedImageSquare').css({
                width: Math.round(rx * boundx) + 'px',
                height: Math.round(ry * boundy) + 'px',
                marginLeft: '-' + Math.round(rx * c.x) + 'px',
                marginTop: '-' + Math.round(ry * c.y) + 'px'
            });
        }
        $('#sx1').val(Math.round(c.x));
        $('#sy1').val(Math.round(c.y));
        $('#sx2').val(Math.round(c.x2));
        $('#sy2').val(Math.round(c.y2));
    }
<?php }; ?>

    //  COLOR PICKER
    $('.pickAColor').pickAColor({
        showSpectrum            : false,
        showAdvanced            : false,
        showSavedColors         : false,
        showHexInput            : false
    });

    $('.pickAColor').on('change', function (){
        var $newColor = $(this).val();    // format for pickAColor
        $('#currentBgColor').val($newColor);
        var $newColorHex = '#'+$newColor; // format for jCrop
        apiPanorama.setOptions({ bgColor: $newColorHex});
        apiPort.setOptions({ bgColor: $newColorHex});
        apiLand.setOptions({ bgColor: $newColorHex});
        apiSquare.setOptions({ bgColor: $newColorHex});
    });

    // Button Actions
    $('.modalAction').on('shown.bs.modal', function () {
            $('#inputForm').submit();
    });

    $('.saveButton').on({
        click: function(){
            $('.modal-text').text('Saving image crop');
            $('.modalAction').modal('show');
        }
    });
});
</script>
