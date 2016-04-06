<?php
/**
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
include __DIR__ . '/customer_rollback.php';

/** @var \Magento\Framework\Registry $registry */
$registry = \Magento\TestFramework\Helper\Bootstrap::getObjectManager()->get('Magento\Framework\Registry');
$registry->unregister('isSecureArea');
$registry->register('isSecureArea', true);

/** @var $website Magento\Store\Model\Website */
$website = \Magento\TestFramework\Helper\Bootstrap::getObjectManager()->create('Magento\Store\Model\Website');
$website->load('newwebsite', 'code');
if ($website->getId()) {
    $website->delete();
}

$registry->unregister('isSecureArea');
$registry->register('isSecureArea', false);
