<?php
/**
 * Magento
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magentocommerce.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Magento to newer
 * versions in the future. If you wish to customize Magento for your
 * needs please refer to http://www.magentocommerce.com for more information.
 *
 * @category    Mage
 * @package     Mage_Adminhtml
 * @copyright   Copyright (c) 2012 Magento Inc. (http://www.magentocommerce.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Coupons generation parameters form
 *
 * @category    Mage
 * @package     Mage_Adminhtml
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Adfab_Emailcampaign_Block_Adminhtml_Campaign_Forms_Coupons
    extends Adfab_Emailcampaign_Block_Adminhtml_Campaign_Forms_Abstract
{
    /**
     * Prepare coupon codes generation parameters form
     *
     * @return Mage_Adminhtml_Block_Widget_Form
     */
    protected function _prepareForm()
    {
        $form = new Varien_Data_Form();

        /**
         * @var Mage_SalesRule_Helper_Coupon $couponHelper
         */
        $couponHelper = Mage::helper('salesrule/coupon');

        $form->setHtmlIdPrefix('emailcampaign_vars_');

        $fieldset = $form->addFieldset('information_fieldset', array('legend'=>Mage::helper('salesrule')->__('Coupons Information')));
        $fieldset->addClass('ignore-validate');
        
        $useRule = $fieldset->addField('variables_coupon_use_rule', 'select', array(
            'label'     => Mage::helper('salesrule')->__('Use Price Rule'),
            'title'     => Mage::helper('salesrule')->__('Use Price Rule'),
            'name'      => 'variables[coupon][use_rule]',
            'required'  => true,
            'values'    => array(
                array(
                    'value'     => 0,
                    'label'     => Mage::helper('adminhtml')->__('No'),
                ),
                array(
                    'value'     => 1,
                    'label'     => Mage::helper('adminhtml')->__('Yes'),
                ),
            ),
        ));
        
        $fieldset->addField('variables_coupon_usage', 'note', array(
            'text'     => Mage::helper('adfab_emailcampaign')->__('You can create a rule exclusively for this campaign <br /> Be careful: when one coupon is generated for a customer, only this one can use it, even if the rule is disabled. <br/> You can use the fields below to customize each generated coupon if the selected rule use auto generation')
        ));
        
        $ruleId = $fieldset->addField('variables_coupon_rule_id', 'select', array(
            'label'    => Mage::helper('adfab_emailcampaign')->__('Rule ID'),
            'name'     => 'variables[coupon][rule_id]',
            'values'   => Mage::getSingleton('adfab_emailcampaign/source_salesRule_rule')->toFormOptionArray(),
            'required' => true,
            'note'     => Mage::helper('adfab_emailcampaign')->__('the rule to use to determine the coupon code')
        ));

        $length = $fieldset->addField('variables_coupon_length', 'text', array(
            'name'     => 'variables[coupon][length]',
            'label'    => Mage::helper('salesrule')->__('Code Length'),
            'title'    => Mage::helper('salesrule')->__('Code Length'),
            'required' => true,
            'note'     => Mage::helper('salesrule')->__('Excluding prefix, suffix and separators.'),
            'value'    => $couponHelper->getDefaultLength(),
            'class'    => 'validate-digits validate-greater-than-zero'
        ));

        $format = $fieldset->addField('variables_coupon_format', 'select', array(
            'label'    => Mage::helper('salesrule')->__('Code Format'),
            'name'     => 'variables[coupon][format]',
            'options'  => $couponHelper->getFormatsList(),
            'required' => true,
            'value'    => $couponHelper->getDefaultFormat()
        ));

        $prefix = $fieldset->addField('variables_coupon_prefix', 'text', array(
            'name'  => 'variables[coupon][prefix]',
            'label' => Mage::helper('salesrule')->__('Code Prefix'),
            'title' => Mage::helper('salesrule')->__('Code Prefix'),
            'value' => $couponHelper->getDefaultPrefix()
        ));

        $suffix = $fieldset->addField('variables_coupon_suffix', 'text', array(
            'name'  => 'variables[coupon][suffix]',
            'label' => Mage::helper('salesrule')->__('Code Suffix'),
            'title' => Mage::helper('salesrule')->__('Code Suffix'),
            'value' => $couponHelper->getDefaultSuffix()
        ));

        $dash = $fieldset->addField('variables_coupon_dash', 'text', array(
            'name'  => 'variables[coupon][dash]',
            'label' => Mage::helper('salesrule')->__('Dash Every X Characters'),
            'title' => Mage::helper('salesrule')->__('Dash Every X Characters'),
            'note'  => Mage::helper('salesrule')->__('If empty no separation.'),
            'value' => $couponHelper->getDefaultDashInterval(),
            'class' => 'validate-digits'
        ));

        $this->setForm($form);
        
        $this->setChild('form_after', $this->getLayout()->createBlock('adminhtml/widget_form_element_dependence')
            ->addFieldMap($useRule->getHtmlId(), $useRule->getName())
            ->addFieldMap($ruleId->getHtmlId(), $ruleId->getName())
            ->addFieldMap($length->getHtmlId(), $length->getName())
            ->addFieldMap($format->getHtmlId(), $format->getName())
            ->addFieldMap($prefix->getHtmlId(), $prefix->getName())
            ->addFieldMap($suffix->getHtmlId(), $suffix->getName())
            ->addFieldMap($dash->getHtmlId(), $dash->getName())
            ->addFieldDependence(
                $ruleId->getName(),
                $useRule->getName(),
                1
            )->addFieldDependence(
                $length->getName(),
                $useRule->getName(),
                1
            )->addFieldDependence(
                $format->getName(),
                $useRule->getName(),
                1
            )->addFieldDependence(
                $prefix->getName(),
                $useRule->getName(),
                1
            )->addFieldDependence(
                $suffix->getName(),
                $useRule->getName(),
                1
            )->addFieldDependence(
                $dash->getName(),
                $useRule->getName(),
                1
            )
        );

        Mage::dispatchEvent('adminhtml_promo_quote_edit_tab_coupons_form_prepare_form', array('form' => $form));
        
        if ($campaign = Mage::registry('emailcampaign_data')) {
            $form->setValues($this->_transform($campaign->getVariable()));
        }

        return parent::_prepareForm();
    }

    /**
     * Retrieve URL to Generate Action
     *
     * @return string
     */
    public function getGenerateUrl()
    {
        return $this->getUrl('*/*/generate');
    }
}
