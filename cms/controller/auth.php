<?php
/**
 * Controller class for user authentication
 *
 *
 * @since 1.0.0
 * @author Keith Wheatley
 * @package echocms\auth
 */
namespace echocms;

class auth
{
    protected $dbh;
    protected $config;
    public $app;

    function __construct()
    {
        require 'model/config.php';
        $this->configModel = new configModel();
        $this->dbh = $this->configModel->setupDbh();
        $this->config = $this->configModel->readConfig();
        require 'assets/lang/en_GB.php';
        $this->lang = $msg;
        require 'model/auth.php';
        $this->authModel = new authModel($this->dbh, $this->config, $this->lang);
    }

    /**
     * Entry page - presents login form or notification of logged in state.
     *
     */
    function index()
    {
        $email = $password = $repeatPassword = $result['error'] = $result['message'] = null;
        if (!$this->authModel->isLogged()) {
            $rememberMe = empty($_POST['rememberMe']) ? '0' : '1';

            if (isset($_POST["email"])) {
                $email = $_POST["email"];
            }
            if (isset($_POST["password"])) {
                $password = $_POST["password"];
            }
            if ($email || $password) {
                $result = $this->authModel->login($email, $password, $rememberMe) ;
            }
        }
        $menu = "user";

        require_once CONFIG_DIR. '/view/common/header.php';
        if ($this->authModel->isLogged()) {
            $result['message'] = $this->lang['logged_in'];
            require_once CONFIG_DIR. '/view/common/notify.php';
        }
        else
            require_once CONFIG_DIR. '/view/auth/login.php';

        require_once CONFIG_DIR. '/view/common/footer.php';
    }

    /**
     * Logout.
     *
     */
    function logout()
    {
        $rememberMe = null;
        $result = $this->authModel->logout();
        $menu = "user";

        require_once CONFIG_DIR. '/view/common/header.php';
        require_once CONFIG_DIR. '/view/common/notify.php';
        require_once CONFIG_DIR. '/view/common/footer.php';
    }

    /**
     * Register User & send "awaiting approval" email to admin.
     *
     */
    function registerUser()
    {
        $_SESSION["view"] = "auth";
        $email = $newPassword = $repeatPassword = $role = $result = null;
        if (isset($_POST["email"])) {
            $email = $_POST["email"];
        }
        if (isset($_POST["newPassword"]))
            $newPassword = $_POST["newPassword"];
        if (isset($_POST["repeatPassword"]))
            $repeatPassword = $_POST["repeatPassword"];
        if ($email || $newPassword || $repeatPassword) {
            $result = $this->authModel->register($email, $newPassword, $repeatPassword);
            $email = null;
        }
        $menu = "register";

        require CONFIG_DIR. '/view/common/header.php';
        if (!isset($result['error']) || $result['error'])
             require CONFIG_DIR. '/view/auth/registerUser.php';
        else require CONFIG_DIR. '/view/common/notify.php';
        require CONFIG_DIR. '/view/common/footer.php';
    }


    /**
     * Request a reset key to be emailed (Forgotten password).
     *
     */
    function requestReset()
    {
        $email = $result = null;
        if (isset($_POST["email"]))
            $email = $_POST["email"];
        if ($email)
            $result = $this->authModel->requestPasswordResetKey($email);
        $menu = "user";

        require CONFIG_DIR. '/view/common/header.php';
        if (!isset($result['error']) || $result['error'])
             require CONFIG_DIR. '/view/auth/requestReset.php';
        else require CONFIG_DIR. '/view/common/notify.php';
        require CONFIG_DIR. '/view/common/footer.php';
    }

    /**
     * Reset password using reset key sent by email
     *
     */
    function resetPass()
    {
        $key = $newPassword = $repeatPassword = $result = null;
        if (isset($_POST["key"]))
            $key = $_POST["key"];
        if (isset($_POST["newPassword"]))
            $newPassword = $_POST["newPassword"];
        if (isset($_POST["repeatPassword"]))
            $repeatPassword = $_POST["repeatPassword"];
        if ($key || $newPassword || $repeatPassword)
            $result = $this->authModel->resetPass($key, $newPassword, $repeatPassword);
        $menu = "user";

        require CONFIG_DIR. '/view/common/header.php';
        if (!isset($result['error']) || $result['error'])
             require CONFIG_DIR. '/view/auth/resetPass.php';
        else require CONFIG_DIR. '/view/common/notify.php';
        require CONFIG_DIR. '/view/common/footer.php';
    }

    /**
     * Change email address by logged in user.
     *
     */
    function changeEmail()
    {
        if (!$this->authModel->isLogged()) {
            $this->authModel->accessDenied();
            exit();
        }
        $newEmail = $password = $result = null;
        if (isset($_POST["newEmail"]))
            $newEmail = $_POST["newEmail"];
        if (isset($_POST["password"]))
            $password = $_POST["password"];
        if ($password || $newEmail)
            $result = $this->authModel->changeEmail($_SESSION["isLoggedUID"], $newEmail, $password);
        $menu = "user";

        require CONFIG_DIR. '/view/common/header.php';
        if (!isset($result['error']) || $result['error'])
             require CONFIG_DIR. '/view/auth/changeEmail.php';
        else require CONFIG_DIR. '/view/common/notify.php';
        require CONFIG_DIR. '/view/common/footer.php';
    }

    /**
     * Change Password.
     *
     */
    function changePassword()
    {
        if (!$this->authModel->isLogged()) {
            $this->authModel->accessDenied();
            exit();
        }
        $password = $newPassword = $repeatPassword = $result = null;
        if (isset($_POST["password"]))
            $password = $_POST["password"];
        if (isset($_POST["newPassword"]))
            $newPassword = $_POST["newPassword"];
        if (isset($_POST["repeatPassword"]))
            $repeatPassword = $_POST["repeatPassword"];
        if ($password || $newPassword || $repeatPassword)
            $result = $this->authModel->changePassword($_SESSION["isLoggedUID"], $password, $newPassword, $repeatPassword);
        $menu = "user";

        require CONFIG_DIR. '/view/common/header.php';
        if (!isset($result['error']) || $result['error'])
             require CONFIG_DIR. '/view/auth/changePassword.php';
        else require CONFIG_DIR. '/view/common/notify.php';
        require CONFIG_DIR. '/view/common/footer.php';
    }

    /**
     * Manage users - list users and activate/deactivate users.
     *
     */
    function manageUsers($action=null, $id=null)
    {
        if (!$this->authModel->isLogged('admin')) {
            $this->authModel->accessDenied();
            exit();
        }
        if (($action == "0" || $action == "1") &&  ctype_digit($id)) {
            $result = $this->authModel->setActivation($id, $action);
        }
        $users = $this->authModel->getUsers();
        $menu = "admin";

        require CONFIG_DIR. '/view/common/header.php';
        require CONFIG_DIR. '/view/auth/manageUsers.php';
        require CONFIG_DIR. '/view/common/footer.php';
    }

    /**
     * Manage access
     *
     */
    function manageAccess($action=null, $id=null)
    {
        if (!$this->authModel->isLogged('admin')) {
            $this->authModel->accessDenied();
            exit();
        }
        $attempts = $this->authModel->getAccessRecords();
        $menu = "admin";

        require CONFIG_DIR. '/view/common/header.php';
        require CONFIG_DIR. '/view/auth/manageAccess.php';
        require CONFIG_DIR. '/view/common/footer.php';
    }
}
