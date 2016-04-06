<?php
namespace Magento\Sales\Api\Data\ShipmentItem;

/**
 * Repository class for @see \Magento\Sales\Api\Data\ShipmentItemInterface
 */
class Repository implements \Magento\Sales\Api\ShipmentItemRepositoryInterface
{
    /**
     * shipmentItemInterfacePersistor
     *
     * @var \Magento\Sales\Api\Data\ShipmentItemInterfacePersistor
     */
    protected $shipmentItemInterfacePersistor = null;

    /**
     * Collection Factory
     *
     * @var \Magento\Sales\Api\Data\ShipmentItemSearchResultInterfaceFactory
     */
    protected $shipmentItemInterfaceSearchResultFactory = null;

    /**
     * \Magento\Sales\Api\Data\ShipmentItemInterface[]
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
     * @param \Magento\Sales\Api\Data\ShipmentItemInterface
     * $shipmentItemInterfacePersistor
     * @param \Magento\Sales\Api\Data\ShipmentItemSearchResultInterfaceFactory
     * $shipmentItemInterfaceSearchResultFactory
     * @param \Magento\Framework\Api\ExtensionAttribute\JoinProcessorInterface
     * $extensionAttributesJoinProcessor
     */
    public function __construct(\Magento\Sales\Api\Data\ShipmentItemInterfacePersistor $shipmentItemInterfacePersistor, \Magento\Sales\Api\Data\ShipmentItemSearchResultInterfaceFactory $shipmentItemInterfaceSearchResultFactory, \Magento\Framework\Api\ExtensionAttribute\JoinProcessorInterface $extensionAttributesJoinProcessor)
    {
        $this->shipmentItemInterfacePersistor = $shipmentItemInterfacePersistor;
        $this->shipmentItemInterfaceSearchResultFactory = $shipmentItemInterfaceSearchResultFactory;
        $this->extensionAttributesJoinProcessor = $extensionAttributesJoinProcessor;
    }

    /**
     * load entity
     *
     * @param int $id
     * @return \Magento\Sales\Api\Data\ShipmentItemInterface
     * @throws \Magento\Framework\Exception\InputException
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function get($id)
    {
        if (!$id) {
            throw new \Magento\Framework\Exception\InputException('ID required');
        }
        if (!isset($this->registry[$id])) {
            $entity = $this->shipmentItemInterfacePersistor->loadEntity($id);
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
     * @return \Magento\Sales\Api\Data\ShipmentItemInterface
     */
    public function create(\Magento\Sales\Api\Data\ShipmentItemInterface $entity)
    {
        return $this->shipmentItemInterfacePersistor->registerNew($entity);
    }

    /**
     * Register entity to create
     *
     * @param array $data
     * @return \Magento\Sales\Api\Data\ShipmentItem\Repository
     */
    public function createFromArray(array $data)
    {
        return $this->shipmentItemInterfacePersistor->registerFromArray($data);
    }

    /**
     * Find entities by criteria
     *
     * @param \Magento\Framework\Api\SearchCriteria $searchCriteria
     * @return \Magento\Sales\Api\Data\ShipmentItemInterface[]
     */
    public function getList(\Magento\Framework\Api\SearchCriteria $searchCriteria)
    {
        $collection = $this->shipmentItemInterfaceSearchResultFactory->create();
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
     * @param \Magento\Sales\Api\Data\ShipmentItemInterface $entity
     */
    public function remove(\Magento\Sales\Api\Data\ShipmentItemInterface $entity)
    {
        $this->shipmentItemInterfacePersistor->registerDeleted($entity);
    }

    /**
     * Register entity to delete
     *
     * @param \Magento\Sales\Api\Data\ShipmentItemInterface $entity
     * @return bool
     */
    public function delete(\Magento\Sales\Api\Data\ShipmentItemInterface $entity)
    {
        $this->shipmentItemInterfacePersistor->registerDeleted($entity);
        return $this->shipmentItemInterfacePersistor->doPersistEntity($entity);
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
        $this->shipmentItemInterfacePersistor->registerDeleted($entity);
        return $this->shipmentItemInterfacePersistor->doPersistEntity($entity);
    }

    /**
     * Perform persist operations
     */
    public function flush()
    {
        $ids = $this->shipmentItemInterfacePersistor->doPersist();
        foreach ($ids as $id) {
        unset($this->registry[$id]);
        }
    }

    /**
     * Perform persist operations for one entity
     *
     * @param \Magento\Sales\Api\Data\ShipmentItemInterface $entity
     * @return \Magento\Sales\Api\Data\ShipmentItemInterface
     */
    public function save(\Magento\Sales\Api\Data\ShipmentItemInterface $entity)
    {
        $this->shipmentItemInterfacePersistor->doPersistEntity($entity);
        return $entity;
    }
}
