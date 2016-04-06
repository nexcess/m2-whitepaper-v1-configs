<?php
namespace Magento\Sales\Api\Data\ShipmentTrack;

/**
 * Repository class for @see \Magento\Sales\Api\Data\ShipmentTrackInterface
 */
class Repository implements \Magento\Sales\Api\ShipmentTrackRepositoryInterface
{
    /**
     * shipmentTrackInterfacePersistor
     *
     * @var \Magento\Sales\Api\Data\ShipmentTrackInterfacePersistor
     */
    protected $shipmentTrackInterfacePersistor = null;

    /**
     * Collection Factory
     *
     * @var \Magento\Sales\Api\Data\ShipmentTrackSearchResultInterfaceFactory
     */
    protected $shipmentTrackInterfaceSearchResultFactory = null;

    /**
     * \Magento\Sales\Api\Data\ShipmentTrackInterface[]
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
     * @param \Magento\Sales\Api\Data\ShipmentTrackInterface
     * $shipmentTrackInterfacePersistor
     * @param \Magento\Sales\Api\Data\ShipmentTrackSearchResultInterfaceFactory
     * $shipmentTrackInterfaceSearchResultFactory
     * @param \Magento\Framework\Api\ExtensionAttribute\JoinProcessorInterface
     * $extensionAttributesJoinProcessor
     */
    public function __construct(\Magento\Sales\Api\Data\ShipmentTrackInterfacePersistor $shipmentTrackInterfacePersistor, \Magento\Sales\Api\Data\ShipmentTrackSearchResultInterfaceFactory $shipmentTrackInterfaceSearchResultFactory, \Magento\Framework\Api\ExtensionAttribute\JoinProcessorInterface $extensionAttributesJoinProcessor)
    {
        $this->shipmentTrackInterfacePersistor = $shipmentTrackInterfacePersistor;
        $this->shipmentTrackInterfaceSearchResultFactory = $shipmentTrackInterfaceSearchResultFactory;
        $this->extensionAttributesJoinProcessor = $extensionAttributesJoinProcessor;
    }

    /**
     * load entity
     *
     * @param int $id
     * @return \Magento\Sales\Api\Data\ShipmentTrackInterface
     * @throws \Magento\Framework\Exception\InputException
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function get($id)
    {
        if (!$id) {
            throw new \Magento\Framework\Exception\InputException('ID required');
        }
        if (!isset($this->registry[$id])) {
            $entity = $this->shipmentTrackInterfacePersistor->loadEntity($id);
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
     * @return \Magento\Sales\Api\Data\ShipmentTrackInterface
     */
    public function create(\Magento\Sales\Api\Data\ShipmentTrackInterface $entity)
    {
        return $this->shipmentTrackInterfacePersistor->registerNew($entity);
    }

    /**
     * Register entity to create
     *
     * @param array $data
     * @return \Magento\Sales\Api\Data\ShipmentTrack\Repository
     */
    public function createFromArray(array $data)
    {
        return $this->shipmentTrackInterfacePersistor->registerFromArray($data);
    }

    /**
     * Find entities by criteria
     *
     * @param \Magento\Framework\Api\SearchCriteria $searchCriteria
     * @return \Magento\Sales\Api\Data\ShipmentTrackInterface[]
     */
    public function getList(\Magento\Framework\Api\SearchCriteria $searchCriteria)
    {
        $collection = $this->shipmentTrackInterfaceSearchResultFactory->create();
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
     * @param \Magento\Sales\Api\Data\ShipmentTrackInterface $entity
     */
    public function remove(\Magento\Sales\Api\Data\ShipmentTrackInterface $entity)
    {
        $this->shipmentTrackInterfacePersistor->registerDeleted($entity);
    }

    /**
     * Register entity to delete
     *
     * @param \Magento\Sales\Api\Data\ShipmentTrackInterface $entity
     * @return bool
     */
    public function delete(\Magento\Sales\Api\Data\ShipmentTrackInterface $entity)
    {
        $this->shipmentTrackInterfacePersistor->registerDeleted($entity);
        return $this->shipmentTrackInterfacePersistor->doPersistEntity($entity);
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
        $this->shipmentTrackInterfacePersistor->registerDeleted($entity);
        return $this->shipmentTrackInterfacePersistor->doPersistEntity($entity);
    }

    /**
     * Perform persist operations
     */
    public function flush()
    {
        $ids = $this->shipmentTrackInterfacePersistor->doPersist();
        foreach ($ids as $id) {
        unset($this->registry[$id]);
        }
    }

    /**
     * Perform persist operations for one entity
     *
     * @param \Magento\Sales\Api\Data\ShipmentTrackInterface $entity
     * @return \Magento\Sales\Api\Data\ShipmentTrackInterface
     */
    public function save(\Magento\Sales\Api\Data\ShipmentTrackInterface $entity)
    {
        $this->shipmentTrackInterfacePersistor->doPersistEntity($entity);
        return $entity;
    }
}
