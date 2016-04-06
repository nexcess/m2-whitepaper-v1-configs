<?php
/**
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magento\ImportExport\Test\Unit\Controller\Adminhtml\History;

use Magento\Framework\TestFramework\Unit\Helper\ObjectManager as ObjectManagerHelper;

/**
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class DownloadTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\Framework\App\Request\Http|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $request;

    /**
     * @var \Magento\Framework\ObjectManager\ObjectManager|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $objectManager;

    /**
     * @var \Magento\Backend\Model\View\Result\Redirect|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $resultRaw;

    /**
     * @var \Magento\Framework\Controller\Result\Raw|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $redirect;

    /**
     * @var \Magento\Framework\TestFramework\Unit\Helper\ObjectManager
     */
    protected $objectManagerHelper;

    /**
     * @var \Magento\Backend\App\Action\Context|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $context;

    /**
     * @var \Magento\ImportExport\Controller\Adminhtml\History\Download
     */
    protected $downloadController;

    /**
     * $var \Magento\ImportExport\Helper\Report|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $reportHelper;

    /**
     * @var \Magento\Framework\App\Response\Http\FileFactory|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $fileFactory;

    /**
     * @var \Magento\Framework\Controller\Result\RawFactory|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $resultRawFactory;

    /**
     * @var \Magento\Framework\Controller\Result\RedirectFactory|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $resultRedirectFactory;

    /**
     * Set up
     */
    protected function setUp()
    {
        $this->request = $this->getMock(
            'Magento\Framework\App\Request\Http',
            ['getParam'],
            [],
            '',
            false
        );
        $this->request->expects($this->any())->method('getParam')->with('filename')->willReturn('filename');
        $this->reportHelper = $this->getMock(
            'Magento\ImportExport\Helper\Report',
            ['importFileExists', 'getReportSize', 'getReportOutput'],
            [],
            '',
            false
        );
        $this->reportHelper->expects($this->any())->method('getReportSize')->willReturn(1);
        $this->reportHelper->expects($this->any())->method('getReportOutput')->willReturn('output');
        $this->objectManager = $this->getMock(
            'Magento\Framework\ObjectManager\ObjectManager',
            ['get'],
            [],
            '',
            false
        );
        $this->objectManager->expects($this->any())
            ->method('get')
            ->with('Magento\ImportExport\Helper\Report')
            ->willReturn($this->reportHelper);
        $this->context = $this->getMock(
            'Magento\Backend\App\Action\Context',
            ['getRequest', 'getObjectManager', 'getResultRedirectFactory'],
            [],
            '',
            false
        );
        $this->fileFactory = $this->getMock(
            'Magento\Framework\App\Response\Http\FileFactory',
            ['create'],
            [],
            '',
            false
        );
        $this->resultRaw = $this->getMock(
            'Magento\Framework\Controller\Result\Raw',
            ['setContents'],
            [],
            '',
            false
        );
        $this->resultRawFactory = $this->getMock(
            '\Magento\Framework\Controller\Result\RawFactory',
            ['create'],
            [],
            '',
            false
        );
        $this->resultRawFactory->expects($this->any())->method('create')->willReturn($this->resultRaw);
        $this->redirect = $this->getMock(
            '\Magento\Backend\Model\View\Result\Redirect',
            ['setPath'],
            [],
            '',
            false
        );

        $this->resultRedirectFactory = $this->getMock(
            'Magento\Framework\Controller\Result\RedirectFactory',
            ['create'],
            [],
            '',
            false
        );
        $this->resultRedirectFactory->expects($this->any())->method('create')->willReturn($this->redirect);

        $this->context->expects($this->any())->method('getRequest')->willReturn($this->request);
        $this->context->expects($this->any())->method('getObjectManager')->willReturn($this->objectManager);
        $this->context->expects($this->any())
            ->method('getResultRedirectFactory')
            ->willReturn($this->resultRedirectFactory);

        $this->objectManagerHelper = new ObjectManagerHelper($this);
        $this->downloadController = $this->objectManagerHelper->getObject(
            'Magento\ImportExport\Controller\Adminhtml\History\Download',
            [
                'context' => $this->context,
                'fileFactory' => $this->fileFactory,
                'resultRawFactory' => $this->resultRawFactory,
                'reportHelper' => $this->reportHelper
            ]
        );

    }

    /**
     * Test execute()
     */
    public function testExecute()
    {
        $this->reportHelper->expects($this->any())->method('importFileExists')->willReturn(true);
        $this->resultRaw->expects($this->once())->method('setContents');
        $this->downloadController->execute();
    }

    /**
     * Test execute() with not found file
     */
    public function testExecuteFileNotFound()
    {
        $this->reportHelper->expects($this->any())->method('importFileExists')->willReturn(false);
        $this->resultRaw->expects($this->never())->method('setContents');
        $this->downloadController->execute();
    }
}
