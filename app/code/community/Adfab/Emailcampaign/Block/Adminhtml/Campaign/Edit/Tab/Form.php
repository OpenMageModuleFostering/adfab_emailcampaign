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
class Adfab_EmailCampaign_Block_Adminhtml_Campaign_Edit_Tab_Form extends Mage_Adminhtml_Block_Widget_Form
{
    
    protected function _prepareForm()
    {
        $model = Mage::registry('emailcampaign_data');
        $form = new Varien_Data_Form();
        $this->setForm($form);
        $form->setHtmlIdPrefix('emailcampaign_');
        $fieldset = $form->addFieldset('emailcampaign_form', array('legend'=>Mage::helper('adfab_emailcampaign')->__('Campaign information')));
        
        /*
		if (!Mage::app()->isSingleStoreMode()) {
			$fieldset->addField('store_id', 'multiselect', array(
					'name'      => 'stores[]',
					'label'     => Mage::helper('adfab_emailcampaign')->__('Store View'),
					'title'     => Mage::helper('adfab_emailcampaign')->__('Store View'),
					'required'  => true,
					'values'    => Mage::getSingleton('adminhtml/system_store')->getStoreValuesForForm(false, false),
			));
		}
		else {
			$fieldset->addField('store_id', 'hidden', array(
					'name'      => 'stores[]',
					'value'     => Mage::app()->getStore(true)->getId()
			));
			$model->setStoreId(Mage::app()->getStore(true)->getId());
		}
		*/
    
        $fieldset->addField('title', 'text', array(
            'label'     => Mage::helper('adfab_emailcampaign')->__('Title'),
            'class'     => 'required-entry',
            'required'  => true,
            'name'      => 'title',
        ));
        
        $fieldset->addField('mode', 'select', array(
            'label'     => Mage::helper('adfab_emailcampaign')->__('Mode'),
            'class'     => 'required-entry',
            'required'  => true,
            'name'      => 'mode',
            'values'    => Mage::getSingleton('adfab_emailcampaign/source_mode')->toOptionArray(),
        ));
    
        $fieldset->addField('status', 'select', array(
            'label'     => Mage::helper('adfab_emailcampaign')->__('Status'),
            'name'      => 'status',
            'required'  => true,
            'values'    => array(
                array(
                    'value'     => Adfab_EmailCampaign_Model_Status::STATUS_ENABLED,
                    'label'     => Mage::helper('adminhtml')->__('Enabled'),
                ),
    
                array(
                    'value'     => Adfab_EmailCampaign_Model_Status::STATUS_DISABLED,
                    'label'     => Mage::helper('adminhtml')->__('Disabled'),
                ),
            ),
        ));
        
        $campaigns = Mage::getSingleton('adfab_emailcampaign/source_campaign')->toOptionArray();

        $code = $fieldset->addField('code', 'select', array(
            'label'     => Mage::helper('adfab_emailcampaign')->__('Event'),
            'name'      => 'code',
            'required'  => true,
            'values'    => $campaigns,
            'note'      => $this->__('if you change this value, please save and continue edit before editing campaign variables')
        ));
        
        $fieldset->addField('template_id', 'select', array(
            'label'     => Mage::helper('core')->__('Template'),
            'name'      => 'template_id',
            'required'  => true,
            'values'    => Mage::getSingleton('adfab_emailcampaign/source_template')->toFormOptionArray()
        ));
        
        $id = $model ? $model->getTemplateId() : 1;
        
        $fieldset->addField('preview', 'note', array(
            'text'     => '<a target="_blank" href="'.Mage::helper("adminhtml")->getUrl('/system_email_template/preview/id/'.$id).'">'.$this->__('Preview').'</a>'
        ));
        
        $fieldset->addField('create_template', 'note', array(
            'text'     => '<a target="_blank" href="'.Mage::helper("adminhtml")->getUrl('/system_email_template/new').'">'.$this->__('Create template').'</a>'
        ));

        /**
         * Adfab Logo. This module is open source and we just ask you to let this logo for a very passive ad :)
         */
        $fieldset = $form->addFieldset('emailcampaign_usage_form', array('class' => 'fieldset-wide', 'legend'=>Mage::helper('adfab_emailcampaign')->__('Campaign Usage') . ' offered by <a href="http://www.adfab.fr"><img src="http://community.adfab.fr/logo.gif" style="vertical-align: middle;"/></a>'));
       
        foreach ($campaigns as $campaign) {

            $usage = $fieldset->addField('usage_' . $campaign['value'], 'note', array(
                'text'     => $campaign['usage']
            ));
            $warning = $fieldset->addField('warning_' . $campaign['value'], 'note', array(
                'text'     => $campaign['warning']
            ));            
        }
        
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
