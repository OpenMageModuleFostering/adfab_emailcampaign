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
 * @subpackage Helper
 * @author     Arnaud Hours <arnaud.hours@adfab.fr>
 */
class Adfab_EmailCampaign_Helper_Connector extends Mage_Core_Helper_Data
{
    
    /**
     * @return Adfab_EmailCampaign_Model_Connector_Abstract
     */
    public function getConnector()
    {
        $connector = Mage::getStoreConfig('newsletter/adfab_emailcampaign/connector');
        if (!empty($connector)) {
            $info = $this->getConnectorInfo($connector);
            if (isset($info['class'])) {
                return Mage::getSingleton($info['class']);
            }
        }
        return $this->getDefaultConnector();
    }
    
    /**
     * 
     * @return Adfab_EmailCampaign_Model_Connector_Magento
     */
    public function getDefaultConnector()
    {
        return Mage::getSingleton('adfab_emailcampaign/connector_magento');
    }
    
    /**
     *
     * @return Adfab_EmailCampaign_Model_Connector_Magento
     */
    public function getVoidConnector()
    {
        return Mage::getSingleton('adfab_emailcampaign/connector_void');
    }
    
    /**
     * retrun connector info in config.xml file
     * @return array
     */
    public function getConnectorInfo($nameInConfig)
    {
        $node = Mage::app()->getConfig()->getNode('global/emailcampaigns/connectors/'.$nameInConfig);
        if ($node) {
            return (array)$node;
        }
        return false;
    }
    
}
