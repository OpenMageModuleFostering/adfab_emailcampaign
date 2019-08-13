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
 * Cron Selector
 * Need jQuery and jquery-cron to work properly
 *
 * @category   Adfab
 * @package    Adfab_Emailcampaign
 * @subpackage Block
 * @author     Arnaud Hours <arnaud.hours@adfab.fr>
 */
class Adfab_Emailcampaign_Block_Adminhtml_Form_Edit_Renderer_Cron extends Mage_Adminhtml_Block_Widget implements Varien_Data_Form_Element_Renderer_Interface
{
 
   /**
    * TODO this method must be generic
    * 
    * @param Varien_Data_Form_Element_Abstract $element
    */
    public function render(Varien_Data_Form_Element_Abstract $element)
    {
        $element->setClass('input-text');
        $element->setRequired(true);
        
        $cron = '0 8 * * *';
        if ($campaign = Mage::registry('emailcampaign_data')) {
            if ($campaign->getVariable('cron_expr')) {
                $cron = $campaign->getVariable('cron_expr');
            }
        }
        
        $html = '<tr>';
        $html .= '<td class="label">' . $element->getLabelHtml() . '</td><td class="value">';
        $html .= '<div id="cron-selector"></div>';
        $html .= $element->getElementHtml();
        $html .= '<script type="text/javascript">';
        $html .= 'jQuery(document).ready(function() {';
        $html .= 'jQuery(\'#cron-selector\').cron({';
        $html .= 'initial: "'.$cron.'", onChange: function(){jQuery(\'#'.$element->getHtmlId().'\').val(jQuery(this).cron("value"));}';
        $html .= '});';
        $html .= '});';
        $html .= '</script>';
        
        $html .= '</td></tr>' . "\n";
        return $html;
    }
}
