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
class Adfab_Emailcampaign_Model_Connector_Magento extends Adfab_Emailcampaign_Model_Connector_Abstract
{
    
    /**
     *
     * @param Adfab_Emailcampaign_Model_Campaign $campaign
     * @param Mage_Customer_Model_Resource_Customer_Collection $customer
     * @param array $data
     */
    public function sendMail(Adfab_Emailcampaign_Model_Campaign $campaign, Mage_Customer_Model_Resource_Customer_Collection $customers, $data)
    {
        $mailTemplate = Mage::getModel('core/email_template');
        $closure = is_object($data) && ($data instanceof Closure);
        
        $translate = Mage::getSingleton('core/translate');
        $translate->setTranslateInline(false);
        
        /* @var $mailTemplate Mage_Core_Model_Email_Template */
        foreach ($customers as $customer) {
            $mailTemplate->setDesignConfig(array('area' => 'frontend'))
                    ->setReplyTo($customer->getEmail())
                    ->setTemplateSubject('test')
                    ->sendTransactional(
                        (is_int($campaign->getTemplateId())) ? (int)$campaign->getTemplateId() : $campaign->getTemplateId(),
                        array('email' => $customer->getEmail(), 'name' => $customer->getFirstname() . ' ' . $customer->getLastname()),
                        $customer->getEmail(),
                        $customer->getFirstname() . ' ' . $customer->getLastname(),
                        $closure ? $data($customer) : ($data['customer'] = $customer)
                    );
        }
        
        $translate->setTranslateInline(true);
        
    }
    
}
