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
 * Provides methods for working with the output buffer;
 *
 * @author Catalin Alexandru Zamfir <catalin.zamfir@raphpframework.ro>
 * @copyright Under the terms of the GNU General Public License v3
 * @version $Id: Output.php 1 2012-10-26 08:27:37Z root $
 */
final class Output {
    /**
     * Container of the stream processing method;
     *
     * @author Catalin Alexandru Zamfir <catalin.zamfir@raphpframework.ro>
     * @copyright Under the terms of the GNU General Public License v3
     * @version $Id: Output.php 1 2012-10-26 08:27:37Z root $
     */
    private static $objOutputStreamHandler = NULL;

    /**
     * Container of the error stream catcher;
     *
     * @author Catalin Alexandru Zamfir <catalin.zamfir@raphpframework.ro>
     * @copyright Under the terms of the GNU General Public License v3
     * @version $Id: Output.php 1 2012-10-26 08:27:37Z root $
     */
    private static $objOutputStreamCatcher = NULL;

    /**
     * Container of the exception catcher;
     *
     * @author Catalin Alexandru Zamfir <catalin.zamfir@raphpframework.ro>
     * @copyright Under the terms of the GNU General Public License v3
     * @version $Id: Output.php 1 2012-10-26 08:27:37Z root $
     */
    private static $objOutputExceptionCatcher = NULL;

    /**
     * Container for the generated output buffer;
     *
     * @author Catalin Alexandru Zamfir <catalin.zamfir@raphpframework.ro>
     * @copyright Under the terms of the GNU General Public License v3
     * @version $Id: Output.php 1 2012-10-26 08:27:37Z root $
     */
    private static $objOutputBuffer = NULL;

    /**
     * Container for output buffer tokens to be replaced;
     *
     * @author Catalin Alexandru Zamfir <catalin.zamfir@raphpframework.ro>
     * @copyright Under the terms of the GNU General Public License v3
     * @version $Id: Output.php 1 2012-10-26 08:27:37Z root $
     */
    private static $objTokensBuffer = NULL;

    /**
     * Container for output buffer strings to replace given tokens;
     *
     * @author Catalin Alexandru Zamfir <catalin.zamfir@raphpframework.ro>
     * @copyright Under the terms of the GNU General Public License v3
     * @version $Id: Output.php 1 2012-10-26 08:27:37Z root $
     */
    private static $objStringBuffer = NULL;

    /**
     * Sets output buffer requirements and worker methods;
     *
     * @author Catalin Alexandru Zamfir <catalin.zamfir@raphpframework.ro>
     * @copyright Under the terms of the GNU General Public License v3
     * @version $Id: Output.php 1 2012-10-26 08:27:37Z root $
     */
    public function __construct () {
        // Set
        self::$objTokensBuffer = new A;
        self::$objStringBuffer = new A;
        self::$objOutputBuffer = new A;

        // Set working methods
        self::setExceptionCatcher (new S ('Error::catchExecutionExceptions'));
        self::setStreamCatcher (new S ('Error::catchStreamErrors'));
        self::setErrorCatcher (new S ('Error::catchExecutionErrors'));

        // Execute
        self::executeStream ();
    }

    /**
     * Sets an exception catcher for the current instance;
     *
     * @author Catalin Alexandru Zamfir <catalin.zamfir@raphpframework.ro>
     * @copyright Under the terms of the GNU General Public License v3
     * @version $Id: Output.php 1 2012-10-26 08:27:37Z root $
     */
    public static final function setExceptionCatcher (S $objExceptionWorker) {
        // Check
        if (Execution
        ::checkMethodIsDefined ($objExceptionWorker)
        ->toBoolean ()) {
            // Check
            if (self::$objOutputExceptionCatcher =
            $objExceptionWorker) {
                // Return
                return new B (TRUE);
            } else {
                // Throws
                throw new CannotSetWorkerMethodException;
            }
        } else {
            // Throws
            throw new MethodNotCallableException;
        }
    }

