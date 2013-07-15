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
 * Provides methods to construct URLs, either sensitive to _GET parameters or not;
 *
 * @author Catalin Alexandru Zamfir <catalin.zamfir@raphpframework.ro>
 * @copyright Under the terms of the GNU General Public License v3
 * @version $Id: Location.php 1 2012-10-26 08:27:37Z root $
 */
final class Location {
    /**
     * Containers of segments, offsets, CURL caches, queries and strings;
     *
     * @author Catalin Alexandru Zamfir <catalin.zamfir@raphpframework.ro>
     * @copyright Under the terms of the GNU General Public License v3
     * @version $Id: Location.php 1 2012-10-26 08:27:37Z root $
     */
    private static $objURLTrans = NULL;
    private static $objQuery  = NULL;
    private static $objGETAssoc = NULL;
    private static $objURLString = NULL;
    private static $objURLSegment = NULL;
    private static $objURLOffset = NULL;
    private static $objCURLCache = NULL;
    private static $objCURLProxy = NULL;

    // CONSTANTS
    const PURGED = '/[^a-zA-Z0-9ăîșțâ \-]/ui';
    const SEARCH = '#^/(.*)/$#';
    const TOKENS = '\\1';

    /**
     * Determines the current Location, parses it and returnes a redefined GET object;
     *
     * @author Catalin Alexandru Zamfir <catalin.zamfir@raphpframework.ro>
     * @copyright Under the terms of the GNU General Public License v3
     * @version $Id: Location.php 1 2012-10-26 08:27:37Z root $
     */
    public function __construct () {
        // Set
        self::$objURLTrans = new S ();

        // Modify the GET query
        self::$objQuery = new A (EXPLODE ('&',
        parse_URL ($_SERVER['REQUEST_URI'],
        PHP_URL_QUERY)));

        // Go
        self::explodeURL ();
    }

    /**
     * When working with URLs in the OLD format, you can CALL URLL::doConvert, to enable the processing (and redirection) of
     * OLD &key=var variables to the new /Key/Var/ URL types. This will ensure that you can still use the normal _GET array for both
     * types of URLs, keeping your project easy maintainable;
     */
    public static final function doConvert () {
        // Check
        if (self::$objQuery->doCount ()
        ->toInt () > 1) {
            // Foreach
            foreach (self::$objQuery as $objK => $objV) {
                // Explode
                $objKV = EXPLODE ('=', $objV);
                self::$objURLTrans->appendString (_WS)
                ->appendString (ucfirst ($objKV[0]))
                ->appendString (_WS)
                ->appendString ($objKV[1]);
            }

            // Redirect
            Header::setKey (Location::rewriteTo ()
            ->appendString (self::$objURLTrans),
            new S ('Location'));
        }
    }

    /**
     * Splits the URL (old-style) to the given key1/var1/key2/var2 schema;
     *
     * @author Elena Ramona <no_reply@raphpframework.ro>
     * @copyright Under the terms of the GNU General Public License v3
     * @version $Id: Location.php 1 2012-10-26 08:27:37Z root $
     */
    private static final function explodeURL () {
        // Set
        self::$objGETAssoc   = new A;
        self::$objURLOffset  = new I (1);
        self::$objURLString  = new S ('http');
        self::$objURLSegment = new A;

        // Switch
        switch ((isset ($_SERVER['HTTPS'])) && ($_SERVER['HTTPS'] == 'on')) {
            case TRUE:
                // Add a https:// in a secured HTTPS
                self::$objURLString->appendString ('s');
                self::$objURLString->appendString ('://');
                break;

            default:
                // Add the :// in a normal HTTP
                self::$objURLString->appendString ('://');
                break;
        }

        // SWitch
        switch ((isset ($_SERVER['SERVER_PORT'])) &&
        ($_SERVER['SERVER_PORT'] != '80')) {
            case TRUE:
                // Add :PORT to our STRING;
                self::$objURLString->appendString ($_SERVER['SERVER_NAME'] . ':'
                . $_SERVER['SERVER_PORT'] . $_SERVER['PHP_SELF']);

                // Increase offset
                self::$objURLOffset->doInc ();
                break;

            default:
                // Leave the string as it is
                self::$objURLString->appendString ($_SERVER['SERVER_NAME']
                . $_SERVER['PHP_SELF']);

                // Increase offset
                self::$objURLOffset->doInc ();
                break;
        }

        // Set an index;
        $objI = new I (-1);
        foreach (explode (_WS, preg_replace (self::SEARCH,
        self::TOKENS, self::$objURLString)) as $objV) {
            // Check
            if ($objV != _NONE) {
                // Set
                self::$objURLSegment[$objI
                ->doInc ()] = new S (trim ($objV));
            }
        }

        // Switch segments
        self::$objGETAssoc = self::fromURL (new I (self::countSegments (
        self::getSiteURL ())->toInt () + self::$objURLOffset->toInt ()));

        // Check
        if (self::$objGETAssoc instanceof A) {
            // Set
            $_GET = new A;

            // Foreach
            foreach (self::$objGETAssoc as $objK => $objV) {
                // Set & overwrite
                $_GET[$objK] = new S ((string) $objV);

                // Fixes
                $_GET[$objK] = $_GET[$objK]->decodeURL ();
                $_GET[$objK] = $_GET[$objK]->pregChange (self::PURGED, _NONE);
                $_GET[$objK] = $_GET[$objK]->trimLeft ();
                $_GET[$objK] = $_GET[$objK]->trimRight ();

                // Hacker proof
                if ($_GET[$objK]->toLength ()
                ->toInt () == 0) {
                    // Set
                    $_GET[$objK] = new S ('1');
                }
            }
        }
    }

