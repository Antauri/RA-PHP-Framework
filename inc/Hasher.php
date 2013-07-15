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
 * Provides methods for hashing or encoding data from one form to another;
 *
 * @author Catalin Alexandru Zamfir <catalin.zamfir@raphpframework.ro>
 * @copyright Under the terms of the GNU General Public License v3
 * @version $Id: Hasher.php 1 2012-10-26 08:27:37Z root $
 */
final class Hasher {
    /**
     * Gets an unique code, via a random entropy and sha1 hashing;
     *
     * @author Catalin Alexandru Zamfir <catalin.zamfir@raphpframework.ro>
     * @copyright Under the terms of the GNU General Public License v3
     * @version $Id: Hasher.php 1 2012-10-26 08:27:37Z root $
     */
    protected static final function getUniqueCode (I $objLength = NULL) {
        // Sha1 of uniqid, of random entropy
        $objCode = new S (sha1 (uniqid (rand (), TRUE)));

        // Check
        if ($objLength == NULL) {
            // Return
            return $objCode;
        } else {
            // Check
            if ($objLength->toInt () >
            $objCode->toLength ()->toInt ()) {
                // Return
                return $objCode;
            } else {
                // Trim down to size
                return $objCode->doSubStr (0,
                $objLength->toInt ());
            }
        }
    }

    /**
     * Returns a hash of the given type, from the given data;
     *
     * @author Catalin Alexandru Zamfir <catalin.zamfir@raphpframework.ro>
     * @copyright Under the terms of the GNU General Public License v3
     * @version $Id: Hasher.php 1 2012-10-26 08:27:37Z root $
     */
    public static final function getUniqueHash (S $objType, S $objStringData) {
        // Check the hash type
        if (in_array ($objType, hash_algos ())) {
            // Return
            return new S (hash ($objType,
            $objStringData));
        } else {
            // Throws
            throw new HashAlgorithmNotDefinedException;
        }
    }

    /**
     * Returns a base64 encoded string from the data;
     *
     * @author Catalin Alexandru Zamfir <catalin.zamfir@raphpframework.ro>
     * @copyright Under the terms of the GNU General Public License v3
     * @version $Id: Hasher.php 1 2012-10-26 08:27:37Z root $
     */
    public static final function encodeBase64 (S $objStringData) {
        // Return
        return new
        S (base64_encode
        ($objStringData));
    }

    /**
     * Returns a base64 decoded string from the data;
     *
     * @author Catalin Alexandru Zamfir <catalin.zamfir@raphpframework.ro>
     * @copyright Under the terms of the GNU General Public License v3
     * @version $Id: Hasher.php 1 2012-10-26 08:27:37Z root $
     */
    public static final function decodeBase64 (S $objStringData) {
        // Return
        return new
        S (base64_decode
        ($objStringData));
    }
}
?>
