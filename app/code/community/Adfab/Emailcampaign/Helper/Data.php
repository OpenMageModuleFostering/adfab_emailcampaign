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
class Adfab_Emailcampaign_Helper_Data extends Mage_Core_Helper_Data
{
    
    protected $_campaigns;
    
    public function log($message, $level = Zend_Log::DEBUG)
    {
        Mage::log($message, $level, 'emailcampaign.log');
    }
    
    /**
     * 
     * @param string $type
     * @return array<Adfab_Emailcampaign_Model_Campaign>
     */
    public function getCampaignsByType($type)
    {
        if (is_null($this->_campaigns)) {
            $this->_campaigns = Mage::getResourceModel('adfab_emailcampaign/campaign_collection')
                ->addFieldToFilter('status', array('eq' => Adfab_Emailcampaign_Model_Status::STATUS_ENABLED));
        }
        $found = array();
        foreach ($this->_campaigns as $campaign) {
            $node = Mage::app()->getConfig()->getNode('global/emailcampaigns/campaigns/' . $campaign->getCode());
            if (false === $node) {
                continue;
            }
            $campaignType = isset($node->type) ? (string)$node->type : '';
            if ($campaignType == $type) {
                $found[] = $campaign;
            }
        }
        return $found;
    }
    
    /**
     * return campaigns config
     * 
     * @param string $node
     * @param string $value
     * @return array
     */
    public function getCampaignConfigByNodeValue($node, $value)
    {
        $nodes = Mage::app()->getConfig()->getNode('global/emailcampaigns/campaigns');
        $campaigns = array();
        foreach ($nodes->children() as $campaign) {
            if (isset($campaign->{$node}) && $value == (string)$campaign->{$node}) {
                $campaigns[] = $campaign;
            }
        }
        return $campaigns;
    }
    
    /**
     * 
     * @param string $code
     * @param string $what
     * @return Mage_Core_Model_Config_Element
     */
    public function getCampaignConfig($code, $what)
    {
        $node = Mage::app()->getConfig()->getNode('global/emailcampaigns/campaigns/' . $code);
        return isset($node->{$what}) ? $node->{$what} : false;
    }
    
    /**
     * 
     * @param string $code
     * @return Adfab_Emailcampaign_Model_Campaign_Abstract
     */
    public function getCampaignModel($code)
    {
        $class = (string)$this->getCampaignConfig($code, 'class');
        if (empty($class)) {
            return false;
        }
        return Mage::getModel($class);
    }
    
}
