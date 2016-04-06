<?php
/**
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magento\Ui\Component\Form\Element;

/**
 * Class Range
 */
class Range extends AbstractElement
{
    const NAME = 'range';

    /**
     * Get component name
     *
     * @return string
     */
    public function getComponentName()
    {
        return static::NAME;
    }

    /**
     * @return mixed
     */
    public function getType()
    {
        return $this->getData('input_type');
    }
}
