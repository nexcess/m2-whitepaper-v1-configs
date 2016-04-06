<?php
/**
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magento\Backend\Block\Widget\Grid\Column\Renderer;

use Magento\Framework\Stdlib\DateTime\DateTimeFormatterInterface;

/**
 * Backend grid item renderer date
 */
class Date extends \Magento\Backend\Block\Widget\Grid\Column\Renderer\AbstractRenderer
{
    /**
     * @var int
     */
    protected $_defaultWidth = 160;

    /**
     * Date format string
     *
     * @var string
     */
    protected static $_format = null;

    /**
     * @var DateTimeFormatterInterface
     */
    protected $dateTimeFormatter;

    /**
     * @param \Magento\Backend\Block\Context $context
     * @param DateTimeFormatterInterface $dateTimeFormatter
     * @param array $data
     */
    public function __construct(
        \Magento\Backend\Block\Context $context,
        DateTimeFormatterInterface $dateTimeFormatter,
        array $data = []
    ) {
        parent::__construct($context, $data);
        $this->dateTimeFormatter = $dateTimeFormatter;
    }

    /**
     * Retrieve date format
     *
     * @return string
     */
    protected function _getFormat()
    {
        $format = $this->getColumn()->getFormat();
        if (!$format) {
            if (self::$_format === null) {
                try {
                    self::$_format = $this->_localeDate->getDateFormat(
                        \IntlDateFormatter::MEDIUM
                    );
                } catch (\Exception $e) {
                    $this->_logger->critical($e);
                }
            }
            $format = self::$_format;
        }
        return $format;
    }

    /**
     * Renders grid column
     *
     * @param   \Magento\Framework\DataObject $row
     * @return  string
     */
    public function render(\Magento\Framework\DataObject $row)
    {
        if ($data = $row->getData($this->getColumn()->getIndex())) {
            $timezone = $this->getColumn()->getTimezone() !== false ? $this->_localeDate->getConfigTimezone() : 'UTC';
            if (!($data instanceof \DateTime)) {
                $localeDate = new \DateTime($data, new \DateTimeZone($timezone));
            } else {
                $data->setTimezone(new \DateTimeZone($timezone));
                $localeDate = $data;
            }
            return $this->dateTimeFormatter->formatObject(
                $this->_localeDate->date(
                    $localeDate
                ),
                $this->_getFormat()
            );
        }
        return $this->getColumn()->getDefault();
    }
}
