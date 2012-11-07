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
 * We define a comprehensive list of default, no message exceptions. Because they are thrown as-is, they usually don't require any
 * message. The strack trace back to them is sufficient enough for any developer to figure out what happened. The name of these exceptions
 * are already pretty concise and can explain the source of the error without requiring any text to be appended to the message.
 */

class CannotInitiateException
extends Exception {
    public function __construct ($message = NULL, $code = NULL, $previous = NULL) {
        /**
         * Why we hardcoded: because our basic "requireDependency" method, is going to
         * get executed a bit late. Exceptions, are special. They can be thrown
         * at anytime. We want to be able to quickly show an error screen.
         *
         * For that reason only, we hard-code a basic (no template, no nothing)
         * path to our Error class. Once this is done, the static renderDeath
         * method is called that depends only on a few extra-classes (Architecture,
         * Definitions, Patterns).
         *
         * This way, if for example, we call new S (1) right in the Definitions.php
         * file, although the framework has not yet loaded, the error screen is thus
         * shown. This helps even core framework developers with a nice debugging
         * screen instead of a white page and a message in the logs.
         *
         * That means we can use our data types right from Patterns, while keeping
         * specific error-code (and error-screen generating code) in the Error
         * object, in an effor to respect the idea of loose-coupling while taking
         * advantage of PHP's own error mechanisms.
         */

        // Path-hardcoded
        require_once 'Exceptions.php';
        require_once 'Definitions.php';
        require_once 'Patterns.php';
        require_once 'Architecture.php';
        require_once 'Error.php';

        // Set (hardcoded)
        _new ('Architecture');
        _new ('Execution');
        _new ('Logging');
        _new ('Error');

        // Hardcoded
        Error::renderDeath (new S ('Exception'),
        new S ((string) get_class ($this)), NULL,
        _S ((string) $this->getTraceAsString ())
        ->doToken (Architecture::getRoot (), _NONE)
        ->prependString ($message));
    }
}

class SystemException
extends Exception {
    public function __construct ($message = NULL, $code = NULL, $previous = NULL) {
        /**
         * Why we hardcoded: because our basic "requireDependency" method, is going to
         * get executed a bit late. Exceptions, are special. They can be thrown
         * at anytime. We want to be able to quickly show an error screen.
         *
         * For that reason only, we hard-code a basic (no template, no nothing)
         * path to our Error class. Once this is done, the static renderDeath
         * method is called that depends only on a few extra-classes (Architecture,
         * Definitions, Patterns).
         *
         * This way, if for example, we call new S (1) right in the Definitions.php
         * file, although the framework has not yet loaded, the error screen is thus
         * shown. This helps even core framework developers with a nice debugging
         * screen instead of a white page and a message in the logs.
         *
         * That means we can use our data types right from Patterns, while keeping
         * specific error-code (and error-screen generating code) in the Error
         * object, in an effor to respect the idea of loose-coupling while taking
         * advantage of PHP's own error mechanisms.
         */

        // Path-hardcoded
        require_once 'Exceptions.php';
        require_once 'Definitions.php';
        require_once 'Patterns.php';
        require_once 'Architecture.php';
        require_once 'Error.php';

        // Set (hardcoded)
        _new ('Architecture');
        _new ('Execution');
        _new ('Logging');
        _new ('Error');

        // Hardcoded
        Error::renderDeath (new S ('System'),
        new S ((string) get_class ($this)), NULL,
        _S ((string) $this->getTraceAsString ())
        ->doToken (Architecture::getRoot (), _NONE)
        ->prependString ($message));
    }
}

class CannotSetErrorReportingException
extends CannotInitiateException {}

class CannotSetIncludePathException
extends CannotInitiateException {}

class CannotSetMemoryLimitException
extends CannotInitiateException {}

class CannotSetSessionCacheExpireException
extends CannotInitiateException {}

class CannotSetScriptTimeLimitException
extends CannotInitiateException {}

class CannotSetDisplayErrorsException
extends CannotInitiateException {}

class DivisionByZeroException
extends CannotInitiateException {}

class MethodNotMappedException
extends CannotInitiateException {}

class MethodNotCallableException
extends CannotInitiateException {}

class NotBooleanException
extends CannotInitiateException {}

class NotIntegerException
extends CannotInitiateException {}

class NotFloatException
extends CannotInitiateException {}

class NotStringException
extends CannotInitiateException {}

