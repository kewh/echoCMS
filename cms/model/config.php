<?php
/**
 * Model class for system configuration
 *
 * @since 1.0.0
 * @author Keith Wheatley
 * @package echocms\config
 */
namespace echocms;

class configModel
{
    /**
     * Setup db connection.
     *
     * Sets dbh object for connection to database
     */
    function setupDbh()
    {
        require $_SERVER['DOCUMENT_ROOT'] . '/cms/config/db.php';
        $this->dbh = new \PDO('mysql:host=' .$db_host. ';dbname=' .$db_name. ';charset=utf8mb4', $db_user, $db_pass, array(\PDO::ATTR_ERRMODE => \PDO::ERRMODE_EXCEPTION));

        return($this->dbh);
    }

    /**
     * Reads all CMS config items.
     *
     * @return array $config {
     *      setting string containing name of setting
     *      value string containing value of configuration setting
     * }
     */
    function readConfig()
    {
        // get config items from config table
        $stmt = $this->dbh->prepare('SELECT setting, value FROM config');
        $stmt->execute();
        $config = array();
        while ($row = $stmt->fetch(\PDO::FETCH_ASSOC)) {
            $config[$row['setting']] = $row['value'];
        }
        date_default_timezone_set($config['site_timezone']);

        // get page config items from pages table, add as 2nd dim array
        $config['pages'] = array();
		    $stmt = $this->dbh->prepare('SELECT page FROM pagesTable');
	      $stmt->execute();
        while ($row = $stmt->fetch(\PDO::FETCH_ASSOC)) {
            $config['pages'][] = $row['page'];
        }

        // get element config items from elements table, add as 2nd dim array
        $config['elements'] = array();
		    $stmt = $this->dbh->prepare('SELECT element FROM elementsTable');
	      $stmt->execute();
        while ($row = $stmt->fetch(\PDO::FETCH_ASSOC)) {
            $config['elements'][] = $row['element'];
        }

        //error_log ('cms/model/config.php readConfig. $config: ' . print_r($config, true) );
        return ($config);
    }

