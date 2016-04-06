<?php
/**
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magento\Backend\Model\Auth;

/**
 * Backend Auth Storage interface
 */
interface StorageInterface
{
    /**
     * Perform login specific actions
     *
     * @return $this
     * @abstract
     * @api
     */
    public function processLogin();

    /**
     * Perform login specific actions
     *
     * @return $this
     * @abstract
     * @api
     */
    public function processLogout();

    /**
     * Check if user is logged in
     *
     * @return bool
     * @abstract
     * @api
     */
    public function isLoggedIn();

    /**
     * Prolong storage lifetime
     *
     * @return void
     * @abstract
     * @api
     */
    public function prolong();
}
