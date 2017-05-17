<?php
/**
 * model class for edit
 *
 * @since 1.0.2
 * @author Keith Wheatley
 * @package echocms\edit
 */
namespace echocms;

class editModelUpdate extends editModel
{
    /**
     *
     * Save item.
     *
     * Save a draft or updated item to pending table.
     *
     */
    function saveItem()
    {
        switch ($_SESSION['item']['status']) {
            case 'live':
             if ($_SESSION['item']['pending_id'] !='')
                    $this->reportError('model/edit_update.php saveItem logic error - update attempted for a live item with existing update pending. SESSION data: ' . print_r($_SESSION, true));
                $_SESSION['item']['status'] = 'update';
                $_SESSION['item']['content_id'] = $_SESSION['item']['id'];
                $this->createPendingItem();
                $this->updateContentPending($_SESSION['item']['id'], $_SESSION['item']['content_id']);
                break;
            case 'update':
                $this->updatePendingItem();
                break;
            case 'draft':
                $this->updatePendingItem();
                break;
            case 'offline':
                $_SESSION['item']['status'] = 'draft';
                $this->updatePendingItem();
                break;
            case 'create':
                $_SESSION['item']['status'] = 'draft';
                $this->createPendingItem();
                break;
            default;
                 $this->reportError('model/edit_update function: saveItem. Error: invalid item Status is: '.print_r($_SESSION['item']['status'],true));
        }
    }

    /**
     * Publish an item.
     *
     * Saves an item to content table.
     *
     */
    function publishItem()
    {
        switch ($_SESSION['item']['status']) {
            case 'live':
                $this->updateContentItem();
                break;
            case 'update':
                $this->deletePendingItem();
                $_SESSION['item']['id'] = $_SESSION['item']['content_id'];
                $_SESSION['item']['pending_id'] = null;
                $this->updateContentItem();
                break;
            case 'create':
                $this->createContentItem();
                break;
            case 'draft': case 'offline':
                $this->deletePendingItem();
                $this->createContentItem();
                break;
            default:
                $this->reportError('model/edit_update function: publishItem. Error: invalid item Status is: '.print_r($_SESSION['item']['status'],true));
        }
    }

    /**
     * Make an item offline
     *
     * Updates a pending item on database, to status of 'offline'.
     */
    function offlineItem()
    {
        switch ($_SESSION['item']['status']) {
            case 'live':
                $this->deleteContentItem();
                $_SESSION['item']['status'] = 'offline';
                $_SESSION['item']['content_id'] = null;
                $this->createPendingItem();
                break;
            case 'update':
                if ($_SESSION['item']['content_id']) {
                    $this->updateContentPending(null, $_SESSION['item']['content_id']);
                }
                $_SESSION['item']['status'] = 'offline';
                $_SESSION['item']['content_id'] = null;
                $this->updatePendingItem();
                break;
            case 'draft':
                $_SESSION['item']['status'] = 'offline';
                $this->updatePendingItem();
                break;
            case 'offline':
                $this->updatePendingItem();
                break;
            default:
                $this->reportError('model/edit_update function: offlineItem. Error: invalid item Status is: '.print_r($_SESSION['item']['status'],true));
        }
    }

    /**
     * Create a pending item.
     *
     * Adds a pending item to database, note there will be no pending or content record.
     *
     */
    private function createPendingItem()
    {
        $this->createPending ($_SESSION['item']);
        $this->createPendingTags($_SESSION['item']['id'], $_SESSION['item']['tags']);
        $this->createPendingTerms($_SESSION['item']['id'], $_SESSION['item']['heading']);
        $this->createPendingImages($_SESSION['item']['id'], $_SESSION['item']['images']);
    }

    /**
     * Creates a content item.
     *
     *
     * Adds a content item to database with a status of 'live'.
     */
     private function createContentItem()
    {
        $_SESSION['item']['status']     = 'live';
        $_SESSION['item']['pending_id'] = null;
        $_SESSION['item']['created']    = date( 'Y-m-d H:i:s');
        $this->createContent ($_SESSION['item']);
        $this->createContentTags ($_SESSION['item']['id'], $_SESSION['item']['tags']);
        $this->createContentTerms ($_SESSION['item']['id'], $_SESSION['item']['heading']);
        $this->createWebsiteImages ($_SESSION['item']['images']);
        $this->createContentImages ($_SESSION['item']['id'], $_SESSION['item']['images']);
    }

