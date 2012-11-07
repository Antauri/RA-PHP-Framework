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
 * Provides methods related to the current execution, either timers or checks for defined methods or objects;
 *
 * @author Catalin Alexandru Zamfir <catalin.zamfir@raphpframework.ro>
 * @copyright Under the terms of the GNU General Public License v3
 * @version $Id: Execution.php 1 2012-10-26 08:27:37Z root $
 */
final class Execution {
    /**
     * Static storage for microtime data, used in get/setExeTime
     *
     * @author Catalin Alexandru Zamfir <catalin.zamfir@raphpframework.ro>
     * @copyright Under the terms of the GNU General Public License v3
     * @version $Id: Execution.php 1 2012-10-26 08:27:37Z root $
     */
    private static $objExecutionTime = NULL;

    /**
     * Initiates some requirements and sets the 'Start' tag for execution time as soon as it can get;
     *
     * @author Catalin Alexandru Zamfir <catalin.zamfir@raphpframework.ro>
     * @copyright Under the terms of the GNU General Public License v3
     * @version $Id: Execution.php 1 2012-10-26 08:27:37Z root $
     */
    public function __construct () {
        // Set
        self::$objExecutionTime = new A;
        self::setExeTime (new S ('Start'));
    }

    /**
     * Sets a timestamp used, used later to calculate execution time;
     *
     * @author Catalin Alexandru Zamfir <catalin.zamfir@raphpframework.ro>
     * @copyright Under the terms of the GNU General Public License v3
     * @version $Id: Execution.php 1 2012-10-26 08:27:37Z root $
     */
    public static final function setExeTime (S $objTimestamp) {
        // Set the curent timestamp
        if (self::$objExecutionTime
        ->offsetSet ($objTimestamp, microtime (TRUE))) {
            // Return
            return new B (TRUE);
        } else {
            // Throws
            throw new CannotSetTimestampException;
        }
    }

    /**
     * Returns the difference between two timestamps;
     *
     * @author Catalin Alexandru Zamfir <catalin.zamfir@raphpframework.ro>
     * @copyright Under the terms of the GNU General Public License v3
     * @version $Id: Execution.php 1 2012-10-26 08:27:37Z root $
     */
    public static final function getExeTime (S $objTimestampA, S $objTimestampB) {
        // Check
        if (self::$objExecutionTime->offsetExists ($objTimestampA) &&
        self::$objExecutionTime->offsetExists ($objTimestampB)) {

            // Diffs
            return new F (self::$objExecutionTime->offsetGet ($objTimestampB) -
            self::$objExecutionTime->offsetGet ($objTimestampA));
        } else {
            // Throws
            throw new TimestampNotSetException;
        }
    }

    /**
     * Checks if a class was defined (PHP got to it and parsed it);
     *
     * @author Catalin Alexandru Zamfir <catalin.zamfir@raphpframework.ro>
     * @copyright Under the terms of the GNU General Public License v3
     * @version $Id: Execution.php 1 2012-10-26 08:27:37Z root $
     */
    public static final function checkIsDefined (S $objName) {
        // Return
        return new B (class_exists
        ($objName, FALSE));
    }

    /**
     * Check if the method can be called or throw an exception;
     *
     * @author Catalin Alexandru Zamfir <catalin.zamfir@raphpframework.ro>
     * @copyright Under the terms of the GNU General Public License v3
     * @version $Id: Execution.php 1 2012-10-26 08:27:37Z root $
     */
    public static final function checkMethodIsDefined (S $objMethod) {
        // Check
        if (is_callable ((string)
        $objMethod)) {
            // Return
            return new B (TRUE);
        } else {
            // Throws
            throw new MethodNotCallableException;
        }
    }

    /**
     * Delayes the script for the given millis as argument;
     *
     * @author Catalin Alexandru Zamfir <catalin.zamfir@raphpframework.ro>
     * @copyright Under the terms of the GNU General Public License v3
     * @version $Id: Execution.php 1 2012-10-26 08:27:37Z root $
     */
    public static final function setScriptDelayTime (I $objDelay) {
        // Wait for a given time
        USLEEP ($objDelay->toInt ());
    }

    /**
     * Method to be used when we want to execute a pre-programmed command or as part of the ChainOfCommand pattern;
     *
     * @author Catalin Alexandru Zamfir <catalin.zamfir@raphpframework.ro>
     * @copyright Under the terms of the GNU General Public License v3
     * @version $Id: Execution.php 1 2012-10-26 08:27:37Z root $
     */
    public static final function executeCommand (S $objCommandName, A $objPassedParameters = NULL) {
        // Return
        return new B (TRUE);
    }

    /**
     * Provides a method to execute a string as backticks;
     *
     * @author Catalin Alexandru Zamfir <catalin.zamfir@raphpframework.ro>
     * @copyright Under the terms of the GNU General Public License v3
     * @version $Id: Execution.php 1 2012-10-26 08:27:37Z root $
     */
    public static final function executeSystem (S $objCommandString) {
        // Return
        return exec ($objCommandString);
    }

    /**
     * Execute stored code through EVAL, return whatever it returns as an O;
     *
     * @author Catalin Alexandru Zamfir <catalin.zamfir@raphpframework.ro>
     * @copyright Under the terms of the GNU General Public License v3
     * @version $Id: Execution.php 1 2012-10-26 08:27:37Z root $
     */
    public static final function executeStoredCode (S $objStoredCode) {
        // Return
        return new O (EVAL ($objStoredCode
        ->prependString ('?>')));
    }
}
?>
