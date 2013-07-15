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
 * Provides an interface for texts management (pages, static or not);
 *
 * @author Catalin Alexandru Zamfir <catalin.zamfir@raphpframework.ro>
 * @copyright Under the terms of the GNU General Public License v3
 * @version $Id: Texts.php 1 2012-10-26 08:27:37Z root $
 */
class Texts extends Commons {

    /* Items */
    public static $objItem;
    public static $objItemId;
    public static $objItemTitle;
    public static $objItemURL;
    public static $objItemContent;
    public static $objItemTags;
    public static $objItemAuthorId;
    public static $objItemCategoryId;
    public static $objItemPublished;
    public static $objItemUpdated;

    /* Images of items */
    public static $objImage;
    public static $objImageId;
    public static $objImageAId;
    public static $objImageTags;
    public static $objImageTitle;
    public static $objImageURL;
    public static $objImageCaption;
    public static $objImagePublished;
    public static $objImageUpdated;

    /* Audios of items */
    public static $objAudio;
    public static $objAudioId;
    public static $objAudioAId;
    public static $objAudioTags;
    public static $objAudioTitle;
    public static $objAudioURL;
    public static $objAudioCaption;
    public static $objAudioPublished;
    public static $objAudioUpdated;

    /* Videos of items */
    public static $objVideo;
    public static $objVideoId;
    public static $objVideoAId;
    public static $objVideoTags;
    public static $objVideoTitle;
    public static $objVideoURL;
    public static $objVideoCaption;
    public static $objVideoPublished;
    public static $objVideoUpdated;

    /* Properties of items */
    public static $objProperty;
    public static $objPropertyId;
    public static $objPropertyAId;
    public static $objPropertyKey;
    public static $objPropertyVar;
    public static $objPropertyPublished;
    public static $objPropertyUpdated;

    /* Comments on items */
    public static $objComment;
    public static $objCommentId;
    public static $objCommentName;
    public static $objCommentAddress;
    public static $objCommentURL;
    public static $objCommentRUId;
    public static $objCommentComment;
    public static $objCommentApproved;
    public static $objCommentAId;
    public static $objCommentPublished;
    public static $objCommentUpdated;

    /* Attachments of items */
    public static $objAttachment;
    public static $objAttachmentId;
    public static $objAttachmentAId;
    public static $objAttachmentTags;
    public static $objAttachmentTitle;
    public static $objAttachmentURL;
    public static $objAttachmentCaption;
    public static $objAttachmentPublished;
    public static $objAttachmentUpdated;

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

        if (self::getHierarchy ()
        ->mpttCheckIfNodeExists (_T ('System'))
        ->toBoolean () == FALSE) {
            // Add'em
            self::getHierarchy ()->mpttAddNode (_T ('System'),
            $this->getCategories ()->offsetGet (0)
            ->offsetGet (self::$objCategoryName),
            new S ((string) Hierarchy::PREVIOUS_BROTHER));
        }
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
        self::getAdministration ()->setLink (_T ('Texts'),
        $objWP, $this->getHELP (_T ('Texts')));

        // ONLY: Items
        if (self::getAuthentication ()
        ->checkCurrentUserZoneACL ($objACL[0])
        ->toBoolean () == TRUE) {
            $objMT = new Path (Architecture
            ::pathTo ($this->getPathToAdmin (),
            $this->getConfigKey (new S ('Items'))));
            self::getAdministration ()->setSink (_T ('Texts'),
            $objMT, $this->getHELP (_T ('Texts')));
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

        // Ties
        self::getAuthentication ()
        ->tieInSystemTexts ($this);

        // Items
        $this->getAdministration ()->setWidget ($this
        ->getHELP (new S ('adminWidgetItems'))
        ->doToken ('%uId', $this->getItemCount ())
        ->doToken ('%gId', $this->getCategoryCount ()));
    }

