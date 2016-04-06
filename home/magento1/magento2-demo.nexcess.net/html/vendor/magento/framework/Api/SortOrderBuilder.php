<?php
/**
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\Framework\Api;


/**
 * Builder for sort order data object.
 *
 */
class SortOrderBuilder extends AbstractSimpleObjectBuilder
{
    /**
     * Set sorting field.
     *
     * @param string $field
     * @return $this
     */
    public function setField($field)
    {
        $this->_set(SortOrder::FIELD, $field);
        return $this;
    }

    /**
     * Set sorting direction.
     *
     * @param string $direction
     * @return $this
     */
    public function setDirection($direction)
    {
        $this->_set(SortOrder::DIRECTION, $direction);
        return $this;
    }

    /**
     * @return $this
     */
    public function setAscendingDirection()
    {
        $this->setDirection(SortOrder::SORT_ASC);
        return $this;
    }

    /**
     * @return $this
     */
    public function setDescendingDirection()
    {
        $this->setDirection(SortOrder::SORT_DESC);
        return $this;
    }
}
