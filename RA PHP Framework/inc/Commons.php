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
 * Provides a method of abstracting features in different mods, via reflection, properties or more;
 *
 * @author Catalin Alexandru Zamfir <catalin.zamfir@raphpframework.ro>
 * @copyright Under the terms of the GNU General Public License v3
 * @version $Id: Commons.php 23 2012-10-26 21:06:06Z root $
 */
abstract class Commons extends Mods {
    /* STATICS */
    private static $objCookie = NULL;
    private static $objAdministration = NULL;
    private static $objAuthentication = NULL;
    private static $objFrontend = NULL;
    private static $objSettings = NULL;
    private static $objSQLTokens = NULL;
    private static $objStandardIMGSizes = NULL;
    private static $objAcceptedImageMimeTypes = NULL;
    private static $objAcceptedVideoMimeTypes = NULL;
    private static $objAcceptedAudioMimeTypes = NULL;

    // Privates
    private $objReflection = NULL;
    private static $objMPTT = NULL;
    private static $objPredefinedACLs = NULL;

    /* PROPERTIES */
    const PROPERTY_ITEM_FEATURED = "Featured";
    const PROPERTY_CATEGORY_FEATURED = "Featured";

    /* NOTIFICATIONS */
    const NOTIFICATION_TYPE_ERROR = 1;
    const NOTIFICATION_TYPE_ALERT = 2;
    const NOTIFICATION_TYPE_MESSAGE = 3;
    const NOTIFICATION_TYPE_NOTIFY = 4;

    /**
     * Requires items if they are present;
     *
     * @author Catalin Alexandru Zamfir <catalin.zamfir@raphpframework.ro>
     * @copyright Under the terms of the GNU General Public License v3
     * @version $Id: Commons.php 23 2012-10-26 21:06:06Z root $
     */
    private function requireItems () {
        // Items
        $this->tieInDatabase (new
        A (Array (static::$objItem)));
    }

    /**
     * Requires images for items if they are present;
     *
     * @author Catalin Alexandru Zamfir <catalin.zamfir@raphpframework.ro>
     * @copyright Under the terms of the GNU General Public License v3
     * @version $Id: Commons.php 23 2012-10-26 21:06:06Z root $
     */
    private function requireImages () {
        // Images of items
        $this->tieInDatabase (new
        A (Array (static::$objImage)));
    }

    /**
     * Requires videos for items if they are present;
     *
     * @author Catalin Alexandru Zamfir <catalin.zamfir@raphpframework.ro>
     * @copyright Under the terms of the GNU General Public License v3
     * @version $Id: Commons.php 23 2012-10-26 21:06:06Z root $
     */
    private function requireVideos () {
        // Videos of items
        $this->tieInDatabase (new
        A (Array (static::$objVideo)));
    }

    /**
     * Requires properties for items if they are present;
     *
     * @author Catalin Alexandru Zamfir <catalin.zamfir@raphpframework.ro>
     * @copyright Under the terms of the GNU General Public License v3
     * @version $Id: Commons.php 23 2012-10-26 21:06:06Z root $
     */
    private function requireProperties () {
        // Properties of items
        $this->tieInDatabase (new
        A (Array (static::$objProperty)));
    }

    /**
     * Requires categories for items if they are present;
     *
     * @author Catalin Alexandru Zamfir <catalin.zamfir@raphpframework.ro>
     * @copyright Under the terms of the GNU General Public License v3
     * @version $Id: Commons.php 23 2012-10-26 21:06:06Z root $
     */
    private function requireCategories () {
        // Categories
        $this->tieInDatabase (new
        A (Array (static::$objCategory)));

        // Set
        $this->enableHierarchy ();
    }

    /**
     * Requires properties for categories if they are present;
     *
     * @author Catalin Alexandru Zamfir <catalin.zamfir@raphpframework.ro>
     * @copyright Under the terms of the GNU General Public License v3
     * @version $Id: Commons.php 23 2012-10-26 21:06:06Z root $
     */
    private function requireCategoryProperties () {
        // Properties of categories
        $this->tieInDatabase (new
        A (Array (static::$objCategoryProperty)));
    }

    /**
     * Requires comments for items if they are present;
     *
     * @author Catalin Alexandru Zamfir <catalin.zamfir@raphpframework.ro>
     * @copyright Under the terms of the GNU General Public License v3
     * @version $Id: Commons.php 23 2012-10-26 21:06:06Z root $
     */
    private function requireComments () {
        // Comments
        $this->tieInDatabase (new
        A (Array (static::$objComment)));
    }

    /**
     * Requires attachments for items if they are present;
     *
     * @author Catalin Alexandru Zamfir <catalin.zamfir@raphpframework.ro>
     * @copyright Under the terms of the GNU General Public License v3
     * @version $Id: Commons.php 23 2012-10-26 21:06:06Z root $
     */
    private function requireAttachments () {
        // Attachments
        $this->tieInDatabase (new
        A (Array (static::$objAttachment)));
    }

    /**
     * Requires audios for items if they are present;
     *
     * @author Catalin Alexandru Zamfir <catalin.zamfir@raphpframework.ro>
     * @copyright Under the terms of the GNU General Public License v3
     * @version $Id: Commons.php 23 2012-10-26 21:06:06Z root $
     */
    private function requireAudios () {
        // Attachments
        $this->tieInDatabase (new
        A (Array (static::$objAudio)));
    }

    /**
     * Makes common requirements, to images, properties, categories or more;
     *
     * @author Catalin Alexandru Zamfir <catalin.zamfir@raphpframework.ro>
     * @copyright Under the terms of the GNU General Public License v3
     * @version $Id: Commons.php 23 2012-10-26 21:06:06Z root $
     */
    private function requireCommon () {
        // Check
        if ($this->getReflection ()
        ->hasProperty ('objItem')
        == TRUE) {
            // To implement.
        }

        // Check
        if ($this->getReflection ()
        ->hasProperty ('objImage')
        == TRUE) {
            // Require
            $this->requireImages ();
        }

        // Check
        if ($this->getReflection ()
        ->hasProperty ('objVideo')
        == TRUE) {
            // Require
            $this->requireVideos ();
        }

        // Check
        if ($this->getReflection ()
        ->hasProperty ('objProperty')
        == TRUE) {
            // Require
            $this->requireProperties ();
        }

        // Check
        if ($this->getReflection ()
        ->hasProperty ('objCategory')
        == TRUE) {
            // Require
            $this->requireCategories ();
        }

        // Check
        if ($this->getReflection ()
        ->hasProperty ('objCategoryProperty')
        == TRUE) {
            // Require
            $this->requireCategoryProperties ();
        }

        // Check
        if ($this->getReflection ()
        ->hasProperty ('objComment')
        == TRUE) {
            // Require
            $this->requireComments ();
        }

        // Check
        if ($this->getReflection ()
        ->hasProperty ('objAttachment')
        == TRUE) {
            // Require
            $this->requireAttachments ();
        }

        // Check
        if ($this->getReflection ()
        ->hasProperty ('objAudio')
        == TRUE) {
            // Require
            $this->requireAudios ();
        }
    }

    /**
     * Sets some default SQL tokens;
     *
     * @author Catalin Alexandru Zamfir <catalin.zamfir@raphpframework.ro>
     * @copyright Under the terms of the GNU General Public License v3
     * @version $Id: Commons.php 23 2012-10-26 21:06:06Z root $
     */
    protected function doChangeToken (S $objSQLParam) {
        // Check
        if (Execution::checkIsDefined (new
        S ('Authentication'))->toBoolean () == FALSE) {
            // Load
            _new ('Authentication');
        }

        // Token
        $objT = new A;

        // Set
        $objT[] = 'objUser';
        $objT[] = 'objUserId';
        $objT[] = 'objUserRegOn';

        // Hierarchy
        $objT[] = 'objHierarchyLeft';
        $objT[] = 'objHierarchyRight';
        $objT[] = 'objHierarchyDate';

        // Replace
        $objR = new A;

        // Set
        $objR[] = Authentication::$objUser;
        $objR[] = Authentication::$objUserId;
        $objR[] = Authentication::$objUserRegOn;

        // Hierarchy
        $objR[] = 'lft';
        $objR[] = 'rgt';
        $objR[] = 'date';

        // Get tokens/replacements
        $this->getSQLTokens ($objT, $objR);

        // Do a CALL to the parent
        return parent::doChangeTokens ($objT,
        $objR, $objSQLParam);
    }

    /**
     * Overriding method to return a mods sitemap, as TEXT_PLAIN or XML;
     *
     * @author Catalin Alexandru Zamfir <catalin.zamfir@raphpframework.ro>
     * @copyright Under the terms of the GNU General Public License v3
     * @version $Id: Commons.php 23 2012-10-26 21:06:06Z root $
     */
    public function getSitemap () {
        // Override by mods
    }

    /**
     * Returns the reflection object for this mod;
     *
     * @author Catalin Alexandru Zamfir <catalin.zamfir@raphpframework.ro>
     * @copyright Under the terms of the GNU General Public License v3
     * @version $Id: Commons.php 23 2012-10-26 21:06:06Z root $
     */
    public function getReflection () {
        // Check
        if (!($this->objReflection
        instanceof ReflectionClass)) {
            // Set
            $this->objReflection =
            new ReflectionClass ($this);
        }

        // Return
        return $this
        ->objReflection;
    }

    /**
     * When requests come through the Frontend, we can do some nasty URL routing on it. Every mod has this method that they can
     * overload and inject code for specific routing inside it's teritorry. We're anxious about keeping URLs organized this way, as
     * it helps maintainance, knowing which URL belongs to which module and knowing where to find the routing, without the need for
     * bootstrapping on the URL registry.
     */
    public function doURLRouting (Frontend $objFront) {
        // None
    }

