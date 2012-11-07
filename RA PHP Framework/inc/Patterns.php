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
 * Provides the best-known Singleton pattern;
 *
 * @author Catalin Alexandru Zamfir <catalin.zamfir@raphpframework.ro>
 * @copyright Under the terms of the GNU General Public License v3
 * @version $Id: Patterns.php 1 2012-10-26 08:27:37Z root $
 */
class Singleton {
    /**
     * Container of singleton instances registered with us;
     *
     * @author Catalin Alexandru Zamfir <catalin.zamfir@raphpframework.ro>
     * @copyright Under the terms of the GNU General Public License v3
     * @version $Id: Patterns.php 1 2012-10-26 08:27:37Z root $
     */
    private static $objRegisteredInstances = NULL;

    /**
     * Returns the instance, singleton or not;
     *
     * @author Catalin Alexandru Zamfir <catalin.zamfir@raphpframework.ro>
     * @copyright Under the terms of the GNU General Public License v3
     * @version $Id: Patterns.php 1 2012-10-26 08:27:37Z root $
     */
    public static final function getInstance (S $objInstance) {
        // Check
        if (self::$objRegisteredInstances == NULL) {
            self::$objRegisteredInstances = new A;
        }

        // Set (native so new works)
        $objInstance = (string) $objInstance;

        // Return
        return self::$objRegisteredInstances
        ->offsetExists ($objInstance) ? self::$objRegisteredInstances[$objInstance] :
        self::$objRegisteredInstances[$objInstance] = new $objInstance;
    }

    /**
     * Checks to see if the instance was defined or not;
     *
     * @author Catalin Alexandru Zamfir <catalin.zamfir@raphpframework.ro>
     * @copyright Under the terms of the GNU General Public License v3
     * @version $Id: Patterns.php 1 2012-10-26 08:27:37Z root $
     */
    public static final function checkHas (S $objInstance) {
        // Return
        return new B (self::$objRegisteredInstances
        ->offsetExists ($objInstance));
    }
}

/**
 * Provides the best-known ChainOfCommand pattern;
 *
 * @author Catalin Alexandru Zamfir <catalin.zamfir@raphpframework.ro>
 * @copyright Under the terms of the GNU General Public License v3
 * @version $Id: Patterns.php 1 2012-10-26 08:27:37Z root $
 */
class ChainOfCommand {
    // Statics
    private static $objRegisteredExecutors = NULL;

    /**
     * Registeres the given object with the ChainOfCommand pattern;
     *
     * @author Catalin Alexandru Zamfir <catalin.zamfir@raphpframework.ro>
     * @copyright Under the terms of the GNU General Public License v3
     * @version $Id: Patterns.php 1 2012-10-26 08:27:37Z root $
     */
    public static final function registerExecutor (M $objToRegister) {
        // Check
        if (self::$objRegisteredExecutors == NULL) {
            self::$objRegisteredExecutors = new A;
        }

        // Check
        if ($objToRegister != NULL) {
            // Set
            self::$objRegisteredExecutors[] =
            $objToRegister;

            // Return
            return new B (TRUE);
        }
    }

    /**
     * Notifies registered objects that a command was issued;
     *
     * @author Catalin Alexandru Zamfir <catalin.zamfir@raphpframework.ro>
     * @copyright Under the terms of the GNU General Public License v3
     * @version $Id: Patterns.php 1 2012-10-26 08:27:37Z root $
     */
    public static final function notifyCommand (S $objCommandName, A $objPassedParameters = NULL) {
        // Foreach
        foreach (self::$objRegisteredExecutors as $objK => $objV) {
            // Execute
            $objV->executeCommand ($objCommandName,
            $objPassedParameters);
        }
    }
}
?>