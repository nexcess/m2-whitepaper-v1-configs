<?php
/**
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magento\Config\Test\Unit\Model\Config\Structure;

/**
 * Class ReaderTest
 */
class ReaderTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\Config\Model\Config\Structure\Reader
     */
    protected $reader;

    /**
     * @var \Magento\Framework\Config\FileResolverInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $fileResolverMock;

    /**
     * @var \Magento\Config\Model\Config\Structure\Converter|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $converterMock;

    /**
     * @var \Magento\Config\Model\Config\SchemaLocator|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $schemaLocatorMock;

    /**
     * @var \Magento\Framework\Config\ValidationStateInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $validationStateMock;

    /**
     * @var \Magento\Framework\View\TemplateEngine\Xhtml\CompilerInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $compilerMock;

    /**
     * Set up
     *
     * @return void
     */
    protected function setUp()
    {
        $this->fileResolverMock = $this->getMockBuilder('Magento\Framework\Config\FileResolverInterface')
            ->getMockForAbstractClass();
        $this->converterMock = $this->getMockBuilder('Magento\Config\Model\Config\Structure\Converter')
            ->disableOriginalConstructor()
            ->getMock();
        $this->schemaLocatorMock = $this->getMockBuilder('Magento\Config\Model\Config\SchemaLocator')
            ->disableOriginalConstructor()
            ->getMock();
        $this->validationStateMock = $this->getMockBuilder('Magento\Framework\Config\ValidationStateInterface')
            ->getMockForAbstractClass();
        $this->compilerMock = $this->getMockBuilder('Magento\Framework\View\TemplateEngine\Xhtml\CompilerInterface')
            ->getMockForAbstractClass();

        $this->reader = new \Magento\Config\Model\Config\Structure\Reader(
            $this->fileResolverMock,
            $this->converterMock,
            $this->schemaLocatorMock,
            $this->validationStateMock,
            $this->compilerMock
        );
    }

    /**
     * Test the successful execution of the 'read' method
     *
     * @return void
     */
    public function testReadSuccessNotValidatedCase()
    {
        $content = '<config><item name="test1"></item><item name="test2"></item></config>';
        $expectedResult = ['result_data'];
        $fileList = ['file' => $content];

        $this->fileResolverMock->expects($this->once())
            ->method('get')
            ->with('system.xml', 'global')
            ->willReturn($fileList);

        $this->compilerMock->expects($this->once())
            ->method('compile')
            ->with(
                $this->isInstanceOf('\DOMElement'),
                $this->isInstanceOf('Magento\Framework\DataObject'),
                $this->isInstanceOf('Magento\Framework\DataObject')
            );
        $this->converterMock->expects($this->once())
            ->method('convert')
            ->with($this->isInstanceOf('\DOMDocument'))
            ->willReturn($expectedResult);

        $this->assertEquals($expectedResult, $this->reader->read());
    }
}