    /**
     * Update a content item.
     *
     * Updates a 'live' content item on database.
     */
    private function updateContentItem()
    {
        $_SESSION['item']['status']  = 'live';
        $_SESSION['item']['updated'] = date( 'Y-m-d H:i:s');
        $_SESSION['item']['updatedBy'] = $_SESSION['isLoggedUID'];
        $this->updateContent ($_SESSION['item']);
        $this->deleteContentTags ($_SESSION['item']['id']);
        $this->deleteContentTerms ($_SESSION['item']['id']);
        $this->deleteContentImages ($_SESSION['item']['id']);
        $this->createContentTags ($_SESSION['item']['id'], $_SESSION['item']['tags']);
        $this->createContentTerms ($_SESSION['item']['id'], $_SESSION['item']['heading']);
        $this->createWebsiteImages ($_SESSION['item']['images']);
        $this->createContentImages ($_SESSION['item']['id'], $_SESSION['item']['images']);
    }

    /**
     * Update a pending item.
     *
     * Updates a pending item on database.
     */
    private function updatePendingItem()
    {
        $_SESSION['item']['updated'] = date( 'Y-m-d H:i:s');
        $_SESSION['item']['updatedBy'] = $_SESSION['isLoggedUID'];
        $this->updatePending ($_SESSION['item']);
        $this->deletePendingTags($_SESSION['item']['id']);
        $this->deletePendingTerms($_SESSION['item']['id']);
        $this->deletePendingImages($_SESSION['item']['id']);
        $this->createPendingTags($_SESSION['item']['id'], $_SESSION['item']['tags']);
        $this->createPendingTerms($_SESSION['item']['id'], $_SESSION['item']['heading']);
        $this->createPendingImages($_SESSION['item']['id'], $_SESSION['item']['images']);
    }

    /**
     * Deletes a pending item from database.
     *
     */
    private function deletePendingItem()
    {
        $this->deletePending ($_SESSION['item']['id']);
        $this->deletePendingTags($_SESSION['item']['id']);
        $this->deletePendingTerms($_SESSION['item']['id']);
        $this->deletePendingImages($_SESSION['item']['id']);
    }

    /**
     * Deletes a content item from database.
     *
     */
    private function deleteContentItem()
    {
        $this->deleteContent ($_SESSION['item']['id']);
        $this->deleteContentTags ($_SESSION['item']['id']);
        $this->deleteContentTerms ($_SESSION['item']['id']);
        $this->deleteContentImages ($_SESSION['item']['id']);
    }

