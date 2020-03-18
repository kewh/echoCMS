<?php
/**
 * model class for edit
 *
 * @since 1.0.11
 * @author Keith Wheatley
 * @package echocms\edit
 */
namespace echocms;

class editModelInput extends editModel
{
    /**
     * Get posted tags
	   *
	   * @return array tags
	   */
	  function getPostedTags()
    {
        if (isset($_POST['tags'])) {
                $_SESSION['item']['tags'] = array_filter(explode(',',$_POST['tags']));
        }

        return($_SESSION['item']['tags']);
    }

	  /**
     * Gets posted item data - returns session data as updated by posted data
	   *
	   * @return array item
	   */
	  function getPostedItemData()
    {
        if ( isset( $_POST['topic']) )
            $_SESSION['item']['topic'] = $_POST['topic'];
        if ( isset( $_POST['subtopic']) )
            $_SESSION['item']['subtopic'] = $_POST['subtopic'];
        if ( isset( $_POST['heading']) )
            $_SESSION['item']['heading'] = htmlentities($_POST['heading']);
        if ( isset( $_POST['caption']) )
            $_SESSION['item']['caption'] = htmlentities($_POST['caption']);
        if ( isset( $_POST['dateDisplay']) && date( 'Y-m-d', strtotime($_SESSION['item']['date'])) != date( 'Y-m-d', strtotime($_POST['dateDisplay'])))
            $_SESSION['item']['date'] =   date( 'Y-m-d', strtotime($_POST['dateDisplay'])) . date( ' H:i:s'); // Timestamp to SQL format
        if ( isset( $_POST['text']) )
            $_SESSION['item']['text'] = htmlentities($_POST['text']);
        if (isset( $_POST['deleteDownload']) && $_POST['deleteDownload'] != '' ) {
            $_SESSION['item']['download_src'] = null;
            $_SESSION['item']['download_name'] = null;
            return($_SESSION['item']);
        }
        if ( !empty($_FILES['download']['error']) && $_FILES['download']['error'] == 0 )
            $this->reportError('cms/model/edit_input getPostedItemData File upload failed. Exceeded maximum size. File size is: '. getMaxFileSize() . '. Error code is: '. $_FILES['download'] ['error']);
        if ( !empty($_FILES['download']['name'])) {
            if ($_FILES['download'] ['error'] != 0) {
                $this->reportError('Posted File upload failed for filename: '. $_FILES['download']['name'] . ' Error code is: ' . $_FILES['download'] ['error']);
            }
            else {
                $tmp_name = $_FILES['download']['tmp_name'];
                $dest = CONFIG_DIR.'/content/downloads/' . $_FILES['download']['name'];
                while (file_exists($dest)) {
                    $dest  = substr_replace($dest, '.1', -4, 0);
                    $_FILES['download']['name'] = substr_replace($_FILES['download']['name'], '.1', -4, 0);
                }
                if ( !move_uploaded_file($tmp_name,  $dest))
                    $this->reportError('File upload failed for filename: ' . $dest);
                $_SESSION['item']['download_src'] = $_FILES['download']['name'];
            }
        }
        if (isset($_POST['downloadTitle']) && $_POST['downloadTitle'] != null) {
            $_SESSION['item']['download_name'] = htmlentities($_POST['downloadTitle']);
        }
        elseif (isset($_SESSION['item']['download_src'])
                    && $_SESSION['item']['download_src'] != null
                    && $_SESSION['item']['download_name'] == null) {
            $_SESSION['item']['download_name'] =  substr($_SESSION['item']['download_src'], 0, strpos($_SESSION['item']['download_src'], "."));
        }

        return($_SESSION['item']);
    }

