<?php
/**
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magento\Customer\Controller\Adminhtml\Index;

use Magento\Customer\Api\Data\CustomerInterface;
use Magento\Customer\Controller\RegistryConstants;
use Magento\Framework\Exception\LocalizedException;

/**
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class Save extends \Magento\Customer\Controller\Adminhtml\Index
{
    /**
     * @var \Magento\Customer\Model\Metadata\FormFactory
     */
    protected $_formFactory;

    /**
     * Reformat customer account data to be compatible with customer service interface
     *
     * @return array
     */
    protected function _extractCustomerData()
    {
        $customerData = [];
        if ($this->getRequest()->getPost('customer')) {
            $serviceAttributes = [
                CustomerInterface::DEFAULT_BILLING,
                CustomerInterface::DEFAULT_SHIPPING,
                'confirmation',
                'sendemail_store_id',
            ];

            $customerData = $this->_extractData(
                $this->getRequest(),
                'adminhtml_customer',
                \Magento\Customer\Api\CustomerMetadataInterface::ENTITY_TYPE_CUSTOMER,
                $serviceAttributes,
                'customer'
            );
        }

        if (isset($customerData['disable_auto_group_change'])) {
            $customerData['disable_auto_group_change'] = (int) filter_var(
                $customerData['disable_auto_group_change'],
                FILTER_VALIDATE_BOOLEAN
            );
        }

        return $customerData;
    }

    /**
     * Perform customer data filtration based on form code and form object
     *
     * @param \Magento\Framework\App\RequestInterface $request
     * @param string $formCode The code of EAV form to take the list of attributes from
     * @param string $entityType entity type for the form
     * @param string[] $additionalAttributes The list of attribute codes to skip filtration for
     * @param string $scope scope of the request
     * @param \Magento\Customer\Model\Metadata\Form $metadataForm to use for extraction
     * @return array Filtered customer data
     */
    protected function _extractData(
        \Magento\Framework\App\RequestInterface $request,
        $formCode,
        $entityType,
        $additionalAttributes = [],
        $scope = null,
        \Magento\Customer\Model\Metadata\Form $metadataForm = null
    ) {
        if ($metadataForm === null) {
            $metadataForm = $this->_formFactory->create(
                $entityType,
                $formCode,
                [],
                false,
                \Magento\Customer\Model\Metadata\Form::DONT_IGNORE_INVISIBLE
            );
        }
        $filteredData = $metadataForm->extractData($request, $scope);

        $object = $this->_objectFactory->create(['data' => $request->getPostValue()]);
        $requestData = $object->getData($scope);
        foreach ($additionalAttributes as $attributeCode) {
            $filteredData[$attributeCode] = isset($requestData[$attributeCode]) ? $requestData[$attributeCode] : false;
        }

        $formAttributes = $metadataForm->getAttributes();
        /** @var \Magento\Customer\Api\Data\AttributeMetadataInterface $attribute */
        foreach ($formAttributes as $attribute) {
            $attributeCode = $attribute->getAttributeCode();
            $frontendInput = $attribute->getFrontendInput();
            if ($frontendInput != 'boolean' && $filteredData[$attributeCode] === false) {
                unset($filteredData[$attributeCode]);
            }
        }

        return $filteredData;
    }

    /**
     * Saves default_billing and default_shipping flags for customer address
     *
     * @param array $addressIdList
     * @param array $extractedCustomerData
     * @return array
     */
    protected function saveDefaultFlags(array $addressIdList, array & $extractedCustomerData)
    {
        $result = [];
        $extractedCustomerData[CustomerInterface::DEFAULT_BILLING] = null;
        $extractedCustomerData[CustomerInterface::DEFAULT_SHIPPING] = null;
        foreach ($addressIdList as $addressId) {
            $scope = sprintf('address/%s', $addressId);
            $addressData = $this->_extractData(
                $this->getRequest(),
                'adminhtml_customer_address',
                \Magento\Customer\Api\AddressMetadataInterface::ENTITY_TYPE_ADDRESS,
                ['default_billing', 'default_shipping'],
                $scope
            );

            if (is_numeric($addressId)) {
                $addressData['id'] = $addressId;
            }
            // Set default billing and shipping flags to customer
            if (!empty($addressData['default_billing']) && $addressData['default_billing'] === 'true') {
                $extractedCustomerData[CustomerInterface::DEFAULT_BILLING] = $addressId;
                $addressData['default_billing'] = true;
            } else {
                $addressData['default_billing'] = false;
            }
            if (!empty($addressData['default_shipping']) && $addressData['default_shipping'] === 'true') {
                $extractedCustomerData[CustomerInterface::DEFAULT_SHIPPING] = $addressId;
                $addressData['default_shipping'] = true;
            } else {
                $addressData['default_shipping'] = false;
            }
            $result[] = $addressData;
        }
        return $result;
    }

    /**
     * Reformat customer addresses data to be compatible with customer service interface
     *
     * @param array $extractedCustomerData
     * @return array
     */
    protected function _extractCustomerAddressData(array & $extractedCustomerData)
    {
        $addresses = $this->getRequest()->getPost('address');
        $result = [];
        if (is_array($addresses)) {
            if (isset($addresses['_template_'])) {
                unset($addresses['_template_']);
            }

            $addressIdList = array_keys($addresses);
            $result = $this->saveDefaultFlags($addressIdList, $extractedCustomerData);
        }

        return $result;
    }

    /**
     * Save customer action
     *
     * @return \Magento\Backend\Model\View\Result\Redirect
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
     * @SuppressWarnings(PHPMD.NPathComplexity)
     */
    public function execute()
    {
        $returnToEdit = false;
        $originalRequestData = $this->getRequest()->getPostValue();
        $customerId = isset($originalRequestData['customer']['entity_id'])
            ? $originalRequestData['customer']['entity_id']
            : null;
        if ($originalRequestData) {
            try {
                // optional fields might be set in request for future processing by observers in other modules
                $customerData = $this->_extractCustomerData();
                $addressesData = $this->_extractCustomerAddressData($customerData);
                $request = $this->getRequest();
                $isExistingCustomer = (bool)$customerId;
                $customer = $this->customerDataFactory->create();
                if ($isExistingCustomer) {
                    $savedCustomerData = $this->_customerRepository->getById($customerId);
                    $customerData = array_merge(
                        $this->customerMapper->toFlatArray($savedCustomerData),
                        $customerData
                    );
                    $customerData['id'] = $customerId;
                }

                $this->dataObjectHelper->populateWithArray(
                    $customer,
                    $customerData,
                    '\Magento\Customer\Api\Data\CustomerInterface'
                );
                $addresses = [];
                foreach ($addressesData as $addressData) {
                    $region = isset($addressData['region']) ? $addressData['region'] : null;
                    $regionId = isset($addressData['region_id']) ? $addressData['region_id'] : null;
                    $addressData['region'] = [
                        'region' => $region,
                        'region_id' => $regionId,
                    ];
                    $addressDataObject = $this->addressDataFactory->create();
                    $this->dataObjectHelper->populateWithArray(
                        $addressDataObject,
                        $addressData,
                        '\Magento\Customer\Api\Data\AddressInterface'
                    );
                    $addresses[] = $addressDataObject;
                }

                $this->_eventManager->dispatch(
                    'adminhtml_customer_prepare_save',
                    ['customer' => $customer, 'request' => $request]
                );
                $customer->setAddresses($addresses);
                $customer->setStoreId($customerData['sendemail_store_id']);

                // Save customer
                if ($isExistingCustomer) {
                    $this->_customerRepository->save($customer);
                } else {
                    $customer = $this->customerAccountManagement->createAccount($customer);
                    $customerId = $customer->getId();
                }

                $isSubscribed = null;
                if ($this->_authorization->isAllowed(null)) {
                    $isSubscribed = $this->getRequest()->getPost('subscription');
                }
                if ($isSubscribed !== null) {
                    if ($isSubscribed !== 'false') {
                        $this->_subscriberFactory->create()->subscribeCustomerById($customerId);
                    } else {
                        $this->_subscriberFactory->create()->unsubscribeCustomerById($customerId);
                    }
                }

                // After save
                $this->_eventManager->dispatch(
                    'adminhtml_customer_save_after',
                    ['customer' => $customer, 'request' => $request]
                );
                $this->_getSession()->unsCustomerData();
                // Done Saving customer, finish save action
                $this->_coreRegistry->register(RegistryConstants::CURRENT_CUSTOMER_ID, $customerId);
                $this->messageManager->addSuccess(__('You saved the customer.'));
                $returnToEdit = (bool)$this->getRequest()->getParam('back', false);
            } catch (\Magento\Framework\Validator\Exception $exception) {
                $messages = $exception->getMessages();
                if (empty($messages)) {
                    $messages = $exception->getMessage();
                }
                $this->_addSessionErrorMessages($messages);
                $this->_getSession()->setCustomerData($originalRequestData);
                $returnToEdit = true;
            } catch (LocalizedException $exception) {
                $this->_addSessionErrorMessages($exception->getMessage());
                $this->_getSession()->setCustomerData($originalRequestData);
                $returnToEdit = true;
            } catch (\Exception $exception) {
                $this->messageManager->addException($exception, __('Something went wrong while saving the customer.'));
                $this->_getSession()->setCustomerData($originalRequestData);
                $returnToEdit = true;
            }
        }
        $resultRedirect = $this->resultRedirectFactory->create();
        if ($returnToEdit) {
            if ($customerId) {
                $resultRedirect->setPath(
                    'customer/*/edit',
                    ['id' => $customerId, '_current' => true]
                );
            } else {
                $resultRedirect->setPath(
                    'customer/*/new',
                    ['_current' => true]
                );
            }
        } else {
            $resultRedirect->setPath('customer/index');
        }
        return $resultRedirect;
    }
}
