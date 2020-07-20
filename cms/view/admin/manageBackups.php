<?php
/**
 * view for admin/manageBackups
 *
 * @since 1.0.13
 * @author Keith Wheatley
 * @package echocms
 */
?>
    <div class='col-xs-12'>
        <h4 class='text-center'>manage backups</h4>
    </div>
    <div class='col-xs-2 col-xs-offset-4 vertical-space-sm'>
        <button class='btn btn-sm btn-default backupImages'>backup images</button>
    </div>
    <div class='col-xs-2 vertical-space-sm'>
        <button class='btn btn-sm btn-default backupDatabase'>backup database</button>
    </div>
    <div class='col-lg-8 col-lg-offset-2 col-xs-10 col-xs-offset-1 vertical-space-sm'>
        <table class='table table-condensed vertical-space-sm'>
            <thead class='header-title'>
                <tr>
                    <th>date</th>
                    <th>backup archives on server</th>
                    <th>size</th>
                    <th> </th>
                    <th> </th>
                </tr>
            </thead>
            <tbody>
                <?php
                    foreach($backups as $backup) { ?>
                <tr>
                    <td><?php echo date( 'j M Y', strtotime(substr($backup['dir'], 0, 10))); ?></td>
                    <td><?php echo $backup['dir']; ?></td>
                    <td><?php echo $backup['size']; ?></td>
                    <td>
                        <button data-dir='<?php echo $backup['dir']; ?>' class='btn btn-xs btn-default download'>download</button>
                    </td>
                    <td>
                        <button data-dir='<?php echo $backup['dir']; ?>' class='btn btn-xs btn-default delete'>&nbsp; delete &nbsp;</button>
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

    $('.backupImages').on({
        click: function(){
            $location = '<?php echo CONFIG_URL; ?>admin/createImageBackup/';
            $('.modal-text').text('Creating new backup archive for images');
            $('.modalAction').modal('show');
        }
    });
    $('.backupDatabase').on({
        click: function(){
            $location = '<?php echo CONFIG_URL; ?>admin/createDatabaseBackup/';
            $('.modal-text').text('Creating new backup archive for Database');
            $('.modalAction').modal('show');
        }
    });
    $('.delete').on({
        click: function(){
            $dir = $(this).data('dir');
            $location = '<?php echo CONFIG_URL; ?>admin/deleteBackupArchive/'+$dir;
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
});
</script>
