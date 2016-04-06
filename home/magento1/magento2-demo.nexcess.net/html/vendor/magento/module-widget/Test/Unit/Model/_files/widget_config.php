<?php
/**
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
return [
    'sales_widget_guestform' => [
        '@' => ['type' => 'Magento\Sales\Block\Widget\Guest\Form'],
        'is_email_compatible' => '1',
        'name' => 'Orders and Returns',
        'description' => 'Orders and Returns Search Form',
        'parameters' => [
            'title' => ['type' => 'text', 'visible' => '0', 'label' => 'Anchor Custom Title'],
            'template' => [
                'type' => 'select',
                'value' => 'hierarchy/widget/link/link_block.phtml',
                'values' => [
                    'default' => [
                        'value' => 'hierarchy/widget/link/link_block.phtml',
                        'label' => 'CMS Page Link Block Template',
                    ],
                    'link_inline' => [
                        'value' => 'hierarchy/widget/link/link_inline.phtml',
                        'label' => 'CMS Page Link Inline Template',
                    ],
                ],
                'visible' => '0',
            ],
            'link_display' => [
                'source_model' => 'Magento\Config\Model\Config\Source\Yesno',
                'type' => 'select',
                'visible' => '1',
                'sort_order' => '10',
                'label' => 'Display a Link to Loading a Spreadsheet',
                'description' => "Defines whether a link to My Account",
            ],
            'link_text' => [
                'type' => 'text',
                'value' => 'Load a list of SKUs',
                'visible' => '1',
                'required' => '1',
                'sort_order' => '20',
                'label' => 'Link Text',
                'description' => 'The text of the link to the My Account &gt; Order by SKU page',
                'depends' => ['link_display' => ['value' => '1']],
            ],
            'id_path' => [
                'type' => 'label',
                '@' => ['type' => 'complex'],
                'helper_block' => [
                    'type' => 'Magento\Backend\Block\Catalog\Product\Widget\Chooser',
                    'data' => ['button' => ['open' => 'Select Product...']],
                ],
                'visible' => '1',
                'required' => '1',
                'sort_order' => '10',
                'label' => 'Product',
            ],
            'condition' => [
                'type' => 'Magento\CatalogWidget\Block\Product\Widget\Conditions',
                'visible' => '1',
                'required' => '1',
                'sort_order' => '10',
                'label' => 'Conditions',
             ],

        ],
        'supported_containers' => [
            '0' => ['container_name' => 'left', 'template' => ['default' => 'default_template']],
            '1' => ['container_name' => 'right', 'template' => ['default' => 'default_template']],
        ],
    ]
];
