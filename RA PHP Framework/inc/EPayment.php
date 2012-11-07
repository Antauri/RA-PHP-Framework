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
 * Provides support for CC processing via GeCAD's ePayment system;
 *
 * @author Catalin Alexandru Zamfir <catalin.zamfir@raphpframework.ro>
 * @copyright Under the terms of the GNU General Public License v3
 * @version $Id: EPayment.php 1 2012-10-26 08:27:37Z root $
 */
final class EPayment {
    /**
     * Container of the Live processing URL, for payments processing;
     *
     * @author Catalin Alexandru Zamfir <catalin.zamfir@raphpframework.ro>
     * @copyright Under the terms of the GNU General Public License v3
     * @version $Id: EPayment.php 1 2012-10-26 08:27:37Z root $
     */
    private $objLive = NULL;

    /**
     * Container of product names;
     *
     * @author Catalin Alexandru Zamfir <catalin.zamfir@raphpframework.ro>
     * @copyright Under the terms of the GNU General Public License v3
     * @version $Id: EPayment.php 1 2012-10-26 08:27:37Z root $
     */
    private $objName = NULL;

    /**
     * Container of product descriptions;
     *
     * @author Catalin Alexandru Zamfir <catalin.zamfir@raphpframework.ro>
     * @copyright Under the terms of the GNU General Public License v3
     * @version $Id: EPayment.php 1 2012-10-26 08:27:37Z root $
     */
    private $objInfo = NULL;

    /**
     * Container for product codes;
     *
     * @author Catalin Alexandru Zamfir <catalin.zamfir@raphpframework.ro>
     * @copyright Under the terms of the GNU General Public License v3
     * @version $Id: EPayment.php 1 2012-10-26 08:27:37Z root $
     */
    private $objCode = NULL;

    /**
     * Container for product prices;
     *
     * @author Catalin Alexandru Zamfir <catalin.zamfir@raphpframework.ro>
     * @copyright Under the terms of the GNU General Public License v3
     * @version $Id: EPayment.php 1 2012-10-26 08:27:37Z root $
     */
    private $objPrice = NULL;

    /**
     * Container for product quantities;
     *
     * @author Catalin Alexandru Zamfir <catalin.zamfir@raphpframework.ro>
     * @copyright Under the terms of the GNU General Public License v3
     * @version $Id: EPayment.php 1 2012-10-26 08:27:37Z root $
     */
    private $objQuantity = NULL;

    /**
     * Container for products VATs;
     *
     * @author Catalin Alexandru Zamfir <catalin.zamfir@raphpframework.ro>
     * @copyright Under the terms of the GNU General Public License v3
     * @version $Id: EPayment.php 1 2012-10-26 08:27:37Z root $
     */
    private $objVAT = NULL;

