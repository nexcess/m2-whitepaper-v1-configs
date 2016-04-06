<?php
/**
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\Setup\Test\Unit\Fixtures;

use \Magento\Setup\Fixtures\CartPriceRulesFixture;

class CartPriceRulesFixtureTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \PHPUnit_Framework_MockObject_MockObject|\Magento\Setup\Fixtures\FixtureModel
     */
    private $fixtureModelMock;

    /**
     * @var \Magento\Setup\Fixtures\CartPriceRulesFixture
     */
    private $model;

    public function setUp()
    {
        $this->fixtureModelMock = $this->getMock('\Magento\Setup\Fixtures\FixtureModel', [], [], '', false);

        $this->model = new CartPriceRulesFixture($this->fixtureModelMock);
    }

    public function testExecute()
    {
        $storeMock = $this->getMock('\Magento\Store\Model\Store', [], [], '', false);
        $storeMock->expects($this->once())
            ->method('getRootCategoryId')
            ->will($this->returnValue(2));

        $websiteMock = $this->getMock('\Magento\Store\Model\Website', [], [], '', false);
        $websiteMock->expects($this->once())
            ->method('getGroups')
            ->will($this->returnValue([$storeMock]));
        $websiteMock->expects($this->once())
            ->method('getId')
            ->will($this->returnValue('website_id'));

        $contextMock = $this->getMock('\Magento\Framework\Model\ResourceModel\Db\Context', [], [], '', false);
        $abstractDbMock = $this->getMockForAbstractClass(
            '\Magento\Framework\Model\ResourceModel\Db\AbstractDb',
            [$contextMock],
            '',
            true,
            true,
            true,
            ['getAllChildren']
        );
        $abstractDbMock->expects($this->once())
            ->method('getAllChildren')
            ->will($this->returnValue([1]));

        $storeManagerMock = $this->getMock('Magento\Store\Model\StoreManager', [], [], '', false);
        $storeManagerMock->expects($this->once())
            ->method('getWebsites')
            ->will($this->returnValue([$websiteMock]));

        $categoryMock = $this->getMock('Magento\Catalog\Model\Category', [], [], '', false);
        $categoryMock->expects($this->once())
            ->method('getResource')
            ->will($this->returnValue($abstractDbMock));
        $categoryMock->expects($this->once())
            ->method('getPath')
            ->will($this->returnValue('path/to/file'));
        $categoryMock->expects($this->once())
            ->method('getId')
            ->will($this->returnValue('category_id'));

        $modelMock = $this->getMock('\Magento\SalesRule\Model\Rule', [], [], '', false);
        $modelMock->expects($this->once())
            ->method('getIdFieldName')
            ->will($this->returnValue('Field Id Name'));

        $objectValueMap = [
            ['Magento\SalesRule\Model\Rule', $modelMock],
            ['Magento\Catalog\Model\Category', $categoryMock]
        ];

        $objectManagerMock = $this->getMock('Magento\Framework\ObjectManager\ObjectManager', [], [], '', false);
        $objectManagerMock->expects($this->once())
            ->method('create')
            ->will($this->returnValue($storeManagerMock));
        $objectManagerMock->expects($this->exactly(2))
            ->method('get')
            ->will($this->returnValueMap($objectValueMap));

        $valueMap = [
            ['cart_price_rules', 0, 1],
            ['cart_price_rules_floor', 3, 3]
        ];

        $this->fixtureModelMock
            ->expects($this->exactly(2))
            ->method('getValue')
            ->will($this->returnValueMap($valueMap));
        $this->fixtureModelMock
            ->expects($this->exactly(3))
            ->method('getObjectManager')
            ->will($this->returnValue($objectManagerMock));

        $this->model->execute();
    }

    public function testNoFixtureConfigValue()
    {
        $ruleMock = $this->getMock('\Magento\SalesRule\Model\Rule', [], [], '', false);
        $ruleMock->expects($this->never())->method('save');

        $objectManagerMock = $this->getMock('Magento\Framework\ObjectManager\ObjectManager', [], [], '', false);
        $objectManagerMock->expects($this->never())
            ->method('get')
            ->with($this->equalTo('Magento\SalesRule\Model\Rule'))
            ->willReturn($ruleMock);

        $this->fixtureModelMock
            ->expects($this->never())
            ->method('getObjectManager')
            ->willReturn($objectManagerMock);
        $this->fixtureModelMock
            ->expects($this->once())
            ->method('getValue')
            ->willReturn(false);

        $this->model->execute();
    }

    public function testGetActionTitle()
    {
        $this->assertSame('Generating Cart Price Rules', $this->model->getActionTitle());
    }

    public function testIntroduceParamLabels()
    {
        $this->assertSame([
            'cart_price_rules' => 'Cart Price Rules'
        ], $this->model->introduceParamLabels());
    }
}
