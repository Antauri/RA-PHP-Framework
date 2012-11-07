<?php
/**
 * Class Mobilpay_Payment_Request_Cash
 * This class can be used for accessing mobilpay.ro payment interface for your configured online services
 * @copyright NETOPIA System
 * @version 1.0
 *
 */
class Mobilpay_Payment_Request_Cash extends Mobilpay_Payment_Request_Abstract
{
  const ERROR_LOAD_FROM_XML_ORDER_INVOICE_ELEM_MISSING	= 0x30000001;

  const CUSTOMER_TYPE_MERCHANT = 0x01;
  const CUSTOMER_TYPE_MOBILPAY = 0x02;

  /*
   * Cash terminal vendor constants
   */

  public $invoice	= null;

  //cash terminal vendor
    public $vendor	= null;


  function __construct()
  {
    parent::__construct();
    $this->type = self::PAYMENT_TYPE_CASH;
  }

  protected function _loadFromXml(DOMElement $elem)
  {
    parent::_parseFromXml($elem);

    //card request specific data
    $elems = $elem->getElementsByTagName('invoice');

    if($elems->length != 1)
    {
      throw new Exception('Mobilpay_Payment_Request_Card::loadFromXml failed; invoice element is missing', self::ERROR_LOAD_FROM_XML_ORDER_INVOICE_ELEM_MISSING);
    }

    $this->invoice = new Mobilpay_Payment_Invoice($elems->item(0));
    return $this;
  }

  protected function _prepare()
  {
    if(is_null($this->signature) || is_null($this->orderId) || !($this->invoice instanceof Mobilpay_Payment_Invoice))
    {
      throw new Exception('One or more mandatory properties are invalid!', self::ERROR_PREPARE_MANDATORY_PROPERTIES_UNSET);
    }

    $this->_xmlDoc 		= new DOMDocument('1.0', 'utf-8');
    $rootElem 			= $this->_xmlDoc->createElement('order');

    //set vendor attribute
    $xmlAttr 			= $this->_xmlDoc->createAttribute('vendor');
    $xmlAttr->nodeValue	= $this->vendor;
    $rootElem->appendChild($xmlAttr);

    //set payment type attribute
    $xmlAttr 			= $this->_xmlDoc->createAttribute('type');
    $xmlAttr->nodeValue	= $this->type;
    $rootElem->appendChild($xmlAttr);

    //set id attribute
    $xmlAttr 			= $this->_xmlDoc->createAttribute('id');
    $xmlAttr->nodeValue	= $this->orderId;
    $rootElem->appendChild($xmlAttr);

    //set timestamp attribute
    $xmlAttr 			= $this->_xmlDoc->createAttribute('timestamp');
    $xmlAttr->nodeValue	= date('YmdHis');
    $rootElem->appendChild($xmlAttr);

    $xmlElem			= $this->_xmlDoc->createElement('signature');
    $xmlElem->nodeValue	= $this->signature;
    $rootElem->appendChild($xmlElem);

    $xmlElem			= $this->_xmlDoc->createElement('service');
    $xmlElem->nodeValue	= $this->service;
    $rootElem->appendChild($xmlElem);

    $xmlElem			= $this->invoice->createXmlElement($this->_xmlDoc);
    $rootElem->appendChild($xmlElem);

    if(is_array($this->params) && sizeof($this->params) > 0)
    {
      $xmlParams = $this->_xmlDoc->createElement('params');
      foreach ($this->params as $key=>$value)
      {
        $xmlParam 	= $this->_xmlDoc->createElement('param');

        $xmlName			= $this->_xmlDoc->createElement('name');
        $xmlName->nodeValue = trim($key);
        $xmlParam->appendChild($xmlName);

        $xmlValue			= $this->_xmlDoc->createElement('value');
        $xmlValue->appendChild($this->_xmlDoc->createCDATASection($value));
        $xmlParam->appendChild($xmlValue);

        $xmlParams->appendChild($xmlParam);
      }

      $rootElem->appendChild($xmlParams);
    }

    if(!is_null($this->returnUrl) || !is_null($this->confirmUrl))
    {
      $xmlUrl = $this->_xmlDoc->createElement('url');
      if(!is_null($this->returnUrl))
      {
        $xmlElem = $this->_xmlDoc->createElement('return');
        $c=$this->_xmlDoc->createCDATASection($this->returnUrl);
        //$xmlElem->nodeValue = $this->returnUrl;
        $xmlElem->appendChild($c);
        $xmlUrl->appendChild($xmlElem);
        //var_dump($xmlElem->nodeValue);
      }
      if(!is_null($this->confirmUrl))
      {
        $xmlElem = $this->_xmlDoc->createElement('confirm');
        $xmlElem->nodeValue = $this->confirmUrl;
        $xmlUrl->appendChild($xmlElem);
      }

      $rootElem->appendChild($xmlUrl);
    }

    $this->_xmlDoc->appendChild($rootElem);

    return $this;
  }
}
