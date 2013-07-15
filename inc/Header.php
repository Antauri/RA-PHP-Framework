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
 * Provides methods for working with the request and/or response header;
 *
 * @author Catalin Alexandru Zamfir <catalin.zamfir@raphpframework.ro>
 * @copyright Under the terms of the GNU General Public License v3
 * @version $Id: Header.php 1 2012-10-26 08:27:37Z root $
 */
final class Header {
    /**
     * Container of request headers sent to us;
     *
     * @author Catalin Alexandru Zamfir <catalin.zamfir@raphpframework.ro>
     * @copyright Under the terms of the GNU General Public License v3
     * @version $Id: Header.php 1 2012-10-26 08:27:37Z root $
     */
    private static $objRequestHeaders = NULL;

    /* CONSTANTS: Statuses */
    const HTTP_STATUS_OK = 'HTTP/1.1 200 OK';
    const MOVED_PERMANENTLY = 'HTTP/1.1 301 Moved Permanently';
    const MOVED_TEMPORARILY = 'HTTP/1.1 302 Moved Temporarily';
    const BAD_REQUEST = 'HTTP/1.1 400 Bad Request';
    const AUTHORIZATION_REQUIRED = 'HTTP/1.1 401 Authorization Required';
    const PAYMENT_REQUIRED = 'HTTP/1.1 402 Payment Required';
    const FORBIDDEN = 'HTTP/1.1 403 Forbidden';
    const NOT_FOUND = 'HTTP/1.1 404 Not Found';
    const METHOD_NOT_ALLOWED = 'HTTP/1.1 405 Method Not Allowed';
    const NOT_ACCEPTABLE = 'HTTP/1.1 406 Not Acceptable';
    const PROXY_AUTHENTICATION_REQ = 'HTTP/1.1 407 Proxy Authentication Required';
    const REQUEST_TIMED_OUT = 'HTTP/1.1 408 Request Time-out';
    const CONFLICT = 'HTTP/1.1 409 Conflict';
    const GONE = 'HTTP/1.1 410 Gone';
    const LENGTH_REQUIRED = 'HTTP/1.1 411 Length Required';
    const PRECONDITION_FAILED = 'HTTP/1.1 412 Precondition Failed';
    const REQUEST_ENTITY_TO_LARGE = 'HTTP/1.1 413 Request Entity Too Large';
    const REQUEST_URI_TOO_LARGE = 'HTTP/1.1 414 Request-URI Too Large';
    const UNSUPPOTED_MEDIA_TYPE = 'HTTP/1.1 415 Unsupported Media Type';
    const REQ_RANGE_NOT_SATISFIABLE = 'HTTP/1.1 416 Requested Range Not Satisfiable';
    const EXPECTATION_FAILED = 'HTTP/1.1 417 Expectation Failed';
    const UNPROCESSABLE_ENTITY = 'HTTP/1.1 422 Unprocessable Entity';
    const LOCKED = 'HTTP/1.1 423 Locked';
    const FAILED_DEPENDENCY = 'HTTP/1.1 424 Failed Dependency';
    const NO_CODE = 'HTTP/1.1 425 No code';
    const UPGRADE_REQUIRED = 'HTTP/1.1 426 Upgrade Required';
    const INTERNAL_SERVER_ERROR = 'HTTP/1.1 500 Internal Server Error';
    const METHOD_NOT_IMPLEMENTED = 'HTTP/1.1 501 Method Not Implemented';
    const BAD_GATEWAY = 'HTTP/1.1 502 Bad Gateway';
    const SERVICE_TEMP_UNAVAILABLE = 'HTTP/1.1 503 Service Temporarily Unavailable';
    const GATEWAY_TIMED_OUT = 'HTTP/1.1 504 Gateway Time-out';
    const HTTP_VERSION_NOT_SUPPORTED = 'HTTP/1.1 505 HTTP Version Not Supported';
    const VARIANT_ALSO_NEGOTIATES = 'HTTP/1.1 506 Variant Also Negotiates';
    const INSUFFICIENT_STORAGE = 'HTTP/1.1 507 Insufficient Storage';
    const UNUSED_508 = 'HTTP/1.1 508 unused';
    const UNUSED_509 = 'HTTP/1.1 509 unused';
    const NOT_EXTENDED = 'HTTP/1.1 510 Not Extended';

