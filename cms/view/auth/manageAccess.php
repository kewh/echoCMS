<?php
/**
 * view for auth/manageAccess
 *
 * @since 1.0.8
 * @author Keith Wheatley
 * @package echocms
 */
?>
<div class='col-xs-12 col-lg-10 col-lg-offset-1 marginTop'>
<table class='table table-condensed'>
    <thead class='header-title'>
        <tr>
            <th>IP</th>
            <th>login date</th>
            <th>userid</th>
        </tr>
    </thead>
    <tbody>
    <?php
        foreach ($attempts as $attempt) { ?>
        <tr class='action'>
            <td><?php echo $attempt['ip']; ?> </td>
            <td><?php echo date( 'd M Y &\nb\sp; H:i', strtotime( $attempt['date'])); ?></td>
            <td <?php if ($attempt['email'] == 'failed access attempt') echo 'class="text-danger"';?>><?php echo $attempt['email'];?> </td>
        </tr>
    <?php } ?>
    </tbody>
</table>
&nbsp;<br>
</div>
