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
 * Provides an interface for settings management, common or specific;
 *
 * @author Catalin Alexandru Zamfir <catalin.zamfir@raphpframework.ro>
 * @copyright Under the terms of the GNU General Public License v3
 * @version $Id: Settings.php 1 2012-10-26 08:27:37Z root $
 */
class Settings extends Commons {

    /* Countries */
    public static $objCountry;
    public static $objCountryIso;
    public static $objCountryName;
    public static $objCountryPrnt;
    public static $objCountryIsoT;
    public static $objCountryCode;

    /* Cities */
    public static $objCities;
    public static $objCitiesId;
    public static $objCitiesCIso;
    public static $objCitiesName;

    /* Errors */
    public static $objErrors;
    public static $objErrorsId;
    public static $objErrorsCode;
    public static $objErrorsTitle;
    public static $objErrorsContent;

    /* FEATURE: Backmapping */
    protected static $objItem;
    protected static $objItemId;
    protected static $objItemTitle;

    /* RSS CONSTANTS */
    const RSS_CHANNEL = 'channel';
    const RSS_TITLE = 'title';
    const RSS_PUBLISHED_DATE = 'pubDate';
    const RSS_LINK = 'link';
    const RSS_DESCRIPTION = 'description';
    const RSS_ITEM = 'item';
    const RSS_GUID = 'guid';
    const RSS_ALTERNATE = 'alternate';
    const RSS_TYPE = 'application/rss+xml';

    /* XML SITEMAPs */
    const XML_URL = 'url';
    const XML_LOCATION = 'loc';
    const XML_LAST_MOD = 'lastmod';
    const XML_CHANGE_FREQ = 'changefreq';
    const XML_PRIORITY = 'priority';

    // CONSTRUCT
    public function __construct () {
        // Commons
        $this->tieInConfiguration ();

        // Table
        $this->tieinDatabase ($objT =
        new A (Array (self::$objCountry,
        self::$objCities,
        self::$objErrors)));

        // ACLs
        $this->setACL (new S ('Configuration'));
        $this->setACL (new S ('Country List'));
        $this->setACL (new S ('Cities'));
        $this->setACL (new S ('Error Pages'));

        // Rewrites
        $this->doRewrites ();
    }

    /**
     * (non-PHPdoc)
     * @see Commons::doRewrites()
     */
    public function doRewrites () {
        // Check the function
        if (function_exists ('apache_get_modules')) {
            // Check it exists
            if (in_array ('mod_rewrite',
            apache_get_modules ())) {
                // Check
                if (REWRITE_ENGINE == TRUE) {
                    // Routing robots.txt
                    Environment::rewriteHTLine (_S ('RewriteRule ^robots\.txt$ %uId [L]')->doToken ('%uId',
                    Location::staticTo (new A (Array (_T ('Type'), _T ('Kind'), _T ('Method'))),
                    new A (Array (__CLASS__, _T ('Source'), _T ('GetRobots'))))
                    ->doToken (Architecture::getHost (),
                    new S ('index.php'))));

                    /**
                     * In August 2011, we've chosen to forever ban IE browsers, no matter
                     * the version, even if it affected us. This is to proove that standards
                     * and principles count more than money in life! (yes you, Microsoft!)
                     */

                    // Check
                    if (RESTRICT_MSIE_USERS == TRUE) {
                        // Say No To IE: Check for proper UA string and forever bound it
                        Environment::rewriteHTLine (_S ('RewriteCond %{HTTP_USER_AGENT} MSIE [NC]'));
                        Environment::rewriteHTLine (_S ('RewriteRule ^(.*)$ %tId [L]')->doToken ('%tId',
                        _S ('http://raphpframework.ro/trk/RA/wiki/SayNoToIE')));
                    }

                    // Register a router for every moding ...
                    foreach (self::getRegistered () as $objK => $objV) {
                      // Check
                      switch ($objV['Obj']) {
                        case 'Administration':
                        case 'Authentication':
                        case 'Codes':
                        case 'Crons':
                        case 'Frontend':
                        case 'Settings':
                          break;

                        default:
                          // Route
                          Environment::rewriteHTLine
                          (_S ('RewriteRule ^%kId/(.*)$ Section/%kId/Item/$1 [L]')
                          ->doToken ('%kId', $objV['Obj']));
                          break;
                      }
                    }
                }
            }
        }
    }

    /**
     * Does pre-routing actions (like www -> to non-www and many other);
     *
     * @author Catalin Alexandru Zamfir <catalin.zamfir@raphpframework.ro>
     * @copyright Under the terms of the GNU General Public License v3
     * @version $Id: Settings.php 1 2012-10-26 08:27:37Z root $
     */
    public static function doPreRouting (Frontend $objFront) {
        // Redirect to HOME, if nothing set;
        if ($_GET->doCount ()->toInt () == 0) {
            // Set the header key and go home my man
            Header::setStr (new S (Header::MOVED_PERMANENTLY));
            Header::setKey (Location::staticTo (new A (Array (_T ('Section'))),
            new A (Array (_T ('Home')))), new S ('Location'));
        }

        // -- Fix the double www. and non-www;
        if (Location::staticTo ()
        ->findPos ('://www.') instanceof I) {
            // Moved me permanently
            Header::setStr (new S (Header::MOVED_PERMANENTLY));
            Header::setKey (Location::staticTo ()
            ->doToken ('://www.', '://'),
            new S ('Location'));
        }

        // -- Fix the OLD to NEW moving of REWRITE_ENGINE;
        if (strpos ($_SERVER['REQUEST_URI'], 'index.php/') !== FALSE && REWRITE_ENGINE == TRUE) {
            // Moved me permanently
            Header::setStr (new S (Header::MOVED_PERMANENTLY));
            Header::setKey (Location::staticTo ()
            ->doToken ('index.php/', _NONE),
            new S ('Location'));
        }
    }

