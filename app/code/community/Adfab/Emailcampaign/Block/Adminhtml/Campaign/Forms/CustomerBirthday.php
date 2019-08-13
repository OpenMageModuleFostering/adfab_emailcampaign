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
 * @subpackage Block
 * @author     Arnaud Hours <arnaud.hours@adfab.fr>
 */
class Adfab_EmailCampaign_Block_Adminhtml_Campaign_Forms_CustomerBirthday extends Adfab_EmailCampaign_Block_Adminhtml_Campaign_Forms_Abstract
{
    
    protected function _prepareForm()
    {
        $form = new Varien_Data_Form();
        $this->setForm($form);
        $form->setHtmlIdPrefix('emailcampaign_vars_');
        $fieldset = $form->addFieldset('emailcampaign_target_form', array('legend'=>Mage::helper('adfab_emailcampaign')->__('Campaign Variables')));
        
        $options = array();
        for ($i = 0; $i < 24; $i++) $options[] = $i;
        
        $fieldset->addField('variables_hour', 'select', array(
            'label'     => Mage::helper('adfab_emailcampaign')->__('Send At (hour)'),
            'class'     => 'required-entry',
            'required'  => true,
            'name'      => 'variables[hour]',
            'options'   => $options
        ));
        
        $options = array();
        for ($i = 0; $i < 60; $i+= 5) $options["$i"] = $i;
        
        $fieldset->addField('variables_minute', 'select', array(
            'label'     => Mage::helper('adfab_emailcampaign')->__('Send At (minute)'),
            'class'     => 'required-entry',
            'required'  => true,
            'name'      => 'variables[minute]',
            'options'   => $options
        ));
        
        if ($campaign = Mage::registry('emailcampaign_data')) {
            $form->setValues($this->_transform($campaign->getVariable()));
        }
        return parent::_prepareForm();
    }
        
}
