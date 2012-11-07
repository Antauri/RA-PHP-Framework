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

 ###################################################################
 This program is provided by S.C. KIT Software CAZ S.R.L. with it's
 HQ based in Romania. Any extensions or GPL'ed code used by this here
 framework are properties of their respective owners.

 If you feel the need to see this framework get extended and want to
 donate to its development please use the website: www.kitsoftware.ro
 for information on how to contact us. Any funds received from the
 community will be put in the development of the framework, and proper
 proof of this would be made available for anyone interested.

 Along the official website of the company, we at KIT Software have put
 up a dedicated website for this framework at www.raphpframework.ro,
 where you can find information on getting the latest source-code.

 If you ever need to consult the SVN trunk of the project, you can either
 use a SVN compatible editor (Eclipse) or any SVN command line or GUI
 client, by checking out: http://raphpframework.ro/svn/RA/trunk - where
 SVN commit access is only granted to the developers but anonymous read
 access is granted to ALL who wish to check out the project.

 It may be so that some modules are not provided by default (ex. frontend)
 or they are provided as a skeleton for you to work with. If that's the case
 please have a full understanding of the way the framework works before diving
 into any production code development;

 ###### RA PHP Framework :: License ##############
 If this code is part of your project or this license exists it means that one
 of the developers of this project has given or allowed a license to to be used
 for the purpose of your project. If that's the case, you need not worry about
 copyright issues as this here disclaimer is proof of it.

 ###### RA PHP Framework :: Configuration ########
 @version $Id: Configuration.php 19 2012-10-26 20:45:34Z root $
 @author Catalin Alexandru Zamfir <catalin.zamfir@raphpframework.ro>
 @copyright S.C. KIT Software CAZ S.R.L. Released under the GPLv3;
 */

