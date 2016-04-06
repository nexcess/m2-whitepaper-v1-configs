<?php
/**
 *
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\Customer\Api;

/**
 * Interface for managing customers accounts.
 */
interface AccountManagementInterface
{
    /**#@+
     * Constant for confirmation status
     */
    const ACCOUNT_CONFIRMED = 'account_confirmed';
    const ACCOUNT_CONFIRMATION_REQUIRED = 'account_confirmation_required';
    const ACCOUNT_CONFIRMATION_NOT_REQUIRED = 'account_confirmation_not_required';
    /**#@-*/

    /**
     * Create customer account. Perform necessary business operations like sending email.
     *
     * @api
     * @param \Magento\Customer\Api\Data\CustomerInterface $customer
     * @param string $password
     * @param string $redirectUrl
     * @return \Magento\Customer\Api\Data\CustomerInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function createAccount(
        \Magento\Customer\Api\Data\CustomerInterface $customer,
        $password = null,
        $redirectUrl = ''
    );

    /**
     * Create customer account using provided hashed password. Should not be exposed as a webapi.
     *
     * @api
     * @param \Magento\Customer\Api\Data\CustomerInterface $customer
     * @param string $hash Password hash that we can save directly
     * @param string $redirectUrl URL fed to welcome email templates. Can be used by templates to, for example, direct
     *                            the customer to a product they were looking at after pressing confirmation link.
     * @return \Magento\Customer\Api\Data\CustomerInterface
     * @throws \Magento\Framework\Exception\InputException If bad input is provided
     * @throws \Magento\Framework\Exception\State\InputMismatchException If the provided email is already used
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function createAccountWithPasswordHash(
        \Magento\Customer\Api\Data\CustomerInterface $customer,
        $hash,
        $redirectUrl = ''
    );

    /**
     * Validate customer data.
     *
     * @api
     * @param \Magento\Customer\Api\Data\CustomerInterface $customer
     * @return \Magento\Customer\Api\Data\ValidationResultsInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function validate(\Magento\Customer\Api\Data\CustomerInterface $customer);

    /**
     * Check if customer can be deleted.
     *
     * @api
     * @param int $customerId
     * @return bool
     * @throws \Magento\Framework\Exception\NoSuchEntityException If group is not found
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function isReadonly($customerId);

    /**
     * Activate a customer account using a key that was sent in a confirmation email.
     *
     * @api
     * @param string $email
     * @param string $confirmationKey
     * @return \Magento\Customer\Api\Data\CustomerInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function activate($email, $confirmationKey);

    /**
     * Activate a customer account using a key that was sent in a confirmation email.
     *
     * @api
     * @param int $customerId
     * @param string $confirmationKey
     * @return \Magento\Customer\Api\Data\CustomerInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function activateById($customerId, $confirmationKey);

    /**
     * Authenticate a customer by username and password
     *
     * @api
     * @param string $email
     * @param string $password
     * @return \Magento\Customer\Api\Data\CustomerInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function authenticate($email, $password);

    /**
     * Change customer password.
     *
     * @api
     * @param string $email
     * @param string $currentPassword
     * @param string $newPassword
     * @return bool true on success
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function changePassword($email, $currentPassword, $newPassword);

    /**
     * Change customer password.
     *
     * @api
     * @param int $customerId
     * @param string $currentPassword
     * @param string $newPassword
     * @return bool true on success
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function changePasswordById($customerId, $currentPassword, $newPassword);

    /**
     * Send an email to the customer with a password reset link.
     *
     * @api
     * @param string $email
     * @param string $template
     * @param int $websiteId
     * @return bool true on success
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function initiatePasswordReset($email, $template, $websiteId = null);

    /**
     * Reset customer password.
     *
     * @api
     * @param string $email
     * @param string $resetToken
     * @param string $newPassword
     * @return bool true on success
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function resetPassword($email, $resetToken, $newPassword);

    /**
     * Check if password reset token is valid.
     *
     * @api
     * @param int $customerId
     * @param string $resetPasswordLinkToken
     * @return bool True if the token is valid
     * @throws \Magento\Framework\Exception\State\InputMismatchException If token is mismatched
     * @throws \Magento\Framework\Exception\State\ExpiredException If token is expired
     * @throws \Magento\Framework\Exception\InputException If token or customer id is invalid
     * @throws \Magento\Framework\Exception\NoSuchEntityException If customer doesn't exist
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function validateResetPasswordLinkToken($customerId, $resetPasswordLinkToken);

    /**
     * Gets the account confirmation status.
     *
     * @api
     * @param int $customerId
     * @return string
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getConfirmationStatus($customerId);

    /**
     * Resend confirmation email.
     *
     * @api
     * @param string $email
     * @param int $websiteId
     * @param string $redirectUrl
     * @return bool true on success
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function resendConfirmation($email, $websiteId, $redirectUrl = '');

    /**
     * Check if given email is associated with a customer account in given website.
     *
     * @api
     * @param string $customerEmail
     * @param int $websiteId If not set, will use the current websiteId
     * @return bool
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function isEmailAvailable($customerEmail, $websiteId = null);

    /**
     * Check store availability for customer given the customerId.
     *
     * @api
     * @param int $customerWebsiteId
     * @param int $storeId
     * @return bool
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function isCustomerInStore($customerWebsiteId, $storeId);

    /**
     * Retrieve default billing address for the given customerId.
     *
     * @api
     * @param int $customerId
     * @return \Magento\Customer\Api\Data\AddressInterface
     * @throws \Magento\Framework\Exception\NoSuchEntityException If the customer Id is invalid
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getDefaultBillingAddress($customerId);

    /**
     * Retrieve default shipping address for the given customerId.
     *
     * @api
     * @param int $customerId
     * @return \Magento\Customer\Api\Data\AddressInterface
     * @throws \Magento\Framework\Exception\NoSuchEntityException If the customer Id is invalid
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getDefaultShippingAddress($customerId);

    /**
     * Return hashed password, which can be directly saved to database.
     *
     * @api
     * @param string $password
     * @return string
     */
    public function getPasswordHash($password);
}
