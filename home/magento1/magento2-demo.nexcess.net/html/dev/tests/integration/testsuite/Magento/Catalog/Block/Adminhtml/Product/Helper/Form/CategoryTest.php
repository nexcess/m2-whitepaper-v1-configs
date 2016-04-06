<?php
/**
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magento\Catalog\Block\Adminhtml\Product\Helper\Form;

class CategoryTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @magentoAppArea adminhtml
     */
    public function testGetAfterElementHtml()
    {
        $objectManager = \Magento\TestFramework\Helper\Bootstrap::getObjectManager();
        $layout = \Magento\TestFramework\Helper\Bootstrap::getObjectManager()->create(
            'Magento\Framework\View\Layout',
            ['area' => \Magento\Backend\App\Area\FrontNameResolver::AREA_CODE]
        );
        $authorization = \Magento\TestFramework\Helper\Bootstrap::getObjectManager()->create(
            'Magento\Framework\AuthorizationInterface',
            ['aclPolicy' =>  new \Magento\Framework\Authorization\Policy\DefaultPolicy()]
        );

        $block = $objectManager->create(
            'Magento\Catalog\Block\Adminhtml\Product\Helper\Form\Category',
            ['layout' => $layout, 'authorization' => $authorization]
        );

        /** @var $formFactory \Magento\Framework\Data\FormFactory */
        $formFactory = $objectManager->get('Magento\Framework\Data\FormFactory');
        $form = $formFactory->create();
        $block->setForm($form);

        $this->assertRegExp('/<button[^>]*New\sCategory[^>]*>/', $block->getAfterElementHtml());
    }
}
