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
 * Manages keys stored in cookie/session for the current object;
 *
 * @author Catalin Alexandru Zamfir <catalin.zamfir@raphpframework.ro>
 * @copyright Under the terms of the GNU General Public License v3
 * @version $Id: Cookie.php 1 2012-10-26 08:27:37Z root $
 */
final class Cookie  {
    /**
     * Cookie container for our cookie needs;
     *
     * @author Catalin Alexandru Zamfir <catalin.zamfir@raphpframework.ro>
     * @copyright Under the terms of the GNU General Public License v3
     * @version $Id: Cookie.php 1 2012-10-26 08:27:37Z root $
     */
    private $objObjectCookie = NULL;

    /**
     * Type of cookie storage (cookie or session);
     *
     * @author Catalin Alexandru Zamfir <catalin.zamfir@raphpframework.ro>
     * @copyright Under the terms of the GNU General Public License v3
     * @version $Id: Cookie.php 1 2012-10-26 08:27:37Z root $
     */
    private static $objObjectCookieStoreType = NULL;

    /**
     * Computed project string (from document_host);
     *
     * @author Catalin Alexandru Zamfir <catalin.zamfir@raphpframework.ro>
     * @copyright Under the terms of the GNU General Public License v3
     * @version $Id: Cookie.php 1 2012-10-26 08:27:37Z root $
     */
    private static $objProjectString = NULL;

    /* CONSTRUCT */
    public function __construct (Commons $objObjectCookie) {
        // Check
        if (self::$objProjectString == NULL) {
            // Set
            self::$objProjectString =
            Hasher::getUniqueHash (new S ('sha512'),
            new S (Architecture::getHost ()));
        }

        // Type (defaulted to PHP's session, hardcode)
        self::$objObjectCookieStoreType = new S ('session');

        // Check & tie-in
        $this->objObjectCookie = (string)
        $objObjectCookie->getObjectAncestry ();
    }

    /**
     * Sets a cookie key;
     *
     * @author Catalin Alexandru Zamfir <catalin.zamfir@raphpframework.ro>
     * @copyright Under the terms of the GNU General Public License v3
     * @version $Id: Cookie.php 1 2012-10-26 08:27:37Z root $
     */
    public function setKey (S $objKey, S $objContent, B $objExpTime) {
        // Switch
        switch (self::$objObjectCookieStoreType) {
            // Case
            case 'session':
                // Store
                $_SESSION
                [(string) self::$objProjectString]
                [$this->objObjectCookie]
                [(string) $objKey] = $objContent;
                break;

            default:
                // Throws
                throw new UnsupportedCookieType;
                break;
        }

        // Return
        return new B (TRUE);
    }

    /**
     * Returns a cookie key;
     *
     * @author Catalin Alexandru Zamfir <catalin.zamfir@raphpframework.ro>
     * @copyright Under the terms of the GNU General Public License v3
     * @version $Id: Cookie.php 1 2012-10-26 08:27:37Z root $
     */
    public function getKey (S $objKey) {
        // Switch
        switch (self::$objObjectCookieStoreType) {
            // Case
            case 'session':
                // Return
                return $_SESSION
                [(string) self::$objProjectString]
                [$this->objObjectCookie]
                [(string) $objKey];
                break;

            default:
                // Throws
                throw new UnsupportedCookieType;
                break;
        }
    }

    /**
     * Checks a given cookie key;
     *
     * @author Catalin Alexandru Zamfir <catalin.zamfir@raphpframework.ro>
     * @copyright Under the terms of the GNU General Public License v3
     * @version $Id: Cookie.php 1 2012-10-26 08:27:37Z root $
     */
    public function checkKey (S $objKey) {
        // Switch
        switch (self::$objObjectCookieStoreType) {
            // Case
            case 'session':
                // Return
                return new B (isset ($_SESSION[(string) self::$objProjectString]
                [$this->objObjectCookie][(string) $objKey]) &&
                !empty ($_SESSION[(string) self::$objProjectString]
                [$this->objObjectCookie][(string) $objKey]));
                break;

            default:
                // Throws
                throw new UnsupportedCookieType;
                break;
        }
    }

    /**
     * Unsets a given cookie key;
     *
     * @author Catalin Alexandru Zamfir <catalin.zamfir@raphpframework.ro>
     * @copyright Under the terms of the GNU General Public License v3
     * @version $Id: Cookie.php 1 2012-10-26 08:27:37Z root $
     */
    public function unSetKey (S $objKey) {
        // Switch
        switch (self::$objObjectCookieStoreType) {
            // Case
            case 'session':
                // Unset
                unset ($_SESSION
                [(string) self::$objProjectString]
                [$this->objObjectCookie]
                [(string) $objKey]);
                break;

            default:
                // Throws
                throw new UnsupportedCookieType;
                break;
        }

        // Return
        return new B (TRUE);
    }
}
?>