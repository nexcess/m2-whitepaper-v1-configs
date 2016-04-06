<?php
/**
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magento\Integration\Model\Oauth;

use Magento\Framework\Oauth\ConsumerInterface;

/**
 * Consumer model
 *
 * @author Magento Core Team <core@magentocommerce.com>
 * @method \Magento\Integration\Model\ResourceModel\Oauth\Consumer _getResource()
 * @method \Magento\Integration\Model\ResourceModel\Oauth\Consumer getResource()
 * @method \Magento\Integration\Model\ResourceModel\Oauth\Consumer\Collection getCollection()
 * @method \Magento\Integration\Model\ResourceModel\Oauth\Consumer\Collection getResourceCollection()
 * @method string getName()
 * @method Consumer setName() setName(string $name)
 * @method Consumer setKey() setKey(string $key)
 * @method Consumer setSecret() setSecret(string $secret)
 * @method Consumer setCallbackUrl() setCallbackUrl(string $url)
 * @method Consumer setCreatedAt() setCreatedAt(string $date)
 * @method string getUpdatedAt()
 * @method Consumer setUpdatedAt() setUpdatedAt(string $date)
 * @method string getRejectedCallbackUrl()
 * @method Consumer setRejectedCallbackUrl() setRejectedCallbackUrl(string $rejectedCallbackUrl)
 */
class Consumer extends \Magento\Framework\Model\AbstractModel implements ConsumerInterface
{
    /**
     * @var \Magento\Framework\Url\Validator
     */
    protected $urlValidator;

    /**
     * @var \Magento\Integration\Model\Oauth\Consumer\Validator\KeyLength
     */
    protected $keyLengthValidator;

    /**
     * @var  \Magento\Integration\Helper\Oauth\Data
     */
    protected $dataHelper;

    /**
     * @param \Magento\Framework\Model\Context $context
     * @param \Magento\Framework\Registry $registry
     * @param \Magento\Integration\Model\Oauth\Consumer\Validator\KeyLength $keyLength
     * @param \Magento\Framework\Url\Validator $urlValidator
     * @param \Magento\Integration\Helper\Oauth\Data $dataHelper
     * @param \Magento\Framework\Model\ResourceModel\AbstractResource $resource
     * @param \Magento\Framework\Data\Collection\AbstractDb $resourceCollection
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\Model\Context $context,
        \Magento\Framework\Registry $registry,
        \Magento\Integration\Model\Oauth\Consumer\Validator\KeyLength $keyLength,
        \Magento\Framework\Url\Validator $urlValidator,
        \Magento\Integration\Helper\Oauth\Data $dataHelper,
        \Magento\Framework\Model\ResourceModel\AbstractResource $resource = null,
        \Magento\Framework\Data\Collection\AbstractDb $resourceCollection = null,
        array $data = []
    ) {
        $this->keyLengthValidator = $keyLength;
        $this->urlValidator = $urlValidator;
        $this->dataHelper = $dataHelper;
        parent::__construct($context, $registry, $resource, $resourceCollection, $data);
    }

    /**
     * Initialize resource model
     *
     * @return void
     */
    protected function _construct()
    {
        parent::_construct();
        $this->_init('Magento\Integration\Model\ResourceModel\Oauth\Consumer');
    }

    /**
     * BeforeSave actions
     *
     * @return $this
     */
    public function beforeSave()
    {
        $this->validate();
        parent::beforeSave();
        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function validate()
    {
        if ($this->getCallbackUrl() || $this->getRejectedCallbackUrl()) {
            $this->setCallbackUrl(trim($this->getCallbackUrl()));
            $this->setRejectedCallbackUrl(trim($this->getRejectedCallbackUrl()));

            if ($this->getCallbackUrl() && !$this->urlValidator->isValid($this->getCallbackUrl())) {
                throw new \Magento\Framework\Exception\LocalizedException(__('Invalid Callback URL'));
            }
            if ($this->getRejectedCallbackUrl() && !$this->urlValidator->isValid($this->getRejectedCallbackUrl())) {
                throw new \Magento\Framework\Exception\LocalizedException(__('Invalid Rejected Callback URL'));
            }
        }

        $this->keyLengthValidator
            ->setLength(\Magento\Framework\Oauth\Helper\Oauth::LENGTH_CONSUMER_KEY)
            ->setName('Consumer Key');
        if (!$this->keyLengthValidator->isValid($this->getKey())) {
            $messages = $this->keyLengthValidator->getMessages();
            throw new \Magento\Framework\Exception\LocalizedException(__(array_shift($messages)));
        }

        $this->keyLengthValidator
            ->setLength(\Magento\Framework\Oauth\Helper\Oauth::LENGTH_CONSUMER_SECRET)
            ->setName('Consumer Secret');
        if (!$this->keyLengthValidator->isValid($this->getSecret())) {
            $messages = $this->keyLengthValidator->getMessages();
            throw new \Magento\Framework\Exception\LocalizedException(__(array_shift($messages)));
        }
        return true;
    }

    /**
     * Load consumer data by consumer key.
     *
     * @param string $key
     * @return $this
     */
    public function loadByKey($key)
    {
        return $this->load($key, 'key');
    }

    /**
     * {@inheritdoc}
     */
    public function getKey()
    {
        return $this->getData('key');
    }

    /**
     * {@inheritdoc}
     */
    public function getSecret()
    {
        return $this->getData('secret');
    }

    /**
     * {@inheritdoc}
     */
    public function getCallbackUrl()
    {
        return $this->getData('callback_url');
    }

    /**
     * {@inheritdoc}
     */
    public function getCreatedAt()
    {
        return $this->getData('created_at');
    }

    /**
     * {@inheritdoc}
     */
    public function isValidForTokenExchange()
    {
        $expiry = $this->dataHelper->getConsumerExpirationPeriod();
        return $expiry > $this->getResource()->getTimeInSecondsSinceCreation($this->getId());
    }
}
