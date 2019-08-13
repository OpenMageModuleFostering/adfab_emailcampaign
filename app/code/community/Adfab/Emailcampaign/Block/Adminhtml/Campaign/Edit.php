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
class Adfab_Emailcampaign_Block_Adminhtml_Campaign_Edit extends Mage_Adminhtml_Block_Widget_Form_Container
{
    
    public function __construct()
    {
        parent::__construct();
         
        $this->_objectId = 'id';
        $this->_blockGroup = 'adfab_emailcampaign';
        $this->_controller = 'adminhtml_campaign';
    
        $this->_updateButton('save', 'label', Mage::helper('adfab_emailcampaign')->__('Save Campaign'));
        $this->_updateButton('delete', 'label', Mage::helper('adfab_emailcampaign')->__('Delete Campaign'));
    
        $this->_addButton('saveandcontinue', array(
            'label'     => Mage::helper('adminhtml')->__('Save And Continue Edit'),
            'onclick'   => 'saveAndContinueEdit()',
            'class'     => 'save',
        ), -100);
    
        $this->_formScripts[] = "
            function saveAndContinueEdit(){
                editForm.submit($('edit_form').action+'back/edit/');
            }
        ";
    }
    
    public function getHeaderText()
    {
        if( Mage::registry('emailcampaign_data') && Mage::registry('emailcampaign_data')->getId() ) {
            return Mage::helper('adfab_emailcampaign')->__("Edit Campaign '%s'", $this->htmlEscape(Mage::registry('emailcampaign_data')->getTitle()));
        } else {
            return Mage::helper('adfab_emailcampaign')->__('Add Campaign');
        }
    }
    
}
