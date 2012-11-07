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
 * Provides abstraction for the underlying database;
 *
 * @author Catalin Alexandru Zamfir <catalin.zamfir@raphpframework.ro>
 * @copyright Under the terms of the GNU General Public License v3
 * @version $Id: Database.php 1 2012-10-26 08:27:37Z root $
 */
class Database extends Template {
    /**
     * Container of SQL host;
     *
     * @author Catalin Alexandru Zamfir <catalin.zamfir@raphpframework.ro>
     * @copyright Under the terms of the GNU General Public License v3
     * @version $Id: Database.php 1 2012-10-26 08:27:37Z root $
     */
    private static $objSQLH = NULL;

    /**
     * Container of SQL user;
     *
     * @author Catalin Alexandru Zamfir <catalin.zamfir@raphpframework.ro>
     * @copyright Under the terms of the GNU General Public License v3
     * @version $Id: Database.php 1 2012-10-26 08:27:37Z root $
     */
    private static $objSQLU = NULL;

    /**
     * Container of SQL password;
     *
     * @author Catalin Alexandru Zamfir <catalin.zamfir@raphpframework.ro>
     * @copyright Under the terms of the GNU General Public License v3
     * @version $Id: Database.php 1 2012-10-26 08:27:37Z root $
     */
    private static $objSQLP = NULL;

    /**
     * Container of SQL database name;
     *
     * @author Catalin Alexandru Zamfir <catalin.zamfir@raphpframework.ro>
     * @copyright Under the terms of the GNU General Public License v3
     * @version $Id: Database.php 1 2012-10-26 08:27:37Z root $
     */
    private static $objSQLD = NULL;

    /**
     * Container of SQL prefix;
     *
     * @author Catalin Alexandru Zamfir <catalin.zamfir@raphpframework.ro>
     * @copyright Under the terms of the GNU General Public License v3
     * @version $Id: Database.php 1 2012-10-26 08:27:37Z root $
     */
    private static $objSQLR = NULL;

    /**
     * Checker of in-transaction or not (flag);
     *
     * @author Catalin Alexandru Zamfir <catalin.zamfir@raphpframework.ro>
     * @copyright Under the terms of the GNU General Public License v3
     * @version $Id: Database.php 1 2012-10-26 08:27:37Z root $
     */
    private static $objInTransaction = NULL;

    /**
     * Saved SQL string, always rewritten with every query;
     *
     * @author Catalin Alexandru Zamfir <catalin.zamfir@raphpframework.ro>
     * @copyright Under the terms of the GNU General Public License v3
     * @version $Id: Database.php 1 2012-10-26 08:27:37Z root $
     */
    private static $objSQLSaved = NULL;

    /**
     * Connection object, to the default or non-default SQL connection;
     *
     * @author Catalin Alexandru Zamfir <catalin.zamfir@raphpframework.ro>
     * @copyright Under the terms of the GNU General Public License v3
     * @version $Id: Database.php 1 2012-10-26 08:27:37Z root $
     */
    private static $objConnection = NULL;

    /**
     * Cache container for tokens that are going to be searched & replaced;
     *
     * @author Catalin Alexandru Zamfir <catalin.zamfir@raphpframework.ro>
     * @copyright Under the terms of the GNU General Public License v3
     * @version $Id: Database.php 1 2012-10-26 08:27:37Z root $
     */
    private static $objCacheToken = NULL;

