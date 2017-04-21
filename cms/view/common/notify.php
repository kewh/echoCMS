    <div class='col-lg-6 col-lg-offset-3 col-sm-8 col-sm-offset-2 col-xs-10 col-xs-offset-1 vertical-space-lg alert modal-backdrop-notify'>
        <div class='text-center'>
            <h4>
                <span style='font-size:200%' class='glyphicon <?php if (empty($result['error'])) echo 'glyphicon-ok'; else echo 'glyphicon-thumbs-down text-danger'?>'>&nbsp;</span>
                <?php echo $result['message'];?>
            </h4>
        </div>
    </div>
    <?php $result['message'] = $result['error'] = null; // to stop modal being called by js in footer ?>