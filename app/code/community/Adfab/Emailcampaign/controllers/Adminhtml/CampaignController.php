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
class Adfab_EmailCampaign_Adminhtml_CampaignController extends Mage_Adminhtml_Controller_Action
{

    protected function _initAction()
    {
        $this->loadLayout()
            ->_setActiveMenu('emailcampaign/items')
            ->_addBreadcrumb(Mage::helper('adminhtml')->__('Campaigns Manager'), Mage::helper('adminhtml')->__('Campaign Manager'));
        
        return $this;
    }

    public function indexAction()
    {
        $this->_initAction();
        $this->renderLayout();
    }

    public function editAction()
    {
        $id = $this->getRequest()->getParam('id');
        $model = Mage::getModel('adfab_emailcampaign/campaign')->load($id);
        
        if ($model->getId() || $id == 0) {
            $data = Mage::getSingleton('adminhtml/session')->getFormData(true);
            if (! empty($data)) {
                $model->setData($data);
            }
            
            Mage::register('emailcampaign_data', $model);
            
            $this->loadLayout();
            $this->_setActiveMenu('emailcampaign/items');
            
            $this->_addBreadcrumb(Mage::helper('adminhtml')->__('Campaign Manager'), Mage::helper('adminhtml')->__('Campaign Manager'));
            $this->_addBreadcrumb(Mage::helper('adminhtml')->__('Campaign News'), Mage::helper('adminhtml')->__('Campaign News'));
            
            $this->getLayout()
                ->getBlock('head')
                ->setCanLoadExtJs(true);
            if (Mage::getSingleton('cms/wysiwyg_config')->isEnabled()) {
                $this->getLayout()
                    ->getBlock('head')
                    ->setCanLoadTinyMce(true);
            }
            
            $this->_addContent($this->getLayout()
                ->createBlock('adfab_emailcampaign/adminhtml_campaign_edit'))
                ->_addLeft($this->getLayout()
                ->createBlock('adfab_emailcampaign/adminhtml_campaign_edit_tabs'));
            
            $this->renderLayout();
        } else {
            Mage::getSingleton('adminhtml/session')->addError(Mage::helper('adfab_emailcampaign')->__('Campaign does not exist'));
            $this->_redirect('*/*/');
        }
    }

    public function newAction()
    {
        $this->_forward('edit');
    }

