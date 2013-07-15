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
 * TIP for Eclipse: Trim whitespace at EOL:
 * F: [\t ]+(?:(\n)|$)
 * R: $1
 */

/**
 * At the time of writing, Marcus Boerger, PHP Lead Developer published SPL_Types @ php.net/spl_types not long ago which are strictly
 * speaking kind of the same idea as DTs in RA. Once and if this extension gets into PHP we will inherit the basic SPL Types
 * and build upon them, getting the required SDT (Strict Data Types) support right from the language.
 *
 * We opted to have DTs instead of PHP's own integer, string, float, boolean, resource and others for the same reasons
 * that made languages like C++ or Java so popular. Having strict data types allows for greater data atomicity, greater control on
 * how errors are produced and treated and in short helps developers write better code.
 *
 * We've defined a few common DTs for S (string), I (integer), F (float), R (resource), B (boolean) with classes that are named
 * as so. Other types like Path, DirectoryPath, Contents are an inheritance of the S class. The O (not zero) class is the
 * mixed, common-ground class between these separated types through which castings can be done. The M class, which is abstract, has
 * the use of mapping PHP functions to magic methods of the object, instead of actually writing code for trivial, already implemented
 * functions in the language.
 *
 * You're going to see the advantages that method chaining these types with PHP functions mapped to object methods give you the
 * kind of chaining that makes writing PHP code under this framework, a distinct pleasure, as logic flows from one corner of code
 * to the other while keeping it compact, light and understandable.
 *
 * As you're going to see from DT methods, with some exceptions, they don't accept other DTs as type hints. That's because it
 * would require a degree of redundancy. Rather, they accept pure PHP types and check if those passed types are the required ones. If
 * that's the case, they allow execution to continue, else they throw an error at least.
 */

/**
 * Abstract mapping class, used to MAP DataType (DT) methods to already existing PHP functions.
 *
 * For example, we have the PHP str_replace function, that we can 'map' to the S (String) DT, by using one of the below.
 * This way we add functionality by ordering passed parameters in an array that we pass when calling the PHP function,
 * without much work, which is exactly what we need for quick, bug-free features.
 */
abstract class M {
    /**
     * determine which PHP function was mapped to what DT method, and act accordingly;
     */
    private static $objFuncMapper = NULL;

    /**
     * @var mixed $objContainer Contains whatever the object must contain. The 'O' class, which is a direct descendant of the 'M'
     * class can contain anything, while S, I, F, etc. will contain specific data types according to their type of object. We
     * get the advantage of Strong Type Hinting (STH) by working with objects rather than usual PHP types, which are prone to wild
     * casting without notice;
     */
    protected $objContainer = NULL;

    /**
     * What this actually means is that we can add a mapping between PHP functions (or object methods, as long as the calling
     * parameter is call_user_func/call_user_func_array compatible) and DT methods.
     *
     * Thus we can map multiple methods to the same PHP function, which should make us be able to call, for ex: str_replace
     * or any other function by a common name, (ex. 'doToken') and issue THE SAME features with our DTs, without writing
     * single line of code that would implement such a feature, already existing in the base language.
     *
     * So, if you're going to map a PHP function to a specific DT that it acts on, you can call this function with two parameters,
     * the first one: the method name that will get mapped while the second is the PHP function we're mapping to. These mappings are
     * going to be resolved further down the line in a method specific for your DT (M::S, or M::I methods for string and integer
     * respectivelly). Read on further for more information.
     *
     */
    protected static function mapMethodToFunction ($objMethod, $objPHPFunction) {
        self::$objFuncMapper == NULL ? self::$objFuncMapper = new A : FALSE;
        self::$objFuncMapper[$objMethod] = $objPHPFunction;
    }

    /**
     * Method used on the current object to return a CLONE of it. Rather than issuing a CLONE $objToBeCopied which breaks a method
     * chain, it's easier to just keep the chain going while issuing this specific method which will return a CLONE of the object
     * it' called through, making code a lot simpler and understandable.
     */
    public function makeCopyObject () {
        // Return
        return CLONE $this;
    }

    /**
     * We use the PHP magic __toString, to enable us to do simple operations like 'echo $theObject', and return a really nice
     * string representation of that object. It's really interesting how you can actually evolve this __toString method in
     * something really complicated and complex. For example, we could return an object that contains a table from the database,
     * but when we will echo it, it will render a JS data-grid for us, with full functionality.
     *
     * We first check the the content of this object is something that can be represented as a string. If it is, we just return it. Else,
     * if we determine it's not a string, we cast it. If that doesn't work, we throw an error. We could have base64_encoded the contents
     * but it's better of to the developer if he whishes something like that should happen to his objects or not.
     *
     */
    public function __toString () {
        // Return
        return (string) $this
        ->objContainer;
    }

    /**
     * By default, DTs can be SERIALIED/UNSERIALIZED . For this to happen, a few magic PHP methods like __set_state should be properly
     * defined and return the given object state to it's requested container.
     *
     */
    public static function __Set_State (Array $objStateArray) {
        // Get
        $savedObjRETURN =
        $objStateArray['objContainer'];

        // Switch
        switch (gettype ($savedObjRETURN)) {
            case 'array':
                return new
                A ($savedObjRETURN);
                break;

            case 'string':
                return new
                S ($savedObjRETURN);
                break;

            case 'integer':
                return new
                I ($savedObjRETURN);
                break;

            case 'double':
                return new
                F ($savedObjRETURN);
                break;

            case 'boolean':
                return new
                B ($savedObjRETURN);
                break;

            case 'resource':
                return new
                R ($savedObjRETURN);
                break;
        }
    }

    /**
     * What is this method used for!? Well, doing some copying from A to B, without destroying to much stuff in the process. Why?
     * For starters, it uses the objects setMix () method which sets the proper type of DT, and it doesn't copy an objects
     * entire properties, but only the value contained in the this->objContainer; We've already stated that one instance of a DT
     * contains one, and only one value, compatible to that specific DT.
     *
     * We could rely for example on A = B, but that would mean copying any other properties from A to B, which is something we
     * really don't want every time. For example, if by any chance we extend the core DTs of our framework, then for sure
     * we don't want something as destructive as an A = B copying, but we just want to copy the contents of A to B, in a manner
     * compatible to the framework.
     *
     * A special WARNING is in order here. If you do a copyTo method on an object, method chaining continues from the parameter, not
     * the original object on which you issued the copyTo method. As it's pretty clear from the source code, we return the parameter,
     * not the original object. Reasoning for this is the fact that if you execute this method, you probably want to do something with
     * the copy, not the original source;
     *
     */
    public final function copyTo (O $objTo) {
        // Return
        return $objTo
        ->setMix ($this
        ->objContainer);
    }

    /**
     * Method moves the content of the source object to the the object given as parameter. It also destroy's  the contents of the
     * source object by setting the this->objContainer to NULL. It continues the method chaining with the passed parameter, because it
     * does not make any sense continuing with an empty object.
     *
     */
    public final function moveTo (O $objTo) {
        // Set
        $objTo->setMix ($this->objContainer);
        $this->objContainer = NULL;
        return $objTo;
    }

    /**
     * The method will check that the content of the current object is one of the requested types in the passed parameter. Method can
     * be overriden to enable check for other types or extensions that you, the user of this framework can thing of. For the default,
     * we have already defined the proper hoocks. Here are some defaults:
     *
     * 	<ul>
     *      <li>set :: checks if it's != NULL or empty ...</li>
     *      <li>arr :: checks if it's array;</li>
     *      <li>bln :: checks if it's boolean;</li>
     *      <li>flt :: checks if it's float;</li>
     *      <li>int :: checks if it's integer;</li>
     *      <li>nbr :: checks if it's number;</li>
     *      <li>obj :: checks if it's object;</li>
     *      <li>res :: checks if it's resource;</li>
     *      <li>str :: checks if it's string;</li>
     *      <li>default: check it's NULL;</li>
     * 	</ul>
     *
     */
    public final function checkIs ($whatType = NULL) {
        // Switch
        switch ($whatType) {
            case 'set':
                return new B (
                ($this->objContainer != NULL) ||
                !(empty ($this->objContainer)));
                break;

            case 'arr':
                return new B (is_array
                ($this->objContainer));
                break;

            case 'bln':
                return new B (is_bool
                ($this->objContainer));
                break;

            case 'flt':
                return new B (is_float
                ($this->objContainer));
                break;

            case 'int':
                return new B (is_int
                ($this->objContainer));
                break;

            case 'nbr':
                return new B (is_numeric
                ($this->objContainer));
                break;

            case 'obj':
                return new B (is_object
                ($this->objContainer));
                break;

            case 'res':
                return new B (is_resource
                ($this->objContainer));
                break;

            case 'str':
                return new B (is_string
                ($this->objContainer));
                break;

            default:
                return new B (is_null
                ($this->objContainer));
                break;
        }
    }

    protected function SQLEscapedString ($mappedObject, $nameOfHook,
    $nameOfFunction, $argumentsOfHook) {
        // Return
        return $this->S ($mappedObject, $nameOfHook,
        $nameOfFunction, $argumentsOfHook);
    }