    /* CONSTANTS: Content-Types */
    const CONTENT_TYPE_TEXT_PLAIN = 'text/plain';
    const CONTENT_TYPE_TEXT_CSV = 'text/csv';
    const CONTENT_TYPE_TEXT_HTML = 'text/html';
    const CONTENT_TYPE_TEXT_XML = 'text/xml';
    const CONTENT_TYPE_IMAGE_GIF = 'image/gif';
    const CONTENT_TYPE_IMAGE_JPEG = 'image/jpeg';
    const CONTENT_TYPE_APPLICATION_OGG = 'application/ogg';

    /* CONSTANTS: Content-Encodings */
    const CONTENT_ENCODING_GZIP = 'gzip';
    const CONTENT_ENCODING_DEFLATE = 'deflate';

    /**
     * Saves the request headers and sets no-cache headers in effect;
     *
     * @author Catalin Alexandru Zamfir <catalin.zamfir@raphpframework.ro>
     * @copyright Under the terms of the GNU General Public License v3
     * @version $Id: Header.php 1 2012-10-26 08:27:37Z root $
     */
    public function __construct () {
        // Get
        self::getRequestHeaders ();
        self::setNoCache ();
    }

    /**
     * Sets a key pair (first content, then type) to the header;
     *
     * @author Catalin Alexandru Zamfir <catalin.zamfir@raphpframework.ro>
     * @copyright Under the terms of the GNU General Public License v3
     * @link http://php.net/header
     * @version $Id: Header.php 1 2012-10-26 08:27:37Z root $
     */
    public static final function setKey (S $headerContent, S $headerType) {
        // Check
        if (headers_sent ()) {
            // Throws
            throw new HeadersAlreadySentException;
        } else {
            // Switch
            switch ($headerType) {
                // Commons
                case 'Location':
                    // Require
                    header ($headerType
                    ->makeCopyObject ()
                    ->appendString (_CL)
                    ->appendString ($headerContent));

                    // Stop output now
                    Output::stopStream ();
                    break;

                default:
                    // Require
                    header ($headerType
                    ->makeCopyObject ()
                    ->appendString (_CL)
                    ->appendString ($headerContent));

                    // BK;
                    break;
            }
        }

        // Return
        return new B (TRUE);
    }

    /**
     * Sets a string (non key/pair) to the header;
     *
     * @author Catalin Alexandru Zamfir <catalin.zamfir@raphpframework.ro>
     * @copyright Under the terms of the GNU General Public License
     * @link http://php.net/header
     * @version $Id: Header.php 1 2012-10-26 08:27:37Z root $
     */
    public static final function setStr (S $headerString) {
        // Check
        if (headers_sent ()) {
            // Throws
            throw new HeadersAlreadySentException;
        } else {
            // Require
            header ($headerString);
        }

        // Return
        return new B (TRUE);
    }

    /**
     * Sets no-cache headers, cause we're outputing from PHP and want freshness;
     *
     * @author Catalin Alexandru Zamfir <catalin.zamfir@raphpframework.ro>
     * @copyright Under the terms of the GNU General Public License
     * @link http://php.net/header
     * @version $Id: Header.php 1 2012-10-26 08:27:37Z root $
     */
    private static final function setNoCache () {
        // Set
        self::setKey (new S ('no-store, no-cache, must-revalidate'),  new S ('Cache-Control'));
        self::setKey (new S ('pre-check=0, post-check=0, max-age=0'), new S ('Cache-Control'));
        self::setKey (new S ('no-cache'), new S ('Pragma'));
    }

    /**
     * Returns the headers that came with the current request, and caches them;
     *
     * @author Catalin Alexandru Zamfir <catalin.zamfir@raphpframework.ro>
     * @copyright Under the terms of the GNU General Public License v3
     * @version $Id: Header.php 1 2012-10-26 08:27:37Z root $
     */
    public static final function getRequestHeaders () {
        // Check
        if (self::$objRequestHeaders == NULL) {
            // Determine if Apache exists
            if (function_exists ('apache_request_headers')) {
                // Set the headers
                self::$objRequestHeaders = new
                A (apache_request_headers ());

                // Foreach
                foreach (self::$objRequestHeaders
                as $objK => $objV) {
                    // Set
                    self::$objRequestHeaders[$objK] = new S ($objV);
                }
            } else {
                // Throws
                throw new MethodNotCallableException ();
            }
        }

        // Return
        return self::$objRequestHeaders;
    }
}
?>