    /**
     * Does requirements inclusion or exclusion;
     *
     * @author Catalin Alexandru Zamfir <catalin.zamfir@raphpframework.ro>
     * @copyright Under the terms of the GNU General Public License v3
     * @version $Id: Settings.php 1 2012-10-26 08:27:37Z root $
     */
    public static function doRequirements (Frontend $objFront) {
        // Append
        $objFront->objJSS->appendString (_S)->appendString ('jQuery')->appendString (_S);
        $objFront->objCSS->appendString (_S)->appendString ('jQuery')->appendString (_S);

        // Check
        if (Frontend::DEFINED_SPECIFIC_JSS_INCLUDES == 0) {
            // Requirements
            self::manageCSS (new Path ($objFront->objCSS . 'jQUI.css'));
            self::manageCSS (new Path ($objFront->objCSS . 'jQGrowl.css'));
            self::manageCSS (new Path ($objFront->objCSS . 'jQAutoComplete.css'));
            self::manageCSS (new Path ($objFront->objCSS . 'jQFacebox.css'));
            self::manageCSS (new Path ($objFront->objCSS . 'jQTipsy.css'));
            self::manageCSS (new Path ($objFront->objCSS . 'jQDataTables.css'));
            self::manageCSS (new Path ($objFront->objCSS . 'jQNivo.css'));
            self::manageJSS (new Path ($objFront->objJSS . 'jQ.js'));
            self::manageJSS (new Path ($objFront->objJSS . 'jQUI.js'));
            self::manageJSS (new Path ($objFront->objJSS . 'jQBGIFrame.js'));
            self::manageJSS (new Path ($objFront->objJSS . 'jQAutoComplete.js'));
            self::manageJSS (new Path ($objFront->objJSS . 'jQClock.js'));
            self::manageJSS (new Path ($objFront->objJSS . 'jQBind.js'));
            self::manageJSS (new Path ($objFront->objJSS . 'jQUICheckbox.js'));
            self::manageJSS (new Path ($objFront->objJSS . 'jQFileStyle.js'));
            self::manageJSS (new Path ($objFront->objJSS . 'jQEasing.js'));
            self::manageJSS (new Path ($objFront->objJSS . 'jQWidget.js'));
            self::manageJSS (new Path ($objFront->objJSS . 'jQMasked.js'));
            self::manageJSS (new Path ($objFront->objJSS . 'jQGrowl.js'));
            self::manageJSS (new Path ($objFront->objJSS . 'jQLiquidMetalAlgo.js'));
            self::manageJSS (new Path ($objFront->objJSS . 'jQModernizr.js'));
            self::manageJSS (new Path ($objFront->objJSS . 'jQSelectVizr.js'));
            self::manageJSS (new Path ($objFront->objJSS . 'jQFacebox.js'));
            self::manageJSS (new Path ($objFront->objJSS . 'jQTipsy.js'));
            self::manageJSS (new Path ($objFront->objJSS . 'jQTimers.js'));
            self::manageJSS (new Path ($objFront->objJSS . 'jQZeroClipboard.js'));
            self::manageJSS (new Path ($objFront->objJSS . 'jQDataTables.js'));
            self::manageJSS (new Path ($objFront->objJSS . 'jQToJson.js'));
            self::manageJSS (new Path ($objFront->objJSS . 'jQTools.js'));
            self::manageJSS (new Path ($objFront->objJSS . 'jQNivo.js'));
            self::manageJSS (new Path ($objFront->objJSS . 'jQExe.js'));
        } else {
            // Set
            $objFront->doSpecificJSSRequirements ();
        }

        // Reset
        $objFront->objJSS->doToken ('jQuery' . _S, _NONE);
        $objFront->objCSS->doToken ('jQuery' . _S, _NONE);

        // Foreach
        foreach (self::getRegistered () as $objK => $objV) {
            // Check
            switch ($objV['Obj']) {
                case 'Administration':
                case 'Settings':
                case 'Frontend':
                    break;

                default:
                    // Get
                    $objMod = _new ($objV['Obj']);
                    $objMod->tieInAuthentication (self::getAuthentication ());
                    $objMod->tieInFrontend ($objFront);
                    break;
            }
        }

        // TIE'ins
        self::getSettings ()
        ->tieInFrontend ($objFront);
    }

    /**
     * Does some common routing of URL paths to specific extensions;
     *
     * @author Catalin Alexandru Zamfir <catalin.zamfir@raphpframework.ro>
     * @copyright Under the terms of the GNU General Public License v3
     * @version $Id: Settings.php 1 2012-10-26 08:27:37Z root $
     */
    public static function doCommonRouting (Frontend $objFront) {
        // Header
        $objFront
        ->renderFrontend (new
        S (_T ('Header')));

        // Check
        if ($_GET
        ->offsetExists (_T ('Error'))) {
            // Render
            $objFront
            ->renderFrontend (new
            S (_T ('Error')));
        } else {
            // Check
            if ($_GET
            ->offsetExists (_T ('Section'))) {
                // Check
                if ($_GET
                ->offsetGet (_T ('Section')) instanceof S) {
                    // Switch
                    switch ($_GET[_T ('Section')]) {
                        default:
                            $objFront
                            ->renderFrontend ($_GET
                            ->offsetGet (_T ('Section')));
                            break;
                    }
                } else {
                    // 404
                    Settings::routeTo (new
                    S ('404'));
                }
            } else {
                // 404
                Settings::routeTo (new
                S ('404'));
            }
        }
    }

    /**
     * Provides an easy interface to redirect to our 404 page;
     *
     * @author Catalin Alexandru Zamfir <catalin.zamfir@raphpframework.ro>
     * @copyright Under the terms of the GNU General Public License v3
     * @version $Id: Settings.php 1 2012-10-26 08:27:37Z root $
     */
    public static final function routeTo (S $objCode) {
        // Switch
        switch ($objCode) {
          // 404
            case '404':
                // Go
                Header::setKey (Location::staticTo (new
                A (Array (_T ('Error'))),
                new A (Array ('404'))),
                new S ('Location'));
                break;
        }
    }

    /**
     * For each given URL path, renders the appropiate "generic template".
     * This generic template contains common code (HTML, CSS, JS) for that specific module;
     * Inside it, widgets are invoked as needed; They are general layouts, containers!
     *
     * @author Catalin Alexandru Zamfir <catalin.zamfir@raphpframework.ro>
     * @copyright Under the terms of the GNU General Public License v3
     * @version $Id: Settings.php 1 2012-10-26 08:27:37Z root $
     */
    public static function doRenderRoute (Frontend $objFront, S $objT) {
        // Switch
        switch ($objT) {
            // Header
            case _T ('Home'):
            case _T ('Header'):
                break;

            // Footer
            case _T ('Footer'):
                // Title
                self::manageTTL (self
                ::getSettings ()->getConfigKey (new
                S ('Application.Title')));

                // Generation time
                $objFront->objGenerationTime = new
                F (Execution::getExeTime (new S ('Start'),
                new S ('Finish')));
                break;

            // 404, 501, etc.
            case _T ('Error'):
                // Title
                self::manageTTL ($_GET[_T ('Error')]);
                break;

            // Oth.
            default:
                // Routers
                $objFront->$objT
                ->doURLRouting ($objFront);
                break;
        }

        // Execute
        $objTp = new Path (Architecture
        ::pathTo ($objFront->getPathToSkin (),
        $objT . TPL_EXTENSION));

        // Foreach
        foreach ($objFront
        ->getDynamicData () as
        $objK => $objV) {
            // Set
            self::setTp ($objFront->$objK,
            new S ($objK), $objTp);
        }

        // Set
        self::setTp ($objFront,
        new S ('OBJ'), $objTp);

        // Execute
        self::exeTp ($objTp);
    }

    /**
     * Does footer requirements &/or actions;
     *
     * @author Catalin Alexandru Zamfir <catalin.zamfir@raphpframework.ro>
     * @copyright Under the terms of the GNU General Public License v3
     * @version $Id: Settings.php 1 2012-10-26 08:27:37Z root $
     */
    public static function doFooter (Frontend $objFront) {
        // HTTP-EQUIVs must be set
        self::manageEQV (new S ('Content-Type'), new S ('text/html; charset=UTF-8'));
        self::manageEQV (new S ('Content-Script-Type'), new S ('text/javascript'));
        self::manageEQV (new S ('Content-Style-Type'), new S ('text/css'));

        // Defaults cause we need'em
        self::manageTAG (new S ('copyright'), new S ('KIT Software CAZ SRL'));
        self::manageTAG (new S ('author'), new S ('Catalin Alexandru Zamfir'));
        self::manageTAG (new S ('generator'), new S ('RA PHP Framework'));
        self::manageTAg (new S ('robots'), new S ('index, follow'));
        self::manageTAG (new S ('revisit-after'), new S ('1 days'));

        // Traffic monitor
        if (self::getSettings ()
        ->getConfigKey (new S ('Have.A.Mint'))
        ->toLength ()->toInt () != 0) {
            // Set
            self::manageJSS (new
            Path (self::getSettings ()
            ->getConfigKey (new
            S ('Have.A.Mint')),
            FALSE));
        }

        // Yahoo
        if (self::getSettings ()
        ->getConfigKey (new
        S ('Yahoo.WT.Key'))->toLength ()
        ->toInt () != 0) {
            // Yahoo
            self::manageTAG (new S ('y_key'),
            self::getSettings ()
            ->getConfigKey (new
            S ('Yahoo.WT.Key')));
        }

        // Google
        if (self::getSettings ()
        ->getConfigKey (new
        S ('Google.WT.Key'))->toLength ()
        ->toInt () != 0) {
            // Google
            self::manageTAG (new S ('google-site-verification'),
            self::getSettings ()
            ->getConfigKey (new
            S ('Google.WT.Key')));
        }

        // Bing
        if (self::getSettings ()
        ->getConfigKey (new
        S ('Bing.WT.Key'))->toLength ()
        ->toInt () != 0) {
            // Bing
            self::manageTAG (new S ('msvalidate.01'),
            self::getSettings ()
            ->getConfigKey (new
            S ('Bing.WT.Key')));
        }

        // Check
        if (Frontend::DEFINED_SPECIFIC_CSS_INCLUDES == 0) {
            // Set
            self::manageCSS (new
            Path (Architecture::pathTo ($objFront->getPathToSkinCSS (),
            $objFront->getObjectAncestry () . CSS_EXTENSION)));
        } else {
            // Set
            $objFront->doSpecificCSSRequirements ();
        }

        // Set the execution time
        Execution::setExeTime (new S ('Finish'));

        // Footer
        $objFront
        ->renderFrontend (new
        S (_T ('Footer')));
    }

