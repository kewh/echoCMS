<?php
/**
 * view for auth/manageAccess
 *
 * @since 1.0.0
 * @author Keith Wheatley
 * @package echocms
 */
?>
<div class='col-xs-12 col-lg-10 col-lg-offset-1 marginTop'>
<table class='table table-condensed'>
    <thead class='header-title'>
        <tr>
            <th>IP</th>
            <th>date</th>
            <th>login</th>
        </tr>
    </thead>
    <tbody>
    <?php $lastAttempt['ip'] = null;
          $lastAttempt['date'] = null;
        foreach ($attempts as $attempt) { ?>
        <tr class='action'>
            <td><?php if ($attempt['ip'] != $lastAttempt['ip']) echo $attempt['ip']; ?> </td>
            <td><?php echo date( 'd M Y &\nb\sp; H:i', strtotime( $attempt['date'])); ?></td>
            <td <?php if ($attempt['email'] == 'failed access attempt') echo 'class="text-danger"';?>><?php echo $attempt['email'];?> </td>
        </tr>
    <?php $lastAttempt = $attempt;} ?>
    </tbody>
</table>
&nbsp;<br>
</div>
