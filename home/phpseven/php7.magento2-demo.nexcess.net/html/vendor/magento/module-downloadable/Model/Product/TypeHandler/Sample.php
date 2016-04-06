<?php
/**
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magento\Downloadable\Model\Product\TypeHandler;

use Magento\Catalog\Model\Product;
use Magento\Downloadable\Model\ComponentInterface;

/**
 * Class Sample
 */
class Sample extends AbstractTypeHandler
{
    /**
     * @var \Magento\Downloadable\Model\SampleFactory
     */
    private $sampleFactory;

    /**
     * @var \Magento\Downloadable\Model\ResourceModel\SampleFactory
     */
    private $sampleResourceFactory;

    /**
     * @param \Magento\Framework\Json\Helper\Data $jsonHelper
     * @param \Magento\Downloadable\Helper\File $downloadableFile
     * @param \Magento\Downloadable\Model\SampleFactory $sampleFactory
     * @param \Magento\Downloadable\Model\ResourceModel\SampleFactory $sampleResourceFactory
     */
    public function __construct(
        \Magento\Framework\Json\Helper\Data $jsonHelper,
        \Magento\Downloadable\Helper\File $downloadableFile,
        \Magento\Downloadable\Model\SampleFactory $sampleFactory,
        \Magento\Downloadable\Model\ResourceModel\SampleFactory $sampleResourceFactory
    ) {
        parent::__construct($jsonHelper, $downloadableFile);
        $this->sampleFactory = $sampleFactory;
        $this->sampleResourceFactory = $sampleResourceFactory;
    }

    /**
     * {@inheritdoc}
     */
    public function getDataKey()
    {
        return 'sample';
    }

    /**
     * {@inheritdoc}
     */
    public function getIdentifierKey()
    {
        return 'sample_id';
    }

    /**
     * {@inheritdoc}
     */
    protected function processDelete()
    {
        if ($this->deletedItems) {
            $this->sampleResourceFactory->create()->deleteItems($this->deletedItems);
        }
    }

    /**
     * @return ComponentInterface
     */
    protected function createItem()
    {
        return $this->sampleFactory->create();
    }

    /**
     * @param ComponentInterface $model
     * @param array $data
     * @param Product $product
     * @return void
     */
    protected function setDataToModel(ComponentInterface $model, array $data, Product $product)
    {
        $model->setData(
            $data
        )->setSampleType(
            $data['type']
        )->setProductId(
            $product->getId()
        )->setStoreId(
            $product->getStoreId()
        );
    }

    /**
     * @param ComponentInterface $model
     * @param array $files
     * @return void
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    protected function setFiles(ComponentInterface $model, array $files)
    {
        if ($model->getSampleType() == \Magento\Downloadable\Helper\Download::LINK_TYPE_FILE) {
            $fileName = $this->downloadableFile->moveFileFromTmp(
                $model->getBaseTmpPath(),
                $model->getBasePath(),
                $files
            );
            $model->setSampleFile($fileName);
        }
        return $this;
    }

    /**
     * @param ComponentInterface $model
     * @param Product $product
     * @return void
     */
    protected function linkToProduct(ComponentInterface $model, Product $product)
    {
        $product->setLastAddedSampleId($model->getId());
        return $this;
    }
}
