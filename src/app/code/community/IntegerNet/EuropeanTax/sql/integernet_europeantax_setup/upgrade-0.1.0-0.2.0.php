<?php
/** @var Mage_Sales_Model_Resource_Setup $installer */
$installer = Mage::getResourceModel('sales/setup', 'sales_setup');

$installer->startSetup();

$installer->getConnection()->addColumn($this->getTable('sales_flat_quote_address'), 'tax_class_id', 'int(11)');

$installer->endSetup();