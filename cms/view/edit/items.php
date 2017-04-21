

    <div class='col-sm-10 col-sm-offset-1 vertical-space-sm'>
      <table class='table table-condensed'>
          <thead class='header-title'>
              <tr>
                  <th>Page</th>
                  <th>Element</th>
                  <th>Date</th>
                  <th>Item</th>
              </tr>
          </thead>
          <tbody>


<?php
    $thisSection = null;
    $thisPage = null;
?>

<?php foreach ($itemsList as $item) { ?>

    <?php if ($thisPage != $item['page']) echo '<tr></tr>';?>

    <tr class='list-item' data-id='<?php echo $item['id'];?>' data-status='<?php echo $item['status'];?>'>
        <td>

    <?php   if ($thisPage != $item['page']) {
                $thisPage = $item['page'];
                $thisSection = null;
                echo $item['page'];
    } ?>

        </td>
        <td>

    <?php   if ($thisSection != $item['element']) {
                $thisSection = $item['element'];
                echo $item['element'];
    } ?>

        </td>
        <td>
                    <?php echo  date( 'd/m/Y &\nb\sp; H:i', strtotime( $item['date']));?>
        </td>
        <td>

                    <?php echo  $item['heading'] . '&nbsp;';
                          if ($item['status'] =='update' || $item['status'] == 'draft')
                            echo '<span class="text-warning"> (' . $item['status'] . ' pending) &nbsp;</span>';
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
