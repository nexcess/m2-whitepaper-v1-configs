<?php
/**
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */

$model = \Magento\TestFramework\Helper\Bootstrap::getObjectManager()->create(
    'Magento\Catalog\Model\ResourceModel\Eav\Attribute'
);
$model->setName('user_attribute')->setId(1)->setEntityTypeId(4)->setIsUserDefined(1);
$model->save();
