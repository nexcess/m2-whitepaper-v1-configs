<?php
/**
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */


namespace Magento\Catalog\Model\Product;

use Magento\Catalog\Api\Data\ProductCustomOptionInterface;
use Magento\Catalog\Api\Data\ProductCustomOptionValuesInterface;
use Magento\Catalog\Model\Product;
use Magento\Catalog\Model\ResourceModel\Product\Option\Value\Collection;
use Magento\Catalog\Pricing\Price\BasePrice;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Model\AbstractExtensibleModel;

/**
 * Catalog product option model
 *
 * @method \Magento\Catalog\Model\ResourceModel\Product\Option getResource()
 * @method int getProductId()
 * @method \Magento\Catalog\Model\Product\Option setProductId(int $value)
 *
 * @author      Magento Core Team <core@magentocommerce.com>
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 * @SuppressWarnings(PHPMD.ExcessivePublicCount)
 */
class Option extends AbstractExtensibleModel implements ProductCustomOptionInterface
{
    const OPTION_GROUP_TEXT = 'text';

    const OPTION_GROUP_FILE = 'file';

    const OPTION_GROUP_SELECT = 'select';

    const OPTION_GROUP_DATE = 'date';

    const OPTION_TYPE_FIELD = 'field';

    const OPTION_TYPE_AREA = 'area';

    const OPTION_TYPE_FILE = 'file';

    const OPTION_TYPE_DROP_DOWN = 'drop_down';

    const OPTION_TYPE_RADIO = 'radio';

    const OPTION_TYPE_CHECKBOX = 'checkbox';

    const OPTION_TYPE_MULTIPLE = 'multiple';

    const OPTION_TYPE_DATE = 'date';

    const OPTION_TYPE_DATE_TIME = 'date_time';

    const OPTION_TYPE_TIME = 'time';

    /**#@+
     * Constants
     */
    const KEY_PRODUCT_SKU = 'product_sku';
    const KEY_OPTION_ID = 'option_id';
    const KEY_TITLE = 'title';
    const KEY_TYPE = 'type';
    const KEY_SORT_ORDER = 'sort_order';
    const KEY_IS_REQUIRE = 'is_require';
    const KEY_PRICE = 'price';
    const KEY_PRICE_TYPE = 'price_type';
    const KEY_SKU = 'sku';
    const KEY_FILE_EXTENSION = 'file_extension';
    const KEY_MAX_CHARACTERS = 'max_characters';
    const KEY_IMAGE_SIZE_Y = 'image_size_y';
    const KEY_IMAGE_SIZE_X = 'image_size_x';
    /**#@-*/

    /**
     * @var Product
     */
    protected $product;

    /**
     * @var array
     */
    protected $options = [];

    /**
     * @var array
     */
    protected $values = null;

    /**
     * Catalog product option value
     *
     * @var Option\Value
     */
    protected $productOptionValue;

    /**
     * Product option factory
     *
     * @var \Magento\Catalog\Model\Product\Option\Type\Factory
     */
    protected $optionTypeFactory;

    /**
     * @var \Magento\Framework\Stdlib\StringUtils
     */
    protected $string;

    /**
     * @var Option\Validator\Pool
     */
    protected $validatorPool;

    /**
     * @param \Magento\Framework\Model\Context $context
     * @param \Magento\Framework\Registry $registry
     * @param \Magento\Framework\Api\ExtensionAttributesFactory $extensionFactory
     * @param \Magento\Framework\Api\AttributeValueFactory $customAttributeFactory
     * @param Option\Value $productOptionValue
     * @param Option\Type\Factory $optionFactory
     * @param \Magento\Framework\Stdlib\StringUtils $string
     * @param Option\Validator\Pool $validatorPool
     * @param \Magento\Framework\Model\ResourceModel\AbstractResource $resource
     * @param \Magento\Framework\Data\Collection\AbstractDb $resourceCollection
     * @param array $data
     * @SuppressWarnings(PHPMD.ExcessiveParameterList)
     */
    public function __construct(
        \Magento\Framework\Model\Context $context,
        \Magento\Framework\Registry $registry,
        \Magento\Framework\Api\ExtensionAttributesFactory $extensionFactory,
        \Magento\Framework\Api\AttributeValueFactory $customAttributeFactory,
        Option\Value $productOptionValue,
        \Magento\Catalog\Model\Product\Option\Type\Factory $optionFactory,
        \Magento\Framework\Stdlib\StringUtils $string,
        Option\Validator\Pool $validatorPool,
        \Magento\Framework\Model\ResourceModel\AbstractResource $resource = null,
        \Magento\Framework\Data\Collection\AbstractDb $resourceCollection = null,
        array $data = []
    ) {
        $this->productOptionValue = $productOptionValue;
        $this->optionTypeFactory = $optionFactory;
        $this->validatorPool = $validatorPool;
        $this->string = $string;
        parent::__construct(
            $context,
            $registry,
            $extensionFactory,
            $customAttributeFactory,
            $resource,
            $resourceCollection,
            $data
        );
    }

