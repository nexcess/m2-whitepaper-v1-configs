<?php
/**
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\Customer\Test\TestCase;

use Magento\Customer\Test\Fixture\Customer;
use Magento\Customer\Test\Page\CustomerAccountCreate;
use Magento\Cms\Test\Page\CmsIndex;
use Magento\Customer\Test\TestStep\LogoutCustomerOnFrontendStep;
use Magento\Mtf\TestCase\Injectable;

/**
 * Test Flow:
 * 1. Go to frontend.
 * 2. Click Register link.
 * 3. Fill registry form.
 * 4. Click 'Create account' button.
 * 5. Perform assertions.
 *
 * @group Customer_Account_(CS)
 * @ZephyrId MAGETWO-23546
 */
class RegisterCustomerFrontendEntityTest extends Injectable
{
    /* tags */
    const MVP = 'yes';
    const DOMAIN = 'CS';
    const TEST_TYPE = 'acceptance_test';
    /* end tags */

    /**
     * Customer registry page.
     *
     * @var CustomerAccountCreate
     */
    protected $customerAccountCreate;

    /**
     * Cms page.
     *
     * @var CmsIndex $cmsIndex
     */
    protected $cmsIndex;

    /**
     * Customer log out step.
     *
     * @var LogoutCustomerOnFrontendStep
     */
    protected $logoutCustomerOnFrontendStep;

    /**
     * Inject data.
     *
     * @param CustomerAccountCreate $customerAccountCreate
     * @param CmsIndex $cmsIndex
     * @param LogoutCustomerOnFrontendStep $logoutCustomerOnFrontendStep
     * @return void
     */
    public function __inject(
        CustomerAccountCreate $customerAccountCreate,
        CmsIndex $cmsIndex,
        LogoutCustomerOnFrontendStep $logoutCustomerOnFrontendStep
    ) {
        $this->customerAccountCreate = $customerAccountCreate;
        $this->cmsIndex = $cmsIndex;
        $this->logoutCustomerOnFrontendStep = $logoutCustomerOnFrontendStep;
    }

    /**
     * Create Customer account on Storefront.
     *
     * @param Customer $customer
     * @return void
     */
    public function test(Customer $customer)
    {
        // Steps
        $this->cmsIndex->open();
        $this->cmsIndex->getLinksBlock()->openLink('Create an Account');
        $this->customerAccountCreate->getRegisterForm()->registerCustomer($customer);
    }

    /**
     * Logout customer from frontend account.
     *
     * @return void
     */
    public function tearDown()
    {
        $this->logoutCustomerOnFrontendStep->run();
    }
}