    public function saveAction()
    {
        if ($data = $this->getRequest()->getPost()) {
            /* @var $model Adfab_EmailCampaign_Model_Campaign */
            $model = Mage::getModel('adfab_emailcampaign/campaign');
            if (isset($data['variables']) && is_array($data['variables'])) {
                $model->setVariable($data['variables']);
            }
            $model->setData($data)
                ->setId($this->getRequest()->getParam('id'));
            
            $class = (string)Mage::helper('adfab_emailcampaign')->getCampaignConfig($model->getCode(), 'class');
            if (!empty($class) && ($class = Mage::getSingleton($class))) {
                if (method_exists($class, 'beforeCampaignSave')) {
                    $class->beforeCampaignSave($model);
                }
            }
            
            try {
                if (!$model->getId()) {
                    $model->setCreatedAt(now())->setUpdatedAt(now());
                } else {
                    $model->setUpdatedAt(now());
                }
                
                $model->save();
                Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('adfab_emailcampaign')->__('Campaign was successfully saved'));
                Mage::getSingleton('adminhtml/session')->setFormData(false);
                
                if ($this->getRequest()->getParam('back')) {
                    $this->_redirect('*/*/edit', array(
                        'id' => $model->getId()
                    ));
                    return;
                }
                $this->_redirect('*/*/');
                return;
            } catch (Exception $e) {
                Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
                Mage::getSingleton('adminhtml/session')->setFormData($data);
                $this->_redirect('*/*/edit', array(
                    'id' => $this->getRequest()
                        ->getParam('id')
                ));
                return;
            }
        }
        Mage::getSingleton('adminhtml/session')->addError(Mage::helper('adfab_emailcampaign')->__('Unable to find item to save'));
        $this->_redirect('*/*/');
    }

    public function processAction()
    {
        $campaign = Mage::getModel('adfab_emailcampaign/campaign');
        $campaign->load($this->getRequest()
            ->getParam('id'));
        
        if (! $campaign) {
            Mage::getSingleton('adminhtml/session')->addError(Mage::helper('adfab_emailcampaign')->__('Unable to find item'));
            $this->_redirect('*/*/');
        }
        
        $class = Mage::getModel(
            (string)Mage::helper('adfab_emailcampaign')->getCampaignConfig($campaign->getCode(), 'class')
        );
        
        if (!$class) {
            Mage::getSingleton('adminhtml/session')->addError(Mage::helper('adfab_emailcampaign')->__('Class was not found'));
            return $this->_redirect('*/*/');
        }
        
        Mage::dispatchEvent('emailcampaign_campaign_process_before', array('campaign' => $campaign, 'model' => $class));
        $class->process($campaign);
        $class->updateLastRunDate();
        Mage::dispatchEvent('emailcampaign_campaign_process_after', array('campaign' => $campaign, 'model' => $class));
        $campaign->save();

        $customers = $class->getCustomerList();
        $msg = null;
        if ($customers->count() > 10) {
            $msg = Mage::helper('adfab_emailcampaign')->__('Test email was sent to %i addresses', $customers->getSize());
        } else if ($customers->count() > 0) {
            $emails = array();
            foreach ($customers as $customer) {
                $emails[] = $customer->getEmail();
            }
            $msg = Mage::helper('adfab_emailcampaign')->__('Email was sent to %s', implode(', ', $emails));
        } else {
            $msg = Mage::helper('adfab_emailcampaign')->__('No customer match this campaign at this time');
        }
        
        Mage::getSingleton('adminhtml/session')->addSuccess($msg);
        $this->_redirect('*/*/');
    }
    
    public function processTestAction()
    {
        $campaign = Mage::getModel('adfab_emailcampaign/campaign');
        $campaign->load($this->getRequest()
            ->getParam('id'));
    
        if (! $campaign) {
            Mage::getSingleton('adminhtml/session')->addError(Mage::helper('adfab_emailcampaign')->__('Unable to find item'));
            $this->_redirect('*/*/');
        }
    
        $class = Mage::getModel(
            (string)Mage::helper('adfab_emailcampaign')->getCampaignConfig($campaign->getCode(), 'class')
        );
    
        if (!$class) {
            Mage::getSingleton('adminhtml/session')->addError(Mage::helper('adfab_emailcampaign')->__('Class was not found'));
            return $this->_redirect('*/*/');
        }
        
        $oldMode = $campaign->getMode();
        $campaign->setMode(Adfab_EmailCampaign_Model_Source_Mode::TEST_ALL_EMAIL);
        Mage::dispatchEvent('emailcampaign_campaign_process_before', array('campaign' => $campaign, 'model' => $class));
        $class->process($campaign);
        Mage::dispatchEvent('emailcampaign_campaign_process_after', array('campaign' => $campaign, 'model' => $class));
        $customers = $class->getCustomerList();
        $campaign->setMode($oldMode)->save();
        $msg = null;
        if ($customers->count() > 10) {
            $msg = Mage::helper('adfab_emailcampaign')->__('Test email was sent to %i addresses in test mode', $customers->getSize());
        } else if ($customers->count() > 0) {
            $emails = array();
            foreach ($customers as $customer) {
                $emails[] = $customer->getEmail();
            }
            $msg = Mage::helper('adfab_emailcampaign')->__('Email was sent to %s in test mode', implode(', ', $emails));
        } else {
            $msg = Mage::helper('adfab_emailcampaign')->__('No test email found');
        }
    
        Mage::getSingleton('adminhtml/session')->addSuccess($msg);
        $this->_redirect('*/*/');
    }

    public function deleteAction()
    {
        if ($this->getRequest()->getParam('id') > 0) {
            try {
                $model = Mage::getModel('adfab_emailcampaign/campaign');
                
                $model->setId($this->getRequest()
                    ->getParam('id'))
                    ->delete();
                
                Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('adminhtml')->__('Campaign was successfully deleted'));
                $this->_redirect('*/*/');
            } catch (Exception $e) {
                Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
                $this->_redirect('*/*/edit', array(
                    'id' => $this->getRequest()
                        ->getParam('id')
                ));
            }
        }
        $this->_redirect('*/*/');
    }

    public function massDeleteAction()
    {
        $ids = $this->getRequest()->getParam('emailcampaign');
        if (! is_array($ids)) {
            Mage::getSingleton('adminhtml/session')->addError(Mage::helper('adminhtml')->__('Please select item(s)'));
        } else {
            try {
                foreach ($ids as $id) {
                    $carouselhome = Mage::getModel('adfab_emailcampaign/campaign')->load($id);
                    $carouselhome->delete();
                }
                Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('adminhtml')->__('Total of %d record(s) were successfully deleted', count($ids)));
            } catch (Exception $e) {
                Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
            }
        }
        $this->_redirect('*/*/index');
    }

    public function massStatusAction()
    {
        $ids = $this->getRequest()->getParam('emailcampaign');
        if (! is_array($ids)) {
            Mage::getSingleton('adminhtml/session')->addError($this->__('Please select item(s)'));
        } else {
            try {
                foreach ($ids as $id) {
                    Mage::getSingleton('adfab_emailcampaign/campaign')->setId($id)
                        ->setStatus($this->getRequest()
                        ->getParam('status'))
                        ->setIsMassupdate(true)
                        ->save();
                }
                $this->_getSession()->addSuccess($this->__('Total of %d record(s) were successfully updated', count($ids)));
            } catch (Exception $e) {
                $this->_getSession()->addError($e->getMessage());
            }
        }
        $this->_redirect('*/*/index');
    }

    public function exportCsvAction()
    {
        $fileName = 'campaigns.csv';
        $content = $this->getLayout()
            ->createBlock('adfab_emailcampaign/adminhtml_campaign_grid')
            ->getCsv();
        
        $this->_sendUploadResponse($fileName, $content);
    }

    public function exportXmlAction()
    {
        $fileName = 'campaigns.xml';
        $content = $this->getLayout()
            ->createBlock('adfab_emailcampaign/adminhtml_campaign_grid')
            ->getXml();
        
        $this->_sendUploadResponse($fileName, $content);
    }

    protected function _sendUploadResponse($fileName, $content, $contentType = 'application/octet-stream')
    {
        $response = $this->getResponse();
        $response->setHeader('HTTP/1.1 200 OK', '');
        $response->setHeader('Pragma', 'public', true);
        $response->setHeader('Cache-Control', 'must-revalidate, post-check=0, pre-check=0', true);
        $response->setHeader('Content-Disposition', 'attachment; filename=' . $fileName);
        $response->setHeader('Last-Modified', date('r'));
        $response->setHeader('Accept-Ranges', 'bytes');
        $response->setHeader('Content-Length', strlen($content));
        $response->setHeader('Content-type', $contentType);
        $response->setBody($content);
        $response->sendResponse();
        die();
    }
    
    public function recipientAction()
    {
        if (! ($id = $this->getRequest()->getParam('id', false))) {
            return $this->_redirect('*/*/');
        }
        $campaign = Mage::getModel('adfab_emailcampaign/campaign')->load($id);
        if (!$campaign->getId()) {
            return $this->_redirect('*/*/');
        }
        if ('cron' != Mage::helper('adfab_emailcampaign')->getCampaignConfig($campaign->getCode(), 'type')) {
            Mage::getSingleton('adminhtml/session')->addError(Mage::helper('adfab_emailcampaign')->__('This action is only available for cron campaigns'));
            return $this->_redirect('*/*/');
        }
        Mage::register('current_campaign', $campaign);
        $this->_initAction();
        $this->renderLayout();
    }
    
}