/**
 * What is the RA PHP Framework, and what's it all about?
 *
 *      The RA PHP Framework has been born out of the will and desire to make a framework that's easy to use, as close to other
 *      languages like C++ and Java, but that offers the same adaptability and speed of Web development that had made PHP one of
 *      the chosen languages for this purpose.
 *
 *      We wanted a framework that's easy to understand, plenty in documentation, has strict data types (named short STH or as it
 *      is properly called: Strong Type Hinting) but which still follows the MVC pattern with a concentration more on providing that
 *      in a way that doesn't necessarily impose it (allowing you to fork it if you want) and concentrating on modules, small ones,
 *      that can be the basics for "what-ever-you-want-your-app-to-be.". If you're familiar to "LEGO" then you're familiar with us.
 *
 *      We for short and don't believe in the "controller/action/param1/param2/paramN" ideology that, in a restricting way
 *      forces filenames to be mapped/named in a specific way. Rather, we believe that a combination of powerful design patterns
 *      can have more impact and versatility on how projects are done allowing for greater customization, control, maintainance as
 *      this is our belief and has been tested through-out many PHP/SQL projects that our team or our developers have been a part of.
 *
 *      So we believe that taking PHP adaptability, combining it with MVC but still providing a plethora of modules that you can
 *      work on and develop and share back with the community that we will succed in offering the community a RAD (Rapid
 *      Application Development) approach that really does the trick.
 *
 *      Why such an approach? Cause we believe in diversity. We've seen a plethora of projects over the years sprung from different
 *      ideas and different views. Leveraging on this fact, our belief is that if we encourage creation through differentiation,
 *      we're going to tap into that wide community of supporters that just love freedom, in software and in the way one great
 *      philosopher J. J. Rouseau has put it: "Your freedom ends where the freedom of others, begins!".
 *
 * So what does "RA" stands for? RA[D]?
 *
 *      You could call it that. It actually stands for "Revolutionary Algorithms" but we've actually used "RA" (Horus, the Egyptian
 *      God) as in "The Eye of RA". The initial meaning has been, revolutionary being the way we actually develop: keeping code
 *      reusable but making it so that this reusability helps applications built on RA to be rapidly developed. We've never actually
 *      had a debate on what to call it, it just sprung out of pure inspiration and it stood like that since then.
 *
 * Where will I find the documentation?
 *
 *      At the http://raphpframwork.ro/svn/RA/trunk there will ALWAYS be available the latest SVN code. We opted NOT to provide an
 *      up-to-date documentation on site, for two obvious reasons:
 *
 *      1. it would be hard to maintain up-to-date information in two places (in the code and online);
 *      2. and it tends to get harder for developers to actually go in the code, then online to read the docs, then back to the code
 *      and vice-versa. You get the ideea.
 *
 *      So, we opted to do something that we believe it's a little bit smarter: we will provide tutorials online, things you can
 *      really learn from, gimmicks, tips, tricks that make RA beautiful, while keeping detailed documentation, RIGHT ON THE
 *      SPOT, by this we mean, in the source-code files of interest. Why is that?
 *
 *      - because we believe that having documentation with the code, will help everyone wanting to dig deep;
 *      - also, it comes along with the code, right ON the code, right where you actually need it;
 *      - and keeping it near-the-code means that a developer modifying or updating a method or a procedure may actually update
 *      and in-file documentation provided, instead of going online and doing modifications online which they usually forget;
 *      - thus, we believe that documentation that is updated on the same spot with the code, WILL NOT GET STALE.
 *
 *      NOTE: Starting from this file, going through each incremental file, you will find information on how those pieces link
 *      with each other and you will be able to grasp the necessary information you need to understand RA and work with it on
 *      a more than daily basis.
 *
 * Ok! Let's the started,  what's with this file?
 *
 *      The purpose of this file is pretty simple: we have a general config file that allows us to set quite global settings for
 *      our application. By global we mean that if we have projects that share a common server, we could make it so that every
 *      project shares this common file associated with the server. You'll se that each file in the INC_DIR directory actually
 *      provides pretty good documentation. It may take some time to read through but it will give you one interesting ideea about
 *      the inner workings of RA that jumping on the "core" developer team would be a snap.
 *
 *      Also, in the development of RA, we've tried our best to make the whole PHP environment, under our control. That means, we
 *      either set ALL ini_set configurations (through constants defined in this config file) and we even generate the .htaccess
 *      for Apache (btw. only compatible with Apache 2+/*Nix/Windows/mod_php) - so that the probability of errors coming from
 *      outside or from unset configuration parameters are low. That means, that we've scanned and tried to control any possible
 *      PHP_INI_PERDIR and PHP_INI_ALL directive listed on the php.net manual. Thus, we're sure that each environment is fully under
 *      the control of the developer rather then under the control of a sysadmin that may not be accustomed to how your application
 *      needs to run or what environment it requires;
 *
 *      Let's get started:
 *          - SKIN: contant determines the default skin to enable if nothing set on the session SESSION['skin']. The user-set
 *          session takes precedence, else, the SKIN value from here is taken. This relates to file/directory structure in modules
 *          where each modules has a structure like: mod/something/skn/[default]/{css, jss, img}. So actually in a big application
 *          skins could be changed dinamically by having views (template files) stored in different directories, each with it's own
 *          customization, .css, .js and image files;
 *
 *          - LANGUAGE: same ideology as above. Changing it here means changing the folder where the framework queries for its
 *          language constructs. In short it relates to the general: int/[en_GB] directory or to a more module specific and quite
 *          interesting: mod/something/int/[en_GB]/ directory structure;
 *
 *          - DATE_STRING/DATE_TIMEZONE: should be pretty clear. They are constants of how we commonly represent dates in our
 *          system. Could be changed for specific needs if you desire. (although we yet have seen a use, as these are related
 *          to how dates are parsed in error reports and related, not how dates are shown in your specific app);
 *
 *          NOTE #1: as you may see from the other LANGUAGE_DIR to HTM_EXTENSION constants, we define some ususal constants that
 *          we use through-out the system for convenience. You'd have to dig deep to either want or need to change these so, for
 *          most applications they could be left as-is;
 *
 *          NOTE #2: you've probably even seen from the directory structure: we don't enforce a clear MVC separation. We actually
 *          love freedom. Although, if you've just purelly scanned these modules you may have observed the fact that they look
 *          the same. Or at least the same. That's cause we've developed a naming scheme that's common for our team and that tends
 *          to reduce code and file polution down to a minimum by using or reusing directory hierarchy or file names constantly.
 */

define ('SKIN', 'default');                                     # Default skin to be applied through-out the framework;
define ('LANGUAGE', 'en_GB');                                   # Default language to be used through the framework;
define ('DATE_STRING', 'F j, Y, g:i a');                        # Default date format used to show dates;
define ('DATE_TIMEZONE', 'Europe/Bucharest');                   # Default timezone for your project;
define ('DEFAULT_LOCALE', 'en_GB.utf8');                        # Default locale for your project;