    /**
     * Gets posted config items.
     *
     * Gets posted updates to configuration items from form in view/config/editConfig.php.
     *
     * @return array $config {
     *      setting string containing name of setting
     *      value string containing value of configuration setting
     * }
     */
    function getPostedConfig()
    {
        // CMS Configuration items
        if ( !empty($_FILES['postedImage']['error']) && $_FILES['postedImage'] ['error'] == 0 )
            $this->reportError('cms/model/config.php. Failed image load. Maximum file size is: '. $this->getMaxFileSize() . '. Posted error code is: '. $_FILES['postedImage'] ['error'] );
        if ( !empty($_FILES['postedImage']['name']) && $_FILES['postedImage'] ['error'] != 0 )
            $this->reportError('cms/model/config.php Posted image error. FILES PostedImage name is: ' . $_FILES['postedImage']['name'] . ' Posted error code is: ' . $_FILES['postedImage'] ['error'], 0);
        if ( !empty($_FILES['postedImage']['name']) && $_FILES['postedImage'] ['error'] == 0 ) {
            $image_posted = $_FILES['postedImage']['tmp_name'];
            $newImage = $_FILES['postedImage']['name'] .'.png';
            $this->createLogoImage($image_posted, $newImage);
            $config['cms_page_logo'] = $newImage;
        }
        if ( isset( $_POST['site_name']) )
            $config['site_name'] = $_POST['site_name'];
        if ( isset( $_POST['date_format']) )
            $config['date_format'] = $_POST['date_format'];
        if ( isset( $_POST['site_timezone']) )
            $config['site_timezone'] = $_POST['site_timezone'];

        $config['elements_updatable'] = empty($_POST['elements_updatable']) ? '0' : '1';
        $config['pages_updatable'] = empty($_POST['pages_updatable']) ? '0' : '1';

        // Authentication items
        if ( isset( $_POST['ip_ban_minutes']) )
            $config['ip_ban_minutes'] = $_POST['ip_ban_minutes'];
        if ( isset( $_POST['ip_ban_attempts']) )
            $config['ip_ban_attempts'] = $_POST['ip_ban_attempts'];
        if ( isset( $_POST['remember_me_days']) )
            $config['remember_me_days'] = $_POST['remember_me_days'];
        if ( isset( $_POST['bcrypt_cost']) )
            $config['bcrypt_cost'] = $_POST['bcrypt_cost'];
        if ( isset( $_POST['password_reset_minutes']) )
            $config['password_reset_minutes'] = $_POST['password_reset_minutes'];

        // Image items
        $config['image_sizes_landscape'] = empty($_POST['image_sizes_landscape']) ? '0' : '1';
        $config['image_sizes_portrait'] = empty($_POST['image_sizes_portrait']) ? '0' : '1';
        $config['image_sizes_panorama'] = empty($_POST['image_sizes_panorama']) ? '0' : '1';
        $config['image_sizes_square'] = empty($_POST['image_sizes_square']) ? '0' : '1';

        $config['image_create_landscape'] = empty($_POST['image_create_landscape']) ? '0' : '1';
        $config['image_create_portrait'] = empty($_POST['image_create_portrait']) ? '0' : '1';
        $config['image_create_panorama'] = empty($_POST['image_create_panorama']) ? '0' : '1';
        $config['image_create_square'] = empty($_POST['image_create_square']) ? '0' : '1';

        if ( isset( $_POST['image_ratio_landscape']) )
            $config['image_ratio_landscape'] = $_POST['image_ratio_landscape'];
        if ( isset( $_POST['image_ratio_portrait']) )
            $config['image_ratio_portrait'] = $_POST['image_ratio_portrait'];
        if ( isset( $_POST['image_ratio_panorama']) )
            $config['image_ratio_panorama'] = $_POST['image_ratio_panorama'];
        if ( isset( $_POST['image_ratio_square']) )
            $config['image_ratio_square'] = $_POST['image_ratio_square'];

        if ( isset( $_POST['image_width_landscape']) )
            $config['image_width_landscape'] = $_POST['image_width_landscape'];
        if ( isset( $_POST['image_width_portrait']) )
            $config['image_width_portrait'] = $_POST['image_width_portrait'];
        if ( isset( $_POST['image_width_panorama']) )
            $config['image_width_panorama'] = $_POST['image_width_panorama'];
        if ( isset( $_POST['image_width_square']) )
            $config['image_width_square'] = $_POST['image_width_square'];

        if ( isset( $_POST['image_quality']) )
            $config['image_quality'] = $_POST['image_quality'];
        if ( isset( $_POST['image_bg_crop']) )
            $config['image_bg_crop'] = $_POST['image_bg_crop'];

        // Email settings
        $config['email_notifications_on'] = empty($_POST['email_notifications_on']) ? '0' : '1';
        $config['smtp_auth'] = empty($_POST['smtp_auth']) ? '0' : '1';
        $config['smtp'] = empty($_POST['smtp']) ? '0' : '1';
        if ( isset( $_POST['site_email']) )
            $config['site_email'] = $_POST['site_email'];
        if ( isset( $_POST['smtp_host']) )
            $config['smtp_host'] = $_POST['smtp_host'];
        if ( isset( $_POST['smtp_password']) )
            $config['smtp_password'] = $_POST['smtp_password'];
        if ( isset( $_POST['smtp_port']) )
            $config['smtp_port'] = $_POST['smtp_port'];
        if ( isset( $_POST['smtp_security']) )
            $config['smtp_security'] = $_POST['smtp_security'];
        if ( isset( $_POST['smtp_username']) )
            $config['smtp_username'] = $_POST['smtp_username'];

        return($config);
    }