    /**
     * Constructs the database object and makes a connection to the SQL_default database;
     *
     * @author Catalin Alexandru Zamfir <catalin.zamfir@raphpframework.ro>
     * @copyright Under the terms of the GNU General Public License v3
     * @version $Id: Database.php 1 2012-10-26 08:27:37Z root $
     */
    public function __construct (S $databaseIndex = NULL) {
        // Set
        self::$objCacheToken = new A;

        // Parse
        $objConfig = new A (parse_ini_file (new
        Path (Architecture::pathTo (CFG_DIR,
        __CLASS__ . INI_EXTENSION)), TRUE));

        // Check
        switch ($databaseIndex == NULL) {
            case TRUE:
                // Set the default
                $databaseIndex = new
                S ('SQL_default');
                break;
        }

        // Defaults
        self::$objInTransaction = new B (FALSE);

        // Set object properties to some defaults
        self::$objSQLH = new S ($objConfig[(string) $databaseIndex]['host']);
        self::$objSQLU = new S ($objConfig[(string) $databaseIndex]['user']);
        self::$objSQLP = new S ($objConfig[(string) $databaseIndex]['pass']);
        self::$objSQLD = new S ($objConfig[(string) $databaseIndex]['database']);
        self::$objSQLR = new S ($objConfig[(string) $databaseIndex]['prefix']);

        // Check
        if (SQL_PERSISTENT_CONNECTION == 1) {
            // Return
            self::$objConnection =
            new MySQLi (self::$objSQLH,
            self::$objSQLU, self::$objSQLP)
            or self::renderSQLScreenOfDeath ();
        } else {
            // Return
            self::$objConnection =
            new MySQLi (self::$objSQLH,
            self::$objSQLU, self::$objSQLP)
            or self::renderSQLScreenOfDeath ();
        }

        // UTF8ize
        self::$objConnection
        ->set_charset ('utf8');

        // Set
        self::$objConnection
        ->select_db (self::$objSQLD)
        or self::renderSQLScreenOfDeath ();

        // Fix big connections
        if (SQL_SET_GLOBAL_MAX_PACKET == TRUE) {
            // MySQL max_allowed_packet
            self::doQuery (new S ('SET GLOBAL
            max_allowed_packet = 256 * 1024 * 1024'));
        }
    }

    /**
     * Returns the current connection object;
     *
     * @author Catalin Alexandru Zamfir <catalin.zamfir@raphpframework.ro>
     * @copyright Under the terms of the GNU General Public License v3
     * @version $Id: Database.php 1 2012-10-26 08:27:37Z root $
     */
    public static final function getConnection () {
        // Return
        return self::$objConnection;
    }

    /**
     * Switch through connections, defined in the configuration;
     *
     * @author Catalin Alexandru Zamfir <catalin.zamfir@raphpframework.ro>
     * @copyright Under the terms of the GNU General Public License v3
     * @version $Id: Database.php 1 2012-10-26 08:27:37Z root $
     */
    protected static final function switchConnections (S $databaseIndex = NULL) {
        // Eradicate the connection
        self::$objConnection->close ();

        // Switch
        switch ($databaseIndex == NULL) {
            case TRUE:
                // Default
                $databaseIndex = new
                S ('SQL_default');
                break;
        }

        // Parse
        $objConfig = new A (parse_ini_file (new
        Path ('db.ini'), TRUE));

        // Set object properties to some defaults
        self::$objSQLH = new S ($objConfig[(string) $databaseIndex]['host']);
        self::$objSQLU = new S ($objConfig[(string) $databaseIndex]['user']);
        self::$objSQLP = new S ($objConfig[(string) $databaseIndex]['pass']);
        self::$objSQLD = new S ($objConfig[(string) $databaseIndex]['database']);
        self::$objSQLR = new S ($objConfig[(string) $databaseIndex]['prefix']);

        // Check
        if (SQL_PERSISTENT_CONNECTION == 1) {
            // Return
            self::$objConnection =
            new MySQLi (self::$objSQLH,
            self::$objSQLU, self::$objSQLP)
            or self::renderSQLScreenOfDeath ();
        } else {
            // Return
            self::$objConnection =
            new MySQLi (self::$objSQLH,
            self::$objSQLU, self::$objSQLP)
            or self::renderSQLScreenOfDeath ();
        }

        // UTF8ize
        self::$objConnection
        ->set_charset ('utf8');

        // Set
        self::$objConnection
        ->select_db (self::$objSQLD)
        or self::renderSQLScreenOfDeath ();

        // Fix big connections
        if (SQL_SET_GLOBAL_MAX_PACKET == TRUE) {
            // MySQL max_allowed_packet
            self::doQuery (new S ('SET GLOBAL
            max_allowed_packet = 256 * 1024 * 1024'));
        }
    }

    /**
     * Query and return the results;
     *
     * @author Catalin Alexandru Zamfir <catalin.zamfir@raphpframework.ro>
     * @copyright Under the terms of the GNU General Public License v3
     * @version $Id: Database.php 1 2012-10-26 08:27:37Z root $
     */
    protected static final function doQuery (S $objQueryString, S $objQueryField = NULL) {
        // Save
        self::$objSQLSaved = $objQueryString;

        // Query
        $objQuery = new O (self::$objConnection->query ($objQueryString
        ->doToken (SQL_PREFIX, self::$objSQLR), MYSQLI_STORE_RESULT));

        // Check
        if ($objQuery
        ->checkIs ('obj')
        ->toBoolean ()) {
            // Set
            $objResourceSet = new A;

            // Check
            if ($objQueryField == NULL) {
                // Forever
                while ($objR = self::getRow ($objQuery)) {
                    // Indexed
                    $objResourceSet[] = $objR;
                }

                // Return
                return $objResourceSet;
            } else {
                // Forever
                while ($objR = self::getRow ($objQuery)) {
                    // Associative
                    $objResourceSet[$objR[$objQueryField]] = $objR;
                }

                // Return
                return $objResourceSet;
            }
        } else if ($objQuery
        ->checkIs ('bln')
        ->toBoolean ()) {
            // Check
            if ($objQuery
            ->toMix () == TRUE) {
                // Return
                return new B (TRUE);
            } else {
                // Death
                self::renderSQLScreenOfDeath ();
            }
        }
    }

    /**
     * Get fields from the table passed as argument;
     *
     * @author Catalin Alexandru Zamfir <catalin.zamfir@raphpframework.ro>
     * @copyright Under the terms of the GNU General Public License v3
     * @version $Id: Database.php 1 2012-10-26 08:27:37Z root $
     */
    protected static final function getFields (S $objQueryTable) {
        // Set
        $tableFieldArray = new A;

        // Foreach
    	foreach (self::$objConnection
        ->query (_S ('SELECT * FROM %pId LIMIT 0')
        ->doToken ('%pId', $objQueryTable))
        ->fetch_fields () as $objK => $objV) {
            // Set
            $tableFieldArray[] = $objV->name;
        }

        // Return
        return $tableFieldArray;
    }

    /**
     * Get number of affected rows from the last query;
     *
     * @author Catalin Alexandru Zamfir <catalin.zamfir@raphpframework.ro>
     * @copyright Under the terms of the GNU General Public License v3
     * @version $Id: Database.php 1 2012-10-26 08:27:37Z root $
     */
    protected static final function getAffectedRows () {
        // Return
        return new I (self::$objConnection
        ->affected_rows);
    }

    /**
     * Get a row from the current set;
     *
     * @author Catalin Alexandru Zamfir <catalin.zamfir@raphpframework.ro>
     * @copyright Under the terms of the GNU General Public License v3
     * @version $Id: Database.php 1 2012-10-26 08:27:37Z root $
     */
    private static final function getRow (O $objQuerySet) {
        // Fetch
        $objSet = mysqli_fetch_assoc
        ($objQuerySet->toMix ());

        // Check
        if ($objSet != FALSE) {
            // Foreach
            foreach ($objSet as $objK => $objV) {
                ($objV != NULL) ?
                ($objSet[$objK] = new S ($objV)):
                ($objSet[$objK] = new S (_NONE));
            }

            // Return
            return new A ($objSet);
        } else {
            // Return
            return FALSE;
        }
    }

    /**
     * Starts a transaction;
     *
     * @author Catalin Alexandru Zamfir <catalin.zamfir@raphpframework.ro>
     * @copyright Under the terms of the GNU General Public License v3
     * @version $Id: Database.php 1 2012-10-26 08:27:37Z root $
     */
    protected static final function createTransaction () {
        // Set
        self::$objInTransaction = new B (TRUE);

        // Return
        return self::doQuery (new
        S ('BEGIN'));
    }

    /**
     * Commits the current transaction;
     *
     * @author Catalin Alexandru Zamfir <catalin.zamfir@raphpframework.ro>
     * @copyright Under the terms of the GNU General Public License v3
     * @version $Id: Database.php 1 2012-10-26 08:27:37Z root $
     */
    protected static final function commitTransaction () {
        // Set
        self::$objInTransaction = new B (FALSE);

        // Return
        return self::doQuery (new
        S ('COMMIT'));
    }

    /**
     * Rollbacks or erases any pending modifications in the transaction;
     *
     * @author Catalin Alexandru Zamfir <catalin.zamfir@raphpframework.ro>
     * @copyright Under the terms of the GNU General Public License v3
     * @version $Id: Database.php 1 2012-10-26 08:27:37Z root $
     */
    protected static final function eraseTransaction () {
        // Check
        if (self::$objInTransaction
        ->toBoolean () == TRUE) {
            // Return
            return self::doQuery (new
            S ('ROLLBACK'));
        }
    }

    /**
     * Used as a shorthand method for query, while also doing token changes in the string;
     *
     * @author Catalin Alexandru Zamfir <catalin.zamfir@raphpframework.ro>
     * @copyright Under the terms of the GNU General Public License v3
     * @version $Id: Database.php 1 2012-10-26 08:27:37Z root $
     */
    protected final function _Q (S $objSQL, S $objQueryField = NULL) {
        // Return
        return self::doQuery ($this->doChangeToken
        ($objSQL), $objQueryField);
    }

    /**
     * Changes tokens for proper strings in the given query;
     *
     * @author Catalin Alexandru Zamfir <catalin.zamfir@raphpframework.ro>
     * @copyright Under the terms of the GNU General Public License v3
     * @version $Id: Database.php 1 2012-10-26 08:27:37Z root $
     */
    protected function doChangeTokens (A $objTokens, A $objReplac, S $objSQLParam) {
        // Check
        if (self::$objCacheToken
        ->offsetExists ($this
        ->getObjectAncestry ())) {
            // Set
            $objTokens =
            self::$objCacheToken
            ->offsetGet ($this
            ->getObjectAncestry ());
        } else {
            // Modify tokens
            foreach ($objTokens as $objK => $objV) {
                // Make an array of temp tokens
                $objTokens[$objK] = '/%\b' . $objV . '\b/iS';
            }

            // Set
            self::$objCacheToken
            ->offsetSet ($this
            ->getObjectAncestry (),
            $objTokens);
        }

        // Return
        return $objSQLParam
        ->pregChange ($objTokens->toArray (),
        $objReplac->toArray (), $objSQLParam);
    }

    /**
     * Returns the auto_increment value of the table given as argument;
     *
     * @author Catalin Alexandru Zamfir <catalin.zamfir@raphpframework.ro>
     * @copyright Under the terms of the GNU General Public License v3
     * @Version $Id: Database.php 1 2012-10-26 08:27:37Z root $
     */
    protected static final function getAutoIncrement (S $objTable) {
        // Return
        return self::doQuery (_S ('SHOW TABLE STATUS LIKE "%tId"')
        ->doToken ('%tId', $objTable))->offsetGet (0)
        ->offsetGet ('Auto_increment');
    }

    /**
     * Checks to see if tables of the given pattern exist;
     *
     * @author Catalin Alexandru Zamfir <catalin.zamfir@raphpframework.ro>
     * @copyright Under the terms of the GNU General Public License v3
     * @Version $Id: Database.php 1 2012-10-26 08:27:37Z root $
     */
    protected static final function checkExists (S $objPattern) {
        // Return
        return self::doQuery (_S ('SHOW TABLES LIKE "%pId"')
        ->doToken ('%pId', $objPattern))->doCount ()->toInt () != 0 ?
        new B (TRUE) : new B (FALSE);
    }

    /**
     * Escapse string via MySQLi's mysql_real_escape_string (charset sensitive);
     *
     * @author Catalin Alexandru Zamfir <catalin.zamfir@raphpframework.ro>
     * @copyright Under the terms of the GNU General Public License v3
     * @version $Id: Database.php 1 2012-10-26 08:27:37Z root $
     */
    protected static final function escapeString (S $objString) {
        // Return
        return new S (self::$objConnection
        ->real_escape_String ($objString));
    }

    /**
     * Throws an SQLException, rendering an SOD (screen of death) with the current SQL string & error;
     *
     * @author Catalin Alexandru Zamfir <catalin.zamfir@raphpframework.ro>
     * @copyright Under the terms of the GNU General Public License v3
     * @version $Id: Database.php 1 2012-10-26 08:27:37Z root $
     */
    protected static final function renderSQLScreenOfDeath () {
        // Erase
        self::eraseTransaction ();

        // Throws
        throw new SQLException ('## SQL:' . _SP . self::$objSQLSaved .
        _SP . '## Error:' . _SP . mysqli_error (self::$objConnection));
    }
}
?>
