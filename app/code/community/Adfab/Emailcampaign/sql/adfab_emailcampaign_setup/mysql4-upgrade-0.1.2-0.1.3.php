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
 * the Adfab Emailcampaign module to newer versions in the future.
 * If you wish to customize the Adfab Emailcampaign module for your needs
 * please refer to http://www.magentocommerce.com for more information.
 *
 * @category   Adfab
 * @package    Adfab_Emailcampaign
 * @copyright  Copyright (C) 2014 Adfab (http://www.adfab.fr/)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * @var $this Mage_Core_Model_Resource_Setup
 */

$installer = $this;
$installer->startSetup();

// try {
    $installer->getConnection()->dropTable($installer->getTable('adfab_emailcampaign/campaign_notification'));
    $table = $installer->getConnection()->newTable($installer->getTable('adfab_emailcampaign/campaign_notification'))
        ->addColumn('notification_id', Varien_Db_Ddl_Table::TYPE_INTEGER, 11, array(
            'unsigned' => true,
            'nullable' => false,
            'primary' => true,
            'identity' => true,
            ), 'Notification ID')
        ->addColumn('date_added', Varien_Db_Ddl_Table::TYPE_DATETIME, null, array(
            'nullable' => true,
            ), 'Date Added')
        ->addColumn('content', Varien_Db_Ddl_Table::TYPE_TEXT, null, array(
                'nullable' => false,
            ), 'Content')
        ->addColumn('is_read', Varien_Db_Ddl_Table::TYPE_BOOLEAN, null, array(
                'nullable' => false,
                'default'  => 0
            ), 'Is Read')
        ->addColumn('type', Varien_Db_Ddl_Table::TYPE_VARCHAR, 32, array(
            'nullable' => false,
            ), 'Type');
    $installer->getConnection()->createTable($table);
// } catch (Exception $e) {
//     Mage::logException($e);
// }

$installer->endSetup();