    /**
     * Creates a content item on database.
     *
     */
    private function createContent($row)
    {
        $stmt = $this->dbh->prepare('INSERT INTO itemsTable (
            pending_id,
            created,
            createdBy,
            updated,
            updatedBy,
            status,
            heading,
            topic,
            subtopic,
            caption,
            text,
            date,
            download_src,
            download_name
            )
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)');
        $array = array(
            $row['pending_id'],
            $row['created'],
            $row['createdBy'],
            $row['updated'],
            $row['updatedBy'],
            $row['status'],
            $row['heading'],
            $row['topic'],
            $row['subtopic'],
            $row['caption'],
            $row['text'],
            $row['date'],
            $row['download_src'],
            $row['download_name']);
        $stmt->execute($array);
        $inserted_id = $this->dbh->lastInsertId();
        $_SESSION['item']['id'] = $inserted_id;
    }

    /**
     * Updates a content item on database.
     *
     */
    private function updateContent($row)
    {
        $stmt = $this->dbh->prepare('UPDATE itemsTable SET
            pending_id = ?,
            created = ?,
            createdBy = ?,
            updated = ?,
            updatedBy = ?,
            status = ?,
            heading = ?,
            topic = ?,
            subtopic = ?,
            caption = ?,
            text = ?,
            date = ?,
            download_src = ?,
            download_name = ?
            WHERE
            id = ?');
        $array = array(
            $row['pending_id'],
            $row['created'],
            $row['createdBy'],
            $row['updated'],
            $row['updatedBy'],
            $row['status'],
            $row['heading'],
            $row['topic'],
            $row['subtopic'],
            $row['caption'],
            $row['text'],
            $row['date'],
            $row['download_src'],
            $row['download_name'],
            $row['id']  );
        $stmt->execute($array);
    }

    /**
     * Delete a content item from database.
     *
     */
    private function deleteContent($id) {
        $stmt = $this->dbh->prepare('DELETE FROM itemsTable WHERE id = ?');
        $stmt->execute(array($id));
    }

    /**
     * Delete a pending item from database.
     *
     */
    private function deletePending($id) {
        $stmt = $this->dbh->prepare('DELETE FROM pendingItemsTable WHERE id = ?');
        $stmt->execute(array($id));
    }

    /**
     * Create tags on database for a live content item.
     *
     */
    private function createContentTags($content_id, $tags)
    {
        foreach($tags as $tag)
        {
            $tag = trim($tag);
            if ($tag != null && $tag != null) {
                $stmt = $this->dbh->prepare('INSERT INTO tagsTable (content_id, tag) VALUES (?, ?)');
                $array = array($content_id, $tag );
                $stmt->execute($array);
            }
        }
    }

    /**
     * Delete tags from database for a live content item.
     *
     */
    private function deleteContentTags($content_id)
    {
        $stmt = $this->dbh->prepare('DELETE FROM tagsTable WHERE content_id = ?');
        $stmt->execute(array($content_id));
    }

    /**
     * Create search terms and add to database for a live content item.
     *
     */
    private function createContentTerms($content_id, $termStr)
    {
        $records = false;
        $termStr = str_replace('&amp;', '', $termStr);
        $termStr = preg_replace('/\band\b/', '', $termStr);
        $termStr = preg_replace('/\bbut\b/', '', $termStr);
        $termStr = preg_replace('/\bfor\b/', '', $termStr);
        $termStr = preg_replace('/\bthe\b/', '', $termStr);
        $termStr = preg_replace('/\bwith\b/', '', $termStr);
        $termStr = preg_replace('/\bAND\b/', '', $termStr);
        $termStr = preg_replace('/\bBUT\b/', '', $termStr);
        $termStr = preg_replace('/\bFOR\b/', '', $termStr);
        $termStr = preg_replace('/\bTHE\b/', '', $termStr);
        $termStr = preg_replace('/\bWITH\b/', '', $termStr);
        $termStr = preg_replace('/[^a-zA-Z0-9\Ş\ \@]+/', '', $termStr);
                   // remove all but aphanumerics, S cedilla, spaces, and @
        $terms = explode(' ', $termStr);
        foreach($terms as $term) {
            $term = trim($term, ' ,');
            if (strlen($term) >2) {
                $stmt = $this->dbh->prepare('INSERT INTO termsTable (content_id, term) VALUES (?, ?)');
                $array = array($content_id, $term );
                $stmt->execute($array);
            }
        }
    }

    /**
     * Delete terms from database for an existing live content item.
     *
     */
    private function deleteContentTerms($content_id)
    {
        $stmt = $this->dbh->prepare('DELETE FROM termsTable WHERE content_id = ?');
        $stmt->execute(array($content_id));
    }

    /**
     * Delete images data from database for a content item.
     *
     */
    private function deleteContentImages($content_id)
    {
        $stmt = $this->dbh->prepare('DELETE FROM imagesTable WHERE content_id = ?');
        $stmt->execute(array($content_id));
    }

    /**
     * Create images data on database for a content item.
     *
     */
    private function createContentImages($content_id, $images)
    {
        foreach ($images as $image) {
            $stmt = $this->dbh->prepare('INSERT INTO imagesTable (
                content_id,
                src, seq,
                mx1, mx2, my1, my2,
                lx1, lx2, ly1, ly2,
                px1, px2, py1, py2,
                sx1, sx2, sy1, sy2,
                height, width, alt, web_images
                )
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)');
            $array = array(
                $content_id, $image['src'], $image['seq'],
                $image['mx1'], $image['mx2'], $image['my1'], $image['my2'],
                $image['lx1'], $image['lx2'], $image['ly1'], $image['ly2'],
                $image['px1'], $image['px2'], $image['py1'], $image['py2'],
                $image['sx1'], $image['sx2'], $image['sy1'], $image['sy2'],
                $image['height'], $image['width'], $image['alt'], $image['web_images']);
            $stmt->execute($array);
        }
    }


    /**
     * Create website images.
     *
     * Create images in all aspect ratio and size combinations required for live website.
     *
     * @param array $images
     * @param string $folder defaults to 'images'.
     *
     * @return void
     */
    function createWebsiteImages($images, $folder='images')
    {
        $image_number = -1;
        foreach ($images as $image) {
            $image_number++;
            if ($image['web_images'] || substr($folder,0,7) == 'backups') {
                $_SESSION ['item']['images'][$image_number]['web_images'] = '0';
                ini_set('memory_limit', '1024M');

                // setup variables for source image from original image input
                list($originalWidth, $originalHeight, $type) = getimagesize(CONFIG_DIR.'/content/images/original/'.$image['src']);
                list($uncroppedWidth, $uncroppedHeight) = getimagesize(CONFIG_DIR.'/content/images/uncropped/'.$image['src']);
                if (! $image_from_src = imagecreatefromjpeg(CONFIG_DIR.'/content/images/original/'.$image['src']))
                    $this->reportError('cms/model/edit_update createWebsiteImages Problem with imagecreatefromjpeg, image src: '. $image['src']);

                // set up array of parameters of the aspect ratios and sizes to be created for this image
                $images_to_create = array(
                    'landscape' => array (
                        'x1' => $image['lx1'],
                        'x2' => $image['lx2'],
                        'y1' => $image['ly1'],
                        'y2' => $image['ly2'],
                        'aspect' => 'landscape',
                        'image_create' => $this->config['image_create_landscape'],
                        'image_ratio' => $this->convertConfigRatio($this->config['image_ratio_landscape']),
                        'image_sizes' => $this->config['image_sizes_landscape'],
                        'image_width' => $this->config['image_width_landscape'],
                    ),
                    'portrait' => array (
                        'x1' => $image['px1'],
                        'x2' => $image['px2'],
                        'y1' => $image['py1'],
                        'y2' => $image['py2'],
                        'aspect' => 'portrait',
                        'image_create' => $this->config['image_create_portrait'],
                        'image_ratio' => $this->convertConfigRatio($this->config['image_ratio_portrait']),
                        'image_sizes' => $this->config['image_sizes_portrait'],
                        'image_width' => $this->config['image_width_portrait'],
                    ),
                    'panorama' => array (
                        'x1' => $image['mx1'],
                        'x2' => $image['mx2'],
                        'y1' => $image['my1'],
                        'y2' => $image['my2'],
                        'aspect' => 'panorama',
                        'image_create' => $this->config['image_create_panorama'],
                        'image_ratio' => $this->convertConfigRatio($this->config['image_ratio_panorama']),
                        'image_sizes' => $this->config['image_sizes_panorama'],
                        'image_width' => $this->config['image_width_panorama'],
                    ),
                    'square' => array (
                        'x1' => $image['sx1'],
                        'x2' => $image['sx2'],
                        'y1' => $image['sy1'],
                        'y2' => $image['sy2'],
                        'aspect' => 'square',
                        'image_create' => $this->config['image_create_square'],
                        'image_ratio' => $this->convertConfigRatio($this->config['image_ratio_square']),
                        'image_sizes' => $this->config['image_sizes_square'],
                        'image_width' => $this->config['image_width_square'],
                    ),
                );

                // create images for the aspect ratios and sizes required for this input image
                foreach ($images_to_create as $i) {
                    if ( $i['image_create']) {
                        $size = 1;
                        do {
                            set_time_limit(30); // resets default time limit back to zero
                            $orig_1x = round ($originalWidth * ( $i['x1'] / $uncroppedWidth));
                            $orig_2x = round ($originalWidth * ( $i['x2'] / $uncroppedWidth));
                            $orig_y1 = round ($originalWidth * ( $i['y1'] / $uncroppedWidth));
                            $orig_y2 = round ($originalWidth * ( $i['y2'] / $uncroppedWidth));
                            $dst_URL = CONFIG_DIR.'/content/'.$folder.'/'. $i['aspect'].'/'. $size .'x/' . $image['src'];
                            $width  = $i['image_width'] * $size;
                            $height = ($i['image_width'] / $i['image_ratio']) * $size;
                            if (!$image_dst = imagecreatetruecolor( $width , $height ))
                                $this->reportError('cms/model/edit_update createWebsiteImages '. $i['aspect'].' image. Problem with imagecreatetruecolor');

                            if (!imagecopyresampled( $image_dst, $image_from_src, 0, 0, $orig_1x, $orig_y1,
                                                     $width, $height, ($orig_2x - $orig_1x), ($orig_y2 - $orig_y1)))
                                $this->reportError('cms/model/edit_update createWebsiteImages '. $i['aspect'].' image. Problem with imagecopyresampled');

                            if (!imagejpeg($image_dst, $dst_URL , $this->config['image_quality']))
                                $this->reportError('cms/model/edit_update createWebsiteImages '. $i['aspect'].' image. Problem with imagejpeg');

                            imagedestroy($image_dst);
                            $size = $size + 1;

                        } while ($i['image_sizes'] && $size <= 3);
                    }
                }
            }
        }
    }

    /**
     * Create a pending item on database.
     *
     */
    private function createPending($row)
    {
        $stmt = $this->dbh->prepare('INSERT INTO pendingItemsTable (
            content_id,
            created,
            createdBy,
            updated,
            updatedBy,
            status,
            heading,
            topic,
            subtopic,
            caption,
            text,
            date,
            download_src,
            download_name
            )
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)');
        $array= array(
            $row['content_id'],
            $row['created'],
            $row['createdBy'],
            $row['updated'],
            $row['updatedBy'],
            $row['status'],
            $row['heading'],
            $row['topic'],
            $row['subtopic'],
            $row['caption'],
            $row['text'],
            $row['date'],
            $row['download_src'],
            $row['download_name']);
        $stmt->execute($array);
        $inserted_id = $this->dbh->lastInsertId();
        $_SESSION['item']['id'] = $inserted_id;
    }


    /**
     * Update a pending item on database.
     *
     */
    private function updatePending($row)
    {
        $stmt = $this->dbh->prepare('UPDATE pendingItemsTable SET
            content_id = ?,
            created = ?,
            createdBy = ?,
            updated = ?,
            updatedBy = ?,
            status = ?,
            heading = ?,
            topic = ?,
            subtopic = ?,
            caption = ?,
            text = ?,
            date = ?,
            download_src = ?,
            download_name = ?
            WHERE
            id = ?');
        $array= array(
            $row['content_id'],
            $row['created'],
            $row['createdBy'],
            $row['updated'],
            $row['updatedBy'],
            $row['status'],
            $row['heading'],
            $row['topic'],
            $row['subtopic'],
            $row['caption'],
            $row['text'],
            $row['date'],
            $row['download_src'],
            $row['download_name'],
            $row['id'] );
        $stmt->execute($array);
    }

    /**
     * Update pending_id for content item on database.
     *
     */
    private function updateContentPending($pending_id, $id)
    {
        $stmt = $this->dbh->prepare('UPDATE itemsTable SET pending_id = ? WHERE id = ?');

        $array = array($pending_id, $id );
        $stmt->execute($array);
    }

    /**
     * Create search terms on database for a pending item.
     *
     */
    private function createPendingTerms($pending_id, $termStr)
    {
        $terms = explode(' ',$termStr);
        foreach($terms as $term) {
            $term = trim($term);
            if (strlen($term) >2) {
                $records = true;
                $stmt = $this->dbh->prepare('INSERT INTO pendingTermsTable (pending_id, term) VALUES (?, ?)');
                $array = array($pending_id, $term );
                $stmt->execute($array);
            }
        }
    }

    /**
     * Delete search terms on database for a pending item.
     *
     */
    private function deletePendingTerms($pending_id)
    {
        $stmt = $this->dbh->prepare('DELETE FROM pendingTermsTable WHERE pending_id = ?');
        $stmt->execute(array($pending_id));
    }

    /**
     * Create tags on database for a pending item.
     *
     */
    private function createPendingTags($pending_id, $tags)
    {
        foreach($tags as $tag) {
            $tag = trim($tag);
            $stmt = $this->dbh->prepare('INSERT INTO pendingTagsTable (pending_id, tag) VALUES (?, ?)');
            $array = array($pending_id, $tag );
            $stmt->execute($array);
        }
    }

    /**
     * Delete tags on database for a pending item.
     *
     */
    private function deletePendingTags($pending_id)
    {
        $stmt = $this->dbh->prepare('DELETE FROM pendingTagsTable WHERE pending_id = ?');
        $stmt->execute(array($pending_id));
    }

    /**
     * Delete images data on database for a pending item.
     *
     */
    private function deletePendingImages($pending_id)
    {
        $stmt = $this->dbh->prepare('DELETE FROM pendingImagesTable WHERE pending_id = ?');
        $stmt->execute(array($pending_id));
    }

    /**
     * Create images data on database for a pending item.
     *
     */
    private function createPendingImages($pending_id, $imageList)
    {
        foreach ($imageList as $image)
        {
            $stmt = $this->dbh->prepare('INSERT INTO pendingImagesTable (
                pending_id,
                src, seq,
                mx1, mx2, my1, my2,
                lx1, lx2, ly1, ly2,
                px1, px2, py1, py2,
                sx1, sx2, sy1, sy2,
                height, width, alt, web_images
                )
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)');
            $array = array(
                $pending_id, $image['src'], $image['seq'],
                $image['mx1'], $image['mx2'], $image['my1'], $image['my2'],
                $image['lx1'], $image['lx2'], $image['ly1'], $image['ly2'],
                $image['px1'], $image['px2'], $image['py1'], $image['py2'],
                $image['sx1'], $image['sx2'], $image['sy1'], $image['sy2'],
                $image['height'], $image['width'], $image['alt'], $image['web_images']
            );
            $stmt->execute($array);
        }
    }
}
