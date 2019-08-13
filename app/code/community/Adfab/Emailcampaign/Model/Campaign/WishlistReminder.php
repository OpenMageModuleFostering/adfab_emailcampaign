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
class Adfab_EmailCampaign_Model_Campaign_WishlistReminder extends Adfab_EmailCampaign_Model_Campaign_Cron
{
    
    public function process(Adfab_EmailCampaign_Model_Campaign $campaign)
    {
        $this->_init($campaign);
        
        $olderThan = $campaign->getVariable('older_than');
        $lastRun = $campaign->getVariable('last_run');
        
        $customers = $this->getCustomerList();
        
        // take everything before $dateTo
        $dateTo = new Zend_Date();
        $dateTo->add((-$olderThan), Zend_Date::DAY);
        
        // but not before $dateFrom
        $dateFrom = new Zend_Date();
        $dateFrom->add((-$olderThan), Zend_Date::DAY);
        $dateFrom->add($lastRun - $this->_processTime, Zend_Date::SECOND);
        
        $ressource = Mage::getSingleton('core/resource');
    
        $customers->getSelect()->join(
            array('w' => $ressource->getTableName('wishlist')),
            'w.customer_id = e.entity_id',
            array()
        )->where(
            'w.updated_at > ?', date('Y-m-d H:i:s', $dateFrom->getTimestamp())
        )->where(
            'w.updated_at < ?', date('Y-m-d H:i:s', $dateTo->getTimestamp())
        );
    
        $this->sendMail(
            $customers,
            function($customer) {
                return array('customer' => $customer);
            }
        );
    }
    
    public function getCampaignUsage()
    {
        return Mage::helper('adfab_emailcampaign')->__('notice_wishlist_reminder');
    }

    public function getCampaignWarning()
    {
        return Mage::helper('adfab_emailcampaign')->__('warning_wishlist_reminder');
    }      
}
