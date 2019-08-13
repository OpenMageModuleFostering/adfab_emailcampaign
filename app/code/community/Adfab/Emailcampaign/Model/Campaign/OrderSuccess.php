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
class Adfab_Emailcampaign_Model_Campaign_OrderSuccess extends Adfab_Emailcampaign_Model_Campaign_Observer
{
    
    public function process(Adfab_Emailcampaign_Model_Campaign $campaign, Varien_Event_Observer $observer)
    {
        $this->_init($campaign);
        $order = $observer->getOrder();
        $customers = $this->getCustomerList()
                ->addFieldToFilter('entity_id', array('eq' => $order->getCustomerId()));

        $this->sendMail(
            $campaign,
            $customers,
            function ($customer) {
                return array('customer' => $customer);
            }
        );
    }
    
    public function getCampaignUsage()
    {
        return Mage::helper('adfab_emailcampaign')->__('notice_order_success');
    }     
}
