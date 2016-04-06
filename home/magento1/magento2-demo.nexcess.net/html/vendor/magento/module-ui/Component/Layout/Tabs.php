<?php
/**
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magento\Ui\Component\Layout;

use Magento\Framework\View\Element\Template;
use Magento\Framework\View\Element\UiComponent\DataSourceInterface;
use Magento\Ui\Component\Layout\Tabs\TabInterface;
use Magento\Framework\View\Element\UiComponentFactory;
use Magento\Framework\View\Element\UiComponentInterface;
use Magento\Framework\View\Element\UiComponent\LayoutInterface;

/**
 * Class Tabs
 */
class Tabs extends \Magento\Framework\View\Layout\Generic implements LayoutInterface
{
    /**
     * @var string
     */
    protected $navContainerName;

    /**
     * @var UiComponentInterface
     */
    protected $component;

    /**
     * @var string
     */
    protected $namespace;

    /**
     * @var array
     */
    protected $structure = [];

    /**
     * @var int
     */
    protected $sortIncrement = 10;

    /**
     * @var UiComponentFactory
     */
    protected $uiComponentFactory;

    /**
     * Constructor
     *
     * @param UiComponentFactory $uiComponentFactory
     * @param null|string $navContainerName
     */
    public function __construct(UiComponentFactory $uiComponentFactory, $navContainerName = null)
    {
        $this->navContainerName = $navContainerName;
        $this->uiComponentFactory = $uiComponentFactory;
    }

    /**
     * Build
     *
     * @param UiComponentInterface $component
     * @return array
     */
    public function build(UiComponentInterface $component)
    {
        $this->component = $component;
        $this->namespace = $component->getContext()->getNamespace();

        $this->addNavigationBlock();

        // Register html content element
        $this->component->getContext()->addComponentDefinition(
            'html_content',
            [
                'component' => 'Magento_Ui/js/form/components/html',
                'extends' => $this->namespace
            ]
        );

        // Initialization of structure components
        $this->initSections();
        $this->initAreas();

        return parent::build($component);
    }

    /**
     * Add children data
     *
     * @param array $topNode
     * @param UiComponentInterface $component
     * @param string $componentType
     * @return void
     * @throws \Magento\Framework\Exception\LocalizedException
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    protected function addChildren(array &$topNode, UiComponentInterface $component, $componentType)
    {
        $childrenAreas = [];
        $collectedComponents = [];

        foreach ($component->getChildComponents() as $childComponent) {
            if ($childComponent instanceof DataSourceInterface) {
                continue;
            }
            if ($childComponent instanceof \Magento\Ui\Component\Wrapper\Block) {
                $this->addWrappedBlock($childComponent, $childrenAreas);
                continue;
            }

            $name = $childComponent->getName();
            $config = $childComponent->getData('config');
            $collectedComponents[$name] = true;
            if (isset($config['is_collection']) && $config['is_collection'] === true) {
                $label = $childComponent->getData('config/label');
                $this->component->getContext()->addComponentDefinition(
                    'collection',
                    [
                        'component' => 'Magento_Ui/js/form/components/collection',
                        'extends' => $this->namespace
                    ]
                );

                /**
                 * @var UiComponentInterface $childComponent
                 * @var array $structure
                 */
                list($childComponent, $structure) = $this->prepareChildComponents($childComponent, $name);

                $childrenStructure = $structure[$name]['children'];

