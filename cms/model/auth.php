<?php
/**
 * model class for user authentication
 *
 * @since 1.0.14
 * @author Keith Wheatley
 * @package echocms\auth
 */
namespace echocms;

use PHPMailer;

class authModel
{
    protected $dbh;
    protected $config;
    protected $msg;

    public function __construct(\PDO $dbh, $config, $msg)
    {
        $this->dbh = $dbh;
        $this->config = $config;
        $this->lang = $msg;
    }

    /**
     * User login.
     *
     * Validates user credentials, and either sets up session if successful or records failed attempt.
     *
     * @param string $email email entered by user.
     * @param string $password password entered by user.
     * @param boolean $rememberMe set on if remember me requested by user on login page.
     *
     * @return array $return {
     *   error boolean set on if error with login
     *   message string text result of login attempt
     * }
     */
    function login($email, $password, $rememberMe)
    {
        $return['message'] = $return['error'] = false;
        $uid = $this->getUserId(strtolower($email));
        $user = $this->getUserData($uid);
        if (empty($user) || !$this->validateEmail($email)) {
            $return['message'] = $this->lang['login_failed'];
        }
        elseif ($this->ipBlocked()) {
            $return['message'] = $this->lang['ip_blocked'];
        }
        elseif (isset($user['isactive']) && $user['isactive'] != 1) {
            $return['message'] = $this->lang['account_inactive'];
        }
        elseif (   empty($password)
                || empty($user['password_hash'])
                || !password_verify($password, $user['password_hash'])
                || !$this->validatePassword($password)) {
            $return['message'] = $this->lang['email_password_incorrect'];
        }
        if ($return['message']) {
            $this->addFailedAccessAttempt();
            $return['error'] = true;
        }
        else {
            $return['message'] = $this->lang['logged_in'];
            $this->recordUserVisit($user['id']);
            //$this->createZipImages();
            $this->setSession($user['id'], $rememberMe);
        }

        return $return;
    }

    /**
     * Check if logged in.
     *
     * Checks if a user is logged in, or logs user in if remember me cookie is set.
     *
     * @param string $role used to check if admin role is logged in.
     *
     * @return boolean set on if user is logged in.
     */
    function isLogged($role=null) {
        if (   ($role == 'admin' && !empty($_SESSION['isLoggedAdmin']))
            || ($role != 'admin' && !empty($_SESSION['isLogged']))) {

            return true;
        }
        if (isset($_COOKIE[$this->config['cookie_name']])) {
            $uid = $this->checkSession($_COOKIE[$this->config['cookie_name']]);
            if ($this->setSession($uid, true)) {
                if (   ($role == 'admin' && !empty($_SESSION['isLoggedAdmin']))
                    || ($role != 'admin' && !empty($_SESSION['isLogged']))) {

                    return true;
                }
            }
        }

        return false;
    }

    /**
     * Log out.
     *
     * Ends session and removes remember me cookie.
     *
     * @return array $return {
     *   error boolean set to off.
     *   message string text result to confirm logout.
     * }
     */
    function logout()
    {
        if (isset($_COOKIE[$this->config['cookie_name']])) {
            setcookie($this->config['cookie_name'], '', time()-3600, '/');
            $cookieValue = $_COOKIE[$this->config['cookie_name']];
            $selector = substr($cookieValue, 64, 12);
            $query = $this->dbh->prepare('DELETE FROM sessions WHERE selector = ?');
            $query->execute(array($selector));
        }
        if (isset($_COOKIE[session_name()])) {
            $session_name = session_name();
            setcookie($session_name, ' ', time()-3600, '/');
        }
        $_SESSION = array();
        session_destroy();
        $result['error'] = null;
        $result['message'] = $this->lang['logged_out'];

        return $result;
    }

