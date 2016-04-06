<?php
/**
 *
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */

$model = \Magento\TestFramework\Helper\Bootstrap::getObjectManager()->create('Magento\Customer\Model\Attribute');
$model->load('custom_attribute_test', 'attribute_code')->delete();

$model2 = \Magento\TestFramework\Helper\Bootstrap::getObjectManager()->create('Magento\Customer\Model\Attribute');
$model2->load('custom_attributes_test', 'attribute_code')->delete();
