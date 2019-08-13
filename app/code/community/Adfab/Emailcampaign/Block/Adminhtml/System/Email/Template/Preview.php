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
 * @subpackage Model
 * @author     Arnaud Hours <arnaud.hours@adfab.fr>
 */
class Adfab_Emailcampaign_Block_Adminhtml_System_Email_Template_Preview extends Mage_Adminhtml_Block_System_Email_Template_Preview
{
    
    /**
     * Prepare html output
     *
     * @return string
     */
    protected function _toHtml()
    {
        $id = $this->getRequest()->getParam('id');
        if (is_numeric($id)) {
            return parent::_toHtml();
        }
        
        // else we can load by code
        
        /** @var $template Mage_Core_Model_Email_Template */
        $template = Mage::getModel('core/email_template');
        if ($id) {
            $localeCode = Mage::getStoreConfig('general/locale/code');
            $template->loadDefault($id);
        } else {
            $template->setTemplateType($this->getRequest()->getParam('type'));
            $template->setTemplateText($this->getRequest()->getParam('text'));
            $template->setTemplateStyles($this->getRequest()->getParam('styles'));
        }
    
        /* @var $filter Mage_Core_Model_Input_Filter_MaliciousCode */
        $filter = Mage::getSingleton('core/input_filter_maliciousCode');
    
        $template->setTemplateText(
            $filter->filter($template->getTemplateText())
        );
    
        Varien_Profiler::start("email_template_proccessing");
        $vars = array();
    
        $templateProcessed = $template->getProcessedTemplate($vars, true);
    
        if ($template->isPlain()) {
            $templateProcessed = "<pre>" . htmlspecialchars($templateProcessed) . "</pre>";
        }
    
        Varien_Profiler::stop("email_template_proccessing");
    
        return $templateProcessed;
    }
    
}
