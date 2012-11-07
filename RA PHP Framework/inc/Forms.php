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
 * Provides a generic way to define forms and work with them;
 *
 * @author Catalin Alexandru Zamfir <catalin.zamfir@raphpframework.ro>
 * @copyright Under the terms of the GNU General Public License v3
 * @version $Id: Forms.php 1 2012-10-26 08:27:37Z root $
 */
class Forms extends Database {
    /**
     * Containers of flags, posts, SQL update fields & ids, errors and mroe;
     *
     * @author Catalin Alexandru Zamfir <catalin.zamfir@raphpframework.ro>
     * @copyright Under the terms of the GNU General Public License v3
     * @version $Id: Forms.php 1 2012-10-26 08:27:37Z root $
     */
    private static $objFormStarted = NULL;
    private static $objPassed = NULL;
    private static $objFormInFieldSet = NULL;
    private static $objPOSTToSession = NULL;
    private static $objSQLUpdateTable = NULL;
    private static $objSQLUpdateField  = NULL;
    private static $objSQLUpdateId = NULL;
    private static $objSQLUpdateCondition = NULL;
    private static $objSQLData = NULL;
    private static $objImageTimestampPrefix = NULL;
    private static $objErrors = NULL;
    private static $objSQLUpdateFields  = NULL;
    private static $objExtraSQLData = NULL;
    private static $objFormDataContainer = NULL;
    private static $objDataContainer = NULL;
    private static $objDataCountInput = NULL;
    private static $objDataToForm = NULL;
    private static $objOptGroupOpen = NULL;
    private static $objOpenedSelectName = NULL;
    private static $objHooks = NULL;
    private static $objMappings = NULL;

    /**
     * Constructs the forms, setting needed requirements, mappings and POST -> SESSION conversion;
     *
     * @author Catalin Alexandru Zamfir <catalin.zamfir@raphpframework.ro>
     * @copyright Under the terms of the GNU General Public License v3
     * @version $Id: Forms.php 1 2012-10-26 08:27:37Z root $
     */
    public function __construct () {
        // Containers (form data)
        self::$objErrors = new A;
        self::$objSQLUpdateFields = new A;
        self::$objExtraSQLData = new A;

        // Containers (input data)
        self::$objDataContainer = new A;
        self::$objFormDataContainer = new A;
        self::$objDataToForm = new I (0);
        self::$objDataCountInput = new I (0);
        self::$objPOSTToSession = new I (0);
        self::$objFormInFieldSet = new I (0);
        self::$objFormStarted = new I (0);
        self::$objPassed = new I (1);
        self::$objOptGroupOpen = new I (0);
        self::$objSQLUpdateTable = new S (_NONE);
        self::$objSQLUpdateField = new S (_NONE);
        self::$objSQLUpdateCondition = new S (_NONE);
        self::$objSQLData = new A;
        self::$objHooks = new A;

        // Set mappings (methods)
        self::$objMappings = new A;

        // Set each
        self::$objMappings->offsetSet ('setAction', new S ('action'));
        self::$objMappings->offsetSet ('setEnctype', new S ('enctype'));
        self::$objMappings->offsetSet ('setTarget', new S ('target'));
        self::$objMappings->offsetSet ('setId', new S ('id'));
        self::$objMappings->offsetSet ('setMask', new S ('mask'));
        self::$objMappings->offsetSet ('setClass', new S ('class'));
        self::$objMappings->offsetSet ('setTitle', new S ('title'));
        self::$objMappings->offsetSet ('setStyle', new S ('style'));
        self::$objMappings->offsetSet ('setTextDirection', new S ('dir'));
        self::$objMappings->offsetSet ('setAccessKey', new S ('accesskey'));
        self::$objMappings->offsetSet ('setTabIndex', new S ('tabindex'));
        self::$objMappings->offsetSet ('setLanguage', new S ('lang'));
        self::$objMappings->offsetSet ('setOnSubmit', new S ('onsubmit'));
        self::$objMappings->offsetSet ('setOnReset', new S ('onreset'));
        self::$objMappings->offsetSet ('setOnClick', new S ('onclick'));
        self::$objMappings->offsetSet ('setOnDblClick', new S ('ondblclick'));
        self::$objMappings->offsetSet ('setOnMouseDown', new S ('onmousedown'));
        self::$objMappings->offsetSet ('setOnMouseUp', new S ('onmouseup'));
        self::$objMappings->offsetSet ('setOnMouseOver', new S ('onmouseover'));
        self::$objMappings->offsetSet ('setOnMouseMove', new S ('onmousemove'));
        self::$objMappings->offsetSet ('setOnMouseOut', new S ('onmouseout'));
        self::$objMappings->offsetSet ('setOnKeyPress', new S ('onkeypress'));
        self::$objMappings->offsetSet ('setOnKeyDown', new S ('onkeydown'));
        self::$objMappings->offsetSet ('setOnKeyUp', new S ('onkeyup'));
        self::$objMappings->offsetSet ('setOnBlur', new S ('onblur'));
        self::$objMappings->offsetSet ('setOnFocus', new S ('onfocus'));
        self::$objMappings->offsetSet ('setOnChange', new S ('onchange'));
        self::$objMappings->offsetSet ('setInputType', new S ('type'));
        self::$objMappings->offsetSet ('setValue', new S ('value'));
        self::$objMappings->offsetSet ('setSize', new S ('size'));
        self::$objMappings->offsetSet ('setAcceptFileType', new S ('accept'));
        self::$objMappings->offsetSet ('setMaxLength', new S ('maxlength'));
        self::$objMappings->offsetSet ('setRegExpType', new S ('RegExpType'));
        self::$objMappings->offsetSet ('setJSRegExpReplace', new S ('JsRegExp'));
        self::$objMappings->offsetSet ('setRegExpErrMsg', new S ('RegExpErrMsg'));
        self::$objMappings->offsetSet ('setPHPRegExpCheck', new S ('RegExpCheck'));
        self::$objMappings->offsetSet ('setRegExpErrMsg', new S ('RegExpErrMsg'));
        self::$objMappings->offsetSet ('setRegExpErrMsg', new S ('RegExpErrMsg'));
        self::$objMappings->offsetSet ('setImageSource', new S ('src'));
        self::$objMappings->offsetSet ('setImageAlternative', new S ('alt'));
        self::$objMappings->offsetSet ('setRows', new S ('rows'));
        self::$objMappings->offsetSet ('setColumns', new S ('cols'));
        self::$objMappings->offsetSet ('setTableName', new S ('SQL_Table'));
        self::$objMappings->offsetSet ('setTableJoinOn', new S ('SQL_Join_On'));
        self::$objMappings->offsetSet ('setTableMapping', new S ('SQL_Save_Into_Table'));
        self::$objMappings->offsetSet ('setUpdateId', new S ('SQL_Update_Id'));
        self::$objMappings->offsetSet ('setUpdateWhere', new S ('SQL_Condition'));
        self::$objMappings->offsetSet ('setUpdateField', new S ('SQL_Update_Field'));
        self::$objMappings->offsetSet ('setSQLAction', new S ('SQL_Update_Or_Insert'));
        self::$objMappings->offsetSet ('setRedirect', new S ('Redirect_If_Ok'));
        self::$objMappings->offsetSet ('setUploadType', new S ('accepted_mime_types'));
        self::$objMappings->offsetSet ('setUploadErrMsg', new S ('upload_error_message'));
        self::$objMappings->offsetSet ('setUploadDirectory', new S ('upload_dir'));
        self::$objMappings->offsetSet ('setUploadImageResize', new S ('upload_resize_img'));
        self::$objMappings->offsetSet ('setExtra', new S ('extra'));
        self::$objMappings->offsetSet ('setRelation', new S ('relation'));
        self::$objMappings->offsetSet ('setFieldset', new S ('fieldset'));
        self::$objMappings->offsetSet ('setLabel', new S ('label'));
        self::$objMappings->offsetSet ('setCheckForUnique', new S ('check_for_unique'));
        self::$objMappings->offsetSet ('setCheckForEmpty', new S ('check_for_empty'));
        self::$objMappings->offsetSet ('setTinyMCETextarea', new S ('tinyMCETextarea'));
        self::$objMappings->offsetSet ('setContainerDiv', new S ('container_div'));
        self::$objMappings->offsetSet ('setMultiple', new S ('multiple'));
        self::$objMappings->offsetSet ('setDisabled', new S ('disabled'));
        self::$objMappings->offsetSet ('setFileController', new S ('RA_File_Controller'));
        self::$objMappings->offsetSet ('setMPTTRemoveUnique', new S ('mptt_remove_unique'));
        self::$objMappings->offsetSet ('setReadOnly', new S ('readonly'));
        self::$objMappings->offsetSet ('setChecked', new S ('checked'));
        self::$objMappings->offsetSet ('setSelected', new S ('selected'));
        self::$objMappings->offsetSet ('setTextProxy', new S ('placeholder'));

        // Check
        if (!empty ($_POST)) {
            // Convert
            $_SESSION['POST'] = $_POST;

            // Configure
            $objConfig = HTMLPurifier_Config::createDefault ();

            // Set
            $objConfig->set ('Cache.SerializerPath', (string)
            Architecture::pathTo (Architecture::getStorage (),
            UPLOAD_DIR, TEMP_DIR));

            // Set
            $objPurify = new HTMLPurifier ($objConfig);

            // Foreach
            foreach ($_SESSION['POST'] as $objK => $objV) {
                // Switch
                switch (is_array ($objV)) {
                    case TRUE:
                        // Foreach
                        foreach ($objV as $objX => $objY) {
                            // Modify
                            $_SESSION['POST'][$objK][$objX] =
                            Database::escapeString (_S ($objPurify
                            ->purify ((string) $objY))
                            ->trimLeft ()->trimRight ());
                        }
                        // BK;
                        break;

                    default:
                        // Modify
                        $_SESSION['POST'][$objK] =
                        Database::escapeString (_S ($objPurify
                        ->purify ((string) $objV))
                        ->trimLeft ()->trimRight ());
                        break;
                }
            }

            // Pased
            self::$objPOSTToSession =
            new I (1);
        } else {
            // Check
            if (self::checkPOST ()
            ->toBoolean () == TRUE) {
                // Set
                self::$objPOSTToSession =
                new I (1);
            } else {
                // Set
                self::$objPOSTToSession =
                new I (0);
            }
        }

        // Check
        if (!empty ($_POST)) {
            // Unset
            unset ($_POST);
        }
    }

