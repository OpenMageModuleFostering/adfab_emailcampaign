<?php
class Adfab_Emailcampaign_Model_Adminhtml_Observer  extends Mage_Core_Model_Abstract {
    
    /**
     * This observer will check that the wysiwyg option is enabled and will set the proper config
     * @param unknown $observer
     */
    function transactionalemail_layout_after($observer) {
        try {
            $controller   = $observer->getAction();

            if($controller->getFullActionName() == 'adminhtml_system_email_template_edit')
            {
                $layout = $controller->getLayout();
                if (Mage::getSingleton('cms/wysiwyg_config')->isEnabled()) {
                    $layout->getBlock('head')->setCanLoadTinyMce(true);
                }
            }
        } catch ( Exception $e ) {
            Mage::log( "transactionalemail_layout_after observer failed: " . $e->getMessage() );
        }
    }
    
    /**
     * This observer will add the wysiwyg feature to transactional emails
     * @param unknown $observer
     */
    function transactionalemail_block_html_before($observer) {
        try {
            $block = $observer->getBlock();
            if (!isset($block)) return;
            
            switch ($block->getType()) {

                case 'adminhtml/system_email_template_edit_form':

                    $form = $block->getForm();
                    $fieldset = $form->getElement('base_fieldset');
                    $emailTemplate = Mage::registry('current_email_template');
                    
                    // TODO : We could add the ability to insert images ?
                    $config = Mage::getSingleton('cms/wysiwyg_config')->getConfig(
                        array('add_widgets'=>false,'add_variables'=>true, 'add_images'=>false)
                    );
                    
                    // this field must appear when wysiwyg is inactive. It's natively done via wysiwyg widget.
                    $fieldset->removeField('insert_variable');
                    $fieldset->removeField('template_text');
                    $fieldset->addField('template_text', 'editor', array(
                        'name'=>'template_text',
                        'label' => Mage::helper('adminhtml')->__('Template Content'),
                        'title' => Mage::helper('adminhtml')->__('Template Content'),
                        'required' => true,
                        'style' => 'height:24em;',
                        'config' => $config,
                        'wysiwyg' => true,
                        'value'   => $emailTemplate->getTemplateText(),
                    ), $form->getElement('template_subject')->getId());
                    
                    break;
                    
                case 'adminhtml/system_email_template_edit':
                    
                    // This template only fix a bug in the original template when a wysiwyg editor is used...
                    $block->setTemplate('emailcampaign/system/email/template/edit.phtml');
                     
                    break;
            }
        } catch ( Exception $e ) {
            Mage::log( "transactionalemail_block_html_before observer failed: " . $e->getMessage() );
        }
    }
}