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
 * Provides an interface to store CSS, JS and PHP (HTML) code via database and execute (EVAL) it as needed;
 *
 * @author Catalin Alexandru Zamfir <catalin.zamfir@raphpframework.ro>
 * @copyright Under the terms of the GNU General Public License v3
 * @version $Id: Codes.php 1 2012-10-26 08:27:37Z root $
 */
class Codes extends Commons {

    /* Items */
    public static $objItem;
    public static $objItemId;
    public static $objItemTitle;
    public static $objItemURL;
    public static $objItemCSS;
    public static $objItemJSS;
    public static $objItemPHP;
    public static $objItemTags;
    public static $objItemPublished;
    public static $objItemUpdated;
    public static $objItemAuthorId;
    public static $objItemCategoryId;

    /* Properties of items */
    public static $objProperty;
    public static $objPropertyId;
    public static $objPropertyAId;
    public static $objPropertyKey;
    public static $objPropertyVar;
    public static $objPropertyPublished;
    public static $objPropertyUpdated;

    /* Categories */
    public static $objCategory;
    public static $objCategoryId;
    public static $objCategoryName;
    public static $objCategoryURL;
    public static $objCategoryDate;

    /* Properties of categories */
    public static $objCategoryProperty;
    public static $objCategoryPropertyId;
    public static $objCategoryPropertyCId;
    public static $objCategoryPropertyKey;
    public static $objCategoryPropertyVar;
    public static $objCategoryPropertyPublished;
    public static $objCategoryPropertyUpdated;

    // CONSTRUCT
    public function __construct () {
        // Commons
        $this->tieInConfiguration ();

        // ACLs
        $this->setACL (new S ('Items'));
        $this->setACL (new S ('Categories'));
    }

    /**
     * (non-PHPdoc)
     * @see Commons::tieInAdministration()
     */
    public function tieInAdministration (Administration $objAdministration) {
        // Do a CALL to the parent;
        parent::tieInAdministration ($objAdministration);

        // Set ACLs;
        $objACL = $this->getACLs ();

        // Administration (menu)
        $objWP = new Path (Architecture
        ::pathTo ($this->getPathToAdmin (),
        $this->getConfigKey (new S ('Dashboard'))));
        self::getAdministration ()->setLink (_T ('Codes'), $objWP,
        $this->getHELP (_T ('Codes')));

        // ONLY: Items
        if (self::getAuthentication ()
        ->checkCurrentUserZoneACL ($objACL[0])
        ->toBoolean () == TRUE) {
            $objMT = new Path (Architecture
            ::pathTo ($this->getPathToAdmin (),
            $this->getConfigKey (new S ('Items'))));
            self::getAdministration ()->setSink (_T ('Codes'),
            $objMT, $this->getHELP (_T ('Codes')));
        }

        // ONLY: Categories
        if (self::getAuthentication ()
        ->checkCurrentUserZoneACL ($objACL[1])
        ->toBoolean () == TRUE) {
            $objMC = new Path (Architecture
            ::pathTo ($this->getPathToAdmin (),
            $this->getConfigKey (new S ('Categories'))));
            self::getAdministration ()->setSink (_T ('Categories'),
            $objMC, $this->getHELP (_T ('Categories')));
        }

        // Items
        $this->getAdministration ()->setWidget ($this
        ->getHELP (new S ('adminWidgetItems'))
        ->doToken ('%uId', $this->getItemCount ())
        ->doToken ('%gId', $this->getCategoryCount ()));
    }

