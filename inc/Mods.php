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
 * Provides methods of registering and activating mods;
 *
 * @author Catalin Alexandru Zamfir <catalin.zamfir@raphpframework.ro>
 * @copyright Under the terms of the GNU General Public License v3
 * @version $Id: Mods.php 1 2012-10-26 08:27:37Z root $
 */
class Mods extends Forms {
    /**
     * Container of registered mods;
     *
     * @author Catalin Alexandru Zamfir <catalin.zamfir@raphpframework.ro>
     * @copyright Under the terms of the GNU General Public License v3
     * @version $Id: Mods.php 1 2012-10-26 08:27:37Z root $
     */
    private static $objRegisteredMods = NULL;

    /**
     * Container of configuration options;
     *
     * @author Catalin Alexandru Zamfir <catalin.zamfir@raphpframework.ro>
     * @copyright Under the terms of the GNU General Public License v3
     * @version $Id: Mods.php 1 2012-10-26 08:27:37Z root $
     */
    private static $objConfiguration = NULL;

    /**
     * Sets proper requirements, makes a database to store common configuration options, in case of #1 setup, warns the user;
     *
     * @author Catalin Alexandru Zamfir <catalin.zamfir@raphpframework.ro>
     * @copyright Under the terms of the GNU General Public License v3
     * @version $Id: Mods.php 1 2012-10-26 08:27:37Z root $
     */
    public function __construct () {
        // Set
        self::$objRegisteredMods = new A;
        self::$objConfiguration = new A;

        // Requirements
        if (self::checkExists (new
        S ('_T_system_configurations'))
        ->toBoolean () == FALSE) {
            // Go
            $this->doQuery (new S ('CREATE TABLE
          IF NOT EXISTS `_T_system_configurations`
          (`k` varchar(255) NOT NULL,
        `v` longtext NOT NULL,
        PRIMARY KEY  (`k`),
        KEY `v` (`v`(255)))
        ENGINE=InnoDB DEFAULT
        CHARSET=utf8;'));

            // Give out one notification, that we're preparing the project (hard-coded text & HTML, cause we want it)
            Error::renderDeath (_T ('Setup'), _T ('Setting things up for you'), NULL, _T ('<span style="font-family: Verdana;
            font-size: 14px;">Upon next refresh we are going to set-up the requirements for your project. This can take some time,
            as we are creating tables, directories, indexes and more. After everything is finished you are free to make changes
            to the project as you wish! Thanks for your patience! Report bugs or issues @ http://raphpframework.ro/</span>'));
        }

        // Foreach
        foreach (_D (MOD_DIR)
        ->scanDirectoryPath () as $objK => $objV) {
            // Register
            $this->doRegister (new
            DirectoryPath (Architecture
            ::pathTo (MOD_DIR, $objV)));
        }
    }

    /**
     * Checks to see if (mod) exists;
     *
     * @author Catalin Alexandru Zamfir <catalin.zamfir@raphpframework.ro>
     * @copyright Under the terms of the GNU General Public License v3
     * @version $Id: Mods.php 1 2012-10-26 08:27:37Z root $
     */
    protected static final function checkIsRegistered (S $objMod) {
        // Check
        if (self::$objRegisteredMods
        ->offsetExists ($objMod)) {
            // Return
            return new B (TRUE);
        } else {
            // Return
            return new B (FALSE);
        }
    }

    /**
     * Used to register (mods);
     *
     * @author Catalin Alexandru Zamfir <catalin.zamfir@raphpframework.ro>
     * @copyright Under the terms of the GNU General Public License v3
     * @version $Id: Mods.php 1 2012-10-26 08:27:37Z root $
     */
    private final function doRegister (Path $objMod) {
        // Set
        $objModConfig = $this
        ->getSection ($objMod, new
        S ('EXTERNAL'));

        // Check
        if (isset ($objModConfig['Is.Active']) &&
        $objModConfig['Is.Active'] == 1) {
            // Set
            self::$objRegisteredMods[$objMod]['Obj'] =
            $objModConfig['Instantiate.With'];
        }
    }

    /**
     * Returns registered (mods);
     *
     * @author Catalin Alexandru Zamfir <catalin.zamfir@raphpframework.ro>
     * @copyright Under the terms of the GNU General Public License v3
     * @version $Id: Mods.php 1 2012-10-26 08:27:37Z root $
     */
    protected static final function getRegistered () {
        // Return
        return self::$objRegisteredMods;
    }

    /**
     * Gets a section of the (mod's) configuration;
     *
     * @author Dumitru Alexandru <dumitru.alexandru@raphpframework.ro>
     * @copyright Under the terms of the GNU General Public License v3
     * @version $Id: Mods.php 1 2012-10-26 08:27:37Z root $
     */
    private final function getSection (Path $objMod, S $objSection) {
        // Configuration
        $this->getConfiguration ($objMod, $objSection);

        // Return
        return self::$objConfiguration
        ->offsetGet ($objMod)
        ->offsetGet ($objSection);
    }

    /**
     * Parses the configuration, sets it to cache, returns requested section;
     *
     * @author Catalin Alexandru Zamfir <catalin.zamfir@raphpframework.ro>
     * @copyright Under the terms of the GNU General Public License v3
     * @version $Id: Mods.php 1 2012-10-26 08:27:37Z root $
     */
    private final function getConfiguration (Path $objMod, S $objSection) {
        // Check
        if ($objSection ==
        new S ('EXTERNAL')) {
            // Set
            $objPath = new S (__CLASS__);
        } else {
            // Set
            $objPath = $this
            ->getObjectAncestry ();
        }

        // Return
        return self::$objConfiguration->offsetSet ($objMod, new
        A (parse_ini_file (Architecture::pathTo ($objMod->toAbsolutePath (), CFG_DIR,
        $objPath . _DOT . CFG_DIR), TRUE)));
    }

    /**
     * Sets a configuration key, on the INTERNAL section (mainly);
     *
     * @author Catalin Alexandru Zamfir <catalin.zamfir@raphpframework.ro>
     * @copyright Under the terms of the GNU General Public License v3
     * @version $Id: Mods.php 1 2012-10-26 08:27:37Z root $
     */
    public final function setConfigKey (S $objConfigKey, S $objConfigVar) {
        // Process
        $objCopyKey = $objConfigKey
        ->makeCopyObject ()->prependString ($this
        ->getObjectAncestry () . _DOT);

        // Check
        if (self::$objConfiguration
        ->doCount ()->toInt () != 0) {

            // Set it back
            self::$objConfiguration
            ->offsetSet ($objCopyKey,
            $objConfigVar);

            // Update
            $this->doQuery (_QS ('doUPDATE')
            ->doToken ('%table', new S ('_T_system_configurations'))
            ->doToken ('%condition', new S ('v = "%vId" WHERE k = "%kId"'))
            ->doToken ('%kId', $objCopyKey)
            ->doToken ('%vId', $objConfigVar));
        } else {
            // Error
            throw new EmptyConfigurationException;
        }
    }

    /**
     * Returns a configuration key, from the INTERNAL section;
     *
     * @author Catalin Alexandru Zamfir <catalin.zamfir@raphpframework.ro>
     * @copyright Under the terms of the GNU General Public License v3
     * @version $Id: Mods.php 1 2012-10-26 08:27:37Z root $
     */
    public final function getConfigKey (S $objConfigKey, B $objSaved = NULL) {
        // Check
        if ($objSaved == NULL) {
            // Set
            $objSaved = new
            B (TRUE);
        }

        // Get the proper config key
        $objCopyKey = $objConfigKey
        ->makeCopyObject ()->prependString ($this
        ->getObjectAncestry () . _DOT);

        // Check for it
        if (self::$objConfiguration
        ->offsetExists ($objCopyKey) &&
        $objSaved->toBoolean () == TRUE) {

            // Return
            return new S (self::$objConfiguration
            ->offsetGet ($objCopyKey));
        } else {
            // Do the query (once and cache it!);
            $objQ = $this->doQuery (_QS ('doSELECT')
            ->doToken ('%what', new S ('*'))
            ->doToken ('%table', new S ('_T_system_configurations'))
            ->doToken ('%condition', new S), new S ('k'));

            // Check
            if ($objQ->doCount ()->toInt () != 0 &&
            $objQ->offsetExists ($objCopyKey)) {

                // Assign
                foreach ($objQ as $objK => $objV) {
                    // Set
                    self::$objConfiguration
                    [$objV['k']] = $objV['v'];
                }

                // Return
                return self::$objConfiguration[$objCopyKey];
            } else {
                // Parse configuration
                $objTempConfigArray = self::getSection ($this
                ->objPathToMod, new S ('INTERNAL'));

                // Reset (system cached)
                self::$objConfiguration = new A;

                // Set configuration params to proper vars
                foreach ($objTempConfigArray as $objK => $objV) {
                    // Set
                    self::$objConfiguration[$this
                    ->getObjectAncestry () . _DOT .
                    $objK] = $objV;
                }

                // Foreach
                foreach (self::$objConfiguration as $objK => $objV) {
                    // At FIRST, check for existence
                    if ($this->doQuery (_QS ('doSELECT')
                    ->doToken ('%what', new S ('*'))
                    ->doToken ('%table', new S ('_T_system_configurations'))
                    ->doToken ('%condition', new S ('WHERE k = "%kId"'))
                    ->doToken ('%kId', $objK))
                    ->doCount ()->toInt () == 0) {

                        // No key, do INSERT it first
                        $this->doQuery (_QS ('doINSERT')
                        ->doToken ('%table', new S ('_T_system_configurations'))
                        ->doToken ('%condition', new S ('k = "%kId", v = "%vId"'))
                        ->doToken ('%kId', $objK)
                        ->doToken ('%vId', $objV));
                    }
                }

                // Return
                return new S ((string) self::$objConfiguration
                ->offsetGet ($objCopyKey));
            }
        }
    }
}
?>
