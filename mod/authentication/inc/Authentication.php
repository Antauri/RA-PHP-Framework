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
 * Provides an extension for basic authentication;
 *
 * @author Catalin Alexandru Zamfir <catalin.zamfir@raphpframework.ro>
 * @copyright Under the terms of the GNU General Public License v3
 * @version $Id: Authentication.php 43 2012-11-07 08:52:49Z root $
 */
class Authentication extends Commons {

    /* Users */
    public static $objUser;
    public static $objUserId;
    public static $objUserUName;
    public static $objUserUPass;
    public static $objUserUNick;
    public static $objUserEML;
    public static $objUserPhone;
    public static $objUserFName;
    public static $objUserLName;
    public static $objUserUGId;
    public static $objUserRegOn;
    public static $objUserLastLog;
    public static $objUserHash;
    public static $objUserActivated;
    public static $objUserCountry;
    public static $objUserSignature;
    public static $objUserDesc;
    public static $objUserYM;
    public static $objUserMSN;
    public static $objUserICQ;
    public static $objUserAOL;
    public static $objUserCity;
    public static $objUserAvatar;
    public static $objUserCredit;
    public static $objUserIp;

    /* Properties */
    public static $objProperty;
    public static $objPropertyId;
    public static $objPropertyAId;
    public static $objPropertyKey;
    public static $objPropertyVar;
    public static $objPropertyPublished;
    public static $objPropertyUpdated;

    /* Groups */
    public static $objGroup;
    public static $objGroupId;
    public static $objGroupName;
    public static $objGroupSEO;

    /* Properties of groups */
    public static $objGroupProperty;
    public static $objGroupPropertyId;
    public static $objGroupPropertyGId;
    public static $objGroupPropertyKey;
    public static $objGroupPropertyVar;
    public static $objGroupPropertyPublished;
    public static $objGroupPropertyUpdated;

    /* Zones */
    public static $objZone;
    public static $objZoneId;
    public static $objZoneName;
    public static $objZoneDesc;
    public static $objZonePrice;

    /* Mappings */
    public static $objMapping;
    public static $objMappingId;
    public static $objMappingZId;
    public static $objMappingUGId;
    public static $objMappingIUG;
    public static $objMappingAorD;
    public static $objMappingErase;

    /* FEATURE: Backmapping */
    public static $objItem;
    public static $objItemId;
    public static $objItemTitle;

    /* MPTT & Others */
    protected static $objMPTT;
    protected static $objMPTTForZone;

    // Defaults
    protected static $objDefaultGroup;
    protected static $objDefaultUsername;
    protected static $objDefaultPassword;

    /* OAUTHs */
    const OAUTH_GOOGLE = 'google.com/accounts/o8/id';
    const OAUTH_YAHOO = 'yahoo.com';
    const OAUTH_MYOPENID = 'myopenid.com';

    /* PROPERTIES: Users */
    const PROPERTY_ITEM_IS_BANNED = 'Banned';
    const PROPERTY_ITEM_PUBLIC_PROFILE = 'Profile Is Public';
    const PROPERTY_ITEM_RECEIVE_NOTIFICATIONS = 'Receive Notifications';
    const PROPERTY_ITEM_COMPANY = 'Company';
    const PROPERTY_ITEM_COMPANY_CONTACT_PERSON = 'Contact Person';
    const PROPERTY_ITEM_HQ = 'Headquarters';
    const PROPERTY_ITEM_ADDRESS_1 = 'Address #1';
    const PROPERTY_ITEM_ADDRESS_2 = 'Address #2';
    const PROPERTY_ITEM_REGISTRY_OF_COMMERCE = 'Registry Of Commerce (J)';
    const PROPERTY_ITEM_UNIQUE_FISCAL_IDENTIFIER = 'Unique Fiscal Identifier (CUI)';
    const PROPERTY_ITEM_VAT_NUMBER = 'VAT Number';
    const PROPERTY_ITEM_BANK_ACCOUNT_RON = 'Bank Account #1 - RON';
    const PROPERTY_ITEM_BANK_ACCOUNT_EUR = 'Bank Account #2 - EUR';
    const PROPERTY_ITEM_BANK_ACCOUNT_USD = 'Bank Account #3 - USD';
    const PROPERTY_ITEM_MAIN_BANK = 'Bank Name (Main)';
    const PROPERTY_ITEM_FAX = 'Fax';
    const PROPERTY_ITEM_SECRET_PIN_OF_IDENTIFICATION = 'Secret PIN For Identification';
    const PROPERTY_ITEM_PRIVATE_KEY_FILE = 'Private Key File';

    /* PROPERTIES: Group */
    const PROPERTY_GROUP_HIGHLIGHT_COLOR = 'Highlight Color (HTML hex with # notation)';
    const PROPERTY_HIDE_FROM_USER_SIGN_UP_FORM = 'Hide From User Sign-Up Form';

    // CONSTRUCT
    public function __construct () {
        // Commons
        $this->tieInConfiguration ();

        // Configuration
        self::$objDefaultGroup    = $this->getConfigKey (new S ('Default.Group'));
        self::$objDefaultUsername = $this->getConfigKey (new S ('Default.Admin.Username'));
        self::$objDefaultPassword = $this->getConfigKey (new S ('Default.Admin.Password'));

        // Backmappings
        self::$objItem = self::$objUser;
        self::$objItemId = self::$objUserId;
        self::$objItemTitle = self::$objUserUName;

        // Table
        $this->tieInDatabase ($objT =
        new A (Array (self::$objUser,
        self::$objGroup, self::$objZone,
        self::$objMapping, self::$objProperty,
        self::$objGroupProperty)));

        // ACLs
        $this->setACL (new S ('Users'));
        $this->setACL (new S ('Groups'));
        $this->setACL (new S ('Zones'));
        $this->setACL (new S ('ACLs For Groups'));
        $this->setACL (new S ('ACLs For Users'));

        // Check unmodified user-data
        if ($this->checkIsLoggedIn ()
        ->toBoolean () == TRUE) { $this
        ->checkDataIsOK (); }

        // Get an MPTT Object, build the ROOT, make sure the table is OK
        self::$objMPTT = new Hierarchy (self::$objGroup, self::$objDefaultGroup);
        self::$objMPTTForZone = new Hierarchy (self::$objZone, $this->getObjectAncestry ());

        // Default user group
        if (self::$objMPTT->mpttCheckIfNodeExists(_T ('Users'))
        ->toBoolean () == FALSE) {
            // Set
            self::$objMPTT->mpttAddNode (_T ('Users'),
            self::$objDefaultGroup, new S ((string)
            Hierarchy::NEXT_BROTHER));
        }

        // The starting point
        $this->setDefaultAdministratorSettings ();

        // Check to see if this zone exists, and if not, add it
        if ($this->checkZoneByName (new S (__CLASS__))->toBoolean () == FALSE)
        $this->doMakeZone (new S (__CLASS__), self::$objZoneName);
        if ($this->checkAdministratorIsMappedToZone ($this->getObjectAncestry ())->toBoolean () == FALSE)
        $this->doMapAdministratorToZone ($this->getObjectAncestry ());

        // Do the tie, with myself
        $this->tieInAuthentication ($this);
    }

    /**
     * (non-PHPdoc)
     * @see Commons::tieInAdministration()
     */
    public function tieInAdministration (Administration $objAdministration) {
        // CALL __parent
        parent::tieInAdministration ($objAdministration);

        // Set ACLs
        $objACL = $this->getACLs ();

        // Administration
        $objWP = new Path (Architecture
        ::pathTo ($this->getPathToAdmin (),
        $this->getConfigKey (new S ('Dashboard'))));
        self::getAdministration ()->setLink (_T ('Users'),
        $objWP, $this->getHELP (_T ('Users')));

        // ONLY: Users
        if ($this->checkCurrentUserZoneACL ($objACL[0])
        ->toBoolean () == TRUE) {
            $objMU = new Path (Architecture
            ::pathTo ($this->getPathToAdmin (),
            $this->getConfigKey (new S ('Users'))));
            self::getAdministration ()->setSink (_T ('Users'),
            $objMU, $this->getHELP (_T ('Users')));
        }

        // ONLY: Groups
        if ($this->checkCurrentUserZoneACL ($objACL[1])
        ->toBoolean () == TRUE) {
            $objMG = new Path (Architecture
            ::pathTo ($this->getPathToAdmin (),
            $this->getConfigKey (new S ('Groups'))));
            self::getAdministration ()->setSink (_T ('Groups'),
            $objMG, $this->getHELP (_T ('Groups')));
        }

        // ONLY: Zones
        if ($this->checkCurrentUserZoneACL ($objACL[2])
        ->toBoolean () == TRUE) {
            $objMZ = new Path (Architecture
            ::pathTo ($this->getPathToAdmin (),
            $this->getConfigKey (new S ('Zones'))));
            self::getAdministration ()->setSink (_T ('Zones'),
            $objMZ, $this->getHELP (_T ('Zones')));
        }

        // ONLY: ACLs for groups
        if ($this->checkCurrentUserZoneACL ($objACL[3])
        ->toBoolean () == TRUE) {
            $objMM = new Path (Architecture
            ::pathTo ($this->getPathToAdmin (),
            $this->getConfigKey (new S ('Mappings'))));
            self::getAdministration ()->setSink (_T ('ACLs for groups'),
            $objMM, $this->getHELP (_T ('ACLs for groups')));
        }

        // ONLY: ACLs for users
        if ($this->checkCurrentUserZoneACL ($objACL[4])
        ->toBoolean () == TRUE) {
            $objMU = new Path (Architecture
            ::pathTo ($this->getPathToAdmin (),
            $this->getConfigKey (new S ('MappingsForUser'))));
            self::getAdministration ()->setSink (_T ('ACLs for users'),
            $objMU, $this->getHELP (_T ('ACLs for users')));
        }

        // Items
        $this->getAdministration ()->setWidget ($this
        ->getHELP (new S ('adminWidgetItems'))
        ->doToken ('%uId', $this->getUserCount ())
        ->doToken ('%gId', $this->getGroupCount ()));
    }

    /**
     * Ties a specific list of texts that are required by the system;
     *
     * @author Catalin Alexandru Zamfir <catalin.zamfir@raphpframework.ro>
     * @copyright Under the terms of the GNU General Public License v3
     * @version $Id: Authentication.php 43 2012-11-07 08:52:49Z root $
     */
    public function tieInSystemTexts (Texts $objT) {
        // Requirements
        $objT->setSystemText (_T ('Sign Up'), $this->getHELP (new S ('Sign Up')));
        $objT->setSystemText (_T ('Sign Up Success'), $this->getHELP (new S ('Sign Up Success')));
        $objT->setSystemText (_T ('Activation Success'), $this->getHELP (new S ('Activation Success')));
        $objT->setSystemText (_T ('Activation Failure'), $this->getHELP (new S ('Activation Failure')));
        $objT->setSystemText (_T ('Sign In'), $this->getHELP (new S ('Sign In')));
        $objT->setSystemText (_T ('Sign Out'), $this->getHELP (new S ('Sign Out')));
        $objT->setSystemText (_T ('OAuth Sign Up'), $this->getHELP (new S ('OAuth Sign Up')));
        $objT->setSystemText (_T ('OAuth Sign Up Success'), $this->getHELP (new S ('OAuth Sign Up Success')));
        $objT->setSystemText (_T ('OAuth Sign Up Error'), $this->getHELP (new S ('OAuth Sign Up Error')));
        $objT->setSystemText (_T ('Profile'), $this->getHELP (new S ('Profile')));
        $objT->setSystemText (_T ('Disabled Sign In'), $this->getHELP (new S ('Disabled Sign In')));
        $objT->setSystemText (_T ('Creditation'), $this->getHELP (new S ('Creditation')));
    }