    /**
     * (non-PHPdoc)
     * @see Commons::renderBackend()
     */
    public function renderBackend (S $objP) {
        // CALL the __parent ();
        parent::renderBackend ($objP);

        // Switch
        switch ($objP) {
            case 'manageItems':
                // Check
                if (isset ($_GET[_T ('Do')])) {
                    // Switch
                    switch ($_GET[_T ('Do')]) {
                        // Add
                        case _T ('Add'):
                            $this->renderForm (new
                            S ('itemCreate'));
                            break;

                        // Edit
                        case _T ('Edit'):
                            $this->renderForm (new
                            S ('itemEdit'));
                            break;

                        // Erase
                        case _T ('Erase'):
                            $this->renderForm (new
                            S ('itemErase'));
                            break;

                        // Properties
                        case _T ('Properties'):
                            $this->renderBackend (new
                            S ('manageProperties'));
                            break;

                        // Comments
                        case _T ('Comments'):
                            $this->renderBackend (new
                            S ('manageComments'));
                            break;
                    }
                } else {
                    // Condition
                    $objCondition = new S ('AS t1 INNER JOIN %objCategory AS t2
                    ON t1.%objItemCategoryId = t2.%objCategoryId');

                    // Maps
                    $objMaps = new A (Array (self::$objItemId->makeCopyObject ()
                    ->prependString (_DOT)->prependString ('t1'),
                    self::$objItemTitle,
                    self::$objItemTags,
                    self::$objCategoryName->makeCopyObject ()
                    ->prependString (_DOT)->prependString ('t2'),
                    self::$objItemPublished->makeCopyObject ()
                    ->prependString ('DATE_FORMAT(FROM_UNIXTIME(')
                    ->appendString ('), "%Y/%m/%d %T")'),
                    self::$objItemUpdated->makeCopyObject ()
                    ->prependString ('DATE_FORMAT(FROM_UNIXTIME(')
                    ->appendString ('), "%Y/%m/%d %T")')));

                    // Pre-processors
                    $objFuncs = new A (Array ((string)
                    self::$objCategoryName
                    => function ($objData) {
                        // Return
                        return Hierarchy::mpttRemoveUnique ($objData);
                    }));

                    // Output
                    $this->outputAsJson (self::$objItem,
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
     * @see Commons::renderWidget()
     */
    public function renderWidget (S $objW, A $objWA = NULL) {
        // CALL the __parent ()
        parent::renderWidget ($objW, $objWA);

        // HTML: Switch
        switch ($objW) {
            case 'executeCode':
                // Check
                if ($objWA->offsetExists ('Identifier')) {
                    // Set CSS
                    $this->objCSS = $this
                    ->getItemByTitle ($objWA
                    ->offsetGet ('Identifier'),
                    self::$objItemCSS);

                    // Set JSS
                    $this->objJSS = $this
                    ->getItemByTitle ($objWA
                    ->offsetGet ('Identifier'),
                    self::$objItemJSS);

                    // Set PHP
                    $this->objPHP = $this
                    ->getItemByTitle ($objWA
                    ->offsetGet ('Identifier'),
                    self::$objItemPHP);

                    // Go
                    self::mapTp ($this, $objP,
                    _S (__FUNCTION__));
                } else {
                    // Throw
                    throw new MethodNotMappedException;
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
        // CALL the __parent ();
        parent::renderForm ($objF, $objFA);

        // Defaults
        if ($objFA == NULL) $objFA = new A;

        // Switch
        switch ($objF) {
            case 'itemCreate':
                // The URL to go back too;
                $objURLToGoBack = Location::rewriteTo (new A (Array (_T ('Do'))));

                // Form
                $this->setName ($objF)
                ->setFieldset (_T ('Add'))
                ->setRedirect ($objURLToGoBack)
                ->setTableName (self::$objItem)
                ->setUpdateField (self::$objItemId);

                // Check
                if ($this->checkPOST (self::$objItemTitle)->toBoolean () == TRUE) {
                    $this->setExtraUpdateData (self::$objItemURL,
                    Location::getFrom ($this->getPOST (self::$objItemTitle)));
                }

                // ONLY if != BIG-MAN;
                if ((int) (string) self::getAuthentication ()
                ->getCurrentUser (Authentication::$objUserId) != 1) {
                    // Set
                    $this->setExtraUpdateData (self::$objItemAuthorId, self::getAuthentication ()
                    ->getCurrentUser (Authentication::$objUserId));
                }

                // Continue
                $this->setExtraUpdateData (self::$objItemUpdated, new S ((string) time ()))
                ->setInputType (new S ('text'))
                ->setName (self::$objItemTags)
                ->setLabel (_T ('Tags'))
                ->setInputInfo ($this->getHELP ($objF))
                ->setCheckForEmpty (new B (TRUE));

                // Check
                if ((int) (string) self::getAuthentication ()
                ->getCurrentUser (Authentication::$objUserId) == 1) {
                    $this->setInputType (new S ('select'))
                    ->setName (self::$objItemAuthorId)
                    ->setLabel (_T ('Author'));

                    // Categories
                    foreach (self::getAuthentication ()->getUsers () as $objK => $objV) {
                        $this->setInputType (new S ('option'))
                        ->setName  ($objV[Authentication::$objUserId])
                        ->setValue ($objV[Authentication::$objUserId])
                        ->setLabel ($objV[Authentication::$objUserUName]);
                    }
                }

                // Form
                $this->setInputType (new S ('select'))
                ->setLabel (_T ('Category'))
                ->setName (self::$objItemCategoryId);

                // Categories
                foreach (self::getHierarchy ()->mpttGetTree () as $objK => $objV) {
                    $this->setInputType (new S ('option'))
                    ->setName  ($objV[self::$objCategoryId])
                    ->setValue ($objV[self::$objCategoryId])
                    ->setLabel (new S (str_repeat ('--' . _SP, (int) (string)
                    $objV['depth']) . Hierarchy::mpttRemoveUnique
                    ($objV[self::$objCategoryName])));
                }

                // Continue
                $this->setInputType (new S ('hidden'))
                ->setName (static::$objPropertyPublished)
                ->setInputType (new S ('text'))
                ->setName (new S ('HiddenDate_AutoUpdate'))
                ->setLabel (new S ('Published'))
                ->setReadOnly (new B (TRUE))
                ->setInputType (new S ('text'))
                ->setName (self::$objItemTitle)
                ->setLabel (_T ('Identifier'))
                ->setCheckForEmpty (new B (TRUE))
                ->setCheckForUnique (new B (TRUE))
                ->setInputType (new S ('textarea'))
                ->setName (self::$objItemCSS)
                ->setLabel (_T ('Stylesheet'))
                ->setClass (new S ('CSS_Code_Editor'))
                ->setInputType (new S ('textarea'))
                ->setName (self::$objItemJSS)
                ->setLabel (_T ('JavaScript'))
                ->setClass (new S ('JSS_Code_Editor'))
                ->setInputType (new S ('textarea'))
                ->setName (self::$objItemPHP)
                ->setLabel (_T ('PHP'))
                ->setClass (new S ('PHP_Code_Editor'))
                ->setInputType (new S ('submit'))
                ->setValue (_T ('Add'))
                ->setAccessKey (new S ('S'))
                ->setFormEndAndExecute (new B (TRUE));
                break;

            case 'itemEdit':
                // The URL to go back too;
                $objURLToGoBack = Location::rewriteTo (new A (Array (_T ('Do'), _T ('Id'))));

                // Form
                $this->setFieldset (_T ('Edit: ')->appendString ($this
                ->getItemById ($_GET[_T ('Id')],
                self::$objItemTitle)))
                ->setRedirect ($objURLToGoBack)
                ->setTableName (self::$objItem)
                ->setUpdateId ($_GET[_T ('Id')])
                ->setUpdateField (self::$objItemId)
                ->setName ($objF)
                ->setExtraUpdateData (self::$objItemUpdated,
                new S ((string) time ()))
                ->setInputType (new S ('text'))
                ->setName (self::$objItemTags)
                ->setLabel (_T ('Tags'))
                ->setInputInfo ($this->getHELP ($objF))
                ->setCheckForEmpty (new B (TRUE));

                // Seo
                if ($this->checkPOST (self::$objItemTitle)->toBoolean () == TRUE) {
                    $this->setExtraUpdateData (self::$objItemURL,
                    Location::getFrom ($this->getPOST (self::$objItemTitle)));
                }

                // Check
                if ((int) (string) self::getAuthentication ()
                ->getCurrentUser (Authentication::$objUserId) == 1) {
                    $this->setInputType (new S ('select'))
                    ->setName (self::$objItemAuthorId)
                    ->setLabel (_T ('Author'));

                    // Users
                    foreach (self::getAuthentication ()->getUsers () as $objK => $objV) {
                        $this->setInputType (new S ('option'))
                        ->setName  ($objV[Authentication::$objUserId])
                        ->setValue ($objV[Authentication::$objUserId])
                        ->setLabel ($objV[Authentication::$objUserUName]);
                    }
                }

                // Form
                $this->setInputType (new S ('select'))
                ->setName (self::$objItemCategoryId)
                ->setLabel (_T ('Category'));

                // Categories
                foreach (self::getHierarchy ()->mpttGetTree () as $objK => $objV) {
                    $this->setInputType (new S ('option'))
                    ->setName  ($objV[self::$objCategoryId])
                    ->setValue ($objV[self::$objCategoryId])
                    ->setLabel (new S (str_repeat ('--' . _SP, (int) (string)
                    $objV['depth']) . Hierarchy::mpttRemoveUnique
                    ($objV[self::$objCategoryName])));
                }

                // Continue
                $this->setInputType (new S ('hidden'))
                ->setName (static::$objPropertyPublished)
                ->setInputType (new S ('text'))
                ->setName (new S ('HiddenDate_AutoUpdate'))
                ->setLabel (new S ('Published'))
                ->setReadOnly (new B (TRUE))
                ->setInputType (new S ('text'))
                ->setName (self::$objItemTitle)
                ->setLabel (_T ('Identifier'))
                ->setCheckForEmpty (new B (TRUE))
                ->setCheckForUnique (new B (TRUE))
                ->setInputType (new S ('textarea'))
                ->setName (self::$objItemCSS)
                ->setClass (new S ('CSS_Code_Editor'))
                ->setLabel (_T ('Stylesheet'))
                ->setInputType (new S ('textarea'))
                ->setName (self::$objItemJSS)
                ->setLabel (_T ('JavaScript'))
                ->setClass (new S ('JSS_Code_Editor'))
                ->setInputType (new S ('textarea'))
                ->setName (self::$objItemPHP)
                ->setLabel (_T ('PHP'))
                ->setClass (new S ('PHP_Code_Editor'))
                ->setInputType (new S ('submit'))
                ->setValue (_T ('Edit'))
                ->setAccessKey (new S ('S'))
                ->setFormEndAndExecute (new B (TRUE));
                break;

            case 'itemErase':
                // The URL to go back too;
                $objURLToGoBack = Location::rewriteTo (new A (Array (_T ('Do'), _T ('Id'))));

                // Erase
                $this->_Q (_QS ('doDELETE')
                ->doToken ('%table', self::$objItem)
                ->doToken ('%condition', new S ('%objItemId = "%Id"'))
                ->doToken ('%Id', $_GET[_T ('Id')]));

                // Redirect
                Header::setKey ($objURLToGoBack, new S ('Location'));
                break;
        }
    }
}
?>
