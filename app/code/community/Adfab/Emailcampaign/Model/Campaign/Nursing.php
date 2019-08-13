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
class Adfab_Emailcampaign_Model_Campaign_Nursing extends Adfab_Emailcampaign_Model_Campaign_Cron
{
    
    protected $_sendAfterDays;
    
    public function process(Adfab_Emailcampaign_Model_Campaign $campaign)
    {
        $this->_init($campaign);
        
        $this->_sendAfterDays = $sendAfterDays = $campaign->getVariable('send_after_days');
        $resend = $campaign->getVariable('resend');
        $lastRun = $campaign->getVariable('last_run');
        
        $resend = (intval($resend) === 1) && $lastRun;
        $customers = $this->getCustomerList();
        
        $format = 'Y-m-d H:i:s';
        // take everything before $dateTo
        $dateTo = new DateTime(date($format));
        $dateTo->sub(new DateInterval('P'.($sendAfterDays).'D'));
        
        $resource = Mage::getSingleton('core/resource');
        
        /* @var $read Varien_Db_Adapter_Pdo_Mysql */
        $read = $resource->getConnection('core_read');
        
        $select = $read->select()
        ->from(
            array('c' => $resource->getTableName('sales/order'), array('customer_id'))
        )->join(
            array('sfo' => $resource->getTableName('sales/order')),
            'c.entity_id = sfo.customer_id',
            array()
        )->where(
            'sfo.created_at > ?', $dateTo->format($format)
        )->group(
            'c.entity_id'
        );
        
        $alreadyOrdered = $read->fetchCol($select);
        
        $customers->getSelect()->joinLeft(
            array('sfo' => $resource->getTableName('sales/order')),
            'sfo.customer_id = e.entity_id',
            array()
        )->where(
            'e.entity_id NOT IN (?)', implode(',', $alreadyOrdered)
        );
        
        if (!$resend) {
            $dateFrom = new DateTime(date($format));
            $dateFrom->sub(new DateInterval('P'.($sendAfterDays).'D'));
            $dateFrom->sub(new DateInterval('PT'.($this->_processTime - $lastRun).'S'));
            $customers->getSelect()->where('sfo.created_at > ?', $dateFrom->format($format));
        }
        
        $customers->getSelect()->distinct('e.entity_id');
    
        $this->sendMail(
            $customers,
            function($customer) {
                return array('customer' => $customer, 'send_after_days' => $this->_sendAfterDays);
            }
        );
    }
    
    public function getCampaignUsage()
    {
        return Mage::helper('adfab_emailcampaign')->__('notice_nursing');
    }
    
}
