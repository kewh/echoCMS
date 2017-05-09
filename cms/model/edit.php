<?php
/**
 * model class for edit
 *
 * @since 1.0.1
 * @author Keith Wheatley
 * @package echocms\edit
 */

namespace echocms;

class editModel
{
    protected $config;
    protected $dbh;

    function __construct(\PDO $dbh, $config)
    {
        $this->dbh = $dbh;
        $this->config = $config;
	  }

	  /**
	   *  Get a list of ids for live content items
     *
     * @return array $items
	   */
    function getContentItemsList()
	  {
        $items = array();
        $stmt = $this->dbh->prepare('SELECT id FROM itemsTable');
        $stmt->execute();
        while ($row = $stmt->fetch(\PDO::FETCH_ASSOC)) {
            $items[] =  $row['id'];
        }

        return ($items);
	  }

    /**
     *  Gets a list of ids for Pending items, excluding 'offline' items
     *
     * @return array $items
	   */
    function getPendingItemsList()
	  {
        $status = 'offline';
        $items = array();
		    $stmt = $this->dbh->prepare('SELECT id FROM pendingItemsTable WHERE status != ?');
		    $stmt->execute(array($status));
        while ($row = $stmt->fetch(\PDO::FETCH_ASSOC)) {
			      $items[] =  $row['id'];
		    }

		    return ($items);
	  }

    /**
	   *  Gets list of all unique config, live & pending, and newly added elements
	   *
     * @return array $elements
	   */
	  function getElementList()
	  {
        // get default elements from config
        $allElements = $this->config['elements'];

        // get elements from Pending items but not 'offline' items
        $status = 'offline';
		    $stmt = $this->dbh->prepare('SELECT element FROM pendingItemsTable WHERE status != ?');
	      $stmt->execute(array($status));
        while ($row = $stmt->fetch(\PDO::FETCH_ASSOC)) {
			      if (!empty($row['element']))
                $allElements[] =  $row['element'];
	      }

        // get elements from live items
		    $stmt = $this->dbh->prepare('SELECT element FROM itemsTable');
	      $stmt->execute();
        while ($row = $stmt->fetch(\PDO::FETCH_ASSOC)) {
			      if (!empty($row['element']))
                $allElements[] =  $row['element'];
		    }

        // get newly added element from session data
        $allElements[] = $_SESSION['item']['element'];

	      $elements = array_unique ($allElements);

	      return ($elements);
	  }

    /**
	   * Gets a list of all unique Live & Pending, and newly added Pages
     *
     * @return array $pages
	   */
	  function getPagesList()
    {
        // get default pages from config
        $allPages = $this->config['pages'];

        // get Pending items but not 'offline' items
        $status = 'offline';
	      $stmt = $this->dbh->prepare('SELECT page FROM pendingItemsTable WHERE status != ?');
	      $stmt->execute(array($status));
        while ($row = $stmt->fetch(\PDO::FETCH_ASSOC)) {
        		if (!empty($row['page']))
		            $allPages[] =  $row['page'];
		    }

        // get all Live items
		    $stmt = $this->dbh->prepare('SELECT page FROM itemsTable');
	      $stmt->execute();
        while ($row = $stmt->fetch(\PDO::FETCH_ASSOC)) {
        		if (!empty($row['page']))
		            $allPages[] =  $row['page'];
		    }

        // get page newly added to session data
        $allPages[] = $_SESSION['item']['page'];

		    $pages = array_unique ($allPages);

		    return ($pages);
	   }

    /**
	   * Gets all editable itemsTable items, i.e. excluding items with update pending
     *
     * @return array $contentList
	   */
	  function getEditableContent()
	  {
		    $stmt = $this->dbh->prepare('SELECT * FROM itemsTable WHERE pending_id IS NULL');
		    $stmt->execute();
		    $contentList = array();
        while ($row = $stmt->fetch(\PDO::FETCH_ASSOC)) {
            $contentList[$row['id']] = $row;
		    }

		    return ($contentList);
	  }

	  /**
     * Gets all details for a single content item
	   *
     * @param integer $id user id
     *
     * @return array $item
	   */
	  function getContentDetails($id)
    {
        $stmt = $this->dbh->prepare('SELECT * FROM itemsTable WHERE id = ? LIMIT 1 FOR UPDATE');
		    $stmt->execute(array($id));
        $item = array();
        while ($row = $stmt->fetch(\PDO::FETCH_ASSOC)) {
            $item = $row;
		    }

		    return ($item);
	  }