    /**
     * If you've already been through DLL, you've probably found that there's a special method named 'mapMethodToFunction' which does
     * just that: maps method names (virtual ones) to PHP functions that get executed on the current object that invokes them. That will
     * usually result in a new object getting created with the outputed value of the PHP function that was invoked. That allows us to
     * quickly add functionality to objects without requiring us to write code for it.
     *
     * For example, this method can be the mapping method for S, I, or F. It takes the name of the hook and switches on it. It gets
     * the mappedObject and and issues a call_user_func_array on it, with the argumentsOfHook properly arranged. For example, if you'd
     * want to do a strpos ('/', '/var/www/somewhere'); then that would translate to the S (string) object containing the path, with
     * the findPos method called on it (ex: $objPath->findPos ('/')). Arguments of hook would thus contain '/' at offset 0 in the array
     * passed as arguments. Thus, to be able to call_user_func_array ('strpos') we need to make argumentsOfHook[1] = $objPath, meaning
     * in short that argumentsOfHook[1] will contain the mappedObject.
     *
     * So, given the mappedObject, the name of the called hook, the name of the function and the arguments passed to it, we thus
     * arrange the object in the array, at a proper index (either shifting or unshifting the arguments). With the mappedObject properly
     * organized in the argumentsOfHook array , we thus issue a call_user_func_array on the nameOfFunction. We use nameOfHook just to
     * determine what kind of operation to do on argumentsOfHook, to get the object in place. It's a bit of tricky PHP programming here,
     * requires some PHP magic skills, but it's worth the trouble. We've developed this way for more than 5 years and it's been one of
     * the best experiences as far as coding goes because functionality gets mapped in such an easy way that drives maintainability.
     *
     */
    protected function S ($mappedObject, $nameOfHook,
    $nameOfFunction, $argumentsOfHook) {
        // Switch #1
        switch ($nameOfHook) {
            case 'escapeCString':
            case 'escapeString':
            case 'escapeMySQLString':
            case 'toHex':
            case 'toChunk':
            case 'encryptIt':
            case 'chrToASCII':
            case 'convertCYR':
            case 'uDecode':
            case 'uEncode':
            case 'countChar':
            case 'toCRC32':
            case 'toHebrew':
            case 'toNLHebrew':
            case 'entityDecode':
            case 'entityEncode':
            case 'charDecode':
            case 'charEncode':
            case 'trimLeft':
            case 'trimRight':
            case 'trimBoth':
            case 'toMD5File':
            case 'toMD5':
            case 'toMetaphoneKey':
            case 'toMoneyFormat':
            case 'nL2BR':
            case 'ordToASCII':
            case 'qpDecode':
            case 'qpEncode':
            case 'toSHA1File':
            case 'toSHA1':
            case 'toSoundEx':
            case 'doCSV':
            case 'replaceIToken':
            case 'doPad':
            case 'doRepeat':
            case 'doShuffle':
            case 'toROT13':
            case 'doSplit':
            case 'toWordCount':
            case 'compareCaseTo':
            case 'compareNCaseTo':
            case 'compareTo':
            case 'compareNTo':
            case 'stripTags':
            case 'removeCStr':
            case 'removeStr':
            case 'findIPos':
            case 'findPos':
            case 'findILPos':
            case 'findLPos':
            case 'findIFirst':
            case 'findFirst':
            case 'findLast':
            case 'doReverse':
            case 'toLength':
            case 'natCaseCmp':
            case 'natCmp':
            case 'charSearch':
            case 'doTokenize':
            case 'toLower':
            case 'toUpper':
            case 'doTranslate':
            case 'doSubStr':
            case 'doSubCompare':
            case 'doSubCount':
            case 'doSubReplace':
            case 'doWrap':
            case 'doBZCompress':
            case 'doBZDecompress':
            case 'doBZOpen':
            case 'doLZFCompress':
            case 'doLZFDecompress':
            case 'changeDirectoryPath':
            case 'scanDirectoryPath':
            case 'getCWorkingDir':
            case 'stripSlashes':
            case 'fileGetContents':
            case 'filePutContents':
            case 'getCSV':
            case 'getURLHeaders':
            case 'ucFirst':
            case 'toUnixTimestamp':
            case 'gZipEncode':
            case 'gZipCompress':
            case 'ucWords':
            case 'decodeJSON':
            case 'encodeURL':
            case 'decodeURL':
                // Unshift
                array_unshift ($argumentsOfHook, $mappedObject);
                break;

            case 'fromStringToArray':
                // Check
                if (isset ($argumentsOfHook[1])) {
                    // Push 3rd param, up one offset
                    $argumentsOfHook[2] = $argumentsOfHook[1];
                }

                // Make 2nd param, current object
                $argumentsOfHook[1] = $mappedObject;
                break;

            case 'pregMatch':
            case 'pregMatchEach':
                // Require
                $objPregArray = Array ();

                // Set
                $argumentsOfHook[1] = $mappedObject;
                $argumentsOfHook[2] = & $objPregArray;
                break;

            case 'pregChange':
            case 'eregReplace':
                $argumentsOfHook[2] = $mappedObject;
                break;

            default:
                // Throw
                throw new MethodNotMappedException;
                break;
        }

        // Switch #2
        switch ($nameOfHook) {
            case 'entityEncode':
            case 'entityDecode':
                $argumentsOfHook[] = 'UTF-8';
                break;
        }

        // Save
        $savedObj = call_user_func_array ($nameOfFunction,
        $argumentsOfHook);

        // Switch #3
        switch ($nameOfHook) {
            case 'pregMatch':
            case 'pregMatchEach':
                $savedObj = $objPregArray;
                break;
        }

        // Return
        if (is_string   ($savedObj)) return new S ($savedObj);
        if (is_int      ($savedObj)) return new I ($savedObj);
        if (is_float    ($savedObj)) return new F ($savedObj);
        if (is_array    ($savedObj)) return new A ($savedObj);
        if (is_bool     ($savedObj)) return new B ($savedObj);
        if (is_resource ($savedObj)) return new R ($savedObj);
    }

    /**
     */
    protected function A ($mappedObject, $nameOfHook,
    $nameOfFunction, $argumentsOfHook) {
        // Switch
        switch ($nameOfHook) {
            case 'changeKeyCase':
            case 'toChunk':
            case 'arrayShuffle':
            case 'arraySlice':
                // Unshift
                array_unshift ($argumentsOfHook, $mappedObject->toArray ());
                break;

            case 'arraySearch':
            case 'inArray':
                array_unshift ($argumentsOfHook, $mappedObject->toArray ());
                $argumentsOfHook = array_reverse ($argumentsOfHook);
                break;

            case 'fromArrayToString':
                // Check
                if (isset ($argumentsOfHook[1])) {
                    // Push 3rd parameter up one offset
                    $argumentsOfHook[2] = $argumentsOfHook[1];
                }

                // Make 2nd parameter, current object
                $argumentsOfHook[1] = $mappedObject->toArray ();
                break;

            default:
                // Throw
                throw new MethodNotMappedException;
                break;
        }

        // Save
        $savedObj = call_user_func_array ($nameOfFunction,
        $argumentsOfHook);

        // Return
        if (is_string   ($savedObj)) return new S ($savedObj);
        if (is_int      ($savedObj)) return new I ($savedObj);
        if (is_float    ($savedObj)) return new F ($savedObj);
        if (is_array    ($savedObj)) return new A ($savedObj);
        if (is_bool     ($savedObj)) return new B ($savedObj);
        if (is_resource ($savedObj)) return new R ($savedObj);
    }

    /**
     */
    protected function I ($mappedObject, $nameOfHook,
    $nameOfFunction, $argumentsOfHook) {
        // Switch
        switch ($nameOfHook) {
            case 'formatNumber':
                // Unshift
                array_unshift ($argumentsOfHook, $mappedObject->toArray ());
                break;

            case 'toDateString':
                // Check
                if (isset ($argumentsOfHook[1])) {
                    // Push 3rd parameter up one offset
                    $argumentsOfHook[2] = $argumentsOfHook[1];
                }

                // Make 2nd paramater, current object
                $argumentsOfHook[1] = $mappedObject->toInt ();
                break;

            default:
                // Throw
                throw new MethodNotMappedException;
                break;
        }

        // Save
        $savedObj = call_user_func_array ($nameOfFunction,
        $argumentsOfHook);

        // Return
        if (is_string   ($savedObj)) return new S ($savedObj);
        if (is_int      ($savedObj)) return new I ($savedObj);
        if (is_float    ($savedObj)) return new F ($savedObj);
        if (is_array    ($savedObj)) return new A ($savedObj);
        if (is_bool     ($savedObj)) return new B ($savedObj);
        if (is_resource ($savedObj)) return new R ($savedObj);
    }

    /**
     */
    protected function F ($mappedObject, $nameOfHook,
    $nameOfFunction, $argumentsOfHook) {
        // Switch
        switch ($nameOfHook) {
            case 'formatNumber':
                // Unshift
                array_unshift ($argumentsOfHook, $mappedObject->toFlt ());
                break;

            case 'toDateString':
                // Check
                if (isset ($argumentsOfHook[1])) {
                    // Push 3rd parameter up one offset
                    $argumentsOfHook[2] = $argumentsOfHook[1];
                }

                // Make 2nd paramater, current object
                $argumentsOfHook[1] = $mappedObject->toFlt ();
                break;

            default:
                // Throw
                throw new MethodNotMappedException;
                break;
        }

        // Save
        $savedObj = call_user_func_array ($nameOfFunction, $argumentsOfHook);

        // Return
        if (is_string   ($savedObj)) return new S ($savedObj);
        if (is_int      ($savedObj)) return new I ($savedObj);
        if (is_float    ($savedObj)) return new F ($savedObj);
        if (is_array    ($savedObj)) return new A ($savedObj);
        if (is_bool     ($savedObj)) return new B ($savedObj);
        if (is_resource ($savedObj)) return new R ($savedObj);
    }

