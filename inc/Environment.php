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
 * Sets default settings needed to run in a predefined environemtn;
 *
 * @author Catalin Alexandru Zamfir <catalin.zamfir@raphpframework.ro>
 * @copyright Under the terms of the GNU General Public License v3
 * @version $Id: Environment.php 1 2012-10-26 08:27:37Z root $
 */
final class Environment {
    /**
     * Container of .htaccess entries;
     *
     * @author Catalin Alexandru Zamfir <catalin.zamfir@raphpframework.ro>
     * @copyright Under the terms of the GNU General Public License v3
     * @version $Id: Environment.php 1 2012-10-26 08:27:37Z root $
     */
    private static $objHT = NULL;

    /**
     * Flag for .htaccess writing or not (upon this request or not);
     *
     * @author Catalin Alexandru Zamfir <catalin.zamfir@raphpframework.ro>
     * @copyright Under the terms of the GNU General Public License v3
     * @version $Id: Environment.php 1 2012-10-26 08:27:37Z root $
     */
    private static $objHTWriteIt = NULL;

    /**
     * Constructs the environment, sets the HT/HT Flags needed for .htaccess auto-write;
     *
     * @author Catalin Alexandru Zamfir <catalin.zamfir@raphpframework.ro>
     * @copyright Under the terms of the GNU General Public License v3
     * @version $Id: Environment.php 1 2012-10-26 08:27:37Z root $
     */
    public function __construct () {
        // Set
        self::$objHT = new A;
        self::$objHTWriteIt = new I (1);
        self::setEnvironment ();
    }

    /**
     * Sets the HT file, output the content, disable the streams and do some cleanup;
     *
     * @author Catalin Alexandru Zamfir <catalin.zamfir@raphpframework.ro>
     * @copyright Under the terms of the GNU General Public License v3
     * @version $Id: Environment.php 1 2012-10-26 08:27:37Z root $
     */
    public function __destruct () {
        // Set .htaccess;
        self::setHTPath ();
    }

    /**
     * Write another HT (.htaccess) line to the buffer, to be written at script end;
     *
     * @author Catalin Alexandru Zamfir <catalin.zamfir@raphpframework.ro>
     * @copyright Under the terms of the GNU General Public License v3
     * @version $Id: Environment.php 1 2012-10-26 08:27:37Z root $
     */
    public static final function rewriteHTLine (S $htString) {
        // Check
        if (self::$objHT[] = $htString) {
            // Return
            return new B (TRUE);
        } else {
            // Return
            return new B (FALSE);
        }
    }

    /**
     * Write the HT buffer to the path of the (.htaccess) file;
     *
     * @author Catalin Alexandru Zamfir <catalin.zamfir@raphpframework.ro>
     * @copyright Under the terms of the GNU General Public License v3
     * @version $Id: Environment.php 1 2012-10-26 08:27:37Z root $
     */
    private static final function writeHTPath (S $htString) {
        // Check, open and write
        if (is_writeable (Architecture::pathTo (Architecture
        ::getRoot (), '.htaccess'))) {
            // Check
            if ($htFile = fopen (Architecture::pathTo (Architecture
            ::getRoot (), '.htaccess'), 'w')) {
                // Lock
                flock  ($htFile, LOCK_EX);
                fwrite ($htFile, $htString);
                flock  ($htFile, LOCK_UN);
                fclose ($htFile);

                // Return
                return new B (TRUE);
            } else {
                // Throws
                throw new CannotWriteFileException;
            }
        }
    }