    /**
     * Construct an GeCAD EPayment object, passing Settings & Authentication as objects;
     *
     * @author Catalin Alexandru Zamfir <catalin.zamfir@raphpframework.ro>
     * @copyright Under the terms of the GNU General Public License v3
     * @version $Id: EPayment.php 1 2012-10-26 08:27:37Z root $
     */
    public function __construct ($objSTG, $objATH) {
        /* WARNING: GeCAD doesn't support STH (Strong Type Hinting) for
         the moment. We will release a STH update of their CLASS as soon as
         time will allow us to. Until that we downgrade to PHP types */

        // GeCAD, ePayment Object!
        $this->objLive = new LiveUpdate ((string) $objCryptKey = $objSTG
        ->getConfigKey (new S ('Settings.Configuration.GeCAD.CryptKey')));

        // Merchant
        $this->objLive->setMerchant ((string) $objMerchant = $objSTG
        ->getConfigKey (new S ('Settings.Configuration.GeCAD.Merchant')));

        // Set the ordering date (for ALL)
        $objIPNServerDate = new S (date ('Y-m-d G:i:s', time ()));
        $this->objLive->setOrderDate ((string) $objIPNServerDate);

        // Set default payment method
        $this->objLive->setPayMethod ('CCVISAMC');

        // Check
        if ($_GET
        ->offsetExists (_T ('Status'))) {
            // Swich
            switch ($_GET
            ->offsetGet (_T ('Status'))) {
                // If IPN
                case 'IPN':
                    // Set IPN data
                    $objIPNOrderPName = $objSTG->getPOST (new S ('IPN_PNAME'))->offsetGet (0);
                    $objIPNOrderPCode = $objSTG->getPOST (new S ('IPN_PID'))->offsetGet (0);
                    $objIPNOrderPDate = $objSTG->getPOST (new S ('IPN_DATE'));

                    // Maths
                    $objCId = (float) (string) $objSTG
                    ->getPOST (new S ('IPN_PRICE'))->offsetGet (0) *
                    (float) (string) $objSTG->getPOST (new
                    S ('IPN_QTY'))->offsetGet (0);

                    // Credit'em!
                    Database::doQuery ($objATH
                    ->doChangeToken (_QS ('doUPDATE')
                    ->doToken ('%table', Authentication::$objUser)
                    ->doToken ('%condition', _S ('%objUserCredit = %objUserCredit + %cId WHERE %objUserId = "%uId"'))
                    ->doToken ('%uId', $objSTG->getPOST (new S ('IPN_PCODE'))->offsetGet (0))
                    ->doToken ('%cId', $objCId)));

                    // Get HASH
                    echo '<EPAYMENT>', $objIPNServerDate, '|', $this->objLive->HMAC ($objCryptKey,
                    $objIPNOrderPCode->toLength ()->toInt () . $objIPNOrderPCode .
                    $objIPNOrderPName->toLength ()->toInt () . $objIPNOrderPName .
                    $objIPNOrderPDate->toLength ()->toInt () . $objIPNOrderPDate .
                    $objIPNServerDate->toLength ()->toInt () . $objIPNServerDate), '</EPAYMENT>';

                    // BK;
                    break;
            }
        }

        // Set
        $this->objName = new A;
        $this->objInfo = new A;
        $this->objCode = new A;
        $this->objPrice = new A;
        $this->objQuantity = new A;
        $this->objVAT = new A;
    }

    /**
     * Sets the product name;
     *
     * @author Catalin Alexandru Zamfir <catalin.zamfir@raphpframework.ro>
     * @copyright Under the terms of the GNU General Public License v3
     * @version $Id: EPayment.php 1 2012-10-26 08:27:37Z root $
     */
    public function setName (S $objProductName) {
        // Set
        $this->objName[] = (string) $objProductName;
    }

    /**
     * Sets the product info;
     *
     * @author Catalin Alexandru Zamfir <catalin.zamfir@raphpframework.ro>
     * @copyright Under the terms of the GNU General Public License v3
     * @version $Id: EPayment.php 1 2012-10-26 08:27:37Z root $
     */
    public function setInfo (S $objInfo) {
        // Set
        $this->objInfo[] = (string) $objInfo;
    }

    /**
     * Sets the product code;
     *
     * @author Catalin Alexandru Zamfir <catalin.zamfir@raphpframework.ro>
     * @copyright Under the terms of the GNU General Public License v3
     * @version $Id: EPayment.php 1 2012-10-26 08:27:37Z root $
     */
    public function setCode (S $objCode) {
        // Set
        $this->objCode[] = (string) $objCode;
    }

    /**
     * Sets the product price;
     *
     * @author Catalin Alexandru Zamfir <catalin.zamfir@raphpframework.ro>
     * @copyright Under the terms of the GNU General Public License v3
     * @version $Id: EPayment.php 1 2012-10-26 08:27:37Z root $
     */
    public function setPrice (F $objPrice) {
        // Set
        $this->objPrice[] = $objPrice->toInt ();
    }