    /**
     * Gets 'draft' & 'update' pending items (not 'offline')
     *
     * @return array $pendingList sorted pending items
     */
	  function getPending()
	  {
        $status = 'offline';
		    $stmt = $this->dbh->prepare('SELECT * FROM pendingItemsTable WHERE status != ?');
		    $stmt->execute(array($status));
		    $pendingList = array();
        while ($row = $stmt->fetch(\PDO::FETCH_ASSOC)) {
            $pendingList[$row['id']] = $row;
		    }

		    //  sort list into status within heading within element
		    //  Ref: sort multidimentional array on columns, see http://php.net/manual/en/function.array-multisort.php
        $page = $element = $date = $status = $heading = array();
		    if ( count($pendingList) > 1) {
		        foreach ($pendingList as $key => $row) {
    			      $heading[$key]  = $row['heading'];
    			      $status[$key]  = $row['status'];
    			      $date[$key]  = $row['date'];
    			      $element[$key] = $row['element'];
    			      $page[$key] = $row['page'];
			      }
			      array_multisort ( $page, SORT_ASC, $element, SORT_ASC, $date, SORT_DESC, $status, SORT_DESC,  $heading, SORT_ASC, $pendingList);
		    }

		    return ($pendingList);
	  }

    /**
     * Gets a sorted list of 'offline' pending items
	   *
	   * @return array $pendingList
	   */
	  function getAllArchived()
	  {
        $status = 'offline';
		    $stmt = $this->dbh->prepare('SELECT * FROM pendingItemsTable WHERE status = ?');
		    $stmt->execute(array($status));
		    $pendingList = array();
        while ($row = $stmt->fetch(\PDO::FETCH_ASSOC)) {
            $pendingList[$row['id']] = $row;
	      }

		    //  SORT LIST into status within heading within element
    	  //  ref: sort multidimentional array on columns, see http://php.net/manual/en/function.array-multisort.php
        $page = $element = $date = $status = $heading = array();
    	  if ( count($pendingList) > 1) {
            foreach ($pendingList as $key => $row) {
        		    $heading[$key]  = $row['heading'];
    			      $status[$key]  = $row['status'];
    			      $date[$key]  = $row['date'];
        		    $element[$key] = $row['element'];
    			      $page[$key] = $row['page'];
            }
        	  array_multisort ( $page, SORT_ASC, $element, SORT_ASC, $date, SORT_DESC, $status, SORT_DESC,  $heading, SORT_ASC, $pendingList);
    	  }

		    return ($pendingList);
    }

    /**
     * Gets all details for a single pending item
     *
     * @param integer $id user id
     *
     * @return array $items
     */
    function getPendingDetails($id)
    {
		    $row = array();
		    $stmt = $this->dbh->prepare('SELECT * FROM pendingItemsTable WHERE id = ? LIMIT 1 FOR UPDATE');
		    $stmt->execute(array($id));
        $item = array();
        while ($row = $stmt->fetch(\PDO::FETCH_ASSOC)) {
            $item = $row;
		    }

		    return ($item);
    }

    /**
     * Gets a list of all unique tags from all pending and content items, and newly added tags
	   *
     * @return array $tagList
	   */
	  function getAllTagList()
    {
	      $tagsAll = array();

        // get pending tags
		    $stmt = $this->dbh->prepare('SELECT tag FROM pendingTagsTable');
	      $stmt->execute();
        while ($row = $stmt->fetch(\PDO::FETCH_ASSOC)) {
            $tagsAll[] = $row['tag'];
        }

        // add live tags
		    $stmt = $this->dbh->prepare('SELECT tag FROM tagsTable');
	      $stmt->execute();
        while ($row = $stmt->fetch(\PDO::FETCH_ASSOC)) {
            $tagsAll[] = $row['tag'];
        }

        // add newly added tags from SESSION data
        foreach ($_SESSION['item']['tags'] as $tag) {
            $tagsAll[] = $tag;
        }

        // merge and sort tags
		    $tagList = array_unique ($tagsAll);
	      asort($tagList);

		    return ($tagList);
	  }

