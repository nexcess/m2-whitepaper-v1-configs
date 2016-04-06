<?php
/**
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magento\Setup\Test\Unit\Module\I18n\Dictionary\Writer;

class CsvTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var string
     */
    protected $_testFile;

    /**
     * @var \Magento\Setup\Module\I18n\Dictionary\Phrase|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $_phraseFirstMock;

    /**
     * @var \Magento\Setup\Module\I18n\Dictionary\Phrase|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $_phraseSecondMock;

    protected function setUp()
    {
        $this->_testFile = str_replace('\\', '/', realpath(dirname(__FILE__))) . '/_files/test.csv';

        $this->_phraseFirstMock = $this->getMock(
            'Magento\Setup\Module\I18n\Dictionary\Phrase',
            [],
            [],
            '',
            false
        );
        $this->_phraseSecondMock = $this->getMock(
            'Magento\Setup\Module\I18n\Dictionary\Phrase',
            [],
            [],
            '',
            false
        );
    }

    protected function tearDown()
    {
        if (file_exists($this->_testFile)) {
            unlink($this->_testFile);
        }
    }

    /**
     * @expectedException \InvalidArgumentException
     * @expectedExceptionMessage Cannot open file for write dictionary: "wrong/path"
     */
    public function testWrongOutputFile()
    {
        $objectManagerHelper = new \Magento\Framework\TestFramework\Unit\Helper\ObjectManager($this);
        $objectManagerHelper->getObject(
            'Magento\Setup\Module\I18n\Dictionary\Writer\Csv',
            ['outputFilename' => 'wrong/path']
        );
    }

    public function testWrite()
    {
        $this->_phraseFirstMock->expects(
            $this->once()
        )->method(
            'getCompiledPhrase'
        )->will(
            $this->returnValue("phrase1_quote'")
        );
        $this->_phraseFirstMock->expects(
            $this->once()
        )->method(
            'getCompiledTranslation'
        )->will(
            $this->returnValue("translation1_quote'")
        );
        $this->_phraseFirstMock->expects(
            $this->once()
        )->method(
            'getContextType'
        )->will(
            $this->returnValue("context_type1_quote\\'")
        );
        $this->_phraseFirstMock->expects(
            $this->once()
        )->method(
            'getContextValueAsString'
        )->will(
            $this->returnValue("content_value1_quote\\'")
        );

        $this->_phraseSecondMock->expects(
            $this->once()
        )->method(
            'getCompiledPhrase'
        )->will(
            $this->returnValue("phrase2_quote'")
        );
        $this->_phraseSecondMock->expects(
            $this->once()
        )->method(
            'getCompiledTranslation'
        )->will(
            $this->returnValue("translation2_quote'")
        );
        $this->_phraseSecondMock->expects(
            $this->once()
        )->method(
            'getContextType'
        )->will(
            $this->returnValue("context_type2_quote\\'")
        );
        $this->_phraseSecondMock->expects(
            $this->once()
        )->method(
            'getContextValueAsString'
        )->will(
            $this->returnValue("content_value2_quote\\'")
        );

        $objectManagerHelper = new \Magento\Framework\TestFramework\Unit\Helper\ObjectManager($this);
        /** @var \Magento\Setup\Module\I18n\Dictionary\Writer\Csv $writer */
        $writer = $objectManagerHelper->getObject(
            'Magento\Setup\Module\I18n\Dictionary\Writer\Csv',
            ['outputFilename' => $this->_testFile]
        );
        $writer->write($this->_phraseFirstMock);
        $writer->write($this->_phraseSecondMock);

        $expected = <<<EXPECTED
phrase1_quote',translation1_quote',"context_type1_quote\'","content_value1_quote\'"
phrase2_quote',translation2_quote',"context_type2_quote\'","content_value2_quote\'"

EXPECTED;

        $this->assertEquals($expected, file_get_contents($this->_testFile));
    }

    public function testWriteWithoutContext()
    {
        $this->_phraseFirstMock->expects($this->once())
            ->method('getCompiledPhrase')
            ->willReturn('phrase1');
        $this->_phraseFirstMock->expects($this->once())
            ->method('getCompiledTranslation')
            ->willReturn('translation1');
        $this->_phraseFirstMock->expects($this->once())->method('getContextType')->will($this->returnValue(''));

        $this->_phraseSecondMock->expects($this->once())
            ->method('getCompiledPhrase')
            ->willReturn('phrase2');
        $this->_phraseSecondMock->expects($this->once())
            ->method('getCompiledTranslation')
            ->willReturn('translation2');
        $this->_phraseSecondMock->expects($this->once())
            ->method('getContextType')
            ->willReturn('context_type2');
        $this->_phraseSecondMock->expects($this->once())
            ->method('getContextValueAsString')
            ->willReturn('');

        $objectManagerHelper = new \Magento\Framework\TestFramework\Unit\Helper\ObjectManager($this);
        /** @var \Magento\Setup\Module\I18n\Dictionary\Writer\Csv $writer */
        $writer = $objectManagerHelper->getObject(
            'Magento\Setup\Module\I18n\Dictionary\Writer\Csv',
            ['outputFilename' => $this->_testFile]
        );
        $writer->write($this->_phraseFirstMock);
        $writer->write($this->_phraseSecondMock);

        $expected = "phrase1,translation1\nphrase2,translation2\n";
        $this->assertEquals($expected, file_get_contents($this->_testFile));
    }
}
