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
 * Provides methods for working with templates and caching their execution;
 *
 * @author Catalin Alexandru Zamfir <catalin.zamfir@raphpframework.ro>
 * @copyright Under the terms of the GNU General Public License v3
 * @version $Id: Template.php 1 2012-10-26 08:27:37Z root $
 */
class Template extends M {
    /**
     * Container of appeneded title strings;
     *
     * @author Catalin Alexandru Zamfir <catalin.zamfir@raphpframework.ro>
     * @copyright Under the terms of the GNU General Public License v3
     * @version $Id: Template.php 1 2012-10-26 08:27:37Z root $
     */
    private static $objTitle = NULL;

    /**
     * Flag for title direction;
     *
     * @author Catalin Alexandru Zamfir <catalin.zamfir@raphpframework.ro>
     * @copyright Under the terms of the GNU General Public License v3
     * @version $Id: Template.php 1 2012-10-26 08:27:37Z root $
     */
    private static $objDirection = NULL;

    /**
     * Container of CSS paths;
     *
     * @author Catalin Alexandru Zamfir <catalin.zamfir@raphpframework.ro>
     * @copyright Under the terms of the GNU General Public License v3
     * @version $Id: Template.php 1 2012-10-26 08:27:37Z root $
     */
    private static $objCSS = NULL;

    /**
     * Container of JSS paths;
     *
     * @author Catalin Alexandru Zamfir <catalin.zamfir@raphpframework.ro>
     * @copyright Under the terms of the GNU General Public License v3
     * @version $Id: Template.php 1 2012-10-26 08:27:37Z root $
     */
    private static $objJSS = NULL;

    /**
     * Container of meta-tags;
     *
     * @author Catalin Alexandru Zamfir <catalin.zamfir@raphpframework.ro>
     * @copyright Under the terms of the GNU General Public License v3
     * @version $Id: Template.php 1 2012-10-26 08:27:37Z root $
     */
    private static $objTAG = NULL;

    /**
     * Container of meta-http-equiv tags;
     *
     * @author Catalin Alexandru Zamfir <catalin.zamfir@raphpframework.ro>
     * @copyright Under the terms of the GNU General Public License v3
     * @version $Id: Template.php 1 2012-10-26 08:27:37Z root $
     */
    private static $objEQV = NULL;

    /**
     * Container of link tags (atom, rss and more);
     *
     * @author Catalin Alexandru Zamfir <catalin.zamfir@raphpframework.ro>
     * @copyright Under the terms of the GNU General Public License v3
     * @version $Id: Template.php 1 2012-10-26 08:27:37Z root $
     */
    private static $objLNK = NULL;

    /**
     * Container of document type;
     *
     * @author Catalin Alexandru Zamfir <catalin.zamfir@raphpframework.ro>
     * @copyright Under the terms of the GNU General Public License v3
     * @version $Id: Template.php 1 2012-10-26 08:27:37Z root $
     */
    private static $objDocType = NULL;

    /**
     * Switch for the container status (not shown if error);
     *
     * @author Catalin Alexandru Zamfir <catalin.zamfir@raphpframework.ro>
     * @copyright Under the terms of the GNU General Public License v3
     * @version $Id: Template.php 1 2012-10-26 08:27:37Z root $
     */
    private static $objContainerSwitch = NULL;

    /**
     * Container for paths that are executing;
     *
     * @author Catalin Alexandru Zamfir <catalin.zamfir@raphpframework.ro>
     * @copyright Under the terms of the GNU General Public License v3
     * @version $Id: Template.php 1 2012-10-26 08:27:37Z root $
     */
    private static $objPaths = NULL;

    /**
     * Hack for disabling of gzip upon request or other conditions;
     *
     * @author Catalin Alexandru Zamfir <catalin.zamfir@raphpframework.ro>
     * @copyright Under the terms of the GNU General Public License v3
     * @version $Id: Template.php 1 2012-10-26 08:27:37Z root $
     */
    private static $objGzipDisabled = false;

    /**
     * Constructs the templating mechanism, sets requirements, a default doc-type, gzip headers and shutdown method;
     *
     * @author Catalin Alexandru Zamfir <catalin.zamfir@raphpframework.ro>
     * @copyright Under the terms of the GNU General Public License v3
     * @version $Id: Template.php 1 2012-10-26 08:27:37Z root $
     */
    public function __construct () {
        // Set
        self::$objTitle = new A;
        self::$objCSS = new A;
        self::$objJSS = new A;
        self::$objTAG = new A;
        self::$objEQV = new A;
        self::$objLNK = new A;
        self::$objPaths = new A;
        self::$objDirection = new I (0);
        self::$objContainerSwitch = new I (1);

        // Set the document type
        self::setDocumentType (new S ('html5'));

        // Check
        if ($_GET->offsetExists (_T ('Method'))) {
        	// Switch
        	switch ($_GET
			->offsetGet (_T ('Method'))) {
				case 'GetUpdates':
					// Stop
					self::disableGzip ();
					break;

        		case 'GetAsBinary':
        			// Check
        			if ($_GET->offsetExists (_T ('What'))) {
        				// Switch
        				switch ($_GET
						->offsetGet (_T ('What'))) {
        					// Raw
        					case _T ('Raw'):
							case _T ('Image'):
							case _T ('Media'):
        						// Stop
        						self::disableGzip ();
        						break;
        				}
        			}
        			// BK;
        			break;
        	}
        }

        // Headers & registrations
        self::setGzippedOutput (new S ('Header'));
        Architecture::onShutdown (new S ('Template::setOutput'));
    }

