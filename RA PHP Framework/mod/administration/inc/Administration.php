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
 * Provides an administration interface for registered extensions with our framework.
 *
 * @author Catalin Alexandru Zamfir <catalin.zamfir@raphpframework.ro>
 * @copyright Under the terms of the GNU General Public License v3
 * @version $Id: Administration.php 1 2012-10-26 08:27:37Z root $
 */
class Administration extends Commons {

    /* CONSTRUCT */
    public function __construct () {
        // Commons
        $this->tieInConfiguration ();

        // Requirements
        $this->objMenu = new A;
        $this->objSubmenu = new A;
        $this->objWidgets = new A;

        // Commons
        $this->tieInAuthentication (_new ('Authentication'));
        $this->tieInDefaultRequirements ();

        // Specifics
        $this->outputAsRequired ();
        $this->doActions ();

        // Check
        if (self::getAuthentication ()
        ->checkIsLoggedIn ()->toBoolean () == TRUE &&
        self::getAuthentication ()->checkCurrentUserZoneACL (new
        S (__CLASS__))->toBoolean () == TRUE) {

            // Check
            if (!$_GET->offsetExists (_T ('P')) && !$_GET
            ->offsetExists (_T ('Run'))) {
                // Redirect
                Header::setKey (Location
                ::rewriteTo (new A (Array (_T ('P'))),
                new A (Array (_T ('Status')))),
                new S ('Location'));
            }

            // Tie
            $this->tieInAdminInterfaces ();
        } else {
            // Check
            if ($_GET->doCount ()
            ->toInt () > 1) {
                // Reset
                Header::setKey (Location::staticTo (new A (Array (_T ('Admin'))),
                new A (Array (_T ('Go')))), new S ('Location'));
            }

            // Path
            self::setTp ($this, new S ('OBJ'),
            $objTp = new Path (Architecture::pathTo ($this
            ->getPathToSkin (), __CLASS__ . TPL_EXTENSION)));

            // Execute
            self::exeTp ($objTp);
        }
    }

    /**
     * Outputs different binary content, as requested via URLs;
     *
     * @author Catalin Alexandru Zamfir <catalin.zamfir@raphpframework.ro>
     * @copyright Under the terms of the GNU General Public License v3
     * @version $Id: Administration.php 1 2012-10-26 08:27:37Z root $
     */
    private function outputAsRequired () {
        // Check
        if ($_GET
        ->offsetExists (_T ('Output'))) {
            // Check
            if ($_GET
            ->offsetExists (_T ('Avatar'))) {
                // Header
                Header::setKey (new S (Header
                ::CONTENT_TYPE_IMAGE_JPEG),
                new S ('Content-Type'));

                // Path
                $objAvatar = new StoragePath (Architecture::pathTo (UPLOAD_DIR,
                $this->getAuthentication ()->getObjectAncestry ()->toLower (),
                'avatars', '64' . _U . '64' . _U . $this->getAuthentication ()
                ->getUserById ($_GET->offsetGet (_T ('Output')),
                Authentication::$objUserAvatar)), FALSE);

                // Check
                if ($objAvatar->checkPathExists (FALSE)
                ->toBoolean () == TRUE && $objAvatar->checkPathIs ('file')
                ->toBoolean () == TRUE) {
                    // Output
                    self::outputBinary ($objAvatar);
                } else {
                    // Output
                    self::outputBinary (new
                    Path (Architecture::pathTo (MOD_DIR,
                    $this->getObjectAncestry ()->toLower (),
                    SKIN_DIR_DIR, SKIN, SKIN_IMG_DIR,
                    'others/user.png')));
                }
            }
        }
    }

