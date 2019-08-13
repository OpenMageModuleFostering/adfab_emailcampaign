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
class Adfab_Emailcampaign_Block_Adminhtml_Campaign_Grid extends Mage_Adminhtml_Block_Widget_Grid
{
    
    public function __construct()
    {
        parent::__construct();
        $this->setId('emailCampaignGrid');
        $this->setDefaultSort('campaign_id');
        $this->setDefaultDir('DESC');
        $this->setSaveParametersInSession(true);
    }
    
    protected function _prepareCollection()
    {
        $collection = Mage::getModel('adfab_emailcampaign/campaign')->getCollection();
        $this->setCollection($collection);
        parent::_prepareCollection();
        
        // for last run date
        foreach ($collection as $campaign) {
            $lastRun = $campaign->getVariable('last_run');
            if ($lastRun) {
                $value = date('Y-m-d H:i:s', $lastRun);
            } else {
                $value = '';
            }
            $campaign->setLastRun($value);
        }
        
        return $this;
    }
    
    protected function _prepareColumns()
    {
        $this->addColumn('campaign_id', array(
            'header'    => Mage::helper('adfab_emailcampaign')->__('ID'),
            'align'     => 'right',
            'width'     => '50px',
            'index'     => 'campaign_id',
        ));
    
        $this->addColumn('title', array(
            'header'    => Mage::helper('adfab_emailcampaign')->__('Title'),
            'align'     => 'left',
            'index'     => 'title'
        ));
        
        $this->addColumn('code', array(
            'header'    => Mage::helper('adfab_emailcampaign')->__('Event'),
            'align'     => 'left',
            'index'     => 'code',
            'type'      => 'options',
            'options'   => Mage::getSingleton('adfab_emailcampaign/source_campaign')->toFormOptionArray()
        ));
        
        $this->addColumn('last_run', array(
            'header'    => Mage::helper('adfab_emailcampaign')->__('Last Run (Cron)'),
            'align'     => 'left',
            'width'     => '80px',
            'index'     => 'last_run',
            'type'      => 'date',
            'format'    => Mage::app()->getLocale()->getDateFormat(Mage_Core_Model_Locale::FORMAT_TYPE_FULL),
        ));
        
        $this->addColumn('mode', array(
            'header'    => Mage::helper('adfab_emailcampaign')->__('Mode'),
            'align'     => 'left',
            'width'     => '80px',
            'index'     => 'mode',
            'type'      => 'options',
            'options'   => Mage::getSingleton('adfab_emailcampaign/source_mode')->toFormOptionArray()
        ));
    
        $this->addColumn('status', array(
            'header'    => Mage::helper('adfab_emailcampaign')->__('Status'),
            'align'     => 'left',
            'width'     => '80px',
            'index'     => 'status',
            'type'      => 'options',
            'options'   => array(
                2 => Mage::helper('adminhtml')->__('Disabled'),
                1 => Mage::helper('adminhtml')->__('Enabled')
            ),
        ));
    
//         if (!Mage::app()->isSingleStoreMode()) {
//             $this->addColumn('store_id', array(
//                 'header'        => Mage::helper('cms')->__('Store View'),
//                 'index'         => 'store_id',
//                 'type'          => 'store',
//                 'store_all'     => true,
//                 'store_view'    => true,
//                 'sortable'      => false,
//                 'filter_condition_callback'
//                 => array($this, '_filterStoreCondition'),
//             ));
//         }
         
        $this->addColumn('action',
            array(
                'header'    =>  Mage::helper('adfab_emailcampaign')->__('Action'),
                'width'     => '100',
                'type'      => 'action',
                'getter'    => 'getId',
                'actions'   => array(
                    array(
                        'caption'   => Mage::helper('adfab_emailcampaign')->__('Edit'),
                        'url'       => array('base'=> '*/*/edit'),
                        'field'     => 'id'
                    ),
                    array(
                        'caption'   => Mage::helper('adfab_emailcampaign')->__('Send To All Test Emails'),
                        'url'       => array('base'=> '*/*/processTest'),
                        'field'     => 'id'
                    ),
                    array(
                        'caption'   => Mage::helper('adfab_emailcampaign')->__('Process Campaign'),
                        'url'       => array('base'=> '*/*/process'),
                        'field'     => 'id',
                        'confirm'  => Mage::helper('adfab_emailcampaign')->__('Manually run this campaign ?')
                    ),
                    array(
                        'caption'   => Mage::helper('adfab_emailcampaign')->__('View Recipients'),
                        'url'       => array('base'=> '*/*/recipient'),
                        'field'     => 'id'
                    )
                ),
                'filter'    => false,
                'sortable'  => false,
                'index'     => 'stores',
                'is_system' => true,
            ));
    
        $this->addExportType('*/*/exportCsv', Mage::helper('adfab_emailcampaign')->__('CSV'));
        $this->addExportType('*/*/exportXml', Mage::helper('adfab_emailcampaign')->__('XML'));
         
        return parent::_prepareColumns();
    }
    
    protected function _prepareMassaction()
    {
        $this->setMassactionIdField('emailcampaign_id');
        $this->getMassactionBlock()->setFormFieldName('emailcampaign');
    
        $this->getMassactionBlock()->addItem('delete', array(
            'label'    => Mage::helper('adfab_emailcampaign')->__('Delete'),
            'url'      => $this->getUrl('*/*/massDelete'),
            'confirm'  => Mage::helper('adfab_emailcampaign')->__('Are you sure?')
        ));
    
        $statuses = Mage::getSingleton('adfab_emailcampaign/status')->getOptionArray();
    
        array_unshift($statuses, array('label'=>'', 'value'=>''));
        $this->getMassactionBlock()->addItem('status', array(
            'label'=> Mage::helper('adfab_emailcampaign')->__('Change status'),
            'url'  => $this->getUrl('*/*/massStatus', array('_current'=>true)),
            'additional' => array(
                'visibility' => array(
                    'name' => 'status',
                    'type' => 'select',
                    'class' => 'required-entry',
                    'label' => Mage::helper('adfab_emailcampaign')->__('Status'),
                    'values' => $statuses
                )
            )
        ));
        return $this;
    }
    
    public function getRowUrl($row)
    {
        return $this->getUrl('*/*/edit', array('id' => $row->getId()));
    }
    
}
