<?php
/**
    Edited for prof. Catalin Apostolescu, UTM, an I, ID.
*/

/*
 This program is free software; you can redistribute it and/or modify
 it under the terms of the GNU General Public License as published by
 the Free Software Foundation; either version 3 of the License, or
 (at your option) any later version.

 This program is distributed in the hope that it will be useful,
 but WITHOUT ANY WARRANTY; without even the implied warranty of
 MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 GNU General Public License for more details.

 You should have received a copy of the GNU General Public License
 along with this program.  If not, see <http://www.gnu.org/licenses/>.
 */

/**
 * Defines specifics of the architecture of this system, providind methods to handle platform differences;
 *
 * @author Catalin Alexandru Zamfir <catalin.zamfir@raphpframework.ro>
 * @copyright Under the terms of the GNU General Public License v3
 * @version $Id: Architecture.php 52 2012-11-07 12:51:58Z root $
 */
final class Architecture {
  /**
   * Stores if we're on Windows or not;
   *
   * @author Catalin Alexandru Zamfir <catalin.zamfir@raphpframework.ro>
   * @copyright Under the terms of the GNU General Public License v3
   * @version $Id: Architecture.php 52 2012-11-07 12:51:58Z root $
   */
  private static $objSecretIvc = NULL;

  /**
   * Stores if we're on Windows or not;
   *
   * @author Catalin Alexandru Zamfir <catalin.zamfir@raphpframework.ro>
   * @copyright Under the terms of the GNU General Public License v3
   * @version $Id: Architecture.php 52 2012-11-07 12:51:58Z root $
   */
  private static $objSecretKey = NULL;

    /**
     * Stores if we're on Windows or not;
     *
     * @author Catalin Alexandru Zamfir <catalin.zamfir@raphpframework.ro>
     * @copyright Under the terms of the GNU General Public License v3
     * @version $Id: Architecture.php 52 2012-11-07 12:51:58Z root $
     */
    private static $objOnWindows = NULL;

    /**
     * Stores if we are in a RELATIVE_PATH from the DOCUMENT_ROOT (inside a directory, not a domain wide setup);
     *
     * @author Catalin Alexandru Zamfir <catalin.zamfir@raphpframework.ro>
     * @copyright Under the terms of the GNU General Public License v3
     * @version $Id: Architecture.php 52 2012-11-07 12:51:58Z root $
     */
    private static $objInsideRootPath = NULL;

    /**
     * Stores the path to the DOCUMENT_ROOT, defined or redefined;
     *
     * @author Catalin Alexandru Zamfir <catalin.zamfir@raphpframework.ro>
     * @copyright Under the terms of the GNU General Public License v3
     * @version $Id: Architecture.php 52 2012-11-07 12:51:58Z root $
     */
    private static $objDocumentRoot = NULL;

    /**
     * Stores the domain (HOST) of the current project;
     *
     * @author Catalin Alexandru Zamfir <catalin.zamfir@raphpframework.ro>
     * @copyright Under the terms of the GNU General Public License v3
     * @version $Id: Architecture.php 52 2012-11-07 12:51:58Z root $
     */
    private static $objDocumentHost = NULL;

    /**
     * Stores the path to the document storage which in 99% of cases must be != DOCUMENT_ROOT, for performance reasons;
     *
     * @author Catalin Alexandru Zamfir <catalin.zamfir@raphpframework.ro>
     * @copyright Under the terms of the GNU General Public License v3
     * @version $Id: Architecture.php 52 2012-11-07 12:51:58Z root $
     */
    private static $objDocumentStorage = NULL;

