<?php
/**
 * integer_net Magento Module
 *
 * @category   IntegerNet
 * @package    IntegerNet_EuropeanTax
 * @copyright  Copyright (c) 2015 integer_net GmbH (http://www.integer-net.de/)
 * @author     Andreas von Studnitz <avs@integer-net.de>
 */
class IntegerNet_EuropeanTax_Model_Sales_Quote extends Mage_Sales_Model_Quote
{
    public function getCustomerTaxClassId()
    {
        Mage::log(__METHOD__);
        if ($this->_isAdmin()) {

            /** @var $customer Mage_Customer_Model_Customer */
            $customer = Mage::getSingleton('adminhtml/session_quote')->getCustomer();
            
        } else {

            /** @var $customer Mage_Customer_Model_Customer */
            $customer = Mage::getSingleton('customer/session')->getCustomer();
        }

        Mage::log($customer->debug());

        if ($taxClassId = $customer->getTaxClassId()) {
            $this->setCustomerTaxClassId($taxClassId);
            return $this->getData('customer_tax_class_id');
        }

        return parent::getCustomerTaxClassId();
    }

    /**
     * @return bool
     */
    protected function _isAdmin()
    {
        return Mage::app()->getStore()->isAdmin() || Mage::getDesign()->getArea() == 'adminhtml';
    }
}