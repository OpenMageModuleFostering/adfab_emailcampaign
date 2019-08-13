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
 * @subpackage Block
 * @author     Arnaud Hours <arnaud.hours@adfab.fr>
 */
class Adfab_Emailcampaign_Block_Adminhtml_Campaign_Forms_Nursing extends Adfab_Emailcampaign_Block_Adminhtml_Campaign_Forms_Abstract
{
    
    protected function _prepareForm()
    {
        $form = new Varien_Data_Form();
        $this->setForm($form);
        $form->setHtmlIdPrefix('emailcampaign_vars_');
        $fieldset = $form->addFieldset('emailcampaign_target_form', array('legend'=>Mage::helper('adfab_emailcampaign')->__('Campaign Variables')));
        
        $fieldset->addField('variables_send_after_days', 'text', array(
            'label'     => Mage::helper('adfab_emailcampaign')->__('Send mail after how many days ?'),
            'class'     => 'required-entry',
            'required'  => true,
            'name'      => 'variables[send_after_days]',
        ));
        
        $fieldset->addField('variables_resend', 'select', array(
            'label' => Mage::helper('adfab_emailcampaign')->__('Re send mail to customers at next planification if they already received it ?'),
            'class' => 'required-entry',
            'required' => true,
            'name' => 'variables[resend]',
            'value' => '1',
            'values' => array(
                '1' => Mage::helper('core')->__('Yes'),
                '2' => Mage::helper('core')->__('No')
            ),
        ));
        
        if ($campaign = Mage::registry('emailcampaign_data')) {
            $form->setValues($this->_transform($campaign->getVariable()));
        }
        return parent::_prepareForm();
    }
        
}
