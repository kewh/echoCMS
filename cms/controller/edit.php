<?php
/**
 * controller class for edit
 *
 *
 * @since 1.0.1
 * @author Keith Wheatley
 * @package echocms\edit
 */
namespace echocms;

class edit
{
    protected $dbh;
    protected $config;
    public $auth;

    function __construct()
    {
        require CONFIG_DIR. '/model/config.php';
        $this->configModel = new configModel();
        $this->dbh = $this->configModel->setupDbh();
        $this->config = $this->configModel->readConfig();
        require 'assets/lang/en_GB.php';
        $this->lang = $msg;
        require CONFIG_DIR. '/model/auth.php';
        $this->authModel = new authModel($this->dbh, $this->config, $this->lang);
        require CONFIG_DIR. '/model/edit.php';
        $this->edit = new editModel($this->dbh, $this->config);
    }

	  /**
     * List items.
	   *
	   */
    function items($statusRequest=null)
    {
        if (!$this->authModel->isLogged()) {
            $this->authModel->accessDenied();
            exit();
        }
        if ($statusRequest == 'offline')  {
            $itemsList = $this->edit->getAllArchived();
            $menu = 'offline';
        }
        else {
            $itemsList = $this->edit->getMergedList();
            $menu = 'update';
        }

        require CONFIG_DIR. '/view/common/header.php';
        require CONFIG_DIR. '/view/edit/items.php';
        require CONFIG_DIR. '/view/common/footer.php';
    }

	  /**
     * Item input form to create and edit content of an item.
	   *
	   */
    function input($itemId = null, $status = null)
    {
        if (!$this->authModel->isLogged()) {
            $this->authModel->accessDenied();
            exit();
        }
        require CONFIG_DIR. '/model/edit_input.php';
        $this->editInput = new editModelInput($this->dbh, $this->config);

        $config = $this->config;
        $postMaxSize = $this->editInput->getMaxFileSize();
        $menu = 'update';

        // set up SESSION data when first time in
        if ($itemId != null ) {
            if ( $itemId == 'create') {
                $_SESSION['item'] = $this->editInput->setupNewItem();
                $menu = 'create';
            }
            else {
                $_SESSION['item'] = $this->editInput->setupExistingItem($itemId, $status);
                if ( ! isset($_SESSION['item']['id']) || $_SESSION['item']['id'] == null) {
                    $this->editModel->reportError('cms/controller/edit.php function: input. Error: invalid item id');
                    exit;
                }
            }
            $item = $_SESSION['item'];
        }
        // update SESSION data and item with incoming posted data
        else {
            $item           = $this->editInput->getPostedItemData();
            $item['tags']   = $this->editInput->getPostedTags();
            $item['images'] = $this->editInput->getImages();
        }

        $tags = $this->editInput->getAllTagList();
        $elements = $this->editInput->getElementList();
        $pages = $this->editInput->getPagesList();

        if ($item['status'] == 'offline') $class = 'text-danger';
        elseif ($item['status'] == 'live') $class = 'text-success';
        else $class = 'text-warning';

        if ( isset( $_POST['currentBgColor']) )
            $_SESSION['currentBgColor'] = $_POST['currentBgColor'];
        elseif (!isset($_SESSION['currentBgColor']))
            $_SESSION['currentBgColor'] = $this->config['image_bg_crop'];

        require CONFIG_DIR. '/view/common/header.php';
        require CONFIG_DIR. '/view/edit/input.php';
        require CONFIG_DIR. '/view/common/footer.php';
    }

