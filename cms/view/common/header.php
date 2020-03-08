<?php
/**
 * view for common/header
 *
 * @since 1.0.10
 * @author Keith Wheatley
 * @package echocms
 */
?>
<!DOCTYPE html>
<html lang='en'>
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>website cms</title>
    <meta name='description' content=''>
    <meta name='robots' content='noindex'>
    <link rel='stylesheet' href='https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css' integrity='sha384-BVYiiSIFeK1dGmJRAkycuHAHRg32OmUcww7on3RYdg4Va+PmSTsz/K68vbdEjh4u' crossorigin='anonymous'>
    <link rel='stylesheet' href='https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap-theme.min.css' integrity='sha384-rHyoN1iRsVXV4nD0JutlnGaslCJuC7uwjduW9SVrLvRYooPp2bWYgmgJQIXwl/Sp' crossorigin='anonymous'>
    <link rel='stylesheet' href='https://code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css'>
    <link rel='stylesheet' href='https://cdn.jsdelivr.net/gh/tapmodo/Jcrop@0.9.12/css/jquery.Jcrop.min.css'>
    <link rel='stylesheet' href='<?php echo CONFIG_URL; ?>assets/css/selectize.bootstrap3.css'>
    <link rel='stylesheet' href='<?php echo CONFIG_URL; ?>assets/css/pick-a-color-1.2.3.min.css'>
    <link rel='stylesheet' href='<?php echo CONFIG_URL; ?>assets/css/echocms.css'>
    <!-- jQuery needed here because various page specific scripts are loaded before footer -->
    <script src='https://code.jquery.com/jquery-1.11.2.min.js'></script>
</head>
<body>
<!-- MODALS  ****************************************************** -->

<div class='modalAction modal fade'>
  <div class='modal-dialog modal-md'>
    <div class='modal-content <?php if (!empty($result['error'])) echo 'alert-danger';?>'>
        <div class='modal-body text-center alert'>
            <br><h4 class='text-center modal-text'>
                <?php if (!empty($result['message'])) echo $result['message'];?>
            </h4>
            <div class='glyphicon glyphicon-refresh spinner'></div>
        </div>
    </div>
  </div>
</div>

<div class='modalInfo modal fade'>
  <div class='modal-dialog modal-md'>
    <div class='modal-content'>
        <div class='modal-header'>
            <button type='button' class='close' data-dismiss='modal'><span class='modal-dismiss-size'>&times;</span></button>
        </div>
        <div class='modal-body text-center alert'>
            <h4 class='text-center modal-text'>
                <?php if (!empty($result['message'])) echo $result['message'];?>
            </h4><br>
        </div>
    </div>
  </div>
</div>

<!-- HEADER  ****************************************************** -->
<div class='container-fluid'>
    <div class='row'>
        <header class='header'>
            <div class='col-xs-12 header-logo'>
                <?php if ($this->config['cms_page_logo'] !== '') { ?>
                <a href='<?php echo CONFIG_URL; ?>' class='pull-left'>
                    <img src='<?php echo CONFIG_URL;?>assets/images/<?php echo $this->config['cms_page_logo']; ?>'
                         alt='<?php echo $this->config['site_name']; ?>'>
                </a>
                <?php } ?>
                <div class='header-title'>
                    <a  href='<?php echo CONFIG_URL; ?>'><?php echo $this->config['site_name']; ?></a>
                </div>
            </div>
          <div class='col-xs-12'>
          <?php if ( isset($_SESSION['isLogged']) && $_SESSION['isLogged'] ) { ?>
              <ul class='nav nav-tabs'>
                    <li role='presentation' class='<?php if ($menu == 'create') echo 'active' ?>'><a href='<?php echo CONFIG_URL; ?>edit/input/create'><span class='glyphicon glyphicon-open-file'</span> create<span class='hidden-xs'> item</span></a></li>
                    <li role='presentation' class='<?php if ($menu == 'update') echo 'active' ?>'><a href='<?php echo CONFIG_URL; ?>edit/items/'><span class='glyphicon glyphicon-edit'</span> edit<span class='hidden-xs'> item</span></a></li>
                    <li role='presentation' class='<?php if ($menu == 'offline') echo 'active' ?>'><a href='<?php echo CONFIG_URL; ?>edit/items/offline'><span class='glyphicon glyphicon-list-alt'</span> offline<span class='hidden-xs'> items</span></a></li>
                    <li role='presentation' class='dropdown pull-right <?php if ($menu == 'user') echo 'active';?>'>
                        <a class='dropdown-toggle' data-toggle='dropdown' href='#' role='button' aria-haspopup='true' aria-expanded='false'>
                          <span class='glyphicon glyphicon-user' ></span><span class='hidden-xs'><small> <?php echo $_SESSION['isLoggedEmail'];?></small></span><span class='caret'></span>
                        </a>
                        <ul class='dropdown-menu'>
                            <li> <a href='<?php echo CONFIG_URL; ?>auth/changeEmail'>change your email</a></li>
                            <li> <a href='<?php echo CONFIG_URL; ?>auth/changePassword'>change password</a></li>
                            <li> <a href='<?php echo CONFIG_URL; ?>auth/logout'>logout</a></li>
                        </ul>
                    </li>

                 <?php if ($_SESSION['isLoggedAdmin']){ ?>
                    <li role='presentation' class='dropdown pull-right <?php if ($menu == 'admin') echo 'active';?>'>
                        <a class='dropdown-toggle' data-toggle='dropdown' href='#' role='button' aria-haspopup='true' aria-expanded='false'>
                          <span class='glyphicon glyphicon-cog'></span><span class='hidden-xs'><small> admin </small></span><span class='caret'></span>
                        </a>
                        <ul class='dropdown-menu'>
                            <li role='presentation'><a href='<?php echo CONFIG_URL; ?>config/editConfig'>configure</a></li>
                            <li role='presentation'><a href='<?php echo CONFIG_URL; ?>auth/manageUsers'>users</a></li>
                            <li role='presentation'><a href='<?php echo CONFIG_URL; ?>auth/manageAccess'>access log</a></li>
                            <li role='presentation'><a href='<?php echo CONFIG_URL; ?>admin/recreateImages'>recreate images</a></li>
                            <li role='presentation'><a href='<?php echo CONFIG_URL; ?>admin/manageBackups'>backup images</a></li>
                            <li role='presentation'><a href='<?php echo CONFIG_URL; ?>admin/bulkLoadImages'>bulk load images</a></li>

                        </ul>
                    </li>
                <?php  } ?>
                </ul>
          <?php } else { ?>
                <ul class='nav nav-tabs'>
                    <li role='presentation' class='pull-right <?php if ($menu == 'user') echo 'active' ?>'><a href='<?php echo CONFIG_URL; ?>auth/index'><span class='glyphicon glyphicon-user' </span> login</a></li>
                    <li role='presentation' class='pull-right <?php if ($menu == 'register') echo 'active' ?>'><a href='<?php echo CONFIG_URL; ?>auth/registerUser'><span class='glyphicon glyphicon-edit' </span> register</a></li>
                </ul>
          <?php } ?>
            </div>
        </header>
