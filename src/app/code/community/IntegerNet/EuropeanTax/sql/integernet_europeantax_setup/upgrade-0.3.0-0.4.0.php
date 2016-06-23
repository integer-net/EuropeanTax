<?php
/** @var Mage_Sales_Model_Resource_Setup $installer */
$installer = Mage::getResourceModel('sales/setup', 'sales_setup');

$installer->startSetup();

$installer->setConfigData('customer/address/taxvat_show', '');
$installer->setConfigData('customer/create_account/auto_group_assign', 0);
$installer->setConfigData('customer/create_account/tax_calculation_address_type', 'shipping');
$installer->setConfigData('customer/create_account/vat_frontend_visibility', 1);

$installer->endSetup();
