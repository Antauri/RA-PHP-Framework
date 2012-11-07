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
 * Provides common formatting methods used to trasnform data from one form to another;
 *
 * @author Catalin Alexandru Zamfir <catalin.zamfir@raphpframework.ro>
 * @copyright Under the terms of the GNU General Public License v3
 * @version $Id: Formatter.php 1 2012-10-26 08:27:37Z root $
 */
final class Formatter {
    /**
     * Returns the given integer byte size, to a human-friendly format;
     *
     * @author Catalin Alexandru Zamfir <catalin.zamfir@raphpframework.ro>
     * @copyright Under the terms of the GNU General Public License v3
     * @version $Id: Formatter.php 1 2012-10-26 08:27:37Z root $
     */
    protected static final function formatByteSizeToHuman (I $objSize) {
        // Set
        $objSizes = Array (' B', ' KB', ' MB', ' GB',
        ' TB', ' PB', ' EB', ' ZB', ' YB');

        // Check
        if ($objSize->toInt () == 0) {
            // Return
            return ('0 B');
        } else {
            // Return
            return (ROUND ($objSize->toInt ()/ POW (1024, ($objI =
            FLOOR (LOG ($objSize->toInt (), 1024)))), 2) .
            $objSizes[$objI]);
        }
    }
}
?>
