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
        /** @var $customer Mage_Customer_Model_Customer */
        $customer = $observer->getCustomer();
        if ($shippingAddress = $customer->getDefaultShippingAddress()) {

            if ($taxClassId = $shippingAddress->getTaxClassId()) {
                $customer->setTaxClassId($taxClassId);
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

    /**
     * Set the posted allowed payment methods on the customer group model.
     *
     * @param Mage_Customer_Model_Group $group
     * @return null
     */
    protected function _setPaymentFilterOnGroup(Mage_Customer_Model_Group $group)
    {
        if (Mage::app()->getRequest()->getParam('payment_methods_posted')) {
            $allowedPaymentMethds = Mage::app()->getRequest()->getParam('allowed_payment_methods');
            $group->setAllowedPaymentMethods($allowedPaymentMethds);
        }
    }
}