    /**
     * Outputs buffer (from Output) at script end (registered through onShutdown);
     *
     * @author Catalin Alexandru Zamfir <catalin.zamfir@raphpframework.ro>
     * @copyright Under the terms of the GNU General Public License v3
     * @version $Id: Template.php 1 2012-10-26 08:27:37Z root $
     */
    public static final function setOutput () {
        // Discard
        Output::eraseStream ();

        // Check
        if (self::getStatus ()->toInt () == 1 &&
        Error::getStatus ()->toInt () != 1) {
            // Set the container
            Output::getBuffer ()
            ->arrayUnShift (self::getDocType ()
            ->appendString (self::getHeadContainer ())
            ->appendString (self::getMetaEQVHeader ())
            ->appendString (self::getTitle ())
            ->appendString (self::getMetaTAGHeader ())
            ->appendString (self::getCSSHeader ())
            ->appendString (self::getMetaLNKHeader ())
            ->appendString (self::getEndHeadContainer ()));

            // End </body> output
            Output::appendBuffer (self::getJSSHeader ()
            ->appendString (self::getHTMLEnd ()));
        }

        // Output (gziped or not)
        self::setGzippedOutput (new S ('Content'));
    }

    /**
     * Appends the current string to the title;
     *
     * @author Catalin Alexandru Zamfir <catalin.zamfir@raphpframework.ro>
     * @copyright Under the terms of the GNU General Public License v3
     * @version $Id: Template.php 1 2012-10-26 08:27:37Z root $
     */
    protected static final function manageTTL (S $webPageStringTitle) {
        // Set
        self::$objTitle[] =
        $webPageStringTitle
        ->stripTags ();
    }

    /**
     * Switches the order of the appending in the title;
     *
     * @author Catalin Alexandru Zamfir <catalin.zamfir@raphpframework.ro>
     * @copyright Under the terms of the GNU General Public License v3
     * @version $Id: Template.php 1 2012-10-26 08:27:37Z root $
     */
    protected static final function switchTTL () {
        // Switch
        switch (self::$objDirection
        ->toInt () == 0) {
            case TRUE:
                // 1
                self::$objDirection
                ->setInt (1);
                break;

            case FALSE:
                // 0
                self::$objDirection
                ->setInt (0);
                break;
        }
    }

    /**
     * Returns the joined title, surrounded with proper tags, for output;
     *
     * @author Catalin Alexandru Zamfir <catalin.zamfir@raphpframework.ro>
     * @copyright Under the terms of the GNU General Public License v3
     * @version $Id: Template.php 1 2012-10-26 08:27:37Z root $
     */
    private static final function getTitle () {
        // Set
        $objHTML = new XMLWriter;
        $objHTML->openMemory ();
        $objHTML->setIndent (true);

        // Tag
        $objHTML->startElement ('title');
        $objHTML->text ((self::$objDirection->toInt () == 1)   ?
        self::$objTitle->arrayReverse ()->fromArrayToString (_DCSP) :
        self::$objTitle->fromArrayToString (_DCSP));
        $objHTML->endElement ();

        // Return
        return new S ($objHTML
        ->outputMemory ());
    }

    /**
     * Appends the given (css) Path to the buffer;
     *
     * @author Catalin Alexandru Zamfir <catalin.zamfir@raphpframework.ro>
     * @copyright Under the terms of the GNU General Public License v3
     * @version $Id: Template.php 1 2012-10-26 08:27:37Z root $
     */
    protected static final function manageCSS (Path $objPath, B $objSetOrUnset = NULL) {
        // Check
        if ($objSetOrUnset == NULL) {
            $objSetOrUnset = new
            B (TRUE);
        }

        // Hash
        $objPathTag =
        Hasher::getUniqueHash (new
        S ('sha512'), $objPath);

        // Check
        if ($objSetOrUnset
        ->toBoolean () == TRUE) {
            // Check
            if (!(self::$objCSS
            ->offsetExists ($objPathTag))) {
                // Check
                if (_S ((string) $objPath)
                ->findPos ('http://') instanceof B) {
                    // Set
                    self::$objCSS->offsetSet ($objPathTag,
                    $objPath
                    ->prependString (Architecture
                    ::pathTo (Architecture::getHost ())
                    ->appendString (_S)));
                } else {
                    // Set
                    self::$objCSS->offsetSet ($objPathTag,
                    $objPath);
                }
            } else {
                // Throws
                throw new HeaderCSSFileAlreadySetException;
            }
        } else {
            // Check
            if (!(self::$objCSS
            ->offsetExists ($objPathTag))) {
                // Set
                self::$objCSS
                ->offsetUnset ($objPathTag);
            } else {
                // Throws
                throw new HeaderCSSFileNotSetException;
            }
        }
    }