    /**
     * Set error message on a given input name;
     *
     * @author Catalin Alexandru Zamfir <catalin.zamfir@raphpframework.ro>
     * @copyright Under the terms of the GNU General Public License v3
     * @version $Id: Forms.php 1 2012-10-26 08:27:37Z root $
     */
    public static final function setErrorOnInput (S $inputWithError, S $errorMessage) {
        // Set
        self::$objPassed = new I (0);
        self::$objErrors[$inputWithError] =
        $errorMessage;

        // Return
        return _new (__CLASS__);
    }

    /**
     * Set extra keys to update, with the given value;
     *
     * @author Catalin Alexandru Zamfir <catalin.zamfir@raphpframework.ro>
     * @copyright Under the terms of the GNU General Public License v3
     * @version $Id: Forms.php 1 2012-10-26 08:27:37Z root $
     */
    public static final function setExtraUpdateData (S $objKey, S $objVar) {
        // Set
        self::$objExtraSQLData[$objKey] = $objVar;

        // Return
        return _new (__CLASS__);
    }

    /**
     * Should be same as __CALL!
     */
    public static final function __CALLSTATIC ($nameOfHook, $argumentsOfHook) {
        // Check
        if (self::$objMappings
        ->offsetExists ($nameOfHook)) {
            // Return
            return self::setAttribute (self::$objMappings
            ->offsetGet ($nameOfHook),
            $argumentsOfHook[0]);
        } else {
            // Return
            return parent::__CALLSTATIC ($nameOfHook, $argumentsOfHook);
        }
    }

    /**
     * Upstream method __CALL to methods defined here or not;
     *
     * @author Catalin Alexandru Zamfir <catalin.zamfir@raphpframework.ro>
     * @copyright Under the terms of the GNU General Public License v3
     * @version $Id: Forms.php 1 2012-10-26 08:27:37Z root $
     */
    public function __CALL ($nameOfHook, $argumentsOfHook) {
        // Check
        if (self::$objMappings
        ->offsetExists ($nameOfHook)) {
            // Return
            return self::setAttribute (self::$objMappings
            ->offsetGet ($nameOfHook), $argumentsOfHook[0]);
        } else {
            // Return
            return parent::__CALL ($nameOfHook, $argumentsOfHook);
        }

    }

    /**
     * Set form submitting method (POST or GET) & action togheter;
     *
     * @author Catalin Alexandru Zamfir <catalin.zamfir@raphpframework.ro>
     * @copyright Under the terms of the GNU General Public License v3
     * @version $Id: Forms.php 1 2012-10-26 08:27:37Z root $
     */
    public static final function setMethod (S $objFormAttributeVar) {
        // Switch
        switch ($objFormAttributeVar) {
            case 'POST':
            case 'GET':
                // Set
                self::setAttribute (new S ('action'),
                Location::rewriteTo ());

                // Return
                return self::setAttribute (new S ('method'),
                $objFormAttributeVar->toLower ());
                break;

            default:
                // Throws
                throw new FormMethodNotSupportedException;
                break;
        }
    }

    /**
     * Sets the name of the current input and, by DOM specs, it's id automatically, as it's unique as the name;
     *
     * @author Catalin Alexandru Zamfir <catalin.zamfir@raphpframework.ro>
     * @copyright Under the terms of the GNU General Public License v3
     * @version $Id: Forms.php 1 2012-10-26 08:27:37Z root $
     */
    public static final function setName (S $objFormAttributeVar) {
        // Set id to whatever name is
        self::setAttribute (new S ('id'),
        $objFormAttributeVar->makeCopyObject ()
        ->doToken (_DOT, _U));

        // Return
        return self::setAttribute (new S ('name'),
        $objFormAttributeVar->makeCopyObject ()
        ->doToken (_DOT, _U));
    }

    /**
     * Sets an input message, a kind of information associated to be shown to the user in a friendly, developer-definer manor;
     *
     * @author Catalin Alexandru Zamfir <catalin.zamfir@raphpframework.ro>
     * @copyright Under the terms of the GNU General Public License v3
     * @version $Id: Forms.php 1 2012-10-26 08:27:37Z root $
     */
    public static final function setInputInfo (S $objFormAttributeVar) {
        // Return
        return self::setAttribute (new
        S ('input_info_msg'), $objFormAttributeVar
        ->entityEncode (ENT_QUOTES));
    }

    /**
     * Sets a tooltip to be shown with the input;
     *
     * @author Catalin Alexandru Zamfir <catalin.zamfir@raphpframework.ro>
     * @copyright Under the terms of the GNU General Public License v3
     * @version $Id: Forms.php 1 2012-10-26 08:27:37Z root $
     */
    public static final function setToolTip (S $objFormAttributeVar) {
        // Return
        return self::setAttribute (new
        S ('tooltip'), $objFormAttributeVar);
    }

    /**
     * Sets an Yes/No combo-box, easier than rendering it ourselves each time we need it;
     *
     * @author Catalin Alexandru Zamfir <catalin.zamfir@raphpframework.ro>
     * @copyright Under the terms of the GNU General Public License v3
     * @version $Id: Forms.php 1 2012-10-26 08:27:37Z root $
     */
    public static final function setYesNoOptions (B $objFormAttributeVar) {
        // Check
        if ($objFormAttributeVar->toBoolean () == TRUE) {
            // Set
            self::setInputType (new S ('option'))
            ->setName (new S ('no'))
            ->setValue (new S ('N'))
            ->setLabel (_T ('No'))
            ->setInputType (new S ('option'))
            ->setName (new S ('yes'))
            ->setValue (new S ('Y'))
            ->setLabel (_T ('Yes'));
        }

        // Return
        return _new (__CLASS__);
    }

    /**
     * Check if  the form has had any errors while processing;
     *
     * @author Catalin Alexandru Zamfir <catalin.zamfir@raphpframework.ro>
     * @copyright Under the terms of the GNU General Public License v3
     * @version $Id: Forms.php 1 2012-10-26 08:27:37Z root $
     */
    public static final function checkFormHasErrors () {
        // Return
        return self::checkPOST ()->toBoolean () == TRUE && self::$objErrors
        ->doCount ()->toInt () == 0 ? new B (FALSE) : new B (TRUE);
    }

