<?php

/**
 * model class for get
 *
 * @since 1.0.0
 * @author Keith Wheatley
 * @package echocms\get
 */
class getModel
{
    private $config = array();

    function __construct()
    {

	      require $_SERVER['DOCUMENT_ROOT'] . '/cms/config/db.php';
        $this->connCMS = new \PDO('mysql:host=' .$db_host. ';dbname=' .$db_name. ';charset=utf8mb4', $db_user, $db_pass,
                             array(\PDO::ATTR_ERRMODE => \PDO::ERRMODE_EXCEPTION));
        define('URL_content', '//' . $_SERVER['HTTP_HOST'] . '/cms/content/');
        $this->config = $this->getConfig();

	  }

    /**
     * Get all CMS config items.
     *
     * @return array $config {
     *      setting string containing name of setting
     *      value string containing value of configuration setting
     * }
     */
	   private function getConfig()
	   {
		    $stmt = $this->connCMS->prepare('SELECT setting, value FROM config');
		    $stmt->execute();
            $configValues = array();
            while ($row = $stmt->fetch(\PDO::FETCH_ASSOC)) {
                $configValues[$row['setting']] = $row['value'];
            }

		    return ($configValues);
	   }

    /**
     * Get items
     *
     * Gets an array of items filtered by page and/or element
     *
     * @param string $page
     * @param string $element
     *
     * @return array $items
     */
	  public function items($page = NULL, $element = NULL)
	  {
        if (strtolower($page) == 'all') $page = NULL;
        if (strtolower($element) == 'all') $element = NULL;
        if ($element AND !$page)
            $sql = 'element = "' . $element . '" ';
        elseif (!$element AND $page)
            $sql = 'page = "' . $page . '" ';
        elseif ($element AND $page)
            $sql = 'page = "' . $page . '" AND element = "' . $element . '" ';
        else $sql = ''; //must be (!$element and !$page)
        if ($sql){
            $sql = 'WHERE ' . $sql;
        }
        $stmt = $this->connCMS->prepare(
            'SELECT id, date, heading, page, element, caption, text, download_src, download_name
            FROM itemsTable ' . $sql .' ORDER BY date DESC');
        $stmt->execute();
        $items = array();
        while ($row = $stmt->fetch(\PDO::FETCH_ASSOC)) {
            $id = $row['id'];
			      $items[$id] = $row;
            $items[$id] += $this->itemImages($id);
            $items[$id] += $this->itemTags($id);
            if ($items[$id]['download_src'] != null)
                $items[$id]['download_src'] = URL_content .'downloads/' . $items[$id]['download_src'];
            $items[$id]['this'] = 'id='. $id . '&page=' . $page  . '&element=' . $element;
            $items[$id]['prev'] = $this->itemPrev($items[$id]['date'], $page, $element);
            $items[$id]['next'] = $this->itemNext($items[$id]['date'], $page, $element);
            $items[$id]['date_tw'] = $this->date_tw($items[$id]['date']);
            $items[$id]['date_display'] = date($this->config['date_format'], strtotime( $items[$id]['date'] ));
            $items[$id]['text'] = html_entity_decode($items[$id]['text']);
        }

		    return ($items);
	  }

    /**
     * Get item
     *
     * Get a single item filtered by page and/or element
     *
     * @param array|string $param1
     * @param string $param2
     *
     * note:
     *   param1 can be an associative array with key of [id] plus optionally [page] and [element]
     *   param1 can be a string containing a valid item id (param2 is then ignored)
     *   param1 can be a string in HTTP query string format with id plus optional page and element
	   *   param1 can be a string containing a page with optionally param2 a string containing a element
     *   in all of above, page/element can be absent, null or 'all', in which case all items are returned for the page/element)
     *
     * @return array $item
     */
	  public function item($param1 = NULL, $param2 = NULL)
	  {
        $item = array();
        $id = $page = $element = $sql = $sqlQ = $stmt = NULL;
        $wa = 'WHERE ';
        if (is_array($param1)) {
            if (!empty($param1['id']))
                 $id = $param1['id'];
            if (!empty($param1['page']) AND strtolower($param1['page']) !== 'all')
                 $page = $param1['page'];
            if (!empty($param1['element']) AND strtolower($param1['element']) !== 'all')
                $element = $param1['element'];
        }
        elseif (isset($param1)) {
            if (is_numeric($param1)) {
                $stmt = $this->connCMS->prepare('SELECT id FROM itemsTable WHERE id = '. $param1 .' ');
                $stmt->execute();
                $stmt->fetch(\PDO::FETCH_ASSOC);
            }
            if ($stmt) {
                $id = $param1;
            }
            else {
                parse_str ($param1, $array);
                if (!empty($array['id'])) {
                    $id = $array['id'];
                    if (!empty($array['page']) AND strtolower($array['page']) !== 'all')
                         $page = $array['page'];
                    if (!empty($array['element']) AND strtolower($array['element']) !== 'all')
                         $element = $array['element'];
                }
                else {
                    if ($param1 != NULL AND strtolower($param1) !== 'all')
                        $page = $param1;
                    if (!empty($param2) AND strtolower($param2) !== 'all')
                        $element = $param2;
                }
            }
        }
        $wa = ' WHERE';
        if ($id) {
            $sql .= $wa . ' id = ' . $id;
            $wa = ' AND';
        }
        if ($page) {
            $sql .= $wa . ' page = "' . $page . '"';
            $wa = ' AND';
        }
        if ($element) {
            $sql .= $wa . ' element = "' . $element . '"';
        }
        $stmt = $this->connCMS->prepare(
             'SELECT id, date, heading, page, element, caption, text, download_src, download_name
              FROM itemsTable ' . $sql . ' LIMIT 1');
        $stmt->execute();
        $item = $stmt->fetch(\PDO::FETCH_ASSOC);
        if ($item) {
            if ($item['download_src'] != null)
                $item['download_src'] = URL_content . 'downloads/' . $item['download_src'];
            $item += $this->itemImages($item['id']);
            $item += $this->itemTags($item['id']);
            $item['this'] = 'id='. $item['id']. '&page=' . $page  . '&element=' . $element;
            $item['prev'] = $this->itemPrev($item['date'], $page, $element);
            $item['next'] = $this->itemNext($item['date'], $page, $element);
            $item['date_tw'] = $this->date_tw($item['date']);
            $item['date_display'] = date($this->config['date_format'], strtotime( $item['date'] ));
            $item['text'] = html_entity_decode($item['text']);
        }

        //error_log ('cms/model/get.php  item. $item : ' . print_r($item, true) );
        return ($item);
    }

    /**
     * Get items for tag.
     *
     * Get all items which are tagged with the specified tag.
     *
     * @param string $tag
     *
     * @return array $items
     */
    public function itemsForTag($tag)
	  {
		    $stmt = $this->connCMS->prepare('SELECT
					   itemsTable.id,
					   itemsTable.date,
					   itemsTable.heading,
             itemsTable.page,
             itemsTable.element,
	  	   		 itemsTable.caption,
             itemsTable.text,
             itemsTable.download_src,
             itemsTable.download_name
				FROM itemsTable
				LEFT JOIN tagsTable
					 ON tagsTable.content_id = itemsTable.id
				WHERE tagsTable.tag = ?
				ORDER BY itemsTable.date DESC');
        $stmt->execute(array($tag));
        $items = array();
        while ($row = $stmt->fetch(\PDO::FETCH_ASSOC)) {
            $id = $row['id'];
			$items[$id] = $row;
            $items[$id] += $this->itemImages($id);
            $items[$id] += $this->itemTags($id);
            if ($items[$id]['download_src'] != null)
                $items[$id]['download_src'] = URL_content . 'downloads/' . $items[$id]['download_src'];
            $items[$id]['date_tw'] = $this->date_tw($items[$id]['date']);
            $items[$id]['date_display'] = date($this->config['date_format'], strtotime( $items[$id]['date'] ));
            $items[$id]['text'] = html_entity_decode($items[$id]['text']);

        }

		    return ($items);
	  }

    /**
     * Query string for next item.
     *
     * Construct a URL query string with an item id which will locate the next item, after the specified date and
     * within the page and element specified.
     *
     * @param string $date
     * @param string $page
     * @param string $element
     *
     * @return string $next URL query string to locate next item
     */
    private function itemNext($date, $page, $element)
	  {
        $sql = '';
        if ($page)
            $sql .= ' AND page = "' . $page . '"';
        if ($element)
            $sql .= ' AND element = "' . $element . '"';
        $stmt = $this->connCMS->prepare(
                'SELECT id FROM itemsTable WHERE date > ?'
                . $sql . ' ORDER BY date ASC LIMIT 1');
        $stmt->execute(array($date));
        $row = $stmt->fetch(\PDO::FETCH_ASSOC);
        if ($row) {
            $next = '?id='. $row['id'];
            if ($page)
                $next .= '&page=' . $page;
            if ($element)
                $next .= '&element=' . $element;
        }
        else {
            $next = NULL;
        }

        return ($next);
	  }

    /**
     * Query string for previous item.
     *
     * Construct a URL query string with an item id which will locate the previous item, before the specified date and
     * within the page and element specified.
     *
     * @param string $date
     * @param string $page
     * @param string $element
     *
     * @return string $next URL query string to locate previous item
     */
	  private function itemPrev($date, $page, $element)
	  {
        $sql = '';
        if ($page)
            $sql .= ' AND page = "' . $page . '"';
        if ($element)
            $sql .= ' AND element = "' . $element . '"';
        $stmt = $this->connCMS->prepare(
             'SELECT id FROM itemsTable WHERE date < ? '
              . $sql . ' ORDER BY date DESC LIMIT 1');
        $stmt->execute(array($date));
        $row = $stmt->fetch(\PDO::FETCH_ASSOC);
        if ($row) {
            $prev = '?id='. $row['id'];
            if ($page)
                $prev .= '&page=' . $page;
            if ($element)
                $prev .= '&element=' . $element;
        }
        else {
            $prev = NULL;
        }

		    return ($prev);
	  }

    /**
     * Get item images.
     *
     * Get all images data from database for a single specified item
     *
     * @param integer $id
     *
     * @return array $images
     */
	  private function itemImages($id)
	  {
        $stmt = $this->connCMS->prepare(
           'SELECT src, alt, seq, width FROM imagesTable WHERE content_id = ? ORDER BY seq ASC');
        $stmt->execute(array($id));
        $images['images'] = array();
        while ($row = $stmt->fetch(\PDO::FETCH_ASSOC)) {
            $images['images'][] = $this->imageDetails($row);
        }

		    return ($images);
    }

    /**
     * Get items images.
     *
	   * Get images data from database for each item in the supplied array of items
     * and add the images data to the items array.
	   *
     * @param array $items
     *
     * @return array $items
     */
	  private function itemsImages($items)
	  {
        foreach ($items as $item) {
            // insert URL for downloads
            if (!empty($item['download_src']))
                $items[$item['id']]['download_src'] = URL_content . 'downloads/' . $item['download_src'];
            $stmt = $this->connCMS->prepare(
               'SELECT src, alt, seq FROM imagesTable WHERE content_id = ? ORDER BY seq ASC');
            $stmt->execute(array($item['id']));
            $images['images'] = array();
            while ($row = $stmt->fetch(\PDO::FETCH_ASSOC)) {
                $images['images'][] = $this->imageDetails($row);
            }

            $items[$item['id']] += $images;
        }

		    return ($items);
    }

    /**
     * Construct image details
     *
     * Construct an array of image details formatted to enable images to be located for website
     *
     * @param array $image
     *
     * @return array $imageDetails
     */
	  private function imageDetails($image)
	  {
        $url = URL_content . 'images/';
        $panorama_2x  = ($this->config['image_sizes_panorama'])  ? 'panorama/2x/' : 'panorama/1x/';
        $panorama_3x  = ($this->config['image_sizes_panorama'])  ? 'panorama/3x/' : 'panorama/1x/';
        $portrait_2x  = ($this->config['image_sizes_portrait'])  ? 'portrait/2x/' : 'portrait/1x/';
        $portrait_3x  = ($this->config['image_sizes_portrait'])  ? 'portrait/3x/' : 'portrait/1x/';
        $landscape_2x = ($this->config['image_sizes_landscape']) ? 'landscape/2x/': 'landscape/1x/';
        $landscape_3x = ($this->config['image_sizes_landscape']) ? 'landscape/3x/': 'landscape/1x/';
        $square_2x    = ($this->config['image_sizes_square'])    ? 'square/2x/'   : 'square/1x/';
        $square_3x    = ($this->config['image_sizes_square'])    ? 'square/3x/'   : 'square/1x/';

        $imageDetails = array(
            'panorama' => array ( 'src'=> $url.'panorama/1x/'.$image['src'],
                                  '1x' => $url.'panorama/1x/'.$image['src'],
                                  '2x' => $url.$panorama_2x.$image['src'],
                                  '3x' => $url.$panorama_3x.$image['src'],
                                  'srcset-w' =>
                                          $url.'panorama/1x/'.$image['src'] . ' ' . $image['width']  . 'w, ' .
                                          $url.$panorama_2x.$image['src'] . ' ' .  $image['width']*2 . 'w, ' .
                                          $url.$panorama_3x.$image['src'] . ' ' .  $image['width']*3 . 'w',
                                  'srcset-d' =>
                                          $url.'panorama/1x/'.$image['src'] . ', ' .
                                          $url.$panorama_2x.$image['src'] . ' x2, ' .
                                          $url.$panorama_3x.$image['src'] . ' x3') ,

            'portrait' => array ( 'src'=> $url.'portrait/1x/'.$image['src'],
                                  '1x' => $url.'portrait/1x/'.$image['src'],
                                  '2x' => $url.$portrait_2x.$image['src'],
                                  '3x' => $url.$portrait_3x.$image['src'],
                                  'srcset-w' =>
                                          $url.'portrait/1x/'.$image['src']. ' ' .  $image['width']  . 'w, ' .
                                          $url.$portrait_2x.$image['src'] . ' ' .  $image['width']*2 . 'w, ' .
                                          $url.$portrait_3x.$image['src'] . ' ' .  $image['width']*3 . 'w',
                                  'srcset-d' =>
                                          $url.'portrait/1x/'.$image['src'] . ', ' .
                                          $url.$portrait_2x.$image['src']. ' x2, ' .
                                          $url.$portrait_3x.$image['src'] . ' x3'),

            'landscape' => array ('src'=> $url.'landscape/1x/'.$image['src'],
                                  '1x' => $url.'landscape/1x/'.$image['src'],
                                  '2x' => $url.$landscape_2x.$image['src'],
                                  '3x' => $url.$landscape_3x.$image['src'],
                                  'srcset-w' =>
                                          $url.'landscape/1x/'.$image['src'] . ' ' .  $image['width'] . 'w, ' .
                                          $url.$landscape_2x.$image['src'] . ' ' .  $image['width']*2 . 'w, ' .
                                          $url.$landscape_3x.$image['src'] . ' ' .  $image['width']*3 . 'w',
                                  'srcset-d' =>
                                          $url.'landscape/1x/'.$image['src'] . ', ' .
                                          $url.$landscape_2x.$image['src'] . ' x2, ' .
                                          $url.$landscape_3x.$image['src'] . ' x3'),

            'square' => array (   'src'=> $url.'square/1x/'.$image['src'],
                                  '1x' => $url.'square/1x/'.$image['src'],
                                  '2x' => $url.$square_2x.$image['src'],
                                  '3x' => $url.$square_3x.$image['src'],
                                  'srcset-w' =>
                                          $url.'square/1x/'.$image['src'] . ' ' .  $image['width'] . 'w, ' .
                                          $url.$square_2x.$image['src'] . ' ' .  $image['width']*2 . 'w, ' .
                                          $url.$square_3x.$image['src'] . ' ' .  $image['width']*3 . 'w',
                                  'srcset-d' =>
                                          $url.'square/1x/'.$image['src'] . ', ' .
                                          $url.$square_2x.$image['src'] . ' x2, ' .
                                          $url.$square_3x.$image['src'] . ' x3'),

            'thumbnail' => $url.'thumbnail/'.$image['src'],
            'uncropped' => $url.'uncropped/'.$image['src'],
            'alt' => $image['alt'],
            'seq' => $image['seq']
        );
		    return ($imageDetails);
    }

    /**
     * Get item tags.
     *
     * Get tags data from database for a single specified item.
     *
     * @param integer $id
     *
     * @return array $tags
     */
	  private function itemTags($id)
	  {
        $stmt = $this->connCMS->prepare('SELECT
            tag
            FROM tagsTable
            WHERE
            content_id = ?');
        $stmt->execute(array($id));
        $tags['tags'] = array();
        while ($row = $stmt->fetch(\PDO::FETCH_ASSOC)) {
            $tags['tags'][] = $row['tag'];
        }

		    return ($tags);
    }

    /**
     * Get items tags
     *
	   * Get tags data from database for each item in the supplied array of items
     * and add the tags data to the items array.
     *
     * @param array $items
     *
     * @return array $items
     */
	  private function itemsTags($items)
	  {
        foreach ($items as $item) {
            $stmt = $this->connCMS->prepare(
                'SELECT tag FROM tagsTable WHERE content_id = ?');
            $stmt->execute(array($item['id']));
            $tags['tags'] = array();
            while ($row = $stmt->fetch(\PDO::FETCH_ASSOC)) {
                $tags['tags'][] = $row['tag'];
            }
            $items[$item['id']] += $tags;
        }

		    return ($items);
    }


    /**
     * Twitter style date
     *
     * @param string $str in mysql DATETIME (YYYY-MM-DD HH:MM:SS) format
     *
     * @return string $displayDate in facebook/twitter-style format (e.g 2 days ago, 2 Jan 2016)
     */
    private function date_tw($str)
	  {
	    list($date, $time) = explode(' ', $str);
	    list($year, $month, $day) = explode('-', $date);
	    list($hour, $minute, $second) = explode(':', $time);
	    $timestamp = mktime($hour, $minute, $second, $month, $day, $year);
        $days_ago = round( ( date('U') - $timestamp ) / ( 60*60*24 ) );
        if($days_ago == 0)
            $displayDate = 'today';
        elseif($days_ago == 1)
            $displayDate = 'yesterday';
        elseif($days_ago < 8)
            $displayDate =  $days_ago.' days ago.';
        else
            $displayDate = date($this->config['date_format'], strtotime($str));

        return ($displayDate);
    }
}

/**
 * Create an instance of this class
 */
$get = new getModel();