    /**
     * Mapping is done using PHP's own __CALL method. By this method, and storing mappings in the objFuncMapper static variable, we can
     * determine if a specific method has been defined and registered to the mapper or not. If it was, we than issue a call_user_func
     * on the method (S, I, F, etc) that's going to do the mapping. Else, we just call the method as it was already been defined.
     *
     */
    public function __CALL ($nameOfHook, $argumentsOfHook) {
        // Check
        if (self::$objFuncMapper == NULL) {
            // Mappings
            self::mapMethodToFunction ('escapeCString',     'addcslashes');
            self::mapMethodToFunction ('escapeString',      'addslashes');
            self::mapMethodToFunction ('escapeMySQLString',	'mysql_real_escape_string');
            self::mapMethodToFunction ('toHex',             'bin2hex');
            self::mapMethodToFunction ('toChunk',           'chunk_split');
            self::mapMethodToFunction ('encryptIt',         'crypt');
            self::mapMethodToFunction ('chrToASCII',        'chr');
            self::mapMethodToFunction ('convertCYR',        'convert_cyr_string');
            self::mapMethodToFunction ('uDecode',           'convert_uudecode');
            self::mapMethodToFunction ('uEncode',           'convert_uuencode');
            self::mapMethodToFunction ('countChar',        	'count_chars');
            self::mapMethodToFunction ('toCRC32',           'crc32');
            self::mapMethodToFunction ('toHebrew',          'hebrev');
            self::mapMethodToFunction ('toNLHebrew',        'hebrevc');
            self::mapMethodToFunction ('entityDecode',      'html_entity_decode');
            self::mapMethodToFunction ('entityEncode',      'htmlentities');
            self::mapMethodToFunction ('charDecode',        'htmlspecialchars_decode');
            self::mapMethodToFunction ('charEncode',        'htmlspecialchars');
            self::mapMethodToFunction ('trimLeft',          'ltrim');
            self::mapMethodToFunction ('trimRight',         'rtrim');
            self::mapMethodToFunction ('trimBoth',			'trim');
            self::mapMethodToFunction ('toMD5File',         'md5_file');
            self::mapMethodToFunction ('toMD5',             'md5');
            self::mapMethodToFunction ('toMetaphoneKey',    'metaphone');
            self::mapMethodToFunction ('toMoneyFormat',     'money_format');
            self::mapMethodToFunction ('nL2BR',             'nl2br');
            self::mapMethodToFunction ('ordToASCII',        'ord');
            self::mapMethodToFunction ('qpDecode',          'quoted_printable_decode');
            self::mapMethodToFunction ('qpEncode',          'quoted_printable_encode');
            self::mapMethodToFunction ('toSHA1File',        'sha1_file');
            self::mapMethodToFunction ('toSHA1',            'sha1');
            self::mapMethodToFunction ('toSoundEx',         'soundex');
            self::mapMethodToFunction ('doCSV',             'str_getcsv');
            self::mapMethodToFunction ('replaceIToken',     'str_ireplace');
            self::mapMethodToFunction ('doPad',             'str_pad');
            self::mapMethodToFunction ('doRepeat',          'str_repeat');
            self::mapMethodToFunction ('doShuffle',         'str_shuffle');
            self::mapMethodToFunction ('toROT13',           'str_rot13');
            self::mapMethodToFunction ('doSplit',           'str_split');
            self::mapMethodToFunction ('toWordCount',       'str_word_count');
            self::mapMethodToFunction ('compareCaseTo',     'strcasecmp');
            self::mapMethodToFunction ('compareNCaseTo',    'strncasecmp');
            self::mapMethodToFunction ('compareTo',         'strcmp');
            self::mapMethodToFunction ('compareNTo',        'strncmp');
            self::mapMethodToFunction ('stripTags',         'strip_tags');
            self::mapMethodToFunction ('removeCStr',        'stripcslashes');
            self::mapMethodToFunction ('removeStr',         'stripslashes');
            self::mapMethodToFunction ('findIPos',          'stripos');
            self::mapMethodToFunction ('findPos',           'strpos');
            self::mapMethodToFunction ('findILPos',         'strripos');
            self::mapMethodToFunction ('findLPos',          'strrpos');
            self::mapMethodToFunction ('findIFirst',        'stristr');
            self::mapMethodToFunction ('findFirst',         'strstr');
            self::mapMethodToFunction ('findLast',          'strrchr');
            self::mapMethodToFunction ('doReverse',         'strrev');
            self::mapMethodToFunction ('toLength',          'strlen');
            self::mapMethodToFunction ('natCaseCmp',        'strnatcasecmp');
            self::mapMethodToFunction ('natCmp',            'strnatcmp');
            self::mapMethodToFunction ('charSearch',        'strpbrk');
            self::mapMethodToFunction ('doTokenize',        'strtok');
            self::mapMethodToFunction ('toLower',           'strtolower');
            self::mapMethodToFunction ('toUpper',           'strtoupper');
            self::mapMethodToFunction ('doTranslate',       'strtr');
            self::mapMethodToFunction ('doSubStr',          'substr');
            self::mapMethodToFunction ('doSubCompare',      'substr_compare');
            self::mapMethodToFunction ('doSubCount',        'substr_count');
            self::mapMethodToFunction ('doSubReplace',      'substr_replace');
            self::mapMethodToFunction ('wrapWords',         'wordwrap');
            self::mapMethodToFunction ('changeKeyCase',     'array_change_key_case');
            self::mapMethodToFunction ('doBZCompress',      'bzcompress');
            self::mapMethodToFunction ('doBZDecompress',    'bzdecompress');
            self::mapMethodToFunction ('doBZOpen',          'bzopen');
            self::mapMethodToFunction ('doLZFCompress',     'lzf_compress');
            self::mapMethodToFunction ('doLZFDecompress',   'lzf_decompress');
            self::mapMethodToFunction ('changeDirectoryPath',   'chdir');
            self::mapMethodToFunction ('scanDirectoryPath',     'scandir');
            self::mapMethodToFunction ('getCWorkingDir',    'getcwd');
            self::mapMethodToFunction ('stripSlashes',      'stripslashes');
            self::mapMethodToFunction ('eregReplace',       'ereg_replace');
            self::mapMethodToFunction ('fileGetContents',   'file_get_contents');
            self::mapMethodToFunction ('filePutContents',   'file_put_contents');
            self::mapMethodToFunction ('inArray',           'in_array');
            self::mapMethodToFunction ('fromStringToArray', 'explode');
            self::mapMethodToFunction ('fromArrayToString', 'implode');
            self::mapMethodToFunction ('getCSV',			'str_getcsv');
            self::mapMethodToFunction ('getURLHeaders',		'get_headers');
            self::mapMethodToFunction ('pregChange',       'preg_replace');
            self::mapMethodToFunction ('ucFirst',           'ucfirst');
            self::mapMethodToFunction ('pregMatch',         'preg_match');
            self::mapMethodToFunction ('pregMatchEach', 	'preg_match_all');
            self::mapMethodToFunction ('arrayShuffle',      'shuffle');
            self::mapMethodToFunction ('toUnixTimestamp',   'strtotime');
            self::mapMethodToFunction ('toDateString',      'date');
            self::mapMethodToFunction ('arraySlice',        'array_slice');
            self::mapMethodToFunction ('formatNumber',      'number_format');
            self::mapMethodToFunction ('gZipEncode',        'gzencode');
            self::mapMethodToFunction ('gZipCompress',      'gzcompress');
            self::mapMethodToFunction ('arraySearch',      	'array_search');
            self::mapMethodToFunction ('ucWords',           'ucwords');
            self::mapMethodToFunction ('decodeJSON',        'json_decode');
            self::mapMethodToFunction ('decodeURL', 		'urldecode');
            self::mapMethodToFunction ('encodeURL', 		'urlencode');
        }

        // Check
        if (isset (self::$objFuncMapper[$nameOfHook])) {
            // Ok, it's mapped
            return call_user_func (array ($this, get_class ($this)), $this,
            $nameOfHook, self::$objFuncMapper[$nameOfHook], $argumentsOfHook);
        } else {
            // Check
            if (is_callable ($nameOfHook)) {
                // Not mapped, go CALL
                return call_user_func ($nameOfHook, $this, $argumentsOfHook);
            } else {
                // Throw
                throw new MethodNotCallableException;
            }
        }
    }

    /**
     * Method is used inside inherited DTs to return to chain. It's prettier than return $this; and it allows for common, generic code
     * development in the future, based on specific rules. We don't know the rules yet, but keeping code future-proof, it's better if such
     * redundant, common code be kept in a specific method, just for safe-keeping and maintainability.
     *
     */
    protected function returnToChain () {
        // Return
        return $this;
    }

    /**
     * Should be same as __CALL!
     */
    public static function __CALLSTATIC ($nameOfHook, $argumentsOfHook) {
        // None
    }

    /**
     * Returns the class name of the given object. This can be used in situations where you want the class name of a given object, and
     * in case you don't give it a parameter, the method will return the CLASS name of the current (this) object. The returned value
     * is an S containing the name of the class the object was instantiated from.
     *
     * @author Catalin Alexandru Zamfir <catalin.zamfir@raphpframework.ro>
     * @copyright Under the terms of the GNU General Public License v3
     * @link http://php.net/get_class
     * @version $Id: Definitions.php 55 2012-11-07 13:56:35Z root $
     */
    public final function getObjectAncestry (M $objChecked = NULL) {
        // Check
        if ($objChecked != NULL) {
            // Return
            return new S (get_class ($objChecked));
        } else {
            // Return
            return new S (get_class ($this));
        }
    }
}

/**
 * Before we started to actually build the S (String), I (Integer), F (Float) and any other DT in the system, we needed
 * a base for all of our DTs. Why this?! Well, what if we have a method that just DOESN'T care what you pass to it, as long
 * as the value inside is of more importance than the kind of data type it needs. That's why, the 'O' DT was made for,
 * to be the backbone of all the other types.
 *
 * If you remember Java and you probably do, there was an Object class that could cold almost any value. It was generic and allowed
 * for Collections, Vectors, Lists and many other containers. With 'O', we're trying to achieve the same goal, giving a middle
 * ground for developer to exchange data between DTs, without too much mojo or magic behind these methods.
 */
class O extends M {
    /**
     * This method, __construct, will take the passed argument and issue it to ->setMix () which is going to be overloaded for
     * every object from here onwards. Thus, Strings, Integers, Floats will also have the setMix () method, but based on their
     * specific setSt/setInt/setBoolean methods, which for example can be a bridge between different DTs. What do I mean here?! Well,
     * for DT methods that return something different than the base type, the setMix () method can be used to set the proper value, while
     * doing the necessary checks. Also, for bug-free cod, every __constructor will make a call either to this->setMix () or to the
     * specific DT setting method, to set the value of that DT;
     *
     */
    public function __construct ($objArgument) {
        // Return
        return $this
        ->setMix ($objArgument);
    }

    /**
     * If by any chance, the current DT contains another DT as it's value, the toMix () method will go to any possible
     * recursive depth to find the needed information that the user has stored in. This way we only have a "virtual" one layer
     * of depth, because anything else will make the framework go at any depth to retrieve the information, which for the user
     * will seem like it has only one possible depth, and nothing more.
     *
     * We do this checking for infinite depths, because it would be possible to make things like new S (new S (new S ('something'))) using
     * the toMix () method which is just a waster of resources. Having only one layer deep, means that there will always be only one
     * type of data inside a DT, not nested DTs that, have no proper use.
     *
     */
    public final function toMix () {
        // Check
        if ($this->objContainer instanceof O) {
            // This, in theory, should end the cycle. Anyway, if you see this comment
            // it means that tests have been OK, and I've been to lazy to go back and remove this comment;
            return $this->objContainer->toMix ();
        } else {
            // If the container contains another object, get the contents from it;
            // This, I hope, will go recursive, because if have any kind of instanceof O,
            // it will go again, until it reaches a toMix, that will return it's objContainer;
            return $this->objContainer;
        }
    }

    /**
     * You will actually need to re-implement this method in any of the specific S, I, F, etc. DTs, because in it's current
     * form it will allow the framework to accept non-strong DTs, which is a big opened door for errors. For example, considering
     * we have a S DT, but we somehow use this method to set the contents of the DT to what we need. Then, we have
     * a bug, a logical error that can cause problems. Big ones! Why?! Because the S (String) would not contain a string anymore!
     *
     * As you can see from the code, this method, if passed an instance of a DT, will go to any depth in that DT, to
     * find the proper content it needs to pass. This way, we can actually make a copy variable out of another. This feature must
     * be documented because many times, because of it's simplicity is left outside the scope or the user of the framework user,
     * maybe due to the lack of "publicity" between other features.
     *
     */
    protected function setMix ($objArgument) {
        // Check
        if ($objArgument instanceof O) {
            // This is to copy the value from object A to B, not to make it an object;
            // Set the container to passedArgument->toMix ();
            $this->objContainer = $objArgument->toMix ();
        } else {
            // Set the container to passedArgument;
            $this->objContainer = $objArgument;
        }

        // Return
        return $this->returnToChain ();
    }

