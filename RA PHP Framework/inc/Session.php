<?php
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
 * Provides abstractization for the _SESSION object;
 *
 * @author Catalin Alexandru Zamfir <catalin.zamfir@raphpframework.ro>
 * @copyright Under the terms of the GNU General Public License v3
 * @version $Id: Session.php 6 2012-10-26 11:01:04Z root $
 */
final class Session {

    /**
     * Creates the session, sets a flag, checks if the session was started only once;
     *
     * @author Catalin Alexandru Zamfir <catalin.zamfir@raphpframework.ro>
     * @copyright Under the terms of the GNU General Public License v3
     * @version $Id: Session.php 6 2012-10-26 11:01:04Z root $
     */
    public function __construct () {
        // Set
        self::setSessionWorker ();

        // Check
        if (self::openSession ()
        ->toBoolean () == TRUE) {
            // Set proper vars
            if (self::setKey (new S ('in_session'), new O (TRUE)) &&
            self::setKey (new S ('skin'), new O (SKIN)) &&
            self::setKey (new S ('language'), new O (LANGUAGE)) &&
            self::setKey (new S ('default_timezone'), new O (DATE_TIMEZONE))) {
                // Return
                return new B (TRUE);
            } else {
                // Throws
                throw new CannotStartSessionException;
            }
        }
    }

    /**
     * When destroyed, do some operations for this session;
     *
     * @author Catalin Alexandru Zamfir <catalin.zamfir@raphpframework.ro>
     * @copyright Under the terms of the GNU General Public License v3
     * @version $Id: Session.php 6 2012-10-26 11:01:04Z root $
     */
    public function __destruct () {

    }

    /**
     * Requires & checks that the given session storage (defined via SESSION_PATH) is/was properly initiated;
     *
     * @author Catalin Alexandru Zamfir <catalin.zamfir@raphpframework.ro>
     * @copyright Under the terms of the GNU General Public License v3
     * @version $Id: Session.php 6 2012-10-26 11:01:04Z root $
     */
    private static final function requireSessionStorage () {
        // Check
        if (!is_dir (Architecture::pathTo
        (Architecture::getStorage (),
        SESSION_PATH))) {
            // Mkdir
            if (!mkdir (Architecture::pathTo
            (Architecture::getStorage (),
            SESSION_PATH), 0777, TRUE)) {
                // Throws
                throw new CannotWriteToSessionStrage;
            }
        }
    }

    /**
     * Sets the session path & name, if given & checks the session storage;
     *
     * @author Catalin Alexandru Zamfir <catalin.zamfir@raphpframework.ro>
     * @copyright Under the terms of the GNU General Public License v3
     * @version $Id: Session.php 6 2012-10-26 11:01:04Z root $
     */
    public static final function setSession ($objPath, $objName) {
        // Requirements
        self::requireSessionStorage ();

        // Go
        return TRUE;
    }

    /**
     * Ends the current session, erasing any open connections or more;
     *
     * @author Catalin Alexandru Zamfir <catalin.zamfir@raphpframework.ro>
     * @copyright Under the terms of the GNU General Public License v3
     * @version $Id: Session.php 6 2012-10-26 11:01:04Z root $
     */
    public static final function endSession () {
        // Go
        return TRUE;
    }

    /**
     * Reads the session data for the given id;
     *
     * @author Catalin Alexandru Zamfir <catalin.zamfir@raphpframework.ro>
     * @copyright Under the terms of the GNU General Public License v3
     * @version $Id: Session.php 6 2012-10-26 11:01:04Z root $
     */
    public static final function readSession ($objId) {
        // Go!
        if (file_exists ($objPath =
        Architecture::pathTo (Architecture::getStorage (),
        SESSION_PATH, SESSION_PREFIX . _U . $objId))) {
            // Return
            if ($objFp = fopen ($objPath, 'r')) {
                // Set
                $objContents = NULL;

                // Go ATOMIC!
                flock ($objFp, LOCK_SH);

                // Read
                while (!feof ($objFp)) {
                    // Set
                    $objContents .=
                    fread ($objFp,
                    32768);
                }

                // Mutex
                flock  ($objFp, LOCK_UN);
                fclose ($objFp);

                // Return
                return (string) $objContents;
            }
        } else {
            // Return
            return FALSE;
        }
    }

