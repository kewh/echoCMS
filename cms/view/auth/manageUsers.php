<div class='col-xs-12 col-lg-10 col-lg-offset-1 marginTop'>
<table class='table table-condensed'>
    <thead class='header-title'>
        <tr>
            <th>email/userid</th>
            <th>registered</th>
            <th>last login</th>
            <th>last IP</th>
            <th class='text-center'>remember me<br> cookie</th>
            <th><span class='pull-right'>active</span></th>
        </tr>
    </thead>
    <tbody>
        <?php
        $i = 0;
        foreach ($users as $user) { ?>
        <tr class='action'>
            <td><?php echo $user['email']; if ($_SESSION['isLoggedEmail'] === $user['email']) echo' (admin)';?> </td>
            <td><?php echo date( 'd M Y', strtotime( $user['dt'])); ?></td>
            <td><?php echo date( 'd M Y &\nb\sp; H:i', strtotime( $user['last_dt'])); ?></td>
            <td><?php echo $user['last_ip']; ?></td>
            <td class='text-center'><span class='<?php if ($user['ip']) echo 'glyphicon glyphicon-ok text-success'; ?>'</span> </td>
            <td class='text-center'>
                <?php if ($user['isactive']) { ?>
                    <span class='glyphicon glyphicon-ok text-success'</span>
                <?php } else {?>
                    <span class='glyphicon glyphicon-remove text-danger'</span>
                <?php } ?>
            </td>
            <td class='activation text-center'>
            <?php if ($_SESSION['isLoggedEmail'] != $user['email']) {
                    if ($user['isactive']) { ?>
                        <a class='pull-left' href='<?php echo CONFIG_URL; ?>auth/manageUsers/0/<?php echo $user['id'];?>'><span class='glyphicon glyphicon-minus-sign text-danger'</span></a>
                    <?php } else { ?>
                    <a class='pull-left' href='<?php echo CONFIG_URL; ?>auth/manageUsers/1/<?php echo $user['id'];?>'><span class='glyphicon glyphicon-plus-sign text-success'</span></a>
                <?php } } ?>
            </td>
        </tr>
        <?php $i = $i + 1;} ?>
    </tbody>
</table>
&nbsp;<br>
</div>
