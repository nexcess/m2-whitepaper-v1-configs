<?php
/**
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magento\Downloadable\Test\Unit\Model\File;

use Magento\Downloadable\Model\File\ContentValidator;

class ContentValidatorTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var ContentValidator
     */
    protected $validator;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $fileContentMock;

    protected function setUp()
    {
        $this->validator = new \Magento\Downloadable\Model\File\ContentValidator();

        $this->fileContentMock = $this->getMock('\Magento\Downloadable\Api\Data\File\ContentInterface');
    }

    public function testIsValid()
    {
        $this->fileContentMock->expects($this->any())->method('getFileData')
            ->will($this->returnValue(base64_encode('test content')));
        $this->fileContentMock->expects($this->any())->method('getName')
            ->will($this->returnValue('valid_name'));

        $this->assertTrue($this->validator->isValid($this->fileContentMock));
    }

    /**
     * @expectedException \Magento\Framework\Exception\InputException
     * @expectedExceptionMessage Provided content must be valid base64 encoded data.
     */
    public function testIsValidThrowsExceptionIfProvidedContentIsNotBase64Encoded()
    {
        $this->fileContentMock->expects($this->any())->method('getFileData')
            ->will($this->returnValue('not_a_base64_encoded_content'));
        $this->fileContentMock->expects($this->any())->method('getName')
            ->will($this->returnValue('valid_name'));
        $this->assertTrue($this->validator->isValid($this->fileContentMock));
    }

    /**
     * @expectedException \Magento\Framework\Exception\InputException
     * @expectedExceptionMessage Provided file name contains forbidden characters.
     * @dataProvider getInvalidNames
     * @param string $fileName
     */
    public function testIsValidThrowsExceptionIfProvidedImageNameContainsForbiddenCharacters($fileName)
    {
        $this->fileContentMock->expects($this->any())->method('getFileData')
            ->will($this->returnValue(base64_encode('test content')));
        $this->fileContentMock->expects($this->any())->method('getName')
            ->will($this->returnValue($fileName));
        $this->assertTrue($this->validator->isValid($this->fileContentMock));
    }

    /**
     * @return array
     */
    public function getInvalidNames()
    {
        return [
            ['test\test'],
            ['test/test'],
            ['test:test'],
            ['test"test'],
            ['test*test'],
            ['test;test'],
            ['test?test'],
            ['test{test'],
            ['test}test'],
            ['test|test'],
            ['test(test'],
            ['test)test'],
            ['test<test'],
            ['test>test'],
        ];
    }
}
