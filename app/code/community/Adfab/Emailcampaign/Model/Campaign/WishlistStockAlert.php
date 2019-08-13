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
class Adfab_EmailCampaign_Model_Campaign_WishlistStockAlert extends Adfab_EmailCampaign_Model_Campaign_Cron
{
    
    public function process(Adfab_EmailCampaign_Model_Campaign $campaign)
    {
        $this->_init($campaign);
        
        $alert = $campaign->getVariable('stock_alert');
        
        $customers = $this->getCustomerList();
        
        $resource = Mage::getSingleton('core/resource');
        $read = $resource->getConnection('core_read');
        
        // configurable, grouped, bundle
        $ids = $read->query('
            Select distinct customer_id from (
                SELECT SUM(csi.qty) as sum_qty, ce.entity_id as customer_id
                FROM wishlist_item AS wi
                INNER JOIN catalog_product_entity AS cpe ON cpe.entity_id = wi.product_id AND cpe.type_id IN ("configurable", "grouped", "bundle")
                INNER JOIN catalog_product_relation AS cpr ON cpr.parent_id = cpe.entity_id
                INNER JOIN cataloginventory_stock_item AS csi ON csi.product_id = cpr.child_id AND csi.qty > 0
                INNER JOIN wishlist as w ON wi.wishlist_id = w.wishlist_id
                INNER JOIN customer_entity as ce ON ce.entity_id = w.customer_id
                GROUP BY cpe.entity_id, email
                HAVING sum_qty <= '.$alert.'
            ) as subRequest
        ')->fetchAll(Zend_Db::FETCH_COLUMN);
        
        // only simple
        $ids = array_merge($ids, $read->query('
            Select distinct customer_id from (
                SELECT SUM(csi.qty) as sum_qty, ce.entity_id as customer_id
                FROM wishlist_item AS wi
                INNER JOIN catalog_product_entity AS cpe ON cpe.entity_id = wi.product_id AND cpe.type_id IN ("simple")
                INNER JOIN cataloginventory_stock_item AS csi ON csi.product_id = cpe.entity_id AND csi.qty > 0
                INNER JOIN wishlist as w ON wi.wishlist_id = w.wishlist_id
                INNER JOIN customer_entity as ce ON ce.entity_id = w.customer_id
                GROUP BY cpe.entity_id, email
                HAVING sum_qty <= '.$alert.'
            ) as subRequest
        ')->fetchAll(Zend_Db::FETCH_COLUMN));
    
        $customers->addFieldToFilter('entity_id', array('in' => $ids));
        
        $this->sendMail(
            $customers,
            function($customer) {
                return array('customer' => $customer);
            }
        );
    }
    
    public function getCampaignUsage()
    {
        return Mage::helper('adfab_emailcampaign')->__('notice_wishlist_stock_alert');
    }

    public function getCampaignWarning()
    {
        return Mage::helper('adfab_emailcampaign')->__('warning_wishlist_stock_alert');
    }      
}