    /**
     * And yes, I know 'gettype' is rather SLOW, but taking in account the fact that it's not used that often, we guess it's a
     * good decision to live it here. A switch with is_* functions may have been quicker, in theory, but nevertheless, relying
     * on a PHP function is better than implementing user-land code. The chances that we're ever going to need it that much ar
     * quite slim, because of the fact that with STH, we already know what type of variable we're using. The only exception is
     * the 'O' DT, for which we needed this.
     *
     */
    public final function getDataType () {
        // Return
        return new S (gettype
        ($this->objContainer));
    }

    /**
     * As above, it's somewhat of an 'O' specific method, meaning that it's use is common only in in the 'O' DT, and quite
     * questionable in DTs that inherit from O. In any case, you guessed it, it changes the PHP-type of the content inside
     * the DT, to anything we pass as the argument.
     *
     */
    public final function setDataType ($typeToConvertTo) {
        // Check
        if (is_string ($typeToConvertTo)) {
            // Check
            if (settype ($this
            ->objContainer,
            $typeToConvertTo)) {
                // Return
                return $this->returnToChain ();
            }
        } else if ($typeToConvertTo instanceof S) {
            // Check
            if (settype ($this
            ->objContainer, $typeToConvertTo)) {
                // Return
                return $this->returnToChain ();
            }
        }
    }
}
/**
 * B: Boolean DT, used to represent a TRUE/FALSE value, which can be at any time converted to an integer or even string
 * if the proper __toString/toString methods are implemented!
 *
 * A rather complex way of representing a boolean, by assigning it's value to an object. The good part of having and passing
 * objects instead of true PHP types is that abnormal changes in value can be detected. It would be horrible for a boolean
 * value to wildly change it's value due to weird assignments.
 */
class B extends M {
    /**
     * The __constructor is used to pass the proper variable at construction time. It relies on the setBoolean method, which can
     * be used specific to this method, to enable proper variable initialization.
     *
     * Most of the methods should however rely on an overloaded setMix () method, or vice-versa, where the overloaded setMix ()
     * method is just a call to the set[SpecificDT] method. Either way, the code here must be 100% sure that the type of
     * the CLASS == the type of what that object contains. Anything else, and it should output an error.
     *
     */
    public function __construct ($objArgument) {
        // Return
        return $this
        ->setBoolean ($objArgument);
    }

    /**
     * This method, toBoolean, will return the boolean value of the container, doing the same thing as toMix, making objects
     * inside the current object transparent to any depth making it a good way to avoid recursion.
     *
     */
    public final function toBoolean () {
        // Check
        if ($this
        ->objContainer instanceof B) {
            // Return
            return $this
            ->objContainer
            ->toBoolean ();
        } else {
            // Return
            return $this->objContainer;
        }
    }

    /**
     * This method, switchType, will switch the boolean value of the container, by negating it as is the quickest way in the west
     * to do it, and the chosen way for us.
     *
     */
    public final function switchType () {
        // Negate
        $this->objContainer = !($this->objContainer);

        // Return
        return $this->returnToChain ();
    }

    /**
     * It does the comparison between the container, and the booleans TRUE/FALSE. It issues 1 or 0 in those cases, or an error
     * screen if something else has happened. It's error-proof, because it doesn't test THE absolute TRUE/FALSE values, leaving
     * that kind of debate to the day to day programmer, where he would to the comparison by returning the proper value.
     *
     */
    public final function getAsInt () {
        // Check
        if ($this->objContainer == TRUE) {
            // TRUE
            return new I (1);
        } else if ($this->objContainer == FALSE) {
            // FALSE
            return new I (0);
        }
    }

    /**
     * This method, setBoolean, will check if the passed arguments is of boolean type, and if it is, it will set the container
     * to that value or else, it will output an error indicating that the code has either executed wrong, or the developer
     * is using this function sideways.
     *
     * This method is specific to the B (Boolean) DT, in the fact that what it will do is set the contained variable to
     * TRUE/FALSE, only if the passed argument is of type Boolean or Integer. In the second case it will try to determine if the I
     * DT is 0 or != 0, and set the TRUE/FALSE boolean types accordingly.
     *
     */
    public final function setBoolean ($objArgument) {
        // Check
        if (is_bool ($objArgument)) {
            // Set
            $this->objContainer = $objArgument;
        } else if ($objArgument instanceof B) {
            // Set
            $this->objContainer = $objArgument->toBoolean ();
        } else if ($objArgument instanceof I) {
            // Check
            if ($objArgument->toInt () != 0) {
                // Set
                $this->objContainer = TRUE;
            } else {
                // Set
                $this->objContainer = FALSE;
            }
        } else {
            // Throw
            throw new NotBooleanException;
        }

        // Return
        return $this->returnToChain ();
    }
}

/**
 * I: Integer DT, used to represent integers in the context of our framework.
 *
 * There's a warning to be set here: because of the fact that PHP doesn't support operator overloading, we're forced to add
 * methods that actually do incrementation/decrementation, etc. (and other) possible integer operations, which on a daily
 * basis is quite annoying. If we don't pass integers as parameters, than the PHP integer type should be used, but if a method
 * requires a specific integer to be passed, than we are forced to use the framework I (Integer) DT, which will allow
 * our famous Strict Type Hinting to come in effect and assure us of proper functionality.
 */
class I extends M {
    /**
     * The __constructor is used to pass the proper variable at construction time. It relies on the setInt method, which can
     * be used specific to this method, to enable proper variable initialization.
     *
     * Most of the methods should however rely on an overloaded setMix () method, or vice-versa, where the overloaded setMix ()
     * method is just a call to the set[SpecificDT] method. Either way, the code here must be 100% sure that the type of
     * the CLASS == the type of what that object contains. Anything else, and it should output an error;
     *
     */
    public function __construct ($objArgument) {
        // Return
        return $this
        ->setInt ($objArgument);
    }

    /**
     * This method, toInt, will return the integer value of the container. It doesn't do any further checks, but relies on the
     * fact that the setInt/setMix methods do their job right.
     *
     * As you can see from the code, the toInt () method is quite different from the toMix () method, because of the fact that
     * the setter methods do the actual checking of the code. What I mean is that because of the fact that the 'depth recursion'
     * problem is done when 'inserting' the contents of the DT, we're free of doing the same functionality when we want to
     * return the value from it;
     *
     * You may wonder why we don't have a __toString/toString method. Well, in theory (but just in theory), PHP integer to string
     * casting is done OK and bug-free, meaning that our M::__toString/toString methods will do their job right.
     *
     */
    public function toInt () {
        // Return
        return $this
        ->objContainer;
    }

    /**
     * This method, setInt, will check that the passed argument is of integer type, or will output an error if not. Along with
     * ->setMix, this method assures that the container can hold ONLY one integer type.
     *
     * Looking at the toInt () method, you can see that, contrary to the parent DT, we're doing the check for recursiveness
     * when calling the setInt () method, instead of "hiding the damage" in the toInt () method. In some edge cases this is little
     * check would've been much faster than doing depth recursion;
     *
     */
    public function setInt ($objArgument) {
        // Check
        if (is_int ($objArgument)) {
            // Set
            $this->objContainer = $objArgument;
        } else if ($objArgument instanceof I) {
            // Set
            $this->objContainer = $objArgument->toInt ();
        } else {
            // Throw
            throw new NotIntegerException;
        }

        // Return
        return $this->returnToChain ();
    }

    /**
     * This method, doInc, will increment the container with the required amount. If no argument is passed, the doInc method
     * will increment the container by one. Non integer parameters have no effect thus no checking is done for performance reasons.
     *
     */
    public function doInc ($amountTo = NULL) {
        // Check
        if ($amountTo instanceof I) {
            // Set
            $this->objContainer += $amountTo->toInt ();
        } else {
            // Set
            ($amountTo !== NULL) ?
            ($this->objContainer += $amountTo) :
            (++$this->objContainer);
        }

        // Return
        return $this->returnToChain ();
    }

    /**
     * This method, doDec, will decrement the container by the given amount, or if the passed argument is of NULL type (the
     * default parameter) it will reduce the container by one. Non integer parameters have no effect thus no checking is done.
     *
     */
    public function doDec ($amountTo = NULL) {
        // Check
        if ($amountTo instanceof I) {
            // Set
            $this->objContainer -= $amountTo->toInt ();
        } else {
            // Set
            ($amountTo !== NULL) ?
            ($this->objContainer -= $amountTo) :
            (--$this->objContainer);
        }

        // Return
        return $this->returnToChain ();
    }

    /**
     * This method, doMtp, will double the container (container * 2) or multiply it by a given value. It's the same mechanism
     * as in the case of doInc/doDec.
     *
     */
    public function doMtp ($withWhat = NULL) {
        // Check
        if ($withWhat instanceof I) {
            // Set
            $this->objContainer *= $withWhat->toInt ();
        } else {
            // Set
            ($withWhat !== NULL) ?
            $this->objContainer *= $withWhat :
            $this->objContainer *= 2;
        }

        // Return
        return $this->returnToChain ();
    }

    /**
     * This method, doDiv, will divide the container by the givena amount, taking great care so that divisions by 0, won't
     * output weird errors.
     *
     */
    public function doDiv ($byWhat) {
        // Check
        if ($this->objContainer == 0) {
            // Throw
            throw new DivisionByZeroException;
        } else {
            // Check
            if ($byWhat instanceof I) {
                // Check
                if ($byWhat
                ->toInt () != 0) {
                    // Set
                    $this->objContainer /= $byWhat->toInt ();

                    // Return
                    return $this->returnToChain ();
                }
            } else {
                if ($byWhat != 0) {
                    // Set
                    $this->objContainer /= $byWhat;

                    // Return
                    return $this->returnToChain ();
                }
            }
        }
    }

    /**
     * This method, doMod, will module the container by the given amount, taking care of the instanceof I, or simple integer
     * scheme. It will set the container to that % (mod). It does the quick modulus operations, so the processor won't be to
     * caught-up with parsing tokens.
     *
     */
    public function doMod ($byWhat) {
        // Check
        if ($byWhat instanceof I) {
            // Set
            $this->objContainer %= $byWhat->toInt ();
        } else {
            // Set
            $this->objContainer %= $byWhat;
        }

        // Return
        return $this->returnToChain ();
    }
}

