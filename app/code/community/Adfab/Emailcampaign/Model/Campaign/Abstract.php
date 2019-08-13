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
abstract class Adfab_Emailcampaign_Model_Campaign_Abstract
{
    
    /**
     * state pattern
     * @var Adfab_Emailcampaign_Model_Campaign_State_Abstract
     */
    private $_state;
    
    /**
     * set this to true will change connector to void and send nothing
     * @var bool
     */
    private $_void = false;
    
    /**
     * @var Adfab_Emailcampaign_Model_Connector_Abstract
     */
    protected $_connector;
    
    /**
     * @var Adfab_Emailcampaign_Model_Campaign
     */
    protected $_campaign;
    
    /**
     * @var int
     */
    protected $_processTime;
    
    protected function _init(Adfab_Emailcampaign_Model_Campaign $campaign)
    {
        $this->_campaign = $campaign;
        $this->_state = Mage::getSingleton('adfab_emailcampaign/campaign_state_' . $campaign->getMode());
        if (!$this->_state) {
            Mage::throwException('invalid state ' . $model);
        }
        if ($this->isVoid()) {
            $this->_connector = Mage::helper('adfab_emailcampaign/connector')->getVoidConnector();
        } else {
            $this->_connector = Mage::helper('adfab_emailcampaign/connector')->getConnector();
        }
        $this->_processTime = time();
    }
    
    /**
     * 
     * @param Adfab_Emailcampaign_Model_Campaign $campaign
     * @param Mage_Customer_Model_Resource_Customer_Collection $customer
     * @param array $data
     */
    public function sendMail(Mage_Customer_Model_Resource_Customer_Collection $customers, $data)
    {
        if (!$this->_connector || !$this->_campaign) {
            Mage::throwException('you must call _init method first');
        }
        $this->_connector->sendMail($this->_campaign, $customers, $data);
    }
    
    /**
     * 
     * @return Mage_Customer_Model_Resource_Customer_Collection
     */
    public function getCustomerList()
    {
        if (!$this->_state) {
            Mage::throwException('you must call _init method first');
        }
        return $this->_state->getCustomerList();
    }
    
    /**
     * return html which explain how the campaign works
     * 
     * @return string
     */
    public function getCampaignUsage()
    {
        return '';
    }
    
    public function getCampaignWarning()
    {
        return '';
    }  
        
    /**
     * @param bool $void
     * @return Adfab_Emailcampaign_Model_Campaign_Abstract
     */
    public function setIsVoid($void)
    {
        $this->_void = $void;
        return $this;
    }
    
    /**
     * @return bool
     */
    public function isVoid()
    {
        return $this->_void;
    }
    
    /**
     * update campaign last run in campaign variables
     * @return Adfab_Emailcampaign_Model_Campaign_Abstract
     */
    public function updateLastRunDate()
    {
        $this->_campaign->setVariable('last_run', $this->_processTime ? $this->_processTime : time());
        return $this;
    }
    
}