                $structure[$name]['children'] = [
                    $name . '_collection' => [
                        'type' => 'collection',
                        'config' => [
                            'active' => 1,
                            'removeLabel' => __('Remove %1', $label),
                            'addLabel' => __('Add New %1', $label),
                            'removeMessage' => $childComponent->getData('config/removeMessage'),
                            'itemTemplate' => 'item_template',
                        ],
                        'children' => [
                            'item_template' => ['type' => $this->namespace,
                                'isTemplate' => true,
                                'component' => 'Magento_Ui/js/form/components/collection/item',
                                'childType' => 'group',
                                'config' => [
                                    'label' => __('New %1', $label),
                                ],
                                'children' => $childrenStructure
                            ]
                        ]
                    ]
                ];
            } else {
                /**
                 * @var UiComponentInterface $childComponent
                 * @var array $structure
                 */
                list($childComponent, $structure) = $this->prepareChildComponents($childComponent, $name);
            }

            $tabComponent = $this->createTabComponent($childComponent, $name);

            $childrenAreas[$name] = [
                'type' => $tabComponent->getComponentName(),
                'dataScope' => 'data.' . $name,
                'config' => $config,
                'insertTo' => [
                    $this->namespace . '.sections' => [
                        'position' => $this->getNextSortIncrement()
                    ]
                ],
                'children' => $structure,
            ];
        }

        $this->structure[static::AREAS_KEY]['children'] = $childrenAreas;
        $topNode = $this->structure;
    }

    /**
     * Add wrapped layout block
     *
     * @param \Magento\Ui\Component\Wrapper\Block $childComponent
     * @param array $areas
     * @return void
     */
    protected function addWrappedBlock(\Magento\Ui\Component\Wrapper\Block $childComponent, array &$areas)
    {
        $name = $childComponent->getName();
        /** @var TabInterface $block */
        $block = $childComponent->getBlock();
        if (!$block->canShowTab()) {
            return;
        }
        $block->setData('target_form', $this->namespace);

        $config = [];
        if ($block->isAjaxLoaded()) {
            $config['url'] = $block->getTabUrl();
        } else {
            $config['content'] = $block->toHtml();
        }

        $tabComponent = $this->createTabComponent($childComponent, $name);
        $areas[$name] = [
            'type' => $tabComponent->getComponentName(),
            'dataScope' => $name,
            'insertTo' => [
                $this->namespace . '.sections' => [
                    'position' => $block->hasSortOrder() ? $block->getSortOrder() : $this->getNextSortIncrement()
                ]
            ],
            'config' => [
                'label' => $block->getTabTitle()
            ],
            'children' => [
                $name => [
                    'type' => 'html_content',
                    'dataScope' => $name,
                    'config' => $config,
                ]
            ],
        ];
    }

    /**
     * Create tab component
     *
     * @param UiComponentInterface $childComponent
     * @param string $name
     * @return UiComponentInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    protected function createTabComponent(UiComponentInterface $childComponent, $name)
    {
        $tabComponent = $this->uiComponentFactory->create(
            $name,
            'tab',
            [
                'context' => $this->component->getContext(),
                'components' => [$childComponent->getName() => $childComponent]
            ]
        );
        $tabComponent->prepare();
        $this->component->addComponent($name, $tabComponent);

        return $tabComponent;
    }

    /**
     * To prepare the structure of child components
     *
     * @param UiComponentInterface $component
     * @param string $parentName
     * @return array
     */
    protected function prepareChildComponents(UiComponentInterface $component, $parentName)
    {
        $name = $component->getName();
        $childComponents = $component->getChildComponents();

        $childrenStructure = [];
        foreach ($childComponents as $childName => $child) {
            $isVisible = $child->getData('config/visible');
            if ($isVisible !== null && $isVisible == 0) {
                continue;
            }
            /**
             * @var UiComponentInterface $childComponent
             * @var array $childStructure
             */
            list($childComponent, $childStructure) = $this->prepareChildComponents($child, $component->getName());
            $childrenStructure = array_merge($childrenStructure, $childStructure);
            $component->addComponent($childName, $childComponent);
        }

        $structure = [
            $name => [
                'type' => $component->getComponentName(),
                'name' => $component->getName(),
                'children' => $childrenStructure
            ]
        ];

        list($config, $dataScope) = $this->prepareConfig((array) $component->getConfiguration(), $name, $parentName);

        if ($dataScope !== false) {
            $structure[$name]['dataScope'] = $dataScope;
        }
        $structure[$name]['config'] = $config;

        return [$component, $structure];
    }

    /**
     * Prepare config
     *
     * @param array $config
     * @param string $name
     * @param string $parentName
     * @return array
     */
    protected function prepareConfig(array $config, $name, $parentName)
    {
        $dataScope = false;
        if (!isset($config['displayArea'])) {
            $config['displayArea'] = 'body';
        }
        if (isset($config['dataScope'])) {
            $dataScope = $config['dataScope'];
            unset($config['dataScope']);
        } else if ($name !== $parentName) {
            $dataScope = $name;
        }

        return [$config, $dataScope];
    }

    /**
     * Prepare initial structure for sections
     *
     * @return void
     */
    protected function initSections()
    {
        $this->structure[static::SECTIONS_KEY] = [
            'type' => 'nav',
            'config' => [
                'label' => $this->component->getData('label'),
            ],
            'children' => [],
        ];
    }

    /**
     * Prepare initial structure for areas
     *
     * @return void
     */
    protected function initAreas()
    {
        $this->structure[static::AREAS_KEY] = [
            'type' => $this->namespace,
            'config' => [
                'namespace' => $this->namespace,
            ],
            'children' => [],
        ];
    }

    /**
     * Add navigation block
     *
     * @return void
     */
    protected function addNavigationBlock()
    {
        $pageLayout = $this->component->getContext()->getPageLayout();
        /** @var \Magento\Ui\Component\Layout\Tabs\Nav $navBlock */
        if (isset($this->navContainerName)) {
            $navBlock = $pageLayout->addBlock(
                'Magento\Ui\Component\Layout\Tabs\Nav',
                'tabs_nav',
                $this->navContainerName
            );
        } else {
            $navBlock = $pageLayout->addBlock('Magento\Ui\Component\Layout\Tabs\Nav', 'tabs_nav', 'content');
        }
        $navBlock->setTemplate('Magento_Ui::layout/tabs/nav/default.phtml');
        $navBlock->setData('data_scope', $this->namespace);

        $this->component->getContext()->addComponentDefinition(
            'nav',
            [
                'component' => 'Magento_Ui/js/form/components/tab_group',
                'config' => [
                    'template' => 'ui/tab'
                ],
                'extends' => $this->namespace
            ]
        );
    }

    /**
     * Get next sort increment
     *
     * @return int
     */
    protected function getNextSortIncrement()
    {
        $this->sortIncrement += 10;
        return $this->sortIncrement;
    }
}