    /**
     * Constructs the architecture, determines some platform specific settings;
     *
     * @author Catalin Alexandru Zamfir <catalin.zamfir@raphpframework.ro>
     * @copyright Under the terms of the GNU General Public License v3
     * @version $Id: Architecture.php 52 2012-11-07 12:51:58Z root $
     */
    public function __construct () {
        // Set basics asap
        self::convertToOurDTs ();
        self::checkWeAreOnWindows ();
        self::fixWindowsRootPath ();
        self::computeDifferenceBetweenDirectoryAndRoot ();
        self::defineInsideRootPath ();
        self::redefineDocumentRootAndHost ();
        self::setStorage ();
        self::fixTimeAndUserAgent ();
        self::registerDependencies ();

        // Encryptions
        self::setSecretKey (new S (SECRET_KEY));
        self::setSecretIvc (new S (SECRET_IVC));

        // Shutdown
        self::onShutdown (new S ('Architecture::checkForGarbage'));
    }

    /**
     * Enables auto-loading of necessary classes;
     *
     * @author Catalin Alexandru Zamfir <catalin.zamfir@raphpframework.ro>
     * @copyright Under the terms of the GNU General Public License v3
     * @version $Id: Architecture.php 52 2012-11-07 12:51:58Z root $
     */
    public static final function requireDependency ($objName) {
        // Set
        $objPath = self::pathTo (self::getRoot (),
        INCLUDE_DIR, $objName . PHP_EXTENSION);

        // Check
        if (file_exists ($objPath)) {
            // Requirements
            require_once $objPath;
        } else if (file_exists ($objPath = self
        ::pathTo (self::getRoot (), MOD_DIR,
        strtolower ($objName), INCLUDE_DIR,
        $objName . PHP_EXTENSION))) {
            // Requirements
            require_once $objPath;
        }
    }

    /**
     * Returns the document root of the project;
     *
     * @author Catalin Alexandru Zamfir <catalin.zamfir@raphpframework.ro>
     * @copyright Under the terms of the GNU General Public License v3
     * @version $Id: Architecture.php 52 2012-11-07 12:51:58Z root $
     */
    public static final function getRoot () {
        // Return
        return self::$objDocumentRoot;
    }

    /**
     * Returns the document host of the project
     *
     * @author Catalin Alexandru Zamfir <catalin.zamfir@raphpframework.ro>
     * @copyright Under the terms of the GNU General Public License v3
     * @version $Id: Architecture.php 52 2012-11-07 12:51:58Z root $
     */
    public static final function getHost () {
        // Return
        return self::$objDocumentHost;
    }

    /**
     * Returns the document storage of the project;
     *
     * @author Catalin Alexandru Zamfir <catalin.zamfir@raphpframework.ro>
     * @copyright Under the terms of the GNU General Public License v3
     * @version $Id: Architecture.php 52 2012-11-07 12:51:58Z root $
     */
    public static final function getStorage () {
        // Return
        return self::$objDocumentStorage;
    }

    /**
     * Returns the secret key for this application;
     *
     * @author Catalin Alexandru Zamfir <catalin.zamfir@raphpframework.ro>
     * @copyright Under the terms of the GNU General Public License v3
     * @version $Id: Architecture.php 52 2012-11-07 12:51:58Z root $
     */
    public static final function getSecretKey () {
      // Return
      return self::$objSecretKey;
    }

    /**
     * Sets the secret key for this application;
     *
     * @author Catalin Alexandru Zamfir <catalin.zamfir@raphpframework.ro>
     * @copyright Under the terms of the GNU General Public License v3
     * @version $Id: Architecture.php 52 2012-11-07 12:51:58Z root $
     */
    public static final function setSecretKey (S $objSecretKey) {
      // Set
      self::$objSecretKey = $objSecretKey;
    }

    /**
     * Returns the crypt vector for this application;
     *
     * @author Catalin Alexandru Zamfir <catalin.zamfir@raphpframework.ro>
     * @copyright Under the terms of the GNU General Public License v3
     * @version $Id: Architecture.php 52 2012-11-07 12:51:58Z root $
     */
    public static final function getSecretIvc () {
      // Return
      return self::$objSecretIvc;
    }