    /**
     * Sets form endings and executes, via its argument, if true;
     *
     * @author Catalin Alexandru Zamfir <catalin.zamfir@raphpframework.ro>
     * @copyright Under the terms of the GNU General Public License v3
     * @version $Id: Forms.php 1 2012-10-26 08:27:37Z root $
     */
    public static final function setFormEndAndExecute (B $objFormAttributeVar) {
        // Check
        if ($objFormAttributeVar
        ->toBoolean () == TRUE) {
            // Check
            if (!(self::$objFormDataContainer
            ->offsetExists ('method'))) {
                // Set
                self::$objFormDataContainer
                ->offsetSet ('method', new
                S ('post'));
            }

            // Check
            if (!(self::$objFormDataContainer
            ->offsetExists ('enctype'))) {
                // Set
                self::$objFormDataContainer
                ->offsetSet ('enctype', new
                S ('multipart/form-data'));
            }

            // Check
            if (!(self::$objFormDataContainer
            ->offsetExists ('SQL_Update_Or_Insert'))) {
                // Set
                self::$objFormDataContainer
                ->offsetSet ('SQL_Update_Or_Insert',
                new S ('update'));
            }

            // Update and check
            self::updateOrInsert ();
            self::setFormHeader ();

            // Template
            $objTp = new
            Path (Architecture
            ::pathTo (FORM_TP_DIR,
            'Forms-Contents.tp'));

            // Check
            if (!self::$objFormDataContainer
            ->offsetExists ('SQL_Operations_On_Input')) {
                // Check
                if (self::$objFormDataContainer
                ->offsetExists ('SQL_Update_Or_Insert')) {
                    // Foreach
                    foreach (self::$objDataContainer
                    as $objK => $objV) {
                        // Check
                        if ($objV
                        ->offsetExists ('name')) {
                            // Set
                            self::$objFormDataContainer
                            ->offsetSet ('SQL_Operations_On_Input',
                            $objV['name']);

                            // BK;
                            break;
                        }
                    }
                }
            }

            // Foreach
            foreach (self::$objDataContainer
            as $objK => $objV) {

                // If we have files inputs
                self::renderDropZone (new I ($objK));

                // Check
                if (self::checkPOST ()
                ->toBoolean () == TRUE) {
                    // Set
                    self::autoOptionsOnPOST (new I ($objK));
                    self::checkboxAndRadioSQLOperations (new I ($objK));
                    self::setInputsFromSession (new I ($objK));

                    // Check
                    if (($objV->offsetExists ('check_for_empty')) &&
                    $objV->offsetGet ('check_for_empty')->toBoolean ()) {
                        // Check
                        if ($_SESSION['POST']
                        ->offsetExists ($objV['name'])) {
                            // Check
                            if ($_SESSION['POST'][$objV['name']]
                            ->toLength ()->toInt () == 0) {
                                // Throws
                                self::setErrorOnInput ($objV['name'],
                                _T ('Empty field!'));
                            }
                        }
                    }

                    // Check
                    if (($objV->offsetExists ('check_for_unique')) &&
                    $objV->offsetGet ('check_for_unique')->toBoolean ()) {
                        // Check
                        if ($_SESSION['POST']
                        ->offsetExists ($objV['name'])) {
                            // Check
                            if (self::$objFormDataContainer
                            ->offsetGet ('SQL_Update_Or_Insert') ==
                            new S ('update')) {
                                // Check
                                if (self::doQuery (_QS ('doSELECT')
                                ->doToken ('%condition', new S ('WHERE %what = "%Id" LIMIT 1'))
                                ->doToken ('%what', $objV['name'])
                                ->doToken ('%table', self::$objFormDataContainer['SQL_Table'])
                                ->doToken ('%Id', $_SESSION['POST'][$objV['name']]))
                                ->offsetGet (0)->offsetGet ($objV['name']) !=
                                $_SESSION['POST'][$objV['name']]) {

                                    // Check
                                    if (self::doQuery (_QS ('doSELECT')
                                    ->doToken ('%condition', new S ('WHERE %what = "%Id" LIMIT 1'))
                                    ->doToken ('%what', $objV['name'])
                                    ->doToken ('%table', self::$objFormDataContainer['SQL_Table'])
                                    ->doToken ('%Id', $_SESSION['POST'][$objV['name']]))
                                    ->doCount ()->toInt () != 0) {
                                        // Set
                                        self::setErrorOnInput ($objV['name'],
                                        _T ('Not unique field!'));
                                    }
                                } else {
                                    // Check
                                    if (self::$objSQLUpdateId ==
                                    self::getAutoIncrement (self::$objFormDataContainer
                                    ->offsetGet ('SQL_Table'))) {

                                        // Check
                                        if (self::doQuery (_QS ('doSELECT')
                                        ->doToken ('%condition', new S ('WHERE %what = "%Id" LIMIT 1'))
                                        ->doToken ('%what', $objV['name'])
                                        ->doToken ('%table', self::$objFormDataContainer['SQL_Table'])
                                        ->doToken ('%Id', $_SESSION['POST'][$objV['name']]))
                                        ->doCount ()->toInt () != 0) {
                                            // Set
                                            self::setErrorOnInput ($objV['name'],
                                            _T ('Not unique field!'));
                                        }
                                    }
                                }
                            }
                        }
                    }

                } else if (self::$objSQLData
                ->offsetExists (0)) {
                    // Check
                    if ($objV
                    ->offsetExists ('name')) {
                        // Check
                        if (self::$objSQLUpdateFields
                        ->inArray ($objV['name'])) {
                            // Set
                            self::setInputFromQuery (new I ($objK));
                        }
                    }

                    // Check
                    if ($objV['type'] == new S ('option')) {
                        // Check
                        if ($objV
                        ->offsetExists ('value')) {
                            // Check
                            if ($objV['value'] ==
                            self::$objSQLData[0][$objV['bound_to']]) {
                                // Set
                                $objV['selected'] = new B (TRUE);
                            }
                        }
                    }
                }

                // Check
                if ($objV
                ->offsetExists ('mptt_remove_unique')) {
                    // Remove identifier
                    $objV['value'] = Hierarchy
                    ::mpttRemoveUnique ($objV['value']);
                }

                // HTMLize
                self::generateHTML ($objV['type'],
                $objV, $objTp);
            }

            // End
            self::setFormFooter ();
            self::$objDataContainer = new A;
            self::$objDataCountInput = new I (0);
            self::$objDataToForm = new I (0);
            self::$objFormDataContainer = new A;
            self::$objExtraSQLData = new A;
            self::$objSQLData = new A;
            self::$objHooks = new A;

            // Return
            return _new (__CLASS__);
        } else {
            // Return
            return _new (__CLASS__);
        }
    }

    /**
     * Register a hook to be executed at specific times inside the form generating code;
     *
     * @author Catalin Alexandru Zamfir <catalin.zamfir@raphpframework.ro>
     * @copyright Under the terms of the GNU General Public License v3
     * @version $Id: Forms.php 1 2012-10-26 08:27:37Z root $
     */
    public static final function registerHook (S $actionFunctionOfUser, S $whereToRegister) {
        // Set
        self::$objHooks[$whereToRegister][] =
        $actionFunctionOfUser;

        // Return
        return _new (__CLASS__);
    }

    /**
     * Set a _POST key to some specific value;
     *
     * @author Catalin Alexandru Zamfir <catalin.zamfir@raphpframework.ro>
     * @copyright Under the terms of the GNU General Public License v3
     * @version $Id: Forms.php 1 2012-10-26 08:27:37Z root $
     */
    public static final function setPOST (S $nameOfKey, S $objKeyContent) {
        // Set
        $_SESSION['POST'][$nameOfKey] =
        $objKeyContent;
    }

    /**
     * Unset a _POST key;
     *
     * @author Catalin Alexandru Zamfir <catalin.zamfir@raphpframework.ro>
     * @copyright Under the terms of the GNU General Public License v3
     * @version $Id: Forms.php 1 2012-10-26 08:27:37Z root $
     */
    public static final function unsetPOST (S $nameOfKey = NULL) {
        // Check
        if ($nameOfKey != NULL) {
            // Check
            if ($_SESSION['POST']
            ->offsetExists ($nameOfKey)) {
                // Unset
                $_SESSION['POST']
                ->offsetUnset ($nameOfKey);
            }
        } else {
            // Unset
            unset ($_SESSION['POST']);
        }
    }

    /**
     * Get a _POST key;
     *
     * @author Catalin Alexandru Zamfir <catalin.zamfir@raphpframework.ro>
     * @copyright Under the terms of the GNU General Public License v3
     * @version $Id: Forms.php 1 2012-10-26 08:27:37Z root $
     */
    public static final function getPOST (S $formInputToGet = NULL) {
        // Switch
        switch ($formInputToGet == NULL) {
            case TRUE:
                // Return
                return $_SESSION['POST'];
                break;

            case FALSE:
                // Return
                return $_SESSION['POST'][$formInputToGet];
                break;
        }
    }

    /**
     * Check a given _POST key or the whole _POST container;
     *
     * @author Catalin Alexandru Zamfir <catalin.zamfir@raphpframework.ro>
     * @copyright Under the terms of the GNU General Public License v3
     * @version $Id: Forms.php 1 2012-10-26 08:27:37Z root $
     */
    public static final function checkPOST (S $objVarToCheck = NULL) {
        // Switch
        switch ($objVarToCheck == NULL) {
            case TRUE:
                // Return
                return new B (isset ($_SESSION['POST']) &&
                $_SESSION['POST']->doCount ()->toInt () != 0);
                break;

            case FALSE:
                // Return
                return new B (isset ($_SESSION['POST'][$objVarToCheck]));
                break;
        }
    }

