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
 * Short description of the class
 *
 * Long description of the class (if any...)
 *
 * @category   Adfab
 * @package    Adfab_EmailCampaign
 * @subpackage Model
 * @author     Arnaud Hours <arnaud.hours@adfab.fr>
 */
class Adfab_EmailCampaign_Model_Resource_Adminhtml_Notification extends Mage_Core_Model_Resource_Db_Abstract
{
    
    /**
     * @var string
     */
    protected $_notificationTable;
    
    /**
     * @var Varien_Db_Adapter_Interface
     */
    protected $_read;
    
    public function _construct()
    {
        $this->_init('adfab_emailcampaign/campaign_notification', 'notification_id');
        $this->_notificationTable = Mage::getSingleton('core/resource')->getTableName('adfab_emailcampaign/campaign_notification');
        $this->_read = $this->_getReadAdapter();
    }
    
    /**
     * Get carouselhome identifier by key
     *
     * @param   string $key
     * @return  int|false
     */
    public function loadByKey($key)
    {
        $select = $this->_read->select()
        ->from($this->_notificationTable)
        ->where('keylang=?',$key);
        $result = $this->_read->fetchRow($select);
    
        if(!$result) {
            return array();
        }
    
        return $result;
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
    
}
