<?php
/**
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magento\Braintree\Block\Adminhtml\Form\Field;

class Countrycreditcard extends \Magento\Config\Block\System\Config\Form\Field\FieldArray\AbstractFieldArray
{
    /**
     * @var \Magento\Braintree\Block\Adminhtml\Form\Field\Countries
     */
    protected $countryRenderer = null;

    /**
     * @var \Magento\Braintree\Block\Adminhtml\Form\Field\CcTypes
     */
    protected $ccTypesRenderer = null;
    
    /**
     * Returns renderer for country element
     * 
     * @return \Magento\Braintree\Block\Adminhtml\Form\Field\Countries
     */
    protected function getCountryRenderer()
    {
        if (!$this->countryRenderer) {
            $this->countryRenderer = $this->getLayout()->createBlock(
                '\Magento\Braintree\Block\Adminhtml\Form\Field\Countries',
                '',
                ['data' => ['is_render_to_js_template' => true]]
            );
        }
        return $this->countryRenderer;
    }

    /**
     * Returns renderer for country element
     * 
     * @return \Magento\Braintree\Block\Adminhtml\Form\Field\Cctypes
     */
    protected function getCcTypesRenderer()
    {
        if (!$this->ccTypesRenderer) {
            $this->ccTypesRenderer = $this->getLayout()->createBlock(
                '\Magento\Braintree\Block\Adminhtml\Form\Field\Cctypes',
                '',
                ['data' => ['is_render_to_js_template' => true]]
            );
        }
        return $this->ccTypesRenderer;
    }

    /**
     * Prepare to render
     * @return void
     */
    protected function _prepareToRender()
    {
        $this->addColumn(
            'country_id',
            [
                'label'     => __('Country'),
                'renderer'  => $this->getCountryRenderer(),
            ]
        );
        $this->addColumn(
            'cc_types',
            [
                'label' => __('Allowed Credit Card Types'),
                'renderer'  => $this->getCcTypesRenderer(),
            ]
        );
        $this->_addAfter = false;
        $this->_addButtonLabel = __('Add Rule');
    }

    /**
     * Prepare existing row data object
     *
     * @param \Magento\Framework\DataObject $row
     * @return void
     */
    protected function _prepareArrayRow(\Magento\Framework\DataObject $row)
    {
        $country = $row->getCountryId();
        $options = [];
        if ($country) {
            $options['option_' . $this->getCountryRenderer()->calcOptionHash($country)]
                = 'selected="selected"';

            $ccTypes = $row->getCcTypes();
            if (!is_array($ccTypes)) {
                $ccTypes = [$ccTypes];
            }
            foreach ($ccTypes as $cardType) {
                $options['option_' . $this->getCcTypesRenderer()->calcOptionHash($cardType)]
                    = 'selected="selected"';
            }
        }
        $row->setData('option_extra_attrs', $options);
        return;
    }
}
