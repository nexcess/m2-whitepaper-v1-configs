<?php
/**
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magento\Catalog\Model\ResourceModel\Eav;

class AttributeTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\Catalog\Model\ResourceModel\Eav\Attribute
     */
    protected $_model;

    protected function setUp()
    {
        $this->_model = \Magento\TestFramework\Helper\Bootstrap::getObjectManager()->create(
            'Magento\Catalog\Model\ResourceModel\Eav\Attribute'
        );
    }

    public function testCRUD()
    {
        $this->_model->setAttributeCode(
            'test'
        )->setEntityTypeId(
            \Magento\TestFramework\Helper\Bootstrap::getObjectManager()->get(
                'Magento\Eav\Model\Config'
            )->getEntityType(
                'catalog_product'
            )->getId()
        )->setFrontendLabel(
            'test'
        );
        $crud = new \Magento\TestFramework\Entity($this->_model, ['frontend_label' => uniqid()]);
        $crud->testCrud();
    }
}
