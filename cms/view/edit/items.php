<?php
/**
 * view for edit/items
 *
 * @since 1.0.14
 * @author Keith Wheatley
 * @package echocms\edit
 */
?>
<br>
<div class="container-fluid">
    <div class='row'>
        <div class='col-sm-2 font-weight-bold text-uppercase'>
            <strong>topic</strong>
        </div>
        <div class='col-sm-2 font-weight-bold text-uppercase'>
            <strong>subtopic</strong>
        </div>
        <div class='col-sm-3 font-weight-bold text-uppercase'>
            <strong>date</strong>
        </div>
        <div class='col-sm-5 font-weight-bold text-uppercase'>
            <strong>heading</strong>
        </div>
    </div>
</div>
<br>


<div class="container-fluid">
    <div class='row'>
        <div class='col-sm-2'>
            <div class='list-all' data-topic='All'>
                <i class="glyphicon glyphicon-play"></i>
                All
            </div>
         </div>
        <div class='col-sm-10'>
        </div>
    </div>
<?php
   $thisSubtopic = null;
   $thisTopic = null;
   foreach ($itemsList as $item) {
   if ($thisTopic != $item['topic']) {
?>
    <div class='row'>
        <div class='col-sm-2'>
            <div class='list-topic' data-topic='<?php echo $item['topic'];?>'>
                <i class="glyphicon glyphicon-play"></i>
                <?php echo $item["topic"];?>
            </div>
         </div>
        <div class='col-sm-10'>
        </div>
    </div>
<?php
    $thisTopic = $item['topic'];
    $thisSubtopic = null;
 } ?>

    <div class='row list-item <?php echo $item['topic'];?> displayNone'
        data-id='<?php echo $item['id'];?>' data-status='<?php echo $item['status'];?>'>
        <div class='col-xs-2'>&nbsp;
        </div>
        <div class='col-xs-10 list-item-hover-area'>
            <div class='col-xs-2'>&nbsp;
                  <?php
                      if ($thisSubtopic != $item['subtopic']) {
                          $thisSubtopic = $item['subtopic'];
                          echo $item['subtopic'];
                      }
                      elseif ($thisSubtopic != null){
                          echo '&nbsp;&nbsp;&nbsp;"';
                      }
                  ?>
            </div>

            <div class='col-xs-3'>
                        <?php echo  date( 'd/m/Y &\nb\sp; H:i', strtotime( $item['date']));?>
            </div>
            <div class='col-sm-7'>
                    <?php
                        if (strlen($item['heading']) > 60) echo substr($item['heading'], 0, 60).'...';
                        else echo $item['heading'] . '&nbsp;';
                        if ($item['status'] =='update' || $item['status'] == 'draft')
                            echo '<span class="text-warning"> (' . $item['status'] . ' pending) &nbsp;</span>';
                    ?>
            </div>
        </div>
    </div>
    <?php } ?>
</div>

<script>
$(document).ready(function() {
    $('.list-item')
        .click(function(){
           var $id = $(this).data('id');
           var $status = $(this).data('status');
           $href = '<?php echo CONFIG_URL; ?>edit/input/'+$id+'/'+$status;
           window.location.href=$href;
        });
    $('.list-topic')
        .click(function(){
           var $topic = '.'+$(this).data('topic');
           if ($(this).find('i').is('.openIcon') ) {
               $('.list-item').addClass('displayNone')
               $(this).find('i').removeClass('openIcon');
           }
           else {
               $('.list-item').addClass('displayNone')
               $($topic).removeClass('displayNone')
               $('.list-topic').find('i').removeClass('openIcon');
               $(this).find('i').addClass('openIcon');
           }
        });
    $('.list-all')
        .click(function(){
           if ($(this).find('i').is('.openIcon') ) {
               $('.list-item').addClass('displayNone');
               $('.list-topic').find('i').removeClass('openIcon');
               $('.list-all').find('i').removeClass('openIcon');
           }
           else {
               $('.list-item').removeClass('displayNone')
               $('.list-topic').find('i').addClass('openIcon');
               $('.list-all').find('i').addClass('openIcon');
           }
        });
});
</script>