    /**
     * Sets an attribute for the current input;
     *
     * @author Catalin Alexandru Zamfir <catalin.zamfir@raphpframework.ro>
     * @copyright Under the terms of the GNU General Public License v3
     * @version $Id: Forms.php 1 2012-10-26 08:27:37Z root $
     */
    private static final function setAttribute (S $objFormAttributeKey, M $objFormAttributeVar) {
        // Switch
        switch ($objFormAttributeKey) {
            case 'method':
            case 'action':
            case 'fieldset':
            case 'SQL_Table':
            case 'SQL_Join_On':
            case 'SQL_Save_Into_Table':
            case 'SQL_Condition':
            case 'SQL_Update_Or_Insert':
            case 'Redirect_If_Ok':
            case 'accepted_mime_types':
            case 'upload_error_message':
            case 'upload_dir':
            case 'upload_resize_img':
                // Set
                self::$objFormDataContainer
                ->offsetSet ($objFormAttributeKey,
                $objFormAttributeVar);
                break;

            // Id
            case 'SQL_Update_Id':
                // Set
                self::$objSQLUpdateId = $objFormAttributeVar;
                break;

            // Field
            case 'SQL_Update_Field':
                // Set
                self::$objSQLUpdateField = $objFormAttributeVar;
                break;

            // Types
            case 'type':
                // Set
                self::$objDataToForm->setInt (1);
                self::$objDataCountInput
                ->setInt (self::$objDataContainer
                ->doCount ()->toInt ());

                // Check
                if (self::$objDataContainer
                ->offsetExists ($objI = self::$objDataCountInput
                ->toInt () - 1)) {
                    // Check
                    if (self::$objDataContainer[$objI]
                    ->offsetExists ('type')) {
                        // Check
                        if (self::$objDataContainer[$objI]['type'] == 'option'    ||
                        self::$objDataContainer[$objI]['type'] == 'optgroup') {
                            // Check
                            if ($objFormAttributeVar == 'optgroup') {
                                // Set
                                self::$objDataContainer[self::$objDataCountInput] = new A (array ('type'
                                => new S ('optgroup_ending'), 'name' => new S ('optgroup_ending')));

                                // Set
                                self::$objOptGroupOpen->setInt (1);
                                self::$objDataCountInput->setInt (self::$objDataContainer
                                ->doCount ()->toInt ());
                            }

                            // Check
                            if ($objFormAttributeVar != 'option' &&
                            $objFormAttributeVar != 'optgroup' &&
                            $objFormAttributeVar != 'optgroup_ending') {
                                // Check
                                if (self::$objOptGroupOpen
                                ->toInt () == 1) {
                                    // Set
                                    self::$objDataContainer[self::$objDataCountInput] =
                                    new A (array ('type' => new S ('optgroup_ending'),
                                    'name' => new S ('optgroup_ending')));

                                    // Set
                                    self::$objOptGroupOpen->setInt (1);
                                    self::$objDataCountInput->setInt (self::$objDataContainer
                                    ->doCount ()->toInt ());
                                }

                                // Set
                                self::$objDataContainer[self::$objDataCountInput] = new A (array ('type' =>
                                new S ('select_ending'), 'name' => new S ('select_ending')));

                                // Set
                                self::$objDataCountInput->setInt (self::$objDataContainer
                                ->doCount ()->toInt ());
                            }
                        }
                    }
                }

                // Check
                if (self::$objDataContainer
                ->offsetExists ($objI)) {
                    // Check
                    if (self::$objDataContainer[$objI]
                    ->offsetExists ('type')) {
                        // Check
                        if (self::$objDataContainer[$objI]['type'] == 'select') {
                            // Check
                            if ($objFormAttributeVar != 'option' &&
                            $objFormAttributeVar != 'optgroup' &&
                            $objFormAttributeVar != 'optgroup_ending') {
                                // Set
                                self::$objDataContainer[self::$objDataCountInput] = new A (array ('type' =>
                                new S ('select_ending'), 'name' => new S ('select_ending')));

                                // Set
                                self::$objDataCountInput->setInt (self::$objDataContainer
                                ->doCount ()->toInt ());
                            }
                        }
                    }
                }

                // Check
                self::$objDataContainer->offsetExists (self::$objDataCountInput)    ?
                self::$objDataContainer->offsetSet (self::$objDataCountInput, _NONE):
                self::$objDataContainer->offsetSet (self::$objDataCountInput, new A);

                // Set
                self::$objDataContainer
                ->offsetGet (self::$objDataCountInput)
                ->offsetSet ($objFormAttributeKey, $objFormAttributeVar);
                break;

            case 'multiple':
                // Check
                if (self::$objDataToForm
                ->toInt () == 1) {
                    // Check
                    if (self::$objDataContainer
                    ->offsetGet (self::$objDataCountInput)
                    ->offsetGet ('type') == new S ('select')) {
                        // Set
                        self::$objDataContainer
                        ->offsetGet (self::$objDataCountInput)
                        ->offsetSet ($objFormAttributeKey,
                        $objFormAttributeVar);
                    } else {
                        // Throws
                        throw new CannotSetMultipleOnNonSELECTInputException;
                    }
                } else {
                    // Throws
                    throw new CannotSetMultipleOnNonSELECTInputException;
                }
                // BK;
                break;

            case 'accept':
                // Check
                if (self::$objDataToForm
                ->toInt () == 1) {
                    // Check
                    if (self::$objDataContainer
                    ->offsetGet (self::$objDataCountInput)
                    ->offsetGet ('type') == new S ('file')) {
                        // Set
                        self::$objDataContainer
                        ->offsetGet (self::$objDataCountInput)
                        ->offsetSet ($objFormAttributeKey,
                        $objFormAttributeVar);
                    } else {
                        // Throws
                        throw new CannotSetAcceptOnNonFileInputException;
                    }
                } else {
                    // Attribute
                    self::$objFormDataContainer
                    ->offsetSet ($objFormAttributeKey,
                    $objFormAttributeVar);
                }
                // BK;
                break;

            case 'alt':
                // Check
                if (self::$objDataToForm->toInt () == 1) {
                    // Check
                    if (self::$objDataContainer
                    ->offsetGet (self::$objDataCountInput)
                    ->offsetGet ('type') == new S ('image')) {
                        // Set
                        self::$objDataContainer
                        ->offsetGet (self::$objDataCountInput)
                        ->offsetSet ($objFormAttributeKey,
                        $objFormAttributeVar);
                    } else {
                        // Throws
                        throw new CannotSetAltOnNonImageInputException;
                    }
                } else {
                    // Throws
                    throw new CannotSetAltOnNonImageInputException;
                }
                // BK;
                break;

            case 'src':
                // Check
                if (self::$objDataToForm->toInt () == 1) {
                    // Check
                    if (self::$objDataContainer
                    ->offsetGet (self::$objDataCountInput)
                    ->offsetGet ('type') == new S ('image')) {
                        // Set
                        self::$objDataContainer
                        ->offsetGet (self::$objDataCountInput)
                        ->offsetSet ($objFormAttributeKey,
                        $objFormAttributeVar);
                    } else {
                        // Throws
                        throw new CannotSetSrcOnNonImageInputException;
                    }
                } else {
                    // Throws
                    throw new CannotSetSrcOnNonImageInputException;
                }
                // BK;
                break;

            case 'checked':
                // Check
                if (self::$objDataToForm->toInt () == 1) {
                    // Check
                    if (self::$objDataContainer
                    ->offsetGet (self::$objDataCountInput)
                    ->offsetGet ('type') == new S ('radio')) {
                        // Set
                        self::$objDataContainer
                        ->offsetGet (self::$objDataCountInput)
                        ->offsetSet ($objFormAttributeKey,
                        $objFormAttributeVar);
                    } else if (self::$objDataContainer
                    ->offsetGet (self::$objDataCountInput)
                    ->offsetGet ('type') == new S ('checkbox')) {
                        // Set
                        self::$objDataContainer
                        ->offsetGet (self::$objDataCountInput)
                        ->offsetSet ($objFormAttributeKey,
                        $objFormAttributeVar);
                    } else {
                        // Throws
                        throw new CannotSetCheckedOnNonRadioCheckboxInputException;
                    }
                } else {
                    // Throws
                    throw new CannotSetCheckedOnNonRadioCheckboxInputException;
                }
                // BK;
                break;

            case 'name':
                // Check
                if (self::$objDataToForm
                ->toInt () == 1) {
                    // Check
                    if (self::$objDataContainer
                    ->offsetGet (self::$objDataCountInput)
                    ->offsetGet ('type') == new S ('select')) {
                        // Set
                        self::$objOpenedSelectName =
                        $objFormAttributeVar;

                        // Set
                        self::$objDataContainer
                        ->offsetGet (self::$objDataCountInput)
                        ->offsetSet ($objFormAttributeKey, $objFormAttributeVar);
                    } else if (self::$objDataContainer->offsetGet (self::$objDataCountInput)
                    ->offsetGet ('type') == new S ('select_ending')) {
                        // Set
                        self::$objOpenedSelectName = NULL;

                        // Set
                        self::$objDataContainer
                        ->offsetGet (self::$objDataCountInput)
                        ->offsetSet ($objFormAttributeKey,
                        $objFormAttributeVar);
                    } else if (self::$objDataContainer
                    ->offsetGet (self::$objDataCountInput)
                    ->offsetGet ('type') == new S ('option')) {
                        // Set
                        self::$objDataContainer
                        ->offsetGet (self::$objDataCountInput)
                        ->offsetSet ('bound_to', self::$objOpenedSelectName);
                    } else {
                        // Set
                        self::$objDataContainer
                        ->offsetGet (self::$objDataCountInput)
                        ->offsetSet ($objFormAttributeKey,
                        $objFormAttributeVar);
                    }
                } else {
                    // Set
                    self::$objFormDataContainer
                    ->offsetSet ($objFormAttributeKey,
                    $objFormAttributeVar);
                }
                // BK;
                break;

            case 'RA_File_Controller':
                // Check
                if (self::$objDataToForm
                ->toInt () == 1) {
                    // Check
                    if (self::$objDataContainer
                    ->offsetGet (self::$objDataCountInput)
                    ->offsetGet ('type') == new S ('file')) {
                        // Set
                        self::$objDataContainer
                        ->offsetGet (self::$objDataCountInput)
                        ->offsetSet ($objFormAttributeKey, $objFormAttributeVar);
                    } else {
                        // Throws
                        throw new CannotSetFileControllerOnNonFileInputException;
                    }
                }
                // BK;
                break;

            case 'label':
                // Set
                self::$objDataContainer
                ->offsetGet (self::$objDataCountInput)
                ->offsetSet ($objFormAttributeKey, $objFormAttributeVar);

                // Check
                if (self::$objDataContainer
                ->offsetGet (self::$objDataCountInput)
                ->offsetGet ('type') == new S ('option')) {
                    // Check
                    if (!(self::$objDataContainer
                    ->offsetGet (self::$objDataCountInput)
                    ->offsetExists ('value'))) {
                        // Set
                        self::$objDataContainer
                        ->offsetGet (self::$objDataCountInput)
                        ->offsetSet ('value', $objFormAttributeVar);
                    }
                }
                // BK;
                break;

            case 'tinyMCETextarea':
                // Set
                $objPath = Architecture::pathTo (FORM_TP_DIR,
                JAVASCRIPT_DIR)->appendString (_S);

                // Required
                self::manageJSS (new Path ($objPath . 'editor/tiny_mce.js'));
                self::manageJSS (new Path ($objPath . 'editor/tiny_mce_exec.js'));

                // Check
                if (self::$objDataContainer
                ->offsetget (self::$objDataCountInput)
                ->offsetExists (new S ('class'))) {
                    // Set
                    self::$objDataContainer
                    ->offsetGet (self::$objDataCountInput)
                    ->offsetGet (new S ('class'))
                    ->appendString (_SP)
                    ->appendString ('RA_Textarea');
                } else {
                    // Set
                    self::$objDataContainer
                    ->offsetGet (self::$objDataCountInput)
                    ->offsetSet (new S ('class'),
                    new S ('RA_Textarea'));
                }
                // BK;
                break;

            default:
                // Check (short, array form)
                self::$objDataToForm->toInt () == 1 ?
                self::$objDataContainer[self::$objDataCountInput][$objFormAttributeKey] = $objFormAttributeVar :
                self::$objFormDataContainer[$objFormAttributeKey] = $objFormAttributeVar;
                break;
        }

        // Return
        return _new (__CLASS__);
    }

