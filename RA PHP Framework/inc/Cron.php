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
 * Returns a instance of MT Dowling's Cron Expression;
 *
 * @author Catalin Alexandru Zamfir <catalin.zamfir@raphpframework.ro>
 * @copyright Under the terms of the GNU General Public License v3
 * @version $Id: Cron.php 1 2012-10-26 08:27:37Z root $
 */
final class Cron extends Template {
    /**
     * Return an instance of Cron Expression @
     * https://github.com/mtdowling/cron-expression;
     *
     * @author Catalin Alexandru Zamfir <catalin.zamfir@raphpframework.ro>
     * @copyright Under the terms of the GNU General Public License v3
     * @version $Id: Cron.php 1 2012-10-26 08:27:37Z root $
     */
    public static final function getCronInstance (S $objCron) {
        // Require #01
        require_once Architecture
        ::pathTo (Architecture::getRoot (),
        PLUGIN_DIR, 'php_cron/FieldInterface.php');

        // Require #02
        require_once Architecture
        ::pathTo (Architecture::getRoot (),
        PLUGIN_DIR, 'php_cron/AbstractField.php');

        // Require #03
        require_once Architecture
        ::pathTo (Architecture::getRoot (),
        PLUGIN_DIR, 'php_cron/DayOfMonthField.php');

        // Require #04
        require_once Architecture
        ::pathTo (Architecture::getRoot (),
        PLUGIN_DIR, 'php_cron/DayOfWeekField.php');

        // Require #05
        require_once Architecture
        ::pathTo (Architecture::getRoot (),
        PLUGIN_DIR, 'php_cron/HoursField.php');

        // Require #06
        require_once Architecture
        ::pathTo (Architecture::getRoot (),
        PLUGIN_DIR, 'php_cron/MinutesField.php');

        // Require #07
        require_once Architecture
        ::pathTo (Architecture::getRoot (),
        PLUGIN_DIR, 'php_cron/MonthField.php');

        // Require #08
        require_once Architecture
        ::pathTo (Architecture::getRoot (),
        PLUGIN_DIR, 'php_cron/YearField.php');

        // Require #09
        require_once Architecture
        ::pathTo (Architecture::getRoot (),
        PLUGIN_DIR, 'php_cron/FieldFactory.php');

        // Require #10
        require_once Architecture
        ::pathTo (Architecture::getRoot (),
        PLUGIN_DIR, 'php_cron/CronExpression.php');

        // Return
        return Cron\CronExpression
        ::factory ((string) $objCron);
    }
}
?>