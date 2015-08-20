<?php
/**
 * integer_net Magento Module
 *
 * @category   IntegerNet
 * @package    IntegerNet_EuropeanTax
 * @copyright  Copyright (c) 2015 integer_net GmbH (http://www.integer-net.de/)
 * @author     Andreas von Studnitz <avs@integer-net.de>
 */
class IntegerNet_EuropeanTax_Helper_Customer_Data extends Mage_Customer_Helper_Data
{
    public function checkVatNumber($countryCode, $vatNumber, $requesterCountryCode = '', $requesterVatNumber = '')
    {
        if (substr($vatNumber, 0, 2) == $countryCode) {
            $vatNumber = substr($vatNumber, 2);
        }
        
        if ($requesterVatNumber && substr($requesterVatNumber, 0, 2) == $requesterCountryCode) {
            $requesterVatNumber = substr($requesterVatNumber, 2);
        }
        
        return parent::checkVatNumber($countryCode, $vatNumber, $requesterCountryCode, $requesterVatNumber);
    }
}