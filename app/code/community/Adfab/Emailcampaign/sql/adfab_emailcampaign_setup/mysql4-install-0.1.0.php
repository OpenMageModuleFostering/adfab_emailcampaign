<?php
/**
 * Adfab extension for Magento
 *
 * Long description of this file (if any...)
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade
 * the Adfab EmailCampaign module to newer versions in the future.
 * If you wish to customize the Adfab EmailCampaign module for your needs
 * please refer to http://www.magentocommerce.com for more information.
 *
 * @category   Adfab
 * @package    Adfab_EmailCampaign
 * @copyright  Copyright (C) 2014 Adfab (http://www.adfab.fr/)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * @var $this Mage_Core_Model_Resource_Setup
 */

$installer = $this;
$installer->startSetup();

try {
    $installer->getConnection()->dropTable($installer->getTable('adfab_emailcampaign/campaign'));
    $table = $installer->getConnection()->newTable($installer->getTable('adfab_emailcampaign/campaign'))
        ->addColumn('campaign_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
            'unsigned' => true,
            'nullable' => false,
            'primary' => true,
            'identity' => true,
            ), 'Campaign ID')
        ->addColumn('title', Varien_Db_Ddl_Table::TYPE_VARCHAR, 255, array(
            'nullable' => false,
            ), 'Campaign Title')
        ->addColumn('status', Varien_Db_Ddl_Table::TYPE_SMALLINT, null, array(
                'nullable' => false,
            ), 'Campaign Status')
        ->addColumn('variables', Varien_Db_Ddl_Table::TYPE_TEXT, null, array(
                'nullable' => true,
            ), 'Serialized Campaign Variables')
        ->addColumn('code', Varien_Db_Ddl_Table::TYPE_VARCHAR, 64, array(
                'nullable' => false,
            ), 'Campaign Code')
        ->addColumn('created_at', Varien_Db_Ddl_Table::TYPE_DATETIME, null, array(
            ), 'Created At')
        ->addColumn('updated_at', Varien_Db_Ddl_Table::TYPE_DATETIME, null, array(
            ), 'Updated At');
    $installer->getConnection()->createTable($table);
} catch (Exception $e) {
    Mage::logException($e);
}

$installer->endSetup();