    /**
     * Get resource instance
     *
     * @return \Magento\Framework\Model\ResourceModel\Db\AbstractDb
     */
    protected function _getResource()
    {
        return $this->_resource ?: parent::_getResource();
    }

    /**
     * @return void
     */
    protected function _construct()
    {
        $this->_init('Magento\Catalog\Model\ResourceModel\Product\Option');
        parent::_construct();
    }

    /**
     * Add value of option to values array
     *
     * @param Option\Value $value
     * @return $this
     */
    public function addValue(Option\Value $value)
    {
        $this->values[$value->getId()] = $value;
        return $this;
    }

    /**
     * Get value by given id
     *
     * @param int $valueId
     * @return Option\Value|null
     */
    public function getValueById($valueId)
    {
        if (isset($this->values[$valueId])) {
            return $this->values[$valueId];
        }

        return null;
    }

    /**
     * @return ProductCustomOptionValuesInterface[]|null
     */
    public function getValues()
    {
        return $this->values;
    }

    /**
     * Retrieve value instance
     *
     * @return Option\Value
     */
    public function getValueInstance()
    {
        return $this->productOptionValue;
    }

    /**
     * Add option for save it
     *
     * @param array $option
     * @return $this
     */
    public function addOption($option)
    {
        $this->options[] = $option;
        return $this;
    }

    /**
     * Get all options
     *
     * @return array
     */
    public function getOptions()
    {
        return $this->options;
    }

    /**
     * Set options for array
     *
     * @param array $options
     * @return $this
     */
    public function setOptions($options)
    {
        $this->options = $options;
        return $this;
    }

    /**
     * Set options to empty array
     *
     * @return $this
     */
    public function unsetOptions()
    {
        $this->options = [];
        return $this;
    }

    /**
     * Retrieve product instance
     *
     * @return Product
     */
    public function getProduct()
    {
        return $this->product;
    }

    /**
     * Set product instance
     *
     * @param Product $product
     * @return $this
     */
    public function setProduct(Product $product = null)
    {
        $this->product = $product;
        return $this;
    }

    /**
     * Get group name of option by given option type
     *
     * @param string $type
     * @return string
     */
    public function getGroupByType($type = null)
    {
        if ($type === null) {
            $type = $this->getType();
        }
        $optionGroupsToTypes = [
            self::OPTION_TYPE_FIELD => self::OPTION_GROUP_TEXT,
            self::OPTION_TYPE_AREA => self::OPTION_GROUP_TEXT,
            self::OPTION_TYPE_FILE => self::OPTION_GROUP_FILE,
            self::OPTION_TYPE_DROP_DOWN => self::OPTION_GROUP_SELECT,
            self::OPTION_TYPE_RADIO => self::OPTION_GROUP_SELECT,
            self::OPTION_TYPE_CHECKBOX => self::OPTION_GROUP_SELECT,
            self::OPTION_TYPE_MULTIPLE => self::OPTION_GROUP_SELECT,
            self::OPTION_TYPE_DATE => self::OPTION_GROUP_DATE,
            self::OPTION_TYPE_DATE_TIME => self::OPTION_GROUP_DATE,
            self::OPTION_TYPE_TIME => self::OPTION_GROUP_DATE,
        ];

        return isset($optionGroupsToTypes[$type]) ? $optionGroupsToTypes[$type] : '';
    }

