<?php
/**
 * integer_net Magento Module
 *
 * @category   IntegerNet
 * @package    IntegerNet_EuropeanTax
 * @copyright  Copyright (c) 2015 integer_net GmbH (http://www.integer-net.de/)
 * @author     Andreas von Studnitz <avs@integer-net.de>
 */
class IntegerNet_EuropeanTax_Model_Customer_Observer extends Mage_Customer_Model_Observer
{
    /**
     * Address after save event handler
     *
     * @param Varien_Event_Observer $observer
     */
    public function afterAddressSave($observer)
    {
        /** @var $customerAddress Mage_Customer_Model_Address */
        $customerAddress = $observer->getCustomerAddress();
        $customer = $customerAddress->getCustomer();

        if (!Mage::helper('customer/address')->isVatValidationEnabled($customer->getStore())
            || Mage::registry(self::VIV_PROCESSED_FLAG)
            || !$this->_canProcessAddress($customerAddress)
        ) {
            return;
        }

        try {
            Mage::register(self::VIV_PROCESSED_FLAG, true);

            /** @var $customerHelper Mage_Customer_Helper_Data */
            $customerHelper = Mage::helper('customer');

            if ($customerAddress->getVatId() == ''
                || !Mage::helper('core')->isCountryInEU($customerAddress->getCountry()))
            {
                $defaultGroupId = $customerHelper->getDefaultCustomerGroupId($customer->getStore());

                if (!$customer->getDisableAutoGroupChange() && $customer->getGroupId() != $defaultGroupId) {
                    $customerGroup = Mage::getModel('customer/group')->load($customer->getGroupId());
                    $customerAddress->setTaxClassId($customerGroup->getTaxClassId());
                    $customerAddress->save();
                }
            } else {

                $result = $customerHelper->checkVatNumber(
                    $customerAddress->getCountryId(),
                    $customerAddress->getVatId()
                );

                if (!$customer->getDisableAutoGroupChange()) {
                    $customerGroup = Mage::getModel('customer/group')->load($customer->getGroupId());
                    if ($result->getIsValid()) {
                        $customerAddress->setTaxClassId($customerGroup->getTaxClassIdVatId());
                    } else {
                        $customerAddress->setTaxClassId($customerGroup->getTaxClassId());
                    }
                    $customerAddress->save();
                }

                if (!Mage::app()->getStore()->isAdmin()) {
                    $validationMessage = Mage::helper('customer')->getVatValidationUserMessage($customerAddress,
                        $customer->getDisableAutoGroupChange(), $result);

                    if (!$validationMessage->getIsError()) {
                        Mage::getSingleton('customer/session')->addSuccess($validationMessage->getMessage());
                    } else {
                        Mage::getSingleton('customer/session')->addError($validationMessage->getMessage());
                    }
                }
            }
        } catch (Exception $e) {
            Mage::register(self::VIV_PROCESSED_FLAG, false, true);
        }
    }
}