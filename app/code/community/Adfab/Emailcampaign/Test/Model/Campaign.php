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
class Adfab_EmailCampaign_Test_Model_Campaign extends EcomDev_PHPUnit_Test_Case
{
    
    /**
     *
     * @test
     */
    public function assertVariablesAreCorrectlySaved()
    {
        $campaign = Mage::getModel('adfab_emailcampaign/campaign');
        $campaign->setVariable(array('test' => 'truc'))
                ->save();
        $campaign = Mage::getModel('adfab_emailcampaign/campaign')->load($campaign->getId());
        
        $this->assertSame($campaign->getVariable('test'), 'truc');
        
        $campaign->setVariable('chose', 'machin')
                ->save();
        
        $campaign = Mage::getResourceModel('adfab_emailcampaign/campaign_collection')
                ->addFieldToFilter('campaign_id', array('eq' => $campaign->getId()))
                ->getFirstItem();
        
        $this->assertSame('truc', $campaign->getVariable('test'));
        $this->assertSame('machin', $campaign->getVariable('chose'));
    }
    
}
