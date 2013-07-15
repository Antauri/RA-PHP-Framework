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
 * Provides methods related to initiation of the environment;
 *
 * @author Catalin Alexandru Zamfir <catalin.zamfir@raphpframework.ro>
 * @copyright Under the terms of the GNU General Public License v3
 * @version $Id: Initiate.php 19 2012-10-26 20:45:34Z root $
 */
final class Initiate {
    /**
     * Container of error prepend string;
     *
     * @author Catalin Alexandru Zamfir <catalin.zamfir@raphpframework.ro>
     * @copyright Under the terms of the GNU General Public License v3
     * @version $Id: Initiate.php 19 2012-10-26 20:45:34Z root $
     */
    private static $objErrorPrependString = NULL;

    /**
     * Container of error append string;
     *
     * @author Catalin Alexandru Zamfir <catalin.zamfir@raphpframework.ro>
     * @copyright Under the terms of the GNU General Public License v3
     * @version $Id: Initiate.php 19 2012-10-26 20:45:34Z root $
     */
    private static $objErrorAppendString = NULL;

    /**
     * Sets the error prepend, append and initiates an environment;
     *
     * @author Catalin Alexandru Zamfir <catalin.zamfir@raphpframework.ro>
     * @copyright Under the terms of the GNU General Public License v3
     * @version $Id: Initiate.php 19 2012-10-26 20:45:34Z root $
     */
    public function __construct () {
        // Set prepend & append strings
        self::setErrorPrependString (new S ('<RA_php_error>'));
        self::setErrorAppendString (new S ('</RA_php_error>'));

        // Set an environemtn (default)
        self::setInitiationEnvironment ();

        // Check
        if (self::getAverageLoading ()
        ->toInt () > SYSTEM_LOAD_MAX) {
            // Throw
            throw new SystemLoadTooHighException;
        }
    }

    /**
     * Sets a string (we catch for in setStreamCatcher) to prepend around errors;
     *
     * @author Catalin Alexandru Zamfir <catalin.zamfir@raphpframework.ro>
     * @copyright Under the terms of the GNU General Public License v3
     * @version $Id: Initiate.php 19 2012-10-26 20:45:34Z root $
     */
    private static final function setErrorPrependString (S $errorPrependString) {
        // Check
        if (self::$objErrorPrependString =
        $errorPrependString) {
            // Return
            return new B (TRUE);
        } else {
            // Throws
            throw new CannotSetErrorPrependStringException;
        }
    }

    /**
     * Returns the error prepend string;
     *
     * @author Catalin Alexandru Zamfir <catalin.zamfir@raphpframework.ro>
     * @copyright Under the terms of the GNU General Public License v3
     * @version $Id: Initiate.php 19 2012-10-26 20:45:34Z root $
     */
    public static final function getErrorPrependString () {
        // Return
        return self::$objErrorPrependString;
    }

    /**
     * Sets a string (we catch for in setStreamCatcher) to append around errors;
     *
     * @author Catalin Alexandru Zamfir <catalin.zamfir@raphpframework.ro>
     * @copyright Under the terms of the GNU General Public License v3
     * @version $Id: Initiate.php 19 2012-10-26 20:45:34Z root $
     */
    private static final function setErrorAppendString (S $errorAppendString) {
        // Set
        if (self::$objErrorAppendString =
        $errorAppendString) {
            // Return
            return new B (TRUE);
        } else {
            // Throws
            throw new CannotSetErrorAppendStringException;
        }
    }

    /**
     * Returns the error append string;
     *
     * @author Catalin Alexandru Zamfir <catalin.zamfir@raphpframework.ro>
     * @copyright Under the terms of the GNU General Public License v3
     * @version $Id: Initiate.php 19 2012-10-26 20:45:34Z root $
     */
    public static final function getErrorAppendString () {
        // Return
        return self::$objErrorAppendString;
    }