    /**
     * Returns the joined (css) buffer;
     *
     * @author Catalin Alexandru Zamfir <catalin.zamfir@raphpframework.ro>
     * @copyright Under the terms of the GNU General Public License v3
     * @version $Id: Template.php 1 2012-10-26 08:27:37Z root $
     */
    private static final function getCSSHeader () {
        // Set
        $objHTML = new XMLWriter;
        $objHTML->openMemory ();
        $objHTML->setIndent (true);

        // Foreach
        foreach (self::$objCSS as $objK => $objV) {
            // Set
            $objHTML->startElement ('link');
            $objHTML->startAttribute ('rel');
            $objHTML->text ('stylesheet');
            $objHTML->endAttribute ();
            $objHTML->startAttribute ('href');
            $objHTML->text ($objV);
            $objHTML->endAttribute ();
            $objHTML->endElement ();
        }

        // Return
        return new S ($objHTML
        ->outputMemory ());
    }

    /**
     * Appends the given LINK tag to the buffer;
     *
     * @author Catalin Alexandru Zamfir <catalin.zamfir@raphpframework.ro>
     * @copyright Under the terms of the GNU General Public License v3
     * @version $Id: Template.php 1 2012-10-26 08:27:37Z root $
     */
    protected static final function manageLNK (S $objMetaName, S $objMetaRelated = NULL,
    S $objMetaType = NULL, S $objMetaContent = NULL) {
        // Check
        if ($objMetaContent != NULL) {
            // Check
            if (self::$objLNK
            ->offsetExists ($objMetaName)) {
                // Set
                self::$objLNK[$objMetaName]['info']
                ->appendString ($objMetaContent
                ->prependString (_SP));
            } else {
                // Set
                self::$objLNK[$objMetaName]['name'] = $objMetaName;
                self::$objLNK[$objMetaName]['info'] = $objMetaContent;
                self::$objLNK[$objMetaName]['type'] = $objMetaType;
                self::$objLNK[$objMetaName]['relt'] = $objMetaRelated;
            }
        } else {
            // Check
            if (self::$objLNK
            ->offsetExists ($objMetaName)) {
                // Unset
                self::$objLNK
                ->offsetUnset ($objMetaName);
            } else {
                // Throws
                throw new HeaderLinkRelFileNotSetException;
            }
        }
    }

    /**
     * Returns the joined (lnk) buffer;
     *
     * @author Catalin Alexandru Zamfir <catalin.zamfir@raphpframework.ro>
     * @copyright Under the terms of the GNU General Public License v3
     * @version $Id: Template.php 1 2012-10-26 08:27:37Z root $
     */
    private static final function getMetaLNKHeader () {
        // Set
        $objHTML = new XMLWriter;
        $objHTML->openMemory ();
        $objHTML->setIndent (true);

        // Foreach
        foreach (self::$objLNK as $objK => $objV) {
            // Set
            $objHTML->startElement ('link');
            $objHTML->startAttribute ('rel');
            $objHTML->text ($objV['relt']);
            $objHTML->endAttribute ();
            $objHTML->startAttribute ('type');
            $objHTML->text ($objV['type']);
            $objHTML->endAttribute ();
            $objHTML->startAttribute ('title');
            $objHTML->text ($objV['name']);
            $objHTML->endAttribute ();
            $objHTML->startAttribute ('href');
            $objHTML->text ($objV['info']);
            $objHTML->endAttribute ();
            $objHTML->endElement ();
        }

        // Return
        return new S ($objHTML
        ->outputMemory ());
    }


    /**
     * Appends the given (meta-equiv) to the buffer;
     *
     * @author Catalin Alexandru Zamfir <catalin.zamfir@raphpframework.ro>
     * @copyright Under the terms of the GNU General Public License v3
     * @version $Id: Template.php 1 2012-10-26 08:27:37Z root $
     */
    protected static final function manageEQV (S $objMetaName, S $objMetaContent = NULL) {
        // Check
        if ($objMetaContent != NULL) {
            // Check
            if (self::$objEQV
            ->offsetExists ($objMetaName)) {
                // Set
                self::$objEQV[$objMetaName]['info']
                ->appendString ($objMetaContent
                ->prependString (_SP));
            } else {
                // Set
                self::$objEQV[$objMetaName]['name'] = $objMetaName;
                self::$objEQV[$objMetaName]['info'] = $objMetaContent;
            }
        } else {
            // Check
            if (self::$objEQV
            ->offsetExists ($objMetaName)) {
                // Unset
                unset (self::$objEQV[$objMetaName]);
            } else {
                // Throws
                throw new HeaderEquivalentNotSetException;
            }
        }
    }

