<?php
/**
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */

require 'default_rollback.php';
require __DIR__ . '/order.php';

$objectManager = \Magento\TestFramework\Helper\Bootstrap::getObjectManager();
/** @var \Magento\Sales\Model\Order $order */
$orderCollection = $objectManager->create('Magento\Sales\Model\Order')->getCollection();
$order = $orderCollection->getFirstItem();

$creditmemoItemFactory = $objectManager->create('Magento\Sales\Model\Order\Creditmemo\ItemFactory');
/** @var \Magento\Sales\Model\Order\CreditmemoFactory $creditmemoFactory */
$creditmemoFactory = $objectManager->get('Magento\Sales\Model\Order\CreditmemoFactory');
$creditmemo = $creditmemoFactory->createByOrder($order, $order->getData());
$creditmemo->setOrder($order);
$creditmemo->setState(Magento\Sales\Model\Order\Creditmemo::STATE_OPEN);
foreach ($order->getItems() as $item) {
    $creditmemoItem = $creditmemoItemFactory->create(
        ['data' => [
                'order_item_id' => $item->getId(),
                'sku' => $item->getSku(),
            ],
        ]
    );
    $creditmemo->addItem($creditmemoItem);
}
$creditmemo->save();
