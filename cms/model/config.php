<?php
/**
 * Model class for system configuration
 *
 * @since 1.0.6
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
        require CONFIG_DIR . '/config/db.php';
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

        // get topics config items from topics table, add as 2nd dim array
        $config['topics'] = array();
		    $stmt = $this->dbh->prepare('SELECT topic FROM topicsTable');
	      $stmt->execute();
        while ($row = $stmt->fetch(\PDO::FETCH_ASSOC)) {
            $config['topics'][] = $row['topic'];
        }

        // get subtopic config items from subtopics table, add as 2 dim array
        $config['subtopics'] = array();
		    $stmt = $this->dbh->prepare('SELECT subtopic FROM subtopicsTable');
	      $stmt->execute();
        while ($row = $stmt->fetch(\PDO::FETCH_ASSOC)) {
            $config['subtopics'][] = $row['subtopic'];
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

        $config['subtopics_updatable'] = empty($_POST['subtopics_updatable']) ? '0' : '1';
        $config['topics_updatable'] = empty($_POST['topics_updatable']) ? '0' : '1';

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
        $config['image_create_collage'] = empty($_POST['image_create_collage']) ? '0' : '1';

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
        if ( isset( $_POST['image_width_collage']) )
            $config['image_width_collage'] = $_POST['image_width_collage'];

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
        $val = intval(trim(ini_get('post_max_size')));
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
     * Get posted topics
	 *
	 * @return array $topics
	 */
	  function getPostedTopics()
    {
        $topics = array();
        if ( isset( $_POST['topics']) && $_POST['topics'] != null) {
            $topics = $_POST['topics'];
        }

        return($topics);
    }

    /**
     * Update config topics.
     *
     * @param array $topics
     */
    function updateConfigTopics($topics)
    {
        $stmt = $this->dbh->prepare('TRUNCATE TABLE topicsTable');
        $stmt->execute();
        foreach($topics as $topic) {
            $topic = trim($topic);
            $stmt = $this->dbh->prepare('INSERT INTO topicsTable (topic) VALUES (?)');
            $array = array($topic);
            $stmt->execute($array);
        }
    }

    /**
     * Get posted subtopics
	 *
	 * @return array subtopics
	 */
	  function getPostedSubtopics()
    {
        $subtopics = array();
        if ( isset( $_POST['subtopics']) && $_POST['subtopics'] != null) {
            $subtopics = $_POST['subtopics'];
        }

        return($subtopics);
    }

    /**
     * Update config subtopics.
     *
     * @param array $subtopics
     */
    function updateConfigSubtopics($subtopics)
    {
        $stmt = $this->dbh->prepare('TRUNCATE TABLE subtopicsTable');
        $stmt->execute();
        foreach($subtopics as $subtopic) {
            $subtopic = trim($subtopic);
            $stmt = $this->dbh->prepare('INSERT INTO subtopicsTable (subtopic) VALUES (?)');
            $array = array($subtopic);
            $stmt->execute($array);
        }
    }

    /**
     *  Update all images with image-prime-ratio inconsistent with current config image_create_xxx settings
     *
     */
    function updateImagesPrimeRatio($config)
    {
        if (!$config['image_create_panorama'])  {
            $stmt = $this->dbh->prepare('UPDATE imagesTable SET prime_aspect_ratio = "" WHERE prime_aspect_ratio = "panorama"');
            $stmt->execute();
        }
        if (!$config['image_create_landscape'])  {
            $stmt = $this->dbh->prepare('UPDATE imagesTable SET prime_aspect_ratio = "" WHERE prime_aspect_ratio = "landscape"');
            $stmt->execute();
        }
        if (!$config['image_create_portrait'])  {
            $stmt = $this->dbh->prepare('UPDATE imagesTable SET prime_aspect_ratio = "" WHERE prime_aspect_ratio = "portrait"');
            $stmt->execute();
        }
        if (!$config['image_create_square'])  {
            $stmt = $this->dbh->prepare('UPDATE imagesTable SET prime_aspect_ratio = "" WHERE prime_aspect_ratio = "square"');
            $stmt->execute();
        }
    }

    /**
     * Report error.
     *
     * Reports a system error to error_log then sets header location to error notification topics and exits script.
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