    /**
     * Group model factory
     *
     * @param string $type Option type
     * @return \Magento\Catalog\Model\Product\Option\Type\DefaultType
     * @throws LocalizedException
     */
    public function groupFactory($type)
    {
        $group = $this->getGroupByType($type);
        if (!empty($group)) {
            return $this->optionTypeFactory->create(
                'Magento\Catalog\Model\Product\Option\Type\\' . $this->string->upperCaseWords($group)
            );
        }
        throw new LocalizedException(__('The option type to get group instance is incorrect.'));
    }

    /**
     * Save options.
     *
     * @return $this
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     */
    public function saveOptions()
    {
        foreach ($this->getOptions() as $option) {
            $this->_validatorBeforeSave = null;
            $this->setData(
                $option
            )->setData(
                'product_id',
                $this->getProduct()->getId()
            )->setData(
                'store_id',
                $this->getProduct()->getStoreId()
            );
            /** Reset is delete flag from the previous iteration */
            $this->isDeleted(false);

            if ($this->getData('option_id') == '0') {
                $this->unsetData('option_id');
            } else {
                $this->setId($this->getData('option_id'));
            }
            $isEdit = (bool)$this->getId() ? true : false;

            if ($this->getData('is_delete') == '1') {
                if ($isEdit) {
                    $this->getValueInstance()->deleteValue($this->getId());
                    $this->deletePrices($this->getId());
                    $this->deleteTitles($this->getId());
                    $this->delete();
                }
            } else {
                if ($this->getData('previous_type') != '') {
                    $previousType = $this->getData('previous_type');

                    /**
                     * if previous option has different group from one is came now
                     * need to remove all data of previous group
                     */
                    if ($this->getGroupByType($previousType) != $this->getGroupByType($this->getData('type'))) {
                        switch ($this->getGroupByType($previousType)) {
                            case self::OPTION_GROUP_SELECT:
                                $this->unsetData('values');
                                if ($isEdit) {
                                    $this->getValueInstance()->deleteValue($this->getId());
                                }
                                break;
                            case self::OPTION_GROUP_FILE:
                                $this->setData('file_extension', '');
                                $this->setData('image_size_x', '0');
                                $this->setData('image_size_y', '0');
                                break;
                            case self::OPTION_GROUP_TEXT:
                                $this->setData('max_characters', '0');
                                break;
                            case self::OPTION_GROUP_DATE:
                                break;
                        }
                        if ($this->getGroupByType($this->getData('type')) == self::OPTION_GROUP_SELECT) {
                            $this->setData('sku', '');
                            $this->unsetData('price');
                            $this->unsetData('price_type');
                            if ($isEdit) {
                                $this->deletePrices($this->getId());
                            }
                        }
                    }
                }
                $this->save();
            }
        }
        //eof foreach()
        return $this;
    }

    /**
     * @return \Magento\Framework\Model\AbstractModel
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function afterSave()
    {
        $this->getValueInstance()->unsetValues();
        if (is_array($this->getData('values'))) {
            foreach ($this->getData('values') as $value) {
                $this->getValueInstance()->addValue($value);
            }

            $this->getValueInstance()->setOption($this)->saveValues();
        } elseif ($this->getGroupByType($this->getType()) == self::OPTION_GROUP_SELECT) {
            throw new LocalizedException(__('Select type options required values rows.'));
        }

        return parent::afterSave();
    }

    /**
     * Return price. If $flag is true and price is percent
     *  return converted percent to price
     *
     * @param bool $flag
     * @return float
     */
    public function getPrice($flag = false)
    {
        if ($flag && $this->getPriceType() == 'percent') {
            $basePrice = $this->getProduct()->getPriceInfo()->getPrice(BasePrice::PRICE_CODE)->getValue();
            $price = $basePrice * ($this->_getData(self::KEY_PRICE) / 100);
            return $price;
        }
        return $this->_getData(self::KEY_PRICE);
    }

    /**
     * Delete prices of option
     *
     * @param int $optionId
     * @return $this
     */
    public function deletePrices($optionId)
    {
        $this->getResource()->deletePrices($optionId);
        return $this;
    }

