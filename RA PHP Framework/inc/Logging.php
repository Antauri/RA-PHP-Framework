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
 * Provides methods related to logging or output to system services;
 *
 * @author Catalin Alexandru Zamfir <catalin.zamfir@raphpframework.ro>
 * @copyright Under the terms of the GNU General Public License v3
 * @version $Id: Logging.php 1 2012-10-26 08:27:37Z root $
 */
class Logging {
    /**
     * Container to the path of the HTML Purifier, used a an XSS-checker for our content;
     *
     * @author Catalin Alexandru Zamfir <catalin.zamfir@raphpframework.ro>
     * @copyright Under the terms of the GNU General Public License v3
     * @version $Id: Logging.php 1 2012-10-26 08:27:37Z root $
     */
    private static $objPurifierPath = NULL;

    /**
     * Container of other, non-mandatory necesities;
     *
     * @author Catalin Alexandru Zamfir <catalin.zamfir@raphpframework.ro>
     * @copyright Under the terms of the GNU General Public License v3
     * @version $Id: Logging.php 1 2012-10-26 08:27:37Z root $
     */
    private static $objNonMandatory = NULL;

    /**
     * Sets the HTML Purifier path, requires others;
     *
     * @author Catalin Alexandru Zamfir <catalin.zamfir@raphpframework.ro>
     * @copyright Under the terms of the GNU General Public License v3
     * @version $Id: Logging.php 1 2012-10-26 08:27:37Z root $
     */
    public function __construct () {
        // Set
        self::setPurifier (new Path (Architecture
        ::pathTo (PLUGIN_DIR, 'php_purifier',
        'HTMLPurifier.standalone.php')));

        // Go
        self::requirePlugins ();
    }

    /**
     * Sets the path to the HTML Purifier (htmlpurifier.org) used as a framework anti-XSS plugin;
     *
     * @author Catalin Alexandru Zamfir <catalin.zamfir@raphpframework.ro>
     * @copyright Under the terms of the GNU General Public License v3
     * @version $Id: Logging.php 1 2012-10-26 08:27:37Z root $
     */
    private static final function setPurifier (Path $objPath) {
        // Just set the path
        if (self::$objPurifierPath = $objPath) {
            // Return
            return new B (TRUE);
        } else {
            // Throws
            throw new CannotSetPluginPathException;
        }
    }

    /**
     * Set other non-mandatory plugin paths;
     *
     * @author Catalin Alexandru Zamfir <catalin.zamfir@raphpframework.ro>
     * @copyright Under the terms of the GNU General Public License v3
     * @version $Id: Logging.php 1 2012-10-26 08:27:37Z root $
     */
    private static final function setNonMandatoryPlugin (Path $objPath) {
        // Just set the path
        if (self::$objNonMandatory[] = $objPath) {
            // Return
            return new B (TRUE);
        } else {
            // Throws
            throw new CannotSetPluginPathException;
        }
    }

    /**
     * Loads &/ requires some low-level framework plugins;
     *
     * @author Catalin Alexandru Zamfir <catalin.zamfir@raphpframework.ro>
     * @copyright Under the terms of the GNU General Public License v3
     * @version $Id: Logging.php 1 2012-10-26 08:27:37Z root $
     */
    private static final function requirePlugins () {
        // Require
        require_once self::$objPurifierPath;

        // Check
        if (self::$objNonMandatory != NULL) {
            // Foreach
            foreach (self::$objNonMandatory as $objPlugin) {
                // Require
                require_once $objPlugin;
            }
        }

        // Return
        return new B (TRUE);
    }

    /**
     * Writes to PHP's error log, if it yet got redirected (via Initiate) or not;
     *
     * @author Catalin Alexandru Zamfir <catalin.zamfir@raphpframework.ro>
     * @copyright Under the terms of the GNU General Public License v3
     * @version $Id: Logging.php 1 2012-10-26 08:27:37Z root $
     */
    public static final function writeLog (S $objLog) {
        // Check
        if (error_log ($objLog)) {
            // Return
            return new B (TRUE);
        } else {
            // Throws
            throw new CannotWriteToLogException;
        }
    }
}
?>