	  /**
	   * Get images
	   *
     * Gets posted image data and incoming images, updates session data with posted data
	   * performs sequencing, deletes, cropping; and processes and stores the incoming images.
	   *
	   * @return array images
	   */
	  function getImages()
    {
        $_SESSION['scrollToImages'] = false;

				//  process incoming image
        if ( !empty($_FILES['postedImage']['error']) && $_FILES['postedImage'] ['error'] == 0     ) {
            $this->reportError('cms/model/edit_input.php. Failed image load. Maximum file size is: '. getMaxFileSize() . '. Posted error code is: '. $_FILES['postedImage'] ['error'] );
        }
        if ( !empty($_FILES['postedImage']['name']) && $_FILES['postedImage'] ['error'] != 0    ) {
            $this->reportError('cms/model/edit_input.php Posted image error. FILES PostedImage name is: ' . $_FILES['postedImage']['name'] . ' Posted error code is: ' . $_FILES['postedImage'] ['error'], 0);
        }
        if ( !empty($_FILES['postedImage']['name']) && $_FILES['postedImage'] ['error'] == 0   ) {
            $image_posted = $_FILES['postedImage']['tmp_name'];
            $newImage = $_FILES['postedImage']['name'];
            $newImage = str_replace(' ','-',$newImage);
            $newImage = preg_replace('/[^A-Za-z0-9\-]/', '', $newImage);
            $newImage = date( 'Y-m-d-H-i-s-') . $newImage;
            $newImage = $newImage . '.jpg'; //note: all files are stored as JPGs
            $this->processNewImage($image_posted, $newImage);
            $this->createThumbnail(end($_SESSION['item']['images']));
            $_SESSION['scrollToImages'] = true;
        }

        //  Process request to delete an image
        if (isset( $_POST['deleteImageId']) && $_POST['deleteImageId'] != null) {
            unset ($_SESSION['item']['images'] [$_POST['deleteImageId']]);
            $_SESSION['item']['images'] = array_values($_SESSION ['item']['images']);
        }

        //  Sort images
        if (  isset( $_POST['imageSorted']) && $_POST['imageSorted'] == 'true' ){
            foreach ( $_SESSION ['item']['images'] as $i => $itemImages) {
                $_SESSION ['item']['images'][$i]['seq'] = $_POST['imgSeq'.$i];
            }
            usort($_SESSION ['item']['images'], function($a, $b) {
                return $a['seq'] - $b['seq'];
            });
        }

        // Check id of image to see if we have input from controller/image.php
				if ($_SESSION['cropId'] == null) {
						return($_SESSION['item']['images']);
				}
				$cropId = $_SESSION['cropId'];

        // Process incoming alt
				if ( isset( $_POST['alt']) )
						$_SESSION['item']['images'] [$cropId]['alt'] = $_POST['alt'];

        // Process incoming prime_aspect_ratio setting
				if ( isset( $_POST['prime_aspect_ratio']) )
						$_SESSION['item']['images'] [$cropId]['prime_aspect_ratio'] = $_POST['prime_aspect_ratio'];

        //  Process incoming cropping coordinates
        if ( isset ($_POST['mx1'] ) && $_POST['mx1'] != null ) {
            $_SESSION ['item']['images'][$cropId]['web_images'] = true;
            $cropCoordsInput = true;
            $_SESSION ['item']['images'] [$cropId]['mx1'] = $_POST['mx1'];
            $_SESSION ['item']['images'] [$cropId]['mx2'] = $_POST['mx2'];
            $_SESSION ['item']['images'] [$cropId]['my1'] = $_POST['my1'];
            $_SESSION ['item']['images'] [$cropId]['my2'] = $_POST['my2'];
            $_SESSION ['item']['images'] [$cropId]['lx1'] = $_POST['lx1'];
            $_SESSION ['item']['images'] [$cropId]['lx2'] = $_POST['lx2'];
            $_SESSION ['item']['images'] [$cropId]['ly1'] = $_POST['ly1'];
            $_SESSION ['item']['images'] [$cropId]['ly2'] = $_POST['ly2'];
            $_SESSION ['item']['images'] [$cropId]['px1'] = $_POST['px1'];
            $_SESSION ['item']['images'] [$cropId]['px2'] = $_POST['px2'];
            $_SESSION ['item']['images'] [$cropId]['py1'] = $_POST['py1'];
            $_SESSION ['item']['images'] [$cropId]['py2'] = $_POST['py2'];
            $_SESSION ['item']['images'] [$cropId]['sx1'] = $_POST['sx1'];
            $_SESSION ['item']['images'] [$cropId]['sx2'] = $_POST['sx2'];
            $_SESSION ['item']['images'] [$cropId]['sy1'] = $_POST['sy1'];
            $_SESSION ['item']['images'] [$cropId]['sy2'] = $_POST['sy2'];
						$_SESSION ['item']['images'] [$cropId]['fx1'] = $_POST['fx1'];
            $_SESSION ['item']['images'] [$cropId]['fx2'] = $_POST['fx2'];
            $_SESSION ['item']['images'] [$cropId]['fy1'] = $_POST['fy1'];
            $_SESSION ['item']['images'] [$cropId]['fy2'] = $_POST['fy2'];

						// set up height and width for the fluid image
            $image =& $_SESSION ['item']['images'] [$cropId];
						if (($image['fx2'] - $image['fx1']) > ($image['fy2'] - $image['fy1'])) {
								$longestSide = $image['width_fluid'] = $this->config['image_maxside_fluid'];
								$image['height_fluid'] = floor((($image['fy2'] - $image['fy1']) / ($image['fx2'] - $image['fx1']) ) * $longestSide);
						}
						else{
								$longestSide = $image['height_fluid'] = $this->config['image_maxside_fluid'];
								$image['width_fluid'] = floor((($image['fx2'] - $image['fx1']) / ($image['fy2'] - $image['fy1']) ) * $longestSide);
						}

            $croppedImage = $_SESSION ['item']['images'] [$cropId];
            $this->createThumbnail($croppedImage);
            $_SESSION['cropId'] = null;
            $_SESSION['scrollToImages'] = true;
        }

        return($_SESSION['item']['images']);
    }

