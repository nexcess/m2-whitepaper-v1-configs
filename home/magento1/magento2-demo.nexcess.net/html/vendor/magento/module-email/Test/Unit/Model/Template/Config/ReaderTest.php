<?php
/**
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magento\Email\Test\Unit\Model\Template\Config;

class ReaderTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\Email\Model\Template\Config\Reader
     */
    protected $_model;

    /**
     * @var \Magento\Catalog\Model\Attribute\Config\Converter|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $_converter;

    /**
     * @var \Magento\Framework\Module\Dir\ReverseResolver|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $_moduleDirResolver;

    /**
     * @var \Magento\Framework\Filesystem\File\Read|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $read;

    /**
     * Paths to fixtures
     *
     * @var array
     */
    protected $_paths;

    protected function setUp()
    {
        $fileResolver = $this->getMock(
            'Magento\Email\Model\Template\Config\FileResolver',
            [],
            [],
            '',
            false
        );
        $this->_paths = [
            __DIR__ . '/_files/Fixture/ModuleOne/etc/email_templates_one.xml',
            __DIR__ . '/_files/Fixture/ModuleTwo/etc/email_templates_two.xml',
        ];

        $this->_converter = $this->getMock('Magento\Email\Model\Template\Config\Converter', ['convert']);

        $moduleReader = $this->getMock(
            'Magento\Framework\Module\Dir\Reader',
            ['getModuleDir'],
            [],
            '',
            false
        );
        $moduleReader->expects(
            $this->once()
        )->method(
            'getModuleDir'
        )->with(
            'etc',
            'Magento_Email'
        )->will(
            $this->returnValue('stub')
        );
        $schemaLocator = new \Magento\Email\Model\Template\Config\SchemaLocator($moduleReader);

        $validationStateMock = $this->getMock('Magento\Framework\Config\ValidationStateInterface', [], [], '', true);
        $validationStateMock->expects($this->any())
            ->method('isValidationRequired')
            ->willReturn(false);

        $this->_moduleDirResolver = $this->getMock(
            'Magento\Framework\Module\Dir\ReverseResolver',
            [],
            [],
            '',
            false
        );
        $readFactory = $this->getMock(
            '\Magento\Framework\Filesystem\File\ReadFactory',
            [],
            [],
            '',
            false
        );
        $this->read = $this->getMock('Magento\Framework\Filesystem\File\Read', [], [], '', false);
        $readFactory->expects($this->any())->method('create')->willReturn($this->read);

        $fileIterator = new \Magento\Email\Model\Template\Config\FileIterator(
            $readFactory,
            $this->_paths,
            $this->_moduleDirResolver
        );
        $fileResolver->expects(
            $this->once()
        )->method(
            'get'
        )->with(
            'email_templates.xml',
            'scope'
        )->will(
            $this->returnValue($fileIterator)
        );

        $this->_model = new \Magento\Email\Model\Template\Config\Reader(
            $fileResolver,
            $this->_converter,
            $schemaLocator,
            $validationStateMock
        );
    }

    public function testRead()
    {
        $this->read->expects(
            $this->at(0)
        )->method(
            'readAll'
        )->will(
            $this->returnValue(file_get_contents($this->_paths[0]))
        );
        $this->read->expects(
            $this->at(1)
        )->method(
            'readAll'
        )->will(
            $this->returnValue(file_get_contents($this->_paths[1]))
        );
        $this->_moduleDirResolver->expects(
            $this->at(0)
        )->method(
            'getModuleName'
        )->with(
            __DIR__ . '/_files/Fixture/ModuleOne/etc/email_templates_one.xml'
        )->will(
            $this->returnValue('Fixture_ModuleOne')
        );
        $this->_moduleDirResolver->expects(
            $this->at(1)
        )->method(
            'getModuleName'
        )->with(
            __DIR__ . '/_files/Fixture/ModuleTwo/etc/email_templates_two.xml'
        )->will(
            $this->returnValue('Fixture_ModuleTwo')
        );
        $constraint = function (\DOMDocument $actual) {
            try {
                $expected = file_get_contents(__DIR__ . '/_files/email_templates_merged.xml');
                $expectedNorm = preg_replace('/xsi:noNamespaceSchemaLocation="[^"]*"/', '', $expected, 1);
                $actualNorm = preg_replace('/xsi:noNamespaceSchemaLocation="[^"]*"/', '', $actual->saveXML(), 1);
                \PHPUnit_Framework_Assert::assertXmlStringEqualsXmlString($expectedNorm, $actualNorm);
                return true;
            } catch (\PHPUnit_Framework_AssertionFailedError $e) {
                return false;
            }
        };
        $expectedResult = new \stdClass();
        $this->_converter->expects(
            $this->once()
        )->method(
            'convert'
        )->with(
            $this->callback($constraint)
        )->will(
            $this->returnValue($expectedResult)
        );

        $this->assertSame($expectedResult, $this->_model->read('scope'));
    }

    /**
     * @expectedException \UnexpectedValueException
     * @expectedExceptionMessage Unable to determine a module
     */
    public function testReadUnknownModule()
    {
        $this->_moduleDirResolver->expects($this->once())->method('getModuleName')->will($this->returnValue(null));
        $this->_converter->expects($this->never())->method('convert');
        $this->_model->read('scope');
    }
}
