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
class Adfab_Emailcampaign_Model_Observer extends Mage_Core_Model_Abstract
{
    
    /**
     * add campaign events into config
     * 
     * @param Varien_Event_Observer $event
     * @return Adfab_Emailcampaign_Model_Observer
     */
    public function addObservers($event)
    {
        if (Mage::app()->getStore()->isAdmin()) {
            return $this;
        }
        $config = Mage::getConfig()->getNode('global/events');
        if (isset($config->adfab_emailcampaign_events_added)) {
            return;
        }
        $nodes = Mage::getConfig()->getNode('global/emailcampaigns/campaigns');
        foreach ($nodes->children() as $node) {
            if (isset($node->type, $node->event) && 'observer' == (string)$node->type) {
                
                if (!isset($config->{(string)$node->event})) {
                    $config->addChild((string)$node->event);
                }
                if (!isset($config->{(string)$node->event}->observers)) {
                    $config->{(string)$node->event}->addChild('observers');
                }
                
                $child = $config->{$node->event}->observers->addChild((string)$node->name);
                $child->addChild('class', 'adfab_emailcampaign/observer');
                $child->addChild('method', 'dispatch');
            }
        }
        $config->addChild('adfab_emailcampaign_events_added', 1);
        return $this;
    }
    
    /**
     * 
     * @param Varien_Event_Observer $observer
     */
    public function dispatch($observer)
    {
        $configs = Mage::helper('adfab_emailcampaign')->getCampaignConfigByNodeValue('event', $observer->getEvent()->getName());
        foreach ($configs as $config) {
            $campaigns = Mage::getResourceModel('adfab_emailcampaign/campaign_collection')
                    ->addFieldToFilter('status', array('eq' => Adfab_Emailcampaign_Model_Status::STATUS_ENABLED))
                    ->addFieldToFilter('code', array('eq' => (string)$config->getName()));
            $class = Mage::getModel((string)$config->class);
            foreach ($campaigns as $campaign) {
                Mage::dispatchEvent('emailcampaign_campaign_process_before', array('campaign' => $campaign, 'model' => $class));
                $success = $class->process($campaign, $observer);
                if ($success !== false) {
                    Mage::dispatchEvent('emailcampaign_campaign_process_after', array('campaign' => $campaign, 'model' => $class));
                } else {
                    Mage::log('Campaign ID ' . $campaign->getId() . ' return false');
                }
            }
        }
    }
    
    /**
     * check if coupon can be used by the customer according to mailing_recipient table
     * @param Varien_Event_Observer $observer
     */
    public function checkCouponCode($observer)
    {
        $quote = $observer->getQuote();
        $coupon = $quote->getCouponCode();
        $recipient = Mage::getResourceModel('adfab_emailcampaign/mailing_recipient_collection')
                ->addFieldToFilter('coupon_code', array('eq' => $coupon))
                ->getFirstItem();
        // only if we have a recipient, check the coupon validity
        if ($recipient && $recipient->getId()) {
            $customer = Mage::getSingleton('customer/session')->getCustomer();
            $customerId = $customer ? $customer->getId() : false;
            // the customer must match with the coupon defined in mailing_recipient table
            if (!$customerId || $recipient->getCustomerId() != $customerId) {
                $result = $observer->getResult();
                $result->setDiscountAmount(0);
                $result->setBaseDiscountAmount(0);
                $quote->unsCouponCode();
            }
        }
    }
    
}
