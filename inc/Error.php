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
 * Provides a mechanism to output error screens or scan the output stream for errors;
 *
 * @author Catalin Alexandru Zamfir <catalin.zamfir@raphpframework.ro>
 * @copyright Under the terms of the GNU General Public License v3
 * @version $Id: Error.php 1 2012-10-26 08:27:37Z root $
 */
final class Error {
    /**
     * Container of the current error status of this object if any has happened;
     *
     * @author Catalin Alexandru Zamfir <catalin.zamfir@raphpframework.ro>
     * @copyright Under the terms of the GNU General Public License v3
     * @version $Id: Error.php 1 2012-10-26 08:27:37Z root $
     */
    private static $objStatus = NULL;

    /**
     * Constructs the error mechanism, settings just the status to 0;
     *
     * @author Catalin Alexandru Zamfir <catalin.zamfir@raphpframework.ro>
     * @copyright Under the terms of the GNU General Public License v3
     * @version $Id: Error.php 1 2012-10-26 08:27:37Z root $
     */
    public function __construct () {
        // Set
        self::$objStatus = new I (0);
    }

    /**
     * Promotes exceptions as errors, using the trace string, so we can use only one error interface;
     *
     * @author Catalin Alexandru Zamfir <catalin.zamfir@raphpframework.ro>
     * @copyright Under the terms of the GNU General Public License v3
     * @version $Id: Error.php 1 2012-10-26 08:27:37Z root $
     */
    public static final function catchExecutionExceptions (Exception $objException) {
        // !!
        try {
            // Promote exceptions
            trigger_error ($objException
            ->getTraceAsString ());
        } catch (Exception $objE) {
            // Prevent no-stacks
            die ($objE->getMessage ());
        }
    }

    /**
     * Save the stream to the buffer, but do a check for errors while on the job;
     *
     * @author Catalin Alexandru Zamfir <catalin.zamfir@raphpframework.ro>
     * @copyright Under the terms of the GNU General Public License v3
     * @version $Id: Error.php 1 2012-10-26 08:27:37Z root $
     */
    public static final function catchStreamErrors ($objBufferString, $objBufferState) {
        // Matching for errors (parse errors, uncatchable)
        if (preg_match ('#' . Initiate::getErrorPrependString () . '(' .
        _ANY . ')' . Initiate::getErrorAppendString () . '#si',
        $objBufferString, $pregString)) {
            // Return
            return self::renderDeath (new S (__CLASS__), NULL,
            NULL, _S ((string) $pregString[0])->trimBoth ()
            ->stripTags (), new B (TRUE));
        } else {
            // Append
            Output::appendBuffer (new
            S ((string) $objBufferString));

            // Nothing
            return NULL;
        }
    }

    /**
     * Catches errors from PHP, displaying them in a pretty error-screen;
     *
     * @author Catalin Alexandru Zamfir <catalin.zamfir@raphpframework.ro>
     * @copyright Under the terms of the GNU General Public License v3
     * @version $Id: Error.php 1 2012-10-26 08:27:37Z root $
     */
    public static final function catchExecutionErrors ($objErrorType, $objErrorString, $objErrorPath, $objErrorPathNo) {
        // Check
        $objErrorType = new I ($objErrorType);
        $objErrorString = new S ($objErrorString);
        $objErrorPath = new S ($objErrorPath);
        $objErrorPathNo = new I ($objErrorPathNo);

        // Hardcoded
        $objHTML = new XMLWriter;
        $objHTML->openMemory ();
        $objHTML->setIndent (true);

        // Go baby, go
        $objHTML->startElement ('ul');
        $objHTML->startElement ('li');
        $objHTML->text ('M: [%EMSG%]');
        $objHTML->endElement ();
        $objHTML->startElement ('li');
        $objHTML->text ('F: [%FILE%]');
        $objHTML->endElement ();
        $objHTML->startElement ('li');
        $objHTML->text ('L: [%LINE%]');
        $objHTML->endElement ();
        $objHTML->endElement ();

        // Set
        $objOutput = new S ($objHTML
        ->outputMemory ());

        // Transform
        $objOutput->doToken ('[%EMSG%]', $objErrorString);
        $objOutput->doToken ('[%FILE%]', $objErrorPath);
        $objOutput->doToken ('[%LINE%]', $objErrorPathNo);

        // Swtich
        switch ($objErrorType->toInt ()) {
            case 2:
            case 512:
                $objType =
                _T ('Warning');
                break;

            case 8:
            case 256:
            case 1024:
                $objType =
                _T ('Notice');
                break;

            case 4096:
                $objType =
                _T ('Recover');
                break;

            case 8191:
                $objType =
                _T ('Error');
                break;

            default:
                $objType =
                _T ('Segfault');
                break;
        }

        // Ouput the screen
        self::renderDeath (new S (__CLASS__), $objType,
        _T ('Fix your code!'), $objOutput);

        // Check
        if ($objErrorType->toInt () == E_ERROR         ||
        $objErrorType->toInt () == E_PARSE             ||
        $objErrorType->toInt () == E_CORE_WARNING      ||
        $objErrorType->toInt () == E_COMPILE_ERROR     ||
        $objErrorType->toInt () == E_COMPILE_WARNING   ||
        $objErrorType->toInt () == E_STRICT) {
            // Throws
            self::renderDeath (new S (__CLASS__), _T ('Segfault'),
            _T ('Fix your code!'), $objOutput);
        }
    }