    /**
     * Returns the joined (meta-equiv) buffer;
     *
     * @author Catalin Alexandru Zamfir <catalin.zamfir@raphpframework.ro>
     * @copyright Under the terms of the GNU General Public License v3
     * @version $Id: Template.php 1 2012-10-26 08:27:37Z root $
     */
    private static final function getMetaEQVHeader () {
        // Set
        $objHTML = new XMLWriter;
        $objHTML->openMemory ();
        $objHTML->setIndent (true);

        // Foreach
        foreach (self::$objEQV as $objK => $objV) {
            // Set
            $objHTML->startElement ('meta');
            $objHTML->startAttribute ('http-equiv');
            $objHTML->text ($objV['name']);
            $objHTML->endAttribute ();
            $objHTML->startAttribute ('content');
            $objHTML->text ($objV['info']);
            $objHTML->endAttribute ();
            $objHTML->endElement ();
        }

        // Return
        return new S ($objHTML
        ->outputMemory ());
    }

    /**
     * Appends the given (meta-tag) to the buffer;
     *
     * @author Catalin Alexandru Zamfir <catalin.zamfir@raphpframework.ro>
     * @copyright Under the terms of the GNU General Public License v3
     * @version $Id: Template.php 1 2012-10-26 08:27:37Z root $
     */
    protected static final function manageTAG (S $objMetaName, S $objMetaContent = NULL) {
        // Check
        if ($objMetaContent != NULL) {
            // Check
            if (self::$objTAG
            ->offsetExists ($objMetaName)) {
                // Set
                self::$objTAG[$objMetaName]['info']
                ->appendString ($objMetaContent
                ->prependString (_SP));
            } else {
                // Set
                self::$objTAG[$objMetaName]['name'] = $objMetaName;
                self::$objTAG[$objMetaName]['info'] = $objMetaContent;
            }
        } else {
            // Check
            if (self::$objTAG
            ->offsetExists ($objMetaName)) {
                // Unset
                unset (self::$objTAG[$objMetaName]);
            } else {
                // Throws
                throw new HeaderTagNotSetException;
            }
        }
    }

    /**
     * Returns the joined (meta-tag) buffer;
     *
     * @author Catalin Alexandru Zamfir <catalin.zamfir@raphpframework.ro>
     * @copyright Under the terms of the GNU General Public License v3
     * @version $Id: Template.php 1 2012-10-26 08:27:37Z root $
     */
    private static final function getMetaTAGHeader () {
        // Set
        $objHTML = new XMLWriter;
        $objHTML->openMemory ();
        $objHTML->setIndent (true);

        // Foreach
        foreach (self::$objTAG as $objK => $objV) {
            // Set
            $objHTML->startElement ('meta');
            $objHTML->startAttribute ('name');
            $objHTML->text ($objV['name']);
            $objHTML->endAttribute ();
            $objHTML->startAttribute ('content');
            $objHTML->text ($objV['info']);
            $objHTML->endAttribute ();
            $objHTML->endElement ();
        }

        // Return
        return new S ($objHTML
        ->outputMemory ());
    }

    /**
     * Append the given (jss) Path to the buffer;
     *
     * @author Catalin Alexandru Zamfir <catalin.zamfir@raphpframework.ro>
     * @copyright Under the terms of the GNU General Public License v3
     * @version $Id: Template.php 1 2012-10-26 08:27:37Z root $
     */
    protected static final function manageJSS (Path $objPath, B $objSetOrUnset = NULL) {
        // Check
        if ($objSetOrUnset == NULL) {
            $objSetOrUnset = new
            B (TRUE);
        }

        // Hash
        $objPathTag =
        Hasher::getUniqueHash (new
        S ('sha512'), $objPath);

        // Check
        if ($objSetOrUnset
        ->toBoolean () == TRUE) {
            // Check
            if (!(self::$objJSS
            ->offsetExists ($objPathTag))) {
                // Check
                if (_S ((string) $objPath)
                ->findPos ('http://') instanceof B) {
                    // Set
                    self::$objJSS->offsetSet ($objPathTag,
                    $objPath
                    ->prependString (Architecture
                    ::pathTo (Architecture::getHost ())
                    ->appendString (_S)));
                } else {
                    // Set
                    self::$objJSS->offsetSet ($objPathTag,
                    $objPath);
                }
            } else {
                // Throws
                throw new HeaderJSSFileAlreadySetException;
            }
        } else {
            // Check
            if (!(self::$objJSS
            ->offsetExists ($objPathTag))) {
                // Set
                self::$objJSS
                ->offsetUnset ($objPathTag);
            } else {
                // Throws
                throw new HeaderJSSFileNotSetException;
            }
        }
    }

    /**
     * Returns the joined (jss) buffer;
     *
     * @author Catalin Alexandru Zamfir <catalin.zamfir@raphpframework.ro>
     * @copyright Under the terms of the GNU General Public License v3
     * @version $Id: Template.php 1 2012-10-26 08:27:37Z root $
     */
    private static final function getJSSHeader () {
        // Set
        $objHTML = new XMLWriter;
        $objHTML->openMemory ();
        $objHTML->setIndent (true);

        // Foreach
        foreach (self::$objJSS as $objK => $objV) {
            // Set
            $objHTML->startElement ('script');
            $objHTML->startAttribute ('type');
            $objHTML->text ('text/javascript');
            $objHTML->endAttribute ();
            $objHTML->startAttribute ('src');
            $objHTML->text ($objV);
            $objHTML->endAttribute ();
            $objHTML->text (null);
            $objHTML->endElement ();
        }

        // Return
        return new S ($objHTML
        ->outputMemory ());
    }

