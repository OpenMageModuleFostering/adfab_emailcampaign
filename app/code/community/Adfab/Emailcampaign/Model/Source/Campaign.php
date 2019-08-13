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
class Adfab_EmailCampaign_Model_Source_Campaign
{
    
    protected $_campaigns;
    protected $_campaignsForm;
    
   /**
    * Options getter
    *
    * @return array
    */
    public function toOptionArray()
    {
        if (is_null($this->_campaigns)) {
            $nodes = Mage::app()->getConfig()->getNode('global/emailcampaigns/campaigns');
            $this->_campaigns = array();
            if (!$nodes) {
                return $this->_campaigns = array();
            }
            foreach ($nodes as $node) {
                foreach ($node as $campaign) { /* @var $node Mage_Core_Model_Config_Element */
                    if (!isset($campaign->name)) {
                        continue;
                    }
                    
                    $usage = '';
                    $warning = '';
                    if (($class = Mage::getSingleton((string)$campaign->class))) {
                        $usage = $class->getCampaignUsage();
                        $warning = $class->getCampaignWarning();
                    }
                    
                    $this->_campaigns[] = array(
                        'label' => Mage::helper(
                        isset($campaign['module']) ? $campaign['module'] : 'adfab_emailcampaign'
                    )->__((string)$campaign->name),
                        'value'     => $campaign->getName(),
                        'usage'     => $usage,
                        'warning'   => $warning
                    );
                }
            }
        }
        return $this->_campaigns;
    }
    
    public function toFormOptionArray()
    {
        if (is_null($this->_campaignsForm)) {
            $nodes = Mage::app()->getConfig()->getNode('global/emailcampaigns/campaigns');
            $this->_campaignsForm = array();
            if (!$nodes) {
                return $this->_campaignsForm = array();
            }
            foreach ($nodes as $node) {
                foreach ($node as $campaign) { /* @var $node Mage_Core_Model_Config_Element */
                    if (!isset($campaign->name)) {
                        continue;
                    }
    
                    $usage = '';
                    $waring = '';
                    if (($class = Mage::getSingleton((string)$campaign->class))) {
                        $usage = $class->getCampaignUsage();
                        $waring = $class->getCampaignWarning();
                    }
                    
                    $this->_campaignsForm[$campaign->getName()] = Mage::helper(
                        isset($campaign['module']) ? $campaign['module'] : 'adfab_emailcampaign'
                    )->__((string)$campaign->name);
                }
            }
        }
        return $this->_campaignsForm;
    }
    
}