    /**
     * Writes the session data to the given id, in the storage, locking files when writing;
     *
     * @author Catalin Alexandru Zamfir <catalin.zamfir@raphpframework.ro>
     * @copyright Under the terms of the GNU General Public License v3
     * @version $Id: Session.php 6 2012-10-26 11:01:04Z root $
     */
    public static final function writeSession ($objId, $objData) {
        // Check
        if ($objFp =
        fopen (Architecture::pathTo (Architecture::getStorage (),
        SESSION_PATH, SESSION_PREFIX . _U . $objId), 'w')) {
            // Go ATOMIC!
            flock ($objFp, LOCK_EX); $objR = fwrite ($objFp, $objData);
            flock ($objFp, LOCK_UN); fclose ($objFp);

            // Go
            return TRUE;
        } else {
            // Go
            return FALSE;
        }
    }

    /**
     * Erases the given (session) Id from the storage;
     *
     * @author Catalin Alexandru Zamfir <catalin.zamfir@raphpframework.ro>
     * @copyright Under the terms of the GNU General Public License v3
     * @version $Id: Session.php 6 2012-10-26 11:01:04Z root $
     */
    public static final function eraseSession ($objId) {
        // Go
        return unlink (Architecture::pathTo (Architecture::getStorage (),
        SESSION_PATH, SESSION_PREFIX . _U . $objId));
    }

    /**
     * A session worker method, does checking of expired files and erases them;
     *
     * @author Catalin Alexandru Zamfir <catalin.zamfir@raphpframework.ro>
     * @copyright Under the terms of the GNU General Public License v3
     * @version $Id: Session.php 6 2012-10-26 11:01:04Z root $
     */
    public static final function garbageSession ($objMaxLifeTime) {
        // Go
        foreach (glob (Architecture::pathTo (Architecture::getStorage (),
        SESSION_PATH, SESSION_PREFIX . _U . '*')) as $objV) {
            // Check
            if (file_exists ($objV)) {
                // Go
                if (filemtime ($objV) +
                $objMaxLifeTime < time ()) {
                    // Erase
                    unlink ($objV);
                }
            }
        }

        // Return
        return TRUE;
    }

    /**
     * Sets the session worker methods;
     *
     * @author Catalin Alexandru Zamfir <catalin.zamfir@raphpframework.ro>
     * @copyright Under the terms of the GNU General Public License v3
     * @version $Id: Session.php 6 2012-10-26 11:01:04Z root $
     */
    private static final function setSessionWorker () {
        // Set our own method
        session_set_save_handler
        ('Session::setSession',
         'Session::endSession',
         'Session::readSession',
         'Session::writeSession',
         'Session::eraseSession',
         'Session::garbageSession');
    }

    /**
     * Opens a new session, checks that only one session is opened;
     *
     * @author Catalin Alexandru Zamfir <catalin.zamfir@raphpframework.ro>
     * @copyright Under the terms of the GNU General Public License v3
     * @version $Id: Session.php 6 2012-10-26 11:01:04Z root $
     */
    public static final function openSession () {
        // Check for just one session
        if (!(self::checkKey (new S ('in_session'),
        new O (TRUE))->toBoolean ())) {
            // Set
            session_set_cookie_params (SESSION_COOKIE_LIFETIME);
            session_cache_limiter (SESSION_CACHE_LIMITER);

            // And now, start it;
            if (session_start ()) {
                // Set
                self::setCacheExpire ();
            } else {
                // Throws
                throw new CannotStartSessionException;
            }
        } else {
            // Throws
            throw new SessionAlreadyStartedException;
        }

        // Return
        return new B (TRUE);
    }

    /**
     * Destroys the session;
     *
     * @author Catalin Alexandru Zamfir <catalin.zamfir@raphpframework.ro>
     * @copyright Under the terms of the GNU General Public License v3
     * @version $Id: Session.php 6 2012-10-26 11:01:04Z root $
     */
    public static final function stopSession () {
        // Check
        if (self::checkKey (new S ('in_session'),
        new O (TRUE))->toBoolean ()) {
            // Check
            if (self::unsetKey (new
            S ('in_session'))->toBoolean ()) {
                // Check
                if (session_destroy ()) {
                    // Erase
                    $_SESSION = array ();
                } else {
                    // Throws
                    throw new CannotDestroySessionException;
                }
            } else {
                // Throws
                throw new CannotUnsetSessionException;
            }
        } else {
            // Throws
            throw new SessionNotStartedException;
        }

        // Return
        return new B (TRUE);
    }