    /**
     * Get user data.
     *
     * Retrieves user data, including session and requests data, for all users (excluding password hash).
     *
     * @return array $users
     */
    function getUsers()
    {
        $stmt = $this->dbh->prepare(
        'SELECT users.*, sessions.ip FROM users
         LEFT JOIN sessions ON users.id=sessions.uid');
        $stmt->execute();
        $users = array();
        while ($row = $stmt->fetch(\PDO::FETCH_ASSOC)) {
            unset($row['password_hash']);
            $users[] = $row;
        }

        return $users;
    }

    /**
     * Set user account activation.
     *
     * Sets a user account to requested value of active or inactive and, if setting to inactive, deletes user's session.
     *
     * @param integer $id user id of account to be actvated/deactivated.
     * @param string $isactiveValue options '0' or '1' to set database value.
     *
     * @return void
     */
    function setActivation($id, $isactiveValue)
    {
        $query = $this->dbh->prepare('UPDATE users SET isactive = ? WHERE id = ?');
        $query->execute(array($isactiveValue, $id));
        $query = $this->dbh->prepare('SELECT email FROM users WHERE id = ?');
        $query->execute(array($id));
        $user = $query->fetch(\PDO::FETCH_ASSOC);
        if ($isactiveValue == '0')
            $this->deleteExistingSessions($id);
    }

    /**
     * Change password.
     *
     * Verifies current password, validates new/repeated password, updates database with new password hash, and sends confirmation email.
     *
     * @param integer $uid userid of account to be changed
     * @param string $currpass current password
     * @param string $newpass new password
     * @param string $repeatpass repeat of new password
     *
     * @return array $return {
     *   error boolean set on if error encountered
     *   message string text result
     * }
     */
    function changePassword($uid, $currpass, $newpass, $repeatpass)
    {
        $return['error'] = $return['message'] = $user = false;
        if (isset($uid))
            $user = $this->getUserData($uid);

        if (    empty($user)
            ||  empty($currpass)
            ||  !password_verify($currpass, $user['password_hash'])) {
            $return['message'] = $this->lang['password_incorrect'];
        }
        elseif (empty($newpass)
            ||  empty($repeatpass)
            ||  !$this->validatePassword($newpass) ){
            $return['message'] = $this->lang['password_incorrect_new'];
        }
        else if ($newpass !== $repeatpass) {
            $return['message'] = $this->lang['password_nomatch'];
        }
        if ($return['message']) {
            $return['error'] = true;
        }
        else {
            $newpass_hash =  password_hash($newpass, PASSWORD_BCRYPT, ['cost' => $this->config['bcrypt_cost']]);
            $query = $this->dbh->prepare('UPDATE users SET password_hash = ? WHERE id = ?');
            $query->execute(array($newpass_hash, $uid));
            $return['error'] = false;
            $return['message'] = $this->lang['password_changed'];
            if ($this->config['email_notifications_on']) {
                $subject = $this->lang['password_changed'] .' on ' . $this->config['site_name'];
                $body = sprintf($this->lang['password_changed_email_notification'], $user['email'], $this->config['admin_email'] );
                $altBody = sprintf($this->lang['password_changed_email_notification'], $user['email'], $this->config['admin_email'] );
                $this->sendEmail($user['email'], $subject, $body, $altBody);
            }
        }

        return $return;
    }

    /**
     * Change email address.
     *
     * Verifies current password, validates new email, updates database and sends confirmation to old email.
     *
     * @param integer $uid userid of account to be changed
     * @param string $currpass current password
     * @param string $newpass new password
     * @param string $repeatpass repeat of new password
     *
     * @return array $return {
     *   error boolean set on if error encountered
     *   message string text result
     * }
     */
    function changeEmail($uid, $newEmail, $password)
    {
        $return['message'] = $return['error'] = false;
        $user = $this->getUserData($uid);

        if (    empty($user)
            || !password_verify($password, $user['password_hash'])) {
            $return['message'] = $this->lang['password_incorrect'];
        }
        elseif (   empty($newEmail)
                || !$this->validateEmail($newEmail)
                || $this->isEmailTaken($newEmail)) {
            $return['message'] = $this->lang['email_invalid'];
        }
        elseif ($newEmail == $user['email']) {
            $return['message'] = $this->lang['newemail_match'];
        }

        if ($return['message']) {
            $return['error'] = true;
        }
        else {
            // update db with new password
            $query = $this->dbh->prepare('UPDATE users SET email = ? WHERE id = ?');
            $query->execute(array($newEmail, $uid));
            $return['error'] = false;
            $return['message'] = $this->lang['email_changed'];
            // send email notification to old email
            if ($this->config['email_notifications_on']) {
                $subject = $this->lang['email_changed'] .' on ' . $this->config['site_name'];
                $body = sprintf($this->lang['email_changed_email_notification'], $newEmail, $this->config['admin_email'] );
                $altBody = sprintf($this->lang['email_changed_email_notification'], $newEmail, $this->config['admin_email'] );
                $this->sendEmail($user['email'], $subject, $body, $altBody);
            }
            $user['email'] = $newEmail;
            // update config if Admin
            if ($_SESSION['isLoggedAdmin']) {
                $query = $this->dbh->prepare('UPDATE config SET value = ? WHERE setting = "admin_email"');
                $query->execute(array($newEmail));
            }
            $_SESSION['isLoggedEmail'] = $newEmail;
        }

        return $return;
    }

    /**
     * Register a new user.
     *
     * Validates email and new/repeat password, either records failed attempt or adds (not active) user to database and sends notification email to admin.
     *
     * @param string $email email address
     * @param string $password password
     * @param string $repeatpassword repeat of password
     *
     * @return array $return {
     *   error boolean set on if error encountered
     *   message string text result
     * }
     */
    function register($email, $password, $repeatpassword)
    {
        $return['message'] = $emailSent = false;
        if ($this->ipBlocked()) {
            $return['message'] = $this->lang['ip_blocked'];
        }
        elseif (isset($user['isactive']) && $user['isactive'] != 1) {
            $return['message'] = $this->lang['account_inactive'];
        }
        elseif (   empty($email)
                || !$this->validateEmail($email)
                || $this->isEmailTaken($email)) {
            $return['message'] = $this->lang['email_password_invalid'];
        }
        elseif (   empty($password)
                || !$this->validatePassword($password)) {
            $return['message'] = $this->lang['email_password_invalid'];
        }
        elseif (   empty($repeatpassword)
                || $password !== $repeatpassword) {
            $return['message'] = $this->lang['password_nomatch'];
        }
        if ($return['message']) {
            $return['error'] = true;
            $this->addFailedAccessAttempt();
        }
        else {
            $uid = $this->addUser($email, $password);
            if (!$uid) {
                $return['message'] = $this->lang['registration_not_successful'];
                return $return;
            }
            $this->recordUserVisit($uid);
            if ($this->config['email_notifications_on']) {
                $subject = sprintf($this->lang['email_activation_subject'], $this->config['site_name']);
                $body = sprintf($this->lang['email_activation_body'], $email);
                $altBody = sprintf($this->lang['email_activation_altbody'], $email);
                $emailSent = $this->sendEmail($this->config['admin_email'], $subject, $body, $altBody);
            }
            if ($emailSent) {
                $return['message'] = $this->lang['account_awaiting_approval'];
            }
            else
            {
                $return['message'] = sprintf($this->lang['account_awaiting_approval_no_email'], $this->config['admin_email']);
            }
            $return['error'] = false;
        }

        return $return;
    }

    /**
     * Request password reset ('forgotten password').
     *
     * Verifies email address, either records a failed access attempt or generates a password reset key, adds to database and sends notification email.
     *
     * @param string $email address of request
     *
     * @return array $return {
     *   error boolean set on if error encountered
     *   message string text result
     * }
     */
    function requestPasswordResetKey($email)
    {
        $return['error'] = $emailSent = false;

        if ($this->ipBlocked()) {
            $return['message'] = $this->lang['ip_blocked'];

            return $return;
        }
        if (!empty($email)){
            $uid = $this->getUserId($email);
        }
        if (    empty($email)
            ||  empty($uid)
            || !$this->validateEmail($email)
            || !$this->getUserId($email)) {
            $return['message'] = $this->lang['email_incorrect'];
            $return['error'] = true;
            $this->addFailedAccessAttempt();

            return $return;
        }
        $user = $this->getUserData($uid);
        if (empty($user['isactive'])) {
            $return['message'] = $this->lang['account_inactive'];

            return $return;
        }
        $query = $this->dbh->prepare('DELETE FROM requests WHERE uid = ?');
        $query->execute(array($uid));
        $key = $this->getRandomKey(20);
        $expire = date('Y-m-d H:i:s', strtotime('+'.$this->config['password_reset_minutes'].' minutes'));
        $expire_display = date('j M Y H:i', strtotime('+'.$this->config['password_reset_minutes'].' minutes'));
        $query = $this->dbh->prepare('INSERT INTO requests (uid, rkey, expire, ip) VALUES (?, ?, ?, ?)');
        $query->execute(array($uid, $key, $expire, $this->getIp()));
        if ($this->config['email_notifications_on']) {
            $subject = sprintf($this->lang['email_reset_subject'], $this->config['site_name']);
            $body = sprintf($this->lang['email_reset_body'], CONFIG_URL, 'resetPass', $key, $this->config['password_reset_minutes'], $expire_display);
            $altBody = sprintf($this->lang['email_reset_altbody'], CONFIG_URL, 'resetPass', $key, $this->config['password_reset_minutes'], $expire_display);
            $emailSent = $this->sendEmail($email, $subject, $body, $altBody);
        }
        if ($emailSent) {
            $return['message'] = $this->lang['reset_requested'];
        }
        else {
            $return['message'] = sprintf($this->lang['reset_requested_no_email'], $this->config['admin_email']);
        }

        return $return;
    }

    /**
     * Record user visit.
     *
     * Updates database with user's IP address and date of last visit.
     *
     * @param integer $id user id
     *
     * @return void
     */
    private function recordUserVisit($id)
    {
        $last_ip = $this->getIp();
        $last_dt = date('Y-m-d H:i:s');
        $query = $this->dbh->prepare('UPDATE users SET last_ip = ?, last_dt = ? WHERE id = ?');
        $query->execute(array($last_ip, $last_dt, $id));
    }

    /**
     * Send email.
     *
     * Sends email using PHPMailer ref: https://github.com/PHPMailer/PHPMailer
     *
     * @param string $email address
     * @param string $subject of email
     * @param string $body of email
     * @param string $altBody of email
     *
     * @return boolean set on if successful or false if error
     */
    protected function sendEmail($email, $subject, $body, $altBody)
    {
        if (!$this->config['email_notifications_on']) {
            error_log('cms/model/auth.php sendEmail attempted but configuration for email notifications off: '. print_r($mail, true));
            return false;
        }
        require 'assets/phpmailer/phpmailer/PHPMailerAutoload.php';
        $mail = new PHPMailer;
        $mail->CharSet = $this->config['mail_charset'];
        if ($this->config['smtp']) {
            $mail->isSMTP();

            //$mail->SMTPDebug = 3;   // info from remote SMTP server

            $mail->Host = $this->config['smtp_host'];
            $mail->SMTPAuth = $this->config['smtp_auth'];
            if (!is_null($this->config['smtp_auth'])) {
                $mail->Username = $this->config['smtp_username'];
                $mail->Password = $this->config['smtp_password'];
            }
            $mail->Port = $this->config['smtp_port'];
            if (!is_null($this->config['smtp_security'])) {
                $mail->SMTPSecure = $this->config['smtp_security'];
                if ($this->config['smtp_security'] == 'ssl') {
                    // see https://github.com/PHPMailer/PHPMailer/wiki/Troubleshooting#php-56-certificate-verification-failure
                    $mail->SMTPOptions = array('ssl' => array('verify_peer' => false, 'verify_peer_name' => false, 'allow_self_signed' => true));
                }
            }
        }
        $mail->From = $this->config['site_email'];
        $mail->FromName = $this->config['site_name'];
        $mail->addAddress($email);
        $mail->isHTML(true);
        $mail->Subject = $subject;
        $mail->Body = $body;
        $mail->AltBody = $altBody;
        if (!$mail->send()) {
            error_log('cms/model/auth.php sendEmail failed. PHPMail error: '. print_r( $mail->ErrorInfo, true));
            error_log('cms/model/auth.php sendEmail failed. PHPMail mail object: '. print_r( $mail, true));
            return false;
        }


        return true;
    }

    /**
     * Reset password.
     *
     * Verifies password reset key (sent to user by email see method:requestPasswordResetKey), verifies password/repeat password, then either records a failed access attempt or updates database with new password hash.
     *
     * @param unknown $key password reset key
     * @param unknown $password new password
     * @param unknown $repeatpassword repeat of new password
     *
     * @return array $return {
     *   error boolean set on if error encountered
     *   message string text result
     * }
     */
    function resetPass($key, $password, $repeatpassword)
    {
        $return['message'] = $return['error'] = $user = $resetRequest = false;
        if (!empty($key)) {
            $resetRequest = $this->getResetRequest($key);
        }
        if (!empty($resetRequest)) {
            $user = $this->getUserData($resetRequest['uid']);
            $expiredate = strtotime($resetRequest['expire']);
            $currentdate = strtotime(date('Y-m-d H:i:s'));
        }
        if ($this->ipBlocked()) {
            $return['message'] = $this->lang['ip_blocked'];
        }
        elseif (    empty($user)
                 || empty($resetRequest)
                 || strlen($key) != 20) {
            $return['message'] = $this->lang['resetkey_invalid'];
        }
        elseif ($this->getIp() != $resetRequest['ip']) {
            $return['message'] = $this->lang['ip_not_recognised'];
        }
        elseif ($currentdate > $expiredate) {
            $return['message'] = $this->lang['resetkey_expired'];
        }
        elseif (!$this->validatePassword($password)){
            $return['message'] = $this->lang['password_incorrect'];
        }
        elseif ($password !== $repeatpassword) {
            $return['message'] = $this->lang['newpassword_nomatch'];
        }
        if ($return['message']) {
            $return['error'] = true;
            $this->addFailedAccessAttempt();

            return $return;
        }
        $password_hash = password_hash($password, PASSWORD_BCRYPT, ['cost' => $this->config['bcrypt_cost']]);
        $query = $this->dbh->prepare('UPDATE users SET password_hash = ? WHERE id = ?');
        $query->execute(array($password_hash, $resetRequest['uid']));
        $query = $this->dbh->prepare('DELETE FROM requests WHERE uid = ?');
        $query->execute(array($user['id']));
        $return['error'] = false;
        $return['message'] = $this->lang['password_reset'];

        return $return;
    }

    /**
     * Get password reset request data.
     *
     * @param string $key reset request key
     *
     * @return string | boolean $resetRequest returns user reset request data or false if no request.
     */
    private function getResetRequest($key)
    {
        $query = $this->dbh->prepare('SELECT id, uid, expire, ip FROM requests WHERE rkey = ?');
        $query->execute(array($key));
        if ($query->rowCount() === 0) {
            $resetRequest = false;
        }
        else {
            $resetRequest = $query->fetch(\PDO::FETCH_ASSOC);
        }

        return $resetRequest;
    }

    /**
     * Gets user id for a given email address.
     *
     * @param string $email
     *
     * @return boolean
     */
    private function getUserId($email)
    {
        $query = $this->dbh->prepare('SELECT id FROM users WHERE email = ?');
        $query->execute(array($email));
        if ($query->rowCount() == 0) {
            return false;
        }
        $return = $query->fetch(\PDO::FETCH_ASSOC)['id'];

        return $return;
    }

    /**
     * Set session.
     *
     * Sets up user session parameters and sets cookie for a specified user id.
     *    ref: https://paragonie.com/blog/2015/04/secure-authentication-php-with-long-term-persistence
     *
     * @param integer $uid user id
     * @param boolean $rememberMe set on if remember me cookie is to be set.
     *
     * @return boolean returns true if session exists for user id, otherwise returns false
     */
    private function setSession($uid, $rememberMe)
    {
        $user = $this->getUserData($uid);
        if (empty($user)) {
            return false;
        }
        $this->deleteExistingSessions($uid);
        if ($rememberMe) {
            // set cookie
            $expire_timestamp = strtotime('+'.$this->config['remember_me_days'].' days');
            $validator = $this->getRandomKey(64);
            $selector = $this->getRandomKey(12);
            $cookieValue = $validator . $selector;
            setcookie($this->config['cookie_name'], $cookieValue, $expire_timestamp, '/');
            // record to db
            $validator_hash = hash('sha256', $validator);
            $agent = $_SERVER['HTTP_USER_AGENT'];
            $expire_datetime = date('Y-m-d H:i:s', $expire_timestamp);
            $ip = $this->getIp();
            $query = $this->dbh->prepare('INSERT INTO sessions (selector, validator, uid, expiredate, ip, agent) VALUES (?, ?, ?, ?, ?, ?)');
            $query->execute(array($selector, $validator_hash, $uid, $expire_datetime, $ip, $agent));
        }
        // set session data
        $_SESSION['isLogged'] = true;
        $_SESSION['isLoggedEmail'] = $user['email'];
        $_SESSION['isLoggedUID'] = $user['id'];
        if ( $user['email'] === $this->config['admin_email']){
             $_SESSION['isLoggedAdmin'] = true;
        }
        else {
            $_SESSION['isLoggedAdmin'] = false;
        }

        return true;
    }

    /**
     * Check Session.
     *
     * Checks if remember-me cookie values are valid against database values.
     *    ref: https://paragonie.com/blog/2015/04/secure-authentication-php-with-long-term-persistence
     *
     * @param string $cookieValue
     *
     * @return integer $session['uid'] user id from sessions table
     */
    private function checkSession($cookieValue)
    {
        if ($this->ipBlocked()) {

            return false;
        }
        $cookie_selector = substr($cookieValue, 64, 12);
        $cookie_validator = substr($cookieValue, 0, 64);
        $cookie_validator_hash = hash('sha256', $cookie_validator);
        // use cookie selector to get sessions data from db
        $query = $this->dbh->prepare('SELECT id, validator, uid, expiredate, ip FROM sessions WHERE selector = ?');
        $query->execute(array($cookie_selector));
        if ($query->rowCount() == 0) {

            return false;
        }
        $session = $query->fetch(\PDO::FETCH_ASSOC);

        // alternative hash_equals function for PHP < 5.6 (ref: http://php.net/manual/en/function.hash-equals.php)
        if(!function_exists('hash_equals')) {
            function hash_equals($a, $b) {
                $ret = strlen($a) ^ strlen($b);
                $ret |= array_sum(unpack("C*", $a^$b));

                return !$ret;
            }
        }

        // check hash of cookie validator against db sessions validator
        if (!hash_equals($cookie_validator_hash, $session['validator'])) {

            return false;
        }
        // check expired date in db sessions
        $expiredate = strtotime($session['expiredate']);
        $currentdate = strtotime(date('Y-m-d H:i:s'));
        if ($currentdate > $expiredate) {
            $this->deleteExistingSessions($session['uid']);

            return false;
        }
        // check current IP same as IP when cookie set
        //if ($this->getIp() != $session['ip']) {
        //    return false;
        //}

        return $session['uid'];
    }



    /**
     * Delete existing sessions.
     *
     * Delete existing sessions from database for a given user id.
     *
     *
     * @param integer $uid user id
     *
     * @return boolean set to true if session deleted otherwise set to false
     */
    private function deleteExistingSessions($uid)
    {
        $query = $this->dbh->prepare('DELETE FROM sessions WHERE uid = ?');
        $query->execute(array($uid));

        return $query->rowCount() == 1;
    }

    /**
     * Checks if an email is already in use.
     *
     * @param string $email
     *
     * @return boolean
     */
    private function isEmailTaken($email)
    {
        $query = $this->dbh->prepare('SELECT count(*) FROM users WHERE email = ?');
        $query->execute(array($email));
        if ($query->fetchColumn() == 0) {

            return false;
        }

        return true;
    }

    /**
     * Add new user.
     *
     *
     * Adds a new user to database, with user set to inactive.
     *
     * @param string $email
     * @param string $password
     *
     * @return integer | boolean $uid user id of new user or false if insert failed
     */
    private function addUser($email, $password)
    {
        $email = htmlentities(strtolower($email));
        $isactive = 0;
        $password_hash =  password_hash($password, PASSWORD_BCRYPT, ['cost' => $this->config['bcrypt_cost']]);
        $query = $this->dbh->prepare('INSERT INTO users (email, password_hash, isactive) VALUES (?, ?, ?)');
        $query->execute(array($email, $password_hash, $isactive));
        if (!$query) {

            return false;
        }
        $uid = $this->dbh->lastInsertId();

        return $uid;
    }

    /**
     * Get user data for a given user id.
     *
     *
     * @param unknown $id Description
     *
     * @return boolean | array $data array of user data otherwise false if no user data
     */
    private function getUserData($id)
    {
        $query = $this->dbh->prepare('SELECT email, password_hash, isactive FROM users WHERE id = ?');
        $query->execute(array($id));
        if ($query->rowCount() == 0) {

            return false;
        }
        $data = $query->fetch(\PDO::FETCH_ASSOC);
        if (!$data) {

            return false;
        }
        $data['id'] = $id;

        return $data;
    }

    /**
     * Validate password.
     *
     *
     * note: password strength checks are done in browser by zxcvbn.js
     * @todo add validation here to confirm javascript strength checks.
     *
     * @param string $password
     *
     * @return boolean
     */
    private function validatePassword($password) {

        return true;
    }

    /**
     * Validate email.
     *
     *
     * @param string $email
     *
     * @return boolean
     */
    private function validateEmail($email) {
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {

            return false;
        }
        else return true;
    }

    /**
     * Block IP.
     *
     *
     * Determines if current IP address should be blocked if failed access attempts exceed maximum.
     *
     * @return boolean
     */
    private function ipBlocked()
    {
        $ip = $this->getIp();
        $query = $this->dbh->prepare('DELETE FROM attempts WHERE ip = ? AND expiredate < NOW()');
        $query->execute(array($ip));
        $query = $this->dbh->prepare('SELECT count(*) FROM attempts WHERE ip = ?');
        $query->execute(array($ip));
        $attempts = $query->fetchColumn();
        if ($attempts < intval($this->config['ip_ban_attempts'])) {

            return false;
        }

        return true;
    }

    /**
     * Add failed access attempt.
     *
     * Adds a failed access attempt to database for the current IP address with an expiry datetime equal to period of IP ban.
     *
     * @return void
     */
    private function addFailedAccessAttempt()
    {
        $ip = $this->getIp();
        $date = date('Y-m-d H:i:s');
        $expiredate = date('Y-m-d H:i:s', strtotime('+'.$this->config['ip_ban_minutes'].' minutes'));
        $query = $this->dbh->prepare('INSERT INTO attempts (ip, date, expiredate) VALUES (?, ?, ?)');
        $query->execute(array($ip, $date, $expiredate));
    }

    /**
     * Delete failed access attempts.
     *
     * Deletes all failed access attempts from database for the current IP address.
     *
     * @return void
     */
    private function deleteFailedAccessAttempts()
    {
        $ip = $this->getIp();
        $query = $this->dbh->prepare('DELETE FROM attempts WHERE ip = ?');
        $query->execute(array($ip));
    }

    /**
     * Get access records.
     *
     * Gets all access records from both failed attempts and user records
     * Sequenced in batches by IP and date sequence, records within batches sequenced by latest date.
     *
     * @return array $attempts
     */
    function getAccessRecords()
    {
        $attempts = array();

        $stmt = $this->dbh->prepare(
        'SELECT ip, date, expiredate FROM attempts');
        $stmt->execute();
        while ($row = $stmt->fetch(\PDO::FETCH_ASSOC)) {
            $row['email'] = 'failed access attempt';
            $attempts[] = $row;
        }

        $stmt = $this->dbh->prepare(
        'SELECT email, last_dt, last_ip FROM users');
        $stmt->execute();
        while ($row = $stmt->fetch(\PDO::FETCH_ASSOC)) {
            $row['date'] = $row['last_dt'];
            $row['ip'] = $row['last_ip'];
            $attempts[] = $row;
        }

        // sort access records array by date
        $date = array();
		    if (isset ($attempts) &&  count($attempts) > 1) {
		        foreach ($attempts as $key => $row) {
                $date[$key]  = $row['date'];
			      }
			      array_multisort ($date, SORT_DESC, $attempts);
		    }
        // create array with sequenced access records
        $sortedAttempts = $used = array();
        $i = 0;
        while ($i < count($attempts)) {
            if (empty($used[$i])) {
                $sortedAttempts[] = $attempts[$i];
                $used[$i] = true;
            }
            $j = $i+1;
            while ($j < count($attempts)) {
                if (empty($used[$j]) && ($attempts[$i]['ip'] == $attempts[$j]['ip'])) {
                    $sortedAttempts[] = $attempts[$j];
                    $used[$j] = true;
                }
                $j++;
            }
            $i++;
        }

        return $sortedAttempts;
    }

    /**
     * Get a random key.
     *
     * Generate a random string of a specified length.
     *   ref https://paragonie.com/blog/2015/04/secure-authentication-php-with-long-term-persistence
     * @todo - check for php 7 and if so, use random_bytes($length)
     *
     * @param integer $length of string to be generated
     *
     * @return string $key
     */
    private function getRandomKey($length = 20)
    {
        $chars = 'A1B2C3D4E5F6G7H8I9J0K1L2M3N4O5P6Q7R8S9T0U1V2W3X4Y5Z6a1b2c3d4e5f6g7h8i9j0k1l2m3n4o5p6q7r8s9t0u1v2w3x4y5z6';
        $key = '';
        for ($i = 0; $i < $length; $i++) {
            $key .= $chars[mt_rand(0, strlen($chars) - 1)];
        }

        return $key;
    }

    /**
     * Get IP address.
     *
     *
     * Gets current IP address.
     *   ref: https://wwphp-fb.github.io/faq/how-to-get-clientsip-in-php/
     *
     * @return string $ip current IP address
     */
    function getIp()
    {
        if (isset($_SERVER['HTTP_CLIENT_IP']) && array_key_exists('HTTP_CLIENT_IP', $_SERVER)) {
            $ip = $_SERVER['HTTP_CLIENT_IP'];
        }
        elseif (isset($_SERVER['HTTP_X_FORWARDED_FOR']) && array_key_exists('HTTP_X_FORWARDED_FOR', $_SERVER)) {
            $ips = explode(',', $_SERVER['HTTP_X_FORWARDED_FOR']);
            $ips = array_map('trim', $ips);
            $ip = $ips[0];
        }
        else {
            $ip = $_SERVER['REMOTE_ADDR'];
        }
        $ip = filter_var($ip, FILTER_VALIDATE_IP);

        return $ip;
    }

    /**
     * Report a system error.
     *
     * Reports error to error_log then sets header location to error notification page and exits script.
     *
     * @param string $message
     */
	  function reportError($message)
  	{
          error_log ('cms/model/auth.php  reportError error message: ' . print_r($message, true) );
          error_log ('IP Address is: ' . $this->getIp());
          //error_log ('cms/model/auth.php  reportError session data: ' . print_r($_SESSION, true) );
          header('location: ' . CONFIG_URL. 'error/notify' );
          exit();
  	}

    /**
     * Access denied.
     *
     * Log user out, drop session and present login page when an attempt is made to access a secure function and user is not logged in
     *
     * @param string $message
     */
  	function accessDenied()
  	{
          $this->logout();
          $this->addFailedAccessAttempt();
          header('location: ' . CONFIG_URL. 'auth/index' );
          exit();
  	}
}