// System defaults
define ('LANGUAGE_DIR', 'int');                                 # Language directory for translation files;
define ('INCLUDE_DIR', 'inc');                                  # Includes directory for .php files;
define ('IMAGE_DIR', 'img');                                    # Images directory (globally and modules);
define ('JAVASCRIPT_DIR', 'jss');                               # Javascript directory (modules only);
define ('UPLOAD_DIR', 'upd');                                   # Upload directory, permanent storage;
define ('PLUGIN_DIR', 'pgn');                                   # Auto-loading plugins (GeSHi, etc.);
define ('CACHE_DIR', 'cch');                                    # Cache directory, cache_* files;
define ('LOG_DIR', 'log');                                      # Log directory, PHP and user-generated logs;
define ('FORM_TP_DIR', 'frm');                                  # Forms directory, generic templates for forms;
define ('ADMIN_DIR', 'adm');                                    # Administrative path to the back-end;
define ('MOD_DIR', 'mod');                                      # Modules directory, for custom mods;
define ('ERR_DIR', 'err');                                      # Error directory, for framework error templates;
define ('TEMP_DIR', 'tmp');                                     # Temporary upload directory, for uploads in progress;
define ('CFG_DIR', 'cfg');                                      # Configuration directory, for .ini files;
define ('BACKUP_DIR', 'bck');                                   # Backup directory, for cron auto-backup;

// Skin defaults
define ('SKIN_DIR_DIR', 'skn');                                 # Skin directory, where IMG/JSS/CSS files go to;
define ('SKIN_JSS_DIR', 'jss');                                 # Javascript directory for skins;
define ('SKIN_CSS_DIR', 'css');                                 # Cascading Style-Sheets directory for skins;
define ('SKIN_IMG_DIR', 'img');                                 # Images directory for skins;

// Extensions
define ('CSS_EXTENSION', '.css');                               # Common extension for .css files;
define ('JSS_EXTENSION', '.js');                                # Common extension for .hs files;
define ('TPL_EXTENSION', '.tp');                                # Common extension for template files; (default .tp);
define ('HLP_EXTENSION', '.qst');                               # Common extension for help and text template files;
define ('SCH_EXTENSION', '.schema');                            # Common extension for schema files;
define ('HTM_EXTENSION', '.html');                              # Common extension for HTML files;
define ('PHP_EXTENSION', '.php');                               # Common extension for PHP files;
define ('INI_EXTENSION', '.ini');                               # Common extension for INI files;

/**
 * Constants for slash!? Really? Why that?
 *
 *      Yes, why not? Why have an incompatible way of concatenating paths? Why store: (. '/' .) in the code when we could
 *      just write: _S (easier, less chars, don't you think?) for every path out there and if we change from a Linux to a
 *      Windows hosting environment or vice-versa, the path character will automatically change and our application will be
 *      compatible with that hosting environment.
 *
 *      Also, although the de-facto standard of storing files/folders in most common system is to use the / (slash) character,
 *      there may be systems that have different structures. To allow for such a weirdness (in a good way) we should think of
 *      everything as configurable.
 */

define ('_S', DIRECTORY_SEPARATOR);                             # _S        = System Slash (PHP constant DIRECTORY_SEPARATOR);
define ('_WS', '/');                                            # _WS       = Web Slash. It's always '/' no matter what;
define ('_NONE', '');                                           # _NONE     = An empty string, used everywhere;
define ('_S_WIN', '\\');                                        # _S_WIN    = Define the Windows (32) System Slash;

/**
 * Processing of CFG_DIR/*.ini's
 * This does not used, yet unitialized A container
 */
$objConfig = parse_ini_file (CFG_DIR . _S .
'Configuration' . INI_EXTENSION,  TRUE);

/**
 *  Section provides auto-configuration of the secret key and initialization vector for the default crypt method of our framework. The
 *  parameters are taken from the configuration file (and must be 128 bits at most). They are used in comunicating with secured
 *  services that you implement on your own (ex. PHP to Java, where you want the data transmitted to be secure, eg. no man-in-the-middle
 *  attacks on your data).
 *
 *  	- SECRET_KEY: defines the key to be used in the default AES encryption;
 *  	- SECRET_IVC: defines the initialization vector (default random data) used to crypt and encrypt the data;
 */
define ('SECRET_KEY', $objConfig['Secrets']['key']);			# The secret AES key used to encrypt data in this framework;
define ('SECRET_IVC', $objConfig['Secrets']['ivc']);			# The initialization vector for the encryption function;