    /**
     * Manages different common actions for this administrative interface;
     *
     * @author Catalin Alexandru Zamfir <catalin.zamfir@raphpframework.ro>
     * @copyright Under the terms of the GNU General Public License v3
     * @version $Id: Administration.php 1 2012-10-26 08:27:37Z root $
     */
    private function doActions () {
        // Check
        if ($_GET
        ->offsetExists (_T ('Do Admin'))) {
            // Switch
            switch ($_GET
            ->offsetGet (_T ('Do Admin'))) {
                // Hide
                case _T ('Hide Left'):
                    // Check
                    if (Session::checkKey (new S ('Hide Left'),
                    new O ('1'))->toBoolean () == FALSE) {
                        // Set
                        Session::setKey (new
                        S ('Hide Left'), new O ('1'));
                    } else {
                        // Set
                        Session::unsetKey (new
                        S ('Hide Left'));
                    }

                    // Redirect
                    Header::setKey (Location::rewriteTo (new
                    A (Array ('Do Admin'))), new S ('Location'));
                    break;

                // Notification
                case _T ('Notification'):
                    // Check
                    if ($_GET->offsetExists ('Id')) {
                        // Update
                        $this->_Q (_QS ('doUPDATE')
                        ->doToken ('%table', new S ('_T_system_notifications'))
                        ->doToken ('%condition', new S ('viewed = "Y" WHERE id = "%nId"'))
                        ->doToken ('%nId', $_GET->offsetGet ('Id')));

                        // Output
                        self::outputString ($_GET[_T ('Id')]);
                    } else {
                        // Output
                        self::outputString (new S);
                    }
                    // BK;
                    break;

                // Cron Stop
                case _T ('Cron Stop'):
                    // Set
                    $this->getSettings ()
                    ->setConfigKey (new S ('Cron.State'),
                    new S ('Stopped'));

                    // Redirect
                    Header::setKey (Location::rewriteTo (new
                    A (Array ('Do Admin'))), new S ('Location'));
                    break;

                // Cron Start
                case _T ('Cron Start'):
                    // Set
                    $this->getSettings ()
                    ->setConfigKey (new S ('Cron.State'),
                    new S ('Run'));

                    // Redirect
                    Header::setKey (Location::rewriteTo (new
                    A (Array ('Do Admin'))), new S ('Location'));
                    break;

                // Anonymous
                case _T ('Log Out'):
                    // Go
                    self::getAuthentication ()->doSignOut ();
                    Location::doCheckPath ();
                    break;
            }
        }
    }

    /**
     * Ties registered administration interfaces;
     *
     * @author Catalin Alexandru Zamfir <catalin.zamfir@raphpframework.ro>
     * @copyright Under the terms of the GNU General Public License v3
     * @version $Id: Administration.php 1 2012-10-26 08:27:37Z root $
     */
    private function tieInAdminInterfaces () {
        // Defaults
        self::manageTTL ($this
        ->getConfigKey (new
        S ('Header')));

        // Switch
        self::switchTTL ();

        // Add the current title
        self::manageTTL (_T ('Administration'));
        self::manageTTL ($_GET->offsetExists (_T ('P')) ? $_GET->offsetGet (_T ('P')) : new S ('Run'));
        self::manageTTL ($_GET->offsetExists (_T ('S')) ? $_GET->offsetGet (_T ('S')) : new S ('Top'));

        // Foreach
        foreach ($this->getRegistered ()
        as $objK => $objV) {

            // Ignore: Administration
            if ($objV['Obj'] == __CLASS__) {
                // Tie
                $this->tieInAdministration ($this);
                continue;
            }

            // Ignore: Frontend
            if ($objV['Obj'] == 'Frontend') {
                // None
                continue;
            }

            // Ignore: Authentication
            if ($this->getObjectAncestry (self
            ::getAuthentication ()) == $objV['Obj']) {
                // Check
                if (self::getAuthentication ()->checkCurrentUserZoneACL ($this
                ->getObjectAncestry (self::getAuthentication ()))->toBoolean () == TRUE) {
                    // Tie
                    self::getAuthentication ()
                    ->tieInAdministration ($this);
                }

                // Go on;
                continue;
            } else {
                // Make & store
                $objMod = _new ($objV['Obj']);
                $objMod->tieInAuthentication (self::getAuthentication ());

                // Check
                if (self::getAuthentication ()
                ->checkCurrentUserZoneACL ($this
                ->getObjectAncestry ($objMod))
                ->toBoolean () == TRUE) {
                    // Tie
                    $objMod->tieInAdministration ($this);
                }
            }
        }

        // Object
        self::setTp ($this, new S ('OBJ'),
        $objTp = new Path (Architecture::pathTo ($this
        ->getPathToSkin (), __CLASS__ . TPL_EXTENSION)));

        // Execute
        self::exeTp ($objTp);
    }