    /**
     * Encodes session as a string to be passed via the wire;
     *
     * @author Catalin Alexandru Zamfir <catalin.zamfir@raphpframework.ro>
     * @copyright Under the terms of the GNU General Public License v3
     * @version $Id: Session.php 6 2012-10-26 11:01:04Z root $
     */
    public static final function encodeSession () {
        // Return
        return new S (session_encode ());
    }

    /**
     * Decodes the given string to the current session;
     *
     * @author Catalin Alexandru Zamfir <catalin.zamfir@raphpframework.ro>
     * @copyright Under the terms of the GNU General Public License v3
     * @link http://php.net/session_decode
     * @version $Id: Session.php 6 2012-10-26 11:01:04Z root $
     */
    public static final function decodeSession (S $sessionString) {
        // Check
        if (session_decode ($sessionString)) {
            // Return
            return new B (TRUE);
        } else {
            // Throws
            throw new CannotDecodeSessionException;
        }
    }

    /**
     * Sets the session_cache_expire to whatever SESSION_CACHE_EXPIRE (constant) is;
     *
     * @author Catalin Alexandru Zamfir <catalin.zamfir@raphpframework.ro>
     * @copyright Under the terms of the GNU General Public License v3
     * @version $Id: Session.php 6 2012-10-26 11:01:04Z root $
     */
    public static final function setCacheExpire () {
        // Check
        if (session_cache_expire
        (SESSION_CACHE_EXPIRE) !== FALSE) {
            // Return
            return new B (TRUE);
        } else {
            // Throws
            throw new CannotSetSessionCacheExpireException;
        }
    }

    /**
     * Gets the current set integer of session_cache_expire;
     *
     * @author Catalin Alexandru Zamfir <catalin.zamfir@raphpframework.ro
     * @copyright Under the terms of the GNU General Public License v3
     * @link http://php.net/session_cache_expire
     * @version $Id: Session.php 6 2012-10-26 11:01:04Z root $
     */
    public static final function getCacheExpire () {
        // Return
        return new I (session_cache_expire ());
    }

    /**
     * Checks the given key;
     *
     * @author Catalin Alexandru Zamfir <catalin.zamfir@raphpframework.ro>
     * @copyright Under the terms of the GNU General Public License v3
     * @version $Id: Session.php 6 2012-10-26 11:01:04Z root $
     */
    public static final function checkKey (S $objKey, O $objVar) {
        // Set
        $objKey = (string) $objKey;

        // Check
        if (isset ($_SESSION[$objKey])) {
            // Check
            if ($_SESSION[$objKey] == $objVar) {
                // Return
                return new B (TRUE);
            } else {
                // Return
                return new B (FALSE);
            }
        } else {
            // Return
            return new B (FALSE);
        }
    }

    /**
     * Sets the given key;
     *
     * @author Catalin Alexandru Zamfir <catalin.zamfir@raphpframework.ro>
     * @copyright Under the terms of the GNU General Public License v3
     * @version $Id: Session.php 6 2012-10-26 11:01:04Z root $
     */
    public static final function setKey (S $objKey, O $objVar) {
        // Set
        $objKey = (string) $objKey;

        // Check
        if ($_SESSION[$objKey] = $objVar) {
            // Return
            return new B (TRUE);
        } else {
            // Return
            return new B (FALSE);
        }
    }

    /**
     * Gets the given key;
     *
     * @author Catalin Alexandru Zamfir <catalin.zamfir@raphpframework.ro>
     * @copyright Under the terms of the GNU General Public License v3
     * @version $Id: Session.php 6 2012-10-26 11:01:04Z root $
     */
    public static final function getKey (S $objKey) {
        // Set
        $objKey = (string) $objKey;

        // Check
        if (isset ($_SESSION[$objKey])) {
            // Return
            return $_SESSION[$objKey];
        } else {
            // Throws
            throw new SessionKeyNotSetException;
        }
    }

    /**
     * Unsets the given key;
     *
     * @author Catalin Alexandru Zamfir <catalin.zamfir@raphpframework.ro>
     * @copyright Under the terms of the GNU General Public License v3
     * @version $Id: Session.php 6 2012-10-26 11:01:04Z root $
     */
    public static final function unsetKey (S $objKey) {
        // Set
        $objKey = (string) $objKey;

        // Check
        if (isset ($_SESSION[$objKey])) {
            // Set
            unset ($_SESSION[$objKey]);

            // Return
            return new B (TRUE);
        } else {
            // Return
            return new B (FALSE);
        }
    }
}
?>