/**
 * F: Float DT, extended from I, it allows us to retain and do calculations on floating values.
 *
 * As you can see, we extend from the I (Integer) DT, because we want to use the basic operations that the doInc, doDec,
 * doMtp, doDiv, doMod methods do. If you look at the code in those methods you'll see that we don't do any checking of
 * the passed parameters, because we expect the developer to pass a proper I or integer as a parameter. Also, passing wrong values,
 * then PHP does the proper thing of matching floats with integers and ignoring integers and strings, or floats and strings getting
 * added to each other.
 *
 * If we really did some kind of checking than a layer of incompatibility between integers and floats would have appeared,
 * require a series of methods that would've fixed the issue. This way, we are sure that a division by string for example,
 * if the passed parameter is not a numeric value will raise an error which we'll be able to catch in our error handler, thus
 * reaching our goal of identifying such mistakes. It's probable that future versions of the framework will implement a better
 * integer/float DT, while keeping backward compatibility with this code, maybe leveraging on the power of SPL_Types if they ever
 * come in existence.
 */
class F extends I {
    /**
     * The constructor uses the setFlt method, which implements the actual code. As we see here, actually passing floats needs
     * that the passed parameter be an actual float. We don't test for integers or convert them to float, but we do accept our
     * types of I.
     *
     * As you can see, we're implementing the integer/float layer of our framework to be compatible only with the DTs
     * provided by our framework. Anything else would allow for some serious bugs or useless checks which isn't our goal for the
     * moment. Of course we will provide better support for this layer as soon as need for it is proven a de-facto standard.
     *
     */
    public function __construct ($objArgument) {
        // Return
        return $this
        ->setFlt ($objArgument);
    }

    /**
     * Again, a specific method is used to return the value contained in the DT, while the parent toInt () method is still
     * callable. For the moment need has made that F be a direct descendat of I, but it seems that usage imposes that F be an
     * extension of M, rather than an extension of I, just to have a better layout of our basic DTs.
     *
     */
    public function toFlt () {
        // Return
        return $this
        ->objContainer;
    }

    /**
     * Set the F (Float) DT to a specified value.
     *
     * If you've read this far you're probably used to the architecture in which we have built the sistem. This method is not
     * that different. It implements the code needed to set the specific value of the DT, and expects a float parameter or
     * at least an instance of I, whichever comes first.
     *
     */
    public function setFlt ($objArgument) {
        // Check
        if (is_float ($objArgument)) {
            // Set
            $this->objContainer = $objArgument;
        } else if ($objArgument instanceof I) {
            // Set
            $this->objContainer = $objArgument->toInt ();
        } else {
            // Throw
            throw new NotFloatException;
        }

        // Return
        return $this->returnToChain ();
    }
}

/**
 * S: String DT, allows us to store strings, and have a bunch of mapped or un-mapped methods on them.
 *
 * This DT, extends from M, as it's a base DT for our framework. As the above already known DTs, it supports
 * mapping, and many of the string operation methods are implemented as mapped methods between the PHP and our framework.
 */
class S extends M implements ArrayAccess {
    /**
     * With this class we chose not to provide any documentation whatsoever, because the code itself is sufficient enough to
     * let the developer understand what is happening 'under-the-hood'. That's why we've set the 'access' comment parameter to it
     * for private, so it will be skipped when generating documentation.
     *
     */
    public function offsetGet ($offsetKey) {
        // Return
        return $this
        ->objContainer[$offsetKey];
    }

    /**
     * With this class we chose not to provide any documentation whatsoever, because the code itself is sufficient enough to
     * let the developer understand what is happening 'under-the-hood'. That's why we've set the 'access' comment parameter to it
     * for private, so it will be skipped when generating documentation.
     *
     */
    public function offsetSet ($offsetKey, $offsetVar) {
        // Return
        return $this
        ->objContainer[$offsetKey] =
        $offsetVar;
    }

    /**
     * With this class we chose not to provide any documentation whatsoever, because the code itself is sufficient enough to
     * let the developer understand what is happening 'under-the-hood'. That's why we've set the 'access' comment parameter to it
     * for private, so it will be skipped when generating documentation.
     *
     */
    public function offsetExists ($offsetKey) {
        // Return
        return isset ($this
        ->objContainer[$offsetKey]);
    }

    /**
     * With this class we chose not to provide any documentation whatsoever, because the code itself is sufficient enough to
     * let the developer understand what is happening 'under-the-hood'. That's why we've set the 'access' comment parameter to it
     * for private, so it will be skipped when generating documentation.
     *
     */
    public function offsetUnset ($offsetKey) {
        // Return
        return $this
        ->objContainer[$offsetKey] =
        NULL;
    }

    /**
     * The constructor, as in any other cases, takes the argument passed to it and saves in the DT, checking it to be an
     * actual string in this case. If it doesn't it will echo an error at least. It will return the current DT instance,
     * so the instance can be assigned to a variable.
     *
     */
    public function __construct ($objArgument = _NONE) {
        // Return
        return $this
        ->setString ($objArgument);
    }

    /**
     * We redefine the 'M' __toString method, because we already know that the contents of the DT is a string, so we just
     * might return the content, without any othr casting or checks, which, of course, is a bit faster, not by much, but even that
     * performance gain is of great importance to us.
     *
     */
    public function __toString () {
        // Return
        return $this->objContainer;
    }

    /**
     * Expects a string parameter, or uses the default one, whichever comes first, this method, as any other setter methods in
     * the context of our DTs, will set the container to the string parameter we pass to it; Besides PHP strings, we also
     * accept instaces of S, from which we copy the information.
     *
     */
    public function setString ($objArgument = _NONE) {
        // Check
        if (is_string ($objArgument)) {
            // Set
            $this->objContainer =
            $objArgument;
        } else if ($objArgument instanceof S) {
            // Set
            $this->objContainer = (string)
            $objArgument;
        } else {
            // Throw
            throw new NotStringException;
        }

        // Return
        return $this->returnToChain ();
    }

    /**
     * Appends a string (or the given argument) to  the current string.
     *
     */
    public function appendString ($objArgument) {
        // Check
        if ($objArgument == NULL) {
            // Return
            return $this->returnToChain ();
        }

        // Check
        if ($objArgument instanceof S) {
            // Set
            $this->objContainer .= (string)
            $objArgument;
        } else if (is_string ($objArgument)) {
            // Set
            $this->objContainer .=
            $objArgument;
        } else {
            // Throw
            throw new NotStringException;
        }

        // Return
        return $this->returnToChain ();
    }

    /**
     * Prepends a string (or the given argument) to  the current string.
     *
     */
    public function prependString ($objArgument) {
        // Check
        if ($objArgument == NULL) {
            // Return
            return $this->returnToChain ();
        }

        // Check
        if ($objArgument instanceof S) {
            // Set
            $this->objContainer =
            $objArgument . $this
            ->objContainer;
        } else if (is_string ($objArgument)) {
            // Set
            $this->objContainer = $objArgument .
            $this->objContainer;
        } else {
            // Throw
            throw new NotStringException;
        }

        // Return
        return $this->returnToChain ();
    }

    /**
     * If we were that lazy, we could have mapped 'str_replace' to 'doToken', in our DTs, but because we use doToken
     * quite extensivelly, for performance reasons, we chose to implement the actual code. From our perspective any mapped
     * PHP function to DT methods, that are used extensivelly should have code implemented in the core of the framework.
     *
     * The above ideology allows us to have a limited set of operations on a DT, while we would give great care only to that
     * set of methods that are used extensivelly.
     *
     */
    public function doToken ($whatToReplace, $withWhatToReplace) {
        $this->objContainer = str_replace ($whatToReplace,
        $withWhatToReplace, $this->objContainer);
        return $this->returnToChain ();
    }
}

/**
 * R: Resource DT, just a container for any PHP returned resources;
 *
 * For the moment, the R DT is a placeholder for common resource results. We will be able to develop this DT,
 * according to specific returned resources, if PHP allows us to do that. For example, if R detects it contains a MySQL
 * resource, it could automatically transform the resource into a result, or it could save it for example to a file, for
 * swapping on it if it's a big resource.
 *
 * In short, God knows what we can do with a DT like R, but because we must think ahead, we just can't live a door
 * placeholder here, and come back to put the actual door here after the walls have been finished, because that would mean
 * that we have to destroy the walls so we can insert the frame of the door in the door placeholder. So we think ahead and
 * issue a placeholder for resources as a specific DT that we can use inside our code.
 */
class R extends M {
    /**
     * Expecting a resource as an argument; the constructor will set the container to the specified resource. Different from other
     * DT constructors, is that it MUST have an argument passed at construction time, answering the question: why do you
     * need an R DT, without anything in it.
     *
     */
    public function __construct ($objArgument) {
        // Return
        return $this
        ->setResource ($objArgument);
    }

    /**
     * The method will return the resource from its container. For the moment, as we've said in the DT description, we
     * don't allow for something specific to be done here, but the R DT can be extended to be used as a MySQL Resource,
     * or any other kind of Resource, and the ->toResource method can be used to do specific-by-resource operations on it's
     * contents.
     *
     */
    public function toResource () {
        // Return
        return $this
        ->objContainer;
    }

    /**
     * As any other DT setter method, it sets the internal container to the passed resource. It expects a resource or an R
     * DT as argument, and it will return the current instance of the DT, so we can use it further down the line.
     *
     */
    public function setResource ($objArgument) {
        // Check
        if ($objArgument instanceof R) {
            // Cjecl
            if (is_resource ($objArgument->toMix ())) {
                // Set
                $this->objContainer =
                $objArgument->toMix ();
            }
        } else if (is_resource ($objArgument)) {
            // Set
            $this->objContainer =
            $objArgument;
        } else {
            // Throw
            throw new NotResourceException;
        }

        // Return
        return $this->returnToChain ();
    }
}

/**
 * Array implementation on our framework, based on the SPL, developed by Marcus Boerger;
 *
 * What we first did, before starting to implement any DTs, is that we started to implement true compatible SPL-type
 * arrays in our framework. We've gone from one-depath arrays which were sadly used quite extensivelly, to having our array
 * implementation be able to support multiple-depth arrays, based on the offsetSet/offsetGet from the SPL.
 */
class A extends M implements ArrayAccess, Iterator, RecursiveIterator, SeekableIterator, Countable {
    /**
     * The __constructor, gets the passed parameter and checks if it's an array, on which case it goes recursive for it, or
     * advances and properly sets the container to the passed parameter, or it will output an error in the last case.
     *
     */
    public function __construct ($objArgument = NULL) {
        // Check
        if ($objArgument != NULL) {
            // Foreach
            foreach ($objArgument as $objK => $objV) {
                // Check
                if (is_array ($objV)) {
                    // Set
                    $this->objContainer[$objK] = new A ($objV);
                } else {
                    // Set
                    $this->objContainer[$objK] = $objV;
                }
            }
        } else {
            // Empty
            $this->objContainer = Array ();
        }
    }

