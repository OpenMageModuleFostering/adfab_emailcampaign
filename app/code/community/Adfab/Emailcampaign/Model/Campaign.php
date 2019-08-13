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
class Adfab_Emailcampaign_Model_Campaign extends Mage_Core_Model_Abstract
{
    
    const TYPE_CRON     = 'cron';
    const TYPE_OBSERVER = 'observer';
    
    protected $_variables = null;
    protected $_variablesLoaded = false;
    
    public function _construct()
    {
        parent::_construct();
        $this->_variables = new Varien_Object();
        $this->_init('adfab_emailcampaign/campaign');
    }
    
    public function setVariable($key, $value = null)
    {
        if (!$this->_variablesLoaded) {
            $this->_initVariables();
        }
        $this->_variables->setData($key, $value);
        $this->setDataChanges(true);
        return $this;
    }
    
    public function getVariable($key = '')
    {
        if (!$this->_variablesLoaded) {
            $this->_initVariables();
        }
        return $this->_variables->getData($key);
    }
    
    protected function _beforeSave()
    {
        parent::_beforeSave();
        if ($this->_variables->hasDataChanges()) {
            $this->setData('variables', serialize($this->_variables->getData()));
        }
    }
    
    protected function _afterLoad()
    {
        parent::_afterLoad();
        $this->_initVariables();
    }
    
    protected function _initVariables()
    {
        $this->_variables->unsetData();
        if ($this->getData('variables')) {
            $this->_variables->addData(unserialize($this->getData('variables')))
                    ->setDataChanges(false);
        }
        $this->_variablesLoaded = true;
    }
    
}