    /**
     * Sets the stream catcher (worker) method;
     *
     * @author Catalin Alexandru Zamfir <catalin.zamfir@raphpframework.ro>
     * @copyright Under the terms of the GNU General Public License v3
     * @version $Id: Output.php 1 2012-10-26 08:27:37Z root $
     */
    public static final function setStreamCatcher (S $objOutputStream) {
        // Check
        if (Execution
        ::checkMethodIsDefined ($objOutputStream)
        ->toBoolean ()) {
            // Check
            if (self::$objOutputStreamHandler =
            $objOutputStream) {
                // Return
                return new B (TRUE);
            } else {
                // Throws
                throw new CannotSetWorkerMethodException;
            }
        } else {
            // Throws
            throw new MethodNotCallableException;
        }
    }

    /**
     * Returns the tokens buffer, defined in the stream;
     *
     * @author Catalin Alexandru Zamfir <catalin.zamfir@raphpframework.ro>
     * @copyright Under the terms of the GNU General Public License v3
     * @version $Id: Output.php 1 2012-10-26 08:27:37Z root $
     */
    public static final function getTokens () {
        // Return
        return self::$objTokensBuffer;
    }

    /**
     * Returns the string buffer, which are going to replace tokens;
     *
     * @author Catalin Alexandru Zamfir <catalin.zamfir@raphpframework.ro>
     * @copyright Under the terms of the GNU General Public License v3
     * @version $Id: Output.php 1 2012-10-26 08:27:37Z root $
     */
    public static final function getStrings () {
        // Return
        return self::$objStringBuffer;
    }

    /**
     * Returns the buffer;
     *
     * @author Catalin Alexandru Zamfir <catalin.zamfir@raphpframework.ro>
     * @copyright Under the terms of the GNU General Public License v3
     * @version $Id: Output.php 1 2012-10-26 08:27:37Z root $
     */
    public static final function getBuffer () {
        // Return
        return self::$objOutputBuffer;
    }

    /**
     * Sets the buffer to the given container;
     *
     * @author Catalin Alexandru Zamfir <catalin.zamfir@raphpframework.ro>
     * @copyright Under the terms of the GNU General Public License v3
     * @version $Id: Output.php 1 2012-10-26 08:27:37Z root $
     */
    public static final function setBuffer (A $objOutputBuffer) {
        // Set
        self::$objOutputBuffer = $objOutputBuffer;
    }

    /**
     * Appends the given string to the buffer;
     *
     * @author Catalin Alexandru Zamfir <catalin.zamfir@raphpframework.ro>
     * @copyright Under the terms of the GNU General Public License v3
     * @version $Id: Output.php 1 2012-10-26 08:27:37Z root $
     */
    public static final function appendBuffer (S $objBufferString) {
        // Set
        self::$objOutputBuffer[] = $objBufferString;
    }

    /**
     * Sets the PHP error catcher to the given method;
     *
     * @author Catalin Alexandru Zamfir <catalin.zamfir@raphpframework.ro>
     * @copyright Under the terms of the GNU General Public License v3
     * @version $Id: Output.php 1 2012-10-26 08:27:37Z root $
     */
    public static final function setErrorCatcher (S & $objOutputStream) {
        // Check
        if (Execution
        ::checkMethodIsDefined ($objOutputStream)
        ->toBoolean ()) {
            // Check
            if (self::$objOutputStreamCatcher = $objOutputStream) {
                // Return
                return new B (TRUE);
            } else {
                // Throws
                throw new CannotSetWorkerMethodException;
            }
        } else {
            // Throws
            throw new MethodNotCallableException;
        }
    }

    /**
     * Executes the stream, sets the error & exception handlers;
     *
     * @author Catalin Alexandru Zamfir <catalin.zamfir@raphpframework.ro>
     * @copyright Under the terms of the GNU General Public License v3
     * @version $Id: Output.php 1 2012-10-26 08:27:37Z root $
     */
    public static final function executeStream () {
        // Promote exceptions to errors
        set_exception_handler ((string)
        self::$objOutputExceptionCatcher);

        // Check
        if (ob_start ((string)
        self::$objOutputStreamHandler)) {
            // Set
            set_error_handler ((string)
            self::$objOutputStreamCatcher);

            // Return
            return new B (TRUE);
        } else {
            // Throws
            throw new CannotStartOutputBufferingException;
        }
    }

