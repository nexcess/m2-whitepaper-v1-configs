<?php
/**
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
/** @var $objectManager \Magento\TestFramework\ObjectManager */
$objectManager = \Magento\TestFramework\Helper\Bootstrap::getObjectManager();
$quote = $objectManager->create('Magento\Quote\Model\Quote');
$quote->load('reserved_order_id', 'reserved_order_id')
    ->delete();

$objectManager->create('Magento\Quote\Model\QuoteIdMask')
    ->delete($quote->getId());
