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
 * Short description of the class
 *
 * Long description of the class (if any...)
 *
 * @category   Adfab
 * @package    Adfab_Emailcampaign
 * @subpackage Model
 * @author     Arnaud Hours <arnaud.hours@adfab.fr>
 */
class Adfab_Emailcampaign_Model_Resource_Mailing extends Mage_Core_Model_Resource_Db_Abstract
{
    
    /**
     * @var string
     */
    protected $_mailingTable;
    
    /**
     * @var Varien_Db_Adapter_Interface
     */
    protected $_read;
    
    public function _construct()
    {
        $this->_init('adfab_emailcampaign/mailing', 'mailing_id');
        $this->_mailingTable = Mage::getSingleton('core/resource')->getTableName('adfab_emailcampaign/mailing');
        $this->_read = $this->_getReadAdapter();
    }
    
    /**
     * @return Varien_Db_Adapter_Pdo_Mysql
     */
    protected function _getReadAdapter()
    {
        return Mage::getSingleton('core/resource')->getConnection('core_read');
    }
    
    /**
     * @return Varien_Db_Adapter_Pdo_Mysql
     */
    protected function _getWriteAdapter()
    {
        return Mage::getSingleton('core/resource')->getConnection('core_write');
    }
    
    /**
     * add mailing data after the campaign is processed
     * @param Adfab_Emailcampaign_Model_Campaign_Abstract $model
     * @param Adfab_Emailcampaign_Model_Campaign $campaign
     */
    public function addMailingData($model, $campaign)
    {
        $customers = $model->getCustomerList();
        $this->_getWriteAdapter()->insert(
            $this->_mailingTable, 
            array(
                'campaign_id'     => $campaign->getId(),
                'recipient_count' => $customers->count(),
                'executed_at'     => date('Y-m-d H:i:s')
            )
        );
        $mailingId = $this->_getWriteAdapter()->lastInsertId($this->_mailingTable, 'mailing_id');
        Mage::getResourceModel('adfab_emailcampaign/mailing_recipient')
                ->addMailingRecipientData($mailingId, $model, $campaign);
    }
    
}