    /**
     * Set the path to write the (.htaccess) file to;
     *
     * @author Catalin Alexandru Zamfir <catalin.zamfir@raphpframework.ro>
     * @copyright Under the terms of the GNU General Public License v3
     * @version $Id: Environment.php 1 2012-10-26 08:27:37Z root $
     */
    private static final function setHTPath () {
        // Check the mod_rewrite is ON;
        if (function_exists ('apache_get_modules')) {
            // Check
            if (in_array ('mod_rewrite',
            apache_get_modules ())) {
                // Check
                if (REWRITE_ENGINE == TRUE) {
                    // Routing defaults, add'em at the end of the script
                    self::rewriteHTLine (new S ('RewriteCond %{REQUEST_FILENAME} !-f'));
                    self::rewriteHTLine (new S ('RewriteCond %{REQUEST_FILENAME} !-d'));
                    self::rewriteHTLine (new S ('RewriteRule ^(.*)$ index.php/$1 [L]'));
                }
            }
        }

        // Check
        if (self::$objHTWriteIt instanceof I) {
            // Check
            if (self::$objHTWriteIt
            ->toInt () == 1) {
                // Do the Array thing!;
                if (sizeof (self::$objHT) > 0) {
                    // Implode the array in a string;
                    $htString = new S (implode (_N_, self::$objHT->toArray ()));

                    // Check if the file exists!
                    if (file_exists (Architecture::pathTo (Architecture
                    ::getRoot (), '.htaccess'))) {
                        // Check
                        if ($htString != file_get_contents (Architecture
                        ::pathTo (Architecture::getRoot (), '.htaccess')) || strlen ($htString) != filesize (Architecture
                        ::pathTo (Architecture::getRoot (), '.htaccess'))) {

                            // Write
                            self::writeHTPath ($htString);
                        }
                    } else {
                        // Touch the HTACCESS;
                        if (touch (Architecture::pathTo (Architecture
                        ::getRoot (), '.htaccess'))) {
                            // Write
                            self::writeHTPath ($htString);
                        }
                    }
                } else {
                    // Return
                    return new B (FALSE);
                }
            }
        } else {
            // Return
            return new B (FALSE);
        }

        // Return
        return new B (TRUE);
    }