    /**
     * Transforms the string, making it URL safe;
     *
     * @author Elena Ramona <no_reply@raphpframework.ro>
     * @copyright Under the terms of the GNU General Public License v3
     * @version $Id: Location.php 1 2012-10-26 08:27:37Z root $
     */
    public static final function getFrom (S $objURLString) {
        // Copy object
        $objURLString = $objURLString
        ->makeCopyObject ();

        // Return
        return $objURLString
        ->stripTags()->pregChange (self::PURGED, _NONE)
        ->trimBoth ()->pregChange ('/' . _SP . '/', '-');
    }

    /**
     * Rewrites the current URL, to the key & var pairs given as arguments;
     *
     * @author Elena Ramona <no_reply@raphpframework.ro>
     * @copyright Under the terms of GNU General Public License v3
     * @version $Id: Location.php 1 2012-10-26 08:27:37Z root $
     */
    public static final function rewriteTo (A $objKey = NULL, A $objVar = NULL) {
        // Check
        if ($objKey == NULL) {
            // Check & return
            if (isset ($_GET) == FALSE) { $_GET = new A; }
            if (($_GET instanceof A) == FALSE) { $_GET = new A ($_GET); }
            return self::getSiteURL (self::toURL (isset ($_GET) ?
            new A ($_GET->toArray ()) : new A));
        } else {
            // Check
            if ($objVar != NULL) {
                // Append
                $objGETAssoc = self::fromURL (new I (self
                ::countSegments (self::getSiteURL ())->toInt () +
                self::$objURLOffset->toInt ()));

                // Foreach
                foreach ($objKey as $objK => $objV) {
                    // Do a for-each loop;
                    $objGETAssoc[$objKey[$objK]] = $objVar[$objK];
                }

                // Return
                return self::getSiteURL (self
                ::toURL ($objGETAssoc));
            } else {
                // Remove
                $objGETAssoc = self::fromURL (new I (self
                ::countSegments (self::getSiteURL ())->toInt () +
                self::$objURLOffset->toInt ()));

                // Foreach
                foreach ($objKey as $objK => $objV) {
                    // Check
                    if (isset ($objGETAssoc[$objV])) {
                        // Unset
                        unset ($objGETAssoc[$objV]);
                    }
                }

                // Return
                return self::getSiteURL (self::toURL ($objGETAssoc));
            }
        }
    }

    /**
     * Rewrites the current URL, to the key & var pairs given as arguments, but erases any other pairs;
     *
     * @author Elena Ramona <no_reply@raphpframework.ro>
     * @copyright Under the terms of the GNU General Public License v3
     * @version $Id: Location.php 1 2012-10-26 08:27:37Z root $
     */
    public static final function staticTo (A $objKey = NULL, A $objVar = NULL) {
        // Check
        if ($objKey == NULL) {
            // Return
            return self::getSiteURL (self::toURL (isset ($_GET) ?
            new A ($_GET->toArray ()) : new A));
        } else {
            // Check
            if ($objVar != NULL) {
                // Append
                $objGETAssoc = new A;

                // Foreach
                foreach ($objKey as $objK => $objV) {
                    // Do a for-each loop;
                    $objGETAssoc[$objKey[$objK]] = $objVar[$objK];
                }

                // Return
                return self::getSiteURL (self
                ::toURL ($objGETAssoc));
            } else {
                // Remove
                $objGETAssoc = new A;
                foreach ($objKey as $objK => $objV) {
                    // Check
                    if (isset ($objGETAssoc[$objV])) {
                        // Unset
                        unset ($objGETAssoc[$objV]);
                    }
                }

                // Return
                return self::getSiteURL (self::toURL ($objGETAssoc));
            }
        }
    }