    /**
     * Sets the initialization crypt vector for this application;
     *
     * @author Catalin Alexandru Zamfir <catalin.zamfir@raphpframework.ro>
     * @copyright Under the terms of the GNU General Public License v3
     * @version $Id: Architecture.php 52 2012-11-07 12:51:58Z root $
     */
    public static final function setSecretIvc (S $objSecretIvc) {
      // Set
      self::$objSecretIvc = $objSecretIvc;
    }

    /**
     * Encrypts a given string;
     *
     * @author Catalin Alexandru Zamfir <catalin.zamfir@raphpframework.ro>
     * @copyright Under the terms of the GNU General Public License v3
     * @version $Id: Architecture.php 52 2012-11-07 12:51:58Z root $
     */
    public static final function doCrypt (S $objCrypt) {
      // Encrypting
    return new S (base64_encode (mcrypt_encrypt (MCRYPT_RIJNDAEL_128,
    Architecture::getSecretKey (), $objCrypt, MCRYPT_MODE_CFB,
    Architecture::getSecretIvc ())));
    }

    /**
     * Decrypts a given string
     *
     * @author Catalin Alexandru Zamfir <catalin.zamfir@raphpframework.ro>
     * @copyright Under the terms of the GNU General Public License v3
     * @version $Id: Architecture.php 52 2012-11-07 12:51:58Z root $
     */
    public static final function doDecrypt (S $objCrypt) {
      // Decrypting
    return new S (mcrypt_decrypt (MCRYPT_RIJNDAEL_128,
    Architecture::getSecretKey (), base64_decode ($objCrypt),
    MCRYPT_MODE_CFB, Architecture::getSecretIvc ()));
    }

    /**
     * Returns true if we're on Windows platform;
     *
     * @author Catalin Alexandru Zamfir <catalin.zamfir@raphpframework.ro>
     * @copyright Under the terms of the GNU General Public License v3
     * @version $Id: Architecture.php 52 2012-11-07 12:51:58Z root $
     */
    public static final function onWindows () {
        // Return
        return self::$objOnWindows;
    }

    /**
     * Returns a path of given arguments, separating them with _S;
     *
     * @author Catalin Alexandru Zamfir <catalin.zamfir@raphpframework.ro>
     * @copyright Under the terms of the GNU General Public License v3
     * @version $Id: Architecture.php 52 2012-11-07 12:51:58Z root $
     */
    public static final function pathTo () {
        // Return
        return new
        S (implode (_S,
        func_get_args ()));
    }

    /**
     * Registers the auto-loader with spl_autoload_register;
     *
     * @author Catalin Alexandru Zamfir <catalin.zamfir@raphpframework.ro>
     * @copyright Under the terms of the GNU General Public License v3
     * @version $Id: Architecture.php 52 2012-11-07 12:51:58Z root $
     */
    private static final function registerDependencies () {
        // Automatic registration of dependencies worker method
        spl_autoload_register ('Architecture::requireDependency');
    }

    /**
     * Registers a method to be called upon shutdown;
     *
     * @author Catalin Alexandru Zamfir <catalin.zamfir@raphpframework.ro>
     * @copyright Under the terms of the GNU General Public License v3
     * @version $Id: Architecture.php 52 2012-11-07 12:51:58Z root $
     */
    public static final function onShutdown (S $objMethod) {
        // Set it to shutdown
        register_shutdown_function ((string)
        $objMethod);
    }

    /**
     * Checks for garbage and erases it;
     *
     * @author Catalin Alexandru Zamfir <catalin.zamfir@raphpframework.ro>
     * @copyright Under the terms of the GNU General Public License v3
     * @version $Id: Architecture.php 52 2012-11-07 12:51:58Z root $
     */
    public static final function checkForGarbage () {
        // Unset
        if (isset ($_SESSION['POST'])) {
            unset ($_SESSION['POST']);
        }

        // Unset
        if (isset ($_SESSION['FILES'])) {
            unset ($_SESSION['FILES']);
        }
    }

