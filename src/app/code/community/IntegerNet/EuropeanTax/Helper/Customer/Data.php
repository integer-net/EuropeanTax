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
    /**
     * Rewrite - Allow VAT ID to contain country code at the beginning
     *
     * @param string $countryCode
     * @param string $vatNumber
     * @param string $requesterCountryCode
     * @param string $requesterVatNumber
     * @return Varien_Object
     */
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

    /**
     * Unserialize and clear name prefix or suffix options
     *
     * @param string $options
     * @return array|bool
     */
    protected function _prepareNamePrefixSuffixOptions($options)
    {
        $result = parent::_prepareNamePrefixSuffixOptions($options);
        if (!is_array($result)) {
            return $result;
        }
        foreach($result as $key => $value) {
            $result[$key] = Mage::helper('strass_template')->__($value);
        }
        return $result;
    }
}