    /**
     * Delete titles of option
     *
     * @param int $optionId
     * @return $this
     */
    public function deleteTitles($optionId)
    {
        $this->getResource()->deleteTitles($optionId);
        return $this;
    }

    /**
     * Get Product Option Collection
     *
     * @param Product $product
     * @return \Magento\Catalog\Model\ResourceModel\Product\Option\Collection
     */
    public function getProductOptionCollection(Product $product)
    {
        $collection = clone $this->getCollection();
        $collection->addFieldToFilter(
            'product_id',
            $product->getId()
        )->addTitleToResult(
            $product->getStoreId()
        )->addPriceToResult(
            $product->getStoreId()
        )->setOrder(
            'sort_order',
            'asc'
        )->setOrder(
            'title',
            'asc'
        );

        if ($this->getAddRequiredFilter()) {
            $collection->addRequiredFilter($this->getAddRequiredFilterValue());
        }

        $collection->addValuesToResult($product->getStoreId());
        return $collection;
    }

    /**
     * Get collection of values for current option
     *
     * @return Collection
     */
    public function getValuesCollection()
    {
        $collection = $this->getValueInstance()->getValuesCollection($this);

        return $collection;
    }

    /**
     * Get collection of values by given option ids
     *
     * @param array $optionIds
     * @param int $storeId
     * @return Collection
     */
    public function getOptionValuesByOptionId($optionIds, $storeId)
    {
        $collection = $this->productOptionValue->getValuesByOption($optionIds, $this->getId(), $storeId);

        return $collection;
    }

    /**
     * Duplicate options for product
     *
     * @param int $oldProductId
     * @param int $newProductId
     * @return $this
     */
    public function duplicate($oldProductId, $newProductId)
    {
        $this->getResource()->duplicate($this, $oldProductId, $newProductId);

        return $this;
    }

    /**
     * Retrieve option searchable data
     *
     * @param int $productId
     * @param int $storeId
     * @return array
     */
    public function getSearchableData($productId, $storeId)
    {
        return $this->_getResource()->getSearchableData($productId, $storeId);
    }

    /**
     * Clearing object's data
     *
     * @return $this
     */
    protected function _clearData()
    {
        $this->_data = [];
        $this->values = null;
        return $this;
    }

    /**
     * Clearing cyclic references
     *
     * @return $this
     */
    protected function _clearReferences()
    {
        if (!empty($this->values)) {
            foreach ($this->values as $value) {
                $value->unsetOption();
            }
        }
        return $this;
    }

    /**
     * {@inheritdoc}
     */
    protected function _getValidationRulesBeforeSave()
    {
        return $this->validatorPool->get($this->getType());
    }

    /**
     * Get product SKU
     *
     * @return string
     */
    public function getProductSku()
    {
        $productSku = $this->_getData(self::KEY_PRODUCT_SKU);
        if (!$productSku) {
            $productSku = $this->getProduct()->getSku();
        }
        return $productSku;
    }

    /**
     * Get option id
     *
     * @return int|null
     * @codeCoverageIgnoreStart
     */
    public function getOptionId()
    {
        return $this->_getData(self::KEY_OPTION_ID);
    }

    /**
     * Get option title
     *
     * @return string
     */
    public function getTitle()
    {
        return $this->_getData(self::KEY_TITLE);
    }

    /**
     * Get option type
     *
     * @return string
     */
    public function getType()
    {
        return $this->_getData(self::KEY_TYPE);
    }

    /**
     * Get sort order
     *
     * @return int
     */
    public function getSortOrder()
    {
        return $this->_getData(self::KEY_SORT_ORDER);
    }

    /**
     * Get is require
     *
     * @return bool
     * @SuppressWarnings(PHPMD.BooleanGetMethodName)
     */
    public function getIsRequire()
    {
        return $this->_getData(self::KEY_IS_REQUIRE);
    }

    /**
     * Get price type
     *
     * @return string|null
     */
    public function getPriceType()
    {
        return $this->_getData(self::KEY_PRICE_TYPE);
    }

    /**
     * Get Sku
     *
     * @return string|null
     */
    public function getSku()
    {
        return $this->_getData(self::KEY_SKU);
    }