    /**
     * Returns the visiting user-agent IP;
     *
     * @author Catalin Alexandru Zamfir <catalin.zamfir@raphpframework.ro>
     * @copyright Under the terms of the GNU General Public License v3
     * @version $Id: Architecture.php 52 2012-11-07 12:51:58Z root $
     */
    public static final function getUserAgentIp () {
        // Return
        return isset ($_SERVER['HTTP_X_FORWARD_FOR']) ?
        $_SERVER['HTTP_X_FORWARD_FOR'] : $_SERVER['REMOTE_ADDR'];
    }

    /**
     * Returns the current request method;
     *
     * @author Catalin Alexandru Zamfir <catalin.zamfir@raphpframework.ro>
     * @copyright Under the terms of the GNU General Public License v3
     * @version $Id: Architecture.php 52 2012-11-07 12:51:58Z root $
     */
    public static final function getRequestMethod () {
        // Return
        return $_SERVER['REQUEST_METHOD'];
    }

    /**
     * Returns the visiting user-agent;
     *
     * @author Catalin Alexandru Zamfir <catalin.zamfir@raphpframework.ro>
     * @copyright Under the terms of the GNU General Public License v3
     * @version $Id: Architecture.php 52 2012-11-07 12:51:58Z root $
     */
    public static final function getUserAgent () {
        // Return
        return $_SERVER['HTTP_USER_AGENT'];
    }

    /**
     * Convers PHP's native data types to our own DTs;
     *
     * @author Catalin Alexandru Zamfir <catalin.zamfir@raphpframework.ro>
     * @copyright Under the terms of the GNU General Public License v3
     * @version $Id: Architecture.php 52 2012-11-07 12:51:58Z root $
     */
    private static final function convertToOurDTs () {
        // Reset
        $_SERVER = new A ($_SERVER);

        // Process
        foreach ($_SERVER as $objK => $objV) {
            // Check
            if (is_int ($objV)) {
                // Set
                $_SERVER[$objK] =
                new I ($objV);
            } else if (is_string ($objV)) {
                // Set
                $_SERVER[$objK] =
                new S ($objV);
            }
        }

        // Reset
        $_POST = new A ($_POST);
    }

    /**
     * Method, stores true if we're on Windows;
     *
     * @author Catalin Alexandru Zamfir <catalin.zamfir@raphpframework.ro>
     * @copyright Under the terms of the GNU General Public License v3
     * @version $Id: Architecture.php 52 2012-11-07 12:51:58Z root $
     */
    private static final function checkWeAreOnWindows () {
        // Are we on Windows?!
        DIRECTORY_SEPARATOR == _S_WIN ?
        self::$objOnWindows = new B (TRUE) :
        self::$objOnWindows = new B (FALSE);
    }

    /**
     * Fixes Windows specific root path;
     *
     * @author Catalin Alexandru Zamfir <catalin.zamfir@raphpframework.ro>
     * @copyright Under the terms of the GNU General Public License v3
     * @version $Id: Architecture.php 52 2012-11-07 12:51:58Z root $
     */
    private static final function fixWindowsRootPath () {
        // Check & fix
        DIRECTORY_SEPARATOR == _S_WIN ? $_SERVER
        ->offsetSet ('DOCUMENT_ROOT', rtrim ($_SERVER
        ->offsetGet ('DOCUMENT_ROOT'), '/')) : FALSE;
    }

    /**
     * Computes difference between current directory and root path, if they're not the same;
     *
     * @author Catalin Alexandru Zamfir <catalin.zamfir@raphpframework.ro>
     * @copyright Under the terms of the GNU General Public License v3
     * @version $Id: Architecture.php 52 2012-11-07 12:51:58Z root $
     */
    private static final function computeDifferenceBetweenDirectoryAndRoot () {
        // Set
        $objPath = dirname (dirname (__FILE__));

        // Check
        if ($objPath != $_SERVER
        ->offsetGet ('DOCUMENT_ROOT')) {
            // Set our own, computed path
            $_SERVER->offsetSet ('SCRIPT_FILENAME',
            str_replace ($_SERVER->offsetGet ('DOCUMENT_ROOT'),
            $objPath, $_SERVER->offsetGet ('SCRIPT_FILENAME')));
            $_SERVER->offsetSet ('DOCUMENT_ROOT', $objPath);
        }
    }