	  /**
     * Process new image
	   *
     * Process new image, add default cropping coordinates to session data and store original and resized image
	   *
     * @param string $image_posted temporary system filename of the uploaded file
     * @param string $newImage name of image
     *
     * @return void
	   */
	  function processNewImage($image_posted, $newImage)
    {
        ini_set('memory_limit', '1024M');
        list($postedWidth, $postedHeight, $type) = getimagesize($image_posted);
        if ( $type == IMAGETYPE_JPEG )
            $image_src = imagecreatefromjpeg($image_posted);
        else if ( $type == IMAGETYPE_PNG )
            $image_src = imagecreatefrompng($image_posted);
        else if ( $type == IMAGETYPE_GIF )
            $image_src = imagecreatefromgif($image_posted);
        if ( !$image_src)
            $this->reportError('cms/model/edit_input processNewImage. Problem opening source image: '. $image_posted);

        // STORE ORIGINAL IMAGE, as a jpg
        set_time_limit(120); // resets default time limit back to zero

        if ($this->config['image_resize_original']) {
						if ($postedWidth >= $postedHeight){
						    $postedWidthResized = $this->config['image_maxside_original'];
			          $postedHeightResized = floor (($this->config['image_maxside_original'] / $postedWidth) * $postedHeight);
						}
						else {
						    $postedHeightResized = $this->config['image_maxside_original'];
			          $postedWidthResized = floor (($this->config['image_maxside_original'] / $postedHeight) * $postedWidth);
						}
				}
				else {
					  $postedHeightResized = $postedHeight;
					  $postedWidthResized = $postedWidth;
				}

	      $image_dst = imagecreatetruecolor($postedWidthResized, $postedHeightResized)
	          or $this->reportError('cms/model/edit_input processNewImage Store Original. Problem with imagecreatetruecolor');

	      if ($type == IMAGETYPE_PNG) {
	          $color = imagecolorallocatealpha($image_dst, 255, 255, 255, 127)
	              or $this->reportError('cms/model/edit_input.php processNewImage. Store Original. Problem with imagecolorallocatealpha');
	          imagefill($image_dst, 0, 0, $color)
	              or $this->reportError('cms/model/edit_input.php processNewImage. Store Original. Problem with imagefill');
	      }
        imagecopyresampled($image_dst, $image_src, 0, 0, 0, 0, $postedWidthResized, $postedHeightResized, $postedWidth, $postedHeight)
            or $this->reportError('cms/model/edit_input processNewImage Store Original. Problem with imagecopyresampled');
        imagejpeg($image_dst, CONFIG_DIR.'/content/images/original/'.$newImage , $this->config['image_quality_original'])
            or $this->reportError('cms/model/edit_input processNewImage Store Original. Problem with imagejpeg');
        imagedestroy($image_dst);

        // STORE RESIZED image with same aspect ratio as original (used for on screen cropping)
        set_time_limit(120);

        // set longest side to 600 and keep aspect ratio
        if ($postedWidthResized >= $postedHeightResized) {
            $uncroppedWidth = 600;
            $uncroppedHeight = floor ((600/ $postedWidthResized) * $postedHeightResized);
        }
        else{
            $uncroppedHeight = 600;
            $uncroppedWidth = floor ((600/ $postedHeightResized) * $postedWidthResized);
        }

        $image_from_src = imagecreatefromjpeg(CONFIG_DIR.'/content/images/original/'.$newImage)
            or $this->reportError('cms/model/edit_input processNewImage. Store Resized. Problem with imagecreatefromjpeg');

        $image_dst = imagecreatetruecolor($uncroppedWidth, $uncroppedHeight)
            or $this->reportError('cms/model/edit_input processNewImage Store Resized. Problem with imagecreatetruecolor');

        imagecopyresampled($image_dst, $image_from_src, 0, 0, 0, 0, $uncroppedWidth, $uncroppedHeight, $postedWidthResized, $postedHeightResized)
            or $this->reportError('cms/model/edit_input processNewImage Store Resized. Problem with imagecopyresampled');

        imagejpeg($image_dst, CONFIG_DIR.'/content/images/uncropped/'.$newImage , 80)
            or $this->reportError('cms/model/edit_input processNewImage Store Resized. Problem with imagejpeg');

        imagedestroy($image_dst);

        // calculate default cropping coordinates for a new image
        $srcRatio = $uncroppedWidth / $uncroppedHeight;
        $image_ratio_panorama = $this->convertConfigRatio($this->config['image_ratio_panorama']);
        $image_ratio_landscape = $this->convertConfigRatio($this->config['image_ratio_landscape']);
        $image_ratio_portrait = $this->convertConfigRatio($this->config['image_ratio_portrait']);
        $image_ratio_square = $this->convertConfigRatio($this->config['image_ratio_square']);

        // coordinates for Panorama image
        if ( $srcRatio > $image_ratio_panorama) {
            $w = floor ( $uncroppedHeight * $image_ratio_panorama);
            $m = floor (( $uncroppedWidth - $w) / 2) ;
            $mx1 =   $m;
            $mx2 =   $m + $w;
            $my1 =   0;
            $my2 =   $uncroppedHeight;
        }
        else {
            $h = floor ( $uncroppedWidth / $image_ratio_panorama );
            $m = floor ( ($uncroppedHeight - $h) / 2);
            $mx1 = 0;
            $mx2 = $uncroppedWidth;
            $my1 = $m;
            $my2 = $m + $h;
        }
        // coordinates for landscape image
        if ( $srcRatio > $image_ratio_landscape) {
            $w = floor ( $uncroppedHeight * $image_ratio_landscape);
            $m = floor (( $uncroppedWidth - $w) / 2) ;
            $lx1 =   $m;
            $lx2 =   $m + $w;
            $ly1 =   0;
            $ly2 =   $uncroppedHeight;
        }
        else {
            $h = floor ( $uncroppedWidth / $image_ratio_landscape);
            $m = floor (( $uncroppedHeight - $h) / 2);
            $lx1 = 0;
            $lx2 = $uncroppedWidth;
            $ly1 = $m;
            $ly2 = $m + $h;
        }

        // coordinates for portrait image
        if ($srcRatio > $image_ratio_portrait) {
            $w = floor ( $uncroppedHeight * $image_ratio_portrait);
            $m = floor (( $uncroppedWidth - $w) / 2) ;
            $px1 =   $m;
            $px2 =   $m + $w;
            $py1 =   0;
            $py2 =   $uncroppedHeight;
        }
        else {
            $h = floor ( $uncroppedWidth / $image_ratio_portrait);
            $m = floor (( $uncroppedHeight - $h) / 2);
            $px1 = 0;
            $px2 = $uncroppedWidth;
            $py1 = $m;
            $py2 = $m + $h;
        }

        // coordinates for square image
        if ($srcRatio > $image_ratio_square) {
            $w = floor ( $uncroppedHeight * $image_ratio_square);
            $m = floor (( $uncroppedWidth - $w) / 2) ;
            $sx1 =   $m;
            $sx2 =   $m + $w;
            $sy1 =   0;
            $sy2 =   $uncroppedHeight;
        }
        else {
            $h = floor ( $uncroppedWidth / $image_ratio_square);
            $m = floor (( $uncroppedHeight - $h) / 2);
            $sx1 = 0;
            $sx2 = $uncroppedWidth;
            $sy1 = $m;
            $sy2 = $m + $h;
        }

        // coordinates for fluid image
				$fx1 = 0;
				$fx2 = $uncroppedWidth;
				$fy1 = 0;
				$fy2 = $uncroppedHeight;

			// set width and height for fluid image, using original aspect ratio scaled to config maxside fluid.
	 		 if ($postedWidth >= $postedHeight) {
	 				 $fluidWidth = $this->config['image_maxside_fluid'];
	 				 $fluidHeight = floor (($this->config['image_maxside_fluid']/ $postedWidth) * $postedHeight);
	 		 }
	 		 else{
				  $fluidHeight = $this->config['image_maxside_fluid'];
				  $fluidWidth = floor (($this->config['image_maxside_fluid']/ $postedHeight) * $postedWidth);
	 		 }

        // add new image to session data
        $imageSeq = count($_SESSION['item']['images']);
        $image = array(
		        'src' => $newImage,
		        'seq' => $imageSeq,
		        'mx1' => $mx1, 'mx2' => $mx2, 'my1' => $my1, 'my2' => $my2,
		        'lx1' => $lx1, 'lx2' => $lx2, 'ly1' => $ly1, 'ly2' => $ly2,
		        'px1' => $px1, 'px2' => $px2, 'py1' => $py1, 'py2' => $py2,
		        'sx1' => $sx1, 'sx2' => $sx2, 'sy1' => $sy1, 'sy2' => $sy2,
		        'fx1' => $fx1, 'fx2' => $fx2, 'fy1' => $fy1, 'fy2' => $fy2,
		        'height' => $uncroppedHeight, 'width' => $uncroppedWidth,
		        'height_fluid' => $fluidHeight, 'width_fluid' => $fluidWidth,
		        'alt' => null, 'web_images' => true, 'prime_aspect_ratio' => 'landscape'
        );
        $_SESSION['item']['images'][] = $image;
    }