/**
 *      Below, some of this configuration options control how the framework will function inside the given hosting environment. By
 *      this we mean, the way it will try to control the environment, how it will set its default settings and what features will
 *      there be enabled for the project at hand. Keeping it short:
 *
 *      - STORAGE: a path on the filesystem where temporary (session, cache) and permanent files are stored (UPLOAD_DIR) which can
 *      reside on the same hard-drive, or a different partition, or some NFS share. You name it. We allow it to be configurable in
 *      order to allow for applications to benefit from such a loose-coupled hirearcy. Also, keeping these files out of the
 *      DOCUMENT_ROOT is a security enhancement, keeps files well organized and empowers maintainability;
 *
 *      - STORAGE_AS_DOCUMENT_ROOT: if set to true, then DOCUMENT_STORAGE defined below will be the same as the DOCUMENT_ROOT. For
 *      most purposes this isn't recommended as it makes some files exposed to URL guessing (if not .htaccess is in place to Deny such
 *      requests) and it clutters the document tree with files that should have a dedicated storage space for them;
 *
 *      - REWRITE_ENGINE: controls if we will allow the APC to write the .htaccess file that will do the URL routing for us. We
 *      allow this to be disabled for instalations that don't want the example.com/Key/Var type URLs for some other weird URL types
 *      that they have in mind. WARNING: Be sure to have +FollowSymLinks set in APACHE_OPTIONS or be ready to debug .htaccess
 *      mod_rewrite issues on your own;
 *
 *      - IGNORE_USER_ABORT: does what it says. Tells if the PHP script is to be aborted if the user halts (using the STOP browser
 *      button) or if the action is ignored and the script executes furhter without interuption;
 *
 *      - ERROR_REPORTING_*: tells what error_reporting should be by default active. We set that to E_ALL, cause we want to catch
 *      PHP notice's and treat them as blocking errors just to be sure that proper code is developed inside the framework, not code
 *      that's written in a hurry which generates a lot of notices (which in fact slow down an application built both in production
 *      but also in the debugging/development stages where these kinda errors that trigger a few bugs must be found and fixed);
 *
 *      - APACHE_SERVER_SIGNATURE: if this configuration parameter is set it basically means that, when showing the default index,
 *      the Apache Server will not add its signature to pages server inside the / (root) of your application and any other subdirectory
 *      under the root until it meets another .htaccess file that rewrites this settings;
 *
 *      - APACHE_OPTIONS: is something you can set for the generated .htaccess to control options specific for the / (root) of your
 *      application. By default we disable Indexes (for performance reasons) and enabled FollowSymLinks (WARNING: for RewriteEngine On
 *      you're bound to leaving FollowSymLinks ENABLED, or a 403 Forbidden will be issued by Apache alone);
 *
 *      - MEMORY_LIMIT/UPLOAD_MAX_FILESIZE/POST_MAX_SIZE: should be all pretty clear. Configured here they control how much memory
 *      a script can consume, what is the maximum file size to be uploaded and the maximum POST size to be accepted. By default, they
 *      are PHP_INI_PERDIR settings and that should be taken in consideration when developing;
 *
 *      - REGISTER_LONG_GPC: by default, disabled, means that those long HTTP_POST/HTTP_GET vars that you see in old PHP4 code, are
 *      not registered anymore. Meaning they are not available. Instead the usual GET/POST are available for use, it a warning that
 *      the GET var is subject to modification by the URL object while POST is subject to modifications by the Forms object. Those are
 *      documented on the spot whith implications for each.
 *
 *      - ASP_TAGS: disabled by default, as so are SHORT_OPEN_TAGS. You can enable it back if you have a project where you're
 *      importing outside code written in this manner, but for projects developed from the scratch on this framework, we disable
 *      this flag because it's best if we kept code future-proof. Also, it may have a performance impact due to the fact that PHP is
 *      not going to scan for ASP tags anymore, nor will it for SHORT_OPEN_TAGS (if disabled);
 *
 *      - ZEND_1_COMPATIBLE: or as we say it: PHP4 compatible (PHP4 = Zend Engine #1) - and we disable such a feature because we'd love
 *      to have only PHP5+ compatible code (classes in specially). So, use of compound PHP4 and PHP5 code, by default is discouraged
 *      but such behaviour could be allowed if one chooses to. It's better if you develop with future in mind, rather than trying to
 *      fix old, broken code;
 *
 *      - MAX_INPUT_TIME: by default this is set to -1 which means, infinite. There's no need to impose such a limit on the input
 *      time as there are scenarios where an user either with a slow connection or uploading something will have to wait for
 *      its request to be finished. Actually having this limit set, would output an error which for many projects, this is not
 *      the desired behaviour. Thus, this got disabled by default;
 *
 *      - IMPLICIT_FLUSH: by default, disabled as this is a performance hog, we don't allow implicit flush with every echo request. As
 *      far as we're concerned, this setting is mainly available for convenience for high-profile or core developers ready and able
 *      to modify the default output buffering mechanism of the framework for their own uses. This is provided as a switch for them
 *      to help debug the output buffering mechanism in place;
 *
 *      - SCRIPT_TIME_LIMIT: by default, PHP sets a script_time_limit to disable scripts that go haywire. We disabled this, by setting
 *      this value to 0, because most big projects usually relly on never-ending crons that run for days on end. For those kind of
 *      projects, setting such a limit on scripts that execute properly is just nuts. For other type of projects, the ones that
 *      focus on a presentation/company website or others, setting this to something else than 0, may be appropiate;
 *
 *      - SESSION_AUTOSTART: by default, enabled, we want the session to auto-start with every request. The session is an important
 *      part of how the framework works and how it stores its values. If you have or need an environment where the session should
 *      be disabled, you can disable auto-start and add the necessary code for things to continue working as before. (manually setting
 *      needed session parameters and others);
 *
 *      - SESSIONCACHEEXPIRE: set to a big number that will make sessions last pretty long (10 years, for example). Why? Because
 *      by default, PHP sessions lasts for as much as 24 minutes on the default settings which is pretty short for operations
 *      that tend to go beyond that limit (writing a review, a blog post, a product description, anything that falls in that
 *      category takes more than half-an-hour, which, on a session authentication mechanis on the default PHP settings would
 *      automatically log the user out, making its content lost in the procedure. We tend to make this problem dissapear by
 *      default settings this expire time as big as possible or at 0 to disable it);
 *
 *      - SESSION_USE_TRANSPARENT_ID: by default, PHP's session mechanism can run on either a session cookie, where it stores the
 *      already known PHPSESSID cookie or it can transparently pass that as part of the URL. The last scenario is not recommended as
 *      users tend to pass URLs around, which means they will pass account information around with the likellyhood that this will
 *      be a security breach for users unfamiliar with how sessions work. Thus, this is disabled. If you ever find a scenario where
 *      this should be used, you're free to enable it back, for your own project;
 *
 *      - SESSION_COOKIE_LIFETIME: the default cookie lifetime is about 24m. Translate that to seconds and it's still not enough for
 *      most projects where editing a bigger text in a WYSIWYG form or reviewing a textarea input takes more than half-an-hour. For
 *      that purpose, we defaulted the session cookie lifetime to 0 which means that the session cookie will be available until
 *      the window (browser) closes;
 *
 *      - SESSION_CACHE_LIMITER: by default set to 'public'. This feature is properly documented on php.net. Although, other uses
 *      other than [public] are extremelly rare, we allow it to be a configurable parameter in the framework, for convenience mainly;
 *
 *      - SESSION_PATH: this reflects the name of the directory, inside the DOCUMENT_STORAGE where we store session files. We opted to
 *      move session files outside the OS /tmp directory for a few reasons: one, it's easier for the developer to inspect the
 *      serialized data stored for a particular session to debug session related issues, if any, and it kind of keeps project
 *      dependencies in one spot. If you're worried about hackers "accessing the SESSION_PATH" from a URL, you should not worry,
 *      as the first line in the generated .htacess file is "IndexIgnore *" which basically means that all folders inside the
 *      DOCUMENT_ROOT with no default index file, will not be shown (thus, they can't be read). More you can secure those directories
 *      even further with a "Deny from all" .htacess file in them;
 *
 *      - SESSION_PREFIX: our SESSION files are stored on the SESSION_PATH, inside the DOCUMENT_STORAGE. They have a file name prefix,
 *      that's by default set to 'session'  but you can name them whatever you want. If for example you share a common SESSION_PATH
 *      for many projects and don't want conflicts, you should change the name for every project. Or, better, you'd like to share
 *      session information between them, case where you'd want to keep the session name, the same;
 *
 *      - SESSION_GC_PROBABILITY: by default set to 100, this sets the probability that the GC would execute everytime, by the
 *      algorithm that GC_PROBABILITY divided by GC_DIVISOR = every N requests, the GC will run, thus doing the cleaning jobs
 *      required by PHP. You can juggle with these settings as you wish;
 *
 *      - SESSION_GC_DIVISOR: by default the GC (Garbage Collector) does not run with every request, because that would consume a lot
 *      of resources that may bee neded somewhere else. Thus, if you wish to change the way PHP's GC starts cleaning things up, you
 *      can change this divisor to something that, when divided with SESSION_GC_PROBABILITY will result in the GC executing every N
 *      requests. (N being the result of the division);
 *
 *      - DEFAULT_CHARSET: we default ot 'utf-8' as the default charset. This can be changed for projects in arabic or in right-to-left
 *      setups where some specific charset is needed or required;
 *
 *      - SHORT_OPEN_TAG: by default, disabled. We want code to be future-proof. Although you can enable this feature back, we
 *      encourage you not to. It may be best if, for example, template code is best kept with ful <?php ?> tags rather going
 *      for the short ones which, in some environments, where code moves around from server to server, this can get tricky and cost
 *      money or time for the sysadmin to make the necessary adjustments for the server to interpret them. Also, they may get
 *      deprecated in the future, we may never know;
 *
 *      - DISPLAY_ERRORS: this must be on for development/staging environments. As they provide the whole mechanism through which
 *      we catch PHP errors either through the default framework error handler or by parsing the output through the framework's
 *      output handler (we do that to catch parse errors). If disabled, the error catching mechanism is supressed and usually, a
 *      blank page is shown to the user upon error. WARNING: only disable this, carefully, for production environments. If your
 *      application is properly developed, then you should not worry about users seeing error screens;
 *
 *      - DISPLAY_STARTUP_ERRORS: if enabled, startup errors (either Apache/ISS or PHP errors) are shown. This pretty much gives
 *      you an interesting idea of what happened when starting PHP, if there were errors or not if for example, you find your
 *      application in a non-working state;
 *
 *      - PHP_HTML_ERRORS: by default we disabled PHP's HTML errors. The HTML surrounding the messages messes with our own error
 *      screens and they way they are built. And they don't offer any functionality, so they are disabled;
 */