    /**
     * Defines the inside root path (relative path);
     *
     * @author Catalin Alexandru Zamfir <catalin.zamfir@raphpframework.ro>
     * @copyright Under the terms of the GNU General Public License v3
     * @version $Id: Architecture.php 52 2012-11-07 12:51:58Z root $
     */
    private static final function defineInsideRootPath () {
        // Define
        self::$objInsideRootPath = substr (str_replace ($_SERVER->offsetGet ('DOCUMENT_ROOT') . _WS, _NONE,
        str_replace (DIRECTORY_SEPARATOR, _WS, __FILE__)),  0, strrpos (str_replace ($_SERVER->offsetGet ('DOCUMENT_ROOT') . _WS, _NONE,
        str_replace (DIRECTORY_SEPARATOR, _WS, __FILE__)), _S . INCLUDE_DIR));
    }

    /**
     * Redefines document root and host, setting them to proper values;
     *
     * @author Catalin Alexandru Zamfir <catalin.zamfir@raphpframework.ro>
     * @copyright Under the terms of the GNU General Public License v3
     * @version $Id: Architecture.php 52 2012-11-07 12:51:58Z root $
     */
    private static final function redefineDocumentRootAndHost () {
        // Check
        $_SERVER->offsetGet ('HTTPS') == 'on' ?
        $objPort = 'https://' : $objPort = 'http://';

        // Switch
        if (self::$objInsideRootPath != NULL) {
            // Root
            self::$objDocumentRoot =
            self::pathTo ($_SERVER
            ->offsetGet ('DOCUMENT_ROOT'),
            self::$objInsideRootPath);

            // Host
            self::$objDocumentHost = new S ($objPort .
            $_SERVER->offsetGet ('HTTP_HOST') . _WS .
            self::$objInsideRootPath);
        } else {
            // Root
            self::$objDocumentRoot =
            self::pathTo ($_SERVER
            ->offsetGet ('DOCUMENT_ROOT'));

            // Host
            self::$objDocumentHost = new S ($objPort .
            $_SERVER->offsetGet ('HTTP_HOST'));
        }
    }

    /**
     * Sets the proper storage, same as document root or different if that's the project set-up;
     *
     * @author Catalin Alexandru Zamfir <catalin.zamfir@raphpframework.ro>
     * @copyright Under the terms of the GNU General Public License v3
     * @version $Id: Architecture.php 52 2012-11-07 12:51:58Z root $
     */
    private static final function setStorage () {
        // Check
        if (STORAGE_AS_DOCUMENT_ROOT == TRUE) {
            // Set
            self::$objDocumentStorage =
            Architecture::pathTo
            (self::$objDocumentRoot);
        } else {
            // Set
            self::$objDocumentStorage =
            new S (STORAGE);
        }
    }

    /**
     * Fixes time and PHP User-Agent string;
     *
     * @author Catalin Alexandru Zamfir <catalin.zamfir@raphpframework.ro>
     * @copyright Under the terms of the GNU General Public License v3
     * @version $Id: Architecture.php 52 2012-11-07 12:51:58Z root $
     */
    private static final function fixTimeAndUserAgent () {
        // Set
        $_SERVER->offsetSet ('REQUEST_TIME', time ());

        // Check
        if (!($_SERVER
        ->offsetExists ('HTTP_USER_AGENT'))) {
            // Make it default to something
            $_SERVER->offsetSet ('HTTP_USER_AGENT',
            new S ('Undefined User-Agent - v0.1'));
        }
    }
}
?>