	  /**
     * Create thumbnail image
     *
     * Creates thumbnail when a new image is input and recreates whenever cropping is changed,
     * uses square cropping, uses uncropped file and not original, to reduce processing time.
	   *
     * @param array $image
     *
     * @return void
	   */
	  function createThumbnail($image)
    {
        $width = $height = 200;
        ini_set('memory_limit', '1024M');
        set_time_limit(120);
        $image_src = imagecreatefromjpeg(CONFIG_DIR.'/content/images/uncropped/'.$image['src'])
            or $this->reportError('cms/model/edit_input createThumbnail. Problem with imagecreatefromjpeg');
        $image_dst = imagecreatetruecolor($width, $height)
            or $this->reportError('cms/model/edit_input createThumbnail. Problem with imagecreatetruecolor');
        imagecopyresampled ($image_dst, $image_src, 0, 0, $image['sx1'], $image['sy1'], $width, $height,($image['sx2'] - $image['sx1']), ($image['sy2'] - $image['sy1']))
            or $this->reportError('cms/model/edit_input createThumbnail. Problem with imagecopyresampled');
        imagejpeg($image_dst, CONFIG_DIR.'/content/images/thumbnail/'.$image['src'] , 90)
            or $this->reportError('cms/model/edit_input createThumbnail. Problem with imagejpeg');
        imagedestroy($image_dst);
    }