    /**
     * Parses the current URL to an associative array;
     *
     * @author Elena Ramona <no_reply@raphpframework.ro>
     * @copyright Under the terms of the GNU General Public License v3
     * @version $Id: Location.php 1 2012-10-26 08:27:37Z root $
     */
    private static final function fromURL (I $segmentNumber) {
        $getURLSegments = new A (array_slice (self::$objURLSegment
        ->toArray (), ($segmentNumber->toInt () - 1)));
        $objI = new I (0);
        $lastURLVar = new S (_NONE);
        $returnedArray = new A;

        // Foreach
        foreach ($getURLSegments as $objV) {
            if ($objI->toInt () % 2) {
                $returnedArray[(string)
                $lastURLVar] = $objV;
            } else {
                $returnedArray[$objV] = new B (FALSE);
                $lastURLVar->setString ($objV);
            }
            // Increment;
            $objI->doInc ();
        }

        // Return
        return $returnedArray;
    }

    /**
     * Joins the associative array into an URL string;
     *
     * @author Elena Ramona <no_reply@raphpframework.ro>
     * @copyright Under the terms of the GNU General Public License v3
     * @version $Id: Location.php 1 2012-10-26 08:27:37Z root $
     */
    private static final function toURL (A $passedArray) {
        // Set
        $objTemporary = new A;

        // Foreach
        foreach ($passedArray as $objK => $objV) {
            // Set
            $objTemporary[] = $objK;
            $objTemporary[] = $objV;
        }

        // Return
        return $objTemporary
        ->fromArrayToString (_WS);
    }

    /**
     * Returns the project URL;
     *
     * @author Elena Ramona <no_reply@raphpframework.ro>
     * @copyright Under the terms of the GNU General Public License v3
     * @version $Id: Location.php 1 2012-10-26 08:27:37Z root $
     */
    private static final function getSiteURL (S $websiteURL = NULL) {
        // Set (fix a weird bug when index not set as string)
        $pageSuffix = _S ($_SERVER['SCRIPT_FILENAME'])->makeCopyObject ()
        ->doToken (Architecture::getRoot (), _NONE);

        // Return
        return Architecture
        ::pathTo (Architecture::getHost (),
        $pageSuffix, $websiteURL)
        ->doToken ($pageSuffix . _WS, _NONE);
    }

    /**
     * Checks paths and returns to a base URL, to restrict from saved bookmarks;
     *
     * @author Elena Ramona <no_reply@raphpframework.ro>
     * @copyright Under the terms of the GNU General Public License v3
     * @version $Id: Location.php 1 2012-10-26 08:27:37Z root $
     */
    public static final function doCheckPath () {
        // Check
        if ($_GET->doCount ()->toInt () != 0) {
            $objFromGET = new A;
            foreach ($_GET as $objK => $objV) {
                $objFromGET[] = $objK;
            }

            // Redirect
            Header::setKey (Location
            ::rewriteTo ($objFromGET),
            new S ('Location'));
        }
    }

    /**
     * Counts number of segments in the URL given to it;
     *
     * @copyright Elena Ramona <no_reply@raphpframework.ro>
     * @version $Id: Location.php 1 2012-10-26 08:27:37Z root $
     */
    private static final function countSegments (S $websiteURL) {
        // Execute the count;
        if ($websiteURL != _NONE) {
            $objK = new I (0);

            // Foreach
            foreach (explode (_WS, preg_replace (self::SEARCH,
            self::TOKENS, $websiteURL)) as $objV) {
                // Check
                if ($objV != _NONE) {
                    $objK->doInc ();
                }
            }

            // Set
            return $objK;
        } else {
            // Set
            $objK = new I (0);

            // Return
            return $objK;
        }
    }