class NotResourceException
extends CannotInitiateException {}

class FileNotFoundException
extends CannotInitiateException {}

class CannotWriteFileException
extends CannotInitiateException {}

class CannotGetSystemAvgLoading
extends CannotInitiateException {}

class OutputBufferNotStartedException
extends CannotInitiateException {}

class HeadersAlreadySentException
extends CannotInitiateException {}

class CannotStartSessionException
extends CannotInitiateException {}

class SessionAlreadyStartedException
extends CannotInitiateException {}

class CannotDestroySessionException
extends CannotInitiateException {}

class CannotUnsetSessionException
extends CannotInitiateException {}

class SessionNotStartedException
extends CannotInitiateException {}

class CannotDecodeSessionException
extends CannotInitiateException {}

class SessionKeyNotSetException
extends CannotInitiateException {}

class DocumentRootNotWriteableException
extends CannotInitiateException {}

class ModRewriteNotAvailableException
extends CannotInitiateException {}

class CannotGetApacheModulesException
extends CannotInitiateException {}

class CacheDirNotWriteableException
extends CannotInitiateException {}

class LogDirNotWriteableException
extends CannotInitiateException {}

class UploadDirNotWriteableException
extends CannotInitiateException {}

class TemporaryDirNotWriteableException
extends CannotInitiateException {}

class DocumentStorageNotWriteableException
extends CannotInitiateException {}

class BackupDirNotWriteableException
extends CannotInitiateException {}

class SystemLoadTooHighException
extends CannotInitiateException {}

class HeaderCSSFileNotSetException
extends CannotInitiateException {}

class HeaderLinkRelFileNotSetException
extends CannotInitiateException {}

class HeaderEquivalentNotSetException
extends CannotInitiateException {}

class HeaderTagNotSetException
extends CannotInitiateException {}

class HeaderJSSFileNotSetException
extends CannotInitiateException {}

class TemplateVariableNotInstanceOfArrayException
extends CannotInitiateException {}

class CannotExtractTemplateVariablesException
extends CannotInitiateException {}

class CannotWriteTemplateCacheFileException
extends CannotInitiateException {}

class HeaderCSSFileAlreadySetException
extends CannotInitiateException {}

class HeaderJSSFileAlreadySetException
extends CannotInitiateException {}

class SQLException
extends CannotInitiateException {}

class OffsetKeyNotSetException
extends CannotInitiateException {}

class HashAlgorithmNotDefinedException
extends CannotInitiateException {}

class FormMethodNotSupportedException
extends CannotInitiateException {}

class CannotSetMultipleOnNonSELECTInputException
extends CannotInitiateException {}

class CannotSetAltOnNonImageInputException
extends CannotInitiateException {}

class CannotSetSrcOnNonImageInputException
extends CannotInitiateException {}

class CannotSetCheckedOnNonRadioCheckboxInputException
extends CannotInitiateException {}

class CannotSetFileControllerOnNonFileInputException
extends CannotInitiateException {}

class CannotMkdirUploadDirectoryException
extends CannotInitiateException {}

class NoFormUploadDirSetException
extends CannotInitiateException {}

class FormInputTypeNotSupportedException
extends CannotInitiateException {}

class RequiredModuleNotFoundException
extends CannotInitiateException {}

class EmptyConfigurationException
extends CannotInitiateException {}

class NotAnObjectException
extends CannotInitiateException {}

class CannotWriteToSessionStrage
extends CannotInitiateException {}

class UnsupportedCookieType
extends CannotInitiateException {}

class CannotSetTimestampException
extends CannotInitiateException {}

class TimestampNotSetException
extends CannotInitiateException {}

class CannotSetPluginPathException
extends CannotInitiateException {}

class CannotWriteToLogException
extends CannotInitiateException {}

class CannotSetErrorPrependStringException
extends CannotInitiateException {}

class CannotSetErrorAppendStringException
extends CannotInitiateException {}

class CannotSetWorkerMethodException
extends CannotInitiateException {}

class CannotStartOutputBufferingException
extends CannotInitiateException {}

class CannotSetOutputTokenException
extends CannotInitiateException {}

class CannotRunNeedsRefactoring
extends CannotInitiateException {}

class CannotMkdirPathException
extends CannotInitiateException {}

class UnknownImageTypeException
extends CannotInitiateException {}

class RecursiveRedirectionException
extends CannotInitiateException {}
?>
