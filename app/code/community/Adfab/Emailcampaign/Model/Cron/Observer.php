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
 * This is not a rewrite, its a cronjob in crontab area event
 * Methods are overloded from magento ce 1.7.0.2
 *
 * @category   Adfab
 * @package    Adfab_Emailcampaign
 * @subpackage Model
 * @author     Arnaud Hours <arnaud.hours@adfab.fr>
 */
class Adfab_Emailcampaign_Model_Cron_Observer extends Mage_Cron_Model_Observer
{
    
    const EMAILCAMPAIGN_CACHE_KEY_LAST_SCHEDULE_GENERATE_AT = 'emailcampaign_cron_last_history_cleanup_at';
    
    protected $_configElement;
    
    protected $_campaigns;
    
    /**
     * Process cron queue
     * Geterate tasks schedule
     * Cleanup tasks schedule
     *
     * @param Varien_Event_Observer $observer
     */
    public function dispatch($observer)
    {
        $schedules = $this->getPendingSchedules();
        $scheduleLifetime = Mage::getStoreConfig(self::XML_PATH_SCHEDULE_LIFETIME) * 60;
        $now = time();
        $jobsRoot = $this->_buildCampaignXmlJobs();
    
        foreach ($schedules->getIterator() as $schedule) {
            $jobConfig = $jobsRoot->{$schedule->getJobCode()};
            if (!$jobConfig || !$jobConfig->run) {
                continue;
            }
    
            $runConfig = $jobConfig->run;
            $time = strtotime($schedule->getScheduledAt());
            if ($time > $now) {
                continue;
            }
            try {
                $errorStatus = Mage_Cron_Model_Schedule::STATUS_ERROR;
//                 $errorMessage = Mage::helper('cron')->__('Unknown error.');
    
                if ($time < $now - $scheduleLifetime) {
                    $errorStatus = Mage_Cron_Model_Schedule::STATUS_MISSED;
                    Mage::throwException(Mage::helper('cron')->__('Too late for the schedule.'));
                }
    
                if ($runConfig->model) {
                    /*if (!preg_match(self::REGEX_RUN_MODEL, (string)$runConfig->model, $run)) {
                        Mage::throwException(Mage::helper('cron')->__('Invalid model/method definition, expecting "model/class::method".'));
                    }
                    if (!($model = Mage::getModel($run[1])) || !method_exists($model, $run[2])) {
                        Mage::throwException(Mage::helper('cron')->__('Invalid callback: %s::%s does not exist', $run[1], $run[2]));
                    }*/
                    if (!($model = Mage::getModel((string)$runConfig->model))) {
                        Mage::throwException(Mage::helper('cron')->__('Invalid callback: %s::process does not exist', $run[1]));
                    }
                    $campaign = $this->_getCampaignById((int)$jobConfig->id);
                    $callback = array($model, 'process');
                    $arguments = array($campaign);
                }
                if (empty($callback)) {
                    Mage::throwException(Mage::helper('cron')->__('No callbacks found'));
                }
    
                if (!$schedule->tryLockJob()) {
                    // another cron started this job intermittently, so skip it
                    continue;
                }
                /**
                 though running status is set in tryLockJob we must set it here because the object
                 was loaded with a pending status and will set it back to pending if we don't set it here
                 */
                $schedule
                ->setStatus(Mage_Cron_Model_Schedule::STATUS_RUNNING)
                ->setExecutedAt(strftime('%Y-%m-%d %H:%M:%S', time()))
                ->save();
                
                Mage::dispatchEvent('emailcampaign_campaign_process_before', array('campaign' => $campaign, 'model' => $model));
                call_user_func_array($callback, $arguments);
                $model->updateLastRunDate();
                Mage::dispatchEvent('emailcampaign_campaign_process_after', array('campaign' => $campaign, 'model' => $model));
                $campaign->save();
    
                $schedule
                ->setStatus(Mage_Cron_Model_Schedule::STATUS_SUCCESS)
                ->setFinishedAt(strftime('%Y-%m-%d %H:%M:%S', time()));
                
                $this->_addAdminhtmlSuccessMessage($campaign, $model);
    
            } catch (Exception $e) {
                $schedule->setStatus($errorStatus)
                ->setMessages($e->__toString());
                $this->_addAdminhtmlErrorMessage($campaign);
            }
            $schedule->save();
        }
    
        $this->generate();
//         $this->cleanup();
    }
    