    /**
     * (non-PHPdoc)
     * @see Commons::tieInAdministration()
     */
    public function tieInAdministration (Administration $objAdministration) {
        // Menu
        $objDD = new Path (Architecture
        ::pathTo ($this->getPathToAdmin (),
        $this->getConfigKey (new S ('Authenticated'))));
        $objAdministration->setLink (new S (_T ('Status')), $objDD,
        $this->getHELP (new S (_T ('Status'))));

        $objWP = new Path (Architecture
        ::pathTo ($this->getPathToAdmin (),
        $this->getConfigKey (new S ('Not.Authenticated'))));
        $objAdministration->setSink (new S (_T ('Status')), $objWP,
        $this->getHELP (new S (_T ('Status'))));

        // Widgets
        $this->setWidget ($this->getHELP (new
        S ('adminStatus'))->doToken ('%uId', self::getAuthentication ()
        ->getCurrentUser (Authentication::$objUserUName)));
    }

    /**
     * Ties default requirements of the current interface (JS & CSS);
     *
     * @author Catalin Alexandru Zamfir <catalin.zamfir@raphpframework.ro>
     * @copyright Under the terms of the GNU General Public License v3
     * @version $Id: Administration.php 1 2012-10-26 08:27:37Z root $
     */
    public function tieInDefaultRequirements () {
        // Set
        $this->objJSS =
        Architecture::pathTo ($this
        ->getPathToSkinJSS (),
        'jQuery');

        // Set
        $this->objCSS =
        Architecture::pathTo ($this
        ->getPathToSkinCSS (),
        'jQuery');

        // Requirements
        self::manageCSS (new Path (Architecture::pathTo ($this->objCSS, 'jQUI.css')));
        self::manageCSS (new Path (Architecture::pathTo ($this->objCSS, 'jQGrowl.css')));
        self::manageCSS (new Path (Architecture::pathTo ($this->objCSS, 'jQAutoComplete.css')));
        self::manageCSS (new Path (Architecture::pathTo ($this->objCSS, 'jQFacebox.css')));
        self::manageCSS (new Path (Architecture::pathTo ($this->objCSS, 'jQTipsy.css')));
        self::manageCSS (new Path (Architecture::pathTo ($this->objCSS, 'jQDataTables.css')));
        self::manageCSS (new Path (Architecture::pathTo ($this->objCSS, 'jQCodeMirror.css')));
        self::manageCSS (new Path (Architecture::pathTo ($this->objCSS, 'jQCodeMirror.Default.css')));
        self::manageJSS (new Path (Architecture::pathTo ($this->objJSS, 'jQ.js')));
        self::manageJSS (new Path (Architecture::pathTo ($this->objJSS, 'jQUI.js')));
        self::manageJSS (new Path (Architecture::pathTo ($this->objJSS, 'jQBGIFrame.js')));
        self::manageJSS (new Path (Architecture::pathTo ($this->objJSS, 'jQAutoComplete.js')));
        self::manageJSS (new Path (Architecture::pathTo ($this->objJSS, 'jQClock.js')));
        self::manageJSS (new Path (Architecture::pathTo ($this->objJSS, 'jQBind.js')));
        self::manageJSS (new Path (Architecture::pathTo ($this->objJSS, 'jQUICheckbox.js')));
        self::manageJSS (new Path (Architecture::pathTo ($this->objJSS, 'jQEasing.js')));
        self::manageJSS (new Path (Architecture::pathTo ($this->objJSS, 'jQWidget.js')));
        self::manageJSS (new Path (Architecture::pathTo ($this->objJSS, 'jQMasked.js')));
        self::manageJSS (new Path (Architecture::pathTo ($this->objJSS, 'jQGrowl.js')));
        self::manageJSS (new Path (Architecture::pathTo ($this->objJSS, 'jQLiquidMetalAlgo.js')));
        self::manageJSS (new Path (Architecture::pathTo ($this->objJSS, 'jQModernizr.js')));
        self::manageJSS (new Path (Architecture::pathTo ($this->objJSS, 'jQSelectVizr.js')));
        self::manageJSS (new Path (Architecture::pathTo ($this->objJSS, 'jQFacebox.js')));
        self::manageJSS (new Path (Architecture::pathTo ($this->objJSS, 'jQTipsy.js')));
        self::manageJSS (new Path (Architecture::pathTo ($this->objJSS, 'jQTimers.js')));
        self::manageJSS (new Path (Architecture::pathTo ($this->objJSS, 'jQZeroClipboard.js')));
        self::manageJSS (new Path (Architecture::pathTo ($this->objJSS, 'jQDataTables.js')));
        self::manageJSS (new Path (Architecture::pathTo ($this->objJSS, 'jQToJson.js')));
        self::manageJSS (new Path (Architecture::pathTo ($this->objJSS, 'jQTools.js')));
        self::manageJSS (new Path (Architecture::pathTo ($this->objJSS, 'jQDropZone.js')));
        self::manageJSS (new Path (Architecture::pathTo ($this->objJSS, 'jQFlot.js')));
        self::manageJSS (new Path (Architecture::pathTo ($this->objJSS, 'jQFlot.ExCanvas.js')));
        self::manageJSS (new Path (Architecture::pathTo ($this->objJSS, 'jQFlot.Between.js')));
        self::manageJSS (new Path (Architecture::pathTo ($this->objJSS, 'jQFlot.ColorHelpers.js')));
        self::manageJSS (new Path (Architecture::pathTo ($this->objJSS, 'jQFlot.Crosshair.js')));
        self::manageJSS (new Path (Architecture::pathTo ($this->objJSS, 'jQFlot.Image.js')));
        self::manageJSS (new Path (Architecture::pathTo ($this->objJSS, 'jQFlot.Navigate.js')));
        self::manageJSS (new Path (Architecture::pathTo ($this->objJSS, 'jQFlot.Pie.js')));
        self::manageJSS (new Path (Architecture::pathTo ($this->objJSS, 'jQFlot.Resize.js')));
        self::manageJSS (new Path (Architecture::pathTo ($this->objJSS, 'jQFlot.Selection.js')));
        self::manageJSS (new Path (Architecture::pathTo ($this->objJSS, 'jQFlot.Stack.js')));
        self::manageJSS (new Path (Architecture::pathTo ($this->objJSS, 'jQFlot.Symbol.js')));
        self::manageJSS (new Path (Architecture::pathTo ($this->objJSS, 'jQFlot.Threshold.js')));
        self::manageJSS (new Path (Architecture::pathTo ($this->objJSS, 'jQNivo.js')));
        self::manageJSS (new Path (Architecture::pathTo ($this->objJSS, 'jQCodeMirror.js')));
        self::manageJSS (new Path (Architecture::pathTo ($this->objJSS, 'jQTo.js')));
        self::manageJSS (new Path (Architecture::pathTo ($this->objJSS, 'jQExe.js')));

        // Set
        self::manageCSS (new Path (Architecture
        ::pathTo ($this->getPathToSkinCSS (),
        $this->getObjectAncestry () .
        CSS_EXTENSION)));
    }