    /**
     * Sets the internal status, shown an error has happened;
     *
     * @author Catalin Alexandru Zamfir <catalin.zamfir@raphpframework.ro>
     * @copyright Under the terms of the GNU General Public License v3
     * @version $Id: Error.php 1 2012-10-26 08:27:37Z root $
     */
    protected static final function setStatus () {
        // Switch
        switch (self::$objStatus
        ->toInt () == 1) {
            case TRUE:
                // Set
                self::$objStatus
                ->setInt (0);
                break;

            case FALSE:
                // Set
                self::$objStatus
                ->setInt (1);
                break;
        }
    }

    /**
     * Returns the internal error status;
     *
     * @author Catalin Alexandru Zamfir <catalin.zamfir@raphpframework.ro>
     * @copyright Under the terms of the GNU General Public License v3
     * @version $Id: Error.php 1 2012-10-26 08:27:37Z root $
     */
    public static final function getStatus () {
        // Return
        return self::$objStatus;
    }

    /**
     * Renders the SOD (screen of death) by parsing HTML with usefull information for the developer and echoing it out;
     *
     * @author Catalin Alexandru Zamfir <catalin.zamfir@raphpframework.ro>
     * @copyright Under the terms of the GNU General Public License v3
     * @version $Id: Error.php 1 2012-10-26 08:27:37Z root $
     */
    public static final function renderDeath (S $errFrom, S $errString = NULL, S $errTip = NULL,
    S $debugErrorString = NULL, B $errFATAL = NULL) {
        // Change
        if ($errFrom == 'System') {
            $debugErrorString = new S;
        }

        // Get contents
        $objOutput = new
        Contents (Architecture::pathTo
        (FORM_TP_DIR, 'HTML-Error.tp'));

        // Set
        Execution::setExeTime (new
        S (__FUNCTION__));

        // Status
        self::setStatus ();

        // Check
        if ($errFATAL != NULL &&
        $errFATAL->toBoolean () == TRUE) {
            // Set
            $errTip = _T ('Fix your code!');
            $errString = _T ('Segfault');
        }

        // Check
        if ($errFATAL != NULL &&
        $errFATAL->toBoolean () == TRUE) {
            // Erase
            Output::setBuffer (new A);
        } else {
            // Dump it to nowhere
            Output::discardStream (new
            B (TRUE));
        }

        // Parse the screen and set the proper debug information, helping the developer while at it
        $objOutput->doToken ('[%MICROTIME%]', Execution::getExeTime (new S ('Start'), new S (__FUNCTION__)));
        $objOutput->doToken ('[%ERROR_DATE%]', date (DATE_STRING, time ()));
        $objOutput->doToken ('[%ERROR_FROM_PHP%]', $debugErrorString);
        $objOutput->doToken ('[%MEMORY%]', memory_get_usage () / 1024);
        $objOutput->doToken ('[%ERROR_EMSG%]', $errString);
        $objOutput->doToken ('[%ERROR_FROM%]', $errFrom);
        $objOutput->doToken ('[%ERROR_ETIP%]', $errTip);
        $objOutput->doToken ('[%PID%]', getmypid ());

        // Check
        if (OB_GZIP == TRUE &&
        OB_GZIP_LEVEL > 0 &&
        OB_GZIP_LEVEL <= 9) {
            // Set
            Header::setKey (new S (Header
            ::CONTENT_ENCODING_GZIP),
            new S ('Content-Encoding'));
        }

        // Check
        if ($errFATAL != NULL &&
        $errFATAL->toBoolean () == TRUE) {
            // Check
            if (OB_GZIP == TRUE &&
            OB_GZIP_LEVEL > 0 &&
            OB_GZIP_LEVEL <= 9) {
                // Return (asume gzip)
                return (gzencode ($objOutput,
                OB_GZIP_LEVEL));
            } else {
                // Return
                return $objOutput;
            }
        } else {
            // Or die script now
            if (OB_GZIP == TRUE &&
            OB_GZIP_LEVEL > 0 &&
            OB_GZIP_LEVEL <= 9) {
                // Erase stream
                Output::eraseStream ();

                // Exit (asume gzip)
                exit (gzencode ($objOutput,
                OB_GZIP_LEVEL));
            } else {
                // Erase stream
                Output::eraseStream ();

                // Exit
                exit ($objOutput);
            }
        }
    }
}
?>