    /**
     * Returns the HTML head tag container;
     *
     * @author Catalin Alexandru Zamfir <catalin.zamfir@raphpframework.ro>
     * @copyright Under the terms of the GNU General Public License v3
     * @version $Id: Template.php 1 2012-10-26 08:27:37Z root $
     */
    private static final function getHeadContainer () {
        // Return
        return _C (Architecture::pathTo (FORM_TP_DIR, 'HTML-Head-Start.tp'))
        ->doToken ('[%BASE_HREF_URL%]', Architecture
        ::pathTo (Architecture::getHost ())
        ->appendString (_S));
    }

    /**
     * Returns the proper HTML head end tag;
     *
     * @author Catalin Alexandru Zamfir <catalin.zamfir@raphpframework.ro>
     * @copyright Under the terms of the GNU General Public License v3
     * @version $Id: Template.php 1 2012-10-26 08:27:37Z root $
     */
    private static final function getEndHeadContainer () {
        // Return
        return new
        Contents (Architecture
        ::pathTo (FORM_TP_DIR,
        'HTML-Head-End.tp'));
    }

    /**
     * Retruns the proper HTML document ending;
     *
     * @author Catalin Alexandru Zamfir <catalin.zamfir@raphpframework.ro>
     * @copyright Under the terms of the GNU General Public License v3
     * @version $Id: Template.php 1 2012-10-26 08:27:37Z root $
     */
    private static final function getHTMLEnd () {
        // Return
        return new
        Contents (Architecture
        ::pathTo (FORM_TP_DIR,
        'HTML-Footer.tp'));
    }

    /**
     * Sets the given document type (default to html5);
     *
     * @author Catalin Alexandru Zamfir <catalin.zamfir@raphpframework.ro>
     * @copyright Under the terms of the GNU General Public License v3
     * @version $Id: Template.php 1 2012-10-26 08:27:37Z root $
     */
    private static final function setDocumentType (S $documentType) {
        // Set
        self::$objDocType = new Contents (Architecture
        ::pathTo (FORM_TP_DIR, 'HTML-Doctype-' .
        $documentType . TPL_EXTENSION));
    }

    /**
     * Gets the current container status;
     *
     * @author Catalin Alexandru Zamfir <catalin.zamfir@raphpframework.ro>
     * @copyright Under the terms of the GNU General Public License v3
     * @version $Id: Template.php 1 2012-10-26 08:27:37Z root $
     */
    private static final function getStatus () {
        // Return
        return self::$objContainerSwitch;
    }

    /**
     * Sets the container status (toggles);
     *
     * @author Catalin Alexandru Zamfir <catalin.zamfir@raphpframework.ro>
     * @copyright Under the terms of the GNU General Public License v3
     * @version $Id: Template.php 1 2012-10-26 08:27:37Z root $
     */
    private static final function switchHTML () {
        // Switch
        switch (self::$objContainerSwitch
        ->toInt () == 0) {
            // 1
            case TRUE:
                self::$objContainerSwitch->setInt (1);
                Environment::setDelayedHTWrite (new B (TRUE));
                return new B (TRUE);
                break;

            // 0
            case FALSE:
                self::$objContainerSwitch->setInt (0);
                Environment::setDelayedHTWrite (new B (FALSE));
                return new B (TRUE);
                break;
        }

        // Return
        return new B (FALSE);
    }

    /**
     * Gets the current document type;
     *
     * @author Catalin Alexandru Zamfir <catalin.zamfir@raphpframework.ro>
     * @copyright Under the terms of the GNU General Public License v3
     * @version $Id: Template.php 1 2012-10-26 08:27:37Z root $
     */
    private static final function getDocType () {
        // Return
        return self::$objDocType;
    }

    /**
     * Check to see if current request is Ajax;
     *
     * @author Catalin Alexandru Zamfir <catalin.zamfir@raphpframework.ro>
     * @copyright Under the terms of the GNU General Public License v3
     * @version $Id: Template.php 1 2012-10-26 08:27:37Z root $
     */
    private static final function requestIsAjax () {
        // Check
        if ($_SERVER->offsetExists ('HTTP_X_REQUESTED_WITH') &&
        $_SERVER->offsetGet ('HTTP_X_REQUESTED_WITH') == 'XMLHttpRequest') {
            // Return
            return new B (TRUE);
        } else {
            // Return
            return new B (FALSE);
        }
    }

    /**
     * Enables gzipping of content on output;
     *
     * @author Catalin Alexandru Zamfir <catalin.zamfir@raphpframework.ro>
     * @copyright Under the terms of the GNU General Public License v3
     * @version $Id: Template.php 1 2012-10-26 08:27:37Z root $
     */
    public static function enableGzip () {
    	// Set
    	self::$objGzipDisabled = false;
    }