    /**
     * Sets SQL operations for the current form, CRU[d]'ing anything it can;
     *
     * @author Catalin Alexandru Zamfir <catalin.zamfir@raphpframework.ro>
     * @copyright Under the terms of the GNU General Public License v3
     * @version $Id: Forms.php 1 2012-10-26 08:27:37Z root $
     */
    private static final function runSQLOperationsOnForm () {
        // Check
        if (self::$objFormDataContainer
        ->offsetExists ('SQL_Table')) {
            // Check
            if (self::$objSQLUpdateCondition != NULL) {
                // Check
                if (self::doQuery (_S ('SELECT * FROM %table WHERE %condition LIMIT 1')
                ->doToken ('%table', self::$objFormDataContainer['SQL_Table'])
                ->doToken ('%condition',self::$objSQLUpdateCondition))
                ->doCount ()->toInt () == 0) {
                    // Set
                    self::$objFormDataContainer
                    ->offsetSet ('SQL_Update_Or_Insert',
                    new S ('insert'));
                }
            }

            // Update
            if ((self::$objFormDataContainer->offsetExists ('SQL_Update_Or_Insert')) &&
            (self::$objFormDataContainer->offsetGet ('SQL_Update_Or_Insert') == new S ('update'))) {
                // Check
                if (self::$objSQLUpdateCondition != NULL) {
                    // Foreach
                    foreach ($_SESSION['POST'] as $objK => $objV) {
                        // Check
                        if (in_array ($objK, self::$objSQLUpdateFields->toArray ())) {
                            // Query
                            self::doQuery (_S ('UPDATE %table SET %whom = "%what" WHERE %condition')
                            ->doToken ('%table', self::$objSQLUpdateTable)
                            ->doToken ('%whom', $objK)->doToken ('%what', $objV)
                            ->doToken ('%condition', self::$objSQLUpdateCondition));
                        }
                    }

                    // Addition
                    foreach (self::$objExtraSQLData as $objK => $objV) {
                        // Query
                        self::doQuery (_S ('UPDATE %table SET %whom = "%what" WHERE %condition')
                        ->doToken ('%table', self::$objSQLUpdateTable)
                        ->doToken ('%whom', $objK)->doToken ('%what', $objV)
                        ->doToken ('%condition', self::$objSQLUpdateCondition));
                    }
                }
            } else if ((self::$objFormDataContainer->offsetExists ('SQL_Update_Or_Insert')) &&
            (self::$objFormDataContainer->offsetGet ('SQL_Update_Or_Insert') == new S ('insert'))) {
                // Check
                if (self::$objFormDataContainer
                ->offsetExists ('SQL_Table')) {
                    $a = new A;
                    $q = new S ('INSERT INTO %table SET' . _SP);
                    $q->doToken ('%table', self::$objFormDataContainer['SQL_Table']);
                    $tableFields = self::getFields (self::$objFormDataContainer['SQL_Table']);

                    // Foreach
                    foreach ($_SESSION['POST'] as $objK => $objV) {
                        $r = new S ('%whom = "%what"');
                        if (in_array ($objK, $tableFields->toArray ())) {
                            $a[] = $r->doToken ('%whom', $objK)->doToken ('%what', $objV);
                        }
                    }

                    // Foreach
                    foreach (self::$objExtraSQLData as $objK => $objV) {
                        $r = new S ('%whom = "%what"');
                        if (in_array ($objK, $tableFields->toArray ())) {
                            $a[] = $r->doToken ('%whom', $objK)->doToken ('%what', $objV);
                        }
                    }

                    // Append
                    $q->appendString (implode (', ', $a->toArray ()));
                    self::doQuery ($q);
                }
            }
        }
    }

    /**
     * Generates the given input type, alongs with it's attribute, of the given path;
     *
     * @author Catalin Alexandru Zamfir <catalin.zamfir@raphpframework.ro>
     * @copyright Under the terms of the GNU General Public License v3
     * @version $Id: Forms.php 1 2012-10-26 08:27:37Z root $
     */
    private static final function generateHTML (S $inputType, A $inputAttributeArray, Path $objTp) {
        // Check
        if (self::$objPOSTToSession->toInt () == 1) {
            // Check
            if ($inputAttributeArray
            ->offsetExists ('name')) {
                // Check
                if ($inputAttributeArray['name']
                ->toLength ()->toInt () != 0) {
                    // Check (be crazy about it)
                    if (isset ($_SESSION['POST'])) {
                        // Check again
                        if ($_SESSION['POST']
                        ->offsetExists ($inputAttributeArray
                        ->offsetGet ('name'))) {
                            // Check
                            if ($_SESSION['POST']->offsetGet ($inputAttributeArray
                            ->offsetGet ('name'))->toLength ()->toInt () != 0) {
                                // Check
                                if (($inputAttributeArray->offsetExists ('RegExpType')) &&
                                ($inputAttributeArray->offsetExists ('RegExpCheck'))) {
                                    // Switch
                                    switch ($inputAttributeArray
                                    ->offsetGet ('RegExpType')) {
                                        default:
                                            // Check
                                            if (self::getPOST ($inputAttributeArray->offsetGet ('name'))
                                            ->pregMatch ($inputAttributeArray->offsetGet ('RegExpCheck'))
                                            ->doCount ()->toInt () == 0) {
                                                // Set
                                                self::$objErrors->offsetSet ($inputAttributeArray->offsetGet ('name'),
                                                $inputAttributeArray->offsetGet ('RegExpErrMsg'));

                                                // Passed
                                                self::$objPassed->setInt (0);
                                            }

                                            // BK;
                                            break;
                                    }
                                } else {
                                    // Passed
                                    self::$objPassed->setInt (1);
                                }
                            } else {
                                // Passed
                                self::$objPassed->setInt (0);
                            }
                        }
                    }
                }
            }
        } else {
            // Passed
            self::$objPassed->setInt (0);
        }

        // Core
        self::setCoreAttributes ($inputAttributeArray, $objTp);
        self::setCoreInputAttributes ($inputAttributeArray, $objTp);

        // Foreach
        foreach ($inputAttributeArray as $objK => $objV) {
            // Switch
            switch ($inputType) {
                case 'radio':
                case 'checkbox':
                    // Switch
                    switch ($objK) {
                        case 'checked':
                            // Switch
                            switch ($_SESSION['POST']
                            ->offsetExists ($inputAttributeArray
                            ->offsetGet ('name'))) {
                                case TRUE:
                                    // Set
                                    self::setTp ($_SESSION['POST']->offsetGet ($inputAttributeArray
                                    ->offsetGet ('name')), new S ($objK), $objTp);
                                    break;

                                case FALSE:
                                    // Set
                                    self::setTp ($objV, new S ($objK), $objTp);
                                    break;
                            }
                            // BK;
                            break;

                        default:
                            self::setTp ($objV, new S ($objK), $objTp);
                            break;
                    }
                    // BK;
                    break;

                case 'textarea':
                case 'select':
                case 'option':
                case 'optgroup':
                case 'file':
                case 'hidden':
                case 'reset':
                case 'submit':
                case 'button':
                case 'image':
                case 'text':
                case 'password':
                case 'optgroup_ending':
                case 'select_ending':
                    // Set
                    self::setTp ($objV, new S ($objK), $objTp);
                    break;

                default:
                    // Throws
                    throw new FormInputTypeNotSupportedException;
                    break;
            }
        }

        // Set
        self::setTp ($inputType, new S ('type'), $objTp);
        self::exeTp ($objTp);
    }

