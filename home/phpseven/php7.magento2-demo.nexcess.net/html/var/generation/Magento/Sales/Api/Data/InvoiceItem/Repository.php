<?php
namespace Magento\Sales\Api\Data\InvoiceItem;

/**
 * Repository class for @see \Magento\Sales\Api\Data\InvoiceItemInterface
 */
class Repository implements \Magento\Sales\Api\InvoiceItemRepositoryInterface
{
    /**
     * invoiceItemInterfacePersistor
     *
     * @var \Magento\Sales\Api\Data\InvoiceItemInterfacePersistor
     */
    protected $invoiceItemInterfacePersistor = null;

    /**
     * Collection Factory
     *
     * @var \Magento\Sales\Api\Data\InvoiceItemSearchResultInterfaceFactory
     */
    protected $invoiceItemInterfaceSearchResultFactory = null;

    /**
     * \Magento\Sales\Api\Data\InvoiceItemInterface[]
     *
     * @var array
     */
    protected $registry = array(
        
    );

    /**
     * Extension attributes join processor.
     *
     * @var \Magento\Framework\Api\ExtensionAttribute\JoinProcessorInterface
     */
    protected $extensionAttributesJoinProcessor = null;

    /**
     * Repository constructor
     *
     * @param \Magento\Sales\Api\Data\InvoiceItemInterface
     * $invoiceItemInterfacePersistor
     * @param \Magento\Sales\Api\Data\InvoiceItemSearchResultInterfaceFactory
     * $invoiceItemInterfaceSearchResultFactory
     * @param \Magento\Framework\Api\ExtensionAttribute\JoinProcessorInterface
     * $extensionAttributesJoinProcessor
     */
    public function __construct(\Magento\Sales\Api\Data\InvoiceItemInterfacePersistor $invoiceItemInterfacePersistor, \Magento\Sales\Api\Data\InvoiceItemSearchResultInterfaceFactory $invoiceItemInterfaceSearchResultFactory, \Magento\Framework\Api\ExtensionAttribute\JoinProcessorInterface $extensionAttributesJoinProcessor)
    {
        $this->invoiceItemInterfacePersistor = $invoiceItemInterfacePersistor;
        $this->invoiceItemInterfaceSearchResultFactory = $invoiceItemInterfaceSearchResultFactory;
        $this->extensionAttributesJoinProcessor = $extensionAttributesJoinProcessor;
    }

    /**
     * load entity
     *
     * @param int $id
     * @return \Magento\Sales\Api\Data\InvoiceItemInterface
     * @throws \Magento\Framework\Exception\InputException
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function get($id)
    {
        if (!$id) {
            throw new \Magento\Framework\Exception\InputException('ID required');
        }
        if (!isset($this->registry[$id])) {
            $entity = $this->invoiceItemInterfacePersistor->loadEntity($id);
            if (!$entity->getId()) {
                throw new \Magento\Framework\Exception\NoSuchEntityException('Requested entity doesn\'t exist');
            }
            $this->registry[$id] = $entity;
        }
        return $this->registry[$id];
    }

    /**
     * Register entity to create
     *
     * @param array $data
     * @return \Magento\Sales\Api\Data\InvoiceItemInterface
     */
    public function create(\Magento\Sales\Api\Data\InvoiceItemInterface $entity)
    {
        return $this->invoiceItemInterfacePersistor->registerNew($entity);
    }

    /**
     * Register entity to create
     *
     * @param array $data
     * @return \Magento\Sales\Api\Data\InvoiceItem\Repository
     */
    public function createFromArray(array $data)
    {
        return $this->invoiceItemInterfacePersistor->registerFromArray($data);
    }

    /**
     * Find entities by criteria
     *
     * @param \Magento\Framework\Api\SearchCriteria $searchCriteria
     * @return \Magento\Sales\Api\Data\InvoiceItemInterface[]
     */
    public function getList(\Magento\Framework\Api\SearchCriteria $searchCriteria)
    {
        $collection = $this->invoiceItemInterfaceSearchResultFactory->create();
        $this->extensionAttributesJoinProcessor->process($collection);
        foreach ($searchCriteria->getFilterGroups() as $filterGroup) {
            foreach ($filterGroup->getFilters() as $filter) {
                $condition = $filter->getConditionType() ? $filter->getConditionType() : 'eq';
                $collection->addFieldToFilter($filter->getField(), [$condition => $filter->getValue()]);
            }
        }
        $collection->setCurPage($searchCriteria->getCurrentPage());
        $collection->setPageSize($searchCriteria->getPageSize());
        return $collection;
    }

    /**
     * Register entity to delete
     *
     * @param \Magento\Sales\Api\Data\InvoiceItemInterface $entity
     */
    public function remove(\Magento\Sales\Api\Data\InvoiceItemInterface $entity)
    {
        $this->invoiceItemInterfacePersistor->registerDeleted($entity);
    }

    /**
     * Register entity to delete
     *
     * @param \Magento\Sales\Api\Data\InvoiceItemInterface $entity
     * @return bool
     */
    public function delete(\Magento\Sales\Api\Data\InvoiceItemInterface $entity)
    {
        $this->invoiceItemInterfacePersistor->registerDeleted($entity);
        return $this->invoiceItemInterfacePersistor->doPersistEntity($entity);
    }

    /**
     * Delete entity by Id
     *
     * @param int $id
     * @return bool
     */
    public function deleteById($id)
    {
        $entity = $this->get($id);
        $this->invoiceItemInterfacePersistor->registerDeleted($entity);
        return $this->invoiceItemInterfacePersistor->doPersistEntity($entity);
    }

    /**
     * Perform persist operations
     */
    public function flush()
    {
        $ids = $this->invoiceItemInterfacePersistor->doPersist();
        foreach ($ids as $id) {
        unset($this->registry[$id]);
        }
    }

    /**
     * Perform persist operations for one entity
     *
     * @param \Magento\Sales\Api\Data\InvoiceItemInterface $entity
     * @return \Magento\Sales\Api\Data\InvoiceItemInterface
     */
    public function save(\Magento\Sales\Api\Data\InvoiceItemInterface $entity)
    {
        $this->invoiceItemInterfacePersistor->doPersistEntity($entity);
        return $entity;
    }
}
