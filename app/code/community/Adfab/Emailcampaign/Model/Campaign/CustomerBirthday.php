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
class Adfab_Emailcampaign_Model_Campaign_CustomerBirthday extends Adfab_Emailcampaign_Model_Campaign_Cron
{
    
    public function process(Adfab_Emailcampaign_Model_Campaign $campaign)
    {
        $this->_init($campaign);
        
        $customers = $this->getCustomerList()->addAttributeToSelect('dob');
        
        $customers->addFieldToFilter(
            'dob',
            array('like' => '%-'.date("m").'-'.date("d").' %')
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
        return Mage::helper('adfab_emailcampaign')->__('notice_customer_birthday');
    }
    
    public function getCampaignWarning()
    {
        $configDob = Mage::getStoreConfig(
                   'customer/address/dob_show',
                   Mage::app()->getStore()
               );
               
        switch ($configDob) {
            case 'req':
                return "";
                break;
            case 'opt':
                return Mage::helper('adfab_emailcampaign')->__('warning_customer_birthday_cart_opt');
                break;
            default:
                return Mage::helper('adfab_emailcampaign')->__('warning_customer_birthday_cart_none');
                break;
        }   
    }  
      
    /**
     * manually set cron expr to schedule campaign every days
     * 
     * @param Adfab_Emailcampaign_Model_Campaign $campaign
     */
    public function beforeCampaignSave($campaign)
    {
        $campaign->setVariable(
            'cron_expr',
            $campaign->getVariable('minute') . ' ' . $campaign->getVariable('hour') . ' * * *'
        );
    }
    
}