    /**
     * Ties in common configuration;
     *
     * @author Catalin Alexandru Zamfir <catalin.zamfir@raphpframework.ro>
     * @copyright Under the terms of the GNU General Public License v3
     * @version $Id: Commons.php 23 2012-10-26 21:06:06Z root $
     */
    protected function tieInConfiguration () {
        // Set
        ChainOfCommand::registerExecutor ($this);

        // Mirror me
        $this->getReflection ();

        // Requirements
        if (self::checkExists (new
        S ('_T_system_notifications'))
        ->toBoolean () == FALSE) {
            // Go
            $this->doQuery (new S ('CREATE TABLE
    	    IF NOT EXISTS `_T_system_notifications`
    	    (`id` char(36) NOT NULL,
    	    `source` varchar(50) NOT NULL,
    	    `msg` longtext NOT NULL,
    	    `type` int(4) NOT NULL,
    	    `published` bigint(20) unsigned NOT NULL,
    	    `viewed` enum("Y", "N") NOT NULL default "N",
    	    KEY `id` (`id`),
    	    KEY `source` (`source`(50)))
    	    ENGINE=InnoDB
    	    DEFAULT CHARSET=utf8;'));

            // Hard-code of setup
            $this->setNotification (_T ('Setup'), _T ('It seems that everything went Ok and the database, directories and any other
            requirements have been properly setup! Even so, we advise you to check that everything is working in proper order, just
            to avoid weird situations and make you comfortable that everything is ok!'), new I (Commons::NOTIFICATION_TYPE_MESSAGE));
        }

        // Check
        if (self::$objStandardIMGSizes == NULL) {
            // Standards
            self::$objStandardIMGSizes = new A;
            self::$objStandardIMGSizes->offsetSet (75, 75);
            self::$objStandardIMGSizes->offsetSet (100, 100);
            self::$objStandardIMGSizes->offsetSet (128, 128);
            self::$objStandardIMGSizes->offsetSet (256, 256);
            self::$objStandardIMGSizes->offsetSet (300, 135);
            self::$objStandardIMGSizes->offsetSet (320, 240);
            self::$objStandardIMGSizes->offsetSet (512, 512);
            self::$objStandardIMGSizes->offsetSet (640, 480);
            self::$objStandardIMGSizes->offsetSet (800, 600);
            self::$objStandardIMGSizes->offsetSet (1024, 768);
            self::$objStandardIMGSizes->offsetSet (1280, 1024);
            self::$objStandardIMGSizes->offsetSet (1600, 1200);
        }

        // Check
        if (self::$objAcceptedImageMimeTypes == NULL) {
            // Set
            self::$objAcceptedImageMimeTypes = new A;
            self::$objAcceptedImageMimeTypes->offsetSet (0, new S ('image/bmp'));
            self::$objAcceptedImageMimeTypes->offsetSet (1, new S ('image/x-windows-bmp'));
            self::$objAcceptedImageMimeTypes->offsetSet (2, new S ('image/gif'));
            self::$objAcceptedImageMimeTypes->offsetSet (3, new S ('image/jpeg'));
            self::$objAcceptedImageMimeTypes->offsetSet (4, new S ('image/pjpeg'));
            self::$objAcceptedImageMimeTypes->offsetSet (5, new S ('image/png'));
            self::$objAcceptedImageMimeTypes->offsetSet (6, new S ('image/vnd.wap.wbmp'));
        }

        // Check
        if (self::$objAcceptedVideoMimeTypes == NULL) {
            // Set
            self::$objAcceptedVideoMimeTypes = new A;
            self::$objAcceptedVideoMimeTypes->offsetSet (0, new S ('audio/ogg'));
            self::$objAcceptedVideoMimeTypes->offsetSet (1, new S ('video/ogg'));
            self::$objAcceptedVideoMimeTypes->offsetSet (2, new S ('application/ogg'));
        }

        // Check
        if (self::$objAcceptedAudioMimeTypes == NULL) {
            // Set
            self::$objAcceptedAudioMimeTypes = new A;
            self::$objAcceptedAudioMimeTypes->offsetSet (0, new S ('audio/ogg'));
            self::$objAcceptedAudioMimeTypes->offsetSet (1, new S ('video/ogg'));
            self::$objAcceptedAudioMimeTypes->offsetSet (2, new S ('audio/mpeg'));
            self::$objAcceptedVideoMimeTypes->offsetSet (3, new S ('application/ogg'));
        }

        // Check
        if (self::$objPredefinedACLs == NULL) {
            // Define ACLs array
            self::$objPredefinedACLs = new A;

            // Check
            if (!(self::$objPredefinedACLs
            ->offsetExists ($this
            ->getObjectAncestry ()))) {
                // Set
                self::$objPredefinedACLs[$this
                ->getObjectAncestry ()] = new A;
            }
        }

        // Check
        if (self::$objMPTT == NULL) {
            // Define hierarchy
            self::$objMPTT = new A;

            // Check
            if (!(self::$objMPTT
            ->offsetExists ($this
            ->getObjectAncestry ()))) {
                // Set
                self::$objMPTT[$this
                ->getObjectAncestry ()] = new A;
            }
        }

        // Check
        if (self::$objCookie == NULL) {
            // Set
            self::$objCookie = new A;
        }

        // Paths
        $this->getCookiesAndPaths ();

        // Check
        if (self::$objSettings == NULL) {
            // Check
            if ($this->getObjectAncestry () != 'Settings') {
                // Load defaults ATH, STG and others
                self::$objSettings = _new ('Settings');
            } else {
                // Itself
                self::$objSettings  = $this;
            }
        }

        // Foreach
        foreach ($this->getReflection ()
        ->getStaticProperties () as $objK => $objV) {
            // Check
            if ($this->getReflection ()
            ->getProperty ($objK)->isPublic ()) {
                // Set
                $this->getReflection ()
                ->setStaticPropertyValue ($objK, $this
                ->getConfigKey (new S ((string) $objK)));
            }
        }

        // Require
        $this->requireCommon ();
    }

    /**
     * Sets a notification for the administration interface;
     *
     * @author Catalin Alexandru Zamfir <catalin.zamfir@raphpframework.ro>
     * @copyright Under the terms of the GNU General Public License v3
     * @version $Id: Commons.php 23 2012-10-26 21:06:06Z root $
     */
    protected function setNotification (S $objFrom, S $objMessage, I $objType) {
        // Set
        $this->_Q (_QS ('doINSERT')
        ->doToken ('%table', new S ('_T_system_notifications'))
        ->doToken ('%condition', new S ('id = UUID (), source = "%fId",
        msg = "%mId", type = "%tId", published = "%dId"'))
        ->doToken ('%fId', $objFrom)
        ->doToken ('%mId', $objMessage)
        ->doToken ('%tId', $objType)
        ->doToken ('%dId', time ()));
    }

    /**
     * Returns the newest notifications not currently viewed;
     *
     * @author Catalin Alexandru Zamfir <catalin.zamfir@raphpframework.ro>
     * @copyright Under the terms of the GNU General Public License v3
     * @version $Id: Commons.php 23 2012-10-26 21:06:06Z root $
     */
    protected function getNewestNotifications () {
        // Return
        return $this->_Q (_QS ('doSELECT')
        ->doToken ('%what', new S ('*'))
        ->doToken ('%table', new S ('_T_system_notifications'))
        ->doToken ('%condition', new S ('WHERE viewed = "N"'))
        ->doToken ('%dId', self::getAuthentication ()
        ->getCurrentUser (Authentication::$objUserLastLog)));
    }

    /**
     * Sets/returns cookies and paths for the current mod;
     *
     * @author Catalin Alexandru Zamfir <catalin.zamfir@raphpframework.ro>
     * @copyright Under the terms of the GNU General Public License v3
     * @version $Id: Commons.php 23 2012-10-26 21:06:06Z root $
     */
    private function getCookiesAndPaths () {
        // Get a session
        self::$objCookie[$this
        ->getObjectAncestry ()] =
        new Cookie ($this);

        // Skin
        if (Session::checkKey (new S ('skin'),
        new O (SKIN))->toBoolean () == FALSE)
        $this->objSkin = Session::getKey (new S ('skin'));
        else $this->objSkin = new S (SKIN);

        // Language
        if (Session::checkKey (new S ('language'),
        new O (LANGUAGE))->toBoolean () == FALSE)
        $this->objLang = Session::getKey (new S ('language'));
        else $this->objLang = new S (LANGUAGE);

        // Paths
        $this->objPathToMod =
        new Path (Architecture
        ::pathTo (MOD_DIR, $this
        ->getObjectAncestry ()
        ->toLower ()));

        // Up's
        $this->objPathToUpd =
        new StoragePath (Architecture
        ::pathTo (UPLOAD_DIR, $this
        ->getObjectAncestry ()
        ->toLower ()), FALSE);

        // Check
        if ($this->objPathToUpd
        ->checkPathExists (FALSE)
        ->toBoolean () == FALSE) {
            // Set
            MKDIR ($this->objPathToUpd->toAbsolutePath (), TRUE);
            CHMOD ($this->objPathToUpd->toAbsolutePath (), 0777);
        }
    }

    /**
     * Returns the object identifier;
     *
     * @author Catalin Alexandru Zamfir <catalin.zamfir@raphpframework.ro>
     * @copyright Under the terms of the GNU General Public License v3
     * @version $Id: Commons.php 23 2012-10-26 21:06:06Z root $
     */
    protected function getIdentifier () {
        // Return
        return $this
        ->getObjectAncestry ();
    }

    /**
     * Sets a predefined ACL;
     *
     * @author Catalin Alexandru Zamfir <catalin.zamfir@raphpframework.ro>
     * @copyright Under the terms of the GNU General Public License v3
     * @version $Id: Commons.php 23 2012-10-26 21:06:06Z root $
     */
    protected function setACL (S $objACL) {
        // Set
        self::$objPredefinedACLs[$this
        ->getObjectAncestry ()][] = $objACL->makeCopyObject ()
        ->prependString ($this->getObjectAncestry () . _DOT);
    }

    /**
     * Returns predefined ACLs;
     *
     * @author Catalin Alexandru Zamfir <catalin.zamfir@raphpframework.ro>
     * @copyright Under the terms of the GNU General Public License v3
     * @version $Id: Commons.php 23 2012-10-26 21:06:06Z root $
     */
    protected function getACLs () {
        // Return
        return self::$objPredefinedACLs[$this
        ->getObjectAncestry ()];
    }

    /**
     * Enables support for item hierarchy;
     *
     * @author Catalin Alexandru Zamfir <catalin.zamfir@raphpframework.ro>
     * @copyright Under the terms of the GNU General Public License v3
     * @version $Id: Commons.php 23 2012-10-26 21:06:06Z root $
     */
    protected function enableHierarchy () {
        // Compute ancestry
        $objThis = (string) $this
        ->getObjectAncestry ();

        // Set
        self::$objMPTT[$this
        ->getObjectAncestry ()] = new
        Hierarchy ($objThis::$objCategory,
        Hierarchy::mpttAddUnique (new S ($objThis), new S ((string)
        time ())));
    }

    /**
     * Returns the hierarchy object;
     *
     * @author Catalin Alexandru Zamfir <catalin.zamfir@raphpframework.ro>
     * @copyright Under the terms of the GNU General Public License v3
     * @version $Id: Commons.php 23 2012-10-26 21:06:06Z root $
     */
    protected function getHierarchy () {
        // Return
        return self::$objMPTT[$this
        ->getObjectAncestry ()];
    }

    /**
     * Returns the object's SQL tokens (fields);
     *
     * @author Catalin Alexandru Zamfir <catalin.zamfir@raphpframework.ro>
     * @copyright Under the terms of the GNU General Public License v3
     * @version $Id: Commons.php 23 2012-10-26 21:06:06Z root $
     */
    protected function getSQLTokens (A & $objT, A & $objR) {
        // Check
        if (self::$objSQLTokens == NULL) {
            self::$objSQLTokens = new A;
        }

        // Hash
        $objThis = (string) $this
        ->getObjectAncestry ();

        // Check
        if (self::$objSQLTokens
        ->offsetExists ($objThis)) {
            // Set
            $objT = new A (self::$objSQLTokens[$objThis]['T']);
            $objR = self::$objSQLTokens[$objThis]['R'];
        } else {
            // Foreach
            foreach ($this->getReflection ()
            ->getStaticProperties ()
            as $objK => $objV) {
                // Check
                if ($this->getReflection ()
                ->getProperty ($objK)->isPublic ()) {
                    // Set
                    $objT[] = $objK;
                    $objR[] = $objThis::$$objK;
                }
            }

            // Set
            self::$objSQLTokens[$objThis]['T'] = $objT->toArray ();
            self::$objSQLTokens[$objThis]['R'] = $objR;
        }
    }

    /**
     * Returns standard image sizes;
     *
     * @author Catalin Alexandru Zamfir <catalin.zamfir@raphpframework.ro>
     * @copyright Under the terms of the GNU General Public License v3
     * @version $Id: Commons.php 23 2012-10-26 21:06:06Z root $
     */
    protected static final function getStandardImageSizes () {
        // Return
        return self::$objStandardIMGSizes;
    }

    /**
     * Returns the administration interface object;
     *
     * @author Catalin Alexandru Zamfir <catalin.zamfir@raphpframework.ro>
     * @copyright Under the terms of the GNU General Public License v3
     * @version $Id: Commons.php 23 2012-10-26 21:06:06Z root $
     */
    protected static final function getAdministration () {
        // Return
        return self::$objAdministration;
    }

    /**
     * Returns the frontend object;
     *
     * @author Catalin Alexandru Zamfir <catalin.zamfir@raphpframework.ro>
     * @copyright Under the terms of the GNU General Public License v3
     * @version $Id: Commons.php 23 2012-10-26 21:06:06Z root $
     */
    protected static final function getFrontend () {
        // Return
        return self::$objFrontend;
    }

    /**
     * Returns the authentication object;
     *
     * @author Catalin Alexandru Zamfir <catalin.zamfir@raphpframework.ro>
     * @copyright Under the terms of the GNU General Public License v3
     * @version $Id: Commons.php 23 2012-10-26 21:06:06Z root $
     */
    protected static final function getAuthentication () {
        // Return
        return self::$objAuthentication;
    }

    /**
     * Ties in (common) services;
     *
     * @author Catalin Alexandru Zamfir <catalin.zamfir@raphpframework.ro>
     * @copyright Under the terms of the GNU General Public License v3
     * @version $Id: Commons.php 23 2012-10-26 21:06:06Z root $
     */
    protected function tieInServices () {
        /**
         * We've chosen to JSONify/Serialize (Object) any method we define in our MODs. This allows for a quick, RAD approach to
         * development, as these methods usually output an PHP Array. That PHP Array, if requested via an URL specifying the Kind, will
         * be outputed in that kind. If a method is incompatible in output (does not return an array) nothing is going to be outputted.
         *
         * Also, developers can bring in security mechanisms, develop their own methods or extend. They can check for an authenticated
         * cookie before allowing access or do some other crazy stuff. We default to not having any security mechanism in place as it
         * would by too restrictive by default.
         *
         * Ex: http://project.org/Type/Settings/Method/GetCountries/Kind/Json
         */

        // Check
        if ($_GET
        ->offsetExists (_T ('Method'))) {
            // Set
            $objMethod = lcfirst ((string) $_GET
            ->offsetGet (_T ('Method')));

            // Go
            if ($_GET
            ->offsetExists (_T ('Kind'))) {
                // REST
                switch ($_GET
                ->offsetGet (_T ('Kind'))) {
                    // REST-ing
                    case _T ('Json'):
                    	// Check
                    	if ($_GET->offsetExists (_T ('As'))) {
                    		// Check
                    		switch ($_GET->offsetGet (_T ('As'))) {
                    			case 'Crypted':
                    				// Set
                    				self::outputCryptedJson ($this
									->$objMethod ());
                    				break;
                    		}
                    	} else {
                    		// Check
                    		self::outputJson ($this
							->$objMethod ());
                    	}
                    	// BK;
                        break;

                    // Serialized
                    case _T ('Object'):
                        self::outputString (new
                        S (serialize ($this
                        ->$objMethod ())));
                        break;

                    // Source
                    case _T ('Source'):
                        self::outputString ($this
                        ->$objMethod ());
                        break;
                }
            } else {
                // Anything
                $this->$objMethod ();
            }
        }
    }

    /**
     * Returns the settings object;
     *
     * @author Catalin Alexandru Zamfir <catalin.zamfir@raphpframework.ro>
     * @copyright Under the terms of the GNU General Public License v3
     * @version $Id: Commons.php 23 2012-10-26 21:06:06Z root $
     */
    protected static final function getSettings () {
        // Return
        return self::$objSettings;
    }

    /**
     * Sets the authentication object;
     *
     * @author Catalin Alexandru Zamfir <catalin.zamfir@raphpframework.ro>
     * @copyright Under the terms of the GNU General Public License v3
     * @version $Id: Commons.php 23 2012-10-26 21:06:06Z root $
     */
    protected static final function setAuthentication (Authentication $objAuthentication) {
        // Set
        self::$objAuthentication = $objAuthentication;
    }

    /**
     * Returns the cookie object;
     *
     * @author Catalin Alexandru Zamfir <catalin.zamfir@raphpframework.ro>
     * @copyright Under the terms of the GNU General Public License v3
     * @version $Id: Commons.php 23 2012-10-26 21:06:06Z root $
     */
    protected function getCookie () {
        // Return
        return self::$objCookie[$this
        ->getObjectAncestry ()];
    }

    /**
     * Returns the (mod) path;
     *
     * @author Catalin Alexandru Zamfir <catalin.zamfir@raphpframework.ro>
     * @copyright Under the terms of the GNU General Public License v3
     * @version $Id: Commons.php 23 2012-10-26 21:06:06Z root $
     */
    protected function getPathTo () {
        // Return
        return $this->objPathToMod;
    }

    /**
     * Returns the path to (mod) admin;
     *
     * @author Catalin Alexandru Zamfir <catalin.zamfir@raphpframework.ro>
     * @copyright Under the terms of the GNU General Public License v3
     * @version $Id: Commons.php 23 2012-10-26 21:06:06Z root $
     */
    protected function getPathToAdmin () {
        // Check
        if (!isset ($this->objPathToAdm))
        $this->objPathToAdm = new Path (Architecture
        ::pathTo ($this->objPathToMod, ADMIN_DIR));

        // Return
        return $this->objPathToAdm;
    }

    /**
     * Returns the (mod) path to skin;
     *
     * @author Catalin Alexandru Zamfir <catalin.zamfir@raphpframework.ro>
     * @copyright Under the terms of the GNU General Public License v3
     * @version $Id: Commons.php 23 2012-10-26 21:06:06Z root $
     */
    protected function getPathToSkin () {
        // Check
        if (!isset ($this->objPathToSkn))
        $this->objPathToSkn = new
        Path (Architecture::pathTo ($this
        ->objPathToMod, SKIN_DIR_DIR,
        $this->objSkin));

        // Return
        return $this->objPathToSkn;
    }

    /**
     * Returns the (mod) path to language;
     *
     * @author Catalin Alexandru Zamfir <catalin.zamfir@raphpframework.ro>
     * @copyright Under the terms of the GNU General Public License v3
     * @version $Id: Commons.php 23 2012-10-26 21:06:06Z root $
     */
    protected function getPathToLanguage () {
        // Check
        if (!isset ($this->objPathToLng))
        $this->objPathToLng = new Path (Architecture
        ::pathTo ($this->objPathToMod,
        LANGUAGE_DIR, $this->objLang));

        // Return
        return $this->objPathToLng;
    }

    /**
     * Returns the (mod) path to the skin css;
     *
     * @author Catalin Alexandru Zamfir <catalin.zamfir@raphpframework.ro>
     * @copyright Under the terms of the GNU General Public License v3
     * @version $Id: Commons.php 23 2012-10-26 21:06:06Z root $
     */
    protected function getPathToSkinCSS () {
        // Return
        return new Path (Architecture
        ::pathTo ($this->getPathToSkin (),
        SKIN_CSS_DIR));
    }

    /**
     * Returns the (mod) path to skin jss;
     *
     * @author Catalin Alexandru Zamfir <catalin.zamfir@raphpframework.ro>
     * @copyright Under the terms of the GNU General Public License v3
     * @version $Id: Commons.php 23 2012-10-26 21:06:06Z root $
     */
    protected function getPathToSkinJSS () {
        // Return
        return new Path (Architecture
        ::pathTo ($this->getPathToSkin (),
        SKIN_JSS_DIR));
    }

    /**
     * Returns the (mod) path to the skin image directory;
     *
     * @author Catalin Alexandru Zamfir <catalin.zamfir@raphpframework.ro>
     * @copyright Under the terms of the GNU General Public License v3
     * @version $Id: Commons.php 23 2012-10-26 21:06:06Z root $
     */
    protected function getPathToSkinIMG () {
        // Return
        return new Path (Architecture
        ::pathTo ($this->getPathToSkin (),
        SKIN_IMG_DIR));
    }

    /**
     * Returns the (mod) path to the upload directory;
     *
     * @author Catalin Alexandru Zamfir <catalin.zamfir@raphpframework.ro>
     * @copyright Under the terms of the GNU General Public License v3
     * @version $Id: Commons.php 23 2012-10-26 21:06:06Z root $
     */
    protected function getPathToUpload () {
        // Return
        return Architecture::pathTo (Architecture::getHost (),
        UPLOAD_DIR, $this->getObjectAncestry ()->toLower ());
    }

    /**
     * Ties in required database tables;
     *
     * @author Catalin Alexandru Zamfir <catalin.zamfir@raphpframework.ro>
     * @copyright Under the terms of the GNU General Public License v3
     * @version $Id: Commons.php 23 2012-10-26 21:06:06Z root $
     */
    protected function tieInDatabase (A $objTableArray) {
        // Foreach
        foreach ($objTableArray as $objK => $objV) {
            // Check
            if (self::checkExists ($objV)
            ->toBoolean () == FALSE) {
                // DB: Auto-create
                $objQueryDB = new Contents (Architecture
                ::pathTo ($this->getPathTo (), CFG_DIR, $this
                ->getObjectAncestry () . SCH_EXTENSION));

                // Foreach
                foreach (_S ((string) $objQueryDB)
                ->fromStringToArray (RA_SCHEMA_HASH_TAG)
                as $objQK => $objQV) {
                    // Set
                    $this->_Q (_S ($objQV));
                }
            }
        }

        // Check
        if ($this->getReflection ()
        ->hasProperty ('objCategory')) {
            // Enable
            $this->enableHierarchy ();
        }
    }

    /**
     * Ties in with the authentication object;
     *
     * @author Catalin Alexandru Zamfir <catalin.zamfir@raphpframework.ro>
     * @copyright Under the terms of the GNU General Public License v3
     * @version $Id: Commons.php 23 2012-10-26 21:06:06Z root $
     */
    public function tieInAuthentication (Authentication $objAuthentication) {
        // Make the tie with authentication;
        self::setAuthentication ($objAuthentication);

        // Set the proper zone
        if (self::getAuthentication ()
        ->checkZoneByName ($this->getObjectAncestry ())->toBoolean () == FALSE)
        self::getAuthentication ()->doMakeZone ($this->getObjectAncestry ());

        // Check
        if (self::getAuthentication ()
        ->checkAdministratorIsMappedToZone ($this->getObjectAncestry ())->toBoolean () == FALSE)
        self::getAuthentication ()->doMapAdministratorToZone ($this->getObjectAncestry ());

        // Check
        if ($this->getACLs () instanceof A) {
            // Foreach
            foreach ($this->getACLs () as $objK => $objV) {
                // Check
                if (self::getAuthentication ()->checkZoneByName ($objV)->toBoolean () == FALSE)
                self::getAuthentication ()->doMakeZone ($objV, $this->getObjectAncestry ());

                // Check
                if (self::getAuthentication ()->checkAdministratorIsMappedToZone ($objV)->toBoolean () == FALSE)
                self::getAuthentication ()->doMapAdministratorToZone ($objV);
            }
        }
    }

    /**
     * Ties in with the administration object;
     *
     * @author Catalin Alexandru Zamfir <catalin.zamfir@raphpframework.ro>
     * @copyright Under the terms of the GNU General Public License v3
     * @version $Id: Commons.php 23 2012-10-26 21:06:06Z root $
     */
    public function tieInAdministration (Administration $objAdministration) {
        // Tie
        self::$objAdministration = $objAdministration;
    }

    /**
     * Ties in with the frontend object;
     *
     * @author Catalin Alexandru Zamfir <catalin.zamfir@raphpframework.ro>
     * @copyright Under the terms of the GNU General Public License v3
     * @version $Id: Commons.php 23 2012-10-26 21:06:06Z root $
     */
    public function tieInFrontend (Frontend $objFrontendObject) {
        // Frontend
        self::$objFrontend = $objFrontendObject;

        // Set
        $objThis = (string) $this->getObjectAncestry ();
        self::$objFrontend->$objThis = $this;
    }

    /**
     * Returns a HELP (Human Edited Language Profile) content from the path;
     *
     * @author Catalin Alexandru Zamfir <catalin.zamfir@raphpframework.ro>
     * @copyright Under the terms of the GNU General Public License v3
     * @version $Id: Commons.php 23 2012-10-26 21:06:06Z root $
     */
    protected function getHELP (S $objHumanLangProfile) {
        // Get contents
        return new S ((string) new Contents (Architecture
        ::pathTo ($this->getPathToLanguage (), __FUNCTION__,
        $objHumanLangProfile . HLP_EXTENSION)));
    }

    /**
     * Checks th given item title is unique;
     *
     * @author Catalin Alexandru Zamfir <catalin.zamfir@raphpframework.ro>
     * @copyright Under the terms of the GNU General Public License v3
     * @version $Id: Commons.php 23 2012-10-26 21:06:06Z root $
     */
    public function checkItemTitleIsUnique (S $objItemTitle) {
        // Return
        return $this->checkItemIsUnique (static::$objItem,
        static::$objItemTitle, $objItemTitle);
    }

    /**
     * Checks the given item URL is unique;
     *
     * @author Catalin Alexandru Zamfir <catalin.zamfir@raphpframework.ro>
     * @copyright Under the terms of the GNU General Public License v3
     * @version $Id: Commons.php 23 2012-10-26 21:06:06Z root $
     */
    public function checkItemURLIsUnique (S $objItemURL) {
        // Return
        return $this->checkItemIsUnique (static::$objItem,
        static::$objItemURL, $objItemURL);
    }

    /**
     * Checks given category URL is unique;
     *
     * @author Catalin Alexandru Zamfir <catalin.zamfir@raphpframework.ro>
     * @copyright Under the terms of the GNU General Public License v3
     * @version $Id: Commons.php 23 2012-10-26 21:06:06Z root $
     */
    public function checkCategoryURLIsUnique (S $objCategoryURL) {
        // Return
        return $this->checkItemIsUnique (static::$objCategory,
        static::$objCategoryURL, $objCategoryURL);
    }

    /**
     * Returns the item count;
     *
     * @author Catalin Alexandru Zamfir <catalin.zamfir@raphpframework.ro>
     * @copyright Under the terms of the GNU General Public License v3
     * @version $Id: Commons.php 23 2012-10-26 21:06:06Z root $
     */
    public function getItemCount (S $objSQLCondition = NULL) {
        // Return
        return new I ((int) (string) $this->_Q (_QS ('doSELECT')
        ->doToken ('%what', new S ('COUNT(%objItemId) AS count'))
        ->doToken ('%table', static::$objItem)
        ->doToken ('%condition', $objSQLCondition))
        ->offsetGet (0)->offsetGet ('count'));
    }

    /**
     * Returns the category count;
     *
     * @author Catalin Alexandru Zamfir <catalin.zamfir@raphpframework.ro>
     * @copyright Under the terms of the GNU General Public License v3
     * @version $Id: Commons.php 23 2012-10-26 21:06:06Z root $
     */
    public function getCategoryCount (S $objSQLCondition = NULL) {
        // Return
        return new I ((int) (string) $this->_Q (_QS ('doSELECT')
        ->doToken ('%what', new S ('COUNT(%objCategoryId) AS count'))
        ->doToken ('%table', static::$objCategory)
        ->doToken ('%condition', $objSQLCondition))
        ->offsetGet (0)->offsetGet ('count'));
    }

    /**
     * Returns the comment count;
     *
     * @author Catalin Alexandru Zamfir <catalin.zamfir@raphpframework.ro>
     * @copyright Under the terms of the GNU General Public License v3
     * @version $Id: Commons.php 23 2012-10-26 21:06:06Z root $
     */
    public function getCommentCount (S $objSQLCondition = NULL) {
        // Return
        return new I ((int) (string) $this->_Q (_QS ('doSELECT')
        ->doToken ('%what', new S ('COUNT(%objCommentId) AS count'))
        ->doToken ('%table', static::$objComment)
        ->doToken ('%condition', $objSQLCondition))
        ->offsetGet (0)->offsetGet ('count'));
    }

    /**
     * Returns the comment count by item id;
     *
     * @author Catalin Alexandru Zamfir <catalin.zamfir@raphpframework.ro>
     * @copyright Under the terms of the GNU General Public License v3
     * @version $Id: Commons.php 23 2012-10-26 21:06:06Z root $
     */
    public function getCommentCountByItemId (S $objItemId, S $objSQLCondition = NULL) {
        // Return
        return new I ((int) (string) $this->_Q (_QS ('doSELECT')
        ->doToken ('%what', new S ('COUNT(%objCommentId) AS count'))
        ->doToken ('%table', static::$objComment)
        ->doToken ('%condition', new S ('WHERE %objCommentAId = "%aId"'))
        ->doToken ('%aId', $objItemId))
        ->offsetGet (0)->offsetGet ('count'));
    }

    /**
     * Returns items based on condition;
     *
     * @author Catalin Alexandru Zamfir <catalin.zamfir@raphpframework.ro>
     * @copyright Under the terms of the GNU General Public License v3
     * @version $Id: Commons.php 23 2012-10-26 21:06:06Z root $
     */
    public function getItems (S $objSQLCondition = NULL) {
        // Return
        return $this->_Q (_QS ('doSELECT')
        ->doToken ('%what', new S ('DISTINCT t1.*'))
        ->doToken ('%table', static::$objItem->makeCopyObject ()
        ->appendString (_SP)->appendString ('AS t1'))
        ->doToken ('%condition', $objSQLCondition));
    }

    /**
     * Returns item by id;
     *
     * @author Catalin Alexandru Zamfir <catalin.zamfir@raphpframework.ro>
     * @copyright Under the terms of the GNU General Public License v3
     * @version $Id: Commons.php 23 2012-10-26 21:06:06Z root $
     */
    public function getItemById (S $objItemId, S $objFieldToGet) {
        // Return
        return $this->_Q (_QS ('doSELECT')
        ->doToken ('%what', $objFieldToGet)->doToken ('%table', static::$objItem)
        ->doToken ('%condition', new S ('WHERE %objItemId = "%Id" LIMIT 1'))
        ->doToken ('%Id', $objItemId))->offsetGet (0)->offsetGet ($objFieldToGet);
    }

    /**
     * Returns item by title;
     *
     * @author Catalin Alexandru Zamfir <catalin.zamfir@raphpframework.ro>
     * @copyright Under the terms of the GNU General Public License v3
     * @version $Id: Commons.php 23 2012-10-26 21:06:06Z root $
     */
    public function getItemByTitle (S $objItemTitle, S $objFieldToGet) {
        // Return
        return $this->_Q (_QS ('doSELECT')
        ->doToken ('%what', $objFieldToGet)->doToken ('%table', static::$objItem)
        ->doToken ('%condition', new S ('WHERE %objItemTitle = "%Id" LIMIT 1'))
        ->doToken ('%Id', $objItemTitle))->offsetGet (0)->offsetGet ($objFieldToGet);
    }

    /**
     * Returns item by URL;
     *
     * @author Catalin Alexandru Zamfir <catalin.zamfir@raphpframework.ro>
     * @copyright Under the terms of the GNU General Public License v3
     * @version $Id: Commons.php 23 2012-10-26 21:06:06Z root $
     */
    public function getItemByURL (S $objItemURL, S $objFieldToGet) {
        // Return
        return $this->_Q (_QS ('doSELECT')
        ->doToken ('%what', $objFieldToGet)->doToken ('%table', static::$objItem)
        ->doToken ('%condition', new S ('WHERE %objItemURL = "%Id" LIMIT 1'))
        ->doToken ('%Id', $objItemURL))->offsetGet (0)->offsetGet ($objFieldToGet);
    }

    /**
     * Returns previous item by current URL;
     *
     * @author Catalin Alexandru Zamfir <catalin.zamfir@raphpframework.ro>
     * @copyright Under the terms of the GNU General Public License v3
     * @version $Id: Commons.php 23 2012-10-26 21:06:06Z root $
     */
    public function getPreviousItemByURL (S $objItemURL, S $objFieldToGet) {
        // Return
        return $this->_Q (_QS ('doSELECT')
        ->doToken ('%what', $objFieldToGet)->doToken ('%table', static::$objItem)
        ->doToken ('%condition', new S ('WHERE %objItemPublished < "%Id"
        AND %objItemCategoryId = "%Cd" ORDER BY %objItemPublished DESC LIMIT 1'))
        ->doToken ('%Id', $this->getItemByURL ($objItemURL, static::$objItemPublished))
        ->doToken ('%Cd', $this->getItemByURL ($objItemURL, static::$objItemCategoryId)))
        ->offsetGet (0)->offsetGet ($objFieldToGet);
    }

	/**
     * Returns next item by current URL;
     *
     * @author Catalin Alexandru Zamfir <catalin.zamfir@raphpframework.ro>
     * @copyright Under the terms of the GNU General Public License v3
     * @version $Id: Commons.php 23 2012-10-26 21:06:06Z root $
     */
    public function getNextItemByURL (S $objItemURL, S $objFieldToGet) {
        // Return
        return $this->_Q (_QS ('doSELECT')
        ->doToken ('%what', $objFieldToGet)->doToken ('%table', static::$objItem)
        ->doToken ('%condition', new S ('WHERE %objItemPublished > "%Id"
        AND %objItemCategoryId = "%Cd" ORDER BY %objItemPublished ASC LIMIT 1'))
        ->doToken ('%Id', $this->getItemByURL ($objItemURL, static::$objItemPublished))
        ->doToken ('%Cd', $this->getItemByURL ($objItemURL, static::$objItemCategoryId)))
        ->offsetGet (0)->offsetGet ($objFieldToGet);
    }

    /**
     * Returns items by category id;
     *
     * @author Catalin Alexandru Zamfir <catalin.zamfir@raphpframework.ro>
     * @copyright Under the terms of the GNU General Public License v3
     * @version $Id: Commons.php 23 2012-10-26 21:06:06Z root $
     */
    public function getItemsByCategoryId (S $objCategoryId, S $objSQLCondition = NULL) {
        // Return
        return $this->_Q (_QS ('doSELECT')
        ->doToken ('%what', new S ('*'))->doToken ('%table', static::$objItem)
        ->doToken ('%condition', _S ('WHERE %objItemCategoryId = "%Id"')
        ->doToken ('%Id', $objCategoryId))->appendString (_SP)
        ->appendString ($objSQLCondition));
    }

    /**
     * Returns items by category name;
     *
     * @author Catalin Alexandru Zamfir <catalin.zamfir@raphpframework.ro>
     * @copyright Under the terms of the GNU General Public License v3
     * @version $Id: Commons.php 23 2012-10-26 21:06:06Z root $
     */
    public function getItemsByCategoryName (S $objCategoryName, S $objSQLCondition = NULL) {
        // Return
        return $this->getItemsByCategoryId ($this
        ->getCategoryByName ($objCategoryName,
        static::$objCategoryId), $objSQLCondition);
    }

    /**
     * Returns items by category URL;
     *
     * @author Catalin Alexandru Zamfir <catalin.zamfir@raphpframework.ro>
     * @copyright Under the terms of the GNU General Public License v3
     * @version $Id: Commons.php 23 2012-10-26 21:06:06Z root $
     */
    public function getItemsByCategoryURL (S $objCategoryURL, S $objSQLCondition = NULL) {
        // Return
        return $this->getItemsByCategoryId ($this
        ->getCategoryByURL ($objCategoryURL,
        static::$objCategoryId), $objSQLCondition);
    }

    /**
     * Returns categories by condition;
     *
     * @author Catalin Alexandru Zamfir <catalin.zamfir@raphpframework.ro>
     * @copyright Under the terms of the GNU General Public License v3
     * @version $Id: Commons.php 23 2012-10-26 21:06:06Z root $
     */
    public function getCategories (S $objSQLCondition = NULL, S $objSubCategory = NULL) {
        // Return
        return static::getHierarchy ()->mpttGetTree ($objSubCategory,
        $objSQLCondition);
    }

    /**
     * Returns category by id;
     *
     * @author Catalin Alexandru Zamfir <catalin.zamfir@raphpframework.ro>
     * @copyright Under the terms of the GNU General Public License v3
     * @version $Id: Commons.php 23 2012-10-26 21:06:06Z root $
     */
    public function getCategoryById (S $objCategoryId, S $objFieldToGet) {
        // Return
        return $this->_Q (_QS ('doSELECT')
        ->doToken ('%what', $objFieldToGet)->doToken ('%table', static::$objCategory)
        ->doToken ('%condition', new S ('WHERE %objCategoryId = "%Id" LIMIT 1'))
        ->doToken ('%Id', $objCategoryId))->offsetGet (0)->offsetGet ($objFieldToGet);
    }

    /**
     * Returns category by name;
     *
     * @author Catalin Alexandru Zamfir <catalin.zamfir@raphpframework.ro>
     * @copyright Under the terms of the GNU General Public License v3
     * @version $Id: Commons.php 23 2012-10-26 21:06:06Z root $
     */
    public function getCategoryByName (S $objCategoryName, S $objFieldToGet) {
        // Return
        return $this->_Q (_QS ('doSELECT')
        ->doToken ('%what', $objFieldToGet)->doToken ('%table', static::$objCategory)
        ->doToken ('%condition', new S ('WHERE %objCategoryName = "%Id" LIMIT 1'))
        ->doToken ('%Id', $objCategoryName))->offsetGet (0)->offsetGet ($objFieldToGet);
    }

    /**
     * Returns category by URL;
     *
     * @author Catalin Alexandru Zamfir <catalin.zamfir@raphpframework.ro>
     * @copyright Under the terms of the GNU General Public License v3
     * @version $Id: Commons.php 23 2012-10-26 21:06:06Z root $
     */
    public function getCategoryByURL (S $objCategoryURL, S $objFieldToGet) {
        return $this->_Q (_QS ('doSELECT')
        ->doToken ('%what', $objFieldToGet)->doToken ('%table', static::$objCategory)
        ->doToken ('%condition', new S ('WHERE %objCategoryURL = "%Id" LIMIT 1'))
        ->doToken ('%Id', $objCategoryURL))->offsetGet (0)->offsetGet ($objFieldToGet);
    }

    /**
     * Returns image by id;
     *
     * @author Catalin Alexandru Zamfir <catalin.zamfir@raphpframework.ro>
     * @copyright Under the terms of the GNU General Public License v3
     * @version $Id: Commons.php 23 2012-10-26 21:06:06Z root $
     */
    public function getImageById (S $objImageId, S $objFieldToGet) {
        // Return
        return $this->_Q (_QS ('doSELECT')
        ->doToken ('%what', $objFieldToGet)->doToken ('%table', static::$objImage)
        ->doToken ('%condition', new S ('WHERE %objImageId = "%Id" LIMIT 1'))
        ->doToken ('%Id', $objImageId))->offsetGet (0)->offsetGet ($objFieldToGet);
    }

    /**
     * Returns image by URL;
     *
     * @author Catalin Alexandru Zamfir <catalin.zamfir@raphpframework.ro>
     * @copyright Under the terms of the GNU General Public License v3
     * @version $Id: Commons.php 23 2012-10-26 21:06:06Z root $
     */
    public function getImageByURL (S $objImageURL, S $objFieldToGet) {
        // Return
        return $this->_Q (_QS ('doSELECT')
        ->doToken ('%what', $objFieldToGet)->doToken ('%table', static::$objImage)
        ->doToken ('%condition', new S ('WHERE %objImageURL = "%Id" LIMIT 1'))
        ->doToken ('%Id', $objImageURL))->offsetGet (0)->offsetGet ($objFieldToGet);
    }

    /**
     * Returns attachment by id;
     *
     * @author Catalin Alexandru Zamfir <catalin.zamfir@raphpframework.ro>
     * @copyright Under the terms of the GNU General Public License v3
     * @version $Id: Commons.php 23 2012-10-26 21:06:06Z root $
     */
    public function getAttachmentById (S $objAttachmentId, S $objFieldToGet) {
        // Return
        return $this->_Q (_QS ('doSELECT')
        ->doToken ('%what', $objFieldToGet)->doToken ('%table', static::$objAttachment)
        ->doToken ('%condition', new S ('WHERE %objAttachmentId = "%Id" LIMIT 1'))
        ->doToken ('%Id', $objAttachmentId))->offsetGet (0)->offsetGet ($objFieldToGet);
    }

    /**
     * Returns attachment by URL;
     *
     * @author Catalin Alexandru Zamfir <catalin.zamfir@raphpframework.ro>
     * @copyright Under the terms of the GNU General Public License v3
     * @version $Id: Commons.php 23 2012-10-26 21:06:06Z root $
     */
    public function getAttachmentByURL (S $objAttachmentURL, S $objFieldToGet) {
        // Return
        return $this->_Q (_QS ('doSELECT')
        ->doToken ('%what', $objFieldToGet)->doToken ('%table', static::$objAttachment)
        ->doToken ('%condition', new S ('WHERE %objAttachmentURL = "%Id" LIMIT 1'))
        ->doToken ('%Id', $objAttachmentURL))->offsetGet (0)->offsetGet ($objFieldToGet);
    }

    /**
     * Returns audio by id;
     *
     * @author Catalin Alexandru Zamfir <catalin.zamfir@raphpframework.ro>
     * @copyright Under the terms of the GNU General Public License v3
     * @version $Id: Commons.php 23 2012-10-26 21:06:06Z root $
     */
    public function getAudioById (S $objAudioId, S $objFieldToGet) {
        // Return
        return $this->_Q (_QS ('doSELECT')
        ->doToken ('%what', $objFieldToGet)->doToken ('%table', static::$objAudio)
        ->doToken ('%condition', new S ('WHERE %objAudioId = "%Id" LIMIT 1'))
        ->doToken ('%Id', $objAudioId))->offsetGet (0)->offsetGet ($objFieldToGet);
    }

    /**
     * Returns audio by URL;
     *
     * @author Catalin Alexandru Zamfir <catalin.zamfir@raphpframework.ro>
     * @copyright Under the terms of the GNU General Public License v3
     * @version $Id: Commons.php 23 2012-10-26 21:06:06Z root $
     */
    public function getAudioByURL (S $objAudioURL, S $objFieldToGet) {
        // Return
        return $this->_Q (_QS ('doSELECT')
        ->doToken ('%what', $objFieldToGet)->doToken ('%table', static::$objAudio)
        ->doToken ('%condition', new S ('WHERE %objAudioURL = "%Id" LIMIT 1'))
        ->doToken ('%Id', $objAudioURL))->offsetGet (0)->offsetGet ($objFieldToGet);
    }

    /**
     * Returns videos by id;
     *
     * @author Catalin Alexandru Zamfir <catalin.zamfir@raphpframework.ro>
     * @copyright Under the terms of the GNU General Public License v3
     * @version $Id: Commons.php 23 2012-10-26 21:06:06Z root $
     */
    public function getVideoById (S $objVideoId, S $objFieldToGet) {
        // Return
        return $this->_Q (_QS ('doSELECT')
        ->doToken ('%what', $objFieldToGet)->doToken ('%table', static::$objVideo)
        ->doToken ('%condition', new S ('WHERE %objVideoId = "%Id" LIMIT 1'))
        ->doToken ('%Id', $objVideoId))->offsetGet (0)->offsetGet ($objFieldToGet);
    }

    /**
     * Returns video by URL;
     *
     * @author Catalin Alexandru Zamfir <catalin.zamfir@raphpframework.ro>
     * @copyright Under the terms of the GNU General Public License v3
     * @version $Id: Commons.php 23 2012-10-26 21:06:06Z root $
     */
    public function getVideoByURL (S $objVideoURL, S $objFieldToGet) {
        // Return
        return $this->_Q (_QS ('doSELECT')
        ->doToken ('%what', $objFieldToGet)->doToken ('%table', static::$objVideo)
        ->doToken ('%condition', new S ('WHERE %objVideoURL = "%Id" LIMIT 1'))
        ->doToken ('%Id', $objVideoURL))->offsetGet (0)->offsetGet ($objFieldToGet);
    }

    /**
     * Checks item field is unqiue;
     *
     * @author Catalin Alexandru Zamfir <catalin.zamfir@raphpframework.ro>
     * @copyright Under the terms of the GNU General Public License v3
     * @version $Id: Commons.php 23 2012-10-26 21:06:06Z root $
     */
    public function checkItemIsUnique (S $objItemTable, S $objItemType, S $objItemData) {
        // Check
        if ($this->_Q (_QS ('doSELECT')
        ->doToken ('%condition', new S ('WHERE %what = "%Id" LIMIT 1'))
        ->doToken ('%what', $objItemType)->doToken ('%table', $objItemTable)
        ->doToken ('%Id', $objItemData))->doCount ()->toInt () == 0) {
            // Return
            return new B (TRUE);
        } else {
            // Return
            return new B (FALSE);
        }
    }

    /**
     * Returns image count by item id;
     *
     * @author Catalin Alexandru Zamfir <catalin.zamfir@raphpframework.ro>
     * @copyright Under the terms of the GNU General Public License v3
     * @version $Id: Commons.php 23 2012-10-26 21:06:06Z root $
     */
    public function getImageCountByItemId (S $objItemId, S $objSQLCondition = NULL) {
        // Return
        return new I ((int) (string) $this->_Q (_QS ('doSELECT')
        ->doToken ('%what', new S ('COUNT(*) as count'))->doToken ('%table', static::$objImage)
        ->doToken ('%condition', new S ('WHERE %objImageAId = "%Id" %condition'))
        ->doToken ('%Id', $objItemId)->doToken ('%condition', $objSQLCondition))
        ->offsetGet (0)->offsetGet ('count'));
    }

    /**
     * Returns property count by item id;
     *
     * @author Catalin Alexandru Zamfir <catalin.zamfir@raphpframework.ro>
     * @copyright Under the terms of the GNU General Public License v3
     * @version $Id: Commons.php 23 2012-10-26 21:06:06Z root $
     */
    public function getPropertyCountByItemId (S $objItemId, S $objSQLCondition = NULL) {
        // Return
        return new I ((int) (string) $this->_Q (_QS ('doSELECT')
        ->doToken ('%what', new S ('COUNT(*) as count'))->doToken ('%table', static::$objProperty)
        ->doToken ('%condition', new S ('WHERE %objPropertyAId = "%Id" %condition'))
        ->doToken ('%Id', $objItemId)->doToken ('%condition', $objSQLCondition))
        ->offsetGet (0)->offsetGet ('count'));
    }

    /**
     * Returns attachments by item id;
     *
     * @author Catalin Alexandru Zamfir <catalin.zamfir@raphpframework.ro>
     * @copyright Under the terms of the GNU General Public License v3
     * @version $Id: Commons.php 23 2012-10-26 21:06:06Z root $
     */
    public function getAttachmentsByItemId (S $objItemId, S $objSQLCondition = NULL) {
    	// Return
    	return $this->_Q (_QS ('doSELECT')
		->doToken ('%what', new S ('*'))->doToken ('%table', static::$objAttachment)
		->doToken ('%condition', new S ('WHERE %objAttachmentAId = "%Id" %condition'))
		->doToken ('%Id', $objItemId)->doToken ('%condition', $objSQLCondition));
    }

    /**
     * Returns images by item id;
     *
     * @author Catalin Alexandru Zamfir <catalin.zamfir@raphpframework.ro>
     * @copyright Under the terms of the GNU General Public License v3
     * @version $Id: Commons.php 23 2012-10-26 21:06:06Z root $
     */
    public function getImagesByItemId (S $objItemId, S $objSQLCondition = NULL) {
        // Return
        return $this->_Q (_QS ('doSELECT')
        ->doToken ('%what', new S ('*'))->doToken ('%table', static::$objImage)
        ->doToken ('%condition', new S ('WHERE %objImageAId = "%Id" %condition'))
        ->doToken ('%Id', $objItemId)->doToken ('%condition', $objSQLCondition));
    }

    /**
     * Returns images by item URL;
     *
     * @author Catalin Alexandru Zamfir <catalin.zamfir@raphpframework.ro>
     * @copyright Under the terms of the GNU General Public License v3
     * @version $Id: Commons.php 23 2012-10-26 21:06:06Z root $
     */
    public function getImagesByItemURL (S $objItemURL, S $objSQLCondition = NULL) {
        // Return
        return $this->getImagesByItemId ($this->getItemByURL ($objItemURL,
        static::$objItemId), $objSQLCondition);
    }

    /**
     * Returns videos by item id;
     *
     * @author Catalin Alexandru Zamfir <catalin.zamfir@raphpframework.ro>
     * @copyright Under the terms of the GNU General Public License v3
     * @version $Id: Commons.php 23 2012-10-26 21:06:06Z root $
     */
    public function getVideosByItemId (S $objItemId, S $objSQLCondition = NULL) {
        // Return
        return $this->_Q (_QS ('doSELECT')
        ->doToken ('%what', new S ('*'))->doToken ('%table', static::$objVideo)
        ->doToken ('%condition', new S ('WHERE %objVideoAId = "%Id" %condition'))
        ->doToken ('%Id', $objItemId)->doToken ('%condition', $objSQLCondition));
    }

    /**
     * Returns videos by item URL;
     *
     * @author Catalin Alexandru Zamfir <catalin.zamfir@raphpframework.ro>
     * @copyright Under the terms of the GNU General Public License v3
     * @version $Id: Commons.php 23 2012-10-26 21:06:06Z root $
     */
    public function getVideosByItemURL (S $objItemURL, S $objSQLCondition = NULL) {
        // Return
        return $this->getVideosByItemId ($this->getItemByURL ($objItemURL,
        static::$objItemId), $objSQLCondition);
    }

    /**
     * Returns property by item id;
     *
     * @author Catalin Alexandru Zamfir <catalin.zamfir@raphpframework.ro>
     * @copyright Under the terms of the GNU General Public License v3
     * @version $Id: Commons.php 23 2012-10-26 21:06:06Z root $
     */
    public function getPropertyByItemId (S $objItemId, S $objPropertyName, S $objSQLCondition = NULL) {
        // Return
        return $this->_Q (_QS ('doSELECT')
        ->doToken ('%what', new S ('*'))->doToken ('%table', static::$objProperty)
        ->doToken ('%condition', new S ('WHERE %objPropertyAId = "%cId"
        AND %objPropertyKey = "%kId" %condition'))
        ->doToken ('%condition', $objSQLCondition)
        ->doToken ('%cId', $objItemId)->doToken ('%kId', $objPropertyName))
        ->offsetGet (0)->offsetGet (static::$objPropertyVar);
    }

    /**
     * Returns properties by item id;
     *
     * @author Catalin Alexandru Zamfir <catalin.zamfir@raphpframework.ro>
     * @copyright Under the terms of the GNU General Public License v3
     * @version $Id: Commons.php 23 2012-10-26 21:06:06Z root $
     */
    public function getPropertiesByItemId (S $objItemId, S $objSQLCondition = NULL) {
        // Return
        return $this->_Q (_QS ('doSELECT')
        ->doToken ('%what', new S ('*'))->doToken ('%table', static::$objProperty)
        ->doToken ('%condition', new S ('WHERE %objPropertyAId = "%Id" %condition'))
        ->doToken ('%Id', $objItemId)->doToken ('%condition', $objSQLCondition));
    }

    /**
     * Returns property count by category id;
     *
     * @author Catalin Alexandru Zamfir <catalin.zamfir@raphpframework.ro>
     * @copyright Under the terms of the GNU General Public License v3
     * @version $Id: Commons.php 23 2012-10-26 21:06:06Z root $
     */
    public function getPropertyCountByCategoryId (S $objCategoryId, S $objSQLCondition = NULL) {
        // Return
        return new I ((int) (string) $this->_Q (_QS ('doSELECT')
        ->doToken ('%what', new S ('COUNT(*) as count'))->doToken ('%table', static::$objCategoryProperty)
        ->doToken ('%condition', new S ('WHERE %objCategoryPropertyCId = "%Id" %condition'))
        ->doToken ('%Id', $objCategoryId)->doToken ('%condition', $objSQLCondition))
        ->offsetGet (0)->offsetGet ('count'));
    }

    /**
     * Returns property by category id;
     *
     * @author Catalin Alexandru Zamfir <catalin.zamfir@raphpframework.ro>
     * @copyright Under the terms of the GNU General Public License v3
     * @version $Id: Commons.php 23 2012-10-26 21:06:06Z root $
     */
    public function getPropertyByCategoryId (S $objCategoryId, S $objPropertyName) {
        // Return
        return $this->_Q (_QS ('doSELECT')
        ->doToken ('%what', new S ('*'))->doToken ('%table', static::$objCategoryProperty)
        ->doToken ('%condition', new S ('WHERE %objCategoryPropertyCId = "%cId"
        AND %objCategoryPropertyKey = "%kId"'))
        ->doToken ('%cId', $objCategoryId)->doToken ('%kId', $objPropertyName))
        ->offsetGet (0)->offsetGet (static::$objCategoryPropertyVar);
    }

    /**
     * Returns properties by category id;
     *
     * @author Catalin Alexandru Zamfir <catalin.zamfir@raphpframework.ro>
     * @copyright Under the terms of the GNU General Public License v3
     * @version $Id: Commons.php 23 2012-10-26 21:06:06Z root $
     */
    public function getPropertiesByCategoryId (S $objCategoryId, S $objSQLCondition = NULL) {
        // Return
        return $this->_Q (_QS ('doSELECT')
        ->doToken ('%what', new S ('*'))->doToken ('%table', static::$objCategoryProperty)
        ->doToken ('%condition', new S ('WHERE %objCategoryPropertyCId = "%Id" %condition'))
        ->doToken ('%Id', $objCategoryId)->doToken ('%condition', $objSQLCondition));
    }

    /**
     * Returns comments;
     *
     * @author Catalin Alexandru Zamfir <catalin.zamfir@raphpframework.ro>
     * @copyright Under the terms of the GNU General Public License v3
     * @version $Id: Commons.php 23 2012-10-26 21:06:06Z root $
     */
    public function getComments (S $objSQLCondition = NULL) {
        // Return
        return $this->_Q (_QS ('doSELECT')
        ->doToken ('%what', new S ('*'))->doToken ('%table', static::$objComment)
        ->doToken ('%condition', $objSQLCondition));
    }

    /**
     * Returns comments by id;
     *
     * @author Catalin Alexandru Zamfir <catalin.zamfir@raphpframework.ro>
     * @copyright Under the terms of the GNU General Public License v3
     * @version $Id: Commons.php 23 2012-10-26 21:06:06Z root $
     */
    public function getCommentById (S $objCommentId, S $objFieldToGet) {
        // Return
        return $this->_Q (_QS ('doSELECT')
        ->doToken ('%what', $objFieldToGet)->doToken ('%table', static::$objComment)
        ->doToken ('%condition', new S ('WHERE %objCommentId = "%Id" LIMIT 1'))
        ->doToken ('%Id', $objCommentId))->offsetGet (0)->offsetGet ($objFieldToGet);
    }

    /**
     * Returns comments by item URL;
     *
     * @author Catalin Alexandru Zamfir <catalin.zamfir@raphpframework.ro>
     * @copyright Under the terms of the GNU General Public License v3
     * @version $Id: Commons.php 23 2012-10-26 21:06:06Z root $
     */
    public function getCommentsByItemURL (S $objItemURL, S $objSQLCondition = NULL) {
        // Return
        return $this->_Q (_QS ('doSELECT')
        ->doToken ('%what', new S ('*'))->doToken ('%table', static::$objComment)
        ->doToken ('%condition', new S ('WHERE %objCommentAId = "%Id"'))
        ->doToken ('%Id', $this->getItemByURL ($objItemURL, static::$objItemId))
        ->appendString (_SP)->appendString ($objSQLCondition));
    }

    /**
     * Returns approved comments by item URL;
     *
     * @author Catalin Alexandru Zamfir <catalin.zamfir@raphpframework.ro>
     * @copyright Under the terms of the GNU General Public License v3
     * @version $Id: Commons.php 23 2012-10-26 21:06:06Z root $
     */
    public function getApprovedCommentsByItemURL (S $objItemURL, S $objSQLCondition = NULL) {
        // Requirements
        $objSQLCondition = $objSQLCondition == NULL ? new S : $objSQLCondition;

        // Return
        return $this->getCommentsByItemURL ($objItemURL, $objSQLCondition
        ->prependString (_SP)->prependString ('AND %objCommentApproved = "Y"'));
    }

    /**
     * Outputs contents of given table, via conditions, columns, flags, functions and hierarchy as JSON;
     *
     * @author Catalin Alexandru Zamfir <catalin.zamfir@raphpframework.ro>
     * @copyright Under the terms of the GNU General Public License v3
     * @version $Id: Commons.php 23 2012-10-26 21:06:06Z root $
     */
    protected function outputAsJson (S $objTable, S $objCondition = NULL, A $objColumns,
    A $objFuncs = NULL, Hierarchy $objMPTT = NULL) {
        // Set
        $objFlags = new A;

        // Check
        if ($this->getReflection ()
        ->hasProperty ('objItem')
        == TRUE) {
            // Check
            if ($objTable == static::$objItem) {
                // Check
                if ($this->getReflection ()
                ->hasProperty ('objImage')
                == TRUE) {
                    // Set
                    $objFlags['images'] =
                    new B (TRUE);
                }

                // Check
                if ($this->getReflection ()
                ->hasProperty ('objVideo')
                == TRUE) {
                    // Set
                    $objFlags['videos'] =
                    new B (TRUE);
                }

                // Check
                if ($this->getReflection ()
                ->hasProperty ('objProperty')
                == TRUE) {
                    // Set
                    $objFlags['properties'] =
                    new B (TRUE);
                }

                // Check
                if ($this->getReflection ()
                ->hasProperty ('objComment')
                == TRUE) {
                    // Set
                    $objFlags['comments'] =
                    new B (TRUE);
                }

                // Check
                if ($this->getReflection ()
                ->hasProperty ('objAttachment')
                == TRUE) {
                    // Set
                    $objFlags['attachments'] =
                    new B (TRUE);
                }

                // Check
                if ($this->getReflection ()
                ->hasProperty ('objAudio')
                == TRUE) {
                    // Set
                    $objFlags['audios'] =
                    new B (TRUE);
                }
            }
        }

        // Check
        if ($this->getReflection ()
        ->hasProperty ('objCategory')
        == TRUE) {
            // Check
            if ($objTable == static::$objCategory) {
                // Check
                if ($this->getReflection ()
                ->hasProperty ('objCategoryProperty')
                == TRUE) {
                    $objFlags['properties'] =
                    new B (TRUE);
                }
            }
        }

        // Check (Authentication)
        if ($this->getReflection ()
        ->hasProperty ('objGroup')
        == TRUE) {
            // Check
            if ($objTable == static::$objGroup) {
                // Check
                if ($this->getReflection ()
                ->hasProperty ('objGroupProperty')
                == TRUE) {
                    $objFlags['properties'] =
                    new B (TRUE);
                }
            }
        }

        // Check
        if ($objFuncs == NULL) {
            $objFuncs = new A;
        }

        // Check
        if (static::checkPOST (new S ('sEcho'))
        ->toBoolean () == TRUE) {
            // Requirements
            $objData = Array ();
            $objSavedCondition = new S;
            $objHadSearchedByColumn = new B (FALSE);

            // Check
            if ($objCondition == NULL) {
                // Set
                $objCondition = new S ;
            } else {
                // Save
                $objSavedCondition = $objCondition
                ->makeCopyObject ();
            }

            // Prepend
            $objCondition->prependString (_SP);

            // Check
            if ($_SESSION['POST']
            ->offsetGet ('sSearch')
            ->toLength ()->toInt () != 0) {
                // Set
                if ($objCondition->findPos ('WHERE') instanceof B) {
                    // Set
                    $objCondition = $objCondition
                    ->appendString (_SP)
                    ->appendString ('WHERE (');
                } else {
                    // Set
                    $objCondition = $objCondition
                    ->appendString (_SP)
                    ->appendString ('AND (');
                }

                // Foreach
                foreach ($objColumns as $objK => $objV) {
                    // Set
                    $objCondition->appendString ('%cId LIKE "%%sId%"')
                    ->doToken ('%cId', $objV->pregChange ('/((.*) \+ 0) AS/i', '$1'))
                    ->doToken ('%sId', $_SESSION['POST']
                    ->offsetGet ('sSearch'));

                    // Check
                    if ($objK != $objColumns->doCount ()->toInt () - 1) {
                        // Append
                        $objCondition
                        ->appendString (_SP)
                        ->appendString ('OR')
                        ->appendString (_SP);
                    }
                }

                // Set
                $objCondition->appendString (')');
            } else {
                // Foreach
                foreach ($_SESSION['POST'] as $objPK => $objPV) {
                    // Check
                    if (_S ((string) $objPK)
                    ->findPos ('sSearch_') instanceof I) {
                        // Check
                        if ($_SESSION['POST']
                        ->offsetGet ($objPK)
                        ->toLength ()->toInt () != 0) {
                            // Set
                            $objHadSearchedByColumn = new B (TRUE);

                            // Set
                            if ($objCondition->findPos ('WHERE') instanceof B) {
                                // Set
                                $objCondition = $objCondition
                                ->appendString (_SP)
                                ->appendString ('WHERE (');
                            } else {
                                // Set
                                $objCondition = $objCondition
                                ->appendString (_SP)
                                ->appendString ('AND (');
                            }

                            // Set
                            $objCondition->appendString ('%cId LIKE "%%sId%"')
                            ->doToken ('%cId', $objColumns[(int) (string)
                            _S ((string) $objPK)->doToken ('sSearch_', _NONE) + 1]
                            ->pregChange ('/\((.*) \+ 0\) AS /i', _NONE))
                            ->doToken ('%sId', $_SESSION['POST']
                            ->offsetGet ($objPK));

                            // Set
                            $objCondition->appendString (')');
                        }
                    }
                }
            }

            // Check
            if ($_SESSION['POST']
            ->offsetExists ('iSortCol_0')) {
                // Check
                if ((int) (string) $_SESSION['POST']
                ->offsetGet ('iSortingCols') > 0) {
                    // Set
                    $objCondition->appendString (_SP)
                    ->appendString ('ORDER BY')
                    ->appendString (_SP);
                }

                // Set
                for ($objI = (int) (string) $_SESSION['POST']
                ->offsetGet ('iSortingCols') - 1;
                $objI >= 0; $objI--) {
                    // Check
                    if ($_SESSION['POST']
                    ->offsetGet ('bSortable_' . (int) (string)
                    $_SESSION['POST']->offsetGet ('iSortCol_' . $objI)) == "true") {
                        // Set
                        $objCondition->appendString ($objColumns->offsetGet ($_SESSION['POST']
                        ->offsetGet ('iSortCol_' . $objI))->pregChange ('/.*AS/', _NONE) .
                        _SP . $_SESSION['POST']->offsetGet ('sSortDir_' . $objI));

                        // Check
                        if ($objI != 0) {
                            // Append
                            $objCondition->appendString (',')
                            ->appendString (_SP);
                        }
                    }
                }
            }

            // Get data by columns (no LIMIT)
            $objSQLData = new I ((int) (string) $this->_Q (_QS ('doSELECT')
            ->doToken ('%what', _S ('COUNT(%fId) AS count')
            ->doToken ('%fId', $objColumns->offsetGet (0)))
            ->doToken ('%table', $objTable)
            ->doToken ('%condition', $objCondition))
            ->offsetGet (0)->offsetGet ('count'));

            // Count (no LIMITing filters applied)
            $objSQLDataCount = $objSQLData;

            // Paging
            if ($_SESSION['POST']->offsetExists ('iDisplayStart') &&
            $_SESSION['POST']->offsetGet ('iDisplayLength') != '-1') {
                // Set
                $objCondition->appendString (_SP)
                ->appendString ('LIMIT %LowerLimit, %UpperLimit')
                ->doToken ('%LowerLimit', $_SESSION['POST']['iDisplayStart'])
                ->doToken ('%UpperLimit', $_SESSION['POST']['iDisplayLength']);
            }

            // Get data by columns
            $objSQLData = $this->_Q (_QS ('doSELECT')
            ->doToken ('%what', $objColumns->fromArrayToString (', '))
            ->doToken ('%table', $objTable)
            ->doToken ('%condition', $objCondition));

            // Count on filter
            $objCount = $this->_Q (_QS ('doSELECT')
            ->doToken ('%what', new S ('COUNT(*) as count'))
            ->doToken ('%table', $objTable)
            ->doToken ('%condition', $objSavedCondition))
            ->offsetGet (0)->offsetGet ('count');

            // Foreach
            foreach ($objSQLData as $objK => $objV) {
                // Set
                if ($objMPTT != NULL) {
                    // Go
                    foreach ($objMPTT->mpttGetTree () as
                    $objMK => $objMV) {
                        // Check
                        if ($objMV->offsetGet ('name') ==
                        $objV->offsetGet ('name')) {
                            // Set
                            $objData[$objK][] = (string) $objMV
                            ->offsetGet ('depth');
                        }
                    }
                }
            }

            // Go
            foreach ($objSQLData as $objK => $objV) {
                // Check
                foreach ($objV as $objKV => $objVV) {
                    // Check
                    if ($objFuncs
                    ->offsetExists ($objKV)) {
                        // Go
                        $objProc = $objFuncs
                        ->offsetGet ($objKV);

                        // Return
                        $objSQLData[$objK][$objKV] =
                        $objProc ($objVV);
                    }
                }
            }

            // Foreach
            foreach ($objSQLData as $objK => $objV) {
                // Go
                foreach ($objV as $objKV => $objVV) {
                    // Set
                    switch ($objKV) {
                        default:
                            $objData[$objK][] =
                            (string) html_entity_decode ($objVV, ENT_QUOTES, "UTF-8");
                            break;
                    }
                }

                // Set
                $objActions = new S;

                // Check
                if ($objFlags->offsetExists ('properties')) {
                    // Check
                    if ($objFlags
                    ->offsetGet ('properties')
                    ->toBoolean () == TRUE) {
                        // Properties
                        $objActions->appendString (_S ('<button title="Properties" class="properties" href="%hId"></button>')
                        ->doToken ('%hId', Location::rewriteTo (new A (Array (_T ('Do'), _T ('Id'))),
                        new A (Array (_T ('Properties'), $objV[static::$objItemId])))));
                    }
                }

                // Check
                if ($objFlags->offsetExists ('comments')) {
                    // Check
                    if ($objFlags
                    ->offsetGet ('comments')
                    ->toBoolean () == TRUE) {
                        // Properties
                        $objActions->appendString (_S ('<button title="Comments" class="comments" href="%hId"></button>')
                        ->doToken ('%hId', Location::rewriteTo (new A (Array (_T ('Do'), _T ('Id'))),
                        new A (Array (_T ('Comments'), $objV[static::$objItemId])))));
                    }
                }

                // Check
                if ($objFlags->offsetExists ('attachments')) {
                    // Check
                    if ($objFlags
                    ->offsetGet ('attachments')
                    ->toBoolean () == TRUE) {
                        // Properties
                        $objActions->appendString (_S ('<button title="Attachments" class="attachments" href="%hId"></button>')
                        ->doToken ('%hId', Location::rewriteTo (new A (Array (_T ('Do'), _T ('Id'))),
                        new A (Array (_T ('Attachments'), $objV[static::$objItemId])))));
                    }
                }

                // Set
                $objActions->appendString ('<br />');

                // Check
                if ($objFlags->offsetExists ('images')) {
                    // Check
                    if ($objFlags
                    ->offsetGet ('images')
                    ->toBoolean () == TRUE) {
                        // Properties
                        $objActions->appendString (_S ('<button title="Images" class="images" href="%hId"></button>')
                        ->doToken ('%hId', Location::rewriteTo (new A (Array (_T ('Do'), _T ('Id'))),
                        new A (Array (_T ('Images'), $objV[static::$objItemId])))));
                    }
                }

                // Check
                if ($objFlags->offsetExists ('audios')) {
                    // Check
                    if ($objFlags
                    ->offsetGet ('audios')
                    ->toBoolean () == TRUE) {
                        // Properties
                        $objActions->appendString (_S ('<button title="Audios" class="audios" href="%hId"></button>')
                        ->doToken ('%hId', Location::rewriteTo (new A (Array (_T ('Do'), _T ('Id'))),
                        new A (Array (_T ('Audios'), $objV[static::$objItemId])))));
                    }
                }

                // Check
                if ($objFlags->offsetExists ('videos')) {
                    // Check
                    if ($objFlags
                    ->offsetGet ('videos')
                    ->toBoolean () == TRUE) {
                        // Properties
                        $objActions->appendString (_S ('<button title="Videos" class="videos" href="%hId"></button>')
                        ->doToken ('%hId', Location::rewriteTo (new A (Array (_T ('Do'), _T ('Id'))),
                        new A (Array (_T ('Videos'), $objV[static::$objItemId])))));
                    }
                }

                // Check
                if ($_GET
                ->offsetExists (_T ('Do'))) {
                    // Switch
                    switch ($_GET
                    ->offsetGet (_T ('Do'))) {
                        // Properties
                        case _T ('Properties'):
                            // Edit
                            $objActions->appendString (_S ('<button class="edit" href="%hId"></button>')
                            ->doToken ('%hId', Location::rewriteTo (new A (Array (_T ('Do Properties'),
                            _T ('Property Id'))), new A (Array (_T ('Edit'),
                            $objV[static::$objPropertyId])))));
                            break;

                        // Images
                        case _T ('Images'):
                            // Edit
                            $objActions->appendString (_S ('<button class="edit" href="%hId"></button>')
                            ->doToken ('%hId', Location::rewriteTo (new A (Array (_T ('Do Images'),
                            _T ('Image Id'))), new A (Array (_T ('Edit'),
                            $objV[static::$objImageId])))));
                            break;

                        // Videos
                        case _T ('Videos'):
                            // Edit
                            $objActions->appendString (_S ('<button class="edit" href="%hId"></button>')
                            ->doToken ('%hId', Location::rewriteTo (new A (Array (_T ('Do Videos'),
                            _T ('Video Id'))), new A (Array (_T ('Edit'),
                            $objV[static::$objVideoId])))));
                            break;

                        // Comments
                        case _T ('Comments'):
                            // Edit
                            $objActions->appendString (_S ('<button class="edit" href="%hId"></button>')
                            ->doToken ('%hId', Location::rewriteTo (new A (Array (_T ('Do Comments'),
                            _T ('Comment Id'))), new A (Array (_T ('Edit'),
                            $objV[static::$objCommentId])))));
                            break;

                        // Attachments
                        case _T ('Attachments'):
                            // Edit
                            $objActions->appendString (_S ('<button class="edit" href="%hId"></button>')
                            ->doToken ('%hId', Location::rewriteTo (new A (Array (_T ('Do Attachments'),
                            _T ('Attachment Id'))), new A (Array (_T ('Edit'),
                            $objV[static::$objCommentId])))));
                            break;

                        // Audios
                        case _T ('Audios'):
                            // Edit
                            $objActions->appendString (_S ('<button class="edit" href="%hId"></button>')
                            ->doToken ('%hId', Location::rewriteTo (new A (Array (_T ('Do Audios'),
                            _T ('Audio Id'))), new A (Array (_T ('Edit'),
                            $objV[static::$objCommentId])))));
                            break;
                    }
                } else {
                    // Edit
                    $objActions->appendString (_S ('<button class="edit" href="%hId"></button>')
                    ->doToken ('%hId', Location::rewriteTo (new A (Array (_T ('Do'), _T ('Id'))),
                    new A (Array (_T ('Edit'), $objV[static::$objItemId])))));
                }

                // Append
                $objActions->appendString (_SP);

                // Check
                if ($_GET
                ->offsetExists (_T ('Do'))) {
                    // Switch
                    switch ($_GET
                    ->offsetGet (_T ('Do'))) {
                        // Properties
                        case _T ('Properties'):
                            // Edit
                            $objActions->appendString (_S ('<button class="erase" href="%hId"></button>')
                            ->doToken ('%hId', Location::rewriteTo (new A (Array (_T ('Do Properties'),
                            _T ('Property Id'))), new A (Array (_T ('Erase'),
                            $objV[static::$objPropertyId])))));
                            break;

                        // Images
                        case _T ('Images'):
                            // Edit
                            $objActions->appendString (_S ('<button class="erase" href="%hId"></button>')
                            ->doToken ('%hId', Location::rewriteTo (new A (Array (_T ('Do Images'),
                            _T ('Image Id'))), new A (Array (_T ('Erase'),
                            $objV[static::$objImageId])))));
                            break;

                        // Videos
                        case _T ('Videos'):
                            // Edit
                            $objActions->appendString (_S ('<button class="erase" href="%hId"></button>')
                            ->doToken ('%hId', Location::rewriteTo (new A (Array (_T ('Do Videos'),
                            _T ('Video Id'))), new A (Array (_T ('Erase'),
                            $objV[static::$objVideoId])))));
                            break;

                        // Comments
                        case _T ('Comments'):
                            // Edit
                            $objActions->appendString (_S ('<button class="erase" href="%hId"></button>')
                            ->doToken ('%hId', Location::rewriteTo (new A (Array (_T ('Do Comments'),
                            _T ('Comment Id'))), new A (Array (_T ('Erase'),
                            $objV[static::$objCommentId])))));
                            break;

                        // Attachments
                        case _T ('Attachments'):
                            // Edit
                            $objActions->appendString (_S ('<button class="erase" href="%hId"></button>')
                            ->doToken ('%hId', Location::rewriteTo (new A (Array (_T ('Do Attachments'),
                            _T ('Attachment Id'))), new A (Array (_T ('Erase'),
                            $objV[static::$objCommentId])))));
                            break;

                        // Audios
                        case _T ('Audios'):
                            // Edit
                            $objActions->appendString (_S ('<button class="erase" href="%hId"></button>')
                            ->doToken ('%hId', Location::rewriteTo (new A (Array (_T ('Do Audios'),
                            _T ('Audio Id'))), new A (Array (_T ('Erase'),
                            $objV[static::$objCommentId])))));
                            break;
                    }
                } else {
                    // Erase
                    $objActions->appendString (_S ('<button class="erase" href="%hId"></button>')
                    ->doToken ('%hId', Location::rewriteTo (new A (Array (_T ('Do'), _T ('Id'))),
                    new A (Array (_T ('Erase'), $objV[static::$objItemId])))));
                }

                // Set
                $objData[$objK][] = (string) $objActions;
            }

            // Make it a JSON array
            $objData = Array ('sEcho' => (int) (string) $_SESSION['POST']['sEcho'],
            'iTotalRecords' => (int) (string) $objCount,
            'iTotalDisplayRecords' => $_SESSION['POST']
            ->offsetGet ('sSearch')->toLength ()->toInt () != 0 ?
            $objSQLDataCount->toInt () : ($objHadSearchedByColumn
            ->toBoolean () == TRUE ? $objSQLDataCount->toInt () :
            (int) (string) $objCount), 'aaData' => $objData);

            // Output
            static::outputString (new S (json_encode ($objData)));
        } else
        // Check
        if (self::checkPOST (new S ('oExport'))
        ->toBoolean () == TRUE) {
            // Set
            $objCSV = new S;

            // Output
            $objArray = self::getPOST (new S ('oExport'))
            ->removeStr ()->decodeJSON ();

            // Output
            $objStream = fopen ('php://temp', 'r+');

            // Foreach
            foreach ($objArray as $objK => $objV) {
                // Unset 'Actions'
                unset ($objV[$objV
                ->doCount ()->toInt () - 1]);

                // Put to CSV
                fputcsv ($objStream, $objV
                ->toArray (), ';', '"');
            }

            // Rewind
            rewind ($objStream);

            // Read
            while (!feof ($objStream)) {
                // Append
                $objCSV->appendString
                (fread ($objStream, 32768));
            }

            // Erase
            fclose ($objStream);

            // Write to
            $_SESSION['CSV Export'] = $objCSV;

            // Return
            static::outputString (Location::rewriteTo (new
            A (Array (_T ('CSV Export'))), new
            A (Array (_T ('Ok')))));
        } else
        // Check
        if ($_GET->offsetExists ('CSV Export')) {
            // MIME Type
            Header::setKey (new S (Header
            ::CONTENT_TYPE_TEXT_CSV),
            new S ('Content-Type'));

            // Force 'Save As'
            Header::setKey (new
            S ('attachment;filename=Export.csv'),
            new S ('Content-Disposition'));

            // Output
            static::outputString ($_SESSION['CSV Export']);

            // Erase
            unset ($_SESSION['CSV Export']);
        }
    }

    /**
     * Returns the given _GET path as binary;
     *
     * @author Catalin Alexandru Zamfir <catalin.zamfir@raphpframework.ro>
     * @copyright Under the terms of the GNU General Public License v3
     * @version $Id: Commons.php 23 2012-10-26 21:06:06Z root $
     */
    public function getAsBinary () {
        // Check
        if ($_GET
        ->offsetExists (_T ('What'))) {
            // Switch
            switch ($_GET
            ->offsetGet (_T ('What'))) {
            	// Raw
            	case _T ('Raw'):
            		// Check
            		if ($_GET->offsetExists (_T ('Output'))) {
            			// Determine
            			$objFp =  new StoragePath (Architecture
						::pathTo (UPLOAD_DIR, $this->getObjectAncestry ()->toLower (),
						_I ((int) (string) $this->getAttachmentById ($_GET[_T ('Output')],
						static::$objAttachmentPublished))->toDateString ('Y/m/d'),
						$this->getAttachmentById ($_GET[_T ('Output')],
						static::$objAttachmentURL)));

            			// Header
            			Header::setKey (new S ($objFp->getMimeType ()),
						new S  ('Content-Type'));

            			// Content-Length
            			Header::setKey (_S ('%fId')
						->doToken ('%fId', $objFp
						->getPathInfo ('fsize')), new
						S ('Content-Length'));

            			// Accept-Ranges
            			Header::setKey (_S ('bytes'),
						new S ('Accept-Ranges'));

            			// Disposition
            			Header::setKey (_S ('attachment; filename="%fId"')->doToken ('%fId',
						$objFp->getPathInfo ('fname') . _DOT . $objFp->getPathInfo ('extension')),
						new S ('Content-Disposition'));

            			// Check
            			self::outputBinary ($objFp);
            		}
            		// BK;
            		break;

                // Images
                case _T ('Image'):
                    // Check
                    if ($_GET->offsetExists (_T ('Output'))) {
                        // Header
                        Header::setKey (new S (Header
                        ::CONTENT_TYPE_IMAGE_JPEG),
                        new S ('Content-Type'));

                        // Check
                        self::outputBinary (new StoragePath (Architecture
                        ::pathTo (UPLOAD_DIR, $this->getObjectAncestry ()->toLower (),
                        _I ((int) (string) $this->getImageById ($_GET[_T ('Output')],
                        static::$objImagePublished))->toDateString ('Y/m/d'),
                        $_GET[_T ('Width')] . _U . $_GET[_T ('Height')] . _U . $this
                        ->getImageById ($_GET[_T ('Output')], static::$objImageURL))));
                    }
                    // BK;
                    break;

				// Videos
                case _T ('Media'):
                    // Check
                    if ($_GET->offsetExists (_T ('Output'))) {
                        // Header
                        Header::setKey (new S (Header
                        ::CONTENT_TYPE_APPLICATION_OGG),
                        new S ('Content-Type'));

                        // Path
                        $objFp = new StoragePath (Architecture
                        ::pathTo (UPLOAD_DIR, $this->getObjectAncestry ()->toLower (),
                        _I ((int) (string) $this->getVideoById ($_GET[_T ('Output')],
                        static::$objVideoPublished))->toDateString ('Y/m/d'),
                        $this->getVideoById ($_GET[_T ('Output')],
                        static::$objVideoURL)));

                        // Content-Length
                        Header::setKey (_S ('%fId')
                        ->doToken ('%fId', $objFp
                        ->getPathInfo ('fsize')), new
                        S ('Content-Length'));

                        // Accept-Ranges
                        Header::setKey (_S ('bytes'),
                        new S ('Accept-Ranges'));

                        // Disposition
                        Header::setKey (new
                        S ('attachment; filename="Stream.ogg"'),
                        new S ('Content-Disposition'));

                        // Check
                        self::outputBinary ($objFp);
                    }
                    // BK;
                    break;
            }
        }
    }

    /**
     * Does an upstream __CALL to mapped functions;;
     *
     * @author Catalin Alexandru Zamfir <catalin.zamfir@raphpframework.ro>
     * @copyright Under the terms of the GNU General Public License v3
     * @version $Id: Commons.php 23 2012-10-26 21:06:06Z root $
     */
    public function __CALL ($objFunctionName, $objFunctionArgs) {
        // Switch
        switch ($objFunctionName) {
            default:
                // Return
                return parent::__CALL ($objFunctionName, $objFunctionArgs);
                break;
        }
    }

    /**
     * Renders the backend page;
     *
     * @author Catalin Alexandru Zamfir <catalin.zamfir@raphpframework.ro>
     * @copyright Under the terms of the GNU General Public License v3
     * @version $Id: Commons.php 23 2012-10-26 21:06:06Z root $
     */
    protected function renderBackend (S $objP) {
        // Requirements
        self::manageCSS (new
        Path (Architecture::pathTo ($this
        ->getPathToSkinCSS (), __FUNCTION__,
        $objP . CSS_EXTENSION)));

        self::manageJSS (new
        Path (Architecture::pathTo ($this
        ->getPathToSkinJSS (), __FUNCTION__,
        $objP . JSS_EXTENSION)));

        // Check
        if ($_GET
        ->offsetExists (_T ('Editor'))) {
            // Switch
            switch ($_GET
            ->offsetGet (_T ('Editor'))) {
                case 'Image':
                    // Set
                    $objAjaxString = new S ('var tinyMCEImageList = new Array (');

                    // Go
                    foreach ($this
                    ->getImagesByItemId ($_GET[_T ('Id')]) as
                    $objK => $objV) {
                        // Foreach
                        foreach ($this
                        ->getStandardImageSizes () as
                        $objRK => $objRV) {
                            // Title
                            $objImageTitle = $objV[static::$objImageTitle]->makeCopyObject ()
                            ->appendString (_DCSP)->appendString ((string) $objRK)
                            ->appendString (_DCSP)->appendString ((string) $objRV);

                            // URL
                            $objImageURL = _S (Architecture::getHost ())
                            ->appendString ('/Type/%tId/Method/GetAsBinary/What/Image/Output/%mId/Width/%wId/Height/%hId')
                            ->doToken ('%tId', $this->getObjectAncestry ())
                            ->doToken ('%mId', $objV[static::$objImageId])
                            ->doToken ('%wId', $objRK)->doToken ('%hId', $objRV);

                            // Append
                            $objAjaxString->appendString ('["')
                            ->appendString ($objImageTitle)
                            ->appendString ('", "')
                            ->appendString ($objImageURL)
                            ->appendString ('"],');
                        }
                    }

                    // End
                    $objAjaxString
                    ->appendString (');')
                    ->doToken (',);', ');');

                    // Output
                    static::outputString ($objAjaxString);
                    break;

                case 'Media':
                    // Set
                    $objAjaxString = new S ('var tinyMCEMediaList = new Array (');

                    // Go
                    foreach ($this
                    ->getVideosByItemId ($_GET[_T ('Id')]) as
                    $objK => $objV) {
                        // Title
                        $objImageTitle = $objV[static::$objVideoTitle]->makeCopyObject ();

                        // URL
                        $objImageURL = _S (Architecture::getHost ())
                        ->appendString ('/Type/%tId/Method/GetAsBinary/What/Media/Output/%mId')
                        ->doToken ('%tId', $this->getObjectAncestry ())
                        ->doToken ('%mId', $objV[static::$objVideoId]);

                        // Append
                        $objAjaxString->appendString ('["')
                        ->appendString ($objImageTitle)
                        ->appendString ('", "')
                        ->appendString ($objImageURL)
                        ->appendString ('"],');
                    }

                    // End
                    $objAjaxString
                    ->appendString (');')
                    ->doToken (',);', ');');

                    // Output
                    static::outputString ($objAjaxString);
                    break;

				case 'Link':
					// Set
					$objAjaxString = new S ('var tinyMCELinkList  = new Array (');

					// Go
					foreach ($this
					->getAttachmentsByItemId ($_GET[_T ('Id')]) as
					$objK => $objV) {
						// Title
						$objLinkTitle = $objV[static::$objAttachmentTitle];

						// URL
						$objLinkURL = _S (Architecture::getHost ())
						->appendString ('/Type/%tId/Method/GetAsBinary/What/Raw/Output/%mId')
						->doToken ('%tId', $this->getObjectAncestry ())
						->doToken ('%mId', $objV[static::$objAttachmentId]);

						// Append
						$objAjaxString->appendString ('["')
						->appendString ($objLinkTitle)
						->appendString ('", "')
						->appendString ($objLinkURL)
						->appendString ('"],');
					}

					// End
					$objAjaxString
					->appendString (');')
					->doToken (',);', ');');

					// Output
					static::outputString ($objAjaxString);
					break;
            }
        }

        // Switch
        switch ($objP) {
            case 'manageConfiguration':
                // Go
                static::mapTp ($this, $objP,
                _S (__FUNCTION__));
                break;

            case 'manageVideos':
                // Check
                if ($_GET
                ->offsetExists (_T ('Output'))) {
                    // Header
                    Header::setKey (new S (Header
                    ::CONTENT_TYPE_APPLICATION_OGG),
                    new S ('Content-Type'));

                    // Path
                    $objFp = new StoragePath (Architecture::pathTo (UPLOAD_DIR,
                    $this->getObjectAncestry ()->toLower (),
                    _I ((int) (string) $this->getVideoById ($_GET[_T ('Output')],
                    static::$objVideoPublished))->toDateString ('Y/m/d'),
                    $this->getVideoById ($_GET[_T ('Output')],
                    static::$objVideoURL)));

                    // Content-Length
                    Header::setKey (_S ('%fId')
                    ->doToken ('%fId', $objFp
                    ->getPathInfo ('fsize')), new
                    S ('Content-Length'));

                    // Accept-Ranges
                    Header::setKey (_S ('bytes'),
                    new S ('Accept-Ranges'));

                    // Content-Disposition
                    Header::setKey (new
                    S ('attachment; filename="Stream.ogg"'),
                    new S ('Content-Disposition'));

                    // Check
                    self::outputBinary ($objFp);
                }

                // Check
                if ($_GET
                ->offsetExists (_T ('Do Videos'))) {
                    // Switch
                    switch ($_GET
                    ->offsetGet (_T ('Do Videos'))) {
                        // Add
                        case _T( 'Add'):
                            $this->renderForm (new
                            S ('videoCreate'));
                            break;

                            // Edit
                        case _T ('Edit'):
                            $this->renderForm (new
                            S ('videoEdit'));
                            break;

                            // Erase
                        case _T ('Erase'):
                            $this->renderForm (new
                            S ('videoErase'));
                            break;
                    }
                } else {
                    // Condition
                    $objCondition = _S ('WHERE %objVideoAId = "%aId"')
                    ->doToken ('%aId', $_GET->offsetGet (_T ('Id')));

                    // Maps
                    $objMaps = new A (Array (static::$objVideoId,
                    static::$objVideoTitle,
                    static::$objVideoTags,
                    static::$objVideoCaption,
                    static::$objVideoURL,
                    static::$objVideoPublished->makeCopyObject ()
                    ->prependString ('DATE_FORMAT(FROM_UNIXTIME(')
                    ->appendString ('), "%Y/%m/%d %T")'),
                    static::$objVideoUpdated->makeCopyObject ()
                    ->prependString ('DATE_FORMAT(FROM_UNIXTIME(')
                    ->appendString ('), "%Y/%m/%d %T")')));

                    // Format
                    $objObject = $this;
                    $objThis = (string) $this->getObjectAncestry ();

                    // Pre-processors
                    $objFuncs = new A (Array ((string) static::$objVideoURL
                    => function ($objData) use ($objObject, $objThis) {
                        // Set
                        $objVideoId = _S ('vdo_')
                        ->appendString (Hasher::getUniqueHash (new
                        S ('sha512'), new S ((string) $objData)));

                        // Return
                        return _S ('<a href="#' . $objVideoId . '" rel="Box">%dId</a>
                        <div id="' . $objVideoId . '" class="video">
                            <video controls="controls" preload="none">
                                <source src="%uId" type="application/ogg" />
                            </video>
                        </div>')
                        ->doToken ('%uId', Location::rewriteTo (new
                        A (Array (_T ('Output'))), new A (Array ($objObject
                        ->getVideoByURL ($objData, $objThis::$objVideoId)))))
                        ->doToken ('%dId', $objData);
                    }));

                    // Output
                    $this->outputAsJson (static::$objVideo,
                    $objCondition, $objMaps, $objFuncs);

                    // Go
                    static::mapTp ($this, $objP,
                    _S (__FUNCTION__));
                }
                // BK;
                break;

            case 'manageImages':
                // Check
                if ($_GET
                ->offsetExists (_T ('Output'))) {
                    // Header
                    Header::setKey (new S (Header
                    ::CONTENT_TYPE_IMAGE_JPEG),
                    new S ('Content-Type'));

                    // Check
                    self::outputBinary (new StoragePath (Architecture
                    ::pathTo (UPLOAD_DIR, $this->getObjectAncestry ()->toLower (),
                    _I ((int) (string) $this->getImageById ($_GET[_T ('Output')],
                    static::$objImagePublished))->toDateString ('Y/m/d'),
                    '800' . _U . '600' . _U . $this->getImageById ($_GET[_T ('Output')],
                    static::$objImageURL))));
                }

                // Check
                if ($_GET
                ->offsetExists (_T ('Do Images'))) {
                    // Switch
                    switch ($_GET
                    ->offsetGet (_T ('Do Images'))) {
                        // Add
                        case _T( 'Add'):
                            $this->renderForm (new
                            S ('imageCreate'));
                            break;

                        // Edit
                        case _T ('Edit'):
                            $this->renderForm (new
                            S ('imageEdit'));
                            break;

                        // Erase
                        case _T ('Erase'):
                            $this->renderForm (new
                            S ('imageErase'));
                            break;
                    }
                } else {
                    // Condition
                    $objCondition = _S ('WHERE %objImageAId = "%aId"')
                    ->doToken ('%aId', $_GET->offsetGet (_T ('Id')));

                    // Maps
                    $objMaps = new A (Array (static::$objImageId,
                    static::$objImageTitle,
                    static::$objImageTags,
                    static::$objImageCaption,
                    static::$objImageURL,
                    static::$objImagePublished->makeCopyObject ()
                    ->prependString ('DATE_FORMAT(FROM_UNIXTIME(')
                    ->appendString ('), "%Y/%m/%d %T")'),
                    static::$objImageUpdated->makeCopyObject ()
                    ->prependString ('DATE_FORMAT(FROM_UNIXTIME(')
                    ->appendString ('), "%Y/%m/%d %T")')));

                    // Format
                    $objObject = $this;
                    $objThis = (string) $this
                    ->getObjectAncestry ();

                    // Pre-processors
                    $objFuncs = new A (Array ((string) static::$objImageURL
                    => function ($objData) use ($objObject, $objThis) {
                        // Set
                        $objImageId = _S ('img_')
                        ->appendString (Hasher::getUniqueHash (new
                        S ('sha512'), new S ((string) $objData)));

                        // Return
                        return _S ('<a href="#' .
                        $objImageId . '" rel="Box">
                        	<img width="128" src="%uId" />
                    	</a>
                        <div id="' . $objImageId . '" class="image">
                            <img src="%uId" width="710" />
                        </div>')
                        ->doToken ('%uId', Location::rewriteTo (new
                        A (Array (_T ('Output'))), new A (Array ($objObject
                        ->getImageByURL ($objData, $objThis::$objImageId)))))
                        ->doToken ('%dId', $objData);
                    }));

                    // Output
                    $this->outputAsJson (static::$objImage,
                    $objCondition, $objMaps, $objFuncs);

                    // Go
                    static::mapTp ($this, $objP,
                    _S (__FUNCTION__));
                }
                // BK;
                break;

            case 'manageAttachments':
                // Check
                if ($_GET
                ->offsetExists (_T ('Output'))) {
                    // Determine
                    $objFp =  new StoragePath (Architecture
                    ::pathTo (UPLOAD_DIR, $this->getObjectAncestry ()->toLower (),
                    _I ((int) (string) $this->getAttachmentById ($_GET[_T ('Output')],
                    static::$objAttachmentPublished))->toDateString ('Y/m/d'),
                    $this->getAttachmentById ($_GET[_T ('Output')],
                    static::$objAttachmentURL)));

					// Header
					Header::setKey (new S ($objFp->getMimeType ()),
					new S  ('Content-Type'));

					// Content-Length
					Header::setKey (_S ('%fId')
					->doToken ('%fId', $objFp
					->getPathInfo ('fsize')), new
					S ('Content-Length'));

					// Accept-Ranges
					Header::setKey (_S ('bytes'),
					new S ('Accept-Ranges'));

					// Disposition
					Header::setKey (_S ('attachment; filename="%fId"')->doToken ('%fId',
					$objFp->getPathInfo ('fname') . _DOT . $objFp->getPathInfo ('extension')),
					new S ('Content-Disposition'));

                    // Disposition
                    Header::setKey (_S ('attachment; filename="%fId"')->doToken ('%fId',
                    $objFp->getPathInfo ('fname') . _DOT . $objFp->getPathInfo ('extension')),
                    new S ('Content-Disposition'));

                    // Check
                    self::outputBinary ($objFp);
                }

                // Check
                if ($_GET
                ->offsetExists (_T ('Do Attachments'))) {
                    // Switch
                    switch ($_GET
                    ->offsetGet (_T ('Do Attachments'))) {
                        // Add
                        case _T( 'Add'):
                            $this->renderForm (new
                            S ('attachmentCreate'));
                            break;

                        // Edit
                        case _T ('Edit'):
                            $this->renderForm (new
                            S ('attachmentEdit'));
                            break;

                        // Erase
                        case _T ('Erase'):
                            $this->renderForm (new
                            S ('attachmentErase'));
                            break;
                    }
                } else {
                    // Condition
                    $objCondition = _S ('WHERE %objAttachmentAId = "%aId"')
                    ->doToken ('%aId', $_GET->offsetGet (_T ('Id')));

                    // Maps
                    $objMaps = new A (Array (static::$objAttachmentId,
                    static::$objAttachmentTitle,
                    static::$objAttachmentTags,
                    static::$objAttachmentCaption,
                    static::$objAttachmentURL,
                    static::$objAttachmentPublished->makeCopyObject ()
                    ->prependString ('DATE_FORMAT(FROM_UNIXTIME(')
                    ->appendString ('), "%Y/%m/%d %T")'),
                    static::$objAttachmentUpdated->makeCopyObject ()
                    ->prependString ('DATE_FORMAT(FROM_UNIXTIME(')
                    ->appendString ('), "%Y/%m/%d %T")')));

                    // Format
                    $objObject = $this;
                    $objThis = (string) $this
                    ->getObjectAncestry ();

                    // Pre-processors
                    $objFuncs = new A (Array ((string) static::$objAttachmentURL
                    => function ($objData) use ($objObject, $objThis) {
                        // Return
                        return _S ('<a href="%uId">%dId</a>')
                        ->doToken ('%uId', Location::rewriteTo (new
                        A (Array (_T ('Output'))), new A (Array ($objObject
                        ->getAttachmentByURL ($objData, $objThis::$objAttachmentId)))))
                        ->doToken ('%dId', $objData);
                    }));

                    // Output
                    $this->outputAsJson (static::$objAttachment,
                    $objCondition, $objMaps, $objFuncs);

                    // Go
                    static::mapTp ($this, $objP,
                    _S (__FUNCTION__));
                }
                // BK;
                break;

            case 'manageAudios':
                // Check
                if ($_GET
                ->offsetExists (_T ('Output'))) {
                    // Determine
                    $objFp =  new StoragePath (Architecture
                    ::pathTo (UPLOAD_DIR, $this->getObjectAncestry ()->toLower (),
                    _I ((int) (string) $this->getAudioById ($_GET[_T ('Output')],
                    static::$objAudioPublished))->toDateString ('Y/m/d'),
                    $this->getAudioById ($_GET[_T ('Output')],
                    static::$objAudioURL)));

                    // Header
                    Header::setKey (new S ($objFp
                    ->getMimeType ()), new S
                    ('Content-Type'));

                    // Disposition
                    Header::setKey (_S ('attachment; filename="%fId"')->doToken ('%fId',
                    $objFp->getPathInfo ('fname') . _DOT . $objFp->getPathInfo ('extension')),
                    new S ('Content-Disposition'));

                    // Check
                    self::outputBinary ($objFp);
                }

                // Check
                if ($_GET
                ->offsetExists (_T ('Do Audios'))) {
                    // Switch
                    switch ($_GET
                    ->offsetGet (_T ('Do Audios'))) {
                        // Add
                        case _T( 'Add'):
                            $this->renderForm (new
                            S ('audioCreate'));
                            break;

                        // Edit
                        case _T ('Edit'):
                            $this->renderForm (new
                            S ('audioEdit'));
                            break;

                        // Erase
                        case _T ('Erase'):
                            $this->renderForm (new
                            S ('audioErase'));
                            break;
                    }
                } else {
                    // Condition
                    $objCondition = _S ('WHERE %objAudioAId = "%aId"')
                    ->doToken ('%aId', $_GET->offsetGet (_T ('Id')));

                    // Maps
                    $objMaps = new A (Array (static::$objAudioId,
                    static::$objAudioTitle,
                    static::$objAudioTags,
                    static::$objAudioCaption,
                    static::$objAudioURL,
                    static::$objAudioPublished->makeCopyObject ()
                    ->prependString ('DATE_FORMAT(FROM_UNIXTIME(')
                    ->appendString ('), "%Y/%m/%d %T")'),
                    static::$objAudioUpdated->makeCopyObject ()
                    ->prependString ('DATE_FORMAT(FROM_UNIXTIME(')
                    ->appendString ('), "%Y/%m/%d %T")')));

                    // Format
                    $objObject = $this;
                    $objThis = (string) $this
                    ->getObjectAncestry ();

                    // Pre-processors
                    $objFuncs = new A (Array ((string) static::$objAudioURL
                    => function ($objData) use ($objObject, $objThis) {
                        // Return
                        return _S ('<a href="%uId">%dId</a>')
                        ->doToken ('%uId', Location::rewriteTo (new
                        A (Array (_T ('Output'))), new A (Array ($objObject
                        ->getAudioByURL ($objData, $objThis::$objAudioId)))))
                        ->doToken ('%dId', $objData);
                    }));

                    // Output
                    $this->outputAsJson (static::$objAudio,
                    $objCondition, $objMaps, $objFuncs);

                    // Go
                    static::mapTp ($this, $objP,
                    _S (__FUNCTION__));
                }
                // BK;
                break;

            case 'manageProperties':
                if ($_GET
                ->offsetExists (_T ('Do Properties'))) {
                    // Switch
                    switch ($_GET
                    ->offsetGet (_T ('Do Properties'))) {
                        // Add
                        case _T ('Add'):
                            $this->renderForm (new
                            S ('propertyCreate'));
                            break;

                            // Edit
                        case _T ('Edit'):
                            $this->renderForm (new
                            S ('propertyEdit'));
                            break;

                            // Erase
                        case _T ('Erase'):
                            $this->renderForm (new
                            S ('propertyErase'));
                            break;
                    }
                } else {
                    // Condition
                    $objCondition = _S ('WHERE %objPropertyAId = "%aId"')
                    ->doToken ('%aId', $_GET->offsetGet (_T ('Id')));

                    // Maps
                    $objMaps = new A (Array (static::$objPropertyId,
                    static::$objPropertyKey,
                    static::$objPropertyVar,
                    static::$objPropertyPublished->makeCopyObject ()
                    ->prependString ('DATE_FORMAT(FROM_UNIXTIME(')
                    ->appendString ('), "%Y/%m/%d %T")'),
                    static::$objPropertyUpdated->makeCopyObject ()
                    ->prependString ('DATE_FORMAT(FROM_UNIXTIME(')
                    ->appendString ('), "%Y/%m/%d %T")')));

                    // Output
                    $this->outputAsJson (static::$objProperty,
                    $objCondition, $objMaps);

                    // Go
                    static::mapTp ($this, $objP,
                    _S (__FUNCTION__));
                }
                // BK;
                break;

            case 'manageCategories':
                // Check
                if ($_GET
                ->offsetExists (_T ('Do'))) {
                    // Switch
                    switch ($_GET
                    ->offsetGet (_T ('Do'))) {
                        // Add
                        case _T ('Add'):
                            $this->renderForm (new
                            S ('categoryCreate'));
                            break;

                        // Edit
                        case _T ('Edit'):
                            $this->renderForm (new
                            S ('categoryEdit'));
                            break;

                        // Erase
                        case _T ('Erase'):
                            $this->renderForm (new
                            S ('categoryErase'));
                            break;

                        // Move
                        case _T ('Move'):
                            $this->renderForm (new
                            S ('categoryMove'));
                            break;

                        // Properties
                        case _T ('Properties'):
                            $this->renderBackend (new
                            S ('manageCategoryProperties'));
                            break;
                    }
                } else {
                    // Condition
                    $objCondition = new S;

                    // Maps
                    $objMaps = new A (Array (static::$objCategoryId,
                    new S ('lft'), static::$objCategoryName));

                    // Tree
                    $objTree = $this->getHierarchy ()->mpttGetTree ();
                    $objOffset = static::$objCategoryName;

                    // Pre-processors
                    $objFuncs = new A (Array ((string) static::$objCategoryName
                    => function ($objData) use ($objTree, $objOffset) {
                        // Return
                        foreach ($objTree as $objTK => $objTV) {
                            // Check
                            if ($objTV[$objOffset] == $objData) {
                                // Return
                                return Hierarchy::mpttRemoveUnique ($objData)
                                ->prependString (str_repeat (Hierarchy::PADDING,
                                (int) (string) $objTV['depth']));
                            }
                        }
                    }));

                    // Output
                    $this->outputAsJson (static::$objCategory,
                    $objCondition, $objMaps, $objFuncs,
                    $this->getHierarchy ());

                    // Go
                    static::mapTp ($this, $objP,
                    _S (__FUNCTION__));
                }
                // BK;
                break;

            case 'manageCategoryProperties':
                // Check
                if ($_GET
                ->offsetExists (_T ('Do Properties'))) {
                    // Switch
                    switch ($_GET
                    ->offsetGet (_T ('Do Properties'))) {
                        // Add
                        case _T ('Add'):
                            $this->renderForm (new
                            S ('propertyCategoryCreate'));
                            break;

                            // Edit
                        case _T ('Edit'):
                            $this->renderForm (new
                            S ('propertyCategoryEdit'));
                            break;

                            // Erase
                        case _T ('Erase'):
                            $this->renderForm (new
                            S ('propertyCategoryErase'));
                            break;
                    }
                } else {
                    // Condition
                    $objCondition = _S ('WHERE %objCategoryPropertyCId = "%aId"')
                    ->doToken ('%aId', $_GET->offsetGet (_T ('Id')));

                    // Maps
                    $objMaps = new A (Array (static::$objCategoryPropertyId,
                    static::$objCategoryPropertyKey,
                    static::$objCategoryPropertyVar,
                    static::$objCategoryPropertyPublished->makeCopyObject ()
                    ->prependString ('DATE_FORMAT(FROM_UNIXTIME(')
                    ->appendString ('), "%Y/%m/%d %T")'),
                    static::$objCategoryPropertyUpdated->makeCopyObject ()
                    ->prependString ('DATE_FORMAT(FROM_UNIXTIME(')
                    ->appendString ('), "%Y/%m/%d %T")')));

                    // Output
                    $this->outputAsJson (static::$objCategoryProperty,
                    $objCondition, $objMaps);

                    // Go
                    static::mapTp ($this, $objP,
                    _S (__FUNCTION__));
                }
                // BK;
                break;

            case 'manageComments':
                // Check
                if ($_GET
                ->offsetExists (_T ('Do Comments'))) {
                    // Switch
                    switch ($_GET
                    ->offsetGet (_T ('Do Comments'))) {
                        // Add
                        case _T ('Add'):
                            $this->renderForm (new
                            S ('commentCreate'));
                            break;

                            // Edit
                        case _T ('Edit'):
                            $this->renderForm (new
                            S ('commentEdit'));
                            break;

                            // Erase
                        case _T ('Erase'):
                            $this->renderForm (new
                            S ('commentErase'));
                            break;
                    }
                } else {
                    // Condition
                    $objCondition = new S ('AS t1 INNER JOIN %objItem AS t2
                    ON t1.%objCommentAId = t2.%objItemId WHERE t2.%objItemId = "%aId"');

                    // Tokenize
                    $objCondition->doToken ('%aId', $_GET[_T ('Id')]);

                    // Maps
                    $objMaps = new A (Array (static::$objCommentId->makeCopyObject ()
                    ->prependString (_DOT)->prependString ('t1'),
                    static::$objCommentName,
                    static::$objItemTitle,
                    static::$objCommentPublished->makeCopyObject ()
                    ->prependString ('DATE_FORMAT(FROM_UNIXTIME(t1.')
                    ->appendString ('), "%Y/%m/%d %T")'),
                    static::$objCommentUpdated->makeCopyObject ()
                    ->prependString ('DATE_FORMAT(FROM_UNIXTIME(t1.')
                    ->appendString ('), "%Y/%m/%d %T")')));

                    // Pre-processors
                    $objFuncs = new A;

                    // Output
                    $this->outputAsJson (static::$objComment,
                    $objCondition, $objMaps, $objFuncs);

                    // Go
                    static::mapTp ($this, $objP,
                    _S (__FUNCTION__));
                }
                // BK;
                break;
        }
    }

    /**
     * Renders a requested form;
     *
     * @author Catalin Alexandru Zamfir <catalin.zamfir@raphpframework.ro>
     * @copyright Under the terms of the GNU General Public License v3
     * @version $Id: Commons.php 23 2012-10-26 21:06:06Z root $
     */
    protected function renderForm (S $objF, A $objFArray = NULL) {
        // Switch
        switch ($objF) {
            case 'videoCreate':
                // Set the URL to go back too;
                $objURLToGoBack = Location::rewriteTo (new A (Array (_T ('Do Videos'))));

                // Form
                $this->setFieldset (_T ('Add video for: ')->appendString ($this
                ->getItemById ($_GET[_T ('Id')], static::$objItemTitle)))
                ->setTableName (static::$objVideo)
                ->setUpdateField (static::$objVideoId)
                ->setExtraUpdateData (static::$objVideoPublished, new S ((string) time ()))
                ->setExtraUpdateData (static::$objVideoUpdated, new S ((string) time ()))
                ->setExtraUpdateData (static::$objVideoAId, $_GET[_T ('Id')])
                ->setUploadDirectory (Architecture::pathTo ($this->getObjectAncestry ()->toLower (),
                _I (time ())->toDateString ('Y/m/d')))
                ->setUploadType (self::$objAcceptedVideoMimeTypes
                ->fromArrayToString ('|'))
                ->setName ($objF)
                ->setRedirect ($objURLToGoBack)
                ->setInputType (new S ('text'))
                ->setName (static::$objVideoTags)
                ->setLabel (_T ('Tags'))
                ->setCheckForEmpty (new B (TRUE))
                ->setInputInfo ($this->getHELP ($objF))
                ->setInputType (new S ('text'))
                ->setName (static::$objVideoTitle)
                ->setLabel (_T ('Title'))
                ->setCheckForEmpty (new B (TRUE))
                ->setCheckForUnique (new B (TRUE))
                ->setInputType (new S ('file'))
                ->setName (static::$objVideoURL)
                ->setLabel (_T ('Video'))
                ->setInputType (new S ('textarea'))
                ->setName (static::$objVideoCaption)
                ->setLabel (_T ('Caption'))
                ->setCheckForEmpty (new B (TRUE))
                ->setInputType (new S ('submit'))
                ->setValue (_T ('Add'))
                ->setAccessKey (new S ('S'))
                ->setFormEndAndExecute (new B (TRUE));
                break;

            case 'videoEdit':
                // Set the URL to go back too;
                $objURLToGoBack = Location::rewriteTo (new A (Array (_T ('Do Videos'), _T ('Video Id'))));

                // Form
                $this->setFieldset (_T ('Edit video for: ')->appendString ($this
                ->getItemById ($_GET[_T ('Id')], static::$objItemTitle)))
                ->setTableName (static::$objVideo)
                ->setUpdateId ($_GET[_T ('Video Id')])
                ->setUpdateField (static::$objVideoId)
                ->setName ($objF)
                ->setRedirect ($objURLToGoBack)
                ->setExtraUpdateData (static::$objVideoPublished, new S ((string) time ()))
                ->setExtraUpdateData (static::$objVideoUpdated, new S ((string) time ()))
                ->setUploadDirectory (Architecture::pathTo ($this->getObjectAncestry ()->toLower (),
                _I (time ())->toDateString ('Y/m/d')))
                ->setUploadType (self::$objAcceptedVideoMimeTypes
                ->fromArrayToString ('|'))
                ->setInputType (new S ('text'))
                ->setName (static::$objVideoTags)
                ->setLabel (_T ('Tags'))
                ->setCheckForEmpty (new B (TRUE))
                ->setInputInfo ($this->getHELP ($objF))
                ->setInputType (new S ('text'))
                ->setName (static::$objVideoTitle)
                ->setLabel (_T ('Title'))
                ->setCheckForEmpty (new B (TRUE))
                ->setCheckForUnique (new B (TRUE))
                ->setInputType (new S ('file'))
                ->setName (static::$objVideoURL)
                ->setLabel (_T ('Video'))
                ->setFileController (new B (TRUE))
                ->setInputType (new S ('textarea'))
                ->setName (static::$objVideoCaption)
                ->setLabel (_T ('Caption'))
                ->setCheckForEmpty (new B (TRUE))
                ->setInputType (new S ('submit'))
                ->setValue (_T ('Edit'))
                ->setAccessKey (new S ('S'))
                ->setFormEndAndExecute (new B (TRUE));
                break;

            case 'videoErase':
                // Set the URL to back too;
                $objURLToGoBack = Location::rewriteTo (new A (Array (_T ('Do Videos'), _T ('Video Id'))));

                // Set
                $objV = new A;

                // Set
                $objV[static::$objVideoPublished] = $this
                ->getVideoById ($_GET[_T ('Video Id')],
                static::$objVideoPublished);

                // Set
                $objV[static::$objVideoURL] = $this
                ->getVideoById ($_GET[_T ('Video Id')],
                static::$objVideoURL);

                // Origin
                _SP (Architecture::pathTo (UPLOAD_DIR, $this->getObjectAncestry ()->toLower (),
                _I ((int) (string) $objV[static::$objVideoPublished])->toDateString ('Y/m/d'),
                $objV[static::$objVideoURL]))->unLinkPath ();

                // Erase
                $this->_Q (_QS ('doDELETE')
                ->doToken ('%table', static::$objVideo)
                ->doToken ('%condition', new S ('%objVideoId = "%Id"'))
                ->doToken ('%Id', $_GET[_T ('Video Id')]));

                // Do a redirect, and get the user back where he belongs;
                Header::setKey ($objURLToGoBack, new S ('Location'));
                break;

            case 'imageCreate':
                // Set the URL to go back too;
                $objURLToGoBack = Location::rewriteTo (new A (Array (_T ('Do Images'))));

                // Form
                $this->setFieldset (_T ('Add image for: ')->appendString ($this
                ->getItemById ($_GET[_T ('Id')], static::$objItemTitle)))
                ->setTableName (static::$objImage)
                ->setUpdateField (static::$objImageId)
                ->setExtraUpdateData (static::$objImagePublished, new S ((string) time ()))
                ->setExtraUpdateData (static::$objImageUpdated, new S ((string) time ()))
                ->setExtraUpdateData (static::$objImageAId, $_GET[_T ('Id')])
                ->setUploadImageResize (static::getStandardImageSizes ())
                ->setUploadDirectory (Architecture::pathTo ($this->getObjectAncestry ()->toLower (),
                _I (time ())->toDateString ('Y/m/d')))
                ->setUploadType (self::$objAcceptedImageMimeTypes
                ->fromArrayToString ('|'))
                ->setName ($objF)
                ->setRedirect ($objURLToGoBack)
                ->setInputType (new S ('text'))
                ->setName (static::$objImageTags)
                ->setLabel (_T ('Tags'))
                ->setCheckForEmpty (new B (TRUE))
                ->setInputInfo ($this->getHELP ($objF))
                ->setInputType (new S ('text'))
                ->setName (static::$objImageTitle)
                ->setLabel (_T ('Title'))
                ->setCheckForEmpty (new B (TRUE))
                ->setCheckForUnique (new B (TRUE))
                ->setInputType (new S ('file'))
                ->setName (static::$objImageURL)
                ->setLabel (_T ('Image'))
                ->setInputType (new S ('textarea'))
                ->setName (static::$objImageCaption)
                ->setLabel (_T ('Caption'))
                ->setCheckForEmpty (new B (TRUE))
                ->setInputType (new S ('submit'))
                ->setValue (_T ('Add'))
                ->setAccessKey (new S ('S'))
                ->setFormEndAndExecute (new B (TRUE));
                break;

            case 'imageEdit':
                // Set the URL to go back too;
                $objURLToGoBack = Location::rewriteTo (new A (Array (_T ('Do Images'), _T ('Image Id'))));

                // Form
                $this->setFieldset (_T ('Edit image for: ')->appendString ($this
                ->getItemById ($_GET[_T ('Id')], static::$objItemTitle)))
                ->setTableName (static::$objImage)
                ->setUpdateId ($_GET[_T ('Image Id')])
                ->setUpdateField (static::$objImageId)
                ->setName ($objF)
                ->setRedirect ($objURLToGoBack)
                ->setExtraUpdateData (static::$objImagePublished, new S ((string) time ()))
                ->setExtraUpdateData (static::$objImageUpdated, new S ((string) time ()))
                ->setUploadImageResize (static::getStandardImageSizes ())
                ->setUploadDirectory (Architecture::pathTo ($this->getObjectAncestry ()->toLower (),
                _I (time ())->toDateString ('Y/m/d')))
                ->setUploadType (self::$objAcceptedImageMimeTypes
                ->fromArrayToString ('|'))
                ->setInputType (new S ('text'))
                ->setName (static::$objImageTags)
                ->setLabel (_T ('Tags'))
                ->setCheckForEmpty (new B (TRUE))
                ->setInputInfo ($this->getHELP ($objF))
                ->setInputType (new S ('text'))
                ->setName (static::$objImageTitle)
                ->setLabel (_T ('Title'))
                ->setCheckForEmpty (new B (TRUE))
                ->setCheckForUnique (new B (TRUE))
                ->setInputType (new S ('file'))
                ->setName (static::$objImageURL)
                ->setLabel (_T ('Image'))
                ->setFileController (new B (TRUE))
                ->setInputType (new S ('textarea'))
                ->setName (static::$objImageCaption)
                ->setLabel (_T ('Caption'))
                ->setCheckForEmpty (new B (TRUE))
                ->setInputType (new S ('submit'))
                ->setValue (_T ('Edit'))
                ->setAccessKey (new S ('S'))
                ->setFormEndAndExecute (new B (TRUE));
                break;

            case 'imageErase':
                // Set the URL to back too;
                $objURLToGoBack = Location::rewriteTo (new A (Array (_T ('Do Images'), _T ('Image Id'))));

                // Set
                $objV = new A;

                // Set
                $objV[static::$objImagePublished] = $this
                ->getImageById ($_GET[_T ('Image Id')],
                static::$objImagePublished);

                // Set
                $objV[static::$objImageURL] = $this
                ->getImageById ($_GET[_T ('Image Id')],
                static::$objImageURL);

                // Sizes
                foreach (static::getStandardImageSizes () as $objRK => $objRV) {
                    // Directory
                    _SP (Architecture::pathTo (UPLOAD_DIR,
                    $this->getObjectAncestry ()->toLower (),
                    _I ((int) (string) $objV[static::$objImagePublished])
                    ->toDateString ('Y/m/d'), $objRK . _U . $objRV . _U .
                    $objV[static::$objImageURL]))->unLinkPath ();
                }

                // Directory
                _SP (Architecture::pathTo (UPLOAD_DIR, $this->getObjectAncestry ()->toLower (),
                _I ((int) (string) $objV[static::$objImagePublished])->toDateString ('Y/m/d') .
                _S . $objV[static::$objImageURL]))->unLinkPath ();

                // Erase
                $this->_Q (_QS ('doDELETE')
                ->doToken ('%table', static::$objImage)
                ->doToken ('%condition', new S ('%objImageId = "%Id"'))
                ->doToken ('%Id', $_GET[_T ('Image Id')]));

                // Do a redirect, and get the user back where he belongs;
                Header::setKey ($objURLToGoBack, new S ('Location'));
                break;

            case 'attachmentCreate':
                // Set the URL to go back too;
                $objURLToGoBack = Location::rewriteTo (new A (Array (_T ('Do Attachments'))));

                // Form
                $this->setFieldset (_T ('Add attachment for: ')->appendString ($this
                ->getItemById ($_GET[_T ('Id')], static::$objItemTitle)))
                ->setTableName (static::$objAttachment)
                ->setUpdateField (static::$objAttachmentId)
                ->setExtraUpdateData (static::$objAttachmentPublished, new S ((string) time ()))
                ->setExtraUpdateData (static::$objAttachmentUpdated, new S ((string) time ()))
                ->setExtraUpdateData (static::$objAttachmentAId, $_GET[_T ('Id')])
                ->setUploadDirectory (Architecture::pathTo ($this->getObjectAncestry ()->toLower (),
                _I (time ())->toDateString ('Y/m/d')))
                ->setName ($objF)
                ->setRedirect ($objURLToGoBack)
                ->setInputType (new S ('text'))
                ->setName (static::$objAttachmentTags)
                ->setLabel (_T ('Tags'))
                ->setCheckForEmpty (new B (TRUE))
                ->setInputInfo ($this->getHELP ($objF))
                ->setInputType (new S ('text'))
                ->setName (static::$objAttachmentTitle)
                ->setLabel (_T ('Title'))
                ->setCheckForEmpty (new B (TRUE))
                ->setCheckForUnique (new B (TRUE))
                ->setInputType (new S ('file'))
                ->setName (static::$objAttachmentURL)
                ->setLabel (_T ('Attachment'))
                ->setInputType (new S ('textarea'))
                ->setName (static::$objAttachmentCaption)
                ->setLabel (_T ('Caption'))
                ->setCheckForEmpty (new B (TRUE))
                ->setInputType (new S ('submit'))
                ->setValue (_T ('Add'))
                ->setAccessKey (new S ('S'))
                ->setFormEndAndExecute (new B (TRUE));
                break;

            case 'attachmentEdit':
                // Set the URL to go back too;
                $objURLToGoBack = Location::rewriteTo (new A (Array (_T ('Do Attachments'), _T ('Attachment Id'))));

                // Set
                $objFp =  new StoragePath (Architecture
				::pathTo (UPLOAD_DIR, $this->getObjectAncestry ()->toLower (),
				_I ((int) (string) $this->getAttachmentById ($_GET[_T ('Attachment Id')],
				static::$objAttachmentPublished))->toDateString ('Y/m/d'),
				$this->getAttachmentById ($_GET[_T ('Attachment Id')],
				static::$objAttachmentURL)));

                // Form
                $this->setFieldset (_T ('Edit attachment for: ')->appendString ($this
                ->getItemById ($_GET[_T ('Id')], static::$objItemTitle)))
                ->setTableName (static::$objAttachment)
                ->setUpdateId ($_GET[_T ('Attachment Id')])
                ->setUpdateField (static::$objAttachmentId)
                ->setName ($objF)
                ->setRedirect ($objURLToGoBack)
                ->setExtraUpdateData (static::$objAttachmentPublished, new S ((string) time ()))
                ->setExtraUpdateData (static::$objAttachmentUpdated, new S ((string) time ()))
                ->setUploadDirectory (Architecture::pathTo ($this->getObjectAncestry ()->toLower (),
                _I (time ())->toDateString ('Y/m/d')))
                ->setInputType (new S ('text'))
                ->setName (new S ('fsize'))
                ->setReadOnly (new B (TRUE))
                ->setLabel (new S ('Size'))
                ->setValue (_S ((string) $objFp->getPathInfo ("fsize"))->appendString (_SP)->appendString (_T ("bytes")))
                ->setInputType (new S ('text'))
                ->setName (new S ('rpath'))
                ->setReadOnly (new B (TRUE))
                ->setLabel (new S ('Path'))
                ->setValue ($objFp->getPathInfo ("rpath"))
                ->setInputType (new S ('text'))
                ->setName (new S ('md5'))
                ->setReadOnly (new B (TRUE))
                ->setLabel (new S ('(md5) Hash'))
                ->setValue ($objFp->getPathInfo ("rpath")->toMD5File ())
                ->setInputType (new S ('text'))
                ->setName (static::$objAttachmentTags)
                ->setLabel (_T ('Tags'))
                ->setCheckForEmpty (new B (TRUE))
                ->setInputInfo ($this->getHELP ($objF))
                ->setInputType (new S ('text'))
                ->setName (static::$objAttachmentTitle)
                ->setLabel (_T ('Title'))
                ->setCheckForEmpty (new B (TRUE))
                ->setCheckForUnique (new B (TRUE))
                ->setInputType (new S ('file'))
                ->setName (static::$objAttachmentURL)
                ->setLabel (_T ('Attachment'))
                ->setFileController (new B (TRUE))
                ->setInputType (new S ('textarea'))
                ->setName (static::$objAttachmentCaption)
                ->setLabel (_T ('Caption'))
                ->setCheckForEmpty (new B (TRUE))
                ->setInputType (new S ('submit'))
                ->setValue (_T ('Edit'))
                ->setAccessKey (new S ('S'))
                ->setFormEndAndExecute (new B (TRUE));
                break;

            case 'attachmentErase':
                // Set the URL to back too;
                $objURLToGoBack = Location::rewriteTo (new A (Array (_T ('Do Attachments'), _T ('Attachment Id'))));

                // Set
                $objV = new A;

                // Set
                $objV[static::$objAttachmentPublished] = $this
                ->getAttachmentById ($_GET[_T ('Attachment Id')],
                static::$objAttachmentPublished);

                // Set
                $objV[static::$objAttachmentURL] = $this
                ->getAttachmentById ($_GET[_T ('Attachment Id')],
                static::$objAttachmentURL);

                // Directory
                _SP (Architecture::pathTo (UPLOAD_DIR, $this->getObjectAncestry ()->toLower (),
                _I ((int) (string) $objV[static::$objAttachmentPublished])->toDateString ('Y/m/d') .
                _S . $objV[static::$objAttachmentURL]), FALSE)->unLinkPath ();

                // Erase
                $this->_Q (_QS ('doDELETE')
                ->doToken ('%table', static::$objAttachment)
                ->doToken ('%condition', new S ('%objAttachmentId = "%Id"'))
                ->doToken ('%Id', $_GET[_T ('Attachment Id')]));

                // Do a redirect, and get the user back where he belongs;
                Header::setKey ($objURLToGoBack, new S ('Location'));
                break;

            case 'audioCreate':
                // Set the URL to go back too;
                $objURLToGoBack = Location::rewriteTo (new A (Array (_T ('Do Audios'))));

                // Form
                $this->setFieldset (_T ('Add audio for: ')->appendString ($this
                ->getItemById ($_GET[_T ('Id')], static::$objItemTitle)))
                ->setTableName (static::$objAudio)
                ->setUpdateField (static::$objAudioId)
                ->setExtraUpdateData (static::$objAudioPublished, new S ((string) time ()))
                ->setExtraUpdateData (static::$objAudioUpdated, new S ((string) time ()))
                ->setExtraUpdateData (static::$objAudioAId, $_GET[_T ('Id')])
                ->setUploadDirectory (Architecture::pathTo ($this->getObjectAncestry ()->toLower (),
                _I (time ())->toDateString ('Y/m/d')))
                ->setUploadType (self::$objAcceptedAudioMimeTypes
                ->fromArrayToString ('|'))
                ->setName ($objF)
                ->setRedirect ($objURLToGoBack)
                ->setInputType (new S ('text'))
                ->setName (static::$objAudioTags)
                ->setLabel (_T ('Tags'))
                ->setCheckForEmpty (new B (TRUE))
                ->setInputInfo ($this->getHELP ($objF))
                ->setInputType (new S ('text'))
                ->setName (static::$objAudioTitle)
                ->setLabel (_T ('Title'))
                ->setCheckForEmpty (new B (TRUE))
                ->setCheckForUnique (new B (TRUE))
                ->setInputType (new S ('file'))
                ->setName (static::$objAudioURL)
                ->setLabel (_T ('Audio'))
                ->setInputType (new S ('textarea'))
                ->setName (static::$objAudioCaption)
                ->setLabel (_T ('Caption'))
                ->setCheckForEmpty (new B (TRUE))
                ->setInputType (new S ('submit'))
                ->setValue (_T ('Add'))
                ->setAccessKey (new S ('S'))
                ->setFormEndAndExecute (new B (TRUE));
                break;

            case 'audioEdit':
                // Set the URL to go back too;
                $objURLToGoBack = Location::rewriteTo (new A (Array (_T ('Do Audios'), _T ('Audio Id'))));

                // Form
                $this->setFieldset (_T ('Edit audio for: ')->appendString ($this
                ->getItemById ($_GET[_T ('Id')], static::$objItemTitle)))
                ->setTableName (static::$objAudio)
                ->setUpdateId ($_GET[_T ('Audio Id')])
                ->setUpdateField (static::$objAudioId)
                ->setName ($objF)
                ->setRedirect ($objURLToGoBack)
                ->setExtraUpdateData (static::$objAudioPublished, new S ((string) time ()))
                ->setExtraUpdateData (static::$objAudioUpdated, new S ((string) time ()))
                ->setUploadDirectory (Architecture::pathTo ($this->getObjectAncestry ()->toLower (),
                _I (time ())->toDateString ('Y/m/d')))
                ->setUploadType (self::$objAcceptedAudioMimeTypes
                ->fromArrayToString ('|'))
                ->setInputType (new S ('text'))
                ->setName (static::$objAudioTags)
                ->setLabel (_T ('Tags'))
                ->setCheckForEmpty (new B (TRUE))
                ->setInputInfo ($this->getHELP ($objF))
                ->setInputType (new S ('text'))
                ->setName (static::$objAudioTitle)
                ->setLabel (_T ('Title'))
                ->setCheckForEmpty (new B (TRUE))
                ->setCheckForUnique (new B (TRUE))
                ->setInputType (new S ('file'))
                ->setName (static::$objAudioURL)
                ->setLabel (_T ('Audio'))
                ->setFileController (new B (TRUE))
                ->setInputType (new S ('textarea'))
                ->setName (static::$objAudioCaption)
                ->setLabel (_T ('Caption'))
                ->setCheckForEmpty (new B (TRUE))
                ->setInputType (new S ('submit'))
                ->setValue (_T ('Edit'))
                ->setAccessKey (new S ('S'))
                ->setFormEndAndExecute (new B (TRUE));
                break;

            case 'audioErase':
                // Set the URL to back too;
                $objURLToGoBack = Location::rewriteTo (new A (Array (_T ('Do Audios'), _T ('Audio Id'))));

                // Set
                $objV = new A;

                // Set
                $objV[static::$objAudioPublished] = $this
                ->getAudioById ($_GET[_T ('Audio Id')],
                static::$objAudioPublished);

                // Set
                $objV[static::$objAudioURL] = $this
                ->getAudioById ($_GET[_T ('Audio Id')],
                static::$objAudioURL);

                // Directory
                _SP (Architecture::pathTo (UPLOAD_DIR, $this->getObjectAncestry ()->toLower (),
                _I ((int) (string) $objV[static::$objAudioPublished])->toDateString ('Y/m/d') .
                _S . $objV[static::$objAudioURL]))->unLinkPath ();

                // Erase
                $this->_Q (_QS ('doDELETE')
                ->doToken ('%table', static::$objAudio)
                ->doToken ('%condition', new S ('%objAudioId = "%Id"'))
                ->doToken ('%Id', $_GET[_T ('Audio Id')]));

                // Do a redirect, and get the user back where he belongs;
                Header::setKey ($objURLToGoBack, new S ('Location'));
                break;

            case 'propertyCreate':
                // Set the URL to go back;
                $objURLToGoBack = Location::rewriteTo (new A (Array (_T ('Do Properties'))));

                // Form
                $this->setFieldset (_T ('Add property for: ')->appendString ($this
                ->getItemById ($_GET[_T ('Id')], static::$objItemTitle)))
                ->setTableName (static::$objProperty)
                ->setUpdateField (static::$objPropertyId)
                ->setExtraUpdateData (static::$objPropertyAId, $_GET[_T ('Id')])
                ->setName ($objF)
                ->setRedirect ($objURLToGoBack)
                ->setInputType (new S ('hidden'))
                ->setName (static::$objPropertyPublished)
                ->setInputType (new S ('text'))
                ->setName (new S ('HiddenDate_AutoUpdate'))
                ->setLabel (new S ('Published'))
                ->setReadOnly (new B (TRUE))
                ->setInputType (new S ('select'))
                ->setName (static::$objPropertyKey)
                ->setInputInfo ($this->getHELP ($objF))
                ->setLabel (_T ('Key'));

                // Foreach
                foreach ($this->getReflection ()->getConstants () as $objK => $objV) {
                    // Check
                    if (_S ($objK)->findPos ('PROPERTY_ITEM_') instanceof I) {
                        // Set
                        $this->setInputType (new S ('option'))
                        ->setName  (new S ($objV))
                        ->setLabel (new S ($objV))
                        ->setValue (new S ($objV));
                    }
                }

                // Continue
                $this->setInputType (new S ('textarea'))
                ->setName (static::$objPropertyVar)
                ->setLabel (_T ('Value'))
                ->setCheckForEmpty (new B (TRUE))
                ->setInputType (new S ('submit'))
                ->setValue (_T ('Add'))
                ->setAccessKey (new S ('S'))
                ->setFormEndAndExecute (new B (TRUE));
                break;

            case 'propertyEdit':
                // Set the URL to go back;
                $objURLToGoBack = Location::rewriteTo (new A (Array (_T ('Do Properties'), _T ('Property Id'))));

                // Form
                $this->setFieldset (_T ('Edit property for: ')->appendString ($this
                ->getItemById ($_GET[_T ('Id')], static::$objItemTitle)))
                ->setTableName (static::$objProperty)
                ->setUpdateId ($_GET[_T ('Property Id')])
                ->setUpdateField (static::$objPropertyId)
                ->setName ($objF)
                ->setRedirect ($objURLToGoBack)
                ->setInputType (new S ('hidden'))
                ->setName (static::$objPropertyPublished)
                ->setInputType (new S ('text'))
                ->setName (new S ('HiddenDate_AutoUpdate'))
                ->setLabel (new S ('Published'))
                ->setInputType (new S ('text'))
                ->setName (static::$objPropertyKey)
                ->setInputInfo ($this->getHELP ($objF))
                ->setLabel (_T ('Key'))
                ->setReadOnly (new B (TRUE))
                ->setInputType (new S ('textarea'))
                ->setName (static::$objPropertyVar)
                ->setLabel (_T ('Value'))
                ->setCheckForEmpty (new B (TRUE))
                ->setInputType (new S ('submit'))
                ->setValue (_T ('Edit'))
                ->setAccessKey (new S ('S'))
                ->setFormEndAndExecute (new B (TRUE));
                break;

            case 'propertyErase':
                // Set the URL to go back too;
                $objURLToGoBack = Location::rewriteTo (new A (Array (_T ('Do Properties'), _T ('Property Id'))));

                // Erase
                $this->_Q (_QS ('doDELETE')
                ->doToken ('%table', static::$objProperty)
                ->doToken ('%condition', new S ('%objPropertyId = "%Id"'))
                ->doToken ('%Id', $_GET[_T ('Property Id')]));

                // Do a redirect, and get the user back where he belongs;
                Header::setKey ($objURLToGoBack, new S ('Location'));
                break;

            case 'categoryCreate':
                // The URL to go back too;
                $objURLToGoBack = Location::rewriteTo (new A (Array (_T ('Do'))));

                // Do some work
                if ($this->checkPOST (new S ('categories_show_all'))->toBoolean () == TRUE) {
                    // Redirect to proper
                    Header::setKey (Location::rewriteTo (new A (Array (_T ('Show ALL'))),
                    new A (Array ('1'))), new S ('Location'));
                }

                if ($this->checkPOST (new S ('add_category_submit'))->toBoolean () == TRUE) {
                    // Requirements;
                    $objFHappened = new B (FALSE);
                    $objToCheck = $this->getPOST (new S ('add_category'));

                    if ($objToCheck->toLength ()->toInt () == 0) {
                        $this->setErrorOnInput (new S ('add_category'),
                        _T ('Category name cannot be empty!'));

                        // Set the memory;
                        $objFHappened->switchType ();
                    } else {
                        if (static::getHierarchy ()->mpttCheckIfNodeExists ($objToCheck = Hierarchy::mpttAddUnique ($objToCheck,
                        new S ((string) time ())))->toBoolean () == TRUE) {
                            // Check to see if the category exists!
                            $this->setErrorOnInput (new S ('add_category'),
                            _T ('Category name must be unique!'));

                            // Set the memory;
                            $objFHappened->switchType ();
                        }

                        if ($this->checkCategoryURLIsUnique (Location::getFrom ($objToCheck))
                        ->toBoolean () == FALSE) {
                            $this->setErrorOnInput (new S ('add_category'),
                            _T ('Category auto-generated URL must be unique!'));
                        }
                    }

                    if ($objFHappened->toBoolean () == FALSE) {
                        // Remember if we should add it as a brother or child;
                        $objAddNodeAS = NULL;

                        // Switch
                        switch ($this->getPOST (new S ('add_category_as_what'))) {
                            case _T ('first child'):
                                $objAddNodeAS = new S ((string)
                                Hierarchy::FIRST_CHILD);
                                break;

                            case _T ('last child'):
                                $objAddNodeAS = new S ((string)
                                Hierarchy::LAST_CHILD);
                                break;

                            case _T ('previous brother'):
                                $objAddNodeAS = new S ((string)
                                Hierarchy::PREVIOUS_BROTHER);
                                break;

                            case _T ('next brother'):
                                $objAddNodeAS = new S ((string)
                                Hierarchy::NEXT_BROTHER);
                                break;
                        }

                        // Add the node;
                        static::getHierarchy ()->mpttAddNode ($objToCheck,
                        $this->getPOST (new S ('add_category_parent_or_bro')), $objAddNodeAS);
                    }
                }

                // Form
                $this->setFieldset (_T ('Add'))
                ->setName ($objF);
                if ($this->checkPOST (new S ('add_category_submit'))->toBoolean () == TRUE)
                $this->setRedirect ($objURLToGoBack);
                $this->setInputType (new S ('submit'))
                ->setName (new S ('categories_show_all'))
                ->setValue (_T ('Show ALL categories'))
                ->setInputInfo ($this->getHELP ($objF))
                ->setInputType (new S ('text'))
                ->setName (new S ('add_category'))
                ->setLabel (_T ('Category'))
                ->setInputType (new S ('select'))
                ->setName (new S ('add_category_as_what'))
                ->setLabel (_T ('As a'))
                ->setInputType (new S ('option'))
                ->setName (new S ('as_first_child'))
                ->setLabel (_T ('first child'))
                ->setInputType (new S ('option'))
                ->setName (new S ('as_last_child'))
                ->setLabel (_T ('last child'))
                ->setInputType (new S ('option'))
                ->setName (new S ('as_previous_brother'))
                ->setLabel (_T ('previous brother'))
                ->setInputType (new S ('option'))
                ->setName (new S ('as_next_brother'))
                ->setLabel (_T ('next brother'))
                ->setInputType (new S ('select'))
                ->setName (new S ('add_category_parent_or_bro'))
                ->setLabel (_T ('Of category'));

                // Category parent or brother of this one
                foreach ($this->getCategories () as $objK => $objV) {
                    $this->setInputType (new S ('option'))
                    ->setName ($objV[static::getHierarchy ()->objNameOfNode])
                    ->setValue ($objV[static::getHierarchy ()->objNameOfNode])
                    ->setLabel (new S (str_repeat ('--' . _SP,
                    (int) (string) $objV['depth']) .
                    Hierarchy::mpttRemoveUnique (CLONE $objV[static::getHierarchy ()
                    ->objNameOfNode])));
                }

                // Continue
                $this->setInputType (new S ('submit'))
                ->setName (new S ('add_category_submit'))
                ->setValue (_T ('Add'))
                ->setAccessKey (new S ('S'))
                ->setFormEndAndExecute (new B (TRUE));
                break;

            case 'categoryEdit':
                // The URL to go back too;
                $objURLToGoBack = Location::rewriteTo (new A (Array (_T ('Do'), _T ('Id'))));

                // Check
                if ($this->checkPOST ()->toBoolean () == TRUE) {
                    // Requirements;
                    $objFHappened = new B (FALSE);
                    $objToCheck = static::getHierarchy ()->mpttAddUnique ($this->getPOST (static::$objCategoryName),
                    $this->getCategoryById ($_GET[_T ('Id')], static::$objCategoryDate));

                    if ($objToCheck->toLength ()->toInt () == 0) {
                        // Check
                        $this->setErrorOnInput (static::$objCategoryName,
                        _T ('Category name cannot be empty!'));

                        // Set the memory;
                        $objFHappened->switchType ();
                    } else if ($this->getCategoryById ($_GET[_T ('Id')],
                    static::$objCategoryName) != $objToCheck) {
                        if (static::getHierarchy ()->mpttCheckIfNodeExists ($objToCheck)
                        ->toBoolean () == TRUE) {
                            // Check to see if the group exists;
                            $this->setErrorOnInput (static::$objCategoryName,
                            new S (_T ('Category name must be unique!')));

                            // Set the memory;
                            $objFHappened->switchType ();
                        }
                    }
                } else {
                    // Nada
                    $objFHappened = new B (FALSE);
                }

                // Form
                $this->setTableName (static::$objCategory)
                ->setUpdateId ($_GET[_T ('Id')])
                ->setUpdateField (static::$objCategoryId)
                ->setFieldset (_T ('Edit: ')->appendString (Hierarchy::mpttRemoveUnique ($this
                ->getCategoryById ($_GET[_T ('Id')], static::$objCategoryName))))
                ->setName ($objF);
                if ($this->checkPOST (new S ('edit_category_submit'))->toBoolean () == TRUE &&
                $objFHappened->toBoolean () == FALSE) {
                    // Set the URL
                    $this->setExtraUpdateData (static::$objCategoryURL,
                    Location::getFrom ($this->getPOST (static::$objCategoryName)))
                    ->setRedirect ($objURLToGoBack);
                }

                // Continue
                $this->setInputType (new S ('text'))
                ->setName (static::$objCategoryName)
                ->setLabel (_T ('Category'))
                ->setMPTTRemoveUnique (new B (TRUE))
                ->setInputInfo ($this->getHELP ($objF))
                ->setInputType (new S ('submit'))
                ->setName (new S ('edit_category_submit'))
                ->setValue (_T ('Edit'))
                ->setAccessKey (new S ('S'))
                ->setFormEndAndExecute (new B (TRUE));
                break;

            case 'categoryErase':
                // The URL to go back too;
                $objURLToGoBack = Location::rewriteTo (new A (Array (_T ('Do'), _T ('Id'))));

                // Erase
                static::getHierarchy ()->mpttRemoveNode ($this
                ->getCategoryById ($_GET[_T ('Id')],
                static::$objCategoryName));

                // Do a redirect, and get the user back where he belongs;
                Header::setKey ($objURLToGoBack, new S ('Location'));

                // BK;
                break;

            case 'propertyCategoryCreate':
                // Set the URL to go back;
                $objURLToGoBack = Location::rewriteTo (new A (Array (_T ('Do Properties'))));

                // Form
                $this->setFieldset (_T ('Add property for category: ')->appendString (Hierarchy::mpttRemoveUnique ($this
                ->getCategoryById ($_GET[_T ('Id')], static::$objCategoryName))))
                ->setTableName (static::$objCategoryProperty)
                ->setUpdateField (static::$objCategoryPropertyId)
                ->setExtraUpdateData (static::$objCategoryPropertyCId, $_GET[_T ('Id')])
                ->setName ($objF)
                ->setRedirect ($objURLToGoBack)
                ->setInputType (new S ('hidden'))
                ->setName (static::$objCategoryPropertyPublished)
                ->setInputType (new S ('text'))
                ->setName (new S ('HiddenDate_AutoUpdate'))
                ->setLabel (new S ('Published'))
                ->setReadOnly (new B (TRUE))
                ->setInputType (new S ('select'))
                ->setName (static::$objCategoryPropertyKey)
                ->setInputInfo ($this->getHELP ($objF))
                ->setLabel (_T ('Key'));

                // Foreach
                foreach ($this->getReflection ()->getConstants () as $objK => $objV) {
                    // Check
                    if (_S ($objK)->findPos ('PROPERTY_CATEGORY_') instanceof I) {
                        // Set
                        $this->setInputType (new S ('option'))
                        ->setName  (new S ($objV))
                        ->setLabel (new S ($objV))
                        ->setValue (new S ($objV));
                    }
                }

                // Continue
                $this->setInputType (new S ('textarea'))
                ->setName (static::$objCategoryPropertyVar)
                ->setLabel (_T ('Value'))
                ->setCheckForEmpty (new B (TRUE))
                ->setInputType (new S ('submit'))
                ->setValue (_T ('Add'))
                ->setAccessKey (new S ('S'))
                ->setFormEndAndExecute (new B (TRUE));
                break;

            case 'propertyCategoryEdit':
                // Set the URL to go back;
                $objURLToGoBack = Location::rewriteTo (new A (Array (_T ('Do Properties'), _T ('Property Id'))));

                // Form
                $this->setFieldset (_T ('Edit property for category: ')->appendString (Hierarchy::mpttRemoveUnique ($this
                ->getCategoryById ($_GET[_T ('Id')], static::$objCategoryName))))
                ->setTableName (static::$objCategoryProperty)
                ->setUpdateId ($_GET[_T ('Property Id')])
                ->setUpdateField (static::$objCategoryPropertyId)
                ->setName ($objF)
                ->setRedirect ($objURLToGoBack)
                ->setInputType (new S ('hidden'))
                ->setName (static::$objCategoryPropertyPublished)
                ->setInputType (new S ('text'))
                ->setName (new S ('HiddenDate_AutoUpdate'))
                ->setLabel (new S ('Published'))
                ->setReadOnly (new B (TRUE))
                ->setInputType (new S ('select'))
                ->setName (static::$objCategoryPropertyKey)
                ->setInputInfo ($this->getHELP ($objF))
                ->setLabel (_T ('Key'));

                // Foreach
                foreach ($this->getReflection ()->getConstants () as $objK => $objV) {
                    // Check
                    if (_S ($objK)->findPos ('PROPERTY_CATEGORY_') instanceof I) {
                        // Set
                        $this->setInputType (new S ('option'))
                        ->setName  (new S ($objV))
                        ->setLabel (new S ($objV))
                        ->setValue (new S ($objV));
                    }
                }

                // Continue
                $this->setInputType (new S ('textarea'))
                ->setName (static::$objCategoryPropertyVar)
                ->setLabel (_T ('Value'))
                ->setCheckForEmpty (new B (TRUE))
                ->setInputType (new S ('submit'))
                ->setValue (_T ('Edit'))
                ->setAccessKey (new S ('S'))
                ->setFormEndAndExecute (new B (TRUE));
                break;

            case 'propertyCategoryErase':
                // Set the URL to go back too;
                $objURLToGoBack = Location::rewriteTo (new A (Array (_T ('Do Properties'), _T ('Property Id'))));

                // Erase
                $this->_Q (_QS ('doDELETE')
                ->doToken ('%table', static::$objCategoryProperty)
                ->doToken ('%condition', new S ('%objCategoryPropertyId = "%Id"'))
                ->doToken ('%Id', $_GET[_T ('Property Id')]));

                // Do a redirect, and get the user back where he belongs;
                Header::setKey ($objURLToGoBack, new S ('Location'));
                break;

            case 'categoryMove':
                // Set some predefines;
                $objURLToGoBack = Location::rewriteTo (new A (Array (_T ('Do'), _T ('Id'), _T ('To'), _T ('Type'))));

                // Get names, as they are unique;
                $objThatIsMoved = $this->getCategoryById ($_GET[_T ('Id')], static::$objCategoryName);
                $objWhereToMove = $this->getCategoryById ($_GET[_T ('To')], static::$objCategoryName);

                // Get node subtree
                $objMovedNodeSubTree = static::getHierarchy ()->mpttGetTree ($objThatIsMoved);

                // Memory
                $objIsChild = new B (FALSE);
                foreach ($objMovedNodeSubTree as $objK => $objV) {
                    if ($objV[static::getHierarchy ()->objNameOfNode] == $objWhereToMove) {
                        $objIsChild->switchType ();
                    }
                }

                // Check if it's a child or not;
                if ($objIsChild->toBoolean () == TRUE) {
                    // Set an error message;
                    static::getAdministration ()->setErrorMessage (_T
                    ('Cannot move parent category to a child of it!'),
                    $objURLToGoBack);
                } else {
                    // Move nodes;
                    static::getHierarchy ()->mpttMoveNode ($objThatIsMoved,
                    $objWhereToMove, $_GET[_T ('Type')]);
                    Header::setKey ($objURLToGoBack, new S ('Location'));
                }
                // BK;
                break;

            case 'categoryMoveOperation':
                // The URL to go back too;
                $objURLToGoBack = Location::rewriteTo (new A (Array (_T ('S'))), new A (Array (_T ('Articles'))));

                // Work
                ($this->checkPOST ()->toBoolean () == TRUE) ?
                ($objOLDCategoryId = $this->getPOST (new S ('old_category_id'))) :
                ($objOLDCategoryId = new S ('0'));

                // Form
                $this->setFieldset (_T ('Move'))
                ->setTableName (static::$objItem)
                ->setUpdateField (static::$objItemId)

                // Specific code here, need abstractization!
                ->setUpdateWhere ($this->doChangeToken (_S ('%objItemCategoryId = "%Id"')
                ->doToken ('%Id', $objOLDCategoryId)))
                ->setName ($objF)
                ->setRedirect ($objURLToGoBack)
                ->setInputType (new S ('select'))
                ->setName (new S ('old_category_id'))
                ->setInputInfo ($this->getHELP ($objF))
                ->setLabel (_T ('Old'));

                // Categories
                foreach ($this->getCategories () as $objK => $objV) {
                    $this->setInputType (new S ('option'))
                    ->setName ($objV[static::getHierarchy ()->objIdField])
                    ->setValue ($objV[static::getHierarchy ()->objIdField])
                    ->setLabel (new S (str_repeat ('--' . _SP,
                    (int) (string) $objV['depth']) .
                    Hierarchy::mpttRemoveUnique ($objV[static::getHierarchy ()
                    ->objNameOfNode])));
                }

                // Categories
                $this->setInputType (new S ('select'))
                ->setName (static::$objItemCategoryId)
                ->setLabel (_T ('New'));

                // Foreach
                foreach ($this->getCategories () as $objK => $objV) {
                    $this->setInputType (new S ('option'))
                    ->setName ($objV[static::getHierarchy ()->objIdField])
                    ->setValue ($objV[static::getHierarchy ()->objIdField])
                    ->setLabel (new S (str_repeat ('--' . _SP,
                    (int) (string) $objV['depth']) .
                    Hierarchy::mpttRemoveUnique ($objV[static::getHierarchy ()
                    ->objNameOfNode])));
                }

                // Continue
                $this->setInputType (new S ('submit'))
                ->setValue (_T ('Move'))
                ->setFormEndAndExecute (new B (TRUE));
                break;

            case 'commentCreate':
                // The URL to go back too;
                $objURLToGoBack = Location::rewriteTo (new A (Array (_T ('Do Comments'))));

                // Form
                $this->setFieldset (_T ('Add comment for: ')->appendString ($this
                ->getItemById ($_GET[_T ('Id')], static::$objItemTitle)))
                ->setRedirect ($objURLToGoBack)
                ->setTableName (static::$objComment)
                ->setUpdateField (static::$objCommentId)
                ->setExtraUpdateData (static::$objCommentAId, $_GET[_T ('Id')])
                ->setExtraUpdateData (static::$objCommentUpdated, new S ((string) time ()))
                ->setExtraUpdateData (static::$objCommentRUId, $this->getAuthentication ()
                ->getCurrentUser (Authentication::$objUserId))
                ->setExtraUpdateData (static::$objCommentName, $this->getAuthentication ()
                ->getCurrentUser (Authentication::$objUserUName))
                ->setName ($objF)
                ->setInputType (new S ('hidden'))
                ->setName (static::$objCommentPublished)
                ->setInputType (new S ('text'))
                ->setName (new S ('HiddenDate_AutoUpdate'))
                ->setLabel (new S ('Published'))
                ->setReadOnly (new B (TRUE))
                ->setInputType (new S ('select'))
                ->setName (static::$objCommentApproved)
                ->setInputInfo ($this->getHELP ($objF))
                ->setLabel (_T ('Approved'))
                ->setYesNoOptions (new B (TRUE))
                ->setInputType (new S ('textarea'))
                ->setName (static::$objCommentComment)
                ->setLabel (_T ('Comment'))
                ->setTinyMCETextarea (new B (TRUE))
                ->setInputType (new S ('submit'))
                ->setValue (_T ('Add'))
                ->setAccessKey (new S ('S'))
                ->setFormEndAndExecute (new B (TRUE));
                break;

            case 'commentEdit':
                // The URL to go back too;
                $objURLToGoBack = Location::rewriteTo (new A (Array (_T ('Do Comments'), _T ('Comment Id'))));

                // Form
                $this->setFieldset (_T ('Edit comment for: ')->appendString ($this
                ->getItemById ($_GET[_T ('Id')], static::$objItemTitle)))
                ->setRedirect ($objURLToGoBack)
                ->setTableName (static::$objComment)
                ->setUpdateId ($_GET[_T ('Comment Id')])
                ->setUpdateField (static::$objCommentId)
                ->setExtraUpdateData (static::$objCommentUpdated, new S ((string) time ()))
                ->setName ($objF)
                ->setInputType (new S ('hidden'))
                ->setName (static::$objCommentPublished)
                ->setInputType (new S ('text'))
                ->setName (new S ('HiddenDate_AutoUpdate'))
                ->setLabel (new S ('Published'))
                ->setReadOnly (new B (TRUE))
                ->setInputType (new S ('select'))
                ->setName (static::$objCommentApproved)
                ->setInputInfo ($this->getHELP ($objF))
                ->setLabel (_T ('Approved'))
                ->setYesNoOptions (new B (TRUE))
                ->setInputType (new S ('textarea'))
                ->setName (static::$objCommentComment)
                ->setLabel (_T ('Comment'))
                ->setTinyMCETextarea (new B (TRUE))
                ->setInputType (new S ('submit'))
                ->setValue (_T ('Edit'))
                ->setAccessKey (new S ('S'))
                ->setFormEndAndExecute (new B (TRUE));
                break;

            case 'commentErase':
                // The URL to go back too;
                $objURLToGoBack = Location::rewriteTo (new A (Array (_T ('Do Comments'), _T ('Comment Id'))));

                // Erase
                $this->_Q (_QS ('doDELETE')
                ->doToken ('%table', static::$objComment)
                ->doToken ('%condition', new S ('%objCommentId = "%Id"'))
                ->doToken ('%Id', $_GET[_T ('Comment Id')]));

                // Do a redirect, and get the user back where he belongs;
                Header::setKey ($objURLToGoBack, new S ('Location'));
                break;
        }
    }

    /**
     * Renders a requested widget;
     *
     * @author Catalin Alexandru Zamfir <catalin.zamfir@raphpframework.ro>
     * @copyright Under the terms of the GNU General Public License v3
     * @version $Id: Commons.php 23 2012-10-26 21:06:06Z root $
     */
    protected function renderWidget (S $objW, A $objWA = NULL) {
        // Requirements
        self::manageCSS (new
        Path (Architecture::pathTo ($this
        ->getPathToSkinCSS (), __FUNCTION__,
        $objW . CSS_EXTENSION)));

        self::manageJSS (new
        Path (Architecture::pathTo ($this
        ->getPathToSkinJSS (), __FUNCTION__,
        $objW . JSS_EXTENSION)));

        // Set
        $this->objWidget = $this
        ->getObjectAncestry () .
        _U . $objW;

        // Set
        $this->objParent = $this
        ->getObjectAncestry ();

        // XML & RSS
        switch ($objW) {
            case 'widgetXML':
                // Yo man woohoooooo
                foreach ($this->getItems (new S ('ORDER
                BY %objItemPublished DESC')) as $objK => $objV) {
                    // Set the XML Sitemap kids
                    $objURL->addCHILD (Settings::XML_CHANGE_FREQ, self::XML_SITEMAP_FREQUENCY);
                    $objURL->addCHILD (Settings::XML_PRIORITY, self::XML_SITEMAP_PRIORITY);
                    $objURL->addCHILD (Settings::XML_LOCATION, $objLOC);
                    $objURL->addCHILD (Settings::XML_LAST_MOD, $objDTE);
                }
                // BK;
                break;

            case 'widgetRSS':
                // Get'em 30
                foreach ($this->getItems (new S ('ORDER
                BY %objItemPublished DESC LIMIT 0, 30')) as $objK => $objV) {
                    // Set the RSS kids
                    $objURL->addCHILD (Settings::RSS_TITLE, $objV[self::$objItemTitle]);
                    $objURL->addCHILD (Settings::RSS_PUBLISHED_DATE, $objDTE);
                    $objURL->addCHILD (Settings::RSS_DESCRIPTION, $objDSC);
                    $objURL->addCHILD (Settings::RSS_LINK, $objLOC);
                    $objURL->addCHILD (Settings::RSS_GUID, $objLOC);
                }
                // BK;
                break;

            case 'widgetItem':
            	/**
            	 * To be also expanded for video/audio, when needed;
            	 */

            	// Check
            	if ($_GET
            	->offsetExists (_T ('Output'))) {
            		// Determine
            		$objFp =  new StoragePath (Architecture
            		::pathTo (UPLOAD_DIR, $this->getObjectAncestry ()->toLower (),
            		_I ((int) (string) $this->getAttachmentById ($_GET[_T ('Output')],
            		static::$objAttachmentPublished))->toDateString ('Y/m/d'),
            		$this->getAttachmentById ($_GET[_T ('Output')],
            		static::$objAttachmentURL)));

            		// Header
            		Header::setKey (new S ($objFp
            		->getMimeType ()), new S
            		('Content-Type'));

            		// Disposition
            		Header::setKey (_S ('attachment; filename="%fId"')->doToken ('%fId',
            		$objFp->getPathInfo ('fname') . _DOT . $objFp->getPathInfo ('extension')),
            		new S ('Content-Disposition'));

            		// Check
            		self::outputBinary ($objFp);
            	}
            	// BK
            	break;
        }

        // Switch
        switch ($objW) {
            // Widgets
        }
    }

    /**
     * Sets a given object key (dynamic);
     *
     * @author Catalin Alexandru Zamfir <catalin.zamfir@raphpframework.ro>
     * @copyright Under the terms of the GNU General Public License v3
     * @version $Id: Commons.php 23 2012-10-26 21:06:06Z root $
     */
    public function __SET ($objKey, $objVar) {
        // Check
        if ($this->objContainer == NULL)
        $this->objContainer = new A;

        // Set & Return
        $this->objContainer[$objKey] = $objVar;
        return new B (TRUE);
    }

    /**
     * Gets a given object key (dynamic);
     *
     * @author Catalin Alexandru Zamfir <catalin.zamfir@raphpframework.ro>
     * @copyright Under the terms of the GNU General Public License v3
     * @version $Id: Commons.php 23 2012-10-26 21:06:06Z root $
     */
    public function __GET ($objKey) {
        // Check
        if (isset ($this
        ->objContainer[$objKey])) {
            // Return
            return $this
            ->objContainer[$objKey];
        } else {
            // Throws
            throw new OffsetKeyNotSetException;
        }
    }

    /**
     * Checks a given object key (dynamic);
     *
     * @author Catalin Alexandru Zamfir <catalin.zamfir@raphpframework.ro>
     * @copyright Under the terms of the GNU General Public License v3
     * @version $Id: Commons.php 23 2012-10-26 21:06:06Z root $
     */
    public function __ISSET ($objKey) {
        // Return
        return isset ($this
        ->objContainer[$objKey]);
    }

    /**
     * Unsets a given object key (dynamic);
     *
     * @author Catalin Alexandru Zamfir <catalin.zamfir@raphpframework.ro>
     * @copyright Under the terms of the GNU General Public License v3
     * @version $Id: Commons.php 23 2012-10-26 21:06:06Z root $
     */
    public function __UNSET ($objKey) {
        // Check
        if (isset ($this
        ->objContainer[$objKey])) {
            // Return
            unset ($this
            ->objContainer[$objKey]);
        } else {
            // Throws
            throw new OffsetKeyNotSetException;
        }
    }

    /**
     * Returns defined (dynamic) keys of the current object;
     *
     * @author Catalin Alexandru Zamfir <catalin.zamfir@raphpframework.ro>
     * @copyright Under the terms of the GNU General Public License v3
     * @version $Id: Commons.php 23 2012-10-26 21:06:06Z root $
     */
    public final function getDynamicData () {
        // Return
        return $this->objContainer;
    }
}
?>