    /**
     * Erases the stream;
     *
     * @author Catalin Alexandru Zamfir <catalin.zamfir@raphpframework.ro>
     * @copyright Under the terms of the GNU General Public License v3
     * @version $Id: Output.php 1 2012-10-26 08:27:37Z root $
     */
    public static final function eraseStream () {
        // Check
        if (self::getId ()
        ->toInt () != 0) {
            // Return
            return new B (ob_end_flush ());
        }
    }

    /**
     * Switches the tokens buffer for their corresponding strings;
     *
     * @author Catalin Alexandru Zamfir <catalin.zamfir@raphpframework.ro>
     * @copyright Under the terms of the GNU General Public License v3
     * @version $Id: Output.php 1 2012-10-26 08:27:37Z root $
     */
    public static final function changeStreamToken (S $objToken, S $objString) {
        // Check
        if (self::$objTokensBuffer[] = $objToken &&
        self::$objStringBuffer[] = $objString) {
            // Return
            return new B (TRUE);
        } else {
            // Throws
            throw new CannotSetOutputTokenException;
        }
    }

    /**
     * Discards the stream;
     *
     * @author Catalin Alexandru Zamfir <catalin.zamfir@raphpframework.ro>
     * @copyright Under the terms of the GNU General Public License v3
     * @version $Id: Output.php 1 2012-10-26 08:27:37Z root $
     */
    public static final function discardStream (B $objDiscardType = NULL) {
        // Check
        if (self::getId ()
        ->toInt () != 0) {
            // Check
            if ($objDiscardType != NULL) {
                // Erase
                ob_clean ();

                // NULLify
                self::$objOutputBuffer = NULL;

                // Return
                return new B (TRUE);
            } else {
                // Erase
                ob_clean ();

                // Return
                return new B (TRUE);
            }
        }
    }

    /**
     * Stops the stream (object-oriended die ());
     *
     * @author Catalin Alexandru Zamfir <catalin.zamfir@raphpframework.ro>
     * @copyright Under the terms of the GNU General Public License v3
     * @version $Id: Output.php 1 2012-10-26 08:27:37Z root $
     */
    public static final function stopStream () {
        // Die;
        die ();
    }

    /**
     * Returns the contents of the stream as a string;
     *
     * @author Catalin Alexandru Zamfir <catalin.zamfir@raphpframework.ro>
     * @copyright Under the terms of the GNU General Public License v3
     * @version $Id: Output.php 1 2012-10-26 08:27:37Z root $
     */
    public static final function getStream () {
        // Check
        if (self::getId ()
        ->toInt () != 0) {
            // Return
            return new S (ob_get_contents ());
        } else {
            // Throws
            throw new OutputBufferNotStartedException;
        }
    }

    /**
     * Returns the size of the buffer;
     *
     * @author Catalin Alexandru Zamfir <catalin.zamfir@raphpframework.ro>
     * @copyright Under the terms of the GNU General Public License v3
     * @version $Id: Output.php 1 2012-10-26 08:27:37Z root $
     */
    public static final function getSize () {
        // Check
        if (self::getId ()
        ->toInt () != 0) {
            // Return
            return new I (ob_get_length ());
        } else {
            // Throws
            throw new OutputBufferNotStartedException;
        }
    }

    /**
     * Returns the id of the current buffer we're in;
     *
     * @author Catalin Alexandru Zamfir <catalin.zamfir@raphpframework.ro>
     * @copyright Under the terms of the GNU General Public License v3
     * @version $Id: Output.php 1 2012-10-26 08:27:37Z root $
     */
    public static final function getId () {
        // Return
        return new I (ob_get_level ());
    }

    /**
     * Writes the stream to the given Path, as a whole;
     *
     * @author Catalin Alexandru Zamfir <catalin.zamfir@raphpframework.ro>
     * @copyright Under the terms of the GNU General Public License
     * @version $Id: Output.php 1 2012-10-26 08:27:37Z root $
     */
    public static final function writeToPath (Path $objPath) {
        // Return
        return $objPath->putToFile (self::getStream ());
    }
}
?>
