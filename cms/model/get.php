<?php

/**
 * model class for get
 *
 * @since 1.0.2
 * @author Keith Wheatley
 * @package echocms\get
 */
class getModel
{
    private $config = array();

    function __construct()
    {
        // The definition of URL and file paths allow for the script including this (get.php)
        // and the CMS directory to be in same or different (sub)directories
        define('CONTENT_DIR', substr(__DIR__,0,-5));
	      require CONTENT_DIR . 'config/db.php';
        $this->connCMS = new \PDO('mysql:host=' .$db_host. ';dbname=' .$db_name . ';charset=utf8mb4', $db_user, $db_pass, array(\PDO::ATTR_ERRMODE => \PDO::ERRMODE_EXCEPTION));
        $this->config = $this->getConfig();
        require CONTENT_DIR . 'config/url.php';
        if ($config_URL_DIR) $config_URL_DIR =  '/' . $config_URL_DIR;
        define('CONTENT_URL', '//' . $_SERVER['HTTP_HOST'] . $config_URL_DIR . '/cms/content/');

    error_log ('cms/model/get.php construct. __DIR__: ' . print_r(__DIR__, true) );
    error_log ('cms/model/get.php construct. $_SERVER[HTTP_HOST]: ' . print_r($_SERVER['HTTP_HOST'], true) );
    error_log ('cms/model/get.php construct. $config_URL_DIR: ' . print_r($config_URL_DIR, true) );
    error_log ('cms/model/get.php construct. CONTENT_DIR: ' . print_r(CONTENT_DIR, true) );
    error_log ('cms/model/get.php construct. CONTENT_URL: ' . print_r(CONTENT_URL, true) );

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
     * Gets an array of all items filtered by specified topic and/or subtopic
     *
     * @param string $topic
     * @param string $subtopic
     *
     * @return array $items
     */
	  public function items($topic = NULL, $subtopic = NULL)
	  {
        if (strtolower($topic) == 'all') $topic = NULL;
        if (strtolower($subtopic) == 'all') $subtopic = NULL;
        if ($subtopic AND !$topic)
            $sql = 'subtopic = "' . $subtopic . '" ';
        elseif (!$subtopic AND $topic)
            $sql = 'topic = "' . $topic . '" ';
        elseif ($subtopic AND $topic)
            $sql = 'topic = "' . $topic . '" AND subtopic = "' . $subtopic . '" ';
        else $sql = ''; //must be (!$subtopic and !$topic)
        if ($sql){
            $sql = 'WHERE ' . $sql;
        }
        $stmt = $this->connCMS->prepare(
            'SELECT id, date, heading, topic, subtopic, caption, text, download_src, download_name
            FROM itemsTable ' . $sql .' ORDER BY date DESC');
        $stmt->execute();
        $items = array();
        while ($row = $stmt->fetch(\PDO::FETCH_ASSOC)) {
            $id = $row['id'];
			      $items[$id] = $row;
            $items[$id] += $this->itemImages($id);
            $items[$id] += $this->itemTags($id);
            if ($items[$id]['download_src'] != null)
                $items[$id]['download_src'] = CONTENT_URL .'downloads/' . $items[$id]['download_src'];
            $items[$id]['this'] = '?id='. $id;
            if ($topic) $items[$id]['this'] .= '&topic=' . $topic;
            if ($subtopic) $items[$id]['this'] .= '&subtopic=' . $subtopic;
            $items[$id]['prev'] = $this->prevItem($items[$id]['date'], $topic, $subtopic);
            $items[$id]['next'] = $this->nextItem($items[$id]['date'], $topic, $subtopic);
            $items[$id]['date_tw'] = $this->date_tw($items[$id]['date']);
            $items[$id]['date_display'] = date($this->config['date_format'], strtotime( $items[$id]['date'] ));
            $items[$id]['text'] = html_entity_decode($items[$id]['text']);
        }

        //error_log ('cms/model/get.php  items. $items : ' . print_r($items, true) );
		    return ($items);
	  }

    /**
     * Get item
     *
     * Get a single item filtered by topic and/or subtopic
     *
     * @param array|string $param1
     * @param string $param2
     *
     * note:
     *   param1 can be an associative array with key of [id]
     *   param1 can be an associative array with keys of [id] and [topic] and/or [subtopic]
     *   param1 can be an associative array with keys of [id] and [tag]
     *   param1 can be a string containing a valid item id (param2 is then ignored)
     *   param1 can be a string in HTTP query string format with id plus optional topic and subtopic OR tag
	   *   param1 can be a string containing a topic with optionally param2 a string containing an subtopic
     *   in above, topic/subtopic can be absent, null or 'all', in which case all items are returned for the topic/subtopic)
     *
     * @return array $item
     */
	  public function item($param1 = NULL, $param2 = NULL)
	  {
        $item = array();
        $id = $tag = $topic = $subtopic = $sql = $sqlQ = $stmt = NULL;

        if (is_array($param1)) {
            if (!empty($param1['id']))
                 $id = $param1['id'];
            if (!empty($param1['tag']))
                $tag = $param1['tag'];
            else {
                if (!empty($param1['topic']) AND strtolower($param1['topic']) !== 'all')
                    $topic = $param1['topic'];
                if (!empty($param1['subtopic']) AND strtolower($param1['subtopic']) !== 'all')
                    $subtopic = $param1['subtopic'];
            }
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
                    if (!empty($array['tag'])) {
                        $tag = $array['tag'];
                    }
                    else {
                        if (!empty($array['topic']) AND strtolower($array['topic']) !== 'all')
                            $topic = $array['topic'];
                        if (!empty($array['subtopic']) AND strtolower($array['subtopic']) !== 'all')
                            $subtopic = $array['subtopic'];
                    }
                }
                else {
                    if ($param1 != NULL AND strtolower($param1) !== 'all')
                        $topic = $param1;
                    if (!empty($param2) AND strtolower($param2) !== 'all')
                        $subtopic = $param2;
                }
            }
        }

        $wa = ' WHERE';
        if ($id) {
            $sql .= $wa . ' id = ' . $id;
            $wa = ' AND';
        }

        else {
            if ($topic) {
                $sql .= $wa . ' topic = "' . $topic . '"';
                $wa = ' AND';
            }
            if ($subtopic) {
                $sql .= $wa . ' subtopic = "' . $subtopic . '"';
            }
        }


        $stmt = $this->connCMS->prepare(
             'SELECT id, date, heading, topic, subtopic, caption, text, download_src, download_name
              FROM itemsTable ' . $sql . ' LIMIT 1');
        $stmt->execute();
        $item = $stmt->fetch(\PDO::FETCH_ASSOC);
        if ($item) {
            if ($item['download_src'] != null)
                $item['download_src'] = CONTENT_URL . 'downloads/' . $item['download_src'];
            $item += $this->itemImages($item['id']);
            $item += $this->itemTags($item['id']);
            $item['date_tw'] = $this->date_tw($item['date']);
            $item['date_display'] = date($this->config['date_format'], strtotime( $item['date'] ));
            $item['text'] = html_entity_decode($item['text']);
            if ($tag) {
                $item['this'] = '?id='. $item['id']. '&tag=' . $tag;
                $item['prev'] = $this->prevItemForTag($item['date'], $tag);
                $item['next'] = $this->nextItemForTag($item['date'], $tag);
            }
            else {
                $item['this'] = '?id='. $item['id'];
                if ($topic) $item['this'] .= '&topic=' . $topic;
                if ($subtopic) $item['this'] .= '&subtopic=' . $subtopic;
                $item['prev'] = $this->prevItem($item['date'], $topic, $subtopic);
                $item['next'] = $this->nextItem($item['date'], $topic, $subtopic);
            }
        }

        //error_log ('cms/model/get.php  item. $item : ' . print_r($item, true) );
        return ($item);
    }

    /**
     * Query string for next item for topic/subtopic.
     *
     * Construct a URL query string with an item id which will locate the next item, after the specified date and
     * within the topic and subtopic specified.
     *
     * @param string $date
     * @param string $topic
     * @param string $subtopic
     *
     * @return string $next URL query string to locate next item
     */
    private function nextItem($date, $topic, $subtopic)
	  {
        $sql = '';
        if ($topic)
            $sql .= ' AND topic = "' . $topic . '"';
        if ($subtopic)
            $sql .= ' AND subtopic = "' . $subtopic . '"';
        $stmt = $this->connCMS->prepare(
                'SELECT id FROM itemsTable WHERE date > ?'
                . $sql . ' ORDER BY date ASC LIMIT 1');
        $stmt->execute(array($date));
        $row = $stmt->fetch(\PDO::FETCH_ASSOC);
        if ($row) {
            $next = '?id='. $row['id'];
            if ($topic)
                $next .= '&topic=' . $topic;
            if ($subtopic)
                $next .= '&subtopic=' . $subtopic;
        }
        else {
            $next = NULL;
        }

        return ($next);
	  }

    /**
     * Query string for previous item for topic/subtopic.
     *
     * Construct a URL query string with an item id which will locate the previous item, before the specified date and
     * within the topic and subtopic specified.
     *
     * @param string $date
     * @param string $topic
     * @param string $subtopic
     *
     * @return string $next URL query string to locate previous item
     */
	  private function prevItem($date, $topic, $subtopic)
	  {
        $sql = '';
        if ($topic)
            $sql .= ' AND topic = "' . $topic . '"';
        if ($subtopic)
            $sql .= ' AND subtopic = "' . $subtopic . '"';
        $stmt = $this->connCMS->prepare(
             'SELECT id FROM itemsTable WHERE date < ? '
              . $sql . ' ORDER BY date DESC LIMIT 1');
        $stmt->execute(array($date));
        $row = $stmt->fetch(\PDO::FETCH_ASSOC);
        if ($row) {
            $prev = '?id='. $row['id'];
            if ($topic)
                $prev .= '&topic=' . $topic;
            if ($subtopic)
                $prev .= '&subtopic=' . $subtopic;
        }
        else {
            $prev = NULL;
        }

		    return ($prev);
	  }

    /**
     * Get items for tag.
     *
     * Gets an array of all items filtered by the specified tag.
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
             itemsTable.topic,
             itemsTable.subtopic,
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
                $items[$id]['download_src'] = CONTENT_URL . 'downloads/' . $items[$id]['download_src'];

            $items[$id]['this'] = '?id='. $items[$id]['id']. '&tag=' . $tag;
            $items[$id]['prev'] = $this->prevItemForTag($items[$id]['date'], $tag);
            $items[$id]['next'] = $this->nextItemForTag($items[$id]['date'], $tag);

            $items[$id]['date_tw'] = $this->date_tw($items[$id]['date']);
            $items[$id]['date_display'] = date($this->config['date_format'], strtotime( $items[$id]['date'] ));
            $items[$id]['text'] = html_entity_decode($items[$id]['text']);
        }

        //error_log ('cms/model/get.php  itemsForTag. $items : ' . print_r($items, true) );
		    return ($items);
	  }

    /**
     * Query string for next item for tag.
     *
     * Construct a URL query string with an item id which will locate the next item,
     * after the date and within the tag specified.
     *
     * @param string $date
     * @param string $tag
     *
     * @return string $next URL query string to locate next item
     */
    private function nextItemForTag($date = NULL, $tag = NULL)
	  {
        $stmt = $this->connCMS->prepare(
            'SELECT itemsTable.id
             FROM itemsTable
             LEFT JOIN tagsTable
             ON tagsTable.content_id = itemsTable.id
             WHERE date > ? AND tagsTable.tag = ?
             ORDER BY itemsTable.date ASC LIMIT 1');
        $stmt->execute(array($date,$tag));
        $row = $stmt->fetch(\PDO::FETCH_ASSOC);
        if ($row) {
            $next = '?id='. $row['id'] . '&tag=' . $tag;
        }
        else {
            $next = NULL;
        }

        return ($next);
	  }

    /**
     * Query string for previous item for tag.
     *
     * Construct a URL query string with an item id which will locate the previous item,
     * before the date and within the tag specified.
     *
     * @param string $date
     * @param string $tag
     *
     * @return string $next URL query string to locate next item
     */
    private function prevItemForTag($date = NULL, $tag = NULL)
	  {
        $stmt = $this->connCMS->prepare(
            'SELECT itemsTable.id
             FROM itemsTable
             LEFT JOIN tagsTable
             ON tagsTable.content_id = itemsTable.id
             WHERE date < ? AND tagsTable.tag = ?
             ORDER BY itemsTable.date DESC LIMIT 1');
        $stmt->execute(array($date, $tag));
        $row = $stmt->fetch(\PDO::FETCH_ASSOC);
        if ($row) {
            $next = '?id='. $row['id'] . '&tag=' . $tag;
        }
        else {
            $next = NULL;
        }

        return ($next);
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
                $items[$item['id']]['download_src'] = CONTENT_URL . 'downloads/' . $item['download_src'];
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
        $url = CONTENT_URL . 'images/';
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