    /**
     * Sadly, PHP doesn't support array context, which means that we're obligated that when we pass an array to a PHP method,
     * to be sure we use the ->toArray () method. There is a fix for this, in that we can implement any known PHP function as
     * a DT method, which should make things a little bit speedy and easier to use.
     *
     */
    public function toArray () {
        // Return
        return $this->objContainer;
    }

    /**
     * We use this method to return only the values of the stored array, without modyfing the container. To modify the container
     * we need another inherent method that will do that, for exampe 'setToValues ()', or any other name you can think of.
     *
     */
    public function toValues () {
        // Return
        return new
        A (array_values ($this
        ->objContainer));
    }

    /**
     * We use this method to return only the keys of the stored array, without modyfing the stored array. Again, we need another
     * method that will actually modify the stored array, if that is what we want.
     *
     */
    public function toKeys () {
        // Return
        return new
        A (array_keys ($this
        ->objContainer));
    }

    /**
     * Method to return the RecursiveIteratorIterator (... Iterator :D ...) of the current contained array. This allows us to
     * do a recursive representation of an array with a foreach loop. See: SPL for more information
     *
     */
    public function toRecursive () {
        // Return
        return new
        RecursiveIteratorIterator
        ($this);
    }

    /**
     * With this class/method we chose not to provide any documentation whatsoever, because the code itself is sufficient enough to
     * let the developer understand what is happening 'under-the-hood'. That's why we've set the 'access' comment parameter to it
     * for private, so it will be skipped when generating documentation.
     *
     */
    public function current () {
        // Return
        return current ($this
        ->objContainer);
    }

    /**
     * Imlementing the SPL next () method, to allow us to do the 'foreach' construct on our object. There's no need for too much
     * extensive documentation on these set of methods that implement the SPL. Check out the SPL for more details.
     *
     */
    public function next () {
        // Return
        return next ($this
        ->objContainer);
    }

    /**
     * With this class/method we chose not to provide any documentation whatsoever, because the code itself is sufficient enough to
     * let the developer understand what is happening 'under-the-hood'. That's why we've set the 'access' comment parameter to it
     * for private, so it will be skipped when generating documentation.
     *
     * return boolean TRUE/FALSE, depending on the result of the FALSE !== current ();
     */
    public function valid () {
        // Return
        return key ($this
        ->objContainer) !== NULL;
    }

    /**
     * Imlementing the SPL rewind () method, to allow us to do the 'foreach' construct on our object. There's no need for too much
     * extensive documentation on these set of methods that implement the SPL. Check out the SPL for more details.
     *
     * return boolean TRUE/FALSE, depending on the result of the FALSE !== next ()
     */
    public function rewind () {
        // Return
        return reset ($this
        ->objContainer);
    }

    /**
     * With this class/method we chose not to provide any documentation whatsoever, because the code itself is sufficient enough to
     * let the developer understand what is happening 'under-the-hood'. That's why we've set the 'access' comment parameter to it
     * for private, so it will be skipped when generating documentation.
     *
     */
    public function key () {
        // Return
        return key ($this
        ->objContainer);
    }

    /**
     * With this class/method we chose not to provide any documentation whatsoever, because the code itself is sufficient enough to
     * let the developer understand what is happening 'under-the-hood'. That's why we've set the 'access' comment parameter to it
     * for private, so it will be skipped when generating documentation.
     *
     */
    public function count () {
        // Return
        return sizeof ($this
        ->objContainer);
    }

    /**
     * With this class/method we chose not to provide any documentation whatsoever, because the code itself is sufficient enough to
     * let the developer understand what is happening 'under-the-hood'. That's why we've set the 'access' comment parameter to it
     * for private, so it will be skipped when generating documentation.
     *
     */
    public function hasChildren () {
        // Return
        return ($this
        ->current () instanceof
        self);
    }

    /**
     * With this class/method we chose not to provide any documentation whatsoever, because the code itself is sufficient enough to
     * let the developer understand what is happening 'under-the-hood'. That's why we've set the 'access' comment parameter to it
     * for private, so it will be skipped when generating documentation.
     *
     */
    public function getChildren () {
        // Return
        return $this
        ->current ();
    }

    /**
     * With this class/method we chose not to provide any documentation whatsoever, because the code itself is sufficient enough to
     * let the developer understand what is happening 'under-the-hood'. That's why we've set the 'access' comment parameter to it
     * for private, so it will be skipped when generating documentation.
     *
     */
    public function seek ($objIndex) {
        // Set
        $this->rewind ();
        $objPosition = 0;

        // Go
        while ($objPosition < $objIndex && $this->valid ()) {
            $this->next ();
            $objPosition++;
        }

        // Check
        if (!$this->valid ()) {
            // Throw
            throw new OutOfBoundsException;
        }
    }

    /**
     * With this class/method we chose not to provide any documentation whatsoever, because the code itself is sufficient enough to
     * let the developer understand what is happening 'under-the-hood'. That's why we've set the 'access' comment parameter to it
     * for private, so it will be skipped when generating documentation.
     *
     */
    public function offsetSet ($offsetKey, $offsetString) {
        // Check
        if ($offsetKey === NULL) {
            // Set
            $this->objContainer[] =
            $offsetString;

            // Return
            return $this->returnToChain ();
        }

        // Check
        if (is_array ($offsetString)) {
            // Set
            $offsetString = new
            A ($offsetString);

            // Return
            return $this->returnToChain ();
        }

        // Check
        if ($offsetKey instanceof S) {
            // Set
            if ($this->objContainer[(string)
            $offsetKey] = $offsetString) {
                // Return
                return $this->returnToChain ();
            } else {
                // Throw
                throw new OffsetKeyNotSetException;
            }
        } else if ($offsetKey instanceof I) {
            // Check
            if ($this->objContainer[$offsetKey
            ->toInt ()] = $offsetString) {
                // Return
                return $this->returnToChain ();
            } else {
                // Throw
                throw new OffsetKeyNotSetException;
            }
        } else {
            // Check
            $this
            ->objContainer[$offsetKey] =
            $offsetString;

            // Return
            return $this->returnToChain ();
        }
    }

    /**
     * With this class/method we chose not to provide any documentation whatsoever, because the code itself is sufficient enough to
     * let the developer understand what is happening 'under-the-hood'. That's why we've set the 'access' comment parameter to it
     * for private, so it will be skipped when generating documentation.
     *
     */
    public function offsetGet ($offsetKey) {
        // Check
        if ($offsetKey instanceof S) {
            // Check
            if (!isset ($this
            ->objContainer[(string)
            $offsetKey])) {
                // Set
                $this->objContainer[(string)
                $offsetKey] = new A;
            }

            // Check
            if (isset ($this
            ->objContainer[(string)
            $offsetKey])) {
                // Set
                return $this
                ->objContainer[(string)
                $offsetKey];
            } else {
                // Throw
                throw new OffsetKeyNotSetException;
            }
        } else if ($offsetKey instanceof I) {
            // Check
            if (!isset ($this
            ->objContainer[$offsetKey
            ->toInt ()])) {
                // Set
                $this->objContainer[$offsetKey
                ->toInt ()] = new A;
            }

            // Check
            if (isset ($this
            ->objContainer[$offsetKey
            ->toInt ()])) {
                // Set
                return $this
                ->objContainer[$offsetKey
                ->toInt ()];
            } else {
                // Throw
                throw new OffsetKeyNotSetException;
            }
        } else {
            // Check
            if (!isset ($this
            ->objContainer[$offsetKey])) {
                // Set
                $this->objContainer[$offsetKey] = new A;
            }

            // Check
            if (isset ($this
            ->objContainer[$offsetKey])) {
                // Set
                return $this
                ->objContainer[$offsetKey];
            } else {
                // Throw
                throw new OffsetKeyNotSetException;
            }
        }
    }

    /**
     * With this class/method we chose not to provide any documentation whatsoever, because the code itself is sufficient enough to
     * let the developer understand what is happening 'under-the-hood'. That's why we've set the 'access' comment parameter to it
     * for private, so it will be skipped when generating documentation.
     *
     */
    public function offsetUnset ($offsetKey) {
        // Check
        if ($offsetKey instanceof S) {
            // Check
            if (isset ($this
            ->objContainer[(string)
            $offsetKey])) {
                // Unset
                unset ($this
                ->objContainer[(string)
                $offsetKey]);
            } else {
                // Throw
                throw new OffsetKeyNotSetException;
            }
        } else if ($offsetKey instanceof I) {
            // Check
            if (isset ($this
            ->objContainer[$offsetKey
            ->toInt ()])) {
                // Unset
                unset ($this
                ->objContainer[$offsetKey
                ->toInt ()]);
            } else {
                // Throw
                throw new OffsetKeyNotSetException;
            }
        } else {
            // Check
            if (isset ($this
            ->objContainer[$offsetKey])) {
                // Unset
                unset ($this
                ->objContainer[$offsetKey]);
            } else {
                // Throw
                throw new OffsetKeyNotSetException;
            }
        }
    }

    /**
     * With this class/method we chose not to provide any documentation whatsoever, because the code itself is sufficient enough to
     * let the developer understand what is happening 'under-the-hood'. That's why we've set the 'access' comment parameter to it
     * for private, so it will be skipped when generating documentation.
     *
     */
    public function offsetExists ($offsetKey) {
        // Check
        if ($offsetKey instanceof S) {
            // Return
            return isset ($this
            ->objContainer[(string)
            $offsetKey]);
        } else if ($offsetKey instanceof I) {
            // Return
            return isset ($this
            ->objContainer[$offsetKey
            ->toInt ()]);
        } else {
            // Return
            return isset ($this
            ->objContainer[$offsetKey]);
        }
    }

    /**
     * With this class/method we chose not to provide any documentation whatsoever, because the code itself is sufficient enough to
     * let the developer understand what is happening 'under-the-hood'. That's why we've set the 'access' comment parameter to it
     * for private, so it will be skipped when generating documentation.
     */
    public function arrayUnShift () {
        // Get
        $getFunctionArguments = func_get_args ();

        // Foreach
        foreach ($getFunctionArguments
        as $objK => $objV) {
            // Set
            array_unshift ($this
            ->objContainer, $objV);
        }

        // Return
        return $this->returnToChain ();
    }

    /**
     * With this class/method we chose not to provide any documentation whatsoever, because the code itself is sufficient enough to
     * let the developer understand what is happening 'under-the-hood'. That's why we've set the 'access' comment parameter to it
     * for private, so it will be skipped when generating documentation.
     */
    public function arrayReverse () {
        // Set
        $this->objContainer = array_reverse ($this->objContainer);

        // Return
        return $this->returnToChain ();
    }

