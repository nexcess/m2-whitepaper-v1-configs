<?php
/**
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magento\Ui\Test\Unit\Model\Export;

use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Framework\Filesystem;
use Magento\Framework\Filesystem\Directory\WriteInterface as DirectoryWriteInterface;
use Magento\Framework\Filesystem\File\WriteInterface as FileWriteInterface;
use Magento\Framework\View\Element\UiComponentInterface;
use Magento\Ui\Component\MassAction\Filter;
use Magento\Ui\Model\Export\ConvertToCsv;
use Magento\Ui\Model\Export\MetadataProvider;

class ConvertToCsvTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var ConvertToCsv
     */
    protected $model;

    /**
     * @var DirectoryWriteInterface | \PHPUnit_Framework_MockObject_MockObject
     */
    protected $directory;

    /**
     * @var Filesystem | \PHPUnit_Framework_MockObject_MockObject
     */
    protected $filesystem;

    /**
     * @var Filter | \PHPUnit_Framework_MockObject_MockObject
     */
    protected $filter;

    /**
     * @var MetadataProvider | \PHPUnit_Framework_MockObject_MockObject
     */
    protected $metadataProvider;

    /**
     * @var FileWriteInterface | \PHPUnit_Framework_MockObject_MockObject
     */
    protected $stream;

    /**
     * @var UiComponentInterface | \PHPUnit_Framework_MockObject_MockObject
     */
    protected $component;

    public function setUp()
    {
        $this->directory = $this->getMockBuilder('Magento\Framework\Filesystem\Directory\WriteInterface')
            ->getMockForAbstractClass();

        $this->filesystem = $this->getMockBuilder('Magento\Framework\Filesystem')
            ->disableOriginalConstructor()
            ->getMock();
        $this->filesystem->expects($this->any())
            ->method('getDirectoryWrite')
            ->with(DirectoryList::VAR_DIR)
            ->willReturn($this->directory);

        $this->filter = $this->getMockBuilder('Magento\Ui\Component\MassAction\Filter')
            ->disableOriginalConstructor()
            ->getMock();

        $this->metadataProvider = $this->getMockBuilder('Magento\Ui\Model\Export\MetadataProvider')
            ->disableOriginalConstructor()
            ->getMock();

        $this->component = $this->getMockBuilder('Magento\Framework\View\Element\UiComponentInterface')
            ->getMockForAbstractClass();

        $this->stream = $this->getMockBuilder('Magento\Framework\Filesystem\File\WriteInterface')
            ->setMethods([
                'lock',
                'unlock',
                'close',
            ])
            ->getMockForAbstractClass();

        $this->model = new ConvertToCsv(
            $this->filesystem,
            $this->filter,
            $this->metadataProvider
        );
    }

    public function testGetCsvFile()
    {
        $componentName = 'component_name';
        $data = ['data_value'];

        $document = $this->getMockBuilder('Magento\Framework\Api\Search\DocumentInterface')
            ->getMockForAbstractClass();

        $this->mockComponent($componentName, [$document]);
        $this->mockFilter();
        $this->mockDirectory();

        $this->stream->expects($this->once())
            ->method('lock')
            ->willReturnSelf();
        $this->stream->expects($this->once())
            ->method('unlock')
            ->willReturnSelf();
        $this->stream->expects($this->once())
            ->method('close')
            ->willReturnSelf();
        $this->stream->expects($this->any())
            ->method('writeCsv')
            ->with($data)
            ->willReturnSelf();

        $this->metadataProvider->expects($this->once())
            ->method('getOptions')
            ->willReturn([]);
        $this->metadataProvider->expects($this->once())
            ->method('getHeaders')
            ->with($this->component)
            ->willReturn($data);
        $this->metadataProvider->expects($this->once())
            ->method('getFields')
            ->with($this->component)
            ->willReturn([]);
        $this->metadataProvider->expects($this->once())
            ->method('getRowData')
            ->with($document, [], [])
            ->willReturn($data);
        $this->metadataProvider->expects($this->once())
            ->method('convertDate')
            ->with($document, $componentName);

        $result = $this->model->getCsvFile();
        $this->assertTrue(is_array($result));
        $this->assertArrayHasKey('type', $result);
        $this->assertArrayHasKey('value', $result);
        $this->assertArrayHasKey('rm', $result);
        $this->assertContains($componentName, $result);
        $this->assertContains('.csv', $result);
    }

    /**
     * @param array $expected
     */
    protected function mockStream($expected)
    {
        $this->stream = $this->getMockBuilder('Magento\Framework\Filesystem\File\WriteInterface')
            ->setMethods([
                'lock',
                'unlock',
                'close',
            ])
            ->getMockForAbstractClass();

        $this->stream->expects($this->once())
            ->method('lock')
            ->willReturnSelf();
        $this->stream->expects($this->once())
            ->method('unlock')
            ->willReturnSelf();
        $this->stream->expects($this->once())
            ->method('close')
            ->willReturnSelf();
        $this->stream->expects($this->once())
            ->method('writeCsv')
            ->with($expected)
            ->willReturnSelf();
    }

    /**
     * @param string $componentName
     * @param array $items
     */
    protected function mockComponent($componentName, $items)
    {
        $context = $this->getMockBuilder('Magento\Framework\View\Element\UiComponent\ContextInterface')
            ->setMethods(['getDataProvider'])
            ->getMockForAbstractClass();

        $dataProvider = $this->getMockBuilder(
            'Magento\Framework\View\Element\UiComponent\DataProvider\DataProviderInterface'
        )
            ->setMethods(['getSearchResult'])
            ->getMockForAbstractClass();

        $searchResult = $this->getMockBuilder('Magento\Framework\Api\Search\SearchResultInterface')
            ->setMethods(['getItems'])
            ->getMockForAbstractClass();

        $this->component->expects($this->any())
            ->method('getName')
            ->willReturn($componentName);
        $this->component->expects($this->once())
            ->method('getContext')
            ->willReturn($context);

        $context->expects($this->once())
            ->method('getDataProvider')
            ->willReturn($dataProvider);

        $dataProvider->expects($this->once())
            ->method('getSearchResult')
            ->willReturn($searchResult);

        $searchResult->expects($this->once())
            ->method('getItems')
            ->willReturn($items);
    }

    protected function mockFilter()
    {
        $this->filter->expects($this->once())
            ->method('getComponent')
            ->willReturn($this->component);
        $this->filter->expects($this->once())
            ->method('prepareComponent')
            ->with($this->component)
            ->willReturnSelf();
        $this->filter->expects($this->once())
            ->method('applySelectionOnTargetProvider')
            ->willReturnSelf();
    }

    protected function mockDirectory()
    {
        $this->directory->expects($this->once())
            ->method('create')
            ->with('export')
            ->willReturnSelf();
        $this->directory->expects($this->once())
            ->method('openFile')
            ->willReturn($this->stream);
    }
}