    /**
     * Sets a main path for the current extension, under a menu name and path;
     *
     * @author Catalin Alexandru Zamfir <catalin.zamfir@raphpframework.ro>
     * @copyright Under the terms of the GNU General Public License v3
     * @version $Id: Administration.php 1 2012-10-26 08:27:37Z root $
     */
    public function setLink (S $objMenuName, Path $objPathToIncludedFile, S $objLinkString = NULL) {
        // Requirements
        $this->objMenu[$objMenuName] = new
        A (Array ('path' => $objPathToIncludedFile,
        'text' => $objLinkString));
    }

    /**
     * Each main menu entry, has a sink, in which other menu items are held;
     *
     * @author Catalin Alexandru Zamfir <catalin.zamfir@raphpframework.ro>
     * @copyright Under the terms of the GNU General Public License v3
     * @version $Id: Administration.php 1 2012-10-26 08:27:37Z root $
     */
    public function setSink (S $objSubMenuName, Path $objPathToIncludedFile, S $objLinkString = NULL) {
        // Requirements
        if ($objLinkString == NULL) $objLinkString = $objSubMenuName;
        foreach (current ($this->objMenu) as $objK => $objV) $objCurrentIndex = $objK;
        $this->objSubmenu[$objCurrentIndex][$objSubMenuName]['name'] = $objSubMenuName;
        $this->objSubmenu[$objCurrentIndex][$objSubMenuName]['path'] = $objPathToIncludedFile;
        $this->objSubmenu[$objCurrentIndex][$objSubMenuName]['text'] = $objLinkString;
    }

