<?php

/**
 * Zend Framework
 *
 * LICENSE
 *
 * This source file is subject to the new BSD license that is bundled
 * with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://framework.zend.com/license/new-bsd
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@zend.com so we can send you a copy immediately.
 *
 * @category   Zend
 * @package    Zend_OpenId
 * @subpackage Zend_OpenId_Provider
 * @copyright  Copyright (c) 2005-2008 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @version    $Id$
 */

/**
 * @see Zend_OpenId_Provider_Storage
 */
require_once "Zend/OpenId/Provider/Storage.php";

/**
 * External storage implemmentation using serialized files
 *
 * @category   Zend
 * @package    Zend_OpenId
 * @subpackage Zend_OpenId_Provider
 * @copyright  Copyright (c) 2005-2008 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class Zend_OpenId_Provider_Storage_File extends Zend_OpenId_Provider_Storage
{

    /**
     * Directory name to store data files in
     *
     * @var string $_dir
     */
    private $_dir;

    /**
     * Constructs storage object and creates storage directory
     *
     * @param string $dir directory name to store data files in
     * @throws Zend_OpenId_Exception
     */
    public function __construct($dir = null)
    {
        if (is_null($dir)) {
            $tmp = getenv('TMP');
            if (empty($tmp)) {
                $tmp = getenv('TEMP');
                if (empty($tmp)) {
                    $tmp = "/tmp";
                }
            }
            $user = get_current_user();
            if (is_string($user) && !empty($user)) {
            	$tmp .= '/' . $user;
			}
            $dir = $tmp . '/openid/provider';
        }
        $this->_dir = $dir;
        if (!is_dir($this->_dir)) {
            if (!@mkdir($this->_dir, 0700, 1)) {
                throw new Zend_OpenId_Exception(
                    "Cannot access storage directory $dir",
                    Zend_OpenId_Exception::ERROR_STORAGE);
            }
        }
    }

    /**
     * Stores information about session identified by $handle
     *
     * @param string $handle assiciation handle
     * @param string $macFunc HMAC function (sha1 or sha256)
     * @param string $secret shared secret
     * @param string $expires expiration UNIX time
     * @return bool
     */
    public function addAssociation($handle, $macFunc, $secret, $expires)
    {
        $name = $this->_dir . '/assoc_' . md5($handle);
        $f = @fopen($name, 'w+');
        if ($f === false) {
            return false;
        }
        flock($f, LOCK_EX);
        $data = serialize(array($handle, $macFunc, $secret, $expires));
        fwrite($f, $data);
        flock($f, LOCK_UN);
        fclose($f);
        return true;
    }

    /**
     * Gets information about association identified by $handle
     * Returns true if given association found and not expired and false
     * otherwise
     *
     * @param string $handle assiciation handle
     * @param string &$macFunc HMAC function (sha1 or sha256)
     * @param string &$secret shared secret
     * @param string &$expires expiration UNIX time
     * @return bool
     */
    public function getAssociation($handle, &$macFunc, &$secret, &$expires)
    {
        $name = $this->_dir . '/assoc_' . md5($handle);
        $f = @fopen($name, 'r');
        if ($f === false) {
            return false;
        }
        $ret = false;
        flock($f, LOCK_EX);
        $data = stream_get_contents($f);
        if (!empty($data)) {
            list($storedHandle, $macFunc, $secret, $expires) = unserialize($data);
            if ($handle === $storedHandle && $expires > time()) {
                $ret = true;
            } else {
                unlink($name);
            }
        }
        flock($f, LOCK_UN);
        fclose($f);
        return $ret;
    }

    /**
     * Removes information about association identified by $handle
     *
     * @param string $handle assiciation handle
     * @return bool
     */
    public function delAssociation($handle)
    {
        $name = $this->_dir . '/assoc_' . md5($handle);
        @unlink($name);
        return true;
    }

    /**
     * Register new user with given $id and $password
     * Returns true in case of success and false if user with given $id already
     * exists
     *
     * @param string $id user identity URL
     * @param string $password encoded user password
     * @return bool
     */
    public function addUser($id, $password)
    {
        $name = $this->_dir . '/user_' . md5($id);
        $f = @fopen($name, 'x');
        if ($f === false) {
            return false;
        }
        flock($f, LOCK_EX);
        $data = serialize(array($id, $password, array()));
        fwrite($f, $data);
        flock($f, LOCK_UN);
        fclose($f);
        return true;
    }

    /**
     * Returns true if user with given $id exists and false otherwise
     *
     * @param string $id user identity URL
     * @return bool
     */
    public function hasUser($id)
    {
        $name = $this->_dir . '/user_' . md5($id);
        $f = @fopen($name, 'r');
        if ($f === false) {
            return false;
        }
        $ret = false;
        flock($f, LOCK_EX);
        $data = stream_get_contents($f);
        if (!empty($data)) {
            list($storedId, $storedPassword, $trusted) = unserialize($data);
            if ($id === $storedId) {
                $ret = true;
            }
        }
        flock($f, LOCK_UN);
        fclose($f);
        return $ret;
    }

    /**
     * Verify if user with given $id exists and has specified $password
     *
     * @param string $id user identity URL
     * @param string $password user password
     * @return bool
     */
    public function checkUser($id, $password)
    {
        $name = $this->_dir . '/user_' . md5($id);
        $f = @fopen($name, 'r');
        if ($f === false) {
            return false;
        }
        $ret = false;
        flock($f, LOCK_EX);
        $data = stream_get_contents($f);
        if (!empty($data)) {
            list($storedId, $storedPassword, $trusted) = unserialize($data);
            if ($id === $storedId && $password === $storedPassword) {
                $ret = true;
            }
        }
        flock($f, LOCK_UN);
        fclose($f);
        return $ret;
    }

    /**
     * Removes information abou specified user
     *
     * @param string $id user identity URL
     * @return bool
     */
    public function delUser($id)
    {
        $name = $this->_dir . '/user_' . md5($id);
        @unlink($name);
        return true;
    }

    /**
     * Returns array of all trusted/untrusted sites for given user identified
     * by $id
     *
     * @param string $id user identity URL
     * @return array
     */
    public function getTrustedSites($id)
    {
        $name = $this->_dir . '/user_' . md5($id);
        $f = @fopen($name, 'r');
        if ($f === false) {
            return false;
        }
        $ret = false;
        flock($f, LOCK_EX);
        $data = stream_get_contents($f);
        if (!empty($data)) {
            list($storedId, $storedPassword, $trusted) = unserialize($data);
            if ($id === $storedId) {
                $ret = $trusted;
            }
        }
        flock($f, LOCK_UN);
        fclose($f);
        return $ret;
    }

    /**
     * Stores information about trusted/untrusted site for given user
     *
     * @param string $id user identity URL
     * @param string $site site URL
     * @param mixed $trusted trust data from extension or just a boolean value
     * @return bool
     */
    public function addSite($id, $site, $trusted)
    {
        $name = $this->_dir . '/user_' . md5($id);
        $f = @fopen($name, 'r+');
        if ($f === false) {
            return false;
        }
        $ret = false;
        flock($f, LOCK_EX);
        $data = stream_get_contents($f);
        if (!empty($data)) {
            list($storedId, $storedPassword, $sites) = unserialize($data);
            if ($id === $storedId) {
                if (is_null($trusted)) {
                    unset($sites[$site]);
                } else {
                    $sites[$site] = $trusted;
                }
                rewind($f);
                ftruncate($f, 0);
                $data = serialize(array($id, $storedPassword, $sites));
                fwrite($f, $data);
                $ret = true;
            }
        }
        flock($f, LOCK_UN);
        fclose($f);
        return $ret;
    }
}