define ('STORAGE', $objConfig['Configuration']['storage']);     # Path to temporary/permanent storage. Ending / important;
define ('STORAGE_AS_DOCUMENT_ROOT', FALSE);                     # If enabled, DOCUMENT_STORAGE == DOCUMENT_ROOT, else not;
define ('REWRITE_ENGINE', TRUE);                                # If apache_get_modules ==> mod_rewrite, RewriteEngine On/Off;
define ('IGNORE_USER_ABORT', TRUE);                             # Continue execution even when the user aborts;
define ('ERROR_REPORTING_LEVEL', E_ALL);                        # The error reporting level to go by, E_ALL by default;
define ('APACHE_SERVER_SIGNATURE', 'Off');                      # To either show or not, the signature string on auto-indexed pages;
define ('APACHE_OPTIONS', '-Indexes +FollowSymLinks');          # Don't auto-index, FollowSymLinks (for mod_rewrite != 403);
define ('MEMORY_LIMIT', '1024M');                               # Maximum memory to consume before issuing an error;
define ('UPLOAD_MAX_FILESIZE', '1024M');                        # 1GB should be enough for most uses;
define ('POST_MAX_SIZE', '256M');                               # An 256MB posted string should be plenty if not enough;
define ('REGISTER_LONG_GPC', 0);                                # Disable registering of HTTP_POST/HTTP_GET, add performance;
define ('ASP_TAGS', 0);                                         # ASP tags not supported, future-proof, some speed improvement;
define ('ZEND_1_COMPATIBLE', 0);                                # PHP4 support is disabled, big performance impact;
define ('MAX_INPUT_TIME', -1);                                  # Time to allow a script to wait for input, usually infinite;
define ('IMPLICIT_FLUSH', 0);                                   # Is PHP's implicit_flush enabled or not;
define ('DEFAULT_OUTPUT_BUFFERING', 'Off');                     # If PHP's default output buffering is enabled;
define ('SCRIPT_TIME_LIMIT', 0);                  		        # Time to allow a script to execute before outputing and error;
define ('SESSION_AUTOSTART', TRUE);                             # By default, auto-start the session;
define ('SESSION_CACHE_EXPIRE', 60 * 60 * 24 * 30);             # One month for the session cache to expire should be enough;
define ('SESSION_USE_TRANSPARENT_ID', 0);                       # Hide the transparent PHPSESSID in URLs;
define ('SESSION_COOKIE_LIFETIME', 86400);                      # One the should be enough for a cookie lifetime;
define ('SESSION_CACHE_LIMITER', 'public');                     # Defaults to 'public' as other uses are rare;
define ('SESSION_PATH', 'ses');                                 # Path in DOCUMENT_ROOT where to save session files;
define ('SESSION_PREFIX', 'session');                           # A file name prefix for session files inside SESSION_PATH;
define ('SESSION_GC_PROBABILITY', 2);                           # The probability that the GC will run;
define ('SESSION_GC_DIVISOR', 1000);                            # The divisor for GC, divided by probability resulting each N requests;
define ('DEFAULT_CHARSET', 'utf-8');                            # Default to utf-8 as it would cover most uses;
define ('SHORT_OPEN_TAG', 0);                                   # Disable short open tags, speed improvement, future-proof;
define ('DISPLAY_ERRORS', 1);                                   # Not to be disabled for development/staging environments;
define ('DISPLAY_STARTUP_ERRORS', 1);                           # If enabled, shows startup (Apache/IIS, PHP) errors;
define ('PHP_HTML_ERRORS', 0);                                  # Disabled HTML errors in PHP, no advantages;
define ('EXCEPTION_ON_HTACCESS_WRITE', FALSE);                  # Enabled auto-write of .htacess file or not (for manual edits);
define ('RESTRICT_MSIE_USERS', FALSE);                          # Restrict users of MSIE from accessing, redirect to hard-coded URL;

/**
 *  Section provides some constants that define how the framework code executes. This should not be changed unless you're in the core
 *  development team or have a full understanding of how the framework works. If you change the defaults, you take responsability
 *  in the way your application works as these have a high implication on how the underlying code executes:
 *
 *  	- DEBUG: if enabled the two error catching mechanism are enabled. We do error catching by two methods, with PHP's own function
 *  	the already known set_error_handler and through PHP's output buffering. The last one is used to catch PHP parse errors which
 *  	are, on the time of this writing (PHP 5.3.12+) uncatchable with the default error catching mechanism. Thus, we do the catching
 *  	by waiting for some error tags (specifically set in the code, read-on further) for which we determine if an error happened
 *  	or not
 *
 *  	- SQL_PERSISTENT_CONNECTION: for MySQL, determines if the connection should be kept permanently opened. We disabled this by
 *  	default because development scripts (that error) tend to forget to close permanent connections which leaves an open window
 *  	for errors from MySQL saying that the total number of active permanent connections has been reached in most common setups);
 *
 *  	- SQL_PREFIX: by default, table names are prefixed with the default prefix set in config.ini. As we prefer not to concatenate
 *  	code for each manually executed SQL string, we use an SQL prefix, which defaults to _T_. That means that if the configuration
 *  	prefix is ra_, when we execute a query with a table name like _T_configuration that translates to ra_configuration in a way
 *  	that's automatic. In most cases, you won't have to deal with this prefix as it's already an implemented core feature for
 *  	anything that goes through the SQL::Q method (Q = short for Query);
 *
 *  	- SQL_SET_GLOBAL_MAX_PACKET: default to a big packet, as we've seen cases where a big import (or our Settings/Countries SQL
 *  	import file, about 50MB) errors because of a max packet being reached. Setting it to a big number, resolves this issue with
 *  	a degree of elegancy and allows us to have big imports/big SQL operations through our own code;
 *
 *  	- CLOSESESSION_ON_OBJECT_SCOPE: if enabled, whenever you're going to receive an error this is going to erase all SESSION data
 *  	leaving you in a non-authenticated state or with previous session data not set. Use wit caution as the implications of this
 *  	parameter go deep in the code and may have a big impact on your application;
 *
 *  	- TEMPORARY_TIMEOUT: files in the UPLOAD_DIR temporary directory that don't have a LOCK_EX on them, but are failed downloads
 *  	that have not been moved to other places are going to be checked to see if the are old. The temporary timeout means that it
 *  	will allow for so many seconds to pass before it starts cleaning out files from the temporary directory (caution!);
 *
 *  	- OB_GZIP: if enabled, and most of the times it is (should disable for CLI use) it will output content gzipped to the browser,
 *  	thus making network transfers a little bit more faster. In combination with GZIP_LEVEL and GZIP_TYPE you should be able to
 *  	get the proper settings for your environment. If you receive weird browser error screens or weird output, be sure to disable
 *  	this and start debugging from there;
 *
 *  	- OB_GZIP_LEVEL: a good 9 (and the biggest compression) will reduce the file size to a minimum but it's resource intensive for
 *  	every request. 6 is a good compromise between network transfer speed and server resources consumed in the process of gzipping
 *  	contents for browser output;
 *
 *  	- OB_GZIP_TYPE: if wet to 0, default output is going to be GZIP, while 1 means DEFLATE. The later is not so well tested in
 *  	some environments, so we default to GZIP. If your application requries the later compression mechanism, then go for that, but
 *  	please keep in mind that it's not as tested as the first mechanism so if you ever see bugs or issues with the second mechanism
 *  	we encourage you to file a bug with us so we can fix it for future releases;
 */

