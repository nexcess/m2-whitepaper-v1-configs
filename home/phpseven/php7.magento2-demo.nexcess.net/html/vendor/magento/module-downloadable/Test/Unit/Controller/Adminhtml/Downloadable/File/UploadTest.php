<?php
/**
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\Downloadable\Test\Unit\Controller\Adminhtml\Downloadable\File;

use Magento\Framework\TestFramework\Unit\Helper\ObjectManager as ObjectManagerHelper;

class UploadTest extends \PHPUnit_Framework_TestCase
{
    /** @var \Magento\Downloadable\Controller\Adminhtml\Downloadable\File\Upload */
    protected $upload;

    /** @var ObjectManagerHelper */
    protected $objectManagerHelper;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject|\Magento\Framework\App\RequestInterface
     */
    protected $request;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject|\Magento\Framework\App\ResponseInterface
     */
    protected $response;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject|\Magento\Downloadable\Model\Link
     */
    protected $link;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject|\Magento\Downloadable\Model\Sample
     */
    protected $sample;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject|\Magento\Backend\App\Action\Context
     */
    protected $context;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject|\Magento\MediaStorage\Model\File\UploaderFactory
     */
    private $uploaderFactory;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject|\Magento\MediaStorage\Helper\File\Storage\Database
     */
    private $storageDatabase;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject|\Magento\Downloadable\Helper\File
     */
    protected $fileHelper;

    /**
     * @var  \PHPUnit_Framework_MockObject_MockObject|\Magento\Backend\Model\Session
     */
    protected $session;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject|\Magento\Framework\Controller\ResultFactory
     */
    protected $resultFactory;

    protected function setUp()
    {
        $this->objectManagerHelper = new ObjectManagerHelper($this);

        $this->storageDatabase = $this->getMockBuilder('\Magento\MediaStorage\Helper\File\Storage\Database')
            ->disableOriginalConstructor()
            ->setMethods(['saveFile'])
            ->getMock();
        $this->uploaderFactory = $this->getMockBuilder('\Magento\MediaStorage\Model\File\UploaderFactory')
            ->disableOriginalConstructor()
            ->setMethods(['create'])
            ->getMock();
        $this->session = $this->getMockBuilder('\Magento\Backend\Model\Session')
            ->disableOriginalConstructor()
            ->getMock();
        $this->resultFactory = $this->getMockBuilder('\Magento\Framework\Controller\ResultFactory')
            ->disableOriginalConstructor()
            ->setMethods(['create'])
            ->getMock();
        $this->context = $this->getMockBuilder('\Magento\Backend\App\Action\Context')
            ->disableOriginalConstructor()
            ->getMock();
        $this->request = $this->getMock('Magento\Framework\App\RequestInterface');
        $this->response = $this->getMock(
            '\Magento\Framework\App\ResponseInterface',
            [
                'setHttpResponseCode',
                'clearBody',
                'sendHeaders',
                'sendResponse',
                'setHeader'
            ]
        );
        $this->fileHelper = $this->getMock(
            '\Magento\Downloadable\Helper\File',
            [
                'uploadFromTmp'
            ],
            [],
            '',
            false
        );
        $this->context->expects($this->any())
            ->method('getRequest')
            ->will($this->returnValue($this->request));
        $this->context->expects($this->any())
            ->method('getSession')
            ->will($this->returnValue($this->session));
        $this->context->expects($this->any())
            ->method('getResultFactory')
            ->will($this->returnValue($this->resultFactory));

        $this->link = $this->getMockBuilder('\Magento\Downloadable\Model\Link')
            ->disableOriginalConstructor()
            ->getMock();
        $this->sample = $this->getMockBuilder('\Magento\Downloadable\Model\Sample')
            ->disableOriginalConstructor()
            ->getMock();

        $this->upload = $this->objectManagerHelper->getObject(
            'Magento\Downloadable\Controller\Adminhtml\Downloadable\File\Upload',
            [
                'context' => $this->context,
                'link' => $this->link,
                'sample' => $this->sample,
                'fileHelper' => $this->fileHelper,
                'uploaderFactory' => $this->uploaderFactory,
                'storageDatabase' => $this->storageDatabase
            ]
        );
    }

    public function testExecute()
    {
        $data = [
            'tmp_name' => 'tmp_name',
            'path' => 'path',
            'file' => 'file'
        ];
        $uploader = $this->getMockBuilder('Magento\MediaStorage\Model\File\Uploader')
            ->disableOriginalConstructor()
            ->getMock();
        $resultJson = $this->getMockBuilder('Magento\Framework\Controller\Result\Json')
            ->disableOriginalConstructor()
            ->setMethods(['setData'])
            ->getMock();
        $this->request->expects($this->once())->method('getParam')->with('type')->willReturn('samples');
        $this->sample->expects($this->once())->method('getBaseTmpPath')->willReturn('base_tmp_path');
        $this->uploaderFactory->expects($this->once())->method('create')->willReturn($uploader);
        $this->fileHelper->expects($this->once())->method('uploadFromTmp')->willReturn($data);
        $this->storageDatabase->expects($this->once())->method('saveFile');
        $this->session->expects($this->once())->method('getName')->willReturn('Name');
        $this->session->expects($this->once())->method('getSessionId')->willReturn('SessionId');
        $this->session->expects($this->once())->method('getCookieLifetime')->willReturn('CookieLifetime');
        $this->session->expects($this->once())->method('getCookiePath')->willReturn('CookiePath');
        $this->session->expects($this->once())->method('getCookieDomain')->willReturn('CookieDomain');
        $this->resultFactory->expects($this->once())->method('create')->willReturn($resultJson);
        $resultJson->expects($this->once())->method('setData')->willReturnSelf();

        $this->assertEquals($resultJson, $this->upload->execute());
    }
}
