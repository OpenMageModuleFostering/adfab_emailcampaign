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
class Adfab_EmailCampaign_Model_Source_Mode
{
    
    const TEST = 'test';
    const TEST_ALL_EMAIL = 'testAllEmail';
    const PRODUCTION = 'production';
    
   /**
    * Options getter
    *
    * @return array
    */
    public function toOptionArray()
    {
        return array(
            array(
                'label' => Mage::helper('adfab_emailcampaign')->__('Test'),
                'value' => self::TEST
            ),
            array(
                'label' => Mage::helper('adfab_emailcampaign')->__('Production'),
                'value' => self::PRODUCTION
            )
        );
    }
    
    public function toFormOptionArray()
    {
        return array(
            self::TEST => Mage::helper('adfab_emailcampaign')->__('Test'),
            self::PRODUCTION => Mage::helper('adfab_emailcampaign')->__('Production')
        );
    }

}
