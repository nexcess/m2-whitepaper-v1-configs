<?php
/**
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magento\Developer\Test\Unit\Model\TemplateEngine\Plugin;

use Magento\Developer\Model\TemplateEngine\Decorator\DebugHintsFactory;
use Magento\Developer\Model\TemplateEngine\Plugin\DebugHints;
use Magento\Store\Model\ScopeInterface;

class DebugHintsTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\Framework\App\Config\ScopeConfigInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $scopeConfigMock;

    /**
     * @var \Magento\Store\Model\StoreManagerInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $storeManager;

    /**
     * @var \Magento\Developer\Helper\Data|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $devHelperMock;

    /**
     * @var DebugHintsFactory | \PHPUnit_Framework_MockObject_MockObject
     */
    protected $debugHintsFactory;

    /**
     * @return void
     */
    protected function setUp()
    {
        $this->scopeConfigMock = $this->getMockBuilder('Magento\Framework\App\Config\ScopeConfigInterface')
            ->getMockForAbstractClass();

        $this->storeManager = $this->getMockBuilder('Magento\Store\Model\StoreManagerInterface')
            ->getMockForAbstractClass();

        $this->devHelperMock = $this->getMockBuilder('Magento\Developer\Helper\Data')
            ->disableOriginalConstructor()
            ->getMock();

        $this->debugHintsFactory = $this->getMockBuilder(
            'Magento\Developer\Model\TemplateEngine\Decorator\DebugHintsFactory'
        )
            ->setMethods(['create'])
            ->disableOriginalConstructor()
            ->getMock();
    }

    /**
     * @param string $debugHintsPath
     * @param bool $showBlockHints
     * @return void
     * @dataProvider afterCreateActiveDataProvider
     */
    public function testAfterCreateActive($debugHintsPath, $showBlockHints)
    {
        $this->devHelperMock->expects($this->once())
            ->method('isDevAllowed')
            ->willReturn(true);

        $this->setupConfigFixture($debugHintsPath, true, $showBlockHints);

        $engine = $this->getMock('Magento\Framework\View\TemplateEngineInterface');

        $debugHintsDecorator = $this->getMockBuilder(
            'Magento\Developer\Model\TemplateEngine\Decorator\DebugHints'
        )
            ->disableOriginalConstructor()
            ->getMock();

        $this->debugHintsFactory->expects($this->once())
            ->method('create')
            ->with([
                'subject' => $engine,
                'showBlockHints' => $showBlockHints,
            ])
            ->willReturn($debugHintsDecorator);

        $subjectMock = $this->getMockBuilder('Magento\Framework\View\TemplateEngineFactory')
            ->disableOriginalConstructor()
            ->getMock();

        $debugHints = new DebugHints(
            $this->scopeConfigMock,
            $this->storeManager,
            $this->devHelperMock,
            $this->debugHintsFactory,
            $debugHintsPath
        );

        $this->assertEquals($debugHintsDecorator, $debugHints->afterCreate($subjectMock, $engine));
    }

    /**
     * @return array
     */
    public function afterCreateActiveDataProvider()
    {
        return [
            ['dev/debug/template_hints_storefront', false],
            ['dev/debug/template_hints_storefront', true],
            ['dev/debug/template_hints_admin', false],
            ['dev/debug/template_hints_admin', true],
        ];
    }

    /**
     * @param string $debugHintsPath
     * @param bool $isDevAllowed
     * @param bool $showTemplateHints
     * @return void
     * @dataProvider afterCreateInactiveDataProvider
     */
    public function testAfterCreateInactive($debugHintsPath, $isDevAllowed, $showTemplateHints)
    {
        $this->devHelperMock->expects($this->any())
            ->method('isDevAllowed')
            ->willReturn($isDevAllowed);

        $this->setupConfigFixture($debugHintsPath, $showTemplateHints, true);

        $engine = $this->getMock('Magento\Framework\View\TemplateEngineInterface');

        $subjectMock = $this->getMockBuilder('Magento\Framework\View\TemplateEngineFactory')
            ->disableOriginalConstructor()
            ->getMock();

        $debugHints = new DebugHints(
            $this->scopeConfigMock,
            $this->storeManager,
            $this->devHelperMock,
            $this->debugHintsFactory,
            $debugHintsPath
        );

        $this->assertSame($engine, $debugHints->afterCreate($subjectMock, $engine));
    }

    /**
     * @return array
     */
    public function afterCreateInactiveDataProvider()
    {
        return [
            ['dev/debug/template_hints_storefront', false, false],
            ['dev/debug/template_hints_storefront', false, true],
            ['dev/debug/template_hints_storefront', true, false],
            ['dev/debug/template_hints_admin', false, false],
            ['dev/debug/template_hints_admin', false, true],
            ['dev/debug/template_hints_admin', true, false],
        ];
    }

    /**
     * Setup fixture values for store config
     *
     * @param string $debugHintsPath
     * @param bool $showTemplateHints
     * @param bool $showBlockHints
     * @return void
     */
    protected function setupConfigFixture($debugHintsPath, $showTemplateHints, $showBlockHints)
    {
        $storeCode = 'default';
        $storeMock = $this->getMock('Magento\Store\Api\Data\StoreInterface');
        $storeMock->expects($this->once())
            ->method('getCode')
            ->willReturn($storeCode);
        $this->storeManager->expects($this->once())
            ->method('getStore')
            ->willReturn($storeMock);

        $this->scopeConfigMock->expects($this->atLeastOnce())
            ->method('getValue')
            ->willReturnMap([
                [
                    $debugHintsPath,
                    ScopeInterface::SCOPE_STORE,
                    $storeCode,
                    $showTemplateHints,
                ],
                [
                    DebugHints::XML_PATH_DEBUG_TEMPLATE_HINTS_BLOCKS,
                    ScopeInterface::SCOPE_STORE,
                    $storeCode,
                    $showBlockHints
                ]
            ]);
    }
}
