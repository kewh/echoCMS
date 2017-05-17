<?php
/**
 * view for admin/manageBackups
 *
 * @since 1.0.0
 * @author Keith Wheatley
 * @package echocms
 */
?>
    <div class='col-xs-4 col-xs-offset-3'>
        <h4 class='text-center'>image backups</h4>
    </div>
    <div class='col-xs-5 vertical-space-sm'>
        <button class='btn btn-sm btn-default backup'>create new backup</button>
    </div>

    <div class='col-lg-10 col-lg-offset-1 col-xs-12 vertical-space-sm'>
        <table class='table table-condensed'>
            <thead class='header-title'>
                <tr>
                    <th>date</th>
                    <th>backup archive</th>
                    <th>size</th>
                    <th></th>
                    <th>action</th>
                </tr>
            </thead>
            <tbody>
                <?php
                    foreach($backups as $backup) { ?>
                <tr>
                    <td><?php echo date( 'j M Y', strtotime(substr($backup['dir'], 7, 10))); ?></td>
                    <td><?php echo $backup['dir']; ?></td>
                    <td><?php echo $backup['size']; ?></td>
                    <td>
                        <button data-dir='<?php echo $backup['dir']; ?>' class='btn btn-xs btn-default download'>download</button>
                    </td>
                    <td>
                        <button data-dir='<?php echo $backup['dir']; ?>' class='btn btn-xs btn-default delete'>&nbsp; delete &nbsp;</button>
                    </td>
                    <td>
                        <button data-dir='<?php echo $backup['dir']; ?>' class='btn btn-xs btn-default restore'>restore to live</button>
                    </td>
                </tr>
                <?php } ?>
            </tbody>
        </table>
    </div>
<script>
$(document).ready(function() {
// Button Actions
    $('.modalAction').on('shown.bs.modal', function () {
        window.location = $location;
    });
    $('.modalInfo').on('shown.bs.modal', function () {
            $('.modalAction').modal('hide');
        window.location = $location;
    });

    $('.backup').on({
        click: function(){
            $location = '<?php echo CONFIG_URL; ?>admin/createImageBackup/';
            $('.modal-text').text('Creating new backup archive for images');
            $('.modalAction').modal('show');
        }
    });
    $('.delete').on({
        click: function(){
            $dir = $(this).data('dir');
            $location = '<?php echo CONFIG_URL; ?>admin/deleteImageBackup/'+$dir;
            $('.modal-text').text('Deleting backup archive for images');
            $('.modalAction').modal('show');
        }
    });
    $('.download').on({
        click: function(){
            $dir = $(this).data('dir');
            $location = '<?php echo CONFIG_URL; ?>admin/downloadImageBackup/'+$dir;
            $('.modal-text').text('Downloading image backup ZIP - check your download folder when complete');
            $('.modalInfo').modal('show');
        }
    });
    $('.restore').on({
        click: function(){
            $dir = $(this).data('dir');
            $location = '<?php echo CONFIG_URL; ?>admin/restoreImageBackup/'+$dir;
            $('.modal-text').text('Restoring backup images to live.');
            $('.modalInfo').modal('show');
        }
    });
});
</script>
