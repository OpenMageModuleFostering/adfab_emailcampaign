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
 * @subpackage Helper
 * @author     Arnaud Hours <arnaud.hours@adfab.fr>
 */
class Adfab_Emailcampaign_Helper_Coupon extends Mage_Core_Helper_Data
{
    
    /**
     * generate 
     * @param Mage_SalesRule_Model_Rule|int $rule
     * @param Mage_Customer_Model_Resource_Customer_Collection $customers
     * @param array $config
     * @return Adfab_Emailcampaign_Helper_Coupon
     */
    public function generateCouponForCustomers($rule, $customers, $config = null)
    {
        if (is_numeric($rule)) {
            $rule = Mage::getModel('salesrule/rule')->load($rule);
        }
        if (!$rule || !$rule->getId()) {
            return $this;
        }
        if (!$rule->getUseAutoGeneration()) {
            $code = $rule->getCouponCode();
            foreach ($customers as $customer) {
                // store this will save the coupon in mailing_recipient table
                $customer->setCampaignCouponCode($code);
            }
        } else {
            // we have to generate 1 coupon for each customer
            $generator = Mage::getModel('salesrule/coupon_massgenerator');
            if (!$config) {
                $config = array();
            }
            $data = array(
                'max_probability'   => .25,
                'max_attempts'      => 10,
                'uses_per_customer' => $rule->getUsesPerCustomer(),
                'uses_per_coupon'   => 1, // 1 per coupon but one customer can use several coupon in the same rule according to $rule->getUsesPerCustomer()
                'qty'               => $customers->count(),
                'length'            => isset($config['length']) ? $config['length'] : 14,
//                 'to_date'           => '2013-12-31',
                'format'            => isset($config['format']) ? $config['format'] : Mage_SalesRule_Helper_Coupon::COUPON_FORMAT_ALPHANUMERIC,
                'suffix'            => isset($config['suffix']) ? $config['suffix'] : '',
                'prefix'            => isset($config['prefix']) ? $config['prefix'] : '',
                'dash'              => isset($config['dash']) ? $config['dash'] : null,
                'rule_id'           => $rule->getId()
            );
        
            if (!$generator->validateData($data)) {
                Mage::log('fail to validate data in ' . __CLASS__);
                return $this;
            }
        
            $generator->setData($data);
            $generator->generatePool();
        
            // retrieve these coupons code
            $coupons = Mage::getResourceModel('salesrule/coupon_collection')
                    ->addFieldToFilter('rule_id', array('eq' => $rule->getId()));
            $coupons->getSelect()
                    ->order('coupon_id DESC')
                    ->limit($customers->count());
            $coupons->load();
        
            foreach ($customers as $customer) {
                $cIterator = $coupons->getIterator();
                $coupon = $cIterator->current();
                if (!$coupon) {
                    Mage::log('too few coupons in ' . __CLASS__);
                    continue;
                }
                // store this will save the coupon in mailing_recipient table
                $customer->setCampaignCouponCode($coupon->getCode());
                $cIterator->next();
            }
        }
    }
    
}