    /**
     * Disbles gzipping of content on output;
     *
     * @author Catalin Alexandru Zamfir <catalin.zamfir@raphpframework.ro>
     * @copyright Under the terms of the GNU General Public License v3
     * @version $Id: Template.php 1 2012-10-26 08:27:37Z root $
     */
    public static function disableGzip () {
    	// Set
    	self::$objGzipDisabled = true;
    }

    /**
     * Returns the status of the output gzipping switch;
     *
     * @author Catalin Alexandru Zamfir <catalin.zamfir@raphpframework.ro>
     * @copyright Under the terms of the GNU General Public License v3
     * @version $Id: Template.php 1 2012-10-26 08:27:37Z root $
     */
    public static function isGzipDisabled () {
    	// Return
    	return self::$objGzipDisabled;
    }

    /**
     * Returns proper XML/other types of markup;
     *
     * @author Catalin Alexandru Zamfir <catalin.zamfir@raphpframework.ro>
     * @copyright Under the terms of the GNU General Public License v3
     * @version $Id: Template.php 1 2012-10-26 08:27:37Z root $
     */
    protected static final function getMarkup (S $objType) {
        // Return
        switch ($objType) {
            case 'RSS':
                // Throws
                throw new CannotRunNeedsRefactoring;
                break;

            case 'MAP':
                // Throws
                throw new CannotRunNeedsRefactoring;
                break;
        }
    }

    /**
     * Converts our framework specific DTs to PHP's json_* compatible ones;
     *
     * @author Catalin Alexandru Zamfir <catalin.zamfir@raphpframework.ro>
     * @copyright Under the terms of the GNU General Public License v3
     * @version $Id: Template.php 1 2012-10-26 08:27:37Z root $
     */
    protected static final function convertDTsToJSON (& $objArray) {
        // Foreach
        foreach ($objArray as $objK => $objV) {
            // Check
            if ($objV instanceof A) {
                // Set
                self::convertDTsToJSON ($objV);

                // Set
                $objArray[$objK] = $objV
                ->toArray ();
            } else if ($objV instanceof S) {
                // Set
                $objArray[$objK] =
                (string) $objV;
            } else if ($objV instanceof I) {
                // Set
                $objArray[$obK] =
                (string) $objV->toInt ();
            } else if ($objV instanceof F) {
                // Set
                $objArray[$obK] =
                (string) $objV->toFlt ();
            }
        }

        // Return
        return $objArray;
    }

    /**
     * Outputs, converting DTs, as Json;
     *
     * @author Catalin Alexandru Zamfir <catalin.zamfir@raphpframework.ro>
     * @copyright Under the terms of the GNU General Public License v3
     * @version $Id: Template.php 1 2012-10-26 08:27:37Z root $
     */
    protected static final function outputJson (A $objArrayToJSON) {
        // Discard stream
        Output::discardStream (new B (TRUE));

        // Switch
        self::switchHTML ();

        // Copy
        $objArrayToJSONCopy =
        $objArrayToJSON
        ->makeCopyObject ()
        ->toArray ();

        // Set
        self::convertDTsToJSON ($objArrayToJSONCopy);

        // JSONify
        echo json_encode
        ($objArrayToJSONCopy);

        // Die
        Output::stopStream ();
    }

    /**
     * Outputs, converting DTs, as Json;
     *
     * @author Catalin Alexandru Zamfir <catalin.zamfir@raphpframework.ro>
     * @copyright Under the terms of the GNU General Public License v3
     * @version $Id: Template.php 1 2012-10-26 08:27:37Z root $
     */
    protected static final function outputCryptedJson (A $objArrayToJSON) {
    	// Discard stream
    	Output::discardStream (new B (TRUE));

    	// Switch
    	self::switchHTML ();

    	// Copy
    	$objArrayToJSONCopy =
    	$objArrayToJSON
    	->makeCopyObject ()
    	->toArray ();

    	// Set
    	self::convertDTsToJSON ($objArrayToJSONCopy);

    	// JSONify
    	echo Architecture::doCrypt (new
		S (json_encode ($objArrayToJSONCopy)));

    	// Die
    	Output::stopStream ();
    }

    /**
     * Outputs as string (uncontained) for common JS uses;
     *
     * @author Catalin Alexandru Zamfir <catalin.zamfir@raphpframework.ro>
     * @copyright Under the terms of the GNU General Public License v3
     * @version $Id: Template.php 1 2012-10-26 08:27:37Z root $
     */
    protected static final function outputString (S $objStringToOutput) {
        // Discard stream
        Output::discardStream (new B (TRUE));

        // Swich
        self::switchHTML ();

        // Echo
        echo $objStringToOutput;

        // Die
        Output::stopStream ();
    }