    /**
     * Set a default environemnt by setting everything that can be set via PHP_INI_PERDIR (namely, via .htaccess);
     *
     * @author Catalin Alexandru Zamfir <catalin.zamfir@raphpframework.ro>
     * @copyright Under the terms of the GNU General Public License v3
     * @version $Id: Environment.php 1 2012-10-26 08:27:37Z root $
     */
    private static final function setEnvironment () {
        // Check
        if (is_writeable (Architecture::getRoot ())) {

            // Check the function
            if (function_exists ('apache_get_modules')) {
                // Check it exists
                if (in_array ('mod_deflate',
                apache_get_modules ())) {
                    // Types
                    $objDeflateTypes = new A;
                    $objDeflateTypes[] = new S ('text/html');
                    $objDeflateTypes[] = new S ('text/plain');
                    $objDeflateTypes[] = new S ('text/javascript');
                    $objDeflateTypes[] = new S ('text/css');
                    $objDeflateTypes[] = new S ('image/bmp');
                    $objDeflateTypes[] = new S ('image/x-windows-bmp');
                    $objDeflateTypes[] = new S ('image/gif');
                    $objDeflateTypes[] = new S ('image/jpeg');
                    $objDeflateTypes[] = new S ('image/pjpeg');
                    $objDeflateTypes[] = new S ('image/png');
                    $objDeflateTypes[] = new S ('image/vnd.wap.wbmp');

                    // Set
                    self::rewriteHTLine (new S ('# FILTERS'));

                    // Go
                    foreach ($objDeflateTypes as
                    $objK => $objV) {
                        // Deflate
                        self::rewriteHTLine (_S ('AddOutputFilterByType')
                        ->appendString (_SP)->appendString ('DEFLATE')
                        ->appendString (_SP)->appendString ($objV));
                    }

                    // Empty
                    self::rewriteHTLine (new S);
                }
            }

            // Defaults
            self::$objHT[] = '# DEFAULT';
            self::$objHT[] = 'FileETag All';
            self::$objHT[] = 'IndexIgnore *';
            self::$objHT[] = 'AddType text/x-component .htc';
            self::$objHT[] = 'SetEnv TZ ' . DATE_TIMEZONE;
            self::$objHT[] = 'Options All ' . APACHE_OPTIONS;
            self::$objHT[] = 'AddDefaultCharset ' . DEFAULT_CHARSET;
            self::$objHT[] = 'ServerSignature ' . APACHE_SERVER_SIGNATURE;
            self::$objHT[] = 'php_value upload_max_filesize ' . UPLOAD_MAX_FILESIZE;
            self::$objHT[] = 'php_value post_max_size ' . POST_MAX_SIZE;
            self::$objHT[] = 'php_value asp_tags ' . ASP_TAGS;
            self::$objHT[] = 'php_value register_long_arrays ' . REGISTER_LONG_GPC;
            self::$objHT[] = 'php_value short_open_tag ' . SHORT_OPEN_TAG;
            self::$objHT[] = 'php_value max_input_time ' . MAX_INPUT_TIME;
            self::$objHT[] = 'php_value session.gc_maxlifetime ' . SESSION_CACHE_EXPIRE;
            self::$objHT[] = 'php_value session.use_trans_sid ' . SESSION_USE_TRANSPARENT_ID;
            self::$objHT[] = 'php_value output_buffering ' . DEFAULT_OUTPUT_BUFFERING;
            self::$objHT[] = 'php_value xdebug.profiler_enable 0';

            // Check for mbstring, if v. < PHP 6
            if (function_exists ('mb_get_info')) {
                // Set
                self::$objHT[] = 'php_value mbstring.language Neutral';
                self::$objHT[] = 'php_value mbstring.internal_encoding UTF-8';
                self::$objHT[] = 'php_value mbstring.encoding_translation On';
                self::$objHT[] = 'php_value mbstring.http_input auto';
                self::$objHT[] = 'php_value mbstring.http_output UTF-8';
                self::$objHT[] = 'php_value mbstring.detect_order auto';
                self::$objHT[] = 'php_value mbstring.substitute_character none';
                self::$objHT[] = 'php_value mbstring.func_overload 7';
            }

            // Error Documents
            self::$objHT[] = 'ErrorDocument 400 /Error/400';
            self::$objHT[] = 'ErrorDocument 402 /Error/402';
            self::$objHT[] = 'ErrorDocument 403 /Error/403';
            self::$objHT[] = 'ErrorDocument 404 /Error/404';
            self::$objHT[] = 'ErrorDocument 405 /Error/405';
            self::$objHT[] = 'ErrorDocument 406 /Error/406';
            self::$objHT[] = 'ErrorDocument 407 /Error/407';
            self::$objHT[] = 'ErrorDocument 408 /Error/408';
            self::$objHT[] = 'ErrorDocument 409 /Error/409';
            self::$objHT[] = 'ErrorDocument 410 /Error/410';
            self::$objHT[] = 'ErrorDocument 411 /Error/411';
            self::$objHT[] = 'ErrorDocument 412 /Error/412';
            self::$objHT[] = 'ErrorDocument 413 /Error/413';
            self::$objHT[] = 'ErrorDocument 414 /Error/414';
            self::$objHT[] = 'ErrorDocument 415 /Error/415';
            self::$objHT[] = 'ErrorDocument 416 /Error/416';
            self::$objHT[] = 'ErrorDocument 417 /Error/417';
            self::$objHT[] = 'ErrorDocument 422 /Error/422';
            self::$objHT[] = 'ErrorDocument 423 /Error/423';
            self::$objHT[] = 'ErrorDocument 424 /Error/424';
            self::$objHT[] = 'ErrorDocument 426 /Error/426';
            self::$objHT[] = 'ErrorDocument 500 /Error/500';
            self::$objHT[] = 'ErrorDocument 501 /Error/501';
            self::$objHT[] = 'ErrorDocument 502 /Error/502';
            self::$objHT[] = 'ErrorDocument 503 /Error/503';
            self::$objHT[] = 'ErrorDocument 504 /Error/504';
            self::$objHT[] = 'ErrorDocument 505 /Error/505';
            self::$objHT[] = 'ErrorDocument 506 /Error/506';
            self::$objHT[] = 'ErrorDocument 507 /Error/507';
            self::$objHT[] = 'ErrorDocument 510 /Error/510';

            // Check the mod_rewrite is ON;
            if (function_exists ('apache_get_modules')) {
                // Check
                if (in_array ('mod_rewrite',
                apache_get_modules ())) {
                    // Check
                    REWRITE_ENGINE == TRUE ?
                    self::rewriteHTLine (new S ('RewriteEngine On')) :
                    self::rewriteHTLine (new S ('RewriteEngine Off'));

                    // If rewrite is OK
                    if (REWRITE_ENGINE == TRUE) {
                        // Protect from bots
                        self::rewriteHTLine (new S ('RewriteCond %{HTTP_USER_AGENT} ^BlackWidow [OR]'));
                        self::rewriteHTLine (new S ('RewriteCond %{HTTP_USER_AGENT} ^Bot\ mailto:craftbot@yahoo.com [OR]'));
                        self::rewriteHTLine (new S ('RewriteCond %{HTTP_USER_AGENT} ^ChinaClaw [OR]'));
                        self::rewriteHTLine (new S ('RewriteCond %{HTTP_USER_AGENT} ^Custo [OR]'));
                        self::rewriteHTLine (new S ('RewriteCond %{HTTP_USER_AGENT} ^DISCo [OR]'));
                        self::rewriteHTLine (new S ('RewriteCond %{HTTP_USER_AGENT} ^Download\ Demon [OR]'));
                        self::rewriteHTLine (new S ('RewriteCond %{HTTP_USER_AGENT} ^eCatch [OR]'));
                        self::rewriteHTLine (new S ('RewriteCond %{HTTP_USER_AGENT} ^EirGrabber [OR]'));
                        self::rewriteHTLine (new S ('RewriteCond %{HTTP_USER_AGENT} ^EmailSiphon [OR]'));
                        self::rewriteHTLine (new S ('RewriteCond %{HTTP_USER_AGENT} ^EmailWolf [OR]'));
                        self::rewriteHTLine (new S ('RewriteCond %{HTTP_USER_AGENT} ^Express\ WebPictures [OR]'));
                        self::rewriteHTLine (new S ('RewriteCond %{HTTP_USER_AGENT} ^ExtractorPro [OR]'));
                        self::rewriteHTLine (new S ('RewriteCond %{HTTP_USER_AGENT} ^EyeNetIE [OR]'));
                        self::rewriteHTLine (new S ('RewriteCond %{HTTP_USER_AGENT} ^WebGo\ IS [OR]'));
                        self::rewriteHTLine (new S ('RewriteCond %{HTTP_USER_AGENT} ^WebFetch [OR]'));
                        self::rewriteHTLine (new S ('RewriteCond %{HTTP_USER_AGENT} ^WebCopier [OR]'));
                        self::rewriteHTLine (new S ('RewriteCond %{HTTP_USER_AGENT} ^WebAuto [OR]'));
                        self::rewriteHTLine (new S ('RewriteCond %{HTTP_USER_AGENT} ^Web\ Sucker [OR]'));
                        self::rewriteHTLine (new S ('RewriteCond %{HTTP_USER_AGENT} ^Web\ Image\ Collector [OR]'));
                        self::rewriteHTLine (new S ('RewriteCond %{HTTP_USER_AGENT} ^VoidEYE [OR]'));
                        self::rewriteHTLine (new S ('RewriteCond %{HTTP_USER_AGENT} ^Teleport\ Pro [OR]'));
                        self::rewriteHTLine (new S ('RewriteCond %{HTTP_USER_AGENT} ^tAkeOut [OR]'));
                        self::rewriteHTLine (new S ('RewriteCond %{HTTP_USER_AGENT} ^Surfbot [OR]'));
                        self::rewriteHTLine (new S ('RewriteCond %{HTTP_USER_AGENT} ^SuperHTTP [OR]'));
                        self::rewriteHTLine (new S ('RewriteCond %{HTTP_USER_AGENT} ^SuperBot [OR]'));
                        self::rewriteHTLine (new S ('RewriteCond %{HTTP_USER_AGENT} ^SmartDownload [OR]'));
                        self::rewriteHTLine (new S ('RewriteCond %{HTTP_USER_AGENT} ^SiteSnagger [OR]'));
                        self::rewriteHTLine (new S ('RewriteCond %{HTTP_USER_AGENT} ^ReGet [OR]'));
                        self::rewriteHTLine (new S ('RewriteCond %{HTTP_USER_AGENT} ^RealDownload [OR]'));
                        self::rewriteHTLine (new S ('RewriteCond %{HTTP_USER_AGENT} ^pcBrowser [OR]'));
                        self::rewriteHTLine (new S ('RewriteCond %{HTTP_USER_AGENT} ^pavuk [OR]'));
                        self::rewriteHTLine (new S ('RewriteCond %{HTTP_USER_AGENT} ^Papa\ Foto [OR]'));
                        self::rewriteHTLine (new S ('RewriteCond %{HTTP_USER_AGENT} ^PageGrabber [OR]'));
                        self::rewriteHTLine (new S ('RewriteCond %{HTTP_USER_AGENT} ^Offline\ Navigator [OR]'));
                        self::rewriteHTLine (new S ('RewriteCond %{HTTP_USER_AGENT} ^Offline\ Explorer [OR]'));
                        self::rewriteHTLine (new S ('RewriteCond %{HTTP_USER_AGENT} ^Octopus [OR]'));
                        self::rewriteHTLine (new S ('RewriteCond %{HTTP_USER_AGENT} ^NetZIP [OR]'));
                        self::rewriteHTLine (new S ('RewriteCond %{HTTP_USER_AGENT} ^Net\ Vampire [OR]'));
                        self::rewriteHTLine (new S ('RewriteCond %{HTTP_USER_AGENT} ^NetSpider [OR]'));
                        self::rewriteHTLine (new S ('RewriteCond %{HTTP_USER_AGENT} ^NetAnts [OR]'));
                        self::rewriteHTLine (new S ('RewriteCond %{HTTP_USER_AGENT} ^NearSite [OR]'));
                        self::rewriteHTLine (new S ('RewriteCond %{HTTP_USER_AGENT} ^Navroad [OR]'));
                        self::rewriteHTLine (new S ('RewriteCond %{HTTP_USER_AGENT} ^Mister\ PiX [OR]'));
                        self::rewriteHTLine (new S ('RewriteCond %{HTTP_USER_AGENT} ^MIDown\ tool [OR]'));
                        self::rewriteHTLine (new S ('RewriteCond %{HTTP_USER_AGENT} ^Mass\ Downloader [OR]'));
                        self::rewriteHTLine (new S ('RewriteCond %{HTTP_USER_AGENT} ^LeechFTP [OR]'));
                        self::rewriteHTLine (new S ('RewriteCond %{HTTP_USER_AGENT} ^larbin [OR]'));
                        self::rewriteHTLine (new S ('RewriteCond %{HTTP_USER_AGENT} ^JOC\ Web\ Spider [OR]'));
                        self::rewriteHTLine (new S ('RewriteCond %{HTTP_USER_AGENT} ^JetCar [OR]'));
                        self::rewriteHTLine (new S ('RewriteCond %{HTTP_USER_AGENT} ^Internet\ Ninja [OR]'));
                        self::rewriteHTLine (new S ('RewriteCond %{HTTP_USER_AGENT} ^InterGET [OR]'));
                        self::rewriteHTLine (new S ('RewriteCond %{HTTP_USER_AGENT} ^Indy\ Library [NC,OR]'));
                        self::rewriteHTLine (new S ('RewriteCond %{HTTP_USER_AGENT} ^Image\ Sucker [OR]'));
                        self::rewriteHTLine (new S ('RewriteCond %{HTTP_USER_AGENT} ^Image\ Stripper [OR]'));
                        self::rewriteHTLine (new S ('RewriteCond %{HTTP_USER_AGENT} ^HTTrack [NC,OR]'));
                        self::rewriteHTLine (new S ('RewriteCond %{HTTP_USER_AGENT} ^HMView [OR]'));
                        self::rewriteHTLine (new S ('RewriteCond %{HTTP_USER_AGENT} ^Grafula [OR]'));
                        self::rewriteHTLine (new S ('RewriteCond %{HTTP_USER_AGENT} ^GrabNet [OR]'));
                        self::rewriteHTLine (new S ('RewriteCond %{HTTP_USER_AGENT} ^Go-Ahead-Got-It [OR]'));
                        self::rewriteHTLine (new S ('RewriteCond %{HTTP_USER_AGENT} ^GetWeb! [OR]'));
                        self::rewriteHTLine (new S ('RewriteCond %{HTTP_USER_AGENT} ^Go!Zilla [OR]'));
                        self::rewriteHTLine (new S ('RewriteCond %{HTTP_USER_AGENT} ^GetRight [OR]'));
                        self::rewriteHTLine (new S ('RewriteCond %{HTTP_USER_AGENT} ^FlashGet [OR]'));
                        self::rewriteHTLine (new S ('RewriteCond %{HTTP_USER_AGENT} ^WebLeacher [OR]'));
                        self::rewriteHTLine (new S ('RewriteCond %{HTTP_USER_AGENT} ^WebSauger [OR]'));
                        self::rewriteHTLine (new S ('RewriteCond %{HTTP_USER_AGENT} ^Website\ eXtractor [OR]'));
                        self::rewriteHTLine (new S ('RewriteCond %{HTTP_USER_AGENT} ^Website\ Quester [OR]'));
                        self::rewriteHTLine (new S ('RewriteCond %{HTTP_USER_AGENT} ^WebStripper [OR]'));
                        self::rewriteHTLine (new S ('RewriteCond %{HTTP_USER_AGENT} ^WebWhacker [OR]'));
                        self::rewriteHTLine (new S ('RewriteCond %{HTTP_USER_AGENT} ^WebZIP [OR]'));
                        self::rewriteHTLine (new S ('RewriteCond %{HTTP_USER_AGENT} ^Wget [OR]'));
                        self::rewriteHTLine (new S ('RewriteCond %{HTTP_USER_AGENT} ^Widow [OR]'));
                        self::rewriteHTLine (new S ('RewriteCond %{HTTP_USER_AGENT} ^WWWOFFLE [OR]'));
                        self::rewriteHTLine (new S ('RewriteCond %{HTTP_USER_AGENT} ^Xaldon\ WebSpider [OR]'));
                        self::rewriteHTLine (new S ('RewriteCond %{HTTP_USER_AGENT} ^Zeus'));
                        self::rewriteHTLine (new S ('RewriteRule ^.* - [F,L]'));
                    }

                    // Return
                    return new B (TRUE);
                } else {
                    // Throws
                    throw new ModRewriteNotAvailableException;
                }
            } else {
                // Throws
                throw new CannotGetApacheModulesException;
            }
        } else {
            // Check
            if (EXCEPTION_ON_HTACCESS_WRITE == TRUE) {
                // Throws
                throw new DocumentRootNotWriteableException;
            }
        }
    }

    /**
     * Delay writing of the (.htaccess) file;
     *
     * @author Catalin Alexandru Zamfir <catalin.zamfir@raphpframework.ro>
     * @copyright Under the terms of the GNU General Public License v3
     * @version $Id: Environment.php 1 2012-10-26 08:27:37Z root $
     */
    public static final function setDelayedHTWrite (B $objSetOrUnset) {
        // Check
        if ($objSetOrUnset
        ->toBoolean () == TRUE) {
            // Set
            self::$objHTWriteIt
            ->setInt (1);
        } else {
            // Set
            self::$objHTWriteIt
            ->setInt (0);
        }
    }
}
?>
