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
class Adfab_EmailCampaign_Test_Model_Config extends EcomDev_PHPUnit_Test_Case
{
    
    /**
     *
     * @test
     */
    public function assertConnectorsAreAccessible()
    {
        $nodes = Mage::app()->getConfig()->getNode('global/emailcampaigns/connectors');
        if (!$nodes) {
            return $this->fail('no connector available');
        }
        foreach ($nodes as $node) {
            foreach ($node as $connector) { /* @var $node Mage_Core_Model_Config_Element */
                $this->assertNotEquals(false, Mage::getModel((string)$connector->class), 'connector ' . $connector->class . ' does not exists');
            }
        }
    }
    
    /**
     *
     * @test
     */
    public function assertCampaignsAreAccessible()
    {
        $nodes = Mage::app()->getConfig()->getNode('global/emailcampaigns/campaigns');
        foreach ($nodes as $node) {
            foreach ($node as $campaign) { /* @var $node Mage_Core_Model_Config_Element */
                $this->assertNotEquals(false, Mage::getModel((string)$campaign->class), 'campaign ' . $campaign->class . ' does not exists');
            }
        }
    }
    
}