    /**
     * Gets a list of tags for a specified pending item
	   *
     * @param integer $pending_id user id
     *
     * @return array $tags
	   */
	  function getPendingTags($pending_id)
	  {
		    $stmt = $this->dbh->prepare('SELECT tag FROM pendingTagsTable WHERE pending_id = ?');
	      $stmt->execute(array($pending_id));
	      $tags = array();
        while ($row = $stmt->fetch(\PDO::FETCH_ASSOC)) {
			      $tags[] = $row['tag'];
		    }

		    return ($tags);
	  }

    /**
     * Gets a list of tags for a specified content item
	   *
     * @param integer $content_id user id
     *
     * @return array $tags
	   */
	  function getContentTags($content_id)
	  {
		    $stmt = $this->dbh->prepare('SELECT tag FROM tagsTable WHERE content_id = ?');
	      $stmt->execute(array($content_id));
	      $tags = array();
        while ($row = $stmt->fetch(\PDO::FETCH_ASSOC)) {
			      $tags[] = $row['tag'];
		    }

		    return ($tags);
	  }

    /**
     * Gets all images for a specified content item
	   *
     * @param integer $content_id user id
     *
     * @return array $imageList
     */
	  function getContentImages($content_id)
	  {
        $stmt = $this->dbh->prepare('SELECT * FROM imagesTable WHERE content_id = ? ORDER BY seq ASC');
		    $stmt->execute(array($content_id));
	      $imageList = array();
        while ($row = $stmt->fetch(\PDO::FETCH_ASSOC)) {
            $imageList[] = $row;
		    }

		    return ($imageList);
	  }

    /**
     * Gets all images for a specified pending item
	   *
     * @param integer $pending_id user id
     *
     * @return array $imageList
	   */
	  function getPendingImages($pending_id)
	  {
        $stmt = $this->dbh->prepare('SELECT * FROM pendingImagesTable WHERE pending_id = ? ORDER BY seq ASC');
		    $stmt->execute(array($pending_id));
	      $imageList = array();
        while ($row = $stmt->fetch(\PDO::FETCH_ASSOC)) {
            $imageList[] = $row;
		    }

		    return ($imageList);
	  }

    /**
	   * Get merged list of items.
	   *
     * Gets a merged list of all content and pending items,
     *   excluding   - 'offline' items in pending table
     *               - 'live' items in contents table with an 'update' item in Pending.
	   *
     * @return array $mergedList
	   */
	  function getMergedList()
	  {
        $contentList = $this->getEditableContent();
		    $pendingList = $this->getPending();
		    $mergedList = array_merge($contentList, $pendingList);
	      // Sort multidimentional array on columns, see http://php.net/manual/en/function.array-multisort.php
        $page = $element = $date = $status = $heading = array();
		    if (isset ($mergedList) &&  count($mergedList) > 1) {
	          foreach ($mergedList as $key => $row) {
                $heading[$key]  = $row['heading'];
                $status[$key]  = $row['status'];
                $date[$key]  = $row['date'];
                $element[$key] = $row['element'];
                $page[$key] = $row['page'];
			      }
            array_multisort ( $page, SORT_ASC, $element, SORT_ASC, $date, SORT_DESC, $status, SORT_DESC,  $heading, SORT_ASC, $mergedList);
		    }

		        return ($mergedList);
	   }

    /**
     * Converts aspect ratio to float.
     *
     * @param string $configRatio config ratio in '9:9' format
     *
     * @return float $floatRatio
     */
    function convertConfigRatio($configRatio)
    {
        if (strpos($configRatio, ':') !== false) {
            $ratioArray = explode(':', $configRatio);
            if (isset($ratioArray[1]) && $ratioArray[1] >0 )
                $floatRatio = $ratioArray[0] / $ratioArray[1];
            else $floatRatio = 1;
        }
        else $floatRatio = 1;

        return $floatRatio;
    }

    /**
     * Report a system error.
     *
     * Reports error to error_log then sets header location to error notification page and exits script.
     *
     * @param string $message
     */
	  public function reportError($message)
	  {
        error_log ('cms/model/auth.php  reportError session data: ' . print_r($_SESSION, true) );
        header('location: ' . CONFIG_URL. 'error/notify' );
        exit();
	  }
}
