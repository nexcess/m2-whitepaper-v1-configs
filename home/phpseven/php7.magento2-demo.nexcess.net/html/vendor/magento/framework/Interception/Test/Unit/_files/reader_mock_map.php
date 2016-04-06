<?php
/**
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
return [
    [
        'global',
        [
            'Magento\Framework\Interception\Test\Unit\Custom\Module\Model\Item' => [
                'plugins' => [
                    'simple_plugin' => [
                        'sortOrder' => 10,
                        'instance' => 'Magento\Framework\Interception\Test\Unit\Custom\Module\Model\ItemPlugin\Simple',
                    ],
                ],
            ]
        ],
    ],
    [
        'backend',
        [
            'Magento\Framework\Interception\Test\Unit\Custom\Module\Model\Item' => [
                'plugins' => [
                    'advanced_plugin' => [
                        'sortOrder' => 5,
                        'instance' => 'Magento\Framework\Interception\Test\Unit\Custom\Module\Model\ItemPlugin\Advanced',
                    ],
                ],
            ],
            'Magento\Framework\Interception\Test\Unit\Custom\Module\Model\ItemContainer' => [
                'plugins' => [
                    'simple_plugin' => [
                        'sortOrder' => 15,
                        'instance' => 'Magento\Framework\Interception\Test\Unit\Custom\Module\Model\ItemContainerPlugin\Simple',
                    ],
                ],
            ],
            'Magento\Framework\Interception\Test\Unit\Custom\Module\Model\StartingBackslash' => [
                'plugins' => [
                    'simple_plugin' => [
                        'sortOrder' => 20,
                        'instance' => '\Magento\Framework\Interception\Test\Unit\Custom\Module\Model\StartingBackslash\Plugin',
                    ],
                ],
            ]
        ]
    ],
    [
        'frontend',
        [
            'Magento\Framework\Interception\Test\Unit\Custom\Module\Model\Item' => [
                'plugins' => ['simple_plugin' => ['disabled' => true]],
            ],
            'Magento\Framework\Interception\Test\Unit\Custom\Module\Model\Item\Enhanced' => [
                'plugins' => [
                    'advanced_plugin' => [
                        'sortOrder' => 5,
                        'instance' => 'Magento\Framework\Interception\Test\Unit\Custom\Module\Model\ItemPlugin\Advanced',
                    ],
                ],
            ],
            'SomeType' => [
                'plugins' => [
                    'simple_plugin' => [
                        'instance' => 'NonExistingPluginClass',
                    ],
                ],
            ]
        ]
    ]
];
