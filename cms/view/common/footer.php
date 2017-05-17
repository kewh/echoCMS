<?php
/**
 * view for common/footer
 *
 * @since 1.0.0
 * @author Keith Wheatley
 * @package echocms
 */
?>
        <footer class='footer'>
            <a class='pull-right' href='https://github.com/kewh/echoCMS'>
                <img src='<?php echo CONFIG_URL;?>assets/images/echocmsLogo.png' alt=''>
            </a>
            <span class='footer-text pull-right'>content management by</span>
        </footer>
    </div>
</div>
<script src='https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js' integrity='sha384-Tc5IQib027qvyjSMfHjOMaLkfuWVxZxUPnCJA7l2mCWNIpG9mGCD8wGNIcPD7Txa' crossorigin='anonymous'></script>
<script>
    // activate modal for error feedback from PHP found errors
    <?php if ( !empty($result['message'])) {
            echo ('$(".modal-text").text("' . $result['message'] . '");');
            if (empty($result['action']))
                echo '$(".modalInfo").modal("show");';
            else
                echo '$(".modalAction").modal("show");';
        } ?>

    // iOS viewport
    $(window).on('orientationchange', function() {
        if (window.orientation === 90 || window.orientation === -90 || window.orientation === 270) {
            $('meta[name="viewport"]').attr('content', 'height=device-width,width=device-height,initial-scale=1.0,maximum-scale=1.0');
        } else {
            $('meta[name="viewport"]').attr('content', 'height=device-height,width=device-width,initial-scale=1.0,maximum-scale=1.0');
        }
        window.scrollTo(0, 0);
    }).trigger('orientationchange');
</script>
</body>
</html>