    /**
     * Output binary, through ->readPath, right to the buffer;
     *
     * @author Catalin Alexandru Zamfir <catalin.zamfir@raphpframework.ro>
     * @copyright Under the terms of the GNU General Public License v3
     * @version $Id: Template.php 1 2012-10-26 08:27:37Z root $
     */
    protected static final function outputBinary (S $objPathToFile) {
        // Discard stream
        Output::discardStream (new B (TRUE));

        // Switch
        self::switchHTML ();

        // Straight to the buffer
        $objPathToFile->readPath ();

        // Die
        Output::stopStream ();
    }

    /**
     * Initiate template caching for the given Path, time & invariance;
     *
     * @author Catalin Alexandru Zamfir <catalin.zamfir@raphpframework.ro>
     * @copyright Under the terms of the GNU General Public License v3
     * @version $Id: Template.php 1 2012-10-26 08:27:37Z root $
     */
    protected static final function iniTp (Path $objTpPath, I $objTpTime = NULL,
    B $objTpLocationVary = NULL, S $objTpCachePath = NULL) {
        // Switch
        if ($objTpTime
        ->toInt () != 0) {
            // Set
            $objCachePath = self::getCachePath ($objTpPath,
            $objTpTime, $objTpLocationVary, $objTpCachePath);

            // Check
            if (self::checkCache ($objCachePath,
            $objTpTime)->toBoolean ()) {
                // Cached, stop if () condition
                return self::tpCache ($objCachePath)
                ->toBoolean ();
            } else {
                // Discard
                Output::discardStream ();

                // Return
                return $objCachePath;
            }
        } else {
            // Discard
            Output::discardStream ();

            // Return
            return new S ('No Cache');
            break;
        }
    }

    /**
     * Sets a template key to be extracted upon execution;
     *
     * @author Catalin Alexandru Zamfir <catalin.zamfir@raphpframework.ro>
     * @copyright Under the terms of the GNU General Public License v3
     * @version $Id: Template.php 1 2012-10-26 08:27:37Z root $
     */
    protected static final function setTp (M $objTpVar, S $objTpKey, Path $objTpPath, S $objTpAction = NULL) {
        // Check
        if ($objTpAction != NULL) {
            // Switch
            switch ($objTpAction) {
                case 'capitalize':
                    // Stringfy
                    $objTpVar = new S ($objTpVar);

                    // Capitalize it, with strtoupper ();
                    self::$objPaths[$objTpPath][$objTpKey] = $objTpVar->toUpper ();
                    break;

                case 'explode':
                    // Check
                    if ($objTpVar instanceof A) {
                        // Foreach
                        foreach ($objTpVar as $objK => $objV) {
                            // Save it
                            self::$objPaths[$objTpPath][$objK] = $objV;
                        }
                    } else {
                        // Throws
                        throw new TemplateVariableNotInstanceOfArrayException;
                    }
                    // BK;
                    break;
            }
        } else {
            // Set
            self::$objPaths[$objTpPath][$objTpKey] = $objTpVar;
        }
    }

    /**
     * Maps the given object, index and __function__ to a specific tp;
     *
     * @author Catalin Alexandru Zamfir <catalin.zamfir@raphpframework.ro>
     * @copyright Under the terms of the GNU General Public License v3
     * @version $Id: Template.php 1 2012-10-26 08:27:37Z root $
     */
    protected static final function mapTp ($objSource, $objPath, $objFunction) {
        // Go
        self::setTp ($objSource,
        new S ('OBJ'), $objTp = new
        Path (Architecture::pathTo ($objSource
        ->getPathToSkin (), $objFunction,
        $objPath . TPL_EXTENSION)));

        // Execute
        self::exeTp ($objTp);
    }

    /**
     * Executes the given template Path, extracting proper variables;
     *
     * @author Catalin Alexandru Zamfir <catalin.zamfir@raphpframework.ro>
     * @copyright Under the terms of the GNU General Public License v3
     * @version $Id: Template.php 1 2012-10-26 08:27:37Z root $
     */
    protected static final function exeTp (Path $objTpPath) {
        // Set
        self::$objPaths[$objTpPath]['dummy_' .
        time ()] = new S ('No Cache');

        // Check
        if (extract (self::$objPaths[$objTpPath]->toArray (),
        EXTR_REFS | EXTR_OVERWRITE)) {
            // Require
            require $objTpPath;

            // Unset
            self::$objPaths
            ->offsetUnset ($objTpPath);

            // Return
            return new B (TRUE);
        } else {
            // Throws
            throw new CannotExtractTemplateVariablesException;
        }
    }

    /**
     * Ends cache for the given template path and stores it to the cache or returns it;
     *
     * @author Catalin Alexandru Zamfir <catalin.zamfir@raphpframework.ro>
     * @copyright Under the terms of the GNU General Public License v3
     * @version $Id: Template.php 1 2012-10-26 08:27:37Z root $
     */
    protected static final function endTp (S $objCachePath, B $objGetContent = NULL) {
        // Check
        if (($objGetContent != NULL) &&
        ($objCachePath == 'No Cache') &&
        Output::discardStream ()) {
            // Return
            return new B (TRUE);
        } else if (($objCachePath != 'No Cache')) {
            // Save it for next time
            self::writeCache ($objCachePath);
        }
    }