    /**
     * Sets a predefined (System) text;
     *
     * @author Catalin Alexandru Zamfir <catalin.zamfir@raphpframework.ro>
     * @copyright Under the terms of the GNU General Public License v3
     * @version $Id: Texts.php 1 2012-10-26 08:27:37Z root $
     */
    public function setSystemText (S $objItemTitle, S $objItemContent) {
        // Check
        if ($this
        ->checkItemTitleIsUnique ($objItemTitle)
        ->toBoolean () == TRUE) {

            // Requirements
            $objCatId = $this->getCategoryByName (_T ('System'), self::$objCategoryId);
            $objURLId = Location::getFrom (CLONE $objItemTitle);

            // SQL Condition
            $objSQLCondition = new S ('%objItemTitle = "%tId",
            %objItemContent = "%cId",
            %objItemURL = "%sId",
            %objItemPublished = "%pId",
            %objItemUpdated = "%pId",
            %objItemAuthorId = "%aId",
            %objItemCategoryId = "%gId"');

            // Find
            $objAuthorId = self
            ::getAuthentication ()->getUsers ()->offsetGet (0)
            ->offsetGet (Authentication::$objUserId);

            // Set
            $this->_Q (_QS ('doINSERT')
            ->doToken ('%table', self::$objItem)
            ->doToken ('%condition', $objSQLCondition)
            ->doToken ('%tId', $objItemTitle)
            ->doToken ('%cId', $objItemContent)
            ->doToken ('%sId', $objURLId)
            ->doToken ('%pId', time ())
            ->doToken ('%gId', $objCatId)
            ->doToken ('%aId', $objAuthorId));
        }
    }

    /**
     * Gets a predefined (System) text;
     *
     * @author Catalin Alexandru Zamfir <catalin.zamfir@raphpframework.ro>
     * @copyright Under the terms of the GNU General Public License v3
     * @version $Id: Texts.php 1 2012-10-26 08:27:37Z root $
     */
    public function getSystemText (S $objItemTitle, S $objFieldToGet) {
        // Do return
        return $this->_Q (_QS ('doSELECT')
        ->doToken ('%what', new S ('*'))->doToken ('%table', self::$objItem)
        ->doToken ('%condition', new S  ('WHERE %objItemTitle = "%Id" LIMIT 1'))
        ->doToken ('%Id', $objItemTitle))->offsetGet (0)->offsetGet ($objFieldToGet);
    }

    /**
     * (non-PHPdoc)
     * @see Commons::getSitemap()
     */
    public function getSitemap () {
        // Set type
        Header::setKey (new S (Header
        ::CONTENT_TYPE_TEXT_PLAIN),
        new S ('Content-Type'));

        // Set
        $objSitemap = new A;

        // Foreach
        foreach ($this->getItems (new S ('ORDER BY
        %objItemPublished DESC')) as $objK => $objV) {
            // Check
            if ($this
            ->getCategoryById ($objV[self::$objItemCategoryId],
            self::$objCategoryName) != _T ('System')) {
                // Set
                $objSitemap[] = Location
                ::staticTo (new A (Array (_T ('Texts'))),
                new A (Array ($objV[self::$objItemURL])));
            }
        }

        // Return
        return $objSitemap
        ->fromArrayToString (_N_);
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

                        // Attachments
                        case _T ('Attachments'):
                            $this->renderBackend (new
                            S ('manageAttachments'));
                            break;

                        // Images
                        case _T ('Images'):
                            $this->renderBackend (new
                            S ('manageImages'));
                            break;

                        // Audios
                        case _T ('Audios'):
                            $this->renderBackend (new
                            S ('manageAudios'));
                            break;

                        // Videos
                        case _T ('Videos'):
                            $this->renderBackend (new
                            S ('manageVideos'));
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

        // Check
        if ($objWA == NULL) {
            $objWA = new A;
        }

        // HTML: Switch
        switch ($objW) {
            case 'widgetListLast':
                // Check
                if (!($objWA
                ->offsetExists ('Count'))) {
                    // Set
                    $objWA->offsetSet ('Count', 10);
                }

                // Set
                $this->objItem = $this
                ->getItems (_S ('WHERE %objItemCategoryId = "%cId"
                ORDER BY %objItemPublished DESC LIMIT 0, %nId')
                ->doToken ('%cId', $objWA['Category'])
                ->doToken ('%nId', $objWA['Count']));

                // Go
                self::mapTp ($this, $objW,
                _S (__FUNCTION__));
                break;

            case 'widgetItem':
                // Check
                if ($this->checkItemURLIsUnique ($_GET
                ->offsetGet (_T ('Item')))->toBoolean ()) {
                    // 404
                    Settings::routeTo (new
                    S ('404'));
                }

                // Set
                self::manageTTL ($this->getItemByURL ($_GET
                ->offsetGet (_T ('Item')), self::$objItemTitle));

                // Set
                self::manageTAG (new S ('description'),
                $this->getItemByURL ($_GET
                ->offsetGet (_T ('Item')),
                self::$objItemTags));

                // Go
                self::mapTp ($this, $objW,
                _S (__FUNCTION__));
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
                    $this->setExtraUpdateData (self::$objItemAuthorId, self::getAuthentication ()
                    ->getCurrentUser (Authentication::$objUserId));
                }

                // Continue
                $this->setInputType (new S ('text'))
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
                ->setLabel (_T ('Title'))
                ->setCheckForEmpty (new B (TRUE))
                ->setCheckForUnique (new B (TRUE))
                ->setInputType (new S ('textarea'))
                ->setName (self::$objItemContent)
                ->setLabel (_T ('Content'))
                ->setTinyMCETextarea (new B (TRUE))
                ->setCheckForEmpty (new B (TRUE))
                ->setInputType (new S ('submit'))
                ->setValue (_T ('Add'))
                ->setAccessKey (new S ('S'))
                ->setFormEndAndExecute (new B (TRUE));
                break;

            case 'itemEdit':
                // The URL to go back too;
                $objURLToGoBack = Location::rewriteTo (new A (Array (_T ('Do'), _T ('Id'))));

                // Form
                $this->setFieldset (_T ('Edit: ')
                ->appendString ($this->getItemById ($_GET[_T ('Id')], self::$objItemTitle)
                ->doSubStr (0, 150)->appendString (_SP)->appendString (_DTE)))
                ->setRedirect ($objURLToGoBack)
                ->setTableName (self::$objItem)
                ->setUpdateId ($_GET[_T ('Id')])
                ->setUpdateField (self::$objItemId)
                ->setName ($objF)
                ->setInputType (new S ('text'))
                ->setReadOnly (new B (TRUE))
                ->setName (new S ('href'))
                ->setLabel (_T ('Generated URL'))
                ->setValue (Location::staticTo (new A (Array (__CLASS__)),
                new A (Array ($this->getItemById ($_GET[_T ('Id')], self::$objItemURL)))))
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
                ->setLabel (_T ('Title'))
                ->setCheckForEmpty (new B (TRUE))
                ->setCheckForUnique (new B (TRUE))
                ->setInputType (new S ('textarea'))
                ->setName (self::$objItemContent)
                ->setLabel (_T ('Content'))
                ->setTinyMCETextarea (new B (TRUE))
                ->setCheckForEmpty (new B (TRUE))
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