    /**
     * Sets the form's header and initiate it;
     *
     * @author Catalin Alexandru Zamfir <catalin.zamfir@raphpframework.ro>
     * @copyright Under the terms of the GNU General Public License v3
     * @version $Id: Forms.php 1 2012-10-26 08:27:37Z root $
     */
    private static final function setFormHeader () {
        // Check
        if (self::$objFormStarted
        ->toInt () == 0) {
            // Check
            if (self::checkPOST ()
            ->toBoolean () == TRUE) {
                // Check
                if ($_SESSION['POST']->doCount ()
                ->toInt () != 0 && sizeof ($_FILES) != 0) {
                    // Check
                    self::checkFILEOperations ();
                }
            }

            // Arrayify
            $coreSetAttributes = new A;

            // We started a form
            self::$objFormStarted->setInt (1);

            // Set the form .tp file;
            $objTp = new
            Path (Architecture
            ::pathTo (FORM_TP_DIR,
            'Forms-Header.tp'));

            // Core
            self::setCoreAttributes (self::$objFormDataContainer, $objTp);
            self::setCoreInputAttributes (self::$objFormDataContainer, $objTp);

            // Foreach
            foreach (self::$objFormDataContainer as $objK => $objV) {

                // Switch
                switch ($objK) {
                    case 'action':
                        !empty ($objV) ?
                        $coreSetAttributes[$objK] = $objV :
                        $coreSetAttributes[$objK] = Location::rewriteTo ();
                        break;

                    case 'fieldset':
                        // Set
                        self::$objFormInFieldSet->setInt (1);
                        $coreSetAttributes->offsetSet ($objK, $objV);
                        break;

                    default:
                        // Set
                        $coreSetAttributes
                        ->offsetSet ($objK, $objV);
                        break;
                }
            }

            // Set
            self::setTp ($coreSetAttributes,
            new S ('coreSetAttributes'), $objTp,
            new S ('explode'));

            // Execute
            self::exeTp ($objTp);
        }
    }

    /**
     * Check if there are any pending file operations (usually uploads) on the current form;
     *
     * @author Catalin Alexandru Zamfir <catalin.zamfir@raphpframework.ro>
     * @copyright Under the terms of the GNU General Public License v3
     * @version $Id: Forms.php 1 2012-10-26 08:27:37Z root $
     */
    private static final function checkFILEOperations () {
        // Set
        $_SESSION['FILES'] = new A ($_FILES);

        // Foreach
        foreach ($_SESSION['FILES']
        as $objK => $objV) {
            // Switch
            switch ($objV['error']) {

                case UPLOAD_ERR_INI_SIZE:
                    self::setErrorOnInput (new S ($objK),
                    _T ('Upload exceeds limit!'));
                    break;

                case UPLOAD_ERR_FORM_SIZE:
                    self::setErrorOnInput (new S ($objK),
                    _T ('Upload exceeds max file size!'));
                    break;

                case UPLOAD_ERR_PARTIAL:
                    self::setErrorOnInput (new S ($objK),
                    _T ('Upload was partial!'));
                    break;

                case UPLOAD_ERR_NO_FILE:
                    self::setErrorOnInput (new S ($objK),
                    _T ('Upload field was empty!'));
                    break;

                case UPLOAD_ERR_CANT_WRITE:
                    self::setErrorOnInput (new S ($objK),
                    _T ('Upload cannot be written to disk!'));
                    break;

                case UPLOAD_ERR_EXTENSION:
                    self::setErrorOnInput (new S ($objK),
                    _T ('Upload is of an unknonw file type!'));
                    break;
            }
        }

        // Check
        if (self::$objFormDataContainer
        ->offsetExists ('accepted_mime_types')) {
            // Set
            $aNotSoRandomString = _PIPE . time ();

            // Check
            if (self::$objFormDataContainer
            ->offsetGet ('accepted_mime_types')
            ->findPos (_PIPE) == FALSE) {
                // Set
                self::$objFormDataContainer
                ->offsetGet ('accepted_mime_types')
                ->appendString ($aNotSoRandomString);
            }

            // MIMEs
            $acceptedMIMETypes = self::$objFormDataContainer
            ->offsetGet ('accepted_mime_types')
            ->fromStringToArray (_PIPE);

            // Foreach
            foreach ($_SESSION['FILES'] as $objK => $objV) {
                // Chekc
                if (!$acceptedMIMETypes
                ->inArray ($objV['type'])
                ->toBoolean ()) {
                    // Check
                    if ($acceptedMIMETypes
                    ->doCount ()->toInt () == 2) {
                        // Set
                        self::$objFormDataContainer
                        ->offsetGet ('accepted_mime_types')
                        ->doToken ($aNotSoRandomString, _NONE);
                    }

                    // Check
                    if (self::$objFormDataContainer
                    ->offsetExists ('upload_error_message')) {
                        // Set
                        self::setErrorOnInput (new S ($objK),
                        self::$objFormDataContainer
                        ->offsetGet ('upload_error_message'));
                    } else {
                        // Set
                        self::setErrorOnInput (new S ($objK),
                        _T ('Upload type forbidden. Accepted:')
                        ->appendString (_SP)->appendString ($acceptedMIMETypes
                        ->fromArrayToString (', ')));
                    }
                }
            }
        }
    }

    /**
     * Processes any pending file operations on this form;
     *
     * @author Catalin Alexandru Zamfir <catalin.zamfir@raphpframework.ro>
     * @copyright Under the terms of the GNU General Public License v3
     * @version $Id: Forms.php 1 2012-10-26 08:27:37Z root $
     */
    private static final function setFILEOperations () {
        // Set
        $objDirectory = Architecture
        ::pathTo (Architecture::getStorage (),
        UPLOAD_DIR, TEMP_DIR)->appendString (_S);

        // Foreach
        foreach ($_SESSION['FILES'] as $objK => $objV) {
            // Check
            is_uploaded_file ($objV['tmp_name']) ? move_uploaded_file ($objV['tmp_name'],
            $objDirectory . basename ($objV['tmp_name'])) : FALSE;
        }

        // Foreach
        foreach ($_SESSION['FILES'] as $objK => $objV) {
            // Set
            $_SESSION['POST'][$objK] =
            basename ($objV['name']);
        }
    }

    /**
     * Sanitizies the string given to it (usually a path, existent or not);
     *
     * @author Catalin Alexandru Zamfir <catalin.zamfir@raphpframework.ro>
     * @copyright Under the terms of the GNU General Public License v3
     * @version $Id: Forms.php 1 2012-10-26 08:27:37Z root $
     */
    private static final function sanitizePATH (S $objStringToCLEAN) {
        // Return
        return $objStringToCLEAN->stripTags ()->pregChange ('/[^a-zA-Z0-9_.-]/', _SP)
        ->trimLeft ()->trimRight ()->pregChange ('/' . _SP . '/', _U)->toLower ();
    }

    /**
     * Moves file uploads to their final destination;
     *
     * @author Catalin Alexandru Zamfir <catalin.zamfir@raphpframework.ro>
     * @copyright Under the terms of the GNU General Public License v3
     * @version $Id: Forms.php 1 2012-10-26 08:27:37Z root $
     */
    private static final function moveFILEOperations () {
        // Check
        if (self::$objFormDataContainer
        ->offsetExists ('upload_dir')) {
            // Set
            $objDirectory = new StorageDirectoryPath (self::$objFormDataContainer
            ->offsetGet ('upload_dir')->makeCopyObject ()->prependString (_S)
            ->prependString (UPLOAD_DIR)->appendString (_S), FALSE);

            // Make
            $objDirectory->makePath ();

            // Get timestamp
            self::$objImageTimestampPrefix = (string) time ();

            // Foreach
            foreach ($_SESSION['FILES'] as $objK => $objV) {
                // Assign
                $objK = new S ($objK);

                // Sanitize
                $_SESSION['FILES'][(string) $objK]['name'] =
                self::sanitizePATH (_S ($_SESSION['FILES'][(string) $objK]['name'])
                ->prependString (self::$objImageTimestampPrefix . _U));

                // Sanitize
                self::setPOST ($objK, self::sanitizePATH (_S (self::getPOST ($objK))
                ->prependString (_U)->prependString (self::$objImageTimestampPrefix)));

                // Set
                $uploadedFileMovedToDirectory = $_SESSION['FILES'][(string) $objK]['name']
                ->makeCopyObject ()->prependString ($objDirectory->toAbsolutePath ());

                // Specific
                if (Architecture::onWindows ()
                ->toBoolean () == TRUE) {
                    // Set
                    $_SESSION['FILES'][(string) $objK]['tmp_name'] = _S ($_SESSION['FILES'][(string) $objK]['tmp_name'])
                    ->doToken (DIRECTORY_SEPARATOR, _S)->doToken (dirname (dirname (Architecture::getRoot ())), _NONE);
                }

                // Set
                $temporaryDirectory = Architecture::pathTo (Architecture::getStorage (), UPLOAD_DIR)
                ->appendString ($_SESSION['FILES'][(string) $objK]['tmp_name']);

                // Rename
                rename ($temporaryDirectory, $uploadedFileMovedToDirectory);
            }
        } else {
            // Throws
            throw new NoFormUploadDirSetException;
        }
    }

    /**
     * Resizes the uploads (images mainly) to the given path, sizes and constraints;
     *
     * @author Catalin Alexandru Zamfir <catalin.zamfir@raphpframework.ro>
     * @copyright Under the terms of the GNU General Public License v3
     * @version $Id: Forms.php 1 2012-10-26 08:27:37Z root $
     */
    private static final function resizeFILEOperations () {
        // Check
        if ((self::$objFormDataContainer->offsetExists ('upload_dir')) &&
        (self::$objFormDataContainer->offsetExists ('upload_resize_img'))) {

            // Resize
            Graphics::resizeImages (new StorageDirectoryPath (self::$objFormDataContainer
            ->offsetGet ('upload_dir')->makeCopyObject ()->prependString (_S)->prependString (UPLOAD_DIR)
            ->appendString (_S)), new A ($_SESSION['FILES']), self::$objFormDataContainer
            ->offsetGet ('upload_resize_img'));
        }
    }

