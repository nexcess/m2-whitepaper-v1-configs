<?php
/**
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magento\ImportExport\Controller\Adminhtml;

/**
 * @magentoAppArea adminhtml
 */
class ImportTest extends \Magento\TestFramework\TestCase\AbstractBackendController
{
    public function testGetFilterAction()
    {
        $this->dispatch('backend/admin/import/index');
        $body = $this->getResponse()->getBody();
        $this->assertContains(
            (string)\Magento\TestFramework\Helper\Bootstrap::getObjectManager()->get(
                'Magento\ImportExport\Helper\Data'
            )->getMaxUploadSizeMessage(),
            $body
        );
    }
}