    /**
     * Creates logo image.
     *
     * @param string $image_posted temporary system filename of the uploaded file
     * @param string $newImage name of image
     *
     * @return void
     */
    function createLogoImage($image_posted, $newImage)
    {
        ini_set('memory_limit', '1024M');
        set_time_limit(120);
        list($postedWidth, $postedHeight, $type) = getimagesize($image_posted);
        if ( $type == IMAGETYPE_JPEG )
            $image_src = imagecreatefromjpeg($image_posted);
        else if ( $type == IMAGETYPE_PNG )
            $image_src = imagecreatefrompng($image_posted);
        else if ( $type == IMAGETYPE_GIF )
            $image_src = imagecreatefromgif($image_posted);
        if (!$image_src)
            $this->reportError('cms/model/config.php createLogoImage. Problem opening source image: '. $image_posted);
        $uncroppedHeight = 200;
        $uncroppedWidth = floor ((200/ $postedHeight) * $postedWidth);

        if (!$image_dst = imagecreatetruecolor($uncroppedWidth, $uncroppedHeight))
            $this->reportError('cms/model/config.php createLogoImage. Problem with imagecreatetruecolor');
        if ($type == IMAGETYPE_PNG) {
            if (!$color = imagecolorallocatealpha($image_dst, 255, 255, 255, 127))
                $this->reportError('cms/model/config.php createLogoImage. Problem with imagecolorallocatealpha');

            if (!$ok = imagefill($image_dst, 0, 0, $color))
                $this->reportError('cms/model/config.php createLogoImage. Problem with imagefill');

            if (!$ok = imagesavealpha($image_dst, TRUE))
                $this->reportError('cms/model/config.php createLogoImage. Problem with imagesavealpha');
        }
        if (!imagecopyresampled($image_dst, $image_src, 0, 0, 0, 0, $uncroppedWidth, $uncroppedHeight, $postedWidth, $postedHeight))
            $this->reportError('cms/model/config.php createLogoImage. Problem with imagecopyresampled');

        if (!imagepng($image_dst, CONFIG_DIR.'/assets/images/'.$newImage, 0))
            $this->reportError('cms/model/config.php createLogoImage. Problem with imagepng');

        imagedestroy($image_dst);
    }

    /**
     * Update config items.
     *
     * Updates database with all updated config items
     *
     * @param array $configUpdates {
     *      setting string containing name of setting
     *      value string containing value of configuration setting
     * }
     *
     * @return void
     */
    function updateConfig($configUpdates)
    {
        foreach ($configUpdates as $setting => $value) {
              $stmt = $this->dbh->prepare('UPDATE config SET value = ? WHERE setting = ?');
              $stmt->execute(array($value, $setting));
        }
    }

    /**
     * Change admin email.
     *
     * Updates the database with new admin email.
     *
     * @param string $newEmail
     *
     * @return void
     */
    function changeAdminEmail($newEmail)
    {
        $query = $this->dbh->prepare('UPDATE config SET value = ? WHERE setting = "admin_email"');
        $query->execute(array($newEmail));
    }

    /**
     * Get the maximum size for a POSTED file allowed by the server.
     *
     * @return integer $postMaxSize
     */
    function getMaxFileSize()
    {
        $val = trim(ini_get('post_max_size'));
        $last = strtolower($val[strlen($val)-1]);
        switch($last) {
            case 'g':
                $val *= 1024;
            case 'm':
                $val *= 1024;
            case 'k':
             $val *= 1024;
              }
        $postMaxSize = $val;

        return($postMaxSize);
    }

    /**
     * Get posted pages
	 *
	 * @return array $pages
	 */
	  function getPostedPages()
    {
        $pages = array();
        if ( isset( $_POST['pages']) && $_POST['pages'] != null) {
            $pages = $_POST['pages'];
        }

        return($pages);
    }

    /**
     * Update config pages.
     *
     * @param array $pages
     */
    function updateConfigPages($pages)
    {
        $stmt = $this->dbh->prepare('TRUNCATE TABLE pagesTable');
        $stmt->execute();
        foreach($pages as $page) {
            $element = trim($page);
            $stmt = $this->dbh->prepare('INSERT INTO pagesTable (page) VALUES (?)');
            $array = array($page);
            $stmt->execute($array);
        }
    }

    /**
     * Get posted elements
	 *
	 * @return array elements
	 */
	  function getPostedElements()
    {
        $elements = array();
        if ( isset( $_POST['elements']) && $_POST['elements'] != null) {
            $elements = $_POST['elements'];
        }

        return($elements);
    }

    /**
     * Update config elements.
     *
     * @param array $elements
     */
    function updateConfigElements($elements)
    {
        $stmt = $this->dbh->prepare('TRUNCATE TABLE elementsTable');
        $stmt->execute();
        foreach($elements as $element) {
            $element = trim($element);
            $stmt = $this->dbh->prepare('INSERT INTO elementsTable (element) VALUES (?)');
            $array = array($element);
            $stmt->execute($array);
        }
    }

    /**
     * Report error.
     *
     * Reports a system error to error_log then sets header location to error notification page and exits script.
     *
     * @param string $message
     */
    function reportError($message)
    {
        error_log ('cms/model/config.php reportError. ' . print_r($message, true) );
        header('location: ' . CONFIG_URL. 'error/notify' );

        exit();
    }
}