    /**
     * Returns the Path to the cache, for the given template;
     *
     * @author Catalin Alexandru Zamfir <catalin.zamfir@raphpframework.ro>
     * @copyright Under the terms of the GNU General Public License v3
     * @version $Id: Template.php 1 2012-10-26 08:27:37Z root $
     */
    private static final function getCachePath (Path $objTpPath, I $objTpTime,
    B $objTpLocationVary = NULL, S $objTpCachePath = NULL) {
        // Throws
        throw new CannotRunNeedsRefactoring;
    }

    /**
     * Writes to the cache, taking the cache Path as an argument;
     *
     * @author Catalin Alexandru Zamfir <catalin.zamfir@raphpframework.ro>
     * @copyright Under the terms of the GNU General Public License v3
     * @version $Id: Template.php 1 2012-10-26 08:27:37Z root $
     */
    private static final function writeCache (Path $objCachePath) {
        // Check
        if ($objCachePath
        ->touchPath ()) {
            // Put to file
            $objCachePath->putToFile (Output
            ::getStream ());

            // Return
            return new B (TRUE);
        } else {
            // Throws
            throw new CannotWriteTemplateCacheFileException;
        }
    }

    /**
     * Checks to see if the given cache Path has expired;
     *
     * @author Catalin Alexandru Zamfir <catalin.zamfir@raphpframework.ro>
     * @copyright Under the terms of the GNU General Public License v3
     * @version $Id: Template.php 1 2012-10-26 08:27:37Z root $
     */
    private static final function checkCache (Path $objCachePath, I $objTpTime) {
        // Check
        if ($objCachePath
        ->checkPathExists ()
        ->toBoolean ()) {
            // Return
            return (($objCachePath->getPathInfo ('mtime')
            ->toInt () + $objTpTime->toInt ()) >
            (time ()) ? new B (TRUE) : new B (FALSE));
        } else {
            // Return
            return new B (FALSE);
        }
    }

    /**
     * Reads out the cache, going straight to the buffer;
     *
     * @author Catalin Alexandru Zamfir <catalin.zamfir@raphpframework.ro>
     * @copyright Under the terms of the GNU General Public License v3
     * @version $Id: Template.php 1 2012-10-26 08:27:37Z root $
     */
    private static final function tpCache (Path $objCachePath) {
        // Return
        return new B (!($objCachePath
        ->checkPathExists () &&
        $objCachePath->readPath ()));
    }

    /**
     * Outputs stored contents through Gzip or not, based on configuration;
     *
     * @author Catalin Alexandru Zamfir <catalin.zamfir@raphpframework.ro>
     * @copyright Under the terms of the GNU General Public License v3
     * @version $Id: Template.php 1 2012-10-26 08:27:37Z root $
     */
    private static final function setGzippedOutput (S $objAction) {
        // Check what we have
        if (Header::getRequestHeaders ()
        ->offsetExists ('Accept-Encoding')) {
            // Set
            $objEncodingType = _S (Header::getRequestHeaders ()
            ->offsetGet ('Accept-Encoding')
            ->fromStringToArray (',')
            ->offsetGet (OB_GZIP_TYPE))
            ->trimBoth ();
        } else {
            // Set (forced)
            $objEncodingType = new
            S ('gzip');
        }

        // Switch
        switch ($objAction) {
            // Header
            case 'Header':
                // Check
                if (OB_GZIP == TRUE && OB_GZIP_LEVEL > 0 && OB_GZIP_LEVEL <= 9 &&
                self::isGzipDisabled () == false) {
                    // Determine
                    Header::setKey (new S ($objEncodingType),
                    new S ('Content-Encoding'));
                }

                // BK;
                break;

            // Contents
            case 'Content':
                // Check if set, or reset
                $objOutputBuffer = Output::getBuffer () instanceof A ?
                Output::getBuffer () : new A (Output::getBuffer ());

                // Check
                if (OB_GZIP == TRUE && OB_GZIP_LEVEL > 0 && OB_GZIP_LEVEL <= 9 &&
				self::isGzipDisabled () == false) {
                    // Switch
                    switch ($objEncodingType) {
                        // Deflate
                        case 'deflate':
                            // Echo
                            echo $objOutputBuffer->fromArrayToString (_NONE)
                            ->pregChange (Output::getTokens ()->toArray (),
                            Output::getStrings ()->toArray ())
                            ->gZipCompress (OB_GZIP_LEVEL);
                            break;

                        default:
                            // Echo
                            echo $objOutputBuffer->fromArrayToString (_NONE)
                            ->pregChange (Output::getTokens ()->toArray (),
                            Output::getStrings ()->toArray ())
                            ->gZipEncode (OB_GZIP_LEVEL, FORCE_GZIP);
                            break;
                    }
                } else {
                    // Echo
                    echo $objOutputBuffer->fromArrayToString (_NONE)
                    ->pregChange (Output::getTokens ()->toArray (),
                    Output::getStrings ()->toArray ());
                }

                // BK;
                break;
        }
    }
}
?>
