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
    /**
     * VAT ID validation processed flag code
     */
    const VIV_PROCESSED_FLAG = 'viv_after_address_save_processed';

    public function customerLoadAfter(Varien_Event_Observer $observer)
    {
        /** @var $customer Mage_Customer_Model_Customer */
        $customer = $observer->getCustomer();
        if ($shippingAddress = $customer->getDefaultShippingAddress()) {

            if ($taxClassId = $shippingAddress->getTaxClassId()) {
                $customer->setTaxClassId($taxClassId);
            }
        }

        if ($quoteId = Mage::getSingleton('checkout/session')->getQuoteId()) {
            /** @var $quoteShippingAddressCollection Mage_Sales_Model_Resource_Quote_Address_Collection */
            $quoteShippingAddressCollection = Mage::getResourceModel('sales/quote_address_collection');
            $quoteShippingAddressCollection->addFieldToFilter('quote_id', $quoteId);
            $quoteShippingAddressCollection->addFieldToFilter('address_type', 'shipping');

            $quoteShippingAddress = $quoteShippingAddressCollection->getFirstItem();
            if ($quoteShippingAddress->getId()) {
                if ($taxClassId = $quoteShippingAddress->getTaxClassId()) {
                    $customer->setTaxClassId($taxClassId);
                }
            }
        }
    }

    public function coreBlockAbstractPrepareLayoutAfter(Varien_Event_Observer $observer)
    {
        $block = $observer->getBlock();
        
        if ($block instanceof Mage_Adminhtml_Block_Customer_Group_Edit_Form) {

            $form = $block->getForm();

            $fieldset = $form->getElement('base_fieldset');

            if (Mage::getSingleton('adminhtml/session')->getCustomerGroupData()) {
                $values = Mage::getSingleton('adminhtml/session')->getCustomerGroupData();
            } else {
                $values = Mage::registry('current_group')->getData();
            }

            $fieldset->addField('tax_class_id_vat_id', 'select',
                array(
                    'name'  => 'tax_class_id_vat_id',
                    'label' => Mage::helper('integernet_europeantax')->__('Tax Class with valid VAT ID'),
                    'title' => Mage::helper('integernet_europeantax')->__('Tax Class with valid VAT ID'),
                    'class' => 'required-entry',
                    'required' => true,
                    'values' => Mage::getSingleton('tax/class_source_customer')->toOptionArray(),
                    'value' => isset($values['tax_class_id_vat_id']) ? $values['tax_class_id_vat_id'] : null,
                )
            );
            
            $fieldset->addField('request_vat_id', 'select', array(
                'name' => 'request_vat_id',
                'label' => Mage::helper('integernet_europeantax')->__('Request VAT ID'),
                'title' => Mage::helper('integernet_europeantax')->__('Request VAT ID'),
                'class' => '',
                'values' => Mage::getSingleton('eav/entity_attribute_source_boolean')->getAllOptions(),
                'value' => isset($values['request_vat_id']) ? $values['request_vat_id'] : 0,
                'required' => false,
            ));

        }
    }

    /**
     * @param Varien_Event_Observer $observer
     */
    public function customerGroupSaveBefore($observer)
    {
        /** @var Mage_Customer_Model_Group $group */
        $group = $observer->getObject();

        $group->setData('request_vat_id', Mage::app()->getRequest()->getParam('request_vat_id'));
        $group->setData('tax_class_id_vat_id', Mage::app()->getRequest()->getParam('tax_class_id_vat_id'));
    }

    public function salesQuoteAddressSaveAfter(Varien_Event_Observer $observer)
    {
        /** @var Mage_Sales_Model_Quote_Address $quoteAddress  */
        $quoteAddress = $observer->getQuoteAddress();

        if ($quoteAddress->getAddressType() != 'shipping') {
            return;
        }

        $customer = $quoteAddress->getQuote()->getCustomer();

        if (!Mage::helper('customer/address')->isVatValidationEnabled($customer->getStore())
            || Mage::registry(self::VIV_PROCESSED_FLAG)
        ) {
            return;
        }

        try {
            Mage::register(self::VIV_PROCESSED_FLAG, true);

            /** @var $customerHelper Mage_Customer_Helper_Data */
            $customerHelper = Mage::helper('customer');

            if ($quoteAddress->getVatId() == ''
                || !Mage::helper('core')->isCountryInEU($quoteAddress->getCountry()))
            {
                $defaultGroupId = $customerHelper->getDefaultCustomerGroupId($customer->getStore());

                if (!$customer->getDisableAutoGroupChange() && $customer->getGroupId() != $defaultGroupId) {
                    $customerGroup = Mage::getModel('customer/group')->load($customer->getGroupId());
                    $quoteAddress->setTaxClassId($customerGroup->getTaxClassId());
                    $quoteAddress->save();
                }
            } else {

                $result = $customerHelper->checkVatNumber(
                    $quoteAddress->getCountryId(),
                    $quoteAddress->getVatId()
                );

                if (!$customer->getDisableAutoGroupChange()) {
                    $customerGroup = Mage::getModel('customer/group')->load($customer->getGroupId());
                    if ($result->getIsValid()) {
                        $quoteAddress->setTaxClassId($customerGroup->getTaxClassIdVatId());
                    } else {
                        $quoteAddress->setTaxClassId($customerGroup->getTaxClassId());
                    }
                    $quoteAddress->save();
                }

                if (!Mage::app()->getStore()->isAdmin()) {
                    $validationMessage = Mage::helper('customer')->getVatValidationUserMessage($quoteAddress,
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