    /**
     * Returns the cURL headers;
     *
     * @author Elena Ramona <no_reply@raphpframework.ro>
     * @copyright Under the terms of the GNU General Public License v3
     * @version $Id: Location.php 1 2012-10-26 08:27:37Z root $
     */
    public static final function getCURLHeaders (S $objURL, I $objType = NULL) {
        // Instantiate
        $objCURL = CURL_INIT ();

        // Counter
        static $objCURLCounter = 0;

        // Set the URL
        CURL_SETOPT ($objCURL, CURLOPT_URL, $objURL);
        CURL_SETOPT ($objCURL, CURLOPT_RETURNTRANSFER, 1);
        CURL_SETOPT ($objCURL, CURLOPT_TIMEOUT, 60);
        CURL_SETOPT ($objCURL, CURLOPT_CONNECTTIMEOUT, 60);
        CURL_SETOPT ($objCURL, CURLOPT_MAXREDIRS, 3);
        CURL_SETOPT ($objCURL, CURLOPT_FOLLOWLOCATION, TRUE);
        CURL_SETOPT ($objCURL, CURLOPT_AUTOREFERER, TRUE);
        CURL_SETOPT ($objCURL, CURLOPT_HEADER, TRUE);
        CURL_SETOPT ($objCURL, CURLOPT_NOBODY, TRUE);

        // Proxy
        if (self::$objCURLProxy != NULL) {
            // Set
            CURL_SETOPT ($objCURL, CURLOPT_PROXY, self::$objCURLProxy);
        }

        // Execute
        $objCURLWas = new S ((string) CURL_EXEC ($objCURL));
        CURL_CLOSE ($objCURL);

        // Return
        if ($objType == NULL) {
            // Go
            return $objCURLWas->trimBoth ();
        } else {
            // Set
            $objCURLHeader = new A;
            $objCURLTemper = new A;

            // Go
            foreach ($objCURLWas->trimBoth ()
            ->fromStringToArray (_N_) as $objK => $objV) {
                $objCURLTemper[_S ($objV)
                ->fromStringToArray (':', 2)->offsetGet (0)] = _S ($objV)
                ->fromStringToArray (':', 2)->offsetGet (1);
            }

            // Rempat
            $objInc = 0;
            foreach ($objCURLTemper as $objK => $objV) {
                // If
                if ($objV instanceof A) {
                    // Go
                    if ($objV->doCount ()->toInt () == 0) {
                        // Go
                        $objCURLTemper[$objInc] = $objK;

                        // Unset
                        UNSET ($objCURLTemper[$objK]);

                        // Inc
                        $objInc = $objInc + 1;
                    }
                }
            }

            // Go
            foreach ($objCURLTemper as $objK => $objV) {
                $objCURLHeader[$objK] = _S ((string) $objV)->trimBoth ()
                ->doToken (_N_, _NONE);
            }

            // Recursive
            if ($objCURLHeader->doCount ()->toInt () == 0) {
                // Count
                $objCURLCounter++;

                // Check
                if ($objCURLCounter < 20) {
                    // Return
                    return self::getCURLHeaders ($objURL, $objType);
                } else {
                    // Set
                    $objCURLCounter = 0;

                    // Return
                    return new A;
                }
            } else {
                // Return
                return $objCURLHeader;
            }
        }
    }

    /**
     * Checks to see if the given URL is a file;
     *
     * @author Elena Ramona <no_reply@raphpframework.ro>
     * @copyright Under the terms of the GNU General Public License v3
     * @version $Id: Location.php 1 2012-10-26 08:27:37Z root $
     */
    public static final function checkIfURLIsFile (S $objURL) {
        // Instantiate
        $objCURL = CURL_INIT ();

        // Set the URL
        CURL_SETOPT ($objCURL, CURLOPT_URL, $objURL);
        CURL_SETOPT ($objCURL, CURLOPT_RETURNTRANSFER, 1);
        CURL_SETOPT ($objCURL, CURLOPT_TIMEOUT, 5);
        CURL_SETOPT ($objCURL, CURLOPT_CONNECTTIMEOUT, 5);
        CURL_SETOPT ($objCURL, CURLOPT_FOLLOWLOCATION, FALSE);
        CURL_SETOPT ($objCURL, CURLOPT_MAXREDIRS, 0);
        CURL_SETOPT ($objCURL, CURLOPT_AUTOREFERER, TRUE);

        // Proxy
        if (self::$objCURLProxy != NULL) {
            // Set
            CURL_SETOPT ($objCURL, CURLOPT_PROXY,
            self::$objCURLProxy);
        }

        // Execute
        $objCURLWas = new S ((string) CURL_EXEC ($objCURL));
        CURL_CLOSE ($objCURL);

        // Return
        return $objCURLWas->toLength ()->toInt () == 0 ?
        new B (FALSE) : new B (TRUE);
    }