    /**
     * Generate cron schedule
     *
     * @return Mage_Cron_Model_Observer
     */
    public function generate()
    {
        /**
         * check if schedule generation is needed
         */
        $lastRun = Mage::app()->loadCache(self::EMAILCAMPAIGN_CACHE_KEY_LAST_SCHEDULE_GENERATE_AT);
        if ($lastRun > time() - Mage::getStoreConfig(self::XML_PATH_SCHEDULE_GENERATE_EVERY)*60) {
            return $this;
        }
    
        $schedules = $this->getPendingSchedules();
        $exists = array();
        foreach ($schedules->getIterator() as $schedule) {
            $exists[$schedule->getJobCode().'/'.$schedule->getScheduledAt()] = 1;
        }
    
        $config = $this->_buildCampaignXmlJobs();
        if ($config instanceof Mage_Core_Model_Config_Element) {
            $this->_generateJobs($config->children(), $exists);
        }
    
        /**
         * save time schedules generation was ran with no expiration
         */
        Mage::app()->saveCache(time(), self::EMAILCAMPAIGN_CACHE_KEY_LAST_SCHEDULE_GENERATE_AT, array('crontab'), null);
    
        return $this;
    }
    
    /**
     * 
     * @return Mage_Core_Model_Config_Element
     */
    protected function _buildCampaignXmlJobs()
    {
        if (is_null($this->_configElement)) {
            $this->_configElement = new Mage_Core_Model_Config_Element('<?xml version="1.0" encoding="UTF-8"?><jobs/>');
            foreach ($this->_getCronCampaigns() as $campaign) {
                $node = $this->_configElement->addChild($campaign->getCode());
                
                $run = $node->addChild('run');
                $schedule = $node->addChild('schedule');
                $node->addChild('id', (string)$campaign->getId()); // so we can retrieve it later
                $schedule->addChild('cron_expr', $campaign->getVariable('cron_expr'));
                $run->addChild('model', Mage::helper('adfab_emailcampaign')->getCampaignConfig($campaign->getCode(), 'class'));
            }
        }
        return $this->_configElement;
    }
    
    protected function _getCronCampaigns()
    {
        if (is_null($this->_campaigns)) {
            $this->_campaigns = Mage::helper('adfab_emailcampaign')->getCampaignsByType(Adfab_Emailcampaign_Model_Campaign::TYPE_CRON);
        }
        return $this->_campaigns;
    }
    
    protected function _getCampaignById($id)
    {
        foreach ($this->_getCronCampaigns() as $campaign) {
            if ($id == $campaign->getId()) {
                return $campaign;
            }
        }
        return false;
    }
    
    protected function _addAdminhtmlSuccessMessage($campaign, $modelInstance)
    {
        if (!$modelInstance) return;
        $collection = $modelInstance->getCustomerList();
        $msg = '';
        $url = Mage::helper("adminhtml")->getUrl('/campaign/index');
        if ($count = $collection->count()) {
            $msg = Mage::helper('adfab_emailcampaign')->__('Campaign <a href="%s">%s</a> was processed and has targeted %s customer(s)', $url, $campaign->getTitle(), $count);
        } else {
            $msg = Mage::helper('adfab_emailcampaign')->__('Campaign <a href="%s">%s</a> was processed but no one was targeted', $url, $campaign->getTitle());
        }
        Mage::getModel('adfab_emailcampaign/adminhtml_notification')
                ->setContent($msg)
                ->setType('success')
                ->save();
    }
    
    protected function _addAdminhtmlErrorMessage($campaign)
    {
        $msg = '';
        $url = Mage::helper("adminhtml")->getUrl('/campaign/index');
        $msg = Mage::helper('adfab_emailcampaign')->__('Campaign <a href="%s">%s</a> has failed, see logs for more details', $url, $campaign->getTitle());
        Mage::getModel('adfab_emailcampaign/adminhtml_notification')
                ->setContent($msg)
                ->setType('error')
                ->save();
    }
    
}