    /**
     * Set form footer & other ending data;
     *
     * @author Catalin Alexandru Zamfir <catalin.zamfir@raphpframework.ro>
     * @copyright Under the terms of the GNU General Public License v3
     * @version $Id: Forms.php 1 2012-10-26 08:27:37Z root $
     */
    private static final function setFormFooter () {
        // Form
        self::$objFormStarted->setInt (0);

        // Check
        if (self::checkPOST ()
        ->toBoolean () == TRUE) {
            // Check
            if (self::$objErrors
            ->doCount ()->toInt () != 0) {
                // Set
                self::$objPassed->setInt (0);
            } else {
                // Set
                self::$objPassed->setInt (1);
            }

            // Check
            if (self::$objPassed
            ->toInt () == 1) {
                // Check
                if ($_SESSION['POST']
                ->doCount ()->toInt () != 0 &&
                sizeof ($_FILES) != 0 &&
                self::$objFormDataContainer
                ->offsetGet ('enctype') == 'multipart/form-data') {
                    // Check
                    if (self::$objFormDataContainer
                    ->offsetExists ('SQL_Operations_On_Input')) {
                        // Check
                        if ($_SESSION['POST']
                        ->offsetExists (self::$objFormDataContainer
                        ->offsetGet ('SQL_Operations_On_Input'))) {
                            // Set
                            self::checkFILEOperations ();
                            self::setFILEOperations ();
                            self::moveFILEOperations ();
                            self::resizeFILEOperations ();
                        }
                    }
                }

                // Check
                if ($_SESSION['POST']
                ->doCount ()->toInt () != 0) {
                    // Check
                    if (self::$objFormDataContainer
                    ->offsetExists ('SQL_Operations_On_Input')) {
                        // Check
                        if ($_SESSION['POST']
                        ->offsetExists (self::$objFormDataContainer
                        ->offsetGet ('SQL_Operations_On_Input'))) {
                            // Set
                            self::runSQLOperationsOnForm ();
                        }
                    }

                    // Check
                    if (self::$objFormDataContainer
                    ->offsetExists ('Redirect_If_Ok')) {
                        // Check
                        if ($_SESSION['POST']
                        ->offsetExists ('RA_dropZoneSet')) {
                            // Check
                            if ((int) (string) $_SESSION['POST']->offsetGet ('RA_dropZoneCur') != (int) (string)
                            $_SESSION['POST']->offsetGet ('RA_dropZoneNum') - 1) {
                                // Output
                                self::outputString (_S ('RA_dropZoneInput_Continue')->appendString (_DCSP)
                                ->appendString (self::$objFormDataContainer->offsetGet ('Redirect_If_Ok')));
                            } else {
                                // Output
                                self::outputString (_S ('RA_dropZoneInput_Ok')->appendString (_DCSP)
                                ->appendString (self::$objFormDataContainer->offsetGet ('Redirect_If_Ok')));
                            }
                        } else {
                            // Add the Redirect_If_Ok header
                            Header::setKey (self::$objFormDataContainer
                            ->offsetGet ('Redirect_If_Ok'), new S ('Location'));
                        }
                    }
                }
            }
        }

        // End
        $objTp = new Path (Architecture
        ::pathTo (FORM_TP_DIR, 'Forms-Footer.tp'));

        // Check
        if (self::$objFormInFieldSet
        ->toInt () == 1) {
            // Set
            $formFieldSet = new B (TRUE);
            self::setTp ($formFieldSet, new S ('fieldset'), $objTp);
            self::$objFormInFieldSet->setInt (0);
        }

        // Execute
        self::exeTp ($objTp);
    }

    /**
     * Set core DOM attributes, ones that are defiend with any HTML inputs;
     *
     * @author Catalin Alexandru Zamfir <catalin.zamfir@raphpframework.ro>
     * @copyright Under the terms of the GNU General Public License v3
     * @version $Id: Forms.php 1 2012-10-26 08:27:37Z root $
     */
    private static final function setCoreInputAttributes (A $inputAttributes, Path $objTp) {
        // Set
        $coreSetAttributes = new A;

        // Foreach
        foreach ($inputAttributes as $objK => $objV) {
            // Switch
            switch ($objK) {
                // JS
                case 'JsRegExp':
                    // Set them for posterity
                    $objOnFocus = new S ('javascript: this.value = this.value.replace (new RegExp ("[%rId]", "g"), "");');
                    $objOnKey   = new S ('javascript: return checkKey (event, "[%rId]");');

                    // Key up/down
                    $coreSetAttributes
                    ->offsetSet ('onkeypress',
                    $objOnKey->makeCopyObject ()
                    ->doToken ('[%rId]', $objV));

                    // Focus
                    $coreSetAttributes
                    ->offsetSet ('onblur',
                    $objOnFocus->makeCopyObject ()
                    ->doToken ('[%rId]', $objV));
                    break;

                default:
                    // Set
                    $coreSetAttributes
                    ->offsetSet ($objK, $objV);
                    break;
            }
        }

        // Set
        self::setTp ($coreSetAttributes,
        new S ('coreSetAttributes'),
        $objTp, new S ('explode'));
    }

    /**
     * Set core DOM attributes, ones that are defiend with any HTML inputs;
     *
     * @author Catalin Alexandru Zamfir <catalin.zamfir@raphpframework.ro>
     * @copyright Under the terms of the GNU General Public License v3
     * @version $Id: Forms.php 1 2012-10-26 08:27:37Z root $
     */
    private static final function setCoreAttributes (A $inputAttributes, Path $objTp) {
        // Set
        $coreSetAttributes = new A;

        // Foreach
        foreach ($inputAttributes as $objK => $objV) {
            // Switch
            switch ($objK) {
                case 'value':
                    // Check
                    if (isset ($objV) && empty ($objV) && ($objV != '0')) {
                        // Empty is set to a string
                        $coreSetAttributes->offsetSet ($objK,
                        'non_space_or_false_replacement_string');
                    } else if (isset ($objV) && ($objV == '0')) {
                        // Or, set to 0
                        $coreSetAttributes->offsetSet ($objK,
                        'non_zero_or_false_replacement_string');
                    }
                    // BK;
                    break;

                case 'id':
                    // Check
                    if ($inputAttributes
                    ->offsetExists ('tag')) {
                        // Set
                        $coreSetAttributes->offsetSet ('tagfid', $objV);
                        $coreSetAttributes->offsetSet ('tagtxt', $inputAttributes->offsetGet ('tag'));
                    }

                    // Set
                    $coreSetAttributes
                    ->offsetSet ($objK, $objV);
                    break;

                case 'name':
                    // Check
                    if (self::$objErrors
                    ->offsetExists ($objV)) {
                        // Set
                        $coreSetAttributes->offsetSet ('err_msg',
                        self::$objErrors[$objV]);

                        // Check
                        !($coreSetAttributes->offsetExists ('class'))    ?
                        $coreSetAttributes->offsetSet ('class',
                        DEFAULT_ERROR_CSS_CLASS)                         :
                        FALSE;

                        // Check
                        if ($inputAttributes
                        ->offsetExists ('err_msg_align')) {
                            // Set
                            $coreSetAttributes->offsetSet ('err_msg_align',
                            $inputAttributes->offsetGet ('err_msg_align'));
                        }
                    }

                    // Set
                    $coreSetAttributes
                    ->offsetSet ($objK, $objV);
                    break;

                case 'class':
                    // Set
                    $coreSetAttributes
                    ->offsetSet ($objK, $objV);

                    // Check
                    if (self::$objErrors
                    ->offsetExists ($objV)) {
                        // Append
                        $coreSetAttributes->offsetGet ($objK)->appendString (_SP)
                        ->appendString (DEFAULT_ERROR_CSS_CLASS);
                    }
                    // BK;
                    break;

                default:
                    // Set
                    $coreSetAttributes
                    ->offsetSet ($objK, $objV);
                    break;
            }
        }

        // Set
        self::setTp ($coreSetAttributes,
        new S ('coreSetAttributes'),
        $objTp, new S ('explode'));
    }

    /**
     * Set option's (tag) status upon _POST;
     *
     * @author Catalin Alexandru Zamfir <catalin.zamfir@raphpframework.ro>
     * @copyright Under the terms of the GNU General Public License v3
     * @version $Id: Forms.php 1 2012-10-26 08:27:37Z root $
     */
    private static final function autoOptionsOnPOST (I $objKey) {
        // Check
        if (self::$objDataContainer->offsetGet ($objKey)
        ->offsetGet ('type') == new S ('option')) {
            // Check
            if ($_SESSION['POST']->offsetExists (self::$objDataContainer
            ->offsetGet ($objKey)->offsetGet ('bound_to'))) {
                // Set
                if ($_SESSION['POST']->offsetGet (self::$objDataContainer
                ->offsetGet ($objKey)->offsetGet ('bound_to')) == self::$objDataContainer
                ->offsetGet ($objKey)->offsetGet ('value')) {
                    // Set
                    self::$objDataContainer->offsetGet ($objKey)
                    ->offsetSet ('selected', new B (TRUE));
                }
            }
        }
    }

