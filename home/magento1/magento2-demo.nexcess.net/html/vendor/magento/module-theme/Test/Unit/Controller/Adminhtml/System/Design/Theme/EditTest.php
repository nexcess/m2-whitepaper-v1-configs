<?php
/**
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magento\Theme\Test\Unit\Controller\Adminhtml\System\Design\Theme;

class EditTest extends \Magento\Theme\Test\Unit\Controller\Adminhtml\System\Design\ThemeTest
{
    /** @var string  */
    protected $name = 'Edit';

    public function testExecuteWithoutLoadedTheme()
    {
        $themeId = 23;
        $this->_request->expects($this->at(0))
            ->method('getParam')
            ->with('id')
            ->willReturn($themeId);

        $theme = $this->getMockForAbstractClass(
            'Magento\Framework\View\Design\ThemeInterface',
            [],
            '',
            false,
            false,
            true,
            ['setType', 'load', 'getId', 'isVisible']
        );
        $theme->expects($this->once())
            ->method('setType');
        $theme->expects($this->once())
            ->method('load')
            ->with($themeId)
            ->willReturnSelf();
        $theme->expects($this->once())
            ->method('getId')
            ->willReturn($themeId);
        $theme->expects($this->once())
            ->method('isVisible')
            ->willReturn(false);

        $this->_objectManagerMock->expects($this->once())
            ->method('create')
            ->with('Magento\Framework\View\Design\ThemeInterface')
            ->willReturn($theme);
        $this->messageManager->expects($this->once())
            ->method('addError');
        $this->session->expects($this->once())
            ->method('setIsUrlNotice')
            ->with(true);
        $this->actionFlag->expects($this->once())
            ->method('get')
            ->willReturn(true);
        $this->response->expects($this->once())
            ->method('setRedirect')
            ->with('http://return.url');
        $this->backendHelper->expects($this->once())
            ->method('getUrl')
            ->willReturn('http://return.url');

        $this->_model->execute();
    }

    public function testExecuteWithException()
    {
        $themeId = 23;
        $this->_request->expects($this->at(0))
            ->method('getParam')
            ->with('id')
            ->willReturn($themeId);

        $theme = $this->getMockForAbstractClass(
            'Magento\Framework\View\Design\ThemeInterface',
            [],
            '',
            false,
            false,
            true,
            ['setType', 'load', 'getId', 'isVisible']
        );
        $theme->expects($this->once())
            ->method('setType');
        $theme->expects($this->once())
            ->method('load')
            ->with($themeId)
            ->willReturnSelf();
        $theme->expects($this->once())
            ->method('getId')
            ->willReturn($themeId);
        $theme->expects($this->once())
            ->method('isVisible')
            ->willReturn(true);

        $this->_objectManagerMock->expects($this->once())
            ->method('create')
            ->with('Magento\Framework\View\Design\ThemeInterface')
            ->willReturn($theme);

        $this->coreRegistry
            ->expects($this->once())
            ->method('register')
            ->willThrowException(new \Exception('Message'));

        $logger = $this->getMockForAbstractClass('Psr\Log\LoggerInterface', [], '', false);
        $logger->expects($this->once())
            ->method('critical');
        $this->_objectManagerMock->expects($this->once())
            ->method('get')
            ->with('Psr\Log\LoggerInterface')
            ->willReturn($logger);

        $this->messageManager->expects($this->once())
            ->method('addError');
        $this->session->expects($this->once())
            ->method('setIsUrlNotice')
            ->with(true);
        $this->actionFlag->expects($this->once())
            ->method('get')
            ->willReturn(true);
        $this->response->expects($this->once())
            ->method('setRedirect')
            ->with('http://return.url');
        $this->backendHelper->expects($this->once())
            ->method('getUrl')
            ->willReturn('http://return.url');

        $this->_model->execute();
    }

    /**
     * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
     */
    public function testExecute()
    {
        $themeId = 23;

        $layout = $this->getMockForAbstractClass('Magento\Framework\View\LayoutInterface', [], '', false);
        $tab = $this->getMock(
            'Magento\Theme\Block\Adminhtml\System\Design\Theme\Edit\Tab\Css',
            ['setFiles', 'canShowTab'],
            [],
            '',
            false
        );
        $menu = $this->getMock('Magento\Backend\Block\Menu', ['setActive', 'getMenuModel'], [], '', false);
        $menuModel = $this->getMock('Magento\Backend\Model\Menu', [], [], '', false);
        $themeHelper = $this->getMock('Magento\Theme\Helper\Theme', [], [], '', false);
        $cssAsset = $this->getMockForAbstractClass('Magento\Framework\View\Asset\LocalInterface', [], '', false);
        $menuItem = $this->getMock('Magento\Backend\Model\Menu\Item', [], [], '', false);
        $resultPage = $this->getMock('Magento\Framework\View\Result\Page', [], [], '', false);
        $pageConfig = $this->getMock('Magento\Framework\View\Page\Config', [], [], '', false);
        $pageTitle = $this->getMock('Magento\Framework\View\Page\Title', [], [], '', false);
        $this->_request->expects($this->at(0))
            ->method('getParam')
            ->with('id')
            ->willReturn($themeId);

        $theme = $this->getMockForAbstractClass(
            'Magento\Framework\View\Design\ThemeInterface',
            [],
            '',
            false,
            false,
            true,
            ['setType', 'load', 'getId', 'isVisible']
        );
        $theme->expects($this->once())
            ->method('setType');
        $theme->expects($this->once())
            ->method('load')
            ->with($themeId)
            ->willReturnSelf();
        $theme->expects($this->once())
            ->method('getId')
            ->willReturn($themeId);
        $theme->expects($this->once())
            ->method('isVisible')
            ->willReturn(true);

        $this->_objectManagerMock
            ->expects($this->once())
            ->method('create')
            ->with('Magento\Framework\View\Design\ThemeInterface')
            ->willReturn($theme);

        $this->coreRegistry
            ->expects($this->once())
            ->method('register')
            ->with('current_theme', $theme);
        $this->view->expects($this->once())
            ->method('loadLayout');
        $tab->expects($this->once())
            ->method('canShowTab')
            ->willReturn(true);
        $tab->expects($this->once())
            ->method('setFiles')
            ->with($cssAsset);
        $layout->expects($this->at(0))
            ->method('getBlock')
            ->with('theme_edit_tabs_tab_css_tab')
            ->willReturn($tab);
        $menu->expects($this->once())
            ->method('setActive')
            ->with('Magento_Theme::system_design_theme');
        $menu->expects($this->once())
            ->method('getMenuModel')
            ->willReturn($menuModel);
        $menuModel->expects($this->once())
            ->method('getParentItems')
            ->with('Magento_Theme::system_design_theme')
            ->willReturn([$menuItem]);
        $menuItem->expects($this->once())
            ->method('getTitle')
            ->willReturn('Title');

        $layout->expects($this->at(1))
            ->method('getBlock')
            ->with('menu')
            ->willReturn($menu);
        $this->view->expects($this->atLeastOnce())
            ->method('getLayout')
            ->willReturn($layout);

        $themeHelper->expects($this->once())
            ->method('getCssAssets')
            ->with($theme)
            ->willReturn($cssAsset);
        $this->_objectManagerMock->expects($this->once())
            ->method('get')
            ->with('Magento\Theme\Helper\Theme')
            ->willReturn($themeHelper);
        $this->view->expects($this->once())
            ->method('getPage')
            ->willReturn($resultPage);
        $resultPage->expects($this->once())
            ->method('getConfig')
            ->willReturn($pageConfig);
        $pageConfig->expects($this->once())
            ->method('getTitle')
            ->willReturn($pageTitle);
        $pageTitle->expects($this->once())
            ->method('prepend')
            ->with('Title');
        $this->view->expects($this->once())
            ->method('renderLayout');

        $this->_model->execute();
    }
}
