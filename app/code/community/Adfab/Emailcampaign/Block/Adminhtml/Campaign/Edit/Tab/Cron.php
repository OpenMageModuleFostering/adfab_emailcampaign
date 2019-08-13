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
class Adfab_EmailCampaign_Block_Adminhtml_Campaign_Edit_Tab_Cron extends Mage_Adminhtml_Block_Widget_Form
{
    
    protected function _prepareForm()
    {
        $model = Mage::registry('emailcampaign_data');
        $form = new Varien_Data_Form();
        $this->setForm($form);
        $form->setHtmlIdPrefix('emailcampaign_schedule_');
        
        $fieldset = $form->addFieldset('emailcampaign_cron_form', array('legend'=>Mage::helper('adfab_emailcampaign')->__('Campaign Planning')));
        
        $fieldset->addField('variables_cron_expr', 'hidden', array(
            'label'     => Mage::helper('adfab_emailcampaign')->__('Send mail at'),
            'class'     => 'required-entry',
            'required'  => true,
            'name'      => 'variables[cron_expr]',
        ));
        
        $form->getElement('variables_cron_expr')->setRenderer(Mage::app()->getLayout()->createBlock(
            'adfab_emailcampaign/adminhtml_form_edit_renderer_cron'
        ));
        
        if ( $model ) {
            $form->setValues($model->getData());
        } else {
            $form->setValues(array(
                'mode'  => 'test'
            ));
        }
        	
        if (Mage::getSingleton('cms/wysiwyg_config')->isEnabled()) {
            $this->getLayout()->getBlock('head')->setCanLoadTinyMce(true);
        }
    
        return parent::_prepareForm();
    }
        
}