    /**
     * Sets default administration group, user & settings;
     *
     * @author Catalin Alexandru Zamfir <catalin.zamfir@raphpframework.ro>
     * @copyright Under the terms of the GNU General Public License v3
     * @version $Id: Authentication.php 43 2012-11-07 08:52:49Z root $
     */
    public function setDefaultAdministratorSettings () {
        // Check
        if ($this->getUserCount ()
        ->toInt () == 0) {
            // Return
            return $this->_Q (_QS ('doINSERT')
            ->doToken ('%table', self::$objUser)->doToken ('%condition', new S ('%objUserUName = "%u",
            %objUserUPass = "%p", %objUserUGId = "%g"'))->doToken ('%u', self::$objDefaultUsername)
            ->doToken ('%p', self::$objDefaultPassword->encryptIt (sha1 (self::$objDefaultPassword)))
            ->doToken ('%g', $this->getGroups ()->offsetGet (0)->offsetGet (self::$objGroupId)));
        }
    }

    /**
     * Check if user is logged in or not;
     *
     * @author Catalin Alexandru Zamfir <catalin.zamfir@raphpframework.ro>
     * @copyright Under the terms of the GNU General Public License v3
     * @version $Id: Authentication.php 43 2012-11-07 08:52:49Z root $
     */
    public function checkIsLoggedIn () {
        // Return
        return new
        B ($this->getCookie ()->checkKey (self::$objUserId)->toBoolean () == TRUE &&
        $this->getCookie ()->checkKey (self::$objUserUName)->toBoolean () == TRUE &&
        $this->getCookie ()->checkKey (self::$objUserUPass)->toBoolean () == TRUE);
    }

    /**
     * Check to see if the data integrity is OK, and nobody tampered with it;
     *
     * @author Catalin Alexandru Zamfir <catalin.zamfir@raphpframework.ro>
     * @copyright Under the terms of the GNU General Public License v3
     * @version $Id: Authentication.php 43 2012-11-07 08:52:49Z root $
     */
    public function checkDataIsOK () {
        // Return
        return new B ($this->getCookie ()->checkKey (self::$objUserId)->toBoolean () == TRUE) &&
        $this->_Q (_QS ('doSELECT')->doToken ('%what', new S ('*'))->doToken ('%table', self::$objUser)
        ->doToken ('%condition', new S ('WHERE %objUserUName = "%uId" AND %objUserUPass = "%pId"
        AND %objUserActivated = "Y" LIMIT 1'))->doToken ('%uId', $this
        ->getCookie ()->getKey (self::$objUserUName))
        ->doToken ('%pId', $this->getCookie ()->getKey (self::$objUserUPass)))
        ->doCount ()->toInt () == 0 ? $this->doSignOut () : FALSE;
    }

    /**
     * Signs a given user in, with his given password;
     *
     * @author Catalin Alexandru Zamfir <catalin.zamfir@raphpframework.ro>
     * @copyright Under the terms of the GNU General Public License v3
     * @version $Id: Authentication.php 43 2012-11-07 08:52:49Z root $
     */
    public function doSignIn (S $objUsername, S $objPassword, B $objRemember = NULL) {
        // Permanent, or not
        $objBt = $objRemember == NULL ?
        new B (FALSE) : $objRemember;

        // Query
        $objSQLQuery = $this->_Q (_QS ('doSELECT')
        ->doToken ('%what', new S ('*'))->doToken ('%table', self::$objUser)
        ->doToken ('%condition', new S ('WHERE %objUserUName = "%uId" AND %objUserUPass = "%pId"
        AND %objUserActivated = "Y" LIMIT 1'))->doToken ('%uId', $objUsername)
        ->doToken ('%pId', $objPassword->encryptIt (sha1 ($objPassword))));

        // Check
        if ($objSQLQuery
        ->doCount ()->toInt () > 0) {
            // Requirements
            $objId = $objSQLQuery->offsetGet (0)->offsetGet (self::$objUserId);
            $objUr = $objSQLQuery->offsetGet (0)->offsetGet (self::$objUserUName);
            $objPw = $objSQLQuery->offsetGet (0)->offsetGet (self::$objUserUPass);

            // Parameters
            $this->getCookie ()->setKey (self::$objUserId, $objId, $objBt);
            $this->getCookie ()->setKey (self::$objUserUName, $objUr, $objBt);
            $this->getCookie ()->setKey (self::$objUserUPass, $objPw, $objBt);

            // Update
            $this->_Q (_QS ('doUPDATE')
            ->doToken ('%table', self::$objUser)
            ->doToken ('%condition', new
            S ('%objUserLastLog = "%pId"
            WHERE %objUserId = "%uId" LIMIT 1'))
            ->doToken ('%uId', $objId)
            ->doToken ('%pId', time ()));

            // Ip
            $objIp = Architecture::getUserAgentIp ();

            // Update
            $this->_Q (_QS ('doUPDATE')
            ->doToken ('%table', self::$objUser)
            ->doToken ('%condition', new
            S ('%objUserIp = "%pId"
            WHERE %objUserId = "%uId" LIMIT 1'))
            ->doToken ('%uId', $objId)
            ->doToken ('%pId', $objIp));

            // Return
            return new B (TRUE);
        } else {
            // Return
            return new B (FALSE);
        }
    }

    /**
     * Signs the current user out;
     *
     * @author Catalin Alexandru Zamfir <catalin.zamfir@raphpframework.ro>
     * @copyright Under the terms of the GNU General Public License v3
     * @version $Id: Authentication.php 43 2012-11-07 08:52:49Z root $
     */
    public function doSignOut () {
        // Check
        if ($this->getCookie ()
        ->checkKey (self::$objUserId)
        ->toBoolean () == TRUE) {
            // Unset
            $this->getCookie ()->unSetKey (self::$objUserId);
            $this->getCookie ()->unSetKey (self::$objUserUName);
            $this->getCookie ()->unSetKey (self::$objUserUPass);

            // Return
            return new B (TRUE);
        } else {
            // Return
            return new B (FALSE);
        }
    }

    /**
     * Activates the given user, of the given hash;
     *
     * @author Catalin Alexandru Zamfir <catalin.zamfir@raphpframework.ro>
     * @copyright Under the terms of the GNU General Public License v3
     * @version $Id: Authentication.php 43 2012-11-07 08:52:49Z root $
     */
    public function activateByHash (S $objHASH) {
        // Return
        return $this->_Q (_QS ('doUPDATE')
        ->doToken ('%table', self::$objUser)->doToken ('%condition', new S ('%objUserActivated = "Y",
      %objUserHash = "" WHERE %objUserHash = "%hId" LIMIT 1'))->doToken ('%hId', $objHASH));
    }

    /**
     * Returns group via user id;
     *
     * @author Catalin Alexandru Zamfir <catalin.zamfir@raphpframework.ro>
     * @copyright Under the terms of the GNU General Public License v3
     * @version $Id: Authentication.php 43 2012-11-07 08:52:49Z root $
     */
    public function getGroupByUserId (S $objUserId, S $objFieldToGet) {
        // Return
        return $this->getGroupById ($this->getUserById ($objUserId,
        self::$objUserUGId), $objFieldToGet);
    }

    /**
     * Returns current user;
     *
     * @author Catalin Alexandru Zamfir <catalin.zamfir@raphpframework.ro>
     * @copyright Under the terms of the GNU General Public License v3
     * @version $Id: Authentication.php 43 2012-11-07 08:52:49Z root $
     */
    public function getCurrentUser (S $objFieldToGet) {
        // Return
        return ($this->getCookie ()->checkKey (self::$objUserId)->toBoolean () == TRUE) ?
        $this->getUserById ($this->getCookie ()->getKey (self::$objUserId),
        $objFieldToGet) : new B (FALSE);
    }

    /**
     * Returns current group for current user;
     *
     * @author Catalin Alexandru Zamfir <catalin.zamfir@raphpframework.ro>
     * @copyright Under the terms of the GNU General Public License v3
     * @version $Id: Authentication.php 43 2012-11-07 08:52:49Z root $
     */
    public function getGroupInfoForCurrentUser (S $objFieldToGet) {
        // Return
        return ($this->getCookie ()->checkKey (self::$objUserId)->toBoolean () == TRUE) ?
        $this->getGroupByUserId ($this->getCookie ()->getKey (self::$objUserId),
        $objFieldToGet) : new B (FALSE);
    }

    /**
     * Returns the group path for the current user;
     *
     * @author Catalin Alexandru Zamfir <catalin.zamfir@raphpframework.ro>
     * @copyright Under the terms of the GNU General Public License v3
     * @version $Id: Authentication.php 43 2012-11-07 08:52:49Z root $
     */
    public function getGroupPathForCurrentUser () {
        // Return
        return self::$objMPTT->mpttGetSinglePath ($this
        ->getGroupInfoForCurrentUser (self::$objGroupName));
    }

    /**
     * Check current user ACL;
     *
     * @author Catalin Alexandru Zamfir <catalin.zamfir@raphpframework.ro>
     * @copyright Under the terms of the GNU General Public License v3
     * @version $Id: Authentication.php 43 2012-11-07 08:52:49Z root $
     */
    public function checkCurrentUserZoneACL (S $objZoneName) {
        // Return
        return $this->getCookie ()->checkKey (self::$objUserId)->toBoolean () == TRUE ?
        $this->checkZoneACL ($this->getCurrentUser (self::$objUserUName), $objZoneName) : new B (FALSE);
    }

    /**
     * Returns user count;
     *
     * @author Catalin Alexandru Zamfir <catalin.zamfir@raphpframework.ro>
     * @copyright Under the terms of the GNU General Public License v3
     * @version $Id: Authentication.php 43 2012-11-07 08:52:49Z root $
     */
    public function getUserCount (S $objSQLCondition = NULL) {
        // Return
        return new I ((int) (string) $this->_Q (_QS ('doSELECT')
        ->doToken ('%what', new S ('COUNT(%objUserId) AS count'))->doToken ('%table', self::$objUser)
        ->doToken ('%condition', $objSQLCondition))->offsetGet (0)->offsetGet ('count'));
    }

    /**
     * Returns property count for given group id;
     *
     * @author Catalin Alexandru Zamfir <catalin.zamfir@raphpframework.ro>
     * @copyright Under the terms of the GNU General Public License v3
     * @version $Id: Authentication.php 43 2012-11-07 08:52:49Z root $
     */
    public function getPropertyCountByGroupId (S $objGroupId, S $objSQLCondition = NULL) {
        // Return
        return new I ((int) (string) $this->_Q (_QS ('doSELECT')
        ->doToken ('%what', new S ('COUNT(*) as count'))->doToken ('%table', self::$objGroupProperty)
        ->doToken ('%condition', new S ('WHERE %objGroupPropertyGId = "%Id" %condition'))
        ->doToken ('%Id', $objGroupId)->doToken ('%condition', $objSQLCondition))
        ->offsetGet (0)->offsetGet ('count'));
    }

    /**
     * Returns group count;
     *
     * @author Catalin Alexandru Zamfir <catalin.zamfir@raphpframework.ro>
     * @copyright Under the terms of the GNU General Public License v3
     * @version $Id: Authentication.php 43 2012-11-07 08:52:49Z root $
     */
    public function getGroupCount (S $objSQLCondition = NULL) {
        // Return
        return new I ((int) (string) $this->_Q (_QS ('doSELECT')
        ->doToken ('%what', new S ('COUNT(%objGroupId) AS count'))->doToken ('%table', self::$objGroup)
        ->doToken ('%condition', $objSQLCondition))->offsetGet (0)->offsetGet ('count'));
    }

    /**
     * Returns zone count;
     *
     * @author Catalin Alexandru Zamfir <catalin.zamfir@raphpframework.ro>
     * @copyright Under the terms of the GNU General Public License v3
     * @version $Id: Authentication.php 43 2012-11-07 08:52:49Z root $
     */
    public function getZoneCount (S $objSQLCondition = NULL) {
        // Return
        return new I ((int) (string) $this->_Q (_QS ('doSELECT')
        ->doToken ('%what', new S ('COUNT(%objZoneId) AS count'))->doToken ('%table', self::$objZone)
        ->doToken ('%condition', $objSQLCondition))->offsetGet (0)->offsetGet ('count'));
    }

    /**
     * Returns mappings count (zones to groups or users);
     *
     * @author Catalin Alexandru Zamfir <catalin.zamfir@raphpframework.ro>
     * @copyright Under the terms of the GNU General Public License v3
     * @version $Id: Authentication.php 43 2012-11-07 08:52:49Z root $
     */
    public function getMappingCount (S $objSQLCondition = NULL) {
        // Return
        return new I ((int) (string) $this->_Q (_QS ('doSELECT')
        ->doToken ('%what', new S ('COUNT(%objMappingId) AS count'))->doToken ('%table', self::$objMapping)
        ->doToken ('%condition', $objSQLCondition))->offsetGet (0)->offsetGet ('count'));
    }

    /**
     * Returns users;
     *
     * @author Catalin Alexandru Zamfir <catalin.zamfir@raphpframework.ro>
     * @copyright Under the terms of the GNU General Public License v3
     * @version $Id: Authentication.php 43 2012-11-07 08:52:49Z root $
     */
    public function getUsers (S $objSQLCondition = NULL) {
        // Return
        return $this->_Q (_QS ('doSELECT')
        ->doToken ('%what', new S ('*'))->doToken ('%table', self::$objUser)
        ->doToken ('%condition', $objSQLCondition));
    }

    /**
     * Returns properties by group id;
     *
     * @author Catalin Alexandru Zamfir <catalin.zamfir@raphpframework.ro>
     * @copyright Under the terms of the GNU General Public License v3
     * @version $Id: Authentication.php 43 2012-11-07 08:52:49Z root $
     */
    public function getPropertiesByGroupId (S $objGroupId, S $objSQLCondition = NULL) {
        // Return
        return $this->_Q (_QS ('doSELECT')
        ->doToken ('%what', new S ('*'))->doToken ('%table', self::$objGroupProperty)
        ->doToken ('%condition', new S ('WHERE %objGroupPropertyGId = "%Id" %condition'))
        ->doToken ('%Id', $objGroupId)->doToken ('%condition', $objSQLCondition));
    }

    /**
     * Returns user by id;
     *
     * @author Catalin Alexandru Zamfir <catalin.zamfir@raphpframework.ro>
     * @copyright Under the terms of the GNU General Public License v3
     * @version $Id: Authentication.php 43 2012-11-07 08:52:49Z root $
     */
    public function getUserById (S $objUserId, S $objFieldToGet) {
        // Return
        return $this->_Q (_QS ('doSELECT')
        ->doToken ('%what', $objFieldToGet)->doToken ('%table', self::$objUser)
        ->doToken ('%condition', new S ('WHERE %objUserId = "%Id" LIMIT 1'))
        ->doToken ('%Id', $objUserId))->offsetGet (0)->offsetGet ($objFieldToGet);
    }

    /**
     * Returns user by name;
     *
     * @author Catalin Alexandru Zamfir <catalin.zamfir@raphpframework.ro>
     * @copyright Under the terms of the GNU General Public License v3
     * @version $Id: Authentication.php 43 2012-11-07 08:52:49Z root $
     */
    public function getUserByName (S $objUserName, S $objFieldToGet) {
        // Return
        return $this->_Q (_QS ('doSELECT')
        ->doToken ('%what', $objFieldToGet)->doToken ('%table', self::$objUser)
        ->doToken ('%condition', new S ('WHERE %objUserUName = "%Id" LIMIT 1'))
        ->doToken ('%Id', $objUserName))->offsetGet (0)->offsetGet ($objFieldToGet);
    }

    /**
     * Returns groups;
     *
     * @author Catalin Alexandru Zamfir <catalin.zamfir@raphpframework.ro>
     * @copyright Under the terms of the GNU General Public License v3
     * @version $Id: Authentication.php 43 2012-11-07 08:52:49Z root $
     */
    public function getGroups (S $objSQLCondition = NULL, S $objSubCategory = NULL) {
        // Make a CALL to the MPTT object;
        return self::$objMPTT->mpttGetTree ($objSubCategory,
        $objSQLCondition);
    }

    /**
     * Returns group by id;
     *
     * @author Catalin Alexandru Zamfir <catalin.zamfir@raphpframework.ro>
     * @copyright Under the terms of the GNU General Public License v3
     * @version $Id: Authentication.php 43 2012-11-07 08:52:49Z root $
     */
    public function getGroupById (S $objGroupId, S $objFieldToGet) {
        // Return
        return $this->_Q (_QS ('doSELECT')
        ->doToken ('%what', $objFieldToGet)->doToken ('%table', self::$objGroup)
        ->doToken ('%condition', new S ('WHERE %objGroupId = "%Id" LIMIT 1'))
        ->doToken ('%Id', $objGroupId))->offsetGet (0)->offsetGet ($objFieldToGet);
    }

    /**
     * Returns group by name;
     *
     * @author Catalin Alexandru Zamfir <catalin.zamfir@raphpframework.ro>
     * @copyright Under the terms of the GNU General Public License v3
     * @version $Id: Authentication.php 43 2012-11-07 08:52:49Z root $
     */
    public function getGroupByName (S $objGroupName, S $objFieldToGet) {
        // Return
        return $this->_Q (_QS ('doSELECT')
        ->doToken ('%what', $objFieldToGet)->doToken ('%table', self::$objGroup)
        ->doToken ('%condition', new S ('WHERE %objGroupName = "%Id" LIMIT 1'))
        ->doToken ('%Id', $objGroupName))->offsetGet (0)->offsetGet ($objFieldToGet);
    }

    /**
     * Returns zones;
     *
     * @author Catalin Alexandru Zamfir <catalin.zamfir@raphpframework.ro>
     * @copyright Under the terms of the GNU General Public License v3
     * @version $Id: Authentication.php 43 2012-11-07 08:52:49Z root $
     */
    public function getZones (S $objSQLCondition = NULL, S $objSubCategory = NULL) {
        // Return
        return self::$objMPTTForZone
        ->mpttGetTree ($objSubCategory,
        $objSQLCondition);
    }

    /**
     * Return zones via SQL condition;
     *
     * @author Catalin Alexandru Zamfir <catalin.zamfir@raphpframework.ro>
     * @copyright Under the terms of the GNU General Public License v3
     * @version $Id: Authentication.php 43 2012-11-07 08:52:49Z root $
     */
    public function getZonesBy (S $objSQLCondition = NULL) {
        return $this->_Q (_QS ('doSELECT')
        ->doToken ('%what', new S ('*'))->doToken ('%table', self::$objZone)
        ->doToken ('%condition', $objSQLCondition));
    }

    /**
     * Returns zone by id;
     *
     * @author Catalin Alexandru Zamfir <catalin.zamfir@raphpframework.ro>
     * @copyright Under the terms of the GNU General Public License v3
     * @version $Id: Authentication.php 43 2012-11-07 08:52:49Z root $
     */
    public function getZoneById (S $objZoneId, S $objFieldToGet) {
        // Return
        return $this->_Q (_QS ('doSELECT')
        ->doToken ('%what', $objFieldToGet)->doToken ('%table', self::$objZone)
        ->doToken ('%condition', new S ('WHERE %objZoneId = "%Id" LIMIT 1'))
        ->doToken ('%Id', $objZoneId))->offsetGet (0)->offsetGet ($objFieldToGet);
    }

    /**
     * Return zones by name;
     *
     * @author Catalin Alexandru Zamfir <catalin.zamfir@raphpframework.ro>
     * @copyright Under the terms of the GNU General Public License v3
     * @version $Id: Authentication.php 43 2012-11-07 08:52:49Z root $
     */
    public function getZoneByName (S $objZoneName, S $objFieldToGet) {
        // Return
        return $this->_Q (_QS ('doSELECT')
        ->doToken ('%what', $objFieldToGet)->doToken ('%table', self::$objZone)
        ->doToken ('%condition', new S ('WHERE %objZoneName = "%Id" LIMIT 1'))
        ->doToken ('%Id', $objZoneName))->offsetGet (0)->offsetGet ($objFieldToGet);
    }

    /**
     * Check zone by name;
     *
     * @author Catalin Alexandru Zamfir <catalin.zamfir@raphpframework.ro>
     * @copyright Under the terms of the GNU General Public License v3
     * @version $Id: Authentication.php 43 2012-11-07 08:52:49Z root $
     */
    public function checkZoneByName (S $objZoneName) {
        if ($this->_Q (_QS ('doSELECT')
        ->doToken ('%what', self::$objZoneName)->doToken ('%table', self::$objZone)
        ->doToken ('%condition', new S ('WHERE %objZoneName = "%var" LIMIT 1'))
        ->doToken ('%var', $objZoneName))->doCount ()->toInt () != 0) {
            // Return
            return new B (TRUE);
        } else {
            // Return
            return new B (FALSE);
        }
    }

    /**
     * Makes a specified zone via parent;
     *
     * @author Catalin Alexandru Zamfir <catalin.zamfir@raphpframework.ro>
     * @copyright Under the terms of the GNU General Public License v3
     * @version $Id: Authentication.php 43 2012-11-07 08:52:49Z root $
     */
    public function doMakeZone (S $objZoneName, S $objZonePName = NULL) {
        // Memorize
        $objAddAsKid = new B (TRUE);

        // Check
        if ($objZonePName == NULL) {
            // Set
            $objZonePName = $this
            ->getObjectAncestry ();

            // Switch
            $objAddAsKid
            ->switchType ();
        }

        // Switch
        switch ($objAddAsKid->toBoolean ()) {
            case TRUE:
                // Return
                return self::$objMPTTForZone->mpttAddNode ($objZoneName,
                $objZonePName, new S ((string) Hierarchy::LAST_CHILD));
                break;

            case FALSE:
                // Return
                return self::$objMPTTForZone->mpttAddNode ($objZoneName,
                $objZonePName, new S ((string) Hierarchy::PREVIOUS_BROTHER));
                break;
        }
    }

    /**
     * Returns zone mappings;
     *
     * @author Catalin Alexandru Zamfir <catalin.zamfir@raphpframework.ro>
     * @copyright Under the terms of the GNU General Public License v3
     * @version $Id: Authentication.php 43 2012-11-07 08:52:49Z root $
     */
    public function getZoneMappings (S $objSQLCondition = NULL) {
        // Do a query, set the conditions, return the array;
        foreach ($objReturnedZones = $this->_Q (_QS ('doSELECT')
        ->doToken ('%what', new S ('*'))->doToken ('%table', self::$objMapping)
        ->doToken ('%condition', $objSQLCondition)) as $objK => $objV) {

            // Get the zone name, for display
            $objV['zone_name'] = $this->getZoneById ($objV[self::$objMappingZId],
            self::$objZoneName);

            // Get either group or the user's name
            if ($objV[self::$objMappingIUG] == 'Y') {
                $objV['user_or_group_name'] = $this
                ->getGroupById ($objV[self::$objMappingUGId],
                self::$objGroupName);
            } else {
                $objV['user_or_group_name'] = $this
                ->getUserById ($objV[self::$objMappingUGId],
                self::$objUserUName);
            }
        }

        // Return
        return $objReturnedZones;
    }

    /**
     * Checks if administrator is mapped to zone;
     *
     * @author Catalin Alexandru Zamfir <catalin.zamfir@raphpframework.ro>
     * @copyright Under the terms of the GNU General Public License v3
     * @version $Id: Authentication.php 43 2012-11-07 08:52:49Z root $
     */
    public function checkAdministratorIsMappedtoZone (S $objZoneName) {
        // Return
        return $this->checkFixedACL (self::$objDefaultGroup,
        $objZoneName, new S ('A'));
    }

    /**
     * Maps administrator to the given zone;
     *
     * @author Catalin Alexandru Zamfir <catalin.zamfir@raphpframework.ro>
     * @copyright Under the terms of the GNU General Public License v3
     * @version $Id: Authentication.php 43 2012-11-07 08:52:49Z root $
     */
    public function doMapAdministratorToZone (S $objZoneName) {
        // Return
        return $this->setFixedACL (self::$objDefaultGroup,
        $objZoneName, new S ('A'));
    }

    /**
     * Checks the ACL mapping table to see if the group has clearance for the specified zone;
     *
     * @author Catalin Alexandru Zamfir <catalin.zamfir@raphpframework.ro>
     * @copyright Under the terms of the GNU General Public License v3
     * @version $Id: Authentication.php 43 2012-11-07 08:52:49Z root $
     */
    private function checkFixedACL (S $objGroup, S $objZone) {
        // Get some requirements
        $objZoneId  = $this->getZoneByName ($objZone, self::$objZoneId);
        $objGroupId = $this->getGroupByName ($objGroup, self::$objGroupId);

        // Do the query, make it happen
        if ($this->_Q (_QS ('doSELECT')
        ->doToken ('%what', new S ('*'))->doToken ('%table', self::$objMapping)
        ->doToken ('%condition', new S ('WHERE %objMappingZId = "%z"
        AND %objMappingUGId = "%g" AND %objMappingIUG = "Y" LIMIT 1'))
        ->doToken ('%g', $objGroupId)
        ->doToken ('%z', $objZoneId))
        ->doCount ()->toInt () != 0) {
            // Return
            return new B (TRUE);
        } else {
            // Return
            return new B (FALSE);
        }
    }

    /**
     * Will set the fix'ed (system!) ACLs;
     *
     * @author Catalin Alexandru Zamfir <catalin.zamfir@raphpframework.ro>
     * @copyright Under the terms of the GNU General Public License v3
     * @version $Id: Authentication.php 43 2012-11-07 08:52:49Z root $
     */
    private function setFixedACL (S $objGroupName, S $objZoneName, S $objTypeOfAccess) {
        // Do switch
        switch ($objTypeOfAccess) {
            case 'D':
            case 'A':
                // Get some requirements;
                $objZoneId  = $this->getZoneByName  ($objZoneName, self::$objZoneId);
                $objGroupId = $this->getGroupByName ($objGroupName, self::$objGroupId);

                // Return
                return $this->_Q (_QS ('doINSERT')
                ->doToken ('%table', self::$objMapping)
                ->doToken ('%condition', new S ('%objMappingIUG = "Y", %objMappingErase = "N",
                %objMappingZId  = "%z", %objMappingAorD = "%t", %objMappingUGId = "%g"'))
                ->doToken ('%z', $objZoneId)
                ->doToken ('%t', $objTypeOfAccess)
                ->doToken ('%g', $objGroupId));
                // BK;
                break;

            default:
                // Render a screen of death
                self::renderDeath (new S (__CLASS__),
                _T ('Incompatible zone access type given!'),
                _T ('Zone access type should be A/D, allow or deny respectivelly!'));
                // BK;
                break;
        }
    }

    /**
     * Will check if the username has clearance over the specified zone, by checking the group path versus the zone name;
     *
     * @author Catalin Alexandru Zamfir <catalin.zamfir@raphpframework.ro>
     * @copyright Under the terms of the GNU General Public License v3
     * @version $Id: Authentication.php 43 2012-11-07 08:52:49Z root $
     */
    private function checkZoneACL (S $objUserName, S $objZoneName) {
        // Get some requirements;
        $objUserId = $this->getUserByName ($objUserName, self::$objUserId);
        $objZoneId = $this->getZoneByName ($objZoneName, self::$objZoneId);

        // Make a new query container;
        $objQ = $this->_Q (_QS ('doSELECT')
        ->doToken ('%what', self::$objMappingAorD)->doToken ('%table', self::$objMapping)
        ->doToken ('%condition', new S ('WHERE %objMappingZId = "%z" AND %objMappingUGId = "%u"
        AND %objMappingIUG = "N" LIMIT 1'))->doToken ('%z', $objZoneId)->doToken ('%u', $objUserId));

        // Check
        if ($objQ->doCount ()->toInt () != 0) {
            // Switch
            switch ($objQ->offsetGet (0)
            ->offsetGet (self::$objMappingAorD)) {
                case 'A':
                    // Return
                    return new B (TRUE);
                    break;

                default:
                    // Return
                    return new B (FALSE);
                    break;
            }
        } else {
            // Determine the path from the users sub-group, to the top root group;
            $objCurrentUserHierarchy = self::$objMPTT->mpttGetSinglePath ($this->getGroupByUserId
            ($this->getUserByName ($objUserName, self::$objUserId), self::$objGroupName));

            // Foreach LVL, get respective zone-mappings;
            foreach ($objCurrentUserHierarchy as $objK => $objV) {
                $objGroupId = $this->getGroupByName ($objV[self::$objGroupName], self::$objGroupId);
                $objQueryACL[] = $this->_Q (_QS ('doSELECT')
                ->doToken ('%what', new S ('*'))->doToken ('%table', self::$objMapping)
                ->doToken ('%condition', new S ('WHERE %objMappingZId = "%z" AND %objMappingUGId = "%g"
                AND %objMappingIUG = "Y"'))->doToken ('%z', $objZoneId)->doToken ('%g', $objGroupId))->offsetGet (0);
            }

            // Go down the tree, pass through every mapped group/subgroup and memorize, the LAST Access/Denied flag;
            $objACLMemorized = new S ('D');
            foreach ($objQueryACL as $objK => $objV) {
                if ($objV->doCount ()->toInt () != 0) {
                    $objACLMemorized->setString ($objV[self::$objMappingAorD]);
                }
            }

            // Switch between A/D/OR ELSE;
            switch ($objACLMemorized) {
                case 'A':
                    return new B (TRUE);
                    break;

                case 'D':
                    return new B (FALSE);
                    break;

                default:
                    return new B (FALSE);
                    break;
            }
        }
    }

    /**
     * In case an error gets returned from our Open Identity provider, this method allows for redirection to a proper path where either
     * the message is shown or the error gets treated. By default we redirect to the proper text!
     *
     * @author Catalin Alexandru Zamfir <catalin.zamfir@raphpframework.ro>
     * @copyright Under the terms of the GNU General Public License v3
     * @version $Id: Authentication.php 43 2012-11-07 08:52:49Z root $
     */
    public function openIdentityError () {
        // Redirect
        Header::setKey (Location
        ::staticTo (new A (Array ('Texts')),
        new A (Array ('OAuth Sign Up Error'))),
        new S ('Location'));
    }

    /**
     * If everything is OK from our Open Identity provider, then we automatically register and login the given user. Basic password
     * is based on it's address, but a mechanism needs to be developed on top to force the user in changing his password.
     *
     * @author Catalin Alexandru Zamfir <catalin.zamfir@raphpframework.ro>
     * @copyright Under the terms of the GNU General Public License v3
     * @version $Id: Authentication.php 43 2012-11-07 08:52:49Z root $
     */
    public function openIdentitySucces () {
        // Reverse
        $objT[] = '_';
        $objR[] = '/';
        $objT[] = '-U-';
        $objR[] = '_';
        $objT[] = '-AT-';
        $objR[] = '@';
        $objT[] = '-DOT-';
        $objR[] = '.';

        // Required
        $objIdentity = $_GET
        ->offsetGet ('contact_email')
        ->doToken ($objT, $objR);

        // Set
        $objSQLCondition = new S;

        // Check
        if ($_GET->offsetExists ('namePerson_friendly')) {
            // Append
            $objSQLCondition->appendString (',')
            ->appendString ('%objUserFName =  "%vId"')
            ->doToken ('%vId', $_GET
            ->offsetGet ('namePerson_friendly')
            ->doToken ($objT, $objR));
        }

        // Check
        if ($_GET->offsetExists ('namePerson')) {
            // Append
            $objSQLCondition->appendString (',')
            ->appendString ('%objUserLName =  "%vId"')
            ->doToken ('%vId', $_GET
            ->offsetGet ('namePerson')
            ->doToken ($objT, $objR));
        }

        // Insert (no-conflict)
        $this->_Q (_QS ('doINSERT')
        ->doToken ('%table', self::$objUser)
        ->doToken ('%condition', new S ('%objUserUName = "%uId",
        %objUserUPass = "%pId", %objUserEML = "%eId", %objUserUGId = "%gId",
        %objUserRegOn = UNIX_TIMESTAMP () %condition'))
        ->doToken ('%uId', $objIdentity)->doToken ('%eId', $objIdentity)
        ->doToken ('%pId', $objIdentity->encryptIt (sha1 ($objIdentity)))
        ->doToken ('%condition', $objSQLCondition)
        ->doToken ('%gId', $this->getGroups (NULL,
        new S ('Users'))->offsetGet (0)
        ->offsetGet (self::$objGroupId)));

        // Authenticate
        $this->doSignIn ($objIdentity,
        $objIdentity);

        // Redirect
        Header::setKey (Location
        ::staticTo (new A (Array ('Texts')),
        new A (Array (Location::getFrom (new
        S ('OAuth Sign Up Success'))))),
        new S ('Location'));
    }

    /**
     * (non-PHPdoc)
     * @see Commons::doURLRouting()
     */
    public function doURLRouting (Frontend $objFront) {
        // Check
        if ($_GET
        ->offsetExists (_T ('Action'))) {
            // Switch
            switch ($_GET
            ->offsetGet (_T ('Action'))) {
                case 'OAuth Log In':
                    $this->renderWidget (new
                    S ('widgetOAuthLogIn'));
                    break;
            }
        }
    }

    /**
     * (non-PHPdoc)
     * @see Commons::renderWidget()
     */
    public function renderWidget (S $objW, A $objWA = NULL) {
        // CALL the __parent ()
        parent::renderWidget ($objW, $objWA);

        // Switch
        switch ($objW) {
            case 'widgetOAuthLogIn':
                // Set
                if ($_GET
                ->offsetExists (_T ('Provider'))) {
                    // Switch
                    switch ($_GET
                    ->offsetGet (_T ('Provider'))) {
                        // Yahoo
                        case _T ('Yahoo'):
                            // Set
                            $objOAuthProvider =
                            self::OAUTH_YAHOO;
                            break;

                        // Google
                        case _T ('Google'):
                            // Set
                            $objOAuthProvider =
                            self::OAUTH_GOOGLE;
                            break;

                        // My Open Id
                        case _T ('MyOpenId'):
                            // Set
                            $objOAuthProvider =
                            self::OAUTH_MYOPENID;
                    }
                } else {
                    // Set
                    $objOAuthProvider = self::OAUTH_YAHOO;
                }

                // Redirect
                Header::setKey (_S (Architecture
                ::pathTo (Architecture::getHost (), PLUGIN_DIR,
          'php_openidentity', 'worker.php?oauth_URL=%uId'))->doToken ('%uId',
                $objOAuthProvider), new S ('Location'));
                break;
        }
    }

    /**
     * (non-PHPdoc)
     * @see Commons::renderBackend()
     */
    public function renderBackend (S $objP) {
        // CALL the __parent ()
        parent::renderBackend ($objP);

        // Switch
        switch ($objP) {
            case 'manageStatus':
                // Set
                $this->objWidgets =
                self::getAdministration ()
                ->getWidget (NULL);

                // Go
                self::mapTp ($this, $objP,
                _S (__FUNCTION__));

                // Erase
                unset ($this->objWidgets);
                break;

            case 'manageUsers':
                // Check
                if ($_GET
                ->offsetExists (_T ('Do'))) {
                    // Switch
                    switch ($_GET
                    ->offsetGet (_T ('Do'))) {
                        // Add
                        case _T ('Add'):
                            $this->renderForm (new
                            S ('userCreate'));
                            break;

                        // Edit
                        case _T ('Edit'):
                            $this->renderForm (new
                            S ('userEdit'));
                            break;

                        // Erase
                        case _T ('Erase'):
                            $this->renderForm (new
                            S ('userErase'));
                            break;

                        // Properties
                        case _T ('Properties'):
                            $this->renderBackend (new
                            S ('manageProperties'));
                            break;
                    }
                } else {
                    // Condition
                    $objCondition = new S ('AS t1 INNER JOIN %objGroup AS t2
                    ON t1.%objUserUGId = t2.%objGroupId');

                    // Maps
                    $objMaps = new A (Array (self::$objUserId->makeCopyObject ()
                    ->prependString (_DOT)->prependString ('t1'),
                    self::$objUserUName,
                    self::$objUserEML,
                    self::$objGroupName->makeCopyObject ()
                    ->prependString (_DOT)->prependString ('t2'),
                    self::$objUserLName,
                    self::$objUserFName,
                    self::$objUserRegOn->makeCopyObject ()
                    ->prependString ('DATE_FORMAT(FROM_UNIXTIME(')
                    ->appendString ('), "%Y/%m/%d %T")')));

                    // Output
                    $this->outputAsJson (self::$objUser,
                    $objCondition, $objMaps);

                    // Go
                    self::mapTp ($this, $objP,
                    _S (__FUNCTION__));
                }
                // BK;
                break;

            case 'manageGroupProperties':
                // Check
                if ($_GET
                ->offsetExists (_T ('Do Properties'))) {
                    // Switch
                    switch ($_GET
                    ->offsetGet (_T ('Do Properties'))) {
                        // Add
                        case _T ('Add'):
                            $this->renderForm (new
                            S ('propertyGroupCreate'));
                            break;

                        // Edit
                        case _T ('Edit'):
                            $this->renderForm (new
                            S ('propertyGroupEdit'));
                            break;

                        // Erase
                        case _T ('Erase'):
                            $this->renderForm (new
                            S ('propertyGroupErase'));
                            break;
                    }
                } else {
                    // Condition
                    $objCondition = new S;

                    // Maps
                    $objMaps = new A (Array (self::$objGroupPropertyId,
                    self::$objGroupPropertyKey,
                    self::$objGroupPropertyVar,
                    self::$objGroupPropertyPublished->makeCopyObject ()
                    ->prependString ('DATE_FORMAT(FROM_UNIXTIME(')
                    ->appendString ('), "%Y/%m/%d %T")'),
                    self::$objGroupPropertyUpdated->makeCopyObject ()
                    ->prependString ('DATE_FORMAT(FROM_UNIXTIME(')
                    ->appendString ('), "%Y/%m/%d %T")')));

                    // Output
                    $this->outputAsJson (self::$objGroupProperty,
                    $objCondition, $objMaps);

                    // Go
                    self::mapTp ($this, $objP,
                    _S (__FUNCTION__));
                }
                // BK;
                break;

            case 'manageGroups':
                // Check
                if ($_GET
                ->offsetExists (_T ('Do'))) {
                    // Switch
                    switch ($_GET
                    ->offsetGet (_T ('Do'))) {
                        // Add
                        case _T ('Add'):
                            $this->renderForm (new
                            S ('groupCreate'));
                            break;

                        // Edit
                        case _T ('Edit'):
                            $this->renderForm (new
                            S ('groupEdit'));
                            break;

                        // Erase
                        case _T ('Erase'):
                            $this->renderForm (new
                            S ('groupErase'));
                            break;

                        // Move
                        case _T ('Move'):
                            $this->renderForm (new
                            S ('groupMove'));
                            break;

                        // Properties
                        case _T ('Properties'):
                            $this->renderBackend (new
                            S ('manageGroupProperties'));
                            break;
                    }
                } else {
                    // Condition
                    $objCondition = new S;

                    // Maps
                    $objMaps = new A (Array (self::$objGroupId,
                    new S ('lft'), self::$objGroupName));

                    // Tree
                    $objTree = self::$objMPTT->mpttGetTree ();
                    $objOffset = self::$objGroupName;

                    // Pre-processors
                    $objFuncs = new A (Array ((string)
                    self::$objGroupName
                    => function ($objData) use ($objTree, $objOffset) {
                        // Return
                        foreach ($objTree as $objTK => $objTV) {
                            // Check
                            if ($objTV[$objOffset] == $objData) {
                                // Return
                                return $objData
                                ->prependString (str_repeat (Hierarchy::PADDING,
                                (int) (string) $objTV['depth']));
                            }
                        }
                    }));

                    // Output
                    $this->outputAsJson (self::$objGroup,
                    $objCondition, $objMaps, $objFuncs,
                    self::$objMPTT);

                    // Go
                    self::mapTp ($this, $objP,
                    _S (__FUNCTION__));
                }
                // BK;
                break;

            case 'manageZones':
                // Check
                if ($_GET
                ->offsetExists (_T ('Do'))) {
                    // Switch
                    switch ($_GET
                    ->offsetGet (_T ('Do'))) {
                        // Add
                        case _T ('Add'):
                            $this->renderForm (new
                            S ('zoneCreate'));
                            break;

                        // Edit
                        case _T ('Edit'):
                            $this->renderForm (new
                            S ('zoneEdit'));
                            break;

                        // Erase
                        case _T ('Erase'):
                            $this->renderForm (new
                            S ('zoneErase'));
                            break;
                    }
                } else {
                    // Condition
                    $objCondition = new S;

                    // Maps
                    $objMaps = new A (Array (self::$objZoneId,
                    new S ('lft'), self::$objZoneName));

                    // Tree
                    $objTree = self::$objMPTTForZone->mpttGetTree ();
                    $objOffset = self::$objZoneName;

                    // Pre-processors
                    $objFuncs = new A (Array ((string) self::$objZoneName
                    => function ($objData) use ($objTree, $objOffset) {
                        // Return
                        foreach ($objTree as $objTK => $objTV) {
                            // Check
                            if ($objTV[$objOffset] == $objData) {
                                // Return
                                return $objData
                                ->prependString (str_repeat (Hierarchy::PADDING,
                                (int) (string) $objTV['depth']));
                            }
                        }
                    }));

                    // Output
                    $this->outputAsJson (self::$objZone,
                    $objCondition, $objMaps, $objFuncs,
                    self::$objMPTTForZone);

                    // Go
                    self::mapTp ($this, $objP,
                    _S (__FUNCTION__));
                }
                // BK;
                break;

            case 'manageMappings':
                // Check
                if ($_GET
                ->offsetExists (_T ('Do'))) {
                    // Switch
                    switch ($_GET
                    ->offsetGet (_T ('Do'))) {
                        // Add
                        case _T ('Add'):
                            $this->renderForm (new
                            S ('zoneMappingCreateForGroups'));
                            break;

                        // Edit
                        case _T ('Edit'):
                            $this->renderForm (new
                            S ('zoneMappingEdit'));
                            break;

                        // Erase
                        case _T ('Erase'):
                            $this->renderForm (new
                            S ('zoneMappingErase'));
                            break;
                    }
                } else {
                    // Condition
                    $objCondition = new S ('AS t1 INNER JOIN %objGroup AS t2
                    ON t1.%objMappingUGId = t2.%objGroupId INNER JOIN %objZone AS t3 ON
                    t1.%objMappingZId = t3.%objZoneId WHERE %objMappingIUG = "Y"');

                    // Maps
                    $objMaps = new A (Array (self::$objMappingId->makeCopyObject ()
                    ->prependString (_DOT)->prependString ('t1'),
                    self::$objGroupName->makeCopyObject ()
                    ->prependString (_DOT)->prependString ('t2')->appendString (' AS group_name'),
                    self::$objZoneName->makeCopyObject ()
                    ->prependString (_DOT)->prependString ('t3'),
                    self::$objMappingAorD));

                    // Pre-processors
                    $objFuncs = new A (Array ((string) self::$objMappingAorD
                    => function ($objData) {
                        // Return
                        return $objData == 'A' ?
                        _T ('Allowed') : _T ('Denied');
                    }));

                    // Output
                    $this->outputAsJson (self::$objMapping,
                    $objCondition, $objMaps, $objFuncs);

                    // Go
                    self::mapTp ($this, $objP,
                    _S (__FUNCTION__));
                }
                // BK;
                break;

            case 'manageMappingsForUsers':
                // Check
                if ($_GET
                ->offsetExists (_T ('Do'))) {
                    // Switch
                    switch ($_GET
                    ->offsetGet (_T ('Do'))) {
                        // Add
                        case _T ('Add'):
                            $this->renderForm (new
                            S ('zoneMappingCreateForUsers'));
                            break;

                            // Edit
                        case _T ('Edit'):
                            $this->renderForm (new
                            S ('zoneMappingEdit'));
                            break;

                            // Erase
                        case _T ('Erase'):
                            $this->renderForm (new
                            S ('zoneMappingErase'));
                            break;
                    }
                } else {
                    // Condition
                    $objCondition = new S ('AS t1 INNER JOIN %objUser AS t2
                    ON t1.%objMappingUGId = t2.%objUserId INNER JOIN %objZone AS t3 ON
                    t1.%objMappingZId = t3.%objZoneId WHERE %objMappingIUG = "N"');

                    // Maps
                    $objMaps = new A (Array (self::$objMappingId->makeCopyObject ()
                    ->prependString (_DOT)->prependString ('t1'),
                    self::$objUserUName->makeCopyObject ()
                    ->prependString (_DOT)->prependString ('t2'),
                    self::$objZoneName->makeCopyObject ()
                    ->prependString (_DOT)->prependString ('t3'),
                    self::$objMappingAorD));

                    // Pre-processors
                    $objFuncs = new A (Array ((string) self::$objMappingAorD
                    => function ($objData) {
                        // Return
                        return $objData == 'A' ?
                        _T ('Allowed') : _T ('Denied');
                    }));

                    // Output
                    $this->outputAsJson (self::$objMapping,
                    $objCondition, $objMaps, $objFuncs);

                    // Go
                    self::mapTp ($this, $objP,
                    _S (__FUNCTION__));
                }
                // BK;
                break;
        }
    }

    /**
     * (non-PHPdoc)
     * @see Commons::renderForm()
     */
    public function renderForm (S $objF, A $objFA = NULL) {
        // Make them defaults
        if ($objFA == NULL) $objFA = new A;

        // Switch
        switch ($objF) {
            default:
                // CALL the __parent ()
                parent::renderForm ($objF, $objFA);
                break;

            case 'adminLoginScreen':
                // Add a <title> tag, saying what we need;
                $this->manageTTL (_T ('Not authenticated!'));

                // Check
                if ($this->checkPOST ()
                ->toBoolean () == TRUE) {
                    // Check
                    if ($this->doSignIn ($this->getPOST (self::$objUserUName),
                    $this->getPOST (self::$objUserUPass))->toBoolean () == TRUE) {
                        // OK
                        Header::setKey (Location::rewriteTo (new A (Array (_T ('P'))),
                        new A (Array (_T ('Status')))), new S ('Location'));
                    } else {
                        // Error
                        $this->setErrorOnInput (self::$objUserUName,
                        _T ('Access denied!'));
                    }
                }

                // Form
                $this->setName ($objF)
                ->setFieldset (_T ('Not authenticated'))
                ->setInputType (new S ('submit'))
                ->setName (new S ('submit'))
                ->setValue (_T ('Go'))
                ->setInputInfo ($this->getHELP ($objF))
                ->setInputType (new S ('text'))
                ->setName (self::$objUserUName)
                ->setLabel (_T ('Username'))
                ->setInputType (new S ('password'))
                ->setName (self::$objUserUPass)
                ->setLabel (_T ('Password'))
                ->setFormEndAndExecute (new B (TRUE));
                break;

            case 'userCreate':
                // Set some predefines
                $objURLToGoBack = Location::rewriteTo (new A (Array (_T ('Do'))));

                // Check if both password fields have been entered correctly;
                if ($this->checkPOST (self::$objUserUName)
                ->toBoolean () == TRUE) {
                    // Check password;
                    if ($this->getPOST (self::$objUserUPass) != $this
                    ->getPOST (new S ('confirmation_password'))) {
                        // Check password mismatch
                        $this->setErrorOnInput (self::$objUserUPass,
                        _T ('Must match!'));
                    }
                }

                // City
                if ($this->checkPOST (self::$objUserCity)
                ->toBoolean () == TRUE) {
                    // Check city exists!
                    if ($this->getSettings ()->checkCityNameIsUnique ($this
                    ->getPOST (self::$objUserCity))
                    ->toBoolean () == TRUE) {
                        // Error
                        $this->setErrorOnInput (self::$objUserCity,
                        _T ('City does not exist in our database!'));
                    }
                }

                // If nickname is empty
                if ($this->checkPOST (self::$objUserUName)
                ->toBoolean () == TRUE) {
                    // If
                    if ($this->getPOST (self::$objUserUNick)
                    ->toLength ()->toInt () == 0) {
                        // Set
                        $this->setPOST (self::$objUserUNick,
                        $this->getPOST (self::$objUserUName));
                    }
                }

                // Settings
                $this->getSettings ()->renderForm (new S ('citySuggest'));

                // Form
                $this->setFieldset (_T ('Add'))
                ->setTableName (self::$objUser)
                ->setUpdateField (self::$objUserId)
                ->setUploadDirectory (Architecture::pathTo ($this->getObjectAncestry ()->toLower (), 'avatars'))
                ->setUploadImageResize (new A (Array (32 => 32, 64 => 64, 128 => 128)))
                ->setExtraUpdateData (self::$objUserRegOn, new S ((string) time ()))
                ->setRedirect ($objURLToGoBack)
                ->setName ($objF)
                ->setInputType (new S ('text'))
                ->setName (self::$objUserUNick)
                ->setLabel (_T ('Nickname'))
                ->setCheckForEmpty (new B (TRUE))
                ->setCheckForUnique (new B (TRUE))
                ->setInputInfo ($this->getHELP ($objF))
                ->setInputType (new S ('text'))
                ->setName (self::$objUserUName)
                ->setLabel (_T ('Username'))
                ->setCheckForEmpty (new B (TRUE))
                ->setCheckForUnique (new B (TRUE))
                ->setJSRegExpReplace (new S ('[^a-zA-Z0-9\@\ \.\-]'))
                ->setInputType (new S ('password'))
                ->setName (self::$objUserUPass)
                ->setLabel (_T ('Password'))
                ->setCheckForEmpty (new B (TRUE))
                ->setInputType (new S ('password'))
                ->setName (new S ('confirmation_password'))
                ->setLabel (_T ('Confirm'))
                ->setCheckForEmpty (new B (TRUE))
                ->setInputType (new S ('text'))
                ->setName (self::$objUserEML)
                ->setJSRegExpReplace (new S ('[^a-zA-Z0-9\.\@\_\-]'))
                ->setLabel (new S (_T ('Email')))
                ->setCheckForEmpty (new B (TRUE))
                ->setCheckForUnique (new B (TRUE))
                ->setInputType (new S ('text'))
                ->setName (self::$objUserPhone)
                ->setLabel (_T ('Phone'))
                ->setJSRegExpReplace (new S ('[^0-9\.\-]'))
                ->setInputType (new S ('text'))
                ->setName (self::$objUserLName)
                ->setLabel (_T ('Last name'))
                ->setCheckForEmpty (new B (TRUE))
                ->setInputType (new S ('text'))
                ->setName (self::$objUserFName)
                ->setLabel (_T ('First name'))
                ->setCheckForEmpty (new B (TRUE))
                ->setInputType (new S ('select'))
                ->setName (self::$objUserCountry)
                ->setLabel (_T ('Country'));

                // Get the COUNTRIES;
                foreach ($this->getSettings ()
                ->getCountries () as $objK => $objV) {
                    // Set
                    $this->setInputType (new S ('option'))
                    ->setName  ($objV[Settings::$objCountryIso])
                    ->setValue ($objV[Settings::$objCountryIso])
                    ->setLabel ($objV[Settings::$objCountryPrnt]);
                }

                // Continue
                $this->setInputType (new S ('text'))
                ->setName (self::$objUserCity)
                ->setLabel (_T ('City'))
                ->setClass (new S ('RA_AutoSuggest'))
                ->setInputType (new S ('select'))
                ->setName (self::$objUserUGId)
                ->setLabel (_T ('Group'));

                // Get the groups of the user;
                foreach (self::$objMPTT->mpttGetTree () as $objK => $objV) {
                    // Set
                    $this->setInputType (new S ('option'))
                    ->setName  ($objV['id'])
                    ->setValue ($objV['id'])
                    ->setLabel (new S (str_repeat ('--' .
                    _SP, (int) (string) $objV['depth']) . $objV['name']));
                }

                // Continue
                $this->setInputType (new S ('text'))
                ->setName (self::$objUserYM)
                ->setLabel (_T ('YM'))
                ->setInputType (new S ('text'))
                ->setName (self::$objUserMSN)
                ->setLabel (_T ('MSN'))
                ->setInputType (new S ('text'))
                ->setName (self::$objUserICQ)
                ->setLabel (_T ('ICQ'))
                ->setInputType (new S ('text'))
                ->setName (self::$objUserAOL)
                ->setLabel (_T ('AOL'))
                ->setInputType (new S ('file'))
                ->setFileController (new B (TRUE))
                ->setName (self::$objUserAvatar)
                ->setLabel (_T ('Avatar'))
                ->setInputType (new S ('textarea'))
                ->setName (self::$objUserDesc)
                ->setLabel (_T ('Description'))
                ->setInputType (new S ('textarea'))
                ->setName (self::$objUserSignature)
                ->setLabel (_T ('Signature'));

                // Encrypt
                if ($this->checkFormHasErrors ()
                ->toBoolean () == FALSE) {
                    // Check
                    if ($this->checkPOST ($objP = self::$objUserUPass)
                    ->toBoolean () == TRUE) {
                        // Set
                        $this->setPOST ($objP, $this->getPOST ($objP)->encryptIt (sha1 ($this->getPOST ($objP))));
                        $this->setPOST (new S ('confirmation_password'), $this->getPOST ($objP));
                    }
                }

                // Continue
                $this->setInputType (new S ('submit'))
                ->setName (new S ('submit'))
                ->setValue (_T ('Add'))
                ->setAccessKey (new S ('S'))
                ->setFormEndAndExecute (new B (TRUE));
                break;

            case 'userEdit':
                // Requirements
                $objURLToGoBack = Location::rewriteTo (new A (Array (_T ('Do'), _T ('Id'))));

                // Check
                if ($this->checkPOST ()->toBoolean () == TRUE) {
                    // Check password mismatch;
                    if ($this->getPOST (self::$objUserUPass) == $this
                    ->getUserById ($_GET[_T ('Id')], self::$objUserUPass) &&
                    $this->getPOST (new S ('confirmation_password')) ==
                    $this->getPOST (self::$objUserUPass)) {
                        // Unset
                        $this->unsetPOST (self::$objUserUPass);
                    } else {
                        // Check
                        if ($this->getPOST (self::$objUserUPass) != $this
                        ->getPOST (new S ('confirmation_password'))) {
                            // Set
                            $this->setErrorOnInput (self::$objUserUPass,
                            _T ('Passwords must match!'));
                        }
                    }
                }

                // City!
                if ($this->checkPOST (self::$objUserCity)
                ->toBoolean () == TRUE) {
                    // Check city exists!
                    if ($this->getSettings ()->checkCityNameIsUnique ($this
                    ->getPOST (self::$objUserCity))->toBoolean () == TRUE) {
                        // Error
                        $this->setErrorOnInput (self::$objUserCity,
                        _T ('City does not exist in our database!'));
                    }
                }

                // If nickname is empty
                if ($this->checkPOST (self::$objUserUName)
                ->toBoolean () == TRUE) {
                    // If
                    if ($this->getPOST (self::$objUserUNick)
                    ->toLength ()->toInt () == 0) {
                        // Set
                        $this->setPOST (self::$objUserUNick,
                        $this->getPOST (self::$objUserUName));
                    }
                }

                // Settings
                $this->getSettings ()->renderForm (new S ('citySuggest'));

                // Form
                $this->setFieldset (_T ('Edit: ')->appendString ($this
                ->getUserById ($_GET[_T ('Id')],
                self::$objUserUName)))
                ->setUpdateId ($_GET[_T ('Id')])
                ->setUploadDirectory (Architecture::pathTo ($this->getObjectAncestry ()->toLower (), 'avatars'))
                ->setUploadImageResize (new A (Array (32 => 32, 64 => 64, 128 => 128)))
                ->setTableName (self::$objUser)
                ->setUpdateField (self::$objUserId)
                ->setName ($objF)
                ->setRedirect ($objURLToGoBack);

                // Requirements
                $objUId = $_GET[_T ('Id')];

                // ONLY if we're NOT the BIG MAN;
                if ((int) (string) $this->getUserById ($_GET[_T ('Id')],
                self::$objUserId) != 1) {
                    // Set
                    $this->setInputType (new S ('select'))
                    ->setName (self::$objUserActivated)
                    ->setLabel (_T ('Activated'))
                    ->setYesNoOptions (new B (TRUE));
                }

                // Continue
                $this->setInputType (new S ('text'))
                ->setName (self::$objUserUNick)
                ->setLabel (_T ('Nickname'))
                ->setInputInfo ($this->getHELP ($objF))
                ->setCheckForEmpty (new B (TRUE))
                ->setCheckForUnique (new B (TRUE))
                ->setInputType (new S ('text'))
                ->setName (self::$objUserUName)
                ->setLabel (_T ('Username'))
                ->setJSRegExpReplace (new S ('[^a-zA-Z0-9\@\ \.\-]'))
                ->setCheckForEmpty (new B (TRUE))
                ->setCheckForUnique (new B (TRUE))
                ->setInputType (new S ('password'))
                ->setName (self::$objUserUPass)
                ->setValue ($this->getUserById ($_GET[_T ('Id')],
                self::$objUserUPass))
                ->setLabel (_T ('Password'))
                ->setCheckForEmpty (new B (TRUE))
                ->setInputType (new S ('password'))
                ->setName (new S ('confirmation_password'))
                ->setValue ($this->getUserById ($_GET[_T ('Id')],
                self::$objUserUPass))
                ->setLabel (_T ('Confirm'))
                ->setCheckForEmpty (new B (TRUE))
                ->setInputType (new S ('text'))
                ->setName (self::$objUserEML)
                ->setJSRegExpReplace (new S ('[^a-zA-Z0-9\.\@\_\-]'))
                ->setLabel (_T ('Email'))
                ->setCheckForEmpty (new B (TRUE))
                ->setCheckForUnique (new B (TRUE))
                ->setInputType (new S ('text'))
                ->setName (self::$objUserPhone)
                ->setLabel (_T ('Phone'))
                ->setJSRegExpReplace (new S ('[^0-9\.\-]'))
                ->setInputType (new S ('text'))
                ->setName (self::$objUserLName)
                ->setLabel (_T ('Last name'))
                ->setInputType (new S ('text'))
                ->setName (self::$objUserFName)
                ->setLabel (_T ('First name'))
                ->setInputType (new S ('select'))
                ->setName (self::$objUserCountry)
                ->setLabel (_T ('Country'));

                // Countries
                foreach ($this->getSettings ()->getCountries () as $objK => $objV) {
                    $this->setInputType (new S ('option'))
                    // Set
                    ->setName  ($objV[Settings::$objCountryIso])
                    ->setValue ($objV[Settings::$objCountryIso])
                    ->setLabel ($objV[Settings::$objCountryPrnt]);
                }

                // Continue
                $this->setInputType (new S ('text'))
                ->setName (self::$objUserCity)
                ->setLabel (_T ('City'))
                ->setClass (new S ('RA_AutoSuggest'));

                // ONLY if we're not the BIG MAN, can we change the group;
                if ((int) (string) $this->getUserById ($_GET[_T ('Id')],
                self::$objUserId) != 1) {
                    $this->setInputType (new S ('select'))
                    ->setName (self::$objUserUGId)
                    ->setLabel (new S (_T ('Group')));

                    // Foreach
                    foreach (self::$objMPTT->mpttGetTree () as $objK => $objV) {
                        // Set
                        $this->setInputType (new S ('option'))
                        ->setName  ($objV['id'])
                        ->setValue ($objV['id'])
                        ->setLabel (new S (str_repeat ('--' .
                        _SP, (int) (string) $objV['depth']) . $objV['name']));
                    }
                }

                // Continue
                $this->setInputType (new S ('text'))
                ->setName (self::$objUserYM)
                ->setLabel (_T ('YM'))
                ->setInputType (new S ('text'))
                ->setName (self::$objUserMSN)
                ->setLabel (_T ('MSN'))
                ->setInputType (new S ('text'))
                ->setName (self::$objUserICQ)
                ->setLabel (_T ('ICQ'))
                ->setInputType (new S ('text'))
                ->setName (self::$objUserAOL)
                ->setLabel (_T ('AOL'))
                ->setInputType (new S ('file'))
                ->setFileController (new B (TRUE))
                ->setName (self::$objUserAvatar)
                ->setLabel (_T ('Avatar'))
                ->setInputType (new S ('textarea'))
                ->setName (self::$objUserDesc)
                ->setLabel (_T ('Description'))
                ->setInputType (new S ('textarea'))
                ->setName (self::$objUserSignature)
                ->setLabel (_T ('Signature'));

                // Encrypt
                if ($this->checkFormHasErrors ()->toBoolean () == FALSE) {
                    // Check
                    if ($this->checkPOST ($objP = self::$objUserUPass)
                    ->toBoolean () == TRUE) {
                        // Set
                        $this->setPOST ($objP, $this->getPOST ($objP)->encryptIt (sha1 ($this->getPOST ($objP))));
                        $this->setPOST (new S ('confirmation_password'), $this->getPOST ($objP));
                    }
                }

                // Continue
                $this->setInputType (new S ('submit'))
                ->setName (new S ('submit'))
                ->setValue (_T ('Edit'))
                ->setAccessKey (new S ('S'))
                ->setFormEndAndExecute (new B (TRUE));
                break;

            case 'userErase':
                // The URL to go back too;
                $objURLToGoBack = Location::rewriteTo (new A (Array (_T ('Do'), _T ('Id'))));

                // Check
                if ((int) (string) $_GET[_T ('Id')] == 1) {
                    // Don't permit
                    self::getAdministration ()
                    ->setErrorMessage (_T ('Cannot erase administrator!'),
                    $objURLToGoBack);
                } else {
                    // Check
                    if ($this->_Q (_QS ('doSELECT')
                    ->doToken ('%what', new S ('*'))
                    ->doToken ('%table', self::$objMapping)
                    ->doToken ('%condition', new S ('WHERE %objMappingUGId = "%Id" AND %objMappingIUG = "N"'))
                    ->doToken ('%Id', $_GET[_T ('Id')]))->doCount ()->toInt () != 0) {
                        // Do not delete users that have specific mappings;
                        self::getAdministration ()
                        ->setErrorMessage (_T ('Cannot erase mapped users!'),
                        $objURLToGoBack);
                    } else {
                        // Go
                        $this->_Q (_QS ('doDELETE')
                        ->doToken ('%table', self::$objUser)
                        ->doToken ('%condition', new S ('%objUserId = "%Id"'))
                        ->doToken ('%Id', $_GET[_T ('Id')]));

                        // Redirect
                        Header::setKey ($objURLToGoBack, new S ('Location'));
                    }
                }
                // BK;
                break;

            case 'propertyGroupCreate':
                // Set the URL to go back;
                $objURLToGoBack = Location::rewriteTo (new A (Array (_T ('Do Properties'))));

                // Form
                $this->setFieldset (_T ('Add properties for: ')
                ->appendString (Hierarchy::mpttRemoveUnique ($this
                ->getGroupById ($_GET[_T ('Id')],
                self::$objGroupName))))
                ->setTableName (self::$objGroupProperty)
                ->setUpdateField (self::$objGroupPropertyId)
                ->setExtraUpdateData (self::$objGroupPropertyGId, $_GET[_T ('Id')])
                ->setExtraUpdateData (self::$objGroupPropertyUpdated, new S ((string) time ()))
                ->setName ($objF)
                ->setRedirect ($objURLToGoBack)
                ->setInputType (new S ('hidden'))
                ->setName (static::$objGroupPropertyPublished)
                ->setInputType (new S ('text'))
                ->setName (new S ('HiddenDate_AutoUpdate'))
                ->setLabel (new S ('Published'))
                ->setReadOnly (new B (TRUE))
                ->setInputType (new S ('select'))
                ->setName (self::$objGroupPropertyKey)
                ->setLabel (_T ('Key'))
                ->setInputInfo ($this->getHELP ($objF));

                // Foreach
                foreach ($this->getReflection ()->getConstants () as $objK => $objV) {
                    // Check
                    if (_S ($objK)->findPos ('PROPERTY_GROUP_') instanceof I) {
                        // Set
                        $this->setInputType (new S ('option'))
                        ->setName  (new S ($objV))
                        ->setLabel (new S ($objV))
                        ->setValue (new S ($objV));
                    }
                }

                // Continue
                $this->setInputType (new S ('textarea'))
                ->setName (self::$objGroupPropertyVar)
                ->setLabel (_T ('Value'))
                ->setCheckForEmpty (new B (TRUE))
                ->setInputType (new S ('submit'))
                ->setValue (_T ('Add'))
                ->setAccessKey (new S ('S'))
                ->setFormEndAndExecute (new B (TRUE));
                break;

            case 'propertyGroupEdit':
                // Set the URL to go back;
                $objURLToGoBack = Location::rewriteTo (new A (Array (_T ('Do Properties'), _T ('Property Id'))));

                // Form
                $this->setFieldset (_T ('Edit properties for: ')
                ->appendString (Hierarchy::mpttRemoveUnique ($this
                ->getGroupById ($_GET[_T ('Id')],
                self::$objGroupName))))
                ->setTableName (self::$objGroupProperty)
                ->setUpdateId ($_GET[_T ('Property Id')])
                ->setUpdateField (self::$objGroupPropertyId)
                ->setExtraUpdateData (self::$objGroupPropertyUpdated, new S ((string) time ()))
                ->setName ($objF)
                ->setRedirect ($objURLToGoBack)
                ->setInputType (new S ('hidden'))
                ->setName (static::$objGroupPropertyPublished)
                ->setInputType (new S ('text'))
                ->setName (new S ('HiddenDate_AutoUpdate'))
                ->setLabel (new S ('Published'))
                ->setReadOnly (new B (TRUE))
                ->setInputType (new S ('select'))
                ->setName (self::$objGroupPropertyKey)
                ->setLabel (_T ('Key'))
                ->setInputInfo ($this->getHELP ($objF));

                // Foreach
                foreach ($this->getReflection ()->getConstants () as $objK => $objV) {
                    // Check
                    if (_S ($objK)->findPos ('PROPERTY_GROUP_') instanceof I) {
                        // Set
                        $this->setInputType (new S ('option'))
                        ->setName  (new S ($objV))
                        ->setLabel (new S ($objV))
                        ->setValue (new S ($objV));
                    }
                }

                // Continue
                $this->setInputType (new S ('textarea'))
                ->setName (self::$objGroupPropertyVar)
                ->setLabel (_T ('Value'))
                ->setCheckForEmpty (new B (TRUE))
                ->setInputType (new S ('submit'))
                ->setValue (_T ('Edit'))
                ->setAccessKey (new S ('S'))
                ->setFormEndAndExecute (new B (TRUE));
                break;

            case 'propertyGroupErase':
                // Set the URL to go back too;
                $objURLToGoBack = Location::rewriteTo (new A (Array (_T ('Do Properties'), _T ('Property Id'))));

                // Do erase it
                $this->_Q (_QS ('doDELETE')
                ->doToken ('%table', self::$objGroupProperty)
                ->doToken ('%condition', new S ('%objGroupPropertyId = "%Id"'))
                ->doToken ('%Id', $_GET[_T ('Property Id')]));

                // Do a redirect, and get the user back where he belongs;
                Header::setKey ($objURLToGoBack, new S ('Location'));
                break;

            case 'groupCreate':
                // The URL to go back too;
                $objURLToGoBack = Location::rewriteTo (new A (Array (_T ('Do'))));

                // Check
                if ($this->checkPOST (new S ('submit_add_group'))
                ->toBoolean () == TRUE) {
                    // Set some requirements;
                    $objFormHappened = new B (FALSE);
                    if ($this->getPOST (new S ('group'))
                    ->toLength ()->toInt () == 0) {
                        // Set
                        $this->setErrorOnInput (new S ('group'),
                        _T ('Group name cannot be empty!'));

                        // Set to memory;
                        $objFormHappened->switchType ();
                    } else {
                        if (self::$objMPTT->mpttCheckIfNodeExists ($this->getPOST (new S ('group')))->toBoolean () == TRUE) {
                            // Check to see if the group exists, and tell the user the group exists;
                            $this->setErrorOnInput (new S ('group'),
                            _T ('Group already exists!'));

                            // Set to memory;
                            $objFormHappened->switchType ();
                        }
                    }

                    // Check
                    if ($objFormHappened->toBoolean () == FALSE) {
                        // Remember if we should add it as a brother or child;
                        $objAddNodeAS = NULL;
                        // Switch
                        switch ($this->getPOST (new S ('group_as_what'))) {
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
                        self::$objMPTT->mpttAddNode ($this->getPOST (new S ('group')),
                        $this->getPOST (new S ('group_parent_or_bro')), $objAddNodeAS);

                        // Do a redirect back;
                        Header::setKey ($objURLToGoBack, new S ('Location'));
                    }
                }

                // Form
                $this->setFieldset (_T ('Add'))
                ->setName (new S ($objF))
                ->setInputType (new S ('text'))
                ->setName (new S ('group'))
                ->setLabel (_T ('Group'))
                ->setJSRegExpReplace (new S ('[^a-zA-Z0-9 -]'))
                ->setInputInfo ($this->getHELP ($objF))
                ->setInputType (new S ('select'))
                ->setName (new S ('group_as_what'))
                ->setLabel (_T ('As a'))
                ->setInputType (new S ('option'))
                ->setName (new S ('as_child'))
                ->setLabel (_T ('first child'))
                ->setInputType (new S ('option'))
                ->setName (new S ('as_child'))
                ->setLabel (_T ('last child'))
                ->setInputType (new S ('option'))
                ->setName (new S ('as_brother'))
                ->setLabel (_T ('previous brother'))
                ->setInputType (new S ('option'))
                ->setName (new S ('as_brother'))
                ->setLabel (_T ('next brother'))
                ->setInputType (new S ('select'))
                ->setName (new S ('group_parent_or_bro'))
                ->setLabel (_T ('Of group'));

                // Do a foreach on the already existing groups;
                foreach (self::$objMPTT->mpttGetTree () as $objK => $objV) {
                    $this->setInputType (new S ('option'))->setName ($objV['name'])->setValue ($objV['name'])
                    ->setLabel (new S (str_repeat ('--' . _SP, (int) (string) $objV['depth']) . $objV['name']));
                }

                // Execute the form;
                $this->setInputType (new S ('submit'))
                ->setName (new S ('submit_add_group'))
                ->setValue (_T ('Add'))
                ->setAccessKey (new S ('S'))
                ->setFormEndAndExecute (new B (TRUE));
                break;

            case 'groupEdit':
                // The URL to go back too;
                $objURLToGoBack = Location::rewriteTo (new A (Array (_T ('Do'), _T ('Id'))));

                // Check
                if ($this
                ->checkPOST (self::$objGroupName)
                ->toBoolean () == TRUE) {
                    // Set
                    $objFormHappened = new B (FALSE);

                    // Check
                    if ($this
                    ->getPOST (self::$objGroupName)
                    ->toLength ()->toInt () == 0) {
                        // Set error
                        $this->setErrorOnInput (self::$objGroupName,
                        _T ('Group name cannot be empty!'));

                        // Set to memory
                        $objFormHappened->switchType ();
                    } else {
                        // Check
                        if (self::$objMPTT
                        ->mpttCheckIfNodeExists ($this
                        ->getPOST (self::$objGroupName))
                        ->toBoolean () == TRUE) {
                            // Set error
                            $this->setErrorOnInput (self::$objGroupName,
                            _T ('Group already exists!'));

                            // Set to memory;
                            $objFormHappened->switchType ();
                        }
                    }
                } else {
                    // Set some requirements;
                    $objFormHappened = new B (FALSE);
                }


                // Form
                $this->setTableName (self::$objGroup)
                ->setUpdateField (self::$objGroupId)
                ->setUpdateId ($_GET[_T ('Id')]);
                if ($this->checkPOST (self::$objGroupName)->toBoolean () == TRUE &&
                $objFormHappened->toBoolean () == FALSE) {
                    // Set the URL
                    $this->setExtraUpdateData (self::$objGroupSEO,
                    Location::getFrom ($this->getPOST (self::$objGroupName)))
                    ->setRedirect ($objURLToGoBack);
                }

                // Continue
                $this->setFieldset (_T ('Edit: ')->appendString ($this
                ->getGroupById ($_GET[_T ('Id')],
                self::$objGroupName)))
                ->setName ($objF)
                ->setInputType (new S ('text'))
                ->setInputInfo ($this->getHELP ($objF))
                ->setName (self::$objGroupName)
                ->setLabel (_T ('Group'))
                ->setJSRegExpReplace (new S ('[^a-zA-Z0-9 -]'))
                ->setInputType (new S ('submit'))
                ->setName (new S ('submit'))
                ->setValue (_T ('Edit'))
                ->setAccessKey (new S ('S'))
                ->setFormEndAndExecute (new B (TRUE));
                break;

            case 'groupErase':
                // The URL to go back too;
                $objNodeHasKids = new B (FALSE);
                $objURLToGoBack = Location::rewriteTo (new A (Array (_T ('Do'), _T ('Id'))));

                // Check if it's administrator group;
                if ((int) (string) $_GET[_T ('Id')] == 1) {
                    // Show the ERRORs;
                    self::getAdministration ()->setErrorMessage (_T ('Cannot erase administrator group!'),
                    $objURLToGoBack);
                } else {
                    // Check to see if there are any zone mappings;
                    if ($this->_Q (_QS ('doSELECT')
                    ->doToken ('%what', new S ('*'))->doToken ('%table', self::$objMapping)
                    ->doToken ('%condition', new S ('WHERE %objMappingUGId = "%Id"
                    AND %objMappingIUG = "Y"'))->doToken ('%Id', $_GET[_T ('Id')]))
                    ->doCount ()->toInt () != 0) {
                        // Do not delete groups with users in them;
                        self::getAdministration ()->setErrorMessage (_T ('Cannot erase mapped groups!'),
                        $objURLToGoBack);
                    } else {
                        // Do erase the group node from the table;
                        self::$objMPTT->mpttRemoveNode ($this->getGroupById ($_GET[_T ('Id')],
                        self::$objGroupName));

                        // Redirect back;
                        Header::setKey ($objURLToGoBack, new S ('Location'));
                    }
                }
                // BK;
                break;

            case 'groupMove':
                // Requirements
                $objURLToGoBack = Location::rewriteTo (new A (Array (_T ('Do'),
                _T ('To'), _T ('Id'), _T ('Type'))));

                // Get names, as they are unique;
                $objThatIsMoved = $this->getGroupById ($_GET[_T ('Id')], self::$objGroupName);
                $objWhereToMove = $this->getGroupById ($_GET[_T ('To')], self::$objGroupName);

                // Get the node subtree, that's move, make sure the node we move to ain't a child;
                $objMovedNodeSubTree = self::$objMPTT->mpttGetTree ($objThatIsMoved);

                // Memorize;
                $objIsChild = new B (FALSE);
                foreach ($objMovedNodeSubTree as $objK => $objV) {
                    if ($objV[self::$objMPTT->objNameOfNode] == $objWhereToMove) {
                        $objIsChild->switchType ();
                    }
                }

                // Check if it's a child or not;
                if ($objIsChild->toBoolean () == TRUE) {
                    // Set an error message;
                    self::getAdministration ()->setErrorMessage (_T ('Cannot move parent group to a child of it!'),
                    $objURLToGoBack);
                } else {
                    // Move nodes;
                    self::$objMPTT->mpttMoveNode ($objThatIsMoved,
                    $objWhereToMove, $_GET[_T ('Type')]);
                    Header::setKey ($objURLToGoBack, new S ('Location'));
                }
                // BK;
                break;

            case 'zoneCreate':
                // The URL to go back too;
                $objURLToGoBack = Location::rewriteTo (new A (Array (_T ('Do'))));

                // Check
                if ($this->checkPOST ()->toBoolean () == TRUE) {
                    # Check that the zone name is not empty!
                    if ($this->getPOST (self::$objZoneName)->toLength ()->toInt () == 0)
                    $this->setErrorOnInput (self::$objZoneName,
                    _T ('Zone name cannot be empty!'));
                }

                // Form
                $this->setFieldset (_T ('Add'))
                ->setTableName (self::$objZone)
                ->setUpdateField (self::$objZoneId)
                ->setName ($objF)
                ->setRedirect ($objURLToGoBack)
                ->setInputType (new S ('text'))
                ->setName (self::$objZoneName)
                ->setInputInfo ($this->getHELP ($objF))
                ->setLabel (_T ('Zone'))
                ->setInputType (new S ('text'))
                ->setName (self::$objZonePrice)
                ->setLabel (_T ('Price'))
                ->setInputType (new S ('textarea'))
                ->setName (self::$objZoneDesc)
                ->setLabel (_T ('Description'))
                ->setTinyMCETextarea (new B (TRUE))
                ->setInputType (new S ('submit'))
                ->setValue (_T ('Add'))
                ->setAccessKey (new S ('S'))
                ->setFormEndAndExecute (new B (TRUE));
                break;

            case 'zoneEdit':
                // Requirements
                $objURLToGoBack = Location::rewriteTo (new A (Array (_T ('Do'), _T ('Id'))));

                // Check
                if ($this->checkPOST ()->toBoolean () == TRUE) {
                    if ($this->checkPOST (self::$objZoneName)->toBoolean () == TRUE) {
                        // Check that the zone name is not empty!
                        if ($this->getPOST (self::$objZoneName)->toLength ()->toInt () == 0)
                        $this->setErrorOnInput (self::$objZoneName,
                        _T ('Zone name cannot be empty!'));
                    }
                }

                // Add some restrictions
                if ($this->_Q (_QS ('doSELECT')
                ->doToken ('%what', new S ('*'))->doToken ('%table', self::$objMapping)
                ->doToken ('%condition', new S ('WHERE %objMappingZId = "%Id"'))
                ->doToken ('%Id', $_GET[_T ('Id')]))->doCount ()->toInt () != 0) {
                    // The name should not be changed, due to mapping;
                    $objNameChangeDisabled = new B (TRUE);
                } else {
                    // The name can be changed;
                    $objNameChangeDisabled = new B (FALSE);
                }

                // Form
                $this->setFieldset (_T ('Edit: ')->appendString ($this
                ->getZoneById ($_GET[_T ('Id')],
                self::$objZoneName)))
                ->setTableName (self::$objZone)
                ->setUpdateId ($_GET[_T ('Id')])
                ->setUpdateField (self::$objZoneId)
                ->setName ($objF)
                ->setRedirect ($objURLToGoBack)
                ->setInputType (new S ('text'))
                ->setName (self::$objZoneName)
                ->setInputInfo ($this->getHELP ($objF))
                ->setLabel (_T ('Zone'))
                ->setReadOnly ($objNameChangeDisabled)
                ->setInputType (new S ('text'))
                ->setName (self::$objZonePrice)
                ->setLabel (_T ('Price'))
                ->setInputType (new S ('textarea'))
                ->setName (self::$objZoneDesc)
                ->setLabel (_T ('Description'))
                ->setTinyMCETextarea (new B (TRUE))
                ->setInputType (new S ('submit'))
                ->setValue (_T ('Edit'))
                ->setAccessKey (new S ('S'))
                ->setFormEndAndExecute (new B (TRUE));
                break;

            case 'zoneErase':
                // The URL to go back too;
                $objURLToGoBack = Location::rewriteTo (new A (Array (_T ('Do'), _T ('Id'))));

                // Check to see if there are any zone mappings, for the current zone;
                $objSQLCondition = new S ('WHERE %s = %i');

                // Erase it;
                $this->_Q (_QS ('doDELETE')
                ->doToken ('%table', self::$objZone)
                ->doToken ('%condition', new S ('%objZoneId = "%Id"'))
                ->doToken ('%Id', $_GET[_T ('Id')]));

                // Redirect the user back;
                Header::setKey ($objURLToGoBack, new S ('Location'));
                break;

            case 'zoneMappingCreateForGroups':
                // The URL to go back too;
                $objURLToGoBack = Location::rewriteTo (new A (Array (_T ('Do'))));

                // Form
                $this->setFieldset (_T ('Add'))
                ->setTableName (self::$objMapping)
                ->setUpdateField (self::$objMappingId)
                ->setExtraUpdateData (self::$objMappingIUG, new S ('Y'))
                ->setName ($objF)
                ->setRedirect ($objURLToGoBack)
                ->setInputType (new S ('select'))
                ->setInputInfo ($this->getHELP ($objF))
                ->setName (self::$objMappingUGId)
                ->setLabel (_T ('Group'));

                // Get the groups;
                foreach (self::$objMPTT->mpttGetTree () as $objK => $objV) {
                    $this->setInputType (new S ('option'))
                    ->setName  ($objV[self::$objGroupId])
                    ->setValue ($objV[self::$objGroupId])
                    ->setLabel (new S (str_repeat ('--' . _SP, (int) (string) $objV['depth']) . $objV[self::$objGroupName]));
                }

                // Continue
                $this->setInputType (new S ('select'))
                ->setName (self::$objMappingZId)
                ->setLabel (_T ('Zone'));

                // Get the zones;
                foreach ($this->getZones (NULL) as $objK => $objV) {
                    $this->setInputType (new S ('option'))
                    ->setName  ($objV[self::$objZoneId])
                    ->setValue ($objV[self::$objZoneId])
                    ->setLabel (new S (str_repeat ('--' . _SP, (int) (string) $objV['depth']) . $objV[self::$objZoneName]));
                }

                // Continue
                $this->setInputType (new S ('select'))
                ->setName (self::$objMappingAorD)
                ->setLabel (_T ('Access'))
                ->setInputType (new S ('option'))
                ->setName (new S ('deny_or_allow_allow'))
                ->setValue (new S ('A'))
                ->setLabel (_T ('Allowed'))
                ->setInputType (new S ('option'))
                ->setName (new S ('deny_or_allow_deny'))
                ->setValue (new S ('D'))
                ->setLabel (_T ('Denied'))
                ->setInputType (new S ('submit'))
                ->setValue (_T ('Add'))
                ->setAccessKey (new S ('S'))
                ->setFormEndAndExecute (new B (TRUE));
                break;

            case 'zoneMappingCreateForUsers':
                // The URL to go back too;
                $objURLToGoBack = Location::rewriteTo (new A (Array (_T ('Do'))));

                // Form
                $this->setFieldset (_T ('Add'))
                ->setTableName (self::$objMapping)
                ->setUpdateField (self::$objMappingId)
                ->setExtraUpdateData (self::$objMappingIUG, new S ('N'))
                ->setName ($objF)
                ->setRedirect ($objURLToGoBack)
                ->setInputType (new S ('select'))
                ->setInputInfo ($this->getHELP ($objF))
                ->setName (self::$objMappingUGId)
                ->setLabel (_T ('User'));

                // Get the users;
                foreach ($this->getUsers () as $objK => $objV) {
                    $this->setInputType (new S ('option'))
                    ->setName  ($objV[self::$objUserId])
                    ->setValue ($objV[self::$objUserId])
                    ->setLabel ($objV[self::$objUserUName]);
                }

                // Continue
                $this->setInputType (new S ('select'))
                ->setName (self::$objMappingZId)
                ->setLabel (_T ('Zone'));

                // Get zones;
                foreach ($this->getZones (NULL) as $objK => $objV) {
                    $this->setInputType (new S ('option'))
                    ->setName ($objV[self::$objZoneId])
                    ->setValue ($objV[self::$objZoneId])
                    ->setLabel (new S (str_repeat ('--' . _SP, (int) (string)
                    $objV['depth']) . $objV[self::$objZoneName]));
                }

                // Continue
                $this->setInputType (new S ('select'))
                ->setName (self::$objMappingAorD)
                ->setLabel (_T ('Access'))
                ->setInputType (new S ('option'))
                ->setName (new S ('deny_or_allow_allow'))
                ->setValue (new S ('A'))
                ->setLabel (_T ('Allowed'))
                ->setInputType (new S ('option'))
                ->setName (new S ('deny_or_allow_deny'))
                ->setValue (new S ('D'))
                ->setLabel (_T ('Denied'))
                ->setInputType (new S ('submit'))
                ->setValue (_T ('Add'))
                ->setAccessKey (new S ('S'))
                ->setFormEndAndExecute (new B (TRUE));
                break;

            case 'zoneMappingEdit':
                // The URL to go back too;
                $objURLToGoBack = Location::rewriteTo (new A (Array (_T ('Do'), _T ('Id'))));

                // Form
                $this->setFieldset (_T ('Edit'))
                ->setTableName (self::$objMapping)
                ->setUpdateId ($_GET[_T ('Id')])
                ->setUpdateField (self::$objMappingId)
                ->setName ($objF)
                ->setRedirect ($objURLToGoBack)
                ->setInputType (new S ('submit'))
                ->setValue (_T ('Edit'))
                ->setInputInfo ($this->getHELP ($objF))
                ->setInputType (new S ('select'))
                ->setName (self::$objMappingAorD)
                ->setLabel (_T ('Access'))
                ->setInputType (new S ('option'))
                ->setName (new S ('deny_or_allow_allow'))
                ->setValue (new S ('A'))
                ->setLabel (_T ('Allowed'))
                ->setInputType (new S ('option'))
                ->setName (new S ('deny_or_allow_deny'))
                ->setValue (new S ('D'))
                ->setLabel (_T ('Denied'))
                ->setAccessKey (new S ('S'))
                ->setFormEndAndExecute (new B (TRUE));
                break;

            case 'zoneMappingErase':
                // The URL to go back too;
                $objURLToGoBack = Location::rewriteTo (new A (Array (_T ('Do'), _T ('Id'))));

                // Erase it;
                $this->_Q (_QS ('doDELETE')
                ->doToken ('%table', self::$objMapping)
                ->doToken ('%condition', new S ('%objMappingId = "%Id"'))
                ->doToken ('%Id', $_GET[_T ('Id')]));

                // Do a redirect, and get the user back where he belongs;
                Header::setKey ($objURLToGoBack, new S ('Location'));
                break;
        }
    }
}
?>
