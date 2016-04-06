<?php
/**
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magento\Downloadable\Model;

use Magento\Catalog\Api\ProductRepositoryInterface;
use Magento\Downloadable\Api\Data\SampleInterfaceFactory;
use Magento\Downloadable\Model\Product\Type;
use Magento\Downloadable\Api\Data\File\ContentUploaderInterface;
use Magento\Downloadable\Api\Data\SampleInterface;
use Magento\Downloadable\Model\Sample\ContentValidator;
use Magento\Framework\Exception\InputException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Json\EncoderInterface;

/**
 * Class SampleRepository
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class SampleRepository implements \Magento\Downloadable\Api\SampleRepositoryInterface
{
    /**
     * @var \Magento\Catalog\Api\ProductRepositoryInterface
     */
    protected $productRepository;

    /**
     * @var ContentValidator
     */
    protected $contentValidator;

    /**
     * @var Type
     */
    protected $downloadableType;

    /**
     * @var SampleInterfaceFactory
     */
    protected $sampleDataObjectFactory;

    /**
     * @var ContentUploaderInterface
     */
    protected $fileContentUploader;

    /**
     * @var EncoderInterface
     */
    protected $jsonEncoder;

    /**
     * @param ProductRepositoryInterface $productRepository
     * @param Type $downloadableType
     * @param SampleInterfaceFactory $sampleDataObjectFactory
     * @param ContentValidator $contentValidator
     * @param ContentUploaderInterface $fileContentUploader
     * @param EncoderInterface $jsonEncoder
     * @param SampleFactory $sampleFactory
     */
    public function __construct(
        ProductRepositoryInterface $productRepository,
        Type $downloadableType,
        SampleInterfaceFactory $sampleDataObjectFactory,
        ContentValidator $contentValidator,
        ContentUploaderInterface $fileContentUploader,
        EncoderInterface $jsonEncoder,
        SampleFactory $sampleFactory
    ) {
        $this->productRepository = $productRepository;
        $this->downloadableType = $downloadableType;
        $this->contentValidator = $contentValidator;
        $this->fileContentUploader = $fileContentUploader;
        $this->jsonEncoder = $jsonEncoder;
        $this->sampleFactory = $sampleFactory;
        $this->downloadableType = $downloadableType;
        $this->sampleDataObjectFactory = $sampleDataObjectFactory;
    }

    /**
     * {@inheritdoc}
     */
    public function getList($sku)
    {
        /** @var \Magento\Catalog\Model\Product $product */
        $product = $this->productRepository->get($sku);
        return $this->getSamplesByProduct($product);
    }

    /**
     * Build a sample data object
     *
     * @param \Magento\Downloadable\Model\Sample $resourceData
     * @return \Magento\Downloadable\Model\Sample
     */
    protected function buildSample($resourceData)
    {
        $sample = $this->sampleDataObjectFactory->create();
        $this->setBasicFields($resourceData, $sample);
        return $sample;
    }

    /**
     * Subroutine for buildLink and buildSample
     *
     * @param \Magento\Downloadable\Model\Link|\Magento\Downloadable\Model\Sample $resourceData
     * @param \Magento\Downloadable\Api\Data\LinkInterface|\Magento\Downloadable\Api\Data\SampleInterface $dataObject
     * @return null
     */
    protected function setBasicFields($resourceData, $dataObject)
    {
        $dataObject->setId($resourceData->getId());
        $storeTitle = $resourceData->getStoreTitle();
        $title = $resourceData->getTitle();
        if (!empty($storeTitle)) {
            $dataObject->setTitle($storeTitle);
        } else {
            $dataObject->setTitle($title);
        }
        $dataObject->setSortOrder($resourceData->getSortOrder());
        $dataObject->setSampleType($resourceData->getSampleType());
        $dataObject->setSampleFile($resourceData->getSampleFile());
        $dataObject->setSampleUrl($resourceData->getSampleUrl());
    }

    /**
     * List of links with associated samples
     *
     * @param \Magento\Catalog\Api\Data\ProductInterface $product
     * @return \Magento\Downloadable\Api\Data\SampleInterface[]
     */
    public function getSamplesByProduct(\Magento\Catalog\Api\Data\ProductInterface $product)
    {
        $sampleList = [];
        $samples = $this->downloadableType->getSamples($product);
        /** @var \Magento\Downloadable\Model\Sample $sample */
        foreach ($samples as $sample) {
            $sampleList[] = $this->buildSample($sample);
        }
        return $sampleList;
    }

    /**
     * Update downloadable sample of the given product
     *
     * @param string $sku
     * @param \Magento\Downloadable\Api\Data\SampleInterface $sample
     * @param bool $isGlobalScopeContent
     * @return int
     */
    public function save(
        $sku,
        SampleInterface $sample,
        $isGlobalScopeContent = true
    ) {
        $product = $this->productRepository->get($sku, true);

        $sampleId = $sample->getId();
        if ($sampleId) {
            return $this->updateSample($product, $sample, $isGlobalScopeContent);
        } else {
            if ($product->getTypeId() !== Type::TYPE_DOWNLOADABLE) {
                throw new InputException(__('Product type of the product must be \'downloadable\'.'));
            }
            if (!$this->contentValidator->isValid($sample)) {
                throw new InputException(__('Provided sample information is invalid.'));
            }

            if (!in_array($sample->getSampleType(), ['url', 'file'])) {
                throw new InputException(__('Invalid sample type.'));
            }

            $title = $sample->getTitle();
            if (empty($title)) {
                throw new InputException(__('Sample title cannot be empty.'));
            }

            return $this->saveSample($product, $sample, $isGlobalScopeContent);
        }
    }

    /**
     * @param \Magento\Catalog\Api\Data\ProductInterface $product
     * @param SampleInterface $sample
     * @param bool $isGlobalScopeContent
     * @return int
     */
    protected function saveSample(
        \Magento\Catalog\Api\Data\ProductInterface $product,
        SampleInterface $sample,
        $isGlobalScopeContent
    ) {
        $sampleData = [
            'sample_id' => $sample->getid() === null ? 0 : $sample->getid(),
            'is_delete' => 0,
            'type' => $sample->getSampleType(),
            'sort_order' => $sample->getSortOrder(),
            'title' => $sample->getTitle(),
        ];

        if ($sample->getSampleType() == 'file' && $sample->getSampleFile() === null) {
            $sampleData['file'] = $this->jsonEncoder->encode(
                [
                    $this->fileContentUploader->upload($sample->getSampleFileContent(), 'sample'),
                ]
            );
        } elseif ($sample->getSampleType() === 'url') {
            $sampleData['sample_url'] = $sample->getSampleUrl();
        } else {
            //existing file
            $sampleData['file'] = $this->jsonEncoder->encode(
                [
                    [
                        'file' => $sample->getSampleFile(),
                        'status' => 'old',
                    ],
                ]
            );
        }

        $downloadableData = ['sample' => [$sampleData]];
        $product->setDownloadableData($downloadableData);
        if ($isGlobalScopeContent) {
            $product->setStoreId(0);
        }
        $this->downloadableType->save($product);
        return $product->getLastAddedSampleId();
    }

    /**
     * @param \Magento\Catalog\Api\Data\ProductInterface $product
     * @param SampleInterface $sample
     * @param bool $isGlobalScopeContent
     * @return int
     * @throws InputException
     * @throws NoSuchEntityException
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     * @SuppressWarnings(PHPMD.NPathComplexity)
     */
    protected function updateSample(
        \Magento\Catalog\Api\Data\ProductInterface $product,
        SampleInterface $sample,
        $isGlobalScopeContent
    ) {
        $sampleId = $sample->getId();
        /** @var $existingSample \Magento\Downloadable\Model\Sample */
        $existingSample = $this->sampleFactory->create()->load($sampleId);

        if (!$existingSample->getId()) {
            throw new NoSuchEntityException(__('There is no downloadable sample with provided ID.'));
        }

        if ($existingSample->getProductId() != $product->getId()) {
            throw new InputException(__('Provided downloadable sample is not related to given product.'));
        }

        $validateFileContent = $sample->getSampleFileContent() === null ? false : true;
        if (!$this->contentValidator->isValid($sample, $validateFileContent)) {
            throw new InputException(__('Provided sample information is invalid.'));
        }
        if ($isGlobalScopeContent) {
            $product->setStoreId(0);
        }

        $title = $sample->getTitle();
        if (empty($title)) {
            if ($isGlobalScopeContent) {
                throw new InputException(__('Sample title cannot be empty.'));
            }
            // use title from GLOBAL scope
            $existingSample->setTitle(null);
        } else {
            $existingSample->setTitle($sample->getTitle());
        }

        if ($sample->getSampleType() === 'file' && $sample->getSampleFileContent() === null) {
            $sample->setSampleFile($existingSample->getSampleFile());
        }
        $this->saveSample($product, $sample, $isGlobalScopeContent);
        return $existingSample->getId();
    }

    /**
     * {@inheritdoc}
     */
    public function delete($id)
    {
        /** @var $sample \Magento\Downloadable\Model\Sample */
        $sample = $this->sampleFactory->create()->load($id);
        if (!$sample->getId()) {
            throw new NoSuchEntityException(__('There is no downloadable sample with provided ID.'));
        }
        $sample->delete();
        return true;
    }
}