    /**
     * Sets the product quantity;
     *
     * @author Catalin Alexandru Zamfir <catalin.zamfir@raphpframework.ro>
     * @copyright Under the terms of the GNU General Public License v3
     * @version $Id: EPayment.php 1 2012-10-26 08:27:37Z root $
     */
    public function setQuantity (I $objQuantity) {
        // Set
        $this->objQuantity[] = $objQuantity->toInt ();
    }

    /**
     * Sets the product VAT;
     *
     * @author Catalin Alexandru Zamfir <catalin.zamfir@raphpframework.ro>
     * @copyright Under the terms of the GNU General Public License v3
     * @version $Id: EPayment.php 1 2012-10-26 08:27:37Z root $
     */
    public function setVAT (I $objVAT) {
        // Set
        $this->objVAT[] = $objVAT->toInt ();
    }

    /**
     * Sets the product currency;
     *
     * @author Catalin Alexandru Zamfir <catalin.zamfir@raphpframework.ro>
     * @copyright Under the terms of the GNU General Public License v3
     * @version $Id: EPayment.php 1 2012-10-26 08:27:37Z root $
     */
    public function setCurrency (S $objCurrency) {
        // Set
        $this->objLive->setPricesCurrency ((string) $objCurrency);
    }

    /**
     * Sets the product order shipping;
     *
     * @author Catalin Alexandru Zamfir <catalin.zamfir@raphpframework.ro>
     * @copyright Under the terms of the GNU General Public License v3
     * @version $Id: EPayment.php 1 2012-10-26 08:27:37Z root $
     */
    public function setOrderShipping (S $objShipping = NULL) {
        // Set
        if ($objShipping == NULL) {
            $this->objLive->setOrderShipping (-1);
        }
    }

    /**
     * Sets the product discount;
     *
     * @author Catalin Alexandru Zamfir <catalin.zamfir@raphpframework.ro>
     * @copyright Under the terms of the GNU General Public License v3
     * @version $Id: EPayment.php 1 2012-10-26 08:27:37Z root $
     */
    public function setDiscount (F $objDiscount) {
        // Set
        $this->objLive->setDiscount ($objDiscount->toInt ());
    }

    /**
     * Sets the method in test mode;
     *
     * @author Catalin Alexandru Zamfir <catalin.zamfir@raphpframework.ro>
     * @copyright Under the terms of the GNU General Public License v3
     * @version $Id: EPayment.php 1 2012-10-26 08:27:37Z root $
     */
    public function setTestMode (B $objMode) {
        // Set
        $this->objLive->setTestMode ($objMode->toBoolean ());
    }

    /**
     * Return the Live Update URL for the given order;
     *
     * @author Catalin Alexandru Zamfir <catalin.zamfir@raphpframework.ro>
     * @copyright Under the terms of the GNU General Public License v3
     * @version $Id: EPayment.php 1 2012-10-26 08:27:37Z root $
     */
    public function getLiveUpdateURL () {
        // Go
        $this->objLive->setOrderPName ($this->objName->toArray ());
        $this->objLive->setOrderPInfo ($this->objInfo->toArray ());
        $this->objLive->setOrderPCode ($this->objCode->toArray ());
        $this->objLive->setOrderPrice ($this->objPrice->toArray ());
        $this->objLive->setOrderQTY   ($this->objQuantity->toArray ());
        $this->objLive->setOrderVAT   ($this->objVAT->toArray ());

        // Return
        return new S ($this->objLive->liveUpdateURL);
    }

    /**
     * Returns the update HTML, for the given order;
     *
     * @author Catalin Alexandru Zamfir <catalin.zamfir@raphpframework.ro>
     * @copyright Under the terms of the GNU General Public License v3
     * @version $Id: EPayment.php 1 2012-10-26 08:27:37Z root $
     */
    public function getLiveUpdateHTML () {
        // Return
        return new S ($this->objLive
        ->getLiveUpdateHTML ());
    }
}
?>