    /**
     * Does feed specific (RSS or XML Sitemap) actions;
     *
     * @author Catalin Alexandru Zamfir <catalin.zamfir@raphpframework.ro>
     * @copyright Under the terms of the GNU General Public License v3
     * @version $Id: Settings.php 1 2012-10-26 08:27:37Z root $
     */
    public static function doFeed (Frontend $objFront, $objT) {
        // Switch
        switch ($objT) {
            // RSS Feeds
            case _T ('RSS'):
                // Object
                $objRSS = self::getMarkup (new S ('RSS'));
                $objDTE = date (DATE_RFC822, time ());
                $objTTL = self::getSettings ()->getConfigKey (new S ('Application.Title'));
                $objXML = $objRSS->addCHILD (Settings::RSS_CHANNEL);
                $objXML->addCHILD (Settings::RSS_LINK, Architecture::getHost ());
                $objXML->addCHILD (Settings::RSS_PUBLISHED_DATE, $objDTE);
                $objXML->addCHILD (Settings::RSS_TITLE, $objTTL);
                $objXML->addCHILD (Settings::RSS_DESCRIPTION, $objTTL);

                // Go through
                foreach ($objFront->getDynamicData () as $objK => $objV) {
                    if ($objV instanceof Commons) {
                        $objFront->$objK->renderWidget (new S ('widgetRSS'),
                        new A (Array ('objXML' => $objXML)));
                    }
                }

                // Output
                self::outputString (new S ($objRSS->asXML ()));
                break;

                // XML Sitemap
            case _T ('XMLSitemap'):
                // Check
                if (isset ($_GET[_T ('Type')])) {
                    // Object
                    $objXML = self::getMarkup (new S ('MAP'));

                    // Go through
                    foreach ($objFront->getDynamicData () as $objK => $objV) {
                        // Specific project!
                        if ($objV instanceof Offers) {
                            $objFront->$objK->renderWidget (new S ('widgetXML'),
                            new A (Array ('objXML' => $objXML)));
                        }
                    }

                    // Output
                    self::outputString (new S ($objXML->asXML ()));
                } else {
                    // XML
                    $objXML = self::getMarkup (new S ('MAP'));

                    // Go through
                    foreach ($objFront->getDynamicData () as $objK => $objV) {
                        if ($objV instanceof Commons) {
                            $objFront->$objK->renderWidget (new S ('widgetXML'),
                            new A (Array ('objXML' => $objXML)));
                        }
                    }

                    // Output
                    self::outputString (new S ($objXML->asXML ()));
                }
                // BK;
                break;
        }
    }

    /**
     * (non-PHPdoc)
     * @see Commons::tieInAdministration()
     */
    public function tieInAdministration (Administration $objAdministration) {
        // Tie
        parent::tieInAdministration ($objAdministration);

        // Set ACLs;
        $objACL = $this->getACLs ();

        // Administration (menu)
        $objWP = new Path (Architecture
        ::pathTo ($this->getPathToAdmin (),
        $this->getConfigKey (new S ('Dashboard'))));
        self::getAdministration ()->setLink (_T ('Settings'), $objWP,
        $this->getHELP (_T ('Settings')));

        // ONLY: Settings.Do.Configuration
        if (self::getAuthentication ()
        ->checkCurrentUserZoneACL ($objACL[0])
        ->toBoolean () == TRUE) {
            $objMS = new Path (Architecture
            ::pathTo ($this->getPathToAdmin (),
            $this->getConfigKey (new S ('Settings'))));
            self::getAdministration ()->setSink (_T ('Settings'),
            $objMS, $this->getHELP (_T ('Settings')));
        }

        // ONLY: Countries
        if (self::getAuthentication ()
        ->checkCurrentUserZoneACL ($objACL[1])
        ->toBoolean () == TRUE) {
            $objMC = new Path (Architecture
            ::pathTo ($this->getPathToAdmin (),
            $this->getConfigKey (new S ('Countries'))));
            self::getAdministration ()->setSink (_T ('Countries'),
            $objMC, $this->getHELP (_T ('Countries')));
        }

        // ONLY: Cities
        if (self::getAuthentication ()
        ->checkCurrentUserZoneACL ($objACL[2])
        ->toBoolean () == TRUE) {
            $objMC = new Path (Architecture
            ::pathTo ($this->getPathToAdmin (),
            $this->getConfigKey (new S ('Cities'))));
            self::getAdministration ()->setSink (_T ('Cities'),
            $objMC, $this->getHELP (_T ('Cities')));
        }

        // ONLY: Error Pages
        if (self::getAuthentication ()
        ->checkCurrentUserZoneACL ($objACL[3])
        ->toBoolean () == TRUE) {
            $objMC = new Path (Architecture
            ::pathTo ($this->getPathToAdmin (),
            $this->getConfigKey (new S ('ErrorPages'))));
            self::getAdministration ()->setSink (_T ('Error Pages'),
            $objMC, $this->getHELP (_T ('Error Pages')));
        }

        // Error Pages
        $this->getAdministration ()->setWidget ($this
        ->getHELP (new S ('adminWidgetErrorPages'))
        ->doToken ('%uId', $this->getErrorPages ()
        ->doCount ()));

        // Cities
        $this->getAdministration ()->setWidget ($this
        ->getHELP (new S ('adminWidgetCountries'))
        ->doToken ('%uId', $this->getCountries ()
        ->doCount ()));

        // Check
        if (self
        ::checkIsRegistered (new S ('mod/codes'))
        ->toBoolean () == FALSE) {
            // Throw
            throw new RequiredModuleNotFoundException;
        }

        // Check
        if (self
        ::checkIsRegistered (new S ('mod/crons'))
        ->toBoolean () == FALSE) {
            // Throw
            throw new RequiredModuleNotFoundException;
        }

        // Check
        if (self
        ::checkIsRegistered (new S ('mod/texts'))
        ->toBoolean () == FALSE) {
            // Throw
            throw new RequiredModuleNotFoundException;
        }

        // Check
        if (self
        ::checkIsRegistered (new S ('mod/frontend'))
        ->toBoolean () == FALSE) {
            // Throw
            throw new RequiredModuleNotFoundException;
        }

        // Check
        if (self
        ::checkIsRegistered (new S ('mod/administration'))
        ->toBoolean () == FALSE) {
            // Throw
            throw new RequiredModuleNotFoundException;
        }

        // Check
        if (self
        ::checkIsRegistered (new S ('mod/authentication'))
        ->toBoolean () == FALSE) {
            // Throw
            throw new RequiredModuleNotFoundException;
        }

        // Auto-cron
        $objCron = _new ('Crons');

        // Cron: Check
        $objCron->setCron (new S ('System Check'),
        new S ('0 0 * * *'), _S (Architecture::getHost ())
        ->appendString ('/Cron/Go/Type/%tId/Execute/%cId')
        ->doToken ('%tId', $this->getObjectAncestry ())
        ->doToken ('%cId', new S ('Check')));

        // Cron: Backup
        $objCron->setCron (new S ('Backup'),
        new S ('0 0 * * *'), _S (Architecture::getHost ())
        ->appendString ('/Cron/Go/Type/%tId/Execute/%cId')
        ->doToken ('%tId', $this->getObjectAncestry ())
        ->doToken ('%cId', new S ('Backup')));
    }

    /**
     * (non-PHPdoc)
     * @see Commons::tieInFrontend()
     */
    public function tieInFrontend (Frontend $objFrontendObject) {
        // Do a CALL to the parent;
        parent::tieInFrontend ($objFrontendObject);

        // Services
        $this->tieInCustomServices ();
    }

    /**
     * Returns the current hostname (for JS);
     *
     * @author Catalin Alexandru Zamfir <catalin.zamfir@raphpframework.ro>
     * @copyright Under the terms of the GNU General Public License v3
     * @version $Id: Settings.php 1 2012-10-26 08:27:37Z root $
     */
    public function getHostname () {
        // Return
        self::outputString (Architecture
        ::pathTo (Architecture::getHost ())
        ->appendString (_WS));
    }

    /**
     * Returns the current root path (for JS);
     *
     * @author Catalin Alexandru Zamfir <catalin.zamfir@raphpframework.ro>
     * @copyright Under the terms of the GNU General Public License v3
     * @version $Id: Settings.php 1 2012-10-26 08:27:37Z root $
     */
    public function getRoot () {
        // Return
        self::outputString (new
        S (Architecture::getRoot ()));
    }

    /**
     * Returns the robots.txt as an URL path (rewritten);
     *
     * @author Catalin Alexandru Zamfir <catalin.zamfir@raphpframework.ro>
     * @copyright Under the terms of the GNU General Public License v3
     * @version $Id: Settings.php 1 2012-10-26 08:27:37Z root $
     */
    public function getRobots () {
        // Set type
        Header::setKey (new S (Header
        ::CONTENT_TYPE_TEXT_PLAIN),
        new S ('Content-Type'));

        // Set
        $objRobots = new S ('User-agent: *');

        // Return
        foreach (glob (Architecture
        ::pathTo (Architecture::getRoot (), '*'))
        as $objK => $objV) {
            // Check
            if (is_dir ($objV)) {
                // Set
                $objRobots->appendString (_N_)
                ->appendString ('Disallow: ')
                ->appendString (_S ($objV)
                ->doToken (Architecture::getRoot (), _NONE))
                ->appendString (_S);
            }
        }

        // Return
        return $objRobots;
    }

    /**
     * Ties in custom services for each extension;
     *
     * @author Catalin Alexandru Zamfir <catalin.zamfir@raphpframework.ro>
     * @copyright Under the terms of the GNU General Public License v3
     * @version $Id: Settings.php 1 2012-10-26 08:27:37Z root $
     */
    public function tieInCustomServices () {
        // Check
        if ($_GET
        ->offsetExists (_T ('Type'))) {
            // Go baby, go!
            _new ((string) $_GET[_T ('Type')])
            ->tieInServices ();
        }
    }

    /**
     * Checks given city name is unique;
     *
     * @author Catalin Alexandru Zamfir <catalin.zamfir@raphpframework.ro>
     * @copyright Under the terms of the GNU General Public License v3
     * @version $Id: Settings.php 1 2012-10-26 08:27:37Z root $
     */
    public function checkCityNameIsUnique (S $objCityName) {
        // Check
        if ($this->_Q (_QS ('doSELECT')
        ->doToken ('%what', new S ('%objCitiesName'))->doToken ('%table', self::$objCities)
        ->doToken ('%condition', new S ('WHERE %objCitiesName = "%Id"'))
        ->doToken ('%Id', $objCityName))->doCount ()->toInt () == 0) {
            // Do return
            return new B (TRUE);
        } else {
            // Do return
            return new B (FALSE);
        }
    }

    /**
     * Returns countries;
     *
     * @author Catalin Alexandru Zamfir <catalin.zamfir@raphpframework.ro>
     * @copyright Under the terms of the GNU General Public License v3
     * @version $Id: Settings.php 1 2012-10-26 08:27:37Z root $
     */
    public function getCountries (S $objSQLCondition = NULL) {
        // Do return
        return $this->_Q (_QS ('doSELECT')
        ->doToken ('%what', new S ('*'))->doToken ('%table', self::$objCountry)
        ->doToken ('%condition', $objSQLCondition));
    }

    /**
     * Returns cities;
     *
     * @author Catalin Alexandru Zamfir <catalin.zamfir@raphpframework.ro>
     * @copyright Under the terms of the GNU General Public License v3
     * @version $Id: Settings.php 1 2012-10-26 08:27:37Z root $
     */
    public function getCities (S $objSQLCondition = NULL) {
        // Do return
        return $this->_Q (_QS ('doSELECT')
        ->doToken ('%what', new S ('*'))->doToken ('%table', self::$objCities)
        ->doToken ('%condition', $objSQLCondition));
    }

    /**
     * Returns error pages;
     *
     * @author Catalin Alexandru Zamfir <catalin.zamfir@raphpframework.ro>
     * @copyright Under the terms of the GNU General Public License v3
     * @version $Id: Settings.php 1 2012-10-26 08:27:37Z root $
     */
    public function getErrorPages (S $objSQLCondition = NULL) {
        // Do return
        return $this->_Q (_QS ('doSELECT')
        ->doToken ('%what', new S ('*'))->doToken ('%table', self::$objErrors)
        ->doToken ('%condition', $objSQLCondition));
    }

    /**
     * Returns error page via its code;
     *
     * @author Catalin Alexandru Zamfir <catalin.zamfir@raphpframework.ro>
     * @copyright Under the terms of the GNU General Public License v3
     * @version $Id: Settings.php 1 2012-10-26 08:27:37Z root $
     */
    public function getErrorPageByCode (S $objErrorCode, S $objFieldToGet) {
        // Do return
        return $this->_Q (_QS ('doSELECT')
        ->doToken ('%what', $objFieldToGet)->doToken ('%table', self::$objErrors)
        ->doToken ('%condition', new S ('WHERE %objErrorsCode = "%Id" LIMIT 1'))
        ->doToken ('%Id', $objErrorCode))->offsetGet (0)->offsetGet ($objFieldToGet);
    }