    /**
     * @return string|null
     */
    public function getFileExtension()
    {
        return $this->getData(self::KEY_FILE_EXTENSION);
    }

    /**
     * @return int|null
     */
    public function getMaxCharacters()
    {
        return $this->getData(self::KEY_MAX_CHARACTERS);
    }

    /**
     * @return int|null
     */
    public function getImageSizeX()
    {
        return $this->getData(self::KEY_IMAGE_SIZE_X);
    }

    /**
     * @return int|null
     */
    public function getImageSizeY()
    {
        return $this->getData(self::KEY_IMAGE_SIZE_Y);
    }
    /**
     * Set product SKU
     *
     * @param string $productSku
     * @return $this
     */
    public function setProductSku($productSku)
    {
        return $this->setData(self::KEY_PRODUCT_SKU, $productSku);
    }

    /**
     * Set option id
     *
     * @param int $optionId
     * @return $this
     */
    public function setOptionId($optionId)
    {
        return $this->setData(self::KEY_OPTION_ID, $optionId);
    }

    /**
     * Set option title
     *
     * @param string $title
     * @return $this
     */
    public function setTitle($title)
    {
        return $this->setData(self::KEY_TITLE, $title);
    }

    /**
     * Set option type
     *
     * @param string $type
     * @return $this
     */
    public function setType($type)
    {
        return $this->setData(self::KEY_TYPE, $type);
    }

    /**
     * Set sort order
     *
     * @param int $sortOrder
     * @return $this
     */
    public function setSortOrder($sortOrder)
    {
        return $this->setData(self::KEY_SORT_ORDER, $sortOrder);
    }

    /**
     * Set is require
     *
     * @param bool $isRequired
     * @return $this
     */
    public function setIsRequire($isRequired)
    {
        return $this->setData(self::KEY_IS_REQUIRE, $isRequired);
    }

    /**
     * Set price
     *
     * @param float $price
     * @return $this
     */
    public function setPrice($price)
    {
        return $this->setData(self::KEY_PRICE, $price);
    }

    /**
     * Set price type
     *
     * @param string $priceType
     * @return $this
     */
    public function setPriceType($priceType)
    {
        return $this->setData(self::KEY_PRICE_TYPE, $priceType);
    }

    /**
     * Set Sku
     *
     * @param string $sku
     * @return $this
     */
    public function setSku($sku)
    {
        return $this->setData(self::KEY_SKU, $sku);
    }

    /**
     * @param string $fileExtension
     * @return $this
     */
    public function setFileExtension($fileExtension)
    {
        return $this->setData(self::KEY_FILE_EXTENSION, $fileExtension);
    }

    /**
     * @param int $maxCharacters
     * @return $this
     */
    public function setMaxCharacters($maxCharacters)
    {
        return $this->setData(self::KEY_MAX_CHARACTERS, $maxCharacters);
    }

    /**
     * @param int $imageSizeX
     * @return $this
     */
    public function setImageSizeX($imageSizeX)
    {
        return $this->setData(self::KEY_IMAGE_SIZE_X, $imageSizeX);
    }

    /**
     * @param int $imageSizeY
     * @return $this
     */
    public function setImageSizeY($imageSizeY)
    {
        return $this->setData(self::KEY_IMAGE_SIZE_Y, $imageSizeY);
    }

    /**
     * @param \Magento\Catalog\Api\Data\ProductCustomOptionValuesInterface[] $values
     * @return $this
     */
    public function setValues(array $values = null)
    {
        $this->values = $values;
        return $this;
    }

    /**
     * {@inheritdoc}
     *
     * @return \Magento\Catalog\Api\Data\ProductCustomOptionExtensionInterface|null
     */
    public function getExtensionAttributes()
    {
        return $this->_getExtensionAttributes();
    }

    /**
     * {@inheritdoc}
     *
     * @param \Magento\Catalog\Api\Data\ProductCustomOptionExtensionInterface $extensionAttributes
     * @return $this
     */
    public function setExtensionAttributes(
        \Magento\Catalog\Api\Data\ProductCustomOptionExtensionInterface $extensionAttributes
    ) {
        return $this->_setExtensionAttributes($extensionAttributes);
    }
    //@codeCoverageIgnoreEnd
}
