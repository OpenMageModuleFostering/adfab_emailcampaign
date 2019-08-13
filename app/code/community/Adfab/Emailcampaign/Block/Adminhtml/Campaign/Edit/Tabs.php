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
class Adfab_Emailcampaign_Block_Adminhtml_Campaign_Edit_Tabs extends Mage_Adminhtml_Block_Widget_Tabs
{
    
    public function __construct()
    {
        parent::__construct();
        $this->setId('emailcampaign_tabs');
        $this->setDestElementId('edit_form');
        $this->setTitle(Mage::helper('adfab_emailcampaign')->__('Campaign Information'));
    }
    
    protected function _beforeToHtml()
    {
        $this->addTab('form_section_info', array(
            'label'     => Mage::helper('adfab_emailcampaign')->__('Campaign Information'),
            'title'     => Mage::helper('adfab_emailcampaign')->__('Campaign Information'),
            'content'   => $this->getLayout()->createBlock('adfab_emailcampaign/adminhtml_campaign_edit_tab_form')->toHtml(),
        ));
        
        if ($campaign = Mage::registry('emailcampaign_data')) {
            $forms = Mage::helper('adfab_emailcampaign')->getCampaignConfig($campaign->getCode(), 'forms');
            if ($forms) {
                $i = 1;
                foreach ($forms->children() as $form) {
                    $this->addTab('form_section_'.$i++, array(
                        'label'     => isset($form->label) ? (string)$form->label : Mage::helper('adfab_emailcampaign')->__('Campaign Variables'),
                        'title'     => isset($form->label) ? (string)$form->label : Mage::helper('adfab_emailcampaign')->__('Campaign Variables'),
                        'content'   => $this->getLayout()->createBlock((string)$form->class)->toHtml(),
                    ));
                }
            }
            if ('cron' == (string)Mage::helper('adfab_emailcampaign')->getCampaignConfig($campaign->getCode(), 'type')
                && '0' !== (string)Mage::helper('adfab_emailcampaign')->getCampaignConfig($campaign->getCode(), 'display_cron')) {
                $this->addTab('form_section_cron', array(
                    'label'     => Mage::helper('adfab_emailcampaign')->__('Campaign Planning'),
                    'title'     => Mage::helper('adfab_emailcampaign')->__('Campaign Planning'),
                    'content'   => $this->getLayout()->createBlock('adfab_emailcampaign/adminhtml_campaign_edit_tab_cron')->toHtml(),
                ));
            }
        }
         
        return parent::_beforeToHtml();
    }
    
}
