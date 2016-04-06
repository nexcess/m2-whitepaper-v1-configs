<?php
namespace Magento\Sales\Api\Data\CreditmemoItem;

/**
 * Repository class for @see \Magento\Sales\Api\Data\CreditmemoItemInterface
 */
class Repository implements \Magento\Sales\Api\CreditmemoItemRepositoryInterface
{
    /**
     * creditmemoItemInterfacePersistor
     *
     * @var \Magento\Sales\Api\Data\CreditmemoItemInterfacePersistor
     */
    protected $creditmemoItemInterfacePersistor = null;

    /**
     * Collection Factory
     *
     * @var \Magento\Sales\Api\Data\CreditmemoItemSearchResultInterfaceFactory
     */
    protected $creditmemoItemInterfaceSearchResultFactory = null;

    /**
     * \Magento\Sales\Api\Data\CreditmemoItemInterface[]
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
     * @param \Magento\Sales\Api\Data\CreditmemoItemInterface
     * $creditmemoItemInterfacePersistor
     * @param \Magento\Sales\Api\Data\CreditmemoItemSearchResultInterfaceFactory
     * $creditmemoItemInterfaceSearchResultFactory
     * @param \Magento\Framework\Api\ExtensionAttribute\JoinProcessorInterface
     * $extensionAttributesJoinProcessor
     */
    public function __construct(\Magento\Sales\Api\Data\CreditmemoItemInterfacePersistor $creditmemoItemInterfacePersistor, \Magento\Sales\Api\Data\CreditmemoItemSearchResultInterfaceFactory $creditmemoItemInterfaceSearchResultFactory, \Magento\Framework\Api\ExtensionAttribute\JoinProcessorInterface $extensionAttributesJoinProcessor)
    {
        $this->creditmemoItemInterfacePersistor = $creditmemoItemInterfacePersistor;
        $this->creditmemoItemInterfaceSearchResultFactory = $creditmemoItemInterfaceSearchResultFactory;
        $this->extensionAttributesJoinProcessor = $extensionAttributesJoinProcessor;
    }

    /**
     * load entity
     *
     * @param int $id
     * @return \Magento\Sales\Api\Data\CreditmemoItemInterface
     * @throws \Magento\Framework\Exception\InputException
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function get($id)
    {
        if (!$id) {
            throw new \Magento\Framework\Exception\InputException('ID required');
        }
        if (!isset($this->registry[$id])) {
            $entity = $this->creditmemoItemInterfacePersistor->loadEntity($id);
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
     * @return \Magento\Sales\Api\Data\CreditmemoItemInterface
     */
    public function create(\Magento\Sales\Api\Data\CreditmemoItemInterface $entity)
    {
        return $this->creditmemoItemInterfacePersistor->registerNew($entity);
    }

    /**
     * Register entity to create
     *
     * @param array $data
     * @return \Magento\Sales\Api\Data\CreditmemoItem\Repository
     */
    public function createFromArray(array $data)
    {
        return $this->creditmemoItemInterfacePersistor->registerFromArray($data);
    }

    /**
     * Find entities by criteria
     *
     * @param \Magento\Framework\Api\SearchCriteria $searchCriteria
     * @return \Magento\Sales\Api\Data\CreditmemoItemInterface[]
     */
    public function getList(\Magento\Framework\Api\SearchCriteria $searchCriteria)
    {
        $collection = $this->creditmemoItemInterfaceSearchResultFactory->create();
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
     * @param \Magento\Sales\Api\Data\CreditmemoItemInterface $entity
     */
    public function remove(\Magento\Sales\Api\Data\CreditmemoItemInterface $entity)
    {
        $this->creditmemoItemInterfacePersistor->registerDeleted($entity);
    }

    /**
     * Register entity to delete
     *
     * @param \Magento\Sales\Api\Data\CreditmemoItemInterface $entity
     * @return bool
     */
    public function delete(\Magento\Sales\Api\Data\CreditmemoItemInterface $entity)
    {
        $this->creditmemoItemInterfacePersistor->registerDeleted($entity);
        return $this->creditmemoItemInterfacePersistor->doPersistEntity($entity);
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
        $this->creditmemoItemInterfacePersistor->registerDeleted($entity);
        return $this->creditmemoItemInterfacePersistor->doPersistEntity($entity);
    }

    /**
     * Perform persist operations
     */
    public function flush()
    {
        $ids = $this->creditmemoItemInterfacePersistor->doPersist();
        foreach ($ids as $id) {
        unset($this->registry[$id]);
        }
    }

    /**
     * Perform persist operations for one entity
     *
     * @param \Magento\Sales\Api\Data\CreditmemoItemInterface $entity
     * @return \Magento\Sales\Api\Data\CreditmemoItemInterface
     */
    public function save(\Magento\Sales\Api\Data\CreditmemoItemInterface $entity)
    {
        $this->creditmemoItemInterfacePersistor->doPersistEntity($entity);
        return $entity;
    }
}