    /**
     * Does an "async" request to a given URL. In short, it timesouts after 1 second (thus the "async");
     *
     * @author Catalin Alexandru Zamfir <catalin.zamfir@raphpframework.ro>
     * @copyright Under the terms of the GNU General Public License v3
     * @version $Id: Location.php 1 2012-10-26 08:27:37Z root $
     */
    public static final function asyncCURLRequest (S $objURL) {
        // Instantiate
        $objCURL = CURL_INIT ();

        // Set some options
        CURL_SETOPT ($objCURL, CURLOPT_URL, $objURL);
        CURL_SETOPT ($objCURL, CURLOPT_RETURNTRANSFER, 0);
        CURL_SETOPT ($objCURL, CURLOPT_TIMEOUT, 1);
        CURL_SETOPT ($objCURL, CURLOPT_BINARYTRANSFER, 1);
        CURL_SETOPT ($objCURL, CURLOPT_ENCODING, 'gzip');
        CURL_SETOPT ($objCURL, CURLOPT_CONNECTTIMEOUT, 5);
        CURL_SETOPT ($objCURL, CURLOPT_FOLLOWLOCATION, TRUE);
        CURL_SETOPT ($objCURL, CURLOPT_MAXREDIRS, 5);
        CURL_SETOPT ($objCURL, CURLOPT_AUTOREFERER, TRUE);
        CURL_SETOPT ($objCURL, CURLOPT_DNS_USE_GLOBAL_CACHE, 1);
        CURL_SETOPT ($objCURL, CURLOPT_TCP_NODELAY, 1);
        CURL_SETOPT ($objCURL, CURLOPT_USERAGENT, $_SERVER['HTTP_USER_AGENT']);

        // Execute & CLOSE
        $objCURLWas = new S ((string)
        CURL_EXEC ($objCURL));
        CURL_CLOSE ($objCURL);

        // Return
        return $objCURLWas;
    }

    /**
     * Downloads the given URL, return its contents as a string;
     *
     * @author Catalin Alexandru Zamfir <catalin.zamfir@raphpframework.ro>
     * @copyright Under the terms of the GNU General Public License v3
     * @version $Id: Location.php 1 2012-10-26 08:27:37Z root $
     */
    public static final function getCURLFile (S $objURL, $objProgressCALLBack = NULL) {
        // Instantiate
        $objCURL = CURL_INIT ();

        // Set some options
        CURL_SETOPT ($objCURL, CURLOPT_URL, $objURL);
        CURL_SETOPT ($objCURL, CURLOPT_RETURNTRANSFER, 1);
        CURL_SETOPT ($objCURL, CURLOPT_TIMEOUT, 60);
        CURL_SETOPT ($objCURL, CURLOPT_BINARYTRANSFER, 1);
        CURL_SETOPT ($objCURL, CURLOPT_ENCODING, 'gzip');
        CURL_SETOPT ($objCURL, CURLOPT_CONNECTTIMEOUT, 30);
        CURL_SETOPT ($objCURL, CURLOPT_FOLLOWLOCATION, TRUE);
        CURL_SETOPT ($objCURL, CURLOPT_MAXREDIRS, 5);
        CURL_SETOPT ($objCURL, CURLOPT_AUTOREFERER, TRUE);
        CURL_SETOPT ($objCURL, CURLOPT_DNS_USE_GLOBAL_CACHE, 1);
        CURL_SETOPT ($objCURL, CURLOPT_TCP_NODELAY, 1);
        CURL_SETOPT ($objCURL, CURLOPT_USERAGENT, $_SERVER['HTTP_USER_AGENT']);

        // Proxy
        if (self::$objCURLProxy != NULL) {
            // Set
            CURL_SETOPT ($objCURL, CURLOPT_PROXY,
            self::$objCURLProxy);
        }

        // Callback for progress
        if ($objProgressCALLBack != NULL) {
            // Set
            CURL_SETOPT ($objCURL,
            CURLOPT_NOPROGRESS,
            FALSE);

            // Progress
            CURL_SETOPT ($objCURL,
            CURLOPT_PROGRESSFUNCTION,
            $objProgressCALLBack);

            // Buffer
            CURL_SETOPT ($objCURL,
            CURLOPT_BUFFERSIZE,
            8192);
        } else {
            // Set
            CURL_SETOPT ($objCURL,
            CURLOPT_NOPROGRESS,
            TRUE);
        }

        // Execute & CLOSE
        $objCURLWas = new S ((string)
        CURL_EXEC ($objCURL));
        CURL_CLOSE ($objCURL);

        // Return
        return $objCURLWas;
    }

