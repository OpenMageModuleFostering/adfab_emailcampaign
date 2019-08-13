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
class Adfab_Emailcampaign_Block_Adminhtml_Notification_Toolbar extends Mage_Adminhtml_Block_Notification_Toolbar
{
    
    protected $_notifications;
    
    /**
     * Check is show toolbar
     *
     * @return bool
     */
    public function isShow()
    {
        if (!$this->isOutputEnabled('Mage_AdminNotification')) {
            return false;
        }
        /*if ($this->getRequest()->getControllerName() == 'notification') {
            return false;
        }
        if ($this->getCriticalCount() == 0 && $this->getMajorCount() == 0 && $this->getMinorCount() == 0
            && $this->getNoticeCount() == 0
        ) {
            return false;
        }*/
    
        return true;
    }
    
    public function getNotifications()
    {
        if (is_null($this->_notifications)) {
            $this->_notifications = Mage::getResourceModel('adfab_emailcampaign/adminhtml_notification_collection')
                ->addFieldToFilter('is_read', array('eq' => 0));
        }
        return $this->_notifications;
    }
    
    public function setIsRead($read = 1)
    {
        if (!is_null($this->_notifications)) {
            foreach ($this->_notifications as $notification) {
                $notification->setIsRead($read);
            }
            $this->_notifications->save();
        }
    }
    
}
