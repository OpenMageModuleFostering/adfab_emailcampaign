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
class Adfab_Emailcampaign_Model_Campaign_OfferAfterOrder extends Adfab_Emailcampaign_Model_Campaign_Cron
{
    
    public function process(Adfab_Emailcampaign_Model_Campaign $campaign)
    {
        $this->_init($campaign);
        
        $sendAfter = $campaign->getVariable('send_after');
        
        $format = 'Y-m-d H:i:s';
        $date = new DateTime(date($format));
        $date->sub(new DateInterval('P'.$sendAfter.'D'));
        
        $ressource = Mage::getSingleton('core/resource');
        $customers = $this->getCustomerList();
        
        $customers->getSelect()->join(
            array('order' => $ressource->getTableName('sales/order')),
            'order.customer_id = e.entity_id',
            array('last_order_date' => 'MAX(order.created_at)', 'count_orders' => 'COUNT(order.customer_id)')
        )->having(
            'MAX(order.created_at) < ?', $date->format($format)
        )/*->having(
            'count_orders = ?', 1
        )*/;
        
        $this->sendMail(
            $customers,
            function($customer) {
                return array('customer' => $customer);
            }
        );
    }
    
    public function getCampaignUsage()
    {
        return Mage::helper('adfab_emailcampaign')->__('notice_offerafterorder');
    }
    
    public function beforeCampaignSave($campaign)
    {
        $ruleId = $campaign->getVariable('rule_id');
        $rule = Mage::getModel('salesrule/rule')->load($ruleId);
        if (!$rule->getId()) {
            Mage::getSingleton('adminhtml/session')->addWarning(Mage::helper('adfab_emailcampaign')->__('Rule with ID "%s" does not exists', $ruleId));
        }
    }
}