    /**
     * Returns the cURL path as a file;
     *
     * @author Catalin Alexandru Zamfir <catalin.zamfir@raphpframework.ro>
     * @copyright Under the terms of the GNU General Public License v3
     * @version $Id: Location.php 1 2012-10-26 08:27:37Z root $
     */
    public static final function getImediateCURLFile (S $objURL, A $objFinishedCALLBack, A $objCustomData = NULL) {
        // Cache
        if (self::$objCURLCache == NULL) {
            // Set
            self::$objCURLCache = new CURL (10000,
            Array (CURLOPT_TIMEOUT => 60,
            CURLOPT_BINARYTRANSFER => 1,
            CURLOPT_ENCODING => 'gzip',
            CURLOPT_CONNECTTIMEOUT => 30,
            CURLOPT_FOLLOWLOCATION => TRUE,
            CURLOPT_MAXREDIRS => 5,
            CURLOPT_AUTOREFERER => TRUE,
            CURLOPT_DNS_USE_GLOBAL_CACHE => 1,
            CURLOPT_TCP_NODELAY => 1));
        }

        // Check
        if (isset ($objFinishedCALLBack['CURL'])) {
            $objCBack = _S ('getImediateCURLSaveAsFile')
            ->prependString (_DC)->prependString (__CLASS__);
        } else {
            $objCBack = $objFinishedCALLBack
            ->toArray ();
        }

        // Go
        self::$objCURLCache->startRequest ($objURL,
        $objCBack, $objCustomData->toArray ());
    }

    /**
     * Sets a cURL proxy;
     *
     * @author Catalin Alexandru Zamfir <catalin.zamfir@raphpframework.ro>
     * @copyright Under the terms of the GNU General Public License v3
     * @version $Id: Location.php 1 2012-10-26 08:27:37Z root $
     */
    public static final function setCURLProxy (S $objCURLProxy) {
        // Check
        if ($objCURLProxy->toLength ()->toInt () != 0) {
            self::$objCURLProxy = $objCURLProxy;
        }
    }

    /**
     * Unsets a cURL proxy;
     *
     * @author Catalin Alexandru Zamfir <catalin.zamfir@raphpframework.ro>
     * @copyright Under the terms of the GNU General Public License v3
     * @version $Id: Location.php 1 2012-10-26 08:27:37Z root $
     */
    public static final function unsetCURLProxy () {
        // Set
        self::$objCURLProxy = NULL;
    }

    /**
     * Returns a cURL path as a string
     *
     * @author Catalin Alexandru Zamfir <catalin.zamfir@raphpframework.ro>
     * @copyright Under the terms of the GNU General Public License v3
     * @version $Id: Location.php 1 2012-10-26 08:27:37Z root $
     */
    public static final function getCURLString (S $objURL, $objProgressCALLBack = NULL) {
        // Return
        return self::getCURLFile ($objURL, $objProgressCALLBack);
    }

    /**
     * Returns immediate cURL as a file;
     *
     * @author Catalin Alexandru Zamfir <catalin.zamfir@raphpframework.ro>
     * @copyright Under the terms of the GNU General Public License v3
     * @version $Id: Location.php 1 2012-10-26 08:27:37Z root $
     */
    public static final function getImediateCURLSaveAsFile ($objContent, $objURL, $objCALLBack, $objCustomData) {
        // Save
        $objCustomData['Save']
        ->filePutContents ($objContent);

        // Create
        touch ($objCustomData['Save'], $objCustomData['Headers']
        ->offsetGet ('Expires')->toUnixTimestamp ()->toInt ());
    }

    /**
     * Flush immediate cURL transfers;
     *
     * @author Catalin Alexandru Zamfir <catalin.zamfir@raphpframework.ro>
     * @copyright Under the terms of the GNU General Public License v3
     * @version $Id: Location.php 1 2012-10-26 08:27:37Z root $
     */
    public static final function getImediateCURLTransfersFinish () {
        if (self::$objCURLCache != NULL) {
            self::$objCURLCache->finishAllRequests ();
        }
    }
}
?>
