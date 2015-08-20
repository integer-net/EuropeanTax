<?php
/** @var Mage_Customer_Model_Resource_Setup $installer */
$installer = $this;

$installer->startSetup();

$installer->addAttribute('customer_address', 'tax_class_id', array(
    'label' => 'Steuerklasse',
    'type' => 'int',
    'input' => 'select',
    'source' => 'tax/class_source_customer',
    'visible' => true,
));

Mage::getSingleton('eav/config')
    ->getAttribute('customer_address', 'tax_class_id')
    ->setData('used_in_forms', array('adminhtml_customer_address'))
    ->save();

$installer->endSetup();