    /**
     * Executes a given cron-job, via its URL;
     *
     * @author Catalin Alexandru Zamfir <catalin.zamfir@raphpframework.ro>
     * @copyright Under the terms of the GNU General Public License v3
     * @version $Id: Settings.php 1 2012-10-26 08:27:37Z root $
     */
    public function executeCronJob () {
        // Check
        if ($_GET
        ->offsetExists (_T ('Execute'))) {
            // Switch
            switch ($_GET->offsetGet (_T ('Execute'))) {
                // Timer
                case _T ('Timer'):
                    // Check
                    if ($this
                    ->getConfigKey (new S ('Cron.Timer'))
                    ->toLength ()->toInt () == 0) {
                        // Set
                        $this->setConfigKey (new S ('Cron.Timer'),
                        new S ((string) time ()));
                    }

                    // Check
                    if ($this
                    ->getConfigKey (new S ('Cron.State'))
                    ->toLength ()->toInt () == 0) {
                        // Set
                        $this->setConfigKey (new S ('Cron.State'),
                        new S ('Run'));
                    }

                    // Check
                    if (time () - (int) (string) $this
                    ->getConfigKey (new S ('Cron.Timer')) < 30) {
                        // Output
                        self::outputString (new S ('Stop'));
                    }

                    // Check
                    if ($this
                    ->getConfigKey (new
                    S ('Cron.State'),
                    new B (FALSE)) != _T ('Run')) {
                        // Set
                        $this->setConfigKey (new
                        S ('Cron.State'),
                        _T ('Run'));

                      // Output
                        self::outputString (new
                        S ('No Run'));
                    }

                    // Check
                    if (Mods::checkIsRegistered (new
                    S ('mod/crons'))->toBoolean () == TRUE) {
                        // Activate
                        $objCron = _new ('Crons');

                        // Set
                        while ($this
                        ->getConfigKey (new
                        S ('Cron.State'),
                        new B (FALSE)) == _T ('Run')) {
                            // Time
                            $objTime = time ();

                            // Get
                            foreach ($objCron
                            ->getItems () as $objCK => $objCV) {
                                // Tab fmt!
                                $objCronTab = Cron
                                ::getCronInstance ($objCV[Crons
                                ::$objItemUnix]);

                                // Check
                                if ($objCronTab->isDue ()) {
                                    // Check
                                    if (time () - $objCronTab
                                    ->getPreviousRunDate ()
                                    ->getTimestamp () <= 60) {
                                        // Check
                                        if (time () - (int) (string)
                                        $objCV[Crons::$objItemLastDueTime] > 60) {
                                            // Set
                                            $objCron->_Q (_QS ('doUPDATE')
                                            ->doToken ('%table', Crons::$objItem)
                                            ->doToken ('%condition', new S ('%objItemLastDueTime = "%dId"'))
                                            ->doToken ('%dId', time ()));

                                            // Run, as async CURL request to URL, ignore output
                                            Location::asyncCURLRequest ($objCV[Crons::$objItemURL]);
                                        }
                                    }
                                }

                                // Set
                                unset ($objCronTab);
                            }

                            // Set
                            $this->setConfigKey (new S ('Cron.Timer'),
                            new S ((string) $objTime));

                            // Set
                            USLEEP (10000000);
                        }
                    }
                    // BK;
                    break;

                case _T ('Check'):
                    // Set
                    self::setNotification (new S ('Cron'),
                    $this->getHELP (new S ('Check'))
                    ->doToken ('%dId', date ('d/m/Y H:i:s')),
                    new I (self::NOTIFICATION_TYPE_MESSAGE));

                    // Check
                    if (file_exists ($objPath = Architecture::pathTo (Architecture
                    ::getStorage (), LOG_DIR, PHP_ERROR_LOG))) {
                        // Erase
                        unlink ($objPath);
                        touch ($objPath);
                    }
                    // BK;
                    break;

                case _T ('Backup'):
                    // Erase
                    foreach (GLOB (Architecture
                    ::pathTo (Architecture::getStorage (),
                    BACKUP_DIR, '*')) as $objV) {
                        // Check
                        if (file_exists ($objV)) {
                            // Go
                            if (time () - filemtime ($objV) > 604800) {
                                // Erase
                                UNLINK ($objV);
                            }
                        }
                    }

                    // Save
                    $objCommand = _S ('tar -cvpzf')
                    ->appendString (_SP)->appendString (Architecture
                    ::pathTo (Architecture::getStorage (), BACKUP_DIR)
                    ->appendString (_S))->appendString ('backup-')
                    ->appendString (date ('d-m-Y-H-i-s'))->appendString ('.tar.gz')
                    ->appendString (_SP)->appendString (Architecture::getRoot ());

                    // Set
                    system ($objCommand);

                    // Notify
                    self::setNotification (new S ('Cron'),
                    $this->getHELP (new S ('Backup'))
                    ->doToken ('%dId', date ('d/m/Y H:i:s')),
                    new I (self::NOTIFICATION_TYPE_MESSAGE));
                    break;
            }
        }
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
            case 'manageCountries':
                // Work
                if (isset ($_GET[_T ('Do')])) {
                    // Switch
                    switch ($_GET[_T ('Do')]) {
                        // Add
                        case _T ('Add'):
                            $this->renderForm (new
                            S ('countryCreate'));
                            break;

                        // Edit
                        case _T ('Edit'):
                            $this->renderForm (new
                            S ('countryEdit'));
                            break;

                        // Erase
                        case _T ('Erase'):
                            $this->renderForm (new
                            S ('countryErase'));
                            break;
                    }
                } else {
                    // Condition
                    $objCondition = new S;

                    // Backmapping
                    self::$objItem = self::$objCountry;
                    self::$objItemId = self::$objCountryIso;
                    self::$objItemTitle = self::$objCountryPrnt;

                    // Maps
                    $objMaps = new A (Array (self::$objCountryIso,
                    self::$objCountryPrnt,
                    self::$objCountryIsoT,
                    self::$objCountryCode));

                    // Output
                    $this->outputAsJson (self::$objCountry,
                    $objCondition, $objMaps);

                    // Go
                    self::mapTp ($this, $objP,
                    _S (__FUNCTION__));
                }
                // BK;
                break;

            case 'manageCities':
                // Work
                if (isset ($_GET[_T ('Do')])) {
                    // Switch
                    switch ($_GET[_T ('Do')]) {
                        // Add
                        case _T ('Add'):
                            $this->renderForm (new
                            S ('cityCreate'));
                            break;

                        // Edit
                        case _T ('Edit'):
                            $this->renderForm (new
                            S ('cityEdit'));
                            break;

                        // Erase
                        case _T ('Erase'):
                            $this->renderForm (new
                            S ('cityErase'));
                            break;
                    }
                } else {
                    // Condition
                    $objCondition = new S ('AS t1 INNER JOIN %objCountry AS t2
                    ON t1.%objCitiesCIso = t2.%objCountryIso');

                    // Backmapping
                    self::$objItem = self::$objCities;
                    self::$objItemId = self::$objCitiesId;
                    self::$objItemTitle = self::$objCitiesName;

                    // Maps
                    $objMaps = new A (Array (self::$objCitiesId,
                    self::$objCitiesName->makeCopyObject ()
                    ->prependString (_DOT)->prependString ('t1'),
                    self::$objCountryPrnt));

                    // Output
                    $this->outputAsJson (self::$objCities,
                    $objCondition, $objMaps);

                    // Go
                    self::mapTp ($this, $objP,
                    _S (__FUNCTION__));
                }
                // BK;
                break;

            case 'manageErrorPages':
                // Work
                if (isset ($_GET[_T ('Do')])) {
                    // Switch
                    switch ($_GET[_T ('Do')]) {
                        // Add
                        case _T ('Add'):
                            $this->renderForm (new
                            S ('errorPageCreate'));
                            break;

                        // Edit
                        case _T ('Edit'):
                            $this->renderForm (new
                            S ('errorPageEdit'));
                            break;

                        // Erase
                        case _T ('Erase'):
                            $this->renderForm (new
                            S ('errorPageErase'));
                            break;
                    }
                } else {
                    // Condition
                    $objCondition = new S;

                    // Backmapping
                    self::$objItem = self::$objErrors;
                    self::$objItemId = self::$objErrorsId;
                    self::$objItemTitle = self::$objErrorsTitle;

                    // Maps
                    $objMaps = new A (Array (self::$objErrorsId,
                    self::$objErrorsCode,
                    self::$objErrorsTitle));

                    // Output
                    $this->outputAsJson (self::$objErrors,
                    $objCondition, $objMaps);

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
     * @see Commons::renderForm()
     */
    public function renderForm (S $objF, A $objFA = NULL) {
        // Make them defaults
        if ($objFA == NULL) $objFA = new A;

        // Switch
        switch ($objF) {
            case 'citySuggest':
                // Ajax
                if ($_GET
                ->offsetExists ('Ajax')) {
                    // Compatiblity
                    Location::doConvert ();

                    // Set
                    $objAJAX = new S;

                    // Switch
                    switch ($_GET
                    ->offsetGet ('Ajax')) {
                        case 'Suggest':
                            // Foreach
                            foreach ($objR = $this->getCities (_S ('WHERE %objCitiesName
                            LIKE "%Id%" LIMIT 0, 100')->doToken ('%Id', $_GET['Q'])) as $objK => $objV) {
                                // Append
                                $objAJAX->appendString ($objV[self::$objCitiesName] . '|' .
                                $objV[self::$objCitiesName] . _N_);
                            }
                            // BK;
                            break;
                    }

                    // Go
                    self::outputString ($objAJAX);
                }
                // BK;
                break;

            case 'countryCreate':
                // The URL to go back too
                $objURLToGoBack = Location::rewriteTo (new A (Array (_T ('Do'))));

                // Form
                $this->setFieldset (_T ('Add'))
                ->setTableName (self::$objCountry);

                // Check
                if ($this->checkPOST ()->toBoolean () == TRUE) {
                    // Set
                    $this->setUpdateId ($this
                    ->getPOST (self::$objCountryIso))
                    ->setExtraUpdateData (self::$objCountryName,
                    $this->getPOST (self::$objCountryPrnt)
                    ->toUpper ());
                }

                // Continue
                $this->setUpdateField (self::$objCountryIso)
                ->setName ($objF)
                ->setRedirect ($objURLToGoBack)
                ->setInputType (new S ('text'))
                ->setName (self::$objCountryIso)
                ->setInputInfo ($this->getHELP ($objF))
                ->setLabel (_T ('ISO'))
                ->setInputType (new S ('text'))
                ->setName (self::$objCountryPrnt)
                ->setLabel (_T ('Country'))
                ->setInputType (new S ('text'))
                ->setName (self::$objCountryIsoT)
                ->setJSRegExpReplace (new S ('[^A-Z]'))
                ->setLabel (_T ('ISO 3'))
                ->setInputType (new S ('text'))
                ->setName (self::$objCountryCode)
                ->setJSRegExpReplace (new S ('[^0-9]'))
                ->setLabel (_T ('Code'))
                ->setInputType (new S ('submit'))
                ->setValue (_T ('Add'))
                ->setAccessKey (new S ('S'))
                ->setFormEndAndExecute (new B (TRUE));
                break;

            case 'countryEdit':
                // The URL to go back too
                $objURLToGoBack = Location::rewriteTo (new A (Array (_T ('Do'), _T ('Id'))));

                // Form
                $this->setFieldset (_T ('Edit'))
                ->setTableName (self::$objCountry)
                ->setUpdateId ($_GET[_T ('Id')]);

                // Check
                if ($this->checkPOST ()->toBoolean () == TRUE) {
                    // Set
                    $objCountryName = $this->getPOST (self::$objCountryPrnt)->makeCopyObject ();
                    $this->setExtraUpdateData (self::$objCountryName, $objCountryName->toUpper ());
                }

                // Continue;
                $this->setUpdateField (self::$objCountryIso)
                ->setName ($objF)
                ->setRedirect ($objURLToGoBack)
                ->setInputType (new S ('text'))
                ->setName (self::$objCountryPrnt)
                ->setInputInfo ($this->getHELP ($objF))
                ->setLabel (_T ('Name'))
                ->setInputType (new S ('text'))
                ->setName (self::$objCountryIsoT)
                ->setJSRegExpReplace (new S ('[^A-Z]'))
                ->setLabel (_T ('ISO 3'))
                ->setInputType (new S ('text'))
                ->setName (self::$objCountryCode)
                ->setJSRegExpReplace (new S ('[^0-9]'))
                ->setLabel (_T ('Code'))
                ->setInputType (new S ('submit'))
                ->setValue (_T ('Edit'))
                ->setAccessKey (new S ('S'))
                ->setFormEndAndExecute (new B (TRUE));
                break;

            case 'countryErase':
                // The URL to go back too
                $objURLToGoBack = Location::rewriteTo (new A (Array (_T ('Do'), _T ('Id'))));

                // Requirements
                $objSQLConditionForUsers = new S ('WHERE %objUserCountry = "%cId"');

                if (self::getAuthentication ()
                ->getUserCount ($objSQLConditionForUsers
                ->doToken ('%cId', $_GET[_T ('Id')]))->toInt () == 0) {
                    // Erase
                    $this->_Q (_QS ('doDELETE')
                    ->doToken ('%table', self::$objCountry)
                    ->doToken ('%condition', new S ('%objCountryIso = "%sId"'))
                    ->doToken ('%sId', $_GET[_T ('Id')]));

                    // Redirect
                    Header::setKey ($objURLToGoBack, new S ('Location'));
                } else {
                    // Errors
                    self::getAdministration ()
                    ->setErrorMessage (_T ('Cannot erase country! Items are mapped to it!'),
                    $objURLToGoBack);
                }
                // BK;
                break;

            case 'cityCreate':
                // The URL to go back too
                $objURLToGoBack = Location::rewriteTo (new A (Array (_T ('Do'))));

                // Form
                $this->setFieldset (_T ('Add'))
                ->setTableName (self::$objCities)
                ->setUpdateField (self::$objCitiesId)
                ->setName ($objF)
                ->setRedirect ($objURLToGoBack)
                ->setInputType (new S ('select'))
                ->setInputInfo ($this->getHELP ($objF))
                ->setName (self::$objCitiesCIso)
                ->setLabel (_T ('Country'));

                // Countries
                foreach ($this
                ->getCountries ()
                as $objK => $objV) {
                    // Each
                    $this->setInputType (new S ('option'))
                    ->setName  ($objV[self::$objCountryIso])
                    ->setValue ($objV[self::$objCountryIso])
                    ->setLabel ($objV[self::$objCountryPrnt]);
                }

                // Continue
                $this->setInputType (new S ('text'))
                ->setName (self::$objCitiesName)
                ->setLabel (_T ('City'))
                ->setCheckForEmpty (new B (TRUE))
                ->setCheckForUnique (new B (TRUE))
                ->setInputType (new S ('submit'))
                ->setValue (_T ('Add'))
                ->setAccessKey (new S ('S'))
                ->setFormEndAndExecute (new B (TRUE));
                break;

            case 'cityEdit':
                // The URL to go back too
                $objURLToGoBack = Location::rewriteTo (new A (Array (_T ('Do'), _T ('Id'))));

                // Form
                $this->setFieldset (_T ('Edit'))
                ->setTableName (self::$objCities)
                ->setUpdateId ($_GET[_T ('Id')])
                ->setUpdateField (self::$objCitiesId)
                ->setName ($objF)
                ->setRedirect ($objURLToGoBack)
                ->setInputType (new S ('select'))
                ->setName (self::$objCitiesCIso)
                ->setInputInfo ($this->getHELP ($objF))
                ->setLabel (_T ('Country'));

                // Countries
                foreach ($this
                ->getCountries ()
                as $objK => $objV) {
                    // Each
                    $this->setInputType (new S ('option'))
                    ->setName  ($objV[self::$objCountryIso])
                    ->setValue ($objV[self::$objCountryIso])
                    ->setLabel ($objV[self::$objCountryPrnt]);
                }

                // Continue
                $this->setInputType (new S ('text'))
                ->setName (self::$objCitiesName)
                ->setLabel (_T ('City'))
                ->setCheckForEmpty (new B (TRUE))
                ->setCheckForUnique (new B (TRUE))
                ->setInputType (new S ('submit'))
                ->setValue (_T ('Edit'))
                ->setAccessKey (new S ('S'))
                ->setFormEndAndExecute (new B (TRUE));
                break;

            case 'cityErase':
                // The URL to go back too
                $objURLToGoBack = Location::rewriteTo (new A (Array (_T ('Do'), _T ('Id'))));

                $this->_Q (_QS ('doDELETE')
                ->doToken ('%table', self::$objCities)
                ->doToken ('%condition', new S ('%objCitiesId = "%Id"'))
                ->doToken ('%Id', $_GET[_T ('Id')]));

                // Redirect;
                Header::setKey ($objURLToGoBack, new S ('Location'));
                break;

            case 'errorPageCreate':
                // The URL to go back too
                $objURLToGoBack = Location::rewriteTo (new A (Array (_T ('Do'))));

                // Form
                $this->setFieldset (_T ('Add'))
                ->setTableName (self::$objErrors)
                ->setUpdateField (self::$objErrorsId)
                ->setName ($objF)
                ->setRedirect ($objURLToGoBack)
                ->setInputType (new S ('text'))
                ->setInputInfo ($this->getHELP ($objF))
                ->setName (self::$objErrorsCode)
                ->setLabel (_T ('Code'))
                ->setJSRegExpReplace (new S ('[^0-9]'))
                ->setCheckForEmpty (new B (TRUE))
                ->setCheckForUnique (new B (TRUE))
                ->setInputType (new S ('text'))
                ->setName (self::$objErrorsTitle)
                ->setLabel (_T ('Title'))
                ->setCheckForEmpty (new B (TRUE))
                ->setCheckForUnique (new B (TRUE))
                ->setInputType (new S ('textarea'))
                ->setName (self::$objErrorsContent)
                ->setLabel (_T ('Content'))
                ->setTinyMCETextarea (new B (TRUE))
                ->setCheckForEmpty (new B (TRUE))
                ->setInputType (new S ('submit'))
                ->setValue (_T ('Add'))
                ->setAccessKey (new S ('S'))
                ->setFormEndAndExecute (new B (TRUE));
                break;

            case 'errorPageEdit':
                // The URL to go back too
                $objURLToGoBack = Location::rewriteTo (new A (Array (_T ('Do'), _T ('Id'))));

                // Form
                $this->setFieldset (_T ('Edit'))
                ->setTableName (self::$objErrors)
                ->setUpdateId ($_GET[_T ('Id')])
                ->setUpdateField (self::$objErrorsId)
                ->setName ($objF)
                ->setRedirect ($objURLToGoBack)
                ->setInputType (new S ('text'))
                ->setName (self::$objErrorsCode)
                ->setInputInfo ($this->getHELP ($objF))
                ->setLabel (_T ('Code'))
                ->setJSRegExpReplace (new S ('[^0-9]'))
                ->setCheckForEmpty (new B (TRUE))
                ->setCheckForUnique (new B (TRUE))
                ->setInputType (new S ('text'))
                ->setName (self::$objErrorsTitle)
                ->setLabel (_T ('Title'))
                ->setCheckForEmpty (new B (TRUE))
                ->setCheckForUnique (new B (TRUE))
                ->setInputType (new S ('textarea'))
                ->setName (self::$objErrorsContent)
                ->setLabel (_T ('Content'))
                ->setTinyMCETextarea (new B (TRUE))
                ->setCheckForEmpty (new B (TRUE))
                ->setInputType (new S ('submit'))
                ->setValue (_T ('Edit'))
                ->setAccessKey (new S ('S'))
                ->setFormEndAndExecute (new B (TRUE));
                break;

            case 'errorPageErase':
                // The URL to go back too
                $objURLToGoBack = Location::rewriteTo (new A (Array (_T ('Do'), _T ('Id'))));

                // Erase
                $this->_Q (_QS ('doDELETE')
                ->doToken ('%table', self::$objErrors)
                ->doToken ('%condition', new S ('%objErrorsId = "%sId"'))
                ->doToken ('%sId', $_GET[_T ('Id')]));

                // Redirect;
                Header::setKey ($objURLToGoBack, new S ('Location'));
                break;

            case 'Administration.Title':
                // The URL to go back too
                $objURLToGoBack = Location::rewriteTo ();

                // Work
                if ($this->checkPOST ()
                ->toBoolean () == TRUE) {
                    // Go
                    foreach ($this->getPOST () as $objK => $objV) {
                        // Switch
                        switch (_S ($objK)->doToken (_U, _DOT)) {
                            // Needs tie-in with
                            case 'Header':
                                self::getAdministration ()
                                ->setConfigKey (_S ($objK)
                                ->doToken (_U, _DOT), $objV);
                                break;

                            default:
                                // Defaults
                                $this->setConfigKey (_S ($objK)
                                ->doToken (_U, _DOT), $objV);
                                break;
                        }
                    }
                }

                // Form
                $this->setFieldset (_T ('Configuration'))
                ->setName ($objF)
                ->setRedirect ($objURLToGoBack)
                ->setInputType (new S ('text'))
                ->setInputInfo ($this->getHELP ($objF))
                ->setName (new S ('Header'))
                ->setLabel (_T ('Choose'))
                ->setValue (self::getAdministration ()
                ->getConfigKey (new S ('Header')))
                ->setInputType (new S ('submit'))
                ->setValue (_T ('Update'))
                ->setAccessKey (new S ('S'))
                ->setFormEndAndExecute (new B (TRUE));
                break;

            case 'Application.Title':
                // The URL to go back too
                $objURLToGoBack = Location::rewriteTo ();

                // Form
                $this->setFieldset (_T ('Configuration'))
                ->setName ($objF)
                ->setRedirect ($objURLToGoBack)
                ->setInputType (new S ('text'))
                ->setInputInfo ($this->getHELP ($objF))
                ->setName (new S ('Application.Title'))
                ->setLabel (_T ('Choose'))
                ->setValue ($this->getConfigKey (new S ('Application.Title')))
                ->setInputType (new S ('submit'))
                ->setValue (_T ('Update'))
                ->setAccessKey (new S ('S'))
                ->setFormEndAndExecute (new B (TRUE));
                break;

            case 'Notification':
                // The URL to go back too
                $objURLToGoBack = Location::rewriteTo ();

                // Form
                $this->setFieldset (_T ('Configuration'))
                ->setName ($objF)
                ->setRedirect ($objURLToGoBack)
                ->setInputType (new S ('text'))
                ->setInputInfo ($this->getHELP ($objF))
                ->setName (new S ('Notification'))
                ->setLabel (_T ('Choose'))
                ->setValue ($this->getConfigKey (new S ('Notification')))
                ->setInputType (new S ('submit'))
                ->setValue (_T ('Update'))
                ->setAccessKey (new S ('S'))
                ->setFormEndAndExecute (new B (TRUE));
                break;

            case 'Default.Date.Format':
                // The URL to go back too
                $objURLToGoBack = Location::rewriteTo ();

                // Get the date format used for now
                $objDateFormat = $this->getConfigKey (new S ('Default.Date.Format'));

                // Form
                $this->setFieldset (_T ('Configuration'))
                ->setName ($objF)
                ->setRedirect ($objURLToGoBack)
                ->setInputType (new S ('select'))
                ->setName (new S ('Default.Date.Format'))
                ->setInputInfo ($this->getHELP ($objF))
                ->setLabel (_T ('Choose'))
                ->setInputType (new S ('option'))
                ->setName (new S ('date_format_1'))
                ->setLabel (new S (date ('M j, Y, H:i:s')))
                ->setValue (new S ('M j, Y, H:i:s'))
                ->setSelected (new B ($objDateFormat ==
                new S ('M j, Y, H:i:s') ? TRUE : FALSE))
                ->setInputType (new S ('option'))
                ->setName (new S ('date_format_2'))
                ->setLabel (new S (date ('D M j G:i:s T Y')))
                ->setValue (new S ('D M j G:i:s T Y'))
                ->setSelected (new B ($objDateFormat ==
                new S ('D M j G:i:s T Y') ? TRUE : FALSE))
                ->setInputType (new S ('option'))
                ->setName (new S ('date_format_3'))
                ->setLabel (new S (date ('r')))
                ->setValue (new S ('r'))
                ->setSelected (new B ($objDateFormat ==
                new S ('r') ? TRUE : FALSE))
                ->setInputType (new S ('option'))
                ->setName (new S ('date_format_4'))
                ->setLabel (new S (date ('d-m-Y H:i:s')))
                ->setValue (new S ('d-m-Y H:i:s'))
                ->setSelected (new B ($objDateFormat ==
                new S ('d-m-Y H:i:s') ? TRUE : FALSE))
                ->setInputType (new S ('option'))
                ->setName (new S ('date_format_5'))
                ->setLabel (new S (date ('Y-m-d H:i:s')))
                ->setValue (new S ('Y-m-d H:i:s'))
                ->setSelected (new B ($objDateFormat ==
                new S ('Y-m-d H:i:s') ? TRUE : FALSE))
                ->setInputType (new S ('submit'))
                ->setValue (_T ('Update'))
                ->setAccessKey (new S ('S'))
                ->setFormEndAndExecute (new B (TRUE));
                break;

            case 'Have.A.Mint':
                // The URL to go back too
                $objURLToGoBack = Location::rewriteTo ();

                // Form
                $this->setFieldset (_T ('Configuration'))
                ->setName ($objF)
                ->setRedirect ($objURLToGoBack)
                ->setInputType (new S ('text'))
                ->setInputInfo ($this->getHELP ($objF))
                ->setName (new S ('Have.A.Mint'))
                ->setLabel (_T ('Choose'))
                ->setValue ($this->getConfigKey (new S ('Have.A.Mint')))
                ->setInputType (new S ('submit'))
                ->setValue (_T ('Update'))
                ->setAccessKey (new S ('S'))
                ->setFormEndAndExecute (new B (TRUE));
                break;

            case 'Yahoo.WT.Key':
                // The URL to go back too
                $objURLToGoBack = Location::rewriteTo ();

                // Form
                $this->setFieldset (_T ('Configuration'))
                ->setName ($objF)
                ->setRedirect ($objURLToGoBack)
                ->setInputType (new S ('text'))
                ->setInputInfo ($this->getHELP ($objF))
                ->setName (new S ('Yahoo.WT.Key'))
                ->setLabel (_T ('Choose'))
                ->setValue ($this->getConfigKey (new S ('Yahoo.WT.Key')))
                ->setInputType (new S ('submit'))
                ->setValue (_T ('Update'))
                ->setAccessKey (new S ('S'))
                ->setFormEndAndExecute (new B (TRUE));
                break;

            case 'Google.WT.Key':
                // The URL to go back too
                $objURLToGoBack = Location::rewriteTo ();

                // Form
                $this->setFieldset (_T ('Configuration'))
                ->setName ($objF)
                ->setRedirect ($objURLToGoBack)
                ->setInputType (new S ('text'))
                ->setInputInfo ($this->getHELP ($objF))
                ->setName (new S ('Google.WT.Key'))
                ->setLabel (_T ('Choose'))
                ->setValue ($this->getConfigKey (new S ('Google.WT.Key')))
                ->setInputType (new S ('submit'))
                ->setValue (_T ('Update'))
                ->setAccessKey (new S ('S'))
                ->setFormEndAndExecute (new B (TRUE));
                break;

            case 'Bing.WT.Key':
                // The URL to go back too
                $objURLToGoBack = Location::rewriteTo ();

                // Form
                $this->setFieldset (_T ('Configuration'))
                ->setName ($objF)
                ->setRedirect ($objURLToGoBack)
                ->setInputType (new S ('text'))
                ->setInputInfo ($this->getHELP ($objF))
                ->setName (new S ('Bing.WT.Key'))
                ->setLabel (_T ('Choose'))
                ->setValue ($this->getConfigKey (new S ('Bing.WT.Key')))
                ->setInputType (new S ('submit'))
                ->setValue (_T ('Update'))
                ->setAccessKey (new S ('S'))
                ->setFormEndAndExecute (new B (TRUE));
                break;

            case 'GeCAD.Key':
                // The URL to go back too
                $objURLToGoBack = Location::rewriteTo ();

                // Form
                $this->setFieldset (_T ('Configuration'))
                ->setName ($objF)
                ->setRedirect ($objURLToGoBack)
                ->setInputType (new S ('text'))
                ->setInputInfo ($this->getHELP ($objF))
                ->setName (new S ('GeCAD.Key'))
                ->setLabel (_T ('Choose'))
                ->setValue ($this->getConfigKey (new S ('GeCAD.Key')))
                ->setInputType (new S ('submit'))
                ->setValue (_T ('Update'))
                ->setAccessKey (new S ('S'))
                ->setFormEndAndExecute (new B (TRUE));
                break;

            case 'GeCAD.Merchant':
                // The URL to go back too
                $objURLToGoBack = Location::rewriteTo ();

                // Form
                $this->setFieldset (_T ('Configuration'))
                ->setName ($objF)
                ->setRedirect ($objURLToGoBack)
                ->setInputType (new S ('text'))
                ->setInputInfo ($this->getHELP ($objF))
                ->setName (new S ('GeCAD.Merchant'))
                ->setLabel (_T ('Choose'))
                ->setValue ($this->getConfigKey (new S ('GeCAD.Merchant')))
                ->setInputType (new S ('submit'))
                ->setValue (_T ('Update'))
                ->setAccessKey (new S ('S'))
                ->setFormEndAndExecute (new B (TRUE));
                break;

            case 'GeCAD.CryptKey':
                // The URL to go back too
                $objURLToGoBack = Location::rewriteTo ();

                // Form
                $this->setFieldset (_T ('Configuration'))
                ->setName ($objF)
                ->setRedirect ($objURLToGoBack)
                ->setInputType (new S ('text'))
                ->setInputInfo ($this->getHELP ($objF))
                ->setName (new S ('GeCAD.CryptKey'))
                ->setLabel (_T ('Choose'))
                ->setValue ($this->getConfigKey (new S ('GeCAD.CryptKey')))
                ->setInputType (new S ('submit'))
                ->setValue (_T ('Update'))
                ->setAccessKey (new S ('S'))
                ->setFormEndAndExecute (new B (TRUE));
                break;

            case 'SSH - In A Box - URL':
                // The URL to go back too
                $objURLToGoBack = Location::rewriteTo ();

                // Form
                $this->setFieldset (_T ('Configuration'))
                ->setName ($objF)
                ->setRedirect ($objURLToGoBack)
                ->setInputType (new S ('text'))
                ->setInputInfo ($this->getHELP ($objF))
                ->setName (new S ('Ssh.InABox'))
                ->setLabel (_T ('Choose'))
                ->setValue ($this->getConfigKey (new S ('Ssh.InABox')))
                ->setInputType (new S ('submit'))
                ->setValue (_T ('Update'))
                ->setAccessKey (new S ('S'))
                ->setFormEndAndExecute (new B (TRUE));
                break;
        }
    }
}
?>