	  /**
	   *
	   * Set up new item.
	   *
     * Sets up new item with default values.
	   *
	   * @return array $item
	   */
	  function setupNewItem()
    {
        $item = array(
            'id'            => null,
            'pending_id'    => null,
            'content_id'    => null,
            'created'       => date( 'Y-m-d H:i:s'),
            'createdBy'     => $_SESSION['isLoggedUID'],
            'updated'       => null,
            'updatedBy'     => null,
            'status'        => 'create',
            'topic'          => null,
            'subtopic'       => null,
            'heading'       => null,
            'date'          => date( 'Y-m-d H:i:s'),
            'caption'       => null,
            'text'          => null,
            'download_src'  => null,
            'download_name' => null,
            'images'        => array(),
            'tags'          => array(),
            'terms'         => array());
        $_SESSION['cropId'] = null;

        return($item);
    }

    /**
     * Set up existing item
     *
     * Initialises session with values from database for an existing content or pending item
     *
     * @param integer $itemId item id
     * @param string $status of item
     *
     * @return array $item
     */
	  function setupExistingItem($itemId, $status)
    {
        $itemId = intval($itemId);
        $_SESSION['cropId'] = null;
        if ($status == 'draft' || $status == 'update' || $status == 'offline') {
            $item           = $this->getPendingDetails($itemId);
            $item['tags']   = $this->getPendingTags($itemId);
            $item['images'] = $this->getPendingImages($itemId);
        }
        elseif ($status == 'live') {
            $item           = $this->getContentDetails($itemId);
            $item['tags']   = $this->getContentTags($itemId);
            $item['images'] = $this->getContentImages($itemId);
            $item['content_id'] = null;
        }
        else $this->reportError('model/edit_input setupExistingItem: unexpected status: '.$status.' for itemId: '.$itemId);

        return($item);
    }

	  /**
     * Get the maximum size for a POSTED file allowed by the server
	   *
     * @return integer $postMaxSize
	   */
	  function getMaxFileSize()
    {
        $val = trim(ini_get('post_max_size'));
        $last = strtolower($val[strlen($val)-1]);
        $val = intval($val);
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
}