	  /**
     * Updates an item with data from input form
	   *
	   */
    function update($modalAction = null)
    {
        if (!$this->authModel->isLogged()) {
            $this->authModel->accessDenied();
            exit();
        }
        // update SESSION data with any incoming posted data
        require CONFIG_DIR. '/model/edit_input.php';
        $this->editInput = new editModelInput($this->dbh, $this->config);
        $_SESSION['item']           = $this->editInput->getPostedItemData();
        $_SESSION['item']['tags']   = $this->editInput->getPostedTags();
        $_SESSION['item']['images'] = $this->editInput->getImages();
        $item = $_SESSION['item'];

        // update database with SESSION data
        require CONFIG_DIR. '/model/edit_update.php';
        $this->editUpdate = new editModelUpdate($this->dbh, $this->config);
        switch ($modalAction) {
            case 'save';
                $this->editUpdate->saveItem();
                break;
            case 'publish';
                $this->editUpdate->publishItem();
                break;
            case 'offline';
                $this->editUpdate->offlineItem();
                break;
        }

        header('location: ' . CONFIG_URL. 'edit/input/'. $_SESSION['item']['id'] .'/'. $_SESSION['item']['status'] );
    }

	  /**
     * Presents form to edit an image
	   *
     */
    function image($cropId = null)
    {
        if (!$this->authModel->isLogged()) {
            $this->authModel->accessDenied();
            exit();
        }
        $_SESSION['cropId'] = $cropId;
        // update SESSION data with any incoming posted data
        require CONFIG_DIR. '/model/edit_input.php';
        $this->editInput = new editModelInput($this->dbh, $this->config);
        $_SESSION['item']            = $this->editInput->getPostedItemData();
        $_SESSION['item']['tags']    = $this->editInput->getPostedTags();
        $_SESSION['item']['images']  = $this->editInput->getImages();

        // prepare image data for view
        if ( empty( $_SESSION['item']['images'][$cropId])) {
            $this->edit->reportError('cms/controller/edit.php function: image. Error: invalid crop id');
            exit;
        }
        $image = $_SESSION['item']['images'][$cropId];
        $image_ratio_panorama =  $this->editInput->convertConfigRatio($this->config['image_ratio_panorama']);
        $image_ratio_landscape =  $this->editInput->convertConfigRatio($this->config['image_ratio_landscape']);
        $image_ratio_portrait =  $this->editInput->convertConfigRatio($this->config['image_ratio_portrait']);
        $image_ratio_square =  $this->editInput->convertConfigRatio($this->config['image_ratio_square']);

        if (empty($_SESSION['currentBgColor']))
            $currentBgColor = $this->config['image_bg_crop'];
        else $currentBgColor = $_SESSION['currentBgColor'];
        $menu = 'update';

        require CONFIG_DIR. '/view/common/header.php';
        require CONFIG_DIR. '/view/edit/image.php';
        require CONFIG_DIR. '/view/common/footer.php';
    }

    /**
     * Manage recreate images
     *
     * Presents page where user can request and monitor recreation of images.
     *
     * Note: This operation uses SSE (Server Sent Events) to monitor progress. The JavaScript in
     *       view/edit/recreateImages fires a call to controller/edit/recreateImages which uses
	   *       model/edit/recreateImages to recreate images and sends events to the browser to
     *       report on progress.
	   */
    function recreateImages()
    {
        if (!$this->authModel->isLogged('admin')) {
            $this->authModel->accessDenied();
            exit();
        }
        $postMaxSize = $this->configModel->getMaxFileSize();
        $config = $this->config = $this->configModel->readConfig();
        $menu = 'admin';

        require CONFIG_DIR. '/view/common/header.php';
        require CONFIG_DIR. '/view/edit/recreateImages.php';
        require CONFIG_DIR. '/view/common/footer.php';
    }

    /**
     *  Recreate images.
	   *
     *  This is the EventSource called by SSE(server sent event). See note on SSE in recreateImages method.
	   */
    function recreateImagesSSE()
    {
        if (!$this->authModel->isLogged('admin')) {
            $this->authModel->accessDenied();
            exit();
        }
        $config = $this->config = $this->configModel->readConfig();
        require CONFIG_DIR. '/model/edit_update.php';
        $this->editUpdate = new editModelUpdate($this->dbh, $this->config);
        $result = $this->editUpdate->recreateImages();
    }
}
