<?php
/**
 * echocms - check setup script
 *
 * @since 1.0.0
 * @author Keith Wheatley
 * @package echocms
 */
        $error = null;
        if(!in_array('mod_rewrite', apache_get_modules())) {
            $error .= 'Apache server mod_rewrite is not available.<br>';
        }
        if (version_compare(phpversion(), '5.5.0', '<')) {
            $error .= 'PHP 5.5.0 or above is required.<br>';
        }
        if (!extension_loaded('gd') || !function_exists('gd_info')) {
            $error .=  'PHP GD library is not installed.<br>';
        }
        if (!extension_loaded('mysqli') || !function_exists('mysqli_info')) {
            $error .= 'mysqli is not installed.<br>';
        }
        if (!extension_loaded('PDO')) {
            $error .= 'PDO is not installed.<br>';
        }
        if (!extension_loaded('zip')) {
            $error .= 'ZipArchive is not installed, download of backups will not be available.<br>';
        }
?>
<!DOCTYPE html>
<html>
<head>
    <title>check server configuration for echocms</title>
    <meta name='robots' content='noindex'/>
    <style type="text/css">
    html {
        height:100%;
    }
    body {
        height:100%;
        margin:0;
        font-family: 'Helvetica Rounded', Arial, sans-serif;
        color:#404040; /* Black Text 1, lighter 25% */
        background-color:#999999;
        font-size:18px;

    }
    h4 {
        text-align: center;
    }
    #topSpacer {
        width:1px;
        height:50%;
        margin-bottom: -150px; /* negative half of height of div to be centered */
    }
    #container {
        width: 600px;
        height: 260px;    /*total height including padding is 300px */
        padding: 20px;
        margin-left:auto;
        margin-right:auto;
        background-color:#ffffff;
    }
    img {
        float: right;
    }
    .text {
        padding-left: 100px;
    }
    </style>
</head>
<body>
    <div id="topSpacer">
    </div>
    <div id="container">

    <?php if ($error) { ?>
        <h4>Your server configuration is not OK for echoCMS because:</h4>
        <div class="text">
            <?php echo ($error); ?>
        </div>
    <?php } else { ?>
        <h4>Your server configuration looks OK for echoCMS</h4>
    <?php } ?>

    </div>
</html>
