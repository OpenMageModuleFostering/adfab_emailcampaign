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
class Adfab_EmailCampaign_Model_Source_Template
{

    /**
     * Config xpath to email template node
     *
     */
    const XML_PATH_TEMPLATE_EMAIL = 'global/template/email/';
    
    /**
     * Generate list of email templates
     *
     * @return array
     */
    public function toFormOptionArray()
    {
        $result = array();
        $collection = Mage::getResourceModel('core/email_template_collection')->load();
        $options = $collection->toOptionArray();
        $defOptions = Mage_Core_Model_Email_Template::getDefaultTemplatesAsOptionsArray();
        foreach ($defOptions as $v) {
            $options[] = $v;
        }
        foreach ($options as $v) {
            $result[$v['value']] = $v['label'];
        }
        // sort by names alphabetically
        asort($result);
        if (!$asHash) {
            $options = array();
            $options[] = array('value' => '', 'label' => '---------Choose Email Template---------');
            foreach ($result as $k => $v) {
                if ($k == '')
                    continue;
                $options[] = array('value' => $k, 'label' => $v);
            }
    
            $result = $options;
        }
        return $result;
    }
}
