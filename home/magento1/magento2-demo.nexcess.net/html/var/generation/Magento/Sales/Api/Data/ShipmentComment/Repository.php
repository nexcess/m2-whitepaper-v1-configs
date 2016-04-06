<?php
namespace Magento\Sales\Api\Data\ShipmentComment;

/**
 * Repository class for @see \Magento\Sales\Api\Data\ShipmentCommentInterface
 */
class Repository implements \Magento\Sales\Api\ShipmentCommentRepositoryInterface
{
    /**
     * shipmentCommentInterfacePersistor
     *
     * @var \Magento\Sales\Api\Data\ShipmentCommentInterfacePersistor
     */
    protected $shipmentCommentInterfacePersistor = null;

    /**
     * Collection Factory
     *
     * @var \Magento\Sales\Api\Data\ShipmentCommentSearchResultInterfaceFactory
     */
    protected $shipmentCommentInterfaceSearchResultFactory = null;

    /**
     * \Magento\Sales\Api\Data\ShipmentCommentInterface[]
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
     * @param \Magento\Sales\Api\Data\ShipmentCommentInterface
     * $shipmentCommentInterfacePersistor
     * @param \Magento\Sales\Api\Data\ShipmentCommentSearchResultInterfaceFactory
     * $shipmentCommentInterfaceSearchResultFactory
     * @param \Magento\Framework\Api\ExtensionAttribute\JoinProcessorInterface
     * $extensionAttributesJoinProcessor
     */
    public function __construct(\Magento\Sales\Api\Data\ShipmentCommentInterfacePersistor $shipmentCommentInterfacePersistor, \Magento\Sales\Api\Data\ShipmentCommentSearchResultInterfaceFactory $shipmentCommentInterfaceSearchResultFactory, \Magento\Framework\Api\ExtensionAttribute\JoinProcessorInterface $extensionAttributesJoinProcessor)
    {
        $this->shipmentCommentInterfacePersistor = $shipmentCommentInterfacePersistor;
        $this->shipmentCommentInterfaceSearchResultFactory = $shipmentCommentInterfaceSearchResultFactory;
        $this->extensionAttributesJoinProcessor = $extensionAttributesJoinProcessor;
    }

    /**
     * load entity
     *
     * @param int $id
     * @return \Magento\Sales\Api\Data\ShipmentCommentInterface
     * @throws \Magento\Framework\Exception\InputException
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function get($id)
    {
        if (!$id) {
            throw new \Magento\Framework\Exception\InputException('ID required');
        }
        if (!isset($this->registry[$id])) {
            $entity = $this->shipmentCommentInterfacePersistor->loadEntity($id);
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
     * @return \Magento\Sales\Api\Data\ShipmentCommentInterface
     */
    public function create(\Magento\Sales\Api\Data\ShipmentCommentInterface $entity)
    {
        return $this->shipmentCommentInterfacePersistor->registerNew($entity);
    }

    /**
     * Register entity to create
     *
     * @param array $data
     * @return \Magento\Sales\Api\Data\ShipmentComment\Repository
     */
    public function createFromArray(array $data)
    {
        return $this->shipmentCommentInterfacePersistor->registerFromArray($data);
    }

    /**
     * Find entities by criteria
     *
     * @param \Magento\Framework\Api\SearchCriteria $searchCriteria
     * @return \Magento\Sales\Api\Data\ShipmentCommentInterface[]
     */
    public function getList(\Magento\Framework\Api\SearchCriteria $searchCriteria)
    {
        $collection = $this->shipmentCommentInterfaceSearchResultFactory->create();
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
     * @param \Magento\Sales\Api\Data\ShipmentCommentInterface $entity
     */
    public function remove(\Magento\Sales\Api\Data\ShipmentCommentInterface $entity)
    {
        $this->shipmentCommentInterfacePersistor->registerDeleted($entity);
    }

    /**
     * Register entity to delete
     *
     * @param \Magento\Sales\Api\Data\ShipmentCommentInterface $entity
     * @return bool
     */
    public function delete(\Magento\Sales\Api\Data\ShipmentCommentInterface $entity)
    {
        $this->shipmentCommentInterfacePersistor->registerDeleted($entity);
        return $this->shipmentCommentInterfacePersistor->doPersistEntity($entity);
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
        $this->shipmentCommentInterfacePersistor->registerDeleted($entity);
        return $this->shipmentCommentInterfacePersistor->doPersistEntity($entity);
    }

    /**
     * Perform persist operations
     */
    public function flush()
    {
        $ids = $this->shipmentCommentInterfacePersistor->doPersist();
        foreach ($ids as $id) {
        unset($this->registry[$id]);
        }
    }

    /**
     * Perform persist operations for one entity
     *
     * @param \Magento\Sales\Api\Data\ShipmentCommentInterface $entity
     * @return \Magento\Sales\Api\Data\ShipmentCommentInterface
     */
    public function save(\Magento\Sales\Api\Data\ShipmentCommentInterface $entity)
    {
        $this->shipmentCommentInterfacePersistor->doPersistEntity($entity);
        return $entity;
    }
}
