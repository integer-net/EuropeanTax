<?php
/**
 * integer_net Magento Module
 *
 * @category   IntegerNet
 * @package    IntegerNet_EuropeanTax
 * @copyright  Copyright (c) 2015 integer_net GmbH (http://www.integer-net.de/)
 * @author     Andreas von Studnitz <avs@integer-net.de>
 */
class IntegerNet_EuropeanTax_Model_Observer 
{
    public function customerLoadAfter(Varien_Event_Observer $observer)
    {
        Mage::log(__METHOD__);
        /** @var $customer Mage_Customer_Model_Customer */
        $customer = $observer->getCustomer();
        if ($shippingAddress = $customer->getDefaultShippingAddress()) {
            if ($taxClassId = $shippingAddress->getTaxClassId()) {
                Mage::log($taxClassId);
                $customer->setTaxClassId($taxClassId);
            }
        } 
        
        Mage::log(__METHOD__ . ' end');
    }
}