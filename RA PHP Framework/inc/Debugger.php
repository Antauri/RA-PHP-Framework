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
 * Provides a common interface for debugging purposes;
 *
 * @author Catalin Alexandru Zamfir <catalin.zamfir@raphpframework.ro>
 * @copyright Under the terms of the GNU General Public License v3
 * @version $Id: Debugger.php 1 2012-10-26 08:27:37Z root $
 */
final class Debugger {
    /**
     * Dumps the arguments, surrounding it with <pre> tags and exiting the script;
     *
     * @author Catalin Alexandru Zamfir <catalin.zamfir@raphpframework.ro>
     * @copyright Under the terms of the GNU General Public License v3
     * @version $Id: Debugger.php 1 2012-10-26 08:27:37Z root $
     */
    public static final function _e ($objR) {
        // Exit & Dump;
        echo '<pre>', var_dump ($objR), '</pre>';
        exit ();
    }
}
?>