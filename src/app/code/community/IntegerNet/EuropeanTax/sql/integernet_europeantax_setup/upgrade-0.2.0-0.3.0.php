<?php
/** @var Mage_Sales_Model_Resource_Setup $installer */
$installer = Mage::getResourceModel('sales/setup', 'sales_setup');

$installer->startSetup();

$installer->getConnection()->addColumn($this->getTable('customer/customer_group'), 'request_vat_id', 'int(1)');
$installer->getConnection()->addColumn($this->getTable('customer/customer_group'), 'tax_class_id_vat_id', 'int(11)');

$installer->endSetup();