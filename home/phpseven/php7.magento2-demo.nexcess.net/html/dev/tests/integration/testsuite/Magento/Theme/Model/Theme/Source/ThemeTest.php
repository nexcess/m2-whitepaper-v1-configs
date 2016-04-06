<?php
/**
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magento\Theme\Model\Theme\Source;

use Magento\TestFramework\Helper\Bootstrap;

/**
 * Theme Test
 *
 */
class ThemeTest extends \PHPUnit_Framework_TestCase
{
    public function testGetAllOptions()
    {
        /** @var $model \Magento\Theme\Model\Theme\Source\Theme */
        $model = Bootstrap::getObjectManager()->create('Magento\Theme\Model\Theme\Source\Theme');

        /** @var $expectedCollection \Magento\Theme\Model\Theme\Collection */
        $expectedCollection = Bootstrap::getObjectManager()
            ->create('Magento\Theme\Model\ResourceModel\Theme\Collection');
        $expectedCollection->addFilter('area', 'frontend');

        $expectedItemsCount = count($expectedCollection);

        $labelsCollection = $model->getAllOptions(false);
        $this->assertEquals($expectedItemsCount, count($labelsCollection));

        $labelsCollection = $model->getAllOptions(true);
        $this->assertEquals(++$expectedItemsCount, count($labelsCollection));
    }
}
