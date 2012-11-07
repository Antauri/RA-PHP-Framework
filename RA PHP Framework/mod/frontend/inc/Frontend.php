<?php
/*
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
 * Provides a frontend that either redirects to common (Settings) URL routes or project-specific ones;
 *
 * @author Catalin Alexandru Zamfir <catalin.zamfir@raphpframework.ro>
 * @copyright Under the terms of the GNU General Public License v3
 * @version $Id: Frontend.php 1 2012-10-26 08:27:37Z root $
 */
class Frontend extends Commons {

    /* CONSTANTS */
    const DEFINED_SPECIFIC_JSS_INCLUDES = 0;
    const DEFINED_SPECIFIC_CSS_INCLUDES = 0;

    /* CONSTRUCT */
    public function __construct () {
        // Commons
        $this->tieInConfiguration ();

        // Requirements
        $this->objIMG = $this->getPathToSkinIMG ();
        $this->objJSS = $this->getPathToSkinJSS ();
        $this->objCSS = $this->getPathToSkinCSS ();

        // Go
        $this->mapTo ();
    }

    /**
     * Maps a given URL path to some specific actions;
     *
     * @author Catalin Alexandru Zamfir <catalin.zamfir@raphpframework.ro>
     * @copyright Under the terms of the GNU General Public License v3
     * @version $Id: Frontend.php 1 2012-10-26 08:27:37Z root $
     */
    private function mapTo () {
        // Pre-routes
        Settings::doPreRouting ($this);

        // Check
        if ($_GET
        ->offsetExists (_T ('Information'))) {
            // Check
            if ($_GET
            ->offsetGet (_T ('Admin')) != _T ('Go')) {
                // Output
                phpinfo ();
            }
        } else if ($_GET
        ->offsetExists (_T ('Admin'))) {
            // Check
            if ($_GET
            ->offsetGet (_T ('Admin')) != _T ('Go')) {
                // Redirect
                Header::setKey (Location::rewriteTo (new A (Array (_T ('Admin'))),
                new A (Array (_T ('Go')))), new S ('Location'));
            } else {
                // Go
                _new ('Administration');
            }
        } else if ($_GET
        ->offsetExists (_T ('Cron'))) {
            // Check
            if ($_GET
            ->offsetGet (_T ('Cron')) != _T ('Go')) {
                // Redirect
                Header::setKey (Location::rewriteTo (new A (Array (_T ('Cron'))),
                new A (Array (_T ('Go')))), new S ('Location'));
            } else {
                // Go
                $this->executeCronJob ();
            }
        } else {
            // Route me baby
            Settings::doRequirements ($this);
            Settings::doCommonRouting ($this);
            Settings::doFooter ($this);

            // Requirements
            $this->doCustomURLRouters ();
            $this->changeStreamTokens ();
        }
    }

    /**
     * Renders frontend, via a type;
     *
     * @author Catalin Alexandru Zamfir <catalin.zamfir@raphpframework.ro>
     * @copyright Under the terms of the GNU General Public License v3
     * @version $Id: Frontend.php 1 2012-10-26 08:27:37Z root $
     */
    public function renderFrontend (S $objT) {
        // Settings
        Settings::doFeed ($this, $objT);
        Settings::doRenderRoute ($this, $objT);
    }

    /**
     * Specific stream tokens S/R (search & rep);
     *
     * @author Catalin Alexandru Zamfir <catalin.zamfir@raphpframework.ro>
     * @copyright Under the terms of the GNU General Public License v3
     * @version $Id: Frontend.php 1 2012-10-26 08:27:37Z root $
     */
    private function changeStreamTokens () {
        // Changes
        self::manageTAG (_S ("og:image"), _S (Architecture::getHost () .
		"/mod/frontend/skn/default/img/others/Facebook.jpg"));
    }

    /**
     * Custom, project-specific URL routers;
     *
     * @author Catalin Alexandru Zamfir <catalin.zamfir@raphpframework.ro>
     * @copyright Under the terms of the GNU General Public License v3
     * @version $Id: Frontend.php 1 2012-10-26 08:27:37Z root $
     */
    private function doCustomURLRouters () {
        // Custom
    }

    /**
     * Executes a cron job, redirected to the proper extension, via URL;
     *
     * @author Catalin Alexandru Zamfir <catalin.zamfir@raphpframework.ro>
     * @copyright Under the terms of the GNU General Public License v3
     * @version $Id: Frontend.php 1 2012-10-26 08:27:37Z root $
     */
    public function executeCronJob () {
        // Execute
        if ($_GET
        ->offsetExists (_T ('Type'))) {
            // Check
            if ($_GET->offsetGet (_T ('Type')) == __CLASS__) {
                // Go baby, go!
                $this->executeCustomCronJob ();
            } else {
                // Go baby, go!
                _new ($_GET[_T ('Type')])
                ->executeCronJob ();
            }
        }
    }

    /**
     * Executes a custom cron-job, project-specific;
     *
     * @author Catalin Alexandru Zamfir <catalin.zamfir@raphpframework.ro>
     * @copyright Under the terms of the GNU General Public License v3
     * @version $Id: Frontend.php 1 2012-10-26 08:27:37Z root $
     */
    public function executeCustomCronJob () {
        // Custom
    }
}
?>