define ('SYSTEM_LOAD_MAX', 90);                                 # The system max average loading after which we error out;
define ('SQL_PERSISTENT_CONNECTION', 0);                        # If we allow SQL connections to be kept alive or nto;
define ('SQL_PREFIX', '_T_');                                   # Standard prefix in basic queries to match table prefixes;
define ('SQL_SET_GLOBAL_MAX_PACKET', FALSE);                    # If we can set the GLOBAL_MAX_PACKET for SQL connections (MySQL);
define ('TEMPORARY_TIMEOUT', 180);                              # Time to check for temporary files in the UPLOAD_DIR;
define ('OB_GZIP', TRUE);                                       # If we enabled GZIPped output buffering or not;
define ('OB_GZIP_LEVEL', 9);                                    # GZIPping requires a compression type; 9 is best, 6 is performance;
define ('OB_GZIP_TYPE', 0);                                     # Here 0 means GZIP, 1 means DEFLATE;

/**
 * We use some code shortcuts and other static data that we define here as constant. For the first, we define a few constants that
 * are used with regularity through our code, such as _N_ (newline), _T_ (tab) or _U (underline) that allow us to make changes in our
 * paths, scripts, filenames as quick as changing one constant to another. Other constants are for:
 *
 * 		- ER*: path to files (stripes/backgrounds) for our error-screen. These errror screens are the defautl framework error screens
 * 		that will notify you of parse errors, notifications, warnings, recoverable errors and others that we catch through RA's error
 * 		handler (in a combination with the output buffering mechanis, for parse errors);
 *
 * 		- PHP_ERROR_LOG: in the LOG_DIR we save a concatenated file of all errors that have happened in the system. We name this by
 * 		default "ERROR_LOG_FROM_PHP" but you can call it "Jackie" if you want it or any other name by that matter (this allows for
 * 		specialized log analysis tools that need a specific file name);
 *
 * 		- DEFAULT_ERROR_CSS_CLASS: the default CSS class for marking errors. You can change it if you'd like to design another kind
 * 		of error page for your specific RA working copy;
 *
 * 		- RA_SCHEMA_HASH_TAG: when editing .schema files we need a TAG for which we execute queries by. As we cannot execute
 * 		multiple queries, creating databases/tables requires that we separate SQL statements by this RA_SCHEMA_HASH_TAG. If you'd
 * 		like to change it then you can do that here (remember to change it globally if you do that);
 */

