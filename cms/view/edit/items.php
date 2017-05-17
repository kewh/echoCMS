<?php
/**
 * view for edit/items
 *
 * @since 1.0.2
 * @author Keith Wheatley
 * @package echocms\edit
 */
?>
<div class='col-sm-10 col-sm-offset-1 vertical-space-sm'>
    <table class='table table-condensed'>
        <thead class='header-title'>
            <tr>
                <th>item</th>
                <th>date</th>
                <th>topic</th>
                <th>subtopic</th>
            </tr>
        </thead>
        <tbody>
        <?php
            $thisSubtopic = null;
            $thisTopic = null;
        ?>

        <?php
            foreach ($itemsList as $item) {
                if ($thisTopic != $item['topic']) echo '<tr></tr>';
        ?>

            <tr class='list-item' data-id='<?php echo $item['id'];?>' data-status='<?php echo $item['status'];?>'>

                <td>
                <?php
                    echo $item['heading'] . '&nbsp;';
                    if ($item['status'] =='update' || $item['status'] == 'draft')
                        echo '<span class="text-warning"> (' . $item['status'] . ' pending) &nbsp;</span>';
                ?>
                </td>

                <td>
                    <?php echo  date( 'd/m/Y &\nb\sp; H:i', strtotime( $item['date']));?>
                </td>

                <td>
                <?php
                    if ($thisTopic != $item['topic']) {
                        $thisTopic = $item['topic'];
                        $thisSubtopic = null;
                        echo $item['topic'];
                    }
                    elseif ($thisSubtopic != null){
                        echo '&nbsp;&nbsp;&nbsp;"';
                    }
                ?>
                </td>

                <td>
                <?php
                    if ($thisSubtopic != $item['subtopic']) {
                        $thisSubtopic = $item['subtopic'];
                        echo $item['subtopic'];
                    }
                    elseif ($thisSubtopic != null){
                        echo '&nbsp;&nbsp;&nbsp;"';
                    }
                    echo '<span class="fade glyphicon glyphicon-pencil badge pull-right"> </span>';
                ?>
                </td>

            </tr>
        <?php } ?>
        </tbody>
    </table>
    &nbsp;<br>
</div>

<script>
$(document).ready(function() {
    $( '.list-item' )
      .mouseenter(function() {
         $(this).find('.fade').addClass('in');
      })
      .mouseleave(function() {
         $(this).find('.fade').removeClass('in');
      })
      .click(function(){
         var $id = $(this).data('id');
         var $status = $(this).data('status');
         $href = '<?php echo CONFIG_URL; ?>edit/input/'+$id+'/'+$status;
         window.location.href=$href;
      });
});
</script>