    /**
     * With this class/method we chose not to provide any documentation whatsoever, because the code itself is sufficient enough to
     * let the developer understand what is happening 'under-the-hood'. That's why we've set the 'access' comment parameter to it
     * for private, so it will be skipped when generating documentation.
     *
     */
    public function doCount () {
        // Return
        return new I (sizeof ($this
        ->objContainer));
    }
}

/**
 * Path: Implementing Path, as relative paths from the webroot. They return error if the path we're trying to work on
 * doesn't exist.
 *
 * We need some kind of verification mechanism for file paths that exist. Also, we wanted something that can be quickly
 * extendeded from SPL, and from our S (String) DT, which can allow us to check the existence of files at any USE time of
 * our files in the framework. What do mean by this?!
 */
class Path extends S {
    /**
     * @var boolean $pathExists Contains true/false, if the current path exists;
     */
    protected $pathExists = NULL;

    /**
     * As you can see, when constructiong a Path object, we're actually eager to have the possibility to trick the error
     * mechanism if we pass a non-existent path. We do that, because of the fact that we're actually trying to create a file,
     * before we use it, and that means that upon creation, we should be able to trick the error mechanism until the file is
     * created.
     *
     */
    public function __construct ($objArgument,
    $objNotFoundException = TRUE) {
        // Return
        return $this
        ->setPath ($objArgument,
        $objNotFoundException);
    }

    /**
     * This method will return the existing file path, no matter what changes have been done to it. This means that in the case
     * we have passed a non-existing path, it will return the string. It's probable that if we have an existing path, files will
     * be moved.
     *
     */
    public function toExistingPath () {
        // Return
        return new S ($this
        ->objContainer);
    }

    /**
     * This method will return the relative path from where the root of the application was instaleed. This means, that before
     * returning anything it will do a str_replace on the path, replacing any occurence of the document root, in the current
     * contained path.
     *
     */
    public function toRelativePath () {
        // Return
        return new S ($this
        ->objContainer);
    }

    /**
     * At the moment of calling our ->to* methods, we don't know if we have a path that contains the document root or not,
     * which means, that in order to be bug-proof, we will first do a replacement on the path, to block-out any still existing
     * occurence of the document root in the stored file path, after which we will do a string prepending of the document root;
     *
     * We could avoid such operations by actually checking the passed parameters at insertion/update times, thing that we'll be
     * doing in the future versions of the framework, while providing backward compatibility;
     *
     */
    public function toAbsolutePath () {
        // Return
        return Architecture::pathTo (Architecture
        ::getRoot (), $this->objContainer);
    }

    /**
     * Once in a while we need to check some special attributes of our stored file path, like the readable, writeable and
     * executable attributes, which will tell us what possible actions can we execute on the file, without echoing an error
     * or something. This is what this method does, if you pass the proper parameter;
     *
     */
    public function checkPathIs ($whatToCheckFor) {
        // Check
        if ($this->pathExists->toBoolean () == TRUE) {
            // Switch
            switch ($whatToCheckFor) {
                case 'readable':
                    return new
                    B (is_readable ($this
                    ->toAbsolutePath ()));
                    break;

                case 'writeable':
                    return new
                    B (is_writeable ($this
                    ->toAbsolutePath ()));
                    break;

                case 'executable':
                    return new
                    B (is_executable ($this
                    ->toAbsolutePath ()));
                    break;

                case 'file':
                    return new
                    B (is_file ($this
                    ->toAbsolutePath ()));
                    break;

                case 'symlink':
                    return new
                    B (is_link ($this
                    ->toAbsolutePath ()));
                    break;

                case 'uploaded':
                    return new
                    B (is_uploaded_file ($this
                    ->toAbsolutePath ()));
                    break;
            }
        } else {
            // Return
            return $this->pathExists;
        }
    }

    /**
     * This method will allow a chmod/chgrp on the specified file. It states that the file belongs to the user in the Apache/IIS
     * environment, which means that it will not check if the file does belong or not. In case it can't change the permissions on
     * the file, it will output a pure error, without no fall-back;
     *
     */
    public function setFileOwnership ($modeAccess, $whatKindOfAccess) {
        // Check
        if ($this->pathExists->toBoolean () == TRUE) {
            // Switch
            switch ($whatKindOfAccess) {
                case 'group':
                    return new B (chgrp ($this
                    ->toAbsolutePath (),
                    $modeAccess));
                    break;

                case 'chmod':
                    return new B (chmod ($this
                    ->toAbsolutePath (),
                    $modeAccess));
                    break;
            }
        } else {
            // Return
            return $this->pathExists;
        }
    }

    /**
     * This method will return whatever info you desire from the current file path. It doesn't do any weird checking on the
     * path to see if it's a file or a directory (as it should by the way), but the good thing is that it will return the
     * proper requested information;
     *
     */
    public function getPathInfo ($whatInfoToGet) {
        // Check
        if ($this->pathExists->toBoolean () == TRUE) {
            // Switch
            switch ($whatInfoToGet) {
                case 'ftype':
                    return new S (filetype
                    ($this->toAbsolutePath ()));
                    break;

                case 'rpath':
                    return new S (realpath
                    ($this->toAbsolutePath ()));
                    break;

                case 'bname':
                    return new S (basename
                    ($this->toAbsolutePath ()));
                    break;

                case 'dname':
                    return new S (dirname
                    ($this->toAbsolutePath ()));
                    break;

                case 'extension':
                    return new S (pathinfo
                    ($this->toAbsolutePath (),
                    PATHINFO_EXTENSION));
                    break;

                case 'fname':
                    return new S (pathinfo
                    ($this->toAbsolutePath (),
                    PATHINFO_FILENAME));
                    break;
            }
        } else {
            // Return
            return new S;
        }

        // Check
        if ($this->pathExists->toBoolean () == TRUE) {
            // Switch
            switch ($whatInfoToGet) {
                case 'atime':
                    return new I (fileatime
                    ($this->toAbsolutePath ()));
                    break;

                case 'ctime':
                    return new I (filectime
                    ($this->toAbsolutePath ()));
                    break;

                case 'mtime':
                    return new I (filemtime
                    ($this->toAbsolutePath ()));
                    break;

                case 'group':
                    return new I (filegroup
                    ($this->toAbsolutePath ()));
                    break;

                case 'inode':
                    return new I (fileinode
                    ($this->toAbsolutePath ()));
                    break;

                case 'owner':
                    return new I (fileowner
                    ($this->toAbsolutePath ()));
                    break;

                case 'chmod':
                    return new I (fileperms
                    ($this->toAbsolutePath ()));
                    break;

                case 'fsize':
                    return new I (filesize
                    ($this->toAbsolutePath ()));
                    break;
            }
        } else {
            // Return
            return $this->pathExists->getAsInt ();
        }
    }

    /**
     * This method will check that the path exists, and it's a file, or if it isn't a file, it will try at least to check that
     * the file path is a directory. If both fail, we have a problem, and we echo an error, if the passed parameter is true;
     *
     */
    public function checkPathExists ($objNotFoundException = TRUE) {
        // Check
        if ($this->pathExists == NULL) {
            // Set
            $this->pathExists = new
            B (file_exists ($this
            ->toAbsolutePath ()));

            // Check
            if ($this->pathExists
            ->toBoolean () == FALSE) {
                // Check
                if ($objNotFoundException == TRUE) {
                    // Throw
                    throw new FileNotFoundException;
                }
            } else {
                // Return
                return $this->pathExists;
            }
        } else {
            // Return
            return $this->pathExists;
        }
    }

    /**
     * We allow this method to read the stored file path if it's a file, directly into the output buffer of our executed script.
     * If not, we do nothing, because this is a method specific for reading files to the buffer;
     *
     */
    public function readPath () {
        // Check
        if ($this->pathExists
        ->toBoolean () == TRUE) {
            // Return
            return new I (readfile ($this
            ->toAbsolutePath ()));
        }
    }

    /**
     * We allow this method to read the stored file path if it's a file, directly into the output buffer of our executed script.
     * If not, we do nothing, because this is a method specific for reading files to the buffer;
     *
     */
    public function readPathAsString () {
      // Check
      if ($this->pathExists
    ->toBoolean () == TRUE) {
        // Return
        return new S (file_get_contents ($this
      ->toAbsolutePath ()));
      }
    }

    /**
     * With this method we delete a file if the current path is a file, or we recursivelly delete a directory and all it's known
     * contents, if the current file path is a directory. In case of errors, we should implement an error mechanism to allow
     * us to inform the user what went wrong;
     *
     */
    public function unLinkPath () {
        // Check
        if ($this->pathExists
        ->toBoolean () == TRUE) {
            // Check
            if (unlink ($this
            ->toAbsolutePath ())) {
                // Return
                return $this->returnToChain ();
            }
        }
    }

    /**
     * In case we've constructed the object with a path that for the moment doesn't exists, we can touch the path, if the writing
     * permissions for the user that the web server is under, has permissions to do that. If not, an error will sure be caught,
     * upon trying to use this method;
     *
     */
    public function touchPath () {
        // Check
        if (touch ($this
        ->toAbsolutePath ())) {
            // Return
            return $this->returnToChain ();
        }
    }

    /**
     * This method will actually rename (or move) the stored path to a new path. It's a quick way to move files around, while
     * checking that we can actually do that before trying to do it.
     *
     */
    public function renamePath ($newRenamedName) {
        // Check
        if (($this->pathExists
        ->toBoolean () == TRUE) &&
        !(file_exists ($newRenamedName))) {
            // Check
            if (rename ($this
            ->toAbsolutePath (),
            $newRenamedName)) {
                // Set
                $this->objContainer =
                $newRenamedName;

                // Return
                return $this->returnToChain ();
            }
        }
    }

    /**
     * This method will copy the file path, to a new path we give it as a parameter. It makes a copy of the file or recursive
     * copy of the directory, to the new specified path;
     *
     */
    public function copyPath ($newCopyName) {
        // Check
        if (($this->pathExists
        ->toBoolean () == TRUE)) {
            // Check
            if (copy ($this
            ->toAbsolutePath (),
            $newCopyName)) {
                //Return
                return $this->returnToChain ();
            }
        }
    }

    /**
     * This method will set the stored path to the the one given by the passed parameter. We can pass a non-existent path, while
     * avoiding error mechanims, but that's a risky thing to do.
     *
     */
    public function setPath ($objArgument, $objNotFoundException = TRUE) {
        // Set
        $this->objContainer = (string)
        $objArgument;

        // Check
        $this->checkPathExists ($objNotFoundException);

        // Return
        return $this->returnToChain ();
    }