define ('_N_', PHP_EOL);                                        # NEW_LINE character (default old: "\n" or PHP_EOL);
define ('_T_', "\t");                                           # TAB character;
define ('_CRLF_', "\r\n");                                      # CRLF, cause Windows needs a fix;
define ('_PIPE', '|');                                          # PIPE, used mainly when exploding ();
define ('_U', '_');                                             # _, for simple inclusion;
define ('_D', '-');                                             # -, for use in URLs;
define ('_SP', ' ');                                            # _SP, as an empty space;
define ('_CL', ': ');                                           # _CP, as a ': ';
define ('_DC', '::');                                           # _DC, as a function call separator;
define ('_DCSP', ' :: ');                                       # _DCSP, a separator (used mainly in <title>);
define ('_ANY', '.*');                                          # _ANY, string (for regexes, mainly);
define ('_DOT',	'.');											# _DOT, string (for regexes, mainly);
define ('_QOT', '\'');                                          # _QOT, as a single quote;
define ('_DTE', '...');                                         # _DTE, the ... extension (when shortening);
define ('_AT', '@');                                            # _AT short for the monkey's tail (address);
define ('PHP_ERROR_LOG', 'Errors');                             # Name of the default PHP error_log file;
define ('DEFAULT_ERROR_CSS_CLASS', 'RA_Error_On_Input');        # Default framework error class;
define ('RA_SCHEMA_HASH_TAG', '___RA_SCHEMA_HASH_TAG_');        # Default SCHEMA_HASH_TAG through which we split queries by;

// Require the engine
require_once 'Loader.php';
?>
