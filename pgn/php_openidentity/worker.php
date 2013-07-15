<?php
    /**
     * We require LightOpenID, as the lightweight object that does the exchange for us with other OP providers. By default, we
     * require the return of an address (email) upon which we generate the username and temporary register the user, while confirming
     * his address. For that, because of the limiting .htaccess here (to allow code compatibility) we manually require it's file.
     */
    require_once 'openidentity.php';

    /**
     * We build the object from the LightOpenID class. We receive our parameters from the "Authentication" object of our framework. We
     * are going to redirect back to the framework, depending on given user actions.
     */
    $objOpenIdentity = new LightOpenID;

    // Default
    $objHeaderString = 'Location: ../../Type/Authentication/Method/OpenIdentityError';

    // Check
    if (!($objOpenIdentity->mode)) {
        // Where are we
        $openid->realm = (!empty($_SERVER['HTTPS']) ?
        'https' : 'http') . '://' . $_SERVER['HTTP_HOST'];

        // Requirements
        $objOpenIdentity->required =
        Array ('contact/email');

        // Options
        $objOpenIdentity->optional =
        Array ('namePerson/friendly',
        'namePerson',
        'birthDate',
        'person/gender',
        'contact/postalCode/home',
        'contact/country/home',
        'pref/lanuage',
        'pref/timezone');

        // Check
        if (isset ($_GET['oauth_URL'])) {
            // Set
            $objOpenIdentity
            ->identity = $_GET['oauth_URL'];

            // Go
            header ('Location: ' .
            $objOpenIdentity->authURL ());
        }
    } else if ($objOpenIdentity->mode == 'cancel') {
        // Redirect
        header ($objHeaderString);
    } else {
        // Check
        if ($objOpenIdentity->validate ()) {
            // Get
            $objAttributes =
            $objOpenIdentity
            ->getAttributes();

            // Set
            $objHeaderString = 'Location: ../../Type/Authentication/Method/OpenIdentitySucces';

            // T/R
            $objT[] = '_';
            $objR[] = '-U-';
            $objT[] = '/';
            $objR[] = '_';
            $objT[] = '@';
            $objR[] = '-AT-';
            $objT[] = '.';
            $objR[] = '-DOT-';

            // Check
            foreach ($objAttributes as $objK => $objV) {
                // Append
                $objHeaderString .= '/' .
                str_replace ($objT, $objR, $objK) . '/' .
                str_replace ($objT, $objR,  $objV);
            }

            // Redirect
            header ($objHeaderString);
        } else {
            // Redirect
            header ($objHeaderString);
        }
    }
?>