    /**
     * Will set the given string contents to the file, using the big PHP function "file_put_contents" (the same as file_get_*) in
     * order to make code a little bit easier on the client side. We're actually eager to have this kind of code as we can easily
     * maintain it and debug it per-se;
     *
     */
    public function putToFile ($stringToWrite) {
        // Check
        if (file_put_contents ($this
        ->toAbsolutePath (),
        $stringToWrite)) {
            // Return
            return $this->returnToChain ();
        } else {
            // Throw
            throw new CannotWriteFileException;
        }
    }

    /**
     * (non-PHPdoc)
     * @throws CannotMkdirPathException
     */
    public function makePath () {
        // Check
        if (!is_dir ($this
        ->toAbsolutePath())) {
            // Make
            if (!mkdir ($this
            ->toAbsolutePath (),
            0777, TRUE)) {
                // Throws
                throw new CannotMkdirPathException;
            }
        }
    }

    /**
     * (non-PHPdoc)
     * To be documented.
     */
    public function getMimeType () {
        // Set
        $objFInfo = finfo_open (FILEINFO_MIME);
        $objTMime = finfo_file ($objFInfo, $this->toAbsolutePath ());
        finfo_close ($objFInfo);

        // Return
        return $objTMime;
    }
}

/**
 * File DirectoryPath: DT, containing the contents of a directory, that can be extended from the SPL;
 *
 * This class is an interface to the DirectoryPathIterator used in the SPL. It's an easy way for us to do intelligent things,
 * with little code. For the moment, it relies upon it's own methods, but it's going to evolve as time and money allows us
 * to invest time in it;
 */
class DirectoryPath extends Path {
    /**
     * This method will return an A (Array) containing all the files found in this directory. It allows us to get an instant view
     * of the contents of the directory we need to process.
     *
     */
    public function scanDirectoryPath (& $setCountTo = NULL, $sortByWhat = SORT_STRING) {
        // Set
        $temporaryScandirArray = array_reverse (scandir ($this->objContainer, $sortByWhat), FALSE);
        $temporaryCount = count ($temporaryScandirArray);
        $scanArrayFiltered = array ();
        $setCountTo = 0;

        // For
        for ($objI = 0; $objI < $temporaryCount; ++$objI) {
            if ($temporaryScandirArray[$objI][0] != '.') {
                $scanArrayFiltered[] = $temporaryScandirArray[$objI];
                $setCountTo++;
            }
        }

        // Return
        return new A ($scanArrayFiltered);
    }
}

/**
 * With this class/method we chose not to provide any documentation whatsoever, because the code itself is sufficient enough to
 * let the developer understand what is happening 'under-the-hood'. That's why we've set the 'access' comment parameter to it
 * for private, so it will be skipped when generating documentation.
 */
class Contents extends Path {
    /**
     * (non-PHPdoc)
     */
    public function setPath ($objArgument, $objNotFoundException = TRUE) {
        // Parent
        parent::setPath ($objArgument,
        $objNotFoundException);

        // Check
        if ($this->pathExists
        ->toBoolean () == TRUE) {
            // Set
            $this->objContainer =
            file_get_contents ($this
            ->toAbsolutePath ());
        }

        // Return
        return $this->returnToChain ();
    }
}

/**
 * StoragePath: Implementing StoragePath, as relative paths from the webroot. They return error if the path we're trying to work on
 * doesn't exist.
 *
 * We need some kind of verification mechanism for file paths that exist. Also, we wanted something that can be quickly
 * extendeded from SPL, and from our S (String) DT, which can allow us to check the existence of files at any USE time of
 * our files in the framework. What do mean by this?!
 */
class StoragePath extends Path {
    /**
     * @var boolean $pathExists Contains true/false, if the current path exists;
     */
    protected $pathExists = NULL;

    /**
     * At the moment of calling our ->to* methods, we don't know if we have a path that contains the document root or not,
     * which means, that in order to be bug-proof, we will first do a replacement on the path, to block-out any still existing
     * occurence of the document root in the stored file path, after which we will do a string prepending of the document root;
     *
     * We could avoid such operations by actually checking the passed parameters at insertion/update times, thing that we'll be
     * doing in the future versions of the framework, while providing backward compatibility;
     *
     */
    public function toAbsolutePath () {
        // Return
        return Architecture::pathTo (Architecture
        ::getStorage (), $this->objContainer);
    }
}

/**
 * File DirectoryPath: DT, containing the contents of a directory, that can be extended from the SPL;
 *
 * This class is an interface to the DirectoryPathIterator used in the SPL. It's an easy way for us to do intelligent things,
 * with little code. For the moment, it relies upon it's own methods, but it's going to evolve as time and money allows us
 * to invest time in it;
 */
class StorageDirectoryPath extends StoragePath {
    // Inherited
}

/**
 * With this class/method we chose not to provide any documentation whatsoever, because the code itself is sufficient enough to
 * let the developer understand what is happening 'under-the-hood'. That's why we've set the 'access' comment parameter to it
 * for private, so it will be skipped when generating documentation.
 */
class StorageContents extends StoragePath {
    // Inherited
}

/**
 * This wrapper function is used to surround text that should be translated from one language to another. It relies on the language
 * already selected for the user (if the proper session variable is set) else it defaults to en_GB. We don't provide an widget
 * to change the language as we only developed the framework in English, but if you're willing to develop other languages either for
 * your specific project or to share it back with the community, than you're welcome to create such a widget/mechanism. All you need
 * to change is the 'language' session variable to the languge of your choice and _T () will act accordingly.
 *
 * WARNING: _T function relies on the GLOBALLY available objT array variable in int/[en_GB]/__T.php files. It does string key hashing
 * as we've tested out and saw that string array keys in PHP are lightning fast in comparison to other methods (gettext, etc). Thus
 * we opted to go for a translation method that uses string substituions (like it was done in C's sprintf/vsprinf);
 *
 */
function _T ($objParam) {
    // Return
    GLOBAL $_T; return isset ($_T[(string) $objParam]) ?
    new S ($_T[(string) $objParam]) : new S ($objParam);
}

function _Interpret ($objParam, $objText) {
    // Return
    GLOBAL $_T; return $_T[(string) $objParam] = $objText;
}

/**
 * Provided as a shorthand function this will return a standard SQL string upon to build on. Many of our methods relly on this
 * function to return one of the SELECT/DELETE/INSERT/UPDATE strings upon which we "tokenize" (CALL ->doToken ('%token', $objVar)) -
 * to generate the proper SQL string. As you may have guessed _QS comes from "Query String" and it's not limited to just the
 * strings you see below. You can propose/create new ones as you wish;
 *
 */
function _QS ($objParam) {
    // Set
    static $objDoSELECTFromTable = 'SELECT %what FROM %table %condition';
    static $objDoDELETEFromTable = 'DELETE FROM %table WHERE %condition';
    static $objDoINSERTOnToTable = 'INSERT IGNORE INTO %table SET %condition';
    static $objDoUPDATEOnToTable = 'UPDATE %table SET %condition';

    // Switch
    switch ($objParam) {
        // #1
        case 'doSELECT':
            // Return
            return new
            S ($objDoSELECTFromTable);
            break;

            // #2
        case 'doDELETE':
            // Return
            return new
            S ($objDoDELETEFromTable);
            break;

            // #3
        case 'doINSERT':
            // Return
            return new
            S ($objDoINSERTOnToTable);
            break;

            // #4
        case 'doUPDATE':
            // Return
            return new
            S ($objDoUPDATEOnToTable);
            break;
    }
}

/**
 * This method is just a shorthand for "new S". Because PHP does not allow for a syntax such as "(new S)->doSomething ()" the best
 * workaround was to wrap this in a function that returns a new object of type S. This allows for such shorthand code and for a better
 * readable code than would be provided by the standard instantiation paradigm;
 *
 */
function _S ($objParam) {
    // Return
    return new S ($objParam);
}

/**
 * This method is just a shorthand for "new A". Because PHP does not allow for a syntax such as "(new A)->doSomething ()" the best
 * workaround was to wrap this in a function that returns a new object of type A. This allows for such shorthand code and for a better
 * readable code than would be provided by the standard instantiation paradigm;
 *
 */
function _A ($objParam) {
    // Return
    return new A ($objParam);
}

/**
 * This method is just a shorthand for "new I". Because PHP does not allow for a syntax such as "(new I)->doSomething ()" the best
 * workaround was to wrap this in a function that returns a new object of type I. This allows for such shorthand code and for a better
 * readable code than would be provided by the standard instantiation paradigm;
 *
 */
function _I ($objParam) {
    // Return
    return new I ($objParam);
}

/**
 * This method is just a shorthand for "new F". Because PHP does not allow for a syntax such as "(new F)->doSomething ()" the best
 * workaround was to wrap this in a function that returns a new object of type F. This allows for such shorthand code and for a better
 * readable code than would be provided by the standard instantiation paradigm;
 *
 */
function _F ($objParam) {
    // Return
    return new F ($objParam);
}

/**
 * This method is just a shorthand for "new FP". Because PHP does not allow for a syntax such as "(new FP)->doSomething ()" the best
 * workaround was to wrap this in a function that returns a new object of type FP. This allows for such shorthand code and for a better
 * readable code than would be provided by the standard instantiation paradigm;
 *
 */
function _P ($objParam) {
    // Return
    return new Path ($objParam);
}

/**
 * This method is just a shorthand for "new FSP". Because PHP does not allow for a syntax such as "(new FSP)->doSomething ()" the best
 * workaround was to wrap this in a function that returns a new object of type FP. This allows for such shorthand code and for a better
 * readable code than would be provided by the standard instantiation paradigm;
 *
 */
function _SP ($objParam, $objNotFoundException = TRUE) {
    // Return
    return new StoragePath ($objParam, $objNotFoundException);
}

/**
 * This method is just a shorthand for "new FC". Because PHP does not allow for a syntax such as "(new FC)->doSomething ()" the best
 * workaround was to wrap this in a function that returns a new object of type FC. This allows for such shorthand code and for a better
 * readable code than would be provided by the standard instantiation paradigm;
 *
 */
function _C ($objParam) {
    // Return
    return new Contents ($objParam);
}

/* As _C, for storage */
function _SC ($objParam) {
    // Return
    return new StorageContents ($objParam);
}

/**
 * This method is just a shorthand for "new FD". Because PHP does not allow for a syntax such as "(new FD)->doSomething ()" the best
 * workaround was to wrap this in a function that returns a new object of type FC. This allows for such shorthand code and for a better
 * readable code than would be provided by the standard instantiation paradigm;
 *
 */
function _D ($objParam) {
    // Return
    return new DirectoryPath ($objParam);
}

/**
 * This method is just a shorthand for the 'new' operator. We us _N as a short for new (nameOfInstance) as an easier method to method
 * chain. This is far easier then a method;
 */
function _new ($objParam) {
    // Return
    return Singleton::getInstance (new S ($objParam));
}
?>
