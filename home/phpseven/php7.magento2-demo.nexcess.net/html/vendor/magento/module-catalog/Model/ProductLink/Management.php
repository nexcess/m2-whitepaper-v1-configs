<?php
/**
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\Catalog\Model\ProductLink;

use Magento\Catalog\Api\Data;
use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Exception\InputException;

class Management implements \Magento\Catalog\Api\ProductLinkManagementInterface
{
    /**
     * @var \Magento\Catalog\Api\ProductRepositoryInterface
     */
    protected $productRepository;

    /**
     * @var \Magento\Catalog\Model\Product\LinkTypeProvider
     */
    protected $linkTypeProvider;

    /**
     * @param \Magento\Catalog\Api\ProductRepositoryInterface $productRepository
     * @param \Magento\Catalog\Model\Product\LinkTypeProvider $linkTypeProvider
     */
    public function __construct(
        \Magento\Catalog\Api\ProductRepositoryInterface $productRepository,
        \Magento\Catalog\Model\Product\LinkTypeProvider $linkTypeProvider
    ) {
        $this->productRepository = $productRepository;
        $this->linkTypeProvider = $linkTypeProvider;
    }

    /**
     * {@inheritdoc}
     */
    public function getLinkedItemsByType($sku, $type)
    {
        $output = [];

        $linkTypes = $this->linkTypeProvider->getLinkTypes();

        if (!isset($linkTypes[$type])) {
            throw new NoSuchEntityException(__('Unknown link type: %1', (string)$type));
        }
        $product = $this->productRepository->get($sku);
        $links = $product->getProductLinks();

        // Only return the links of type specified
        foreach ($links as $link) {
            if ($link->getLinkType() == $type) {
                $output[] = $link;
            }
        }

        return $output;
    }

    /**
     * {@inheritdoc}
     */
    public function setProductLinks($sku, array $items)
    {
        $linkTypes = $this->linkTypeProvider->getLinkTypes();

        // Check if product link type is set and correct
        if (!empty($items)) {
            foreach ($items as $newLink) {
                $type = $newLink->getLinkType();
                if ($type == null) {
                    throw InputException::requiredField("linkType");
                }
                if (!isset($linkTypes[$type])) {
                    throw new NoSuchEntityException(
                        __('Provided link type "%1" does not exist', $type)
                    );
                }
            }
        }

        $product = $this->productRepository->get($sku);

        // Replace only links of the specified type
        $existingLinks = $product->getProductLinks();
        $newLinks = [];
        if (!empty($existingLinks)) {
            foreach ($existingLinks as $link) {
                if ($link->getLinkType() != $type) {
                    $newLinks[] = $link;
                }
            }
            $newLinks = array_merge($newLinks, $items);
        } else {
            $newLinks = $items;
        }
        $product->setProductLinks($newLinks);
        try {
            $this->productRepository->save($product);
        } catch (\Exception $exception) {
            throw new CouldNotSaveException(__('Invalid data provided for linked products'));
        }

        return true;
    }
}
