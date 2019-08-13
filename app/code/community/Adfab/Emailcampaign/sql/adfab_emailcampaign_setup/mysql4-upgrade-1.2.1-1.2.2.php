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

try {
    $installer->getConnection()->dropTable($installer->getTable('adfab_emailcampaign/mailing'));
    $table = $installer->getConnection()->newTable($installer->getTable('adfab_emailcampaign/mailing'))
        ->addColumn('mailing_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
            'unsigned' => true,
            'nullable' => false,
            'primary' => true,
            'identity' => true,
            ), 'Mailing ID')
        ->addColumn('campaign_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
            'unsigned' => true,
            'nullable' => false,
            ), 'Campaign ID')
        ->addColumn('recipient_count', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
                'unsigned' => true,
                'nullable' => false,
            ), 'Campaign ID')
        ->addColumn('executed_at', Varien_Db_Ddl_Table::TYPE_DATETIME, null, array(
            ), 'Executed At');
    $installer->getConnection()->createTable($table);
    
    $installer->getConnection()
        ->addConstraint(
            'FK_MAILING_CAMPAIGN',
            $installer->getTable('adfab_emailcampaign/mailing'),
            'campaign_id',
            $installer->getTable('adfab_emailcampaign/campaign'),
            'campaign_id',
            'cascade',
            'cascade'
    );
    
    $installer->getConnection()->dropTable($installer->getTable('adfab_emailcampaign/mailing_recipient'));
    $table = $installer->getConnection()->newTable($installer->getTable('adfab_emailcampaign/mailing_recipient'))
        ->addColumn('recipient_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
            'unsigned' => true,
            'nullable' => false,
            'primary' => true,
            'identity' => true,
        ), 'Recipient ID')
        ->addColumn('mailing_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
            'unsigned' => true,
            'nullable' => false,
        ), 'Mailing ID')
        ->addColumn('customer_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
            'unsigned' => true,
            'nullable' => false,
        ), 'Customer ID')
        ->addColumn('opened', Varien_Db_Ddl_Table::TYPE_BOOLEAN, null, array(
            'nullable' => false,
            'default'  => false
        ), 'Customer ID')
        ->addColumn('coupon_code', Varien_Db_Ddl_Table::TYPE_VARCHAR, 255, array(
            'nullable' => true,
        ), 'Coupon Code');
    $installer->getConnection()->createTable($table);
    
    $installer->getConnection()
        ->addConstraint(
            'FK_MAILING_RECIPIENT_MAILING',
            $installer->getTable('adfab_emailcampaign/mailing_recipient'),
            'mailing_id',
            $installer->getTable('adfab_emailcampaign/mailing'),
            'mailing_id',
            'cascade',
            'cascade'
    );
    
    $installer
        ->getConnection()
        ->addKey(
            $installer->getTable('adfab_emailcampaign/mailing_recipient'),
            'IDX_MAILING_RECIPIENT_COUPON',
            'coupon_code'
    );
    
} catch (Exception $e) {
    Mage::logException($e);
}

$installer->endSetup();