    /**
     * Sets an environemnt by setting most of ini_set PHP_INI_ALL environment settings;
     *
     * @author Catalin Alexandru Zamfir <catalin.zamfir@raphpframework.ro>
     * @copyright Under the terms of the GNU General Public License v3
     * @version $Id: Initiate.php 19 2012-10-26 20:45:34Z root $
     */
    private static final function setInitiationEnvironment () {
        // Set a script max execution time
        set_time_limit (SCRIPT_TIME_LIMIT);

        // Localize this script
        setlocale (LC_ALL, DEFAULT_LOCALE);

        // Set DATE_TIMEZONE default
        ini_set ('date.timezone', DATE_TIMEZONE);

        // Set the PHP include path
        if (set_include_path (get_include_path () .
        PATH_SEPARATOR . Architecture::pathTo (Architecture
        ::getRoot (), INCLUDE_DIR))) {

            // Set the PHP options that can be set
            if (ini_set ('memory_limit', MEMORY_LIMIT) !== FALSE) {
                // Set error_reporting
                if (!(ini_set ('error_reporting',
                ERROR_REPORTING_LEVEL) !== FALSE)) {
                    // Throws
                    throw new CannotSetErrorReportingException;
                }

                // Set the session.cache_expire
                if (!(ini_set ('session.cache_expire',
                SESSION_CACHE_EXPIRE) !== FALSE)) {
                    // Throws
                    throw new CannotSetSessionCacheExpireException;
                }

                // And the max_execution_time
                if (!(ini_set ('max_execution_time',
                SCRIPT_TIME_LIMIT) !== FALSE)) {
                    // Throws
                    throw new CannotSetScriptTimeLimitException;
                }

                // While showing me errors
                if (!(ini_set ('display_errors',
                DISPLAY_ERRORS) !== FALSE)) {
                    // Throws
                    throw new CannotSetDisplayErrorsException;
                }

                // Custom user-agent (we visit as many)
                ini_set ('user_agent', $_SERVER['HTTP_USER_AGENT']);

                // Set other non-important settings
                ini_set ('display_startup_errors', DISPLAY_STARTUP_ERRORS);
                ini_set ('default_charset', DEFAULT_CHARSET);
                ini_set ('error_log', Architecture::pathTo (Architecture::getStorage (), LOG_DIR, PHP_ERROR_LOG));
                ini_set ('html_errors', PHP_HTML_ERRORS);
                ini_set ('implicit_flush', IMPLICIT_FLUSH);
                ini_set ('error_prepend_string', self::$objErrorPrependString);
                ini_set ('error_append_string', self::$objErrorAppendString);
                ini_set ('session.auto_start', SESSION_AUTOSTART);
                ini_set ('session.gc_probability', SESSION_GC_PROBABILITY);
                ini_set ('session.gc_divisor', SESSION_GC_DIVISOR);
                ini_set ('session.gc_maxlifetime', SESSION_COOKIE_LIFETIME);

                // Ignore user abort, or not
                ignore_user_abort (IGNORE_USER_ABORT);

                // Permission
                self::checkStoragePermissions ();

                // Return
                return new B (TRUE);
            } else {
                // Throws
                throw new CannotSetMemoryLimitException;
            }
        } else {
            // Throws
            throw new CannotSetIncludePathException;
        }
    }

    /**
     * Tries to return an average loading of the last N minutes, if the operating system supports it;
     *
     * @author Catalin Alexandru Zamfir <catalin.zamfir@raphpframework.ro>
     * @copyright Under the terms of the GNU General Public License v3
     * @version $Id: Initiate.php 19 2012-10-26 20:45:34Z root $
     */
    private static final function getAverageLoading () {
        // Check
        if (Architecture::onWindows ()
        ->toBoolean () == FALSE) {
            // Get the AVG;
            if ($averageLinuxLoad = new A (sys_getloadavg ())) {
                // Return
                return new I ((int) $averageLinuxLoad[0]);
            } else {
                // Throws
                throw new CannotGetSystemAvgLoading;
            }
        } else {
            // Return
            return new I (0);
        }
    }

    /**
     * Checks storage permissions, does mkdir & chmod on paths, assures everything is in order;
     *
     * @author Catalin Alexandru Zamfir <catalin.zamfir@raphpframework.ro>
     * @copyright Under the terms of the GNU General Public License v3
     * @version $Id: Initiate.php 19 2012-10-26 20:45:34Z root $
     */
    private static final function checkStoragePermissions () {
        // Check
        if (!(is_writeable ($objPath = Architecture
        ::pathTo (Architecture::getStorage ())))) {
            // Throws
            throw new DocumentStorageNotWriteableException;
        }

        // Check
        if (!file_exists ($objStore = Architecture
        ::pathTo ($objPath, CACHE_DIR))) {
            // Make
            mkdir ($objStore, 0777, TRUE);
            chmod ($objStore, 0777);
        }

        // Check
        if (!file_exists ($objStore = Architecture
        ::pathTo ($objPath, LOG_DIR))) {
            // Make
            mkdir ($objStore, 0777, TRUE);
            chmod ($objStore, 0777);
        }

        // Check
        if (!file_exists ($objStore = Architecture
        ::pathTo ($objPath, BACKUP_DIR))) {
            // Make
            mkdir ($objStore, 0777, TRUE);
            chmod ($objStore, 0777);
        }

        // Check
        if (!file_exists ($objStore = Architecture
        ::pathTo ($objPath, UPLOAD_DIR, TEMP_DIR))) {
            // Make
            mkdir ($objStore, 0777, TRUE);
            chmod ($objStore, 0777);
        }

        // Check
        if (!is_writeable (Architecture
        ::pathTo ($objPath, CACHE_DIR))) {
            // Throws
            throw new CacheDirNotWriteableException;
        }

        // Check
        if (!is_writeable (Architecture
        ::pathTo ($objPath, LOG_DIR))) {
            // Throws
            throw new  LogDirNotWriteableException;
        }

        // Check
        if (!is_writeable (Architecture
        ::pathTo ($objPath, BACKUP_DIR))) {
            // Throws
            throw new BackupDirNotWriteableException;
        }

        // Check
        if (!is_writeable (Architecture
        ::pathTo ($objPath, UPLOAD_DIR))) {
            // Throws
            throw new UploadDirNotWriteableException;
        } else {
            if (!is_writeable (Architecture
            ::pathTo ($objPath, UPLOAD_DIR,TEMP_DIR))) {
                // Throws
                throw new TemporaryDirNotWriteableException;
            }
        }
    }
}
?>