    /**
     * Do specific checkbox/radio SQL operations;
     *
     * @author Catalin Alexandru Zamfir <catalin.zamfir@raphpframework.ro>
     * @copyright Under the terms of the GNU General Public License v3
     * @version $Id: Forms.php 1 2012-10-26 08:27:37Z root $
     */
    private static final function checkboxAndRadioSQLOperations (I $objKey) {
        // Check
        if ((self::$objDataContainer->offsetGet ($objKey)
        ->offsetGet ('type') == new S ('checkbox')) or
        (self::$objDataContainer->offsetGet ($objKey)
        ->offsetGet ('type') == new S ('radio'))) {

            // Check
            if ($_SESSION['POST']
            ->offsetGet (self::$objDataContainer
            ->offsetGet ($objKey)->offsetGet ('name'))) {
                // Set
                self::$objDataContainer->offsetGet ($objKey)
                ->offsetSet ('checked', new B (TRUE));

                // Set
                self::$objDataContainer->offsetGet ($objKey)
                ->offsetSet ('value', $_SESSION['POST']
                ->offsetGet (self::$objDataContainer
                ->offsetGet ($objKey)
                ->offsetGet ('name')));
            } else {
                // Query
                self::doQuery (_S ('UPDATE %table SET %whom = "" WHERE %condition')
                ->doToken ('%table', self::$objSQLUpdateTable)
                ->doToken ('%whom', self::$objDataContainer
                ->offsetGet ($objKey)->offsetGet ('name'))
                ->doToken ('%condition', self::$objSQLUpdateCondition));

                // Check
                if (!(self::$objDataContainer
                ->offsetGet ($objKey)
                ->offsetExists ('value'))) {
                    // Set
                    self::$objDataContainer
                    ->offsetGet ($objKey)
                    ->offsetSet ('value',
                    new S ('on'));
                }
            }
        }
    }

    /**
     * Set input values to the ones saved from _POST;
     *
     * @author Catalin Alexandru Zamfir <catalin.zamfir@raphpframework.ro>
     * @copyright Under the terms of the GNU General Public License v3
     * @version $Id: Forms.php 1 2012-10-26 08:27:37Z root $
     */
    private static final function setInputsFromSession (I $objKey) {
        // Check
        if (self::$objDataContainer
        ->offsetGet ($objKey)
        ->offsetExists ('name')) {
            // Check
            if ($_SESSION['POST']->offsetExists (self::$objDataContainer
            ->offsetGet ($objKey)->offsetGet ('name'))) {
                // Set
                self::$objDataContainer->offsetGet ($objKey)
                ->offsetSet ('value', $_SESSION['POST']
                ->offsetGet (self::$objDataContainer
                ->offsetGet ($objKey)->offsetGet ('name')));
            }
        }
    }

    /**
     * Sets input values to the ones from the form query;
     *
     * @author Catalin Alexandru Zamfir <catalin.zamfir@raphpframework.ro>
     * @copyright Under the terms of the GNU General Public License v3
     * @version $Id: Forms.php 1 2012-10-26 08:27:37Z root $
     */
    private static final function setInputFromQuery (I $objKey) {
        // Check
        if ((self::$objDataContainer->offsetGet ($objKey)
        ->offsetGet ('type') == new S ('checkbox')) or
        (self::$objDataContainer->offsetGet ($objKey)
        ->offsetGet ('type') == new S ('radio'))) {

            // Check
            if (self::$objSQLData->offsetGet (0)->offsetGet (self::$objDataContainer
            ->offsetGet ($objKey)->offsetGet ('name'))->toLength ()->toInt () != 0) {
                // Check
                if (self::$objDataContainer->offsetGet ($objKey)
                ->offsetGet ('type') == 'radio') {
                    // Check
                    if (self::$objDataContainer->offsetGet ($objKey)->offsetGet ('value') ==
                    self::$objSQLData->offsetGet (0)->offsetGet (self::$objDataContainer
                    ->offsetGet ($objKey)->offsetGet ('name'))) {
                        // Set
                        self::$objDataContainer->offsetGet ($objKey)
                        ->offsetSet ('checked', new B (TRUE));
                    }
                } else {
                    // Set
                    self::$objDataContainer->offsetGet ($objKey)
                    ->offsetSet ('checked', new B (TRUE));
                }
            } else {
                // Check
                if (self::$objDataContainer
                ->offsetGet ($objKey)
                ->offsetExists ('value')) {
                    // Check
                    if (self::$objDataContainer
                    ->offsetGet ($objKey)
                    ->offsetGet ('value') == _NONE) {
                        // Set
                        self::$objDataContainer
                        ->offsetGet ($objKey)
                        ->offsetSet ('value', new S ('on'));
                    }
                } else {
                    // Set
                    self::$objDataContainer
                    ->offsetGet ($objKey)
                    ->offsetSet ('value', new S ('on'));
                }
            }
        } else {
            // Check
            if (self::$objSQLData->offsetGet (0)->offsetGet (self::$objDataContainer
            ->offsetGet ($objKey)->offsetGet ('name')) instanceof S) {
                // Chekc
                if (self::$objSQLData->offsetGet (0)->offsetGet (self::$objDataContainer
                ->offsetGet ($objKey)->offsetGet ('name'))->toLength ()->toInt () != 0) {
                    // Set
                    self::$objDataContainer->offsetGet ($objKey)->offsetSet ('value',
                    self::$objSQLData->offsetGet (0)->offsetGet (self::$objDataContainer
                    ->offsetGet ($objKey)->offsetGet ('name')));
                }
            }
        }
    }

    /**
     * Make a file controller (disabled on form updates but no new file) and render it;
     *
     * @author Catalin Alexandru Zamfir <catalin.zamfir@raphpframework.ro>
     * @copyright Under the terms of the GNU General Public License v3
     * @version $Id: Forms.php 1 2012-10-26 08:27:37Z root $
     */
    private static final function renderDropZone (I $objKey) {
        // Check
        if (self::$objDataContainer->offsetGet ($objKey)->offsetExists ('RA_File_Controller') &&
            self::$objDataContainer->offsetGet ($objKey)->offsetExists ('id') &&
            self::$objDataContainer->offsetGet ($objKey)->offsetExists ('type')) {

            // Check
            if (self::$objDataContainer->offsetGet ($objKey)
            ->offsetGet ('type') == new S ('file')) {
                // Set
                self::$objDataContainer[$objKey]['disabled'] = new B (TRUE);
                self::$objDataContainer[$objKey]['RA_File_Controller_Id'] =
                self::$objDataContainer[$objKey]['id'];
            }
        }
    }

    /**
     * Update fields on update or insert;
     *
     * @author Catalin Alexandru Zamfir <catalin.zamfir@raphpframework.ro>
     * @copyright Under the terms of the GNU General Public License v3
     * @version $Id: Forms.php 1 2012-10-26 08:27:37Z root $
     */
    private static final function updateOrInsert () {
        // Check
        if (self::$objFormDataContainer
        ->offsetExists ('SQL_Update_Or_Insert') &&
        self::$objFormDataContainer
        ->offsetExists ('SQL_Table')) {
            // Check
            if (self::$objSQLUpdateId == NULL) {
                // Set
                self::$objSQLUpdateId =
                self::getAutoIncrement
                (self::$objFormDataContainer
                ->offsetGet ('SQL_Table'));
            }

            // Check
            if (self::$objFormDataContainer
            ->offsetExists ('SQL_Condition')) {
                // Set
                self::$objSQLUpdateCondition =
                self::$objFormDataContainer
                ->offsetGet ('SQL_Condition');
            } else {
                // Set
                self::$objSQLUpdateCondition = _S ('%whom = "%what"')
                ->doToken ('%whom', self::$objSQLUpdateField)
                ->doToken ('%what', self::$objSQLUpdateId);
            }

            // Check
            if (self::$objFormDataContainer
            ->offsetGet ('SQL_Update_Or_Insert')
            == new S ('update')) {
                // More inserts than one
                if (self::$objFormDataContainer
                ->offsetExists ('SQL_Join_On') && self::$objFormDataContainer
                ->offsetExists ('SQL_Save_Into_Table')) {
                    // Set
                    self::$objSQLUpdateTable = self::$objFormDataContainer
                    ->offsetGet ('SQL_Table')->makeCopyObject ()->appendString (_SP)
                    ->appendString (self::$objFormDataContainer['SQL_Join_On']);

                    // Set
                    $objUpdateTable =
                    self::$objFormDataContainer
                    ->offsetGet ('SQL_Save_Into_Table')
                    ->toValues ();

                    // Count
                    $objUpdateTableCount =
                    $objUpdateTable
                    ->doCount ();

                    // Set
                    $objUpdateTable[$objUpdateTableCount->doInc ()] =
                    self::$objFormDataContainer->offsetGet ('SQL_Table');

                    // For
                    for ($objI = 0; $objI <
                    $objUpdateTableCount
                    ->toInt (); ++$objI) {
                        // Set
                        self::$objSQLUpdateFields = new A (array_merge (self::$objSQLUpdateFields->toArray (),
                        self::getFields (new S ($objUpdateTable[$objI]))->toArray ()));
                    }
                } else {
                    // Set
                    self::$objSQLUpdateTable  = self::$objFormDataContainer->offsetGet ('SQL_Table');
                    self::$objSQLUpdateFields = self::getFields (self::$objSQLUpdateTable);
                }
            }

            // Check
            if (self::checkPOST ()
            ->toBoolean () == FALSE) {
                // Query
                self::$objSQLData = self::doQuery (_S ('SELECT * FROM %table
                WHERE %condition LIMIT 1')->doToken ('%table', self::$objSQLUpdateTable)
                ->doToken ('%condition', self::$objSQLUpdateCondition));
            }
        }
    }
}
?>
