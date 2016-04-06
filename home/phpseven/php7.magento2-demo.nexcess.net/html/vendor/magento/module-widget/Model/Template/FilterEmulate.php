<?php
/**
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magento\Widget\Model\Template;

class FilterEmulate extends Filter
{
    /**
     * Generate widget with emulation frontend area
     *
     * @param string[] $construction
     * @return string
     */
    public function widgetDirective($construction)
    {
        return $this->_appState->emulateAreaCode('frontend', [$this, 'generateWidget'], [$construction]);
    }
}