    /**
     * Sets an error message to be shown upon specific actions;
     *
     * @author Catalin Alexandru Zamfir <catalin.zamfir@raphpframework.ro>
     * @copyright Under the terms of the GNU General Public License v3
     * @version $Id: Administration.php 1 2012-10-26 08:27:37Z root $
     */
    public function setErrorMessage (S $objErrorMessage, S $objURLToGoBack) {
        // Set the template file
        $objTp = new Path (Architecture::pathTo ($this->getPathToSkin (), __FUNCTION__ . TPL_EXTENSION));
        self::setTp ($objErrorMessage, new S ('actionErrorMessage'), $objTp);
        self::setTp ($objURLToGoBack, new S ('objURLToGoBack'), $objTp);
        self::exeTp ($objTp);
    }

    /**
     * Sets a widget to be shown as part of the 'Status' interface;
     *
     * @author Catalin Alexandru Zamfir <catalin.zamfir@raphpframework.ro>
     * @copyright Under the terms of the GNU General Public License v3
     * @version $Id: Administration.php 1 2012-10-26 08:27:37Z root $
     */
    public function setWidget (S $objWidgetWText, B $objEVAL = NULL) {
        // Requirements
        if ($objEVAL == NULL) $objEVAL = new B (TRUE);
        foreach (current ($this->objMenu) as $objK => $objV) $objCurrentIndex = $objK;
        $this->objWidgets[] = new A (Array ('wtext' => $objWidgetWText, 'wEVAL' => $objEVAL));
    }

    /**
     * Returns an associated widget, via its title;
     *
     * @author Catalin Alexandru Zamfir <catalin.zamfir@raphpframework.ro>
     * @copyright Under the terms of the GNU General Public License v3
     * @version $Id: Administration.php 1 2012-10-26 08:27:37Z root $
     */
    public function getWidget (S $objWidgetTitle = NULL) {
        // If NULL, return the array;
        if ($objWidgetTitle == NULL) return $this->objWidgets;

        // Foreach
        foreach ($this->objWidgets as $objK => $objV) {
            // Check
            if ($objV['title'] == $objWidgetTitle) {
                return $this->objWidgets[$objK];
                break;
            }
        }
    }
}
?>
