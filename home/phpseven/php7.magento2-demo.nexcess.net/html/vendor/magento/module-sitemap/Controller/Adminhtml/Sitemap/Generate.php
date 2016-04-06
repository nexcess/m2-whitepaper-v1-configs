<?php
/**
 *
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magento\Sitemap\Controller\Adminhtml\Sitemap;


class Generate extends \Magento\Sitemap\Controller\Adminhtml\Sitemap
{
    /**
     * Generate sitemap
     *
     * @return void
     */
    public function execute()
    {
        // init and load sitemap model
        $id = $this->getRequest()->getParam('sitemap_id');
        $sitemap = $this->_objectManager->create('Magento\Sitemap\Model\Sitemap');
        /* @var $sitemap \Magento\Sitemap\Model\Sitemap */
        $sitemap->load($id);
        // if sitemap record exists
        if ($sitemap->getId()) {
            try {
                $sitemap->generateXml();

                $this->messageManager->addSuccess(
                    __('The sitemap "%1" has been generated.', $sitemap->getSitemapFilename())
                );
            } catch (\Magento\Framework\Exception\LocalizedException $e) {
                $this->messageManager->addError($e->getMessage());
            } catch (\Exception $e) {
                $this->messageManager->addException($e, __('We can\'t generate the sitemap right now.'));
            }
        } else {
            $this->messageManager->addError(__('We can\'t find a sitemap to generate.'));
        }

        // go to grid
        $this->_redirect('adminhtml/*/');
    }
}
