<?php
/**
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magento\Framework\View\TemplateEngine\Xhtml;

use Magento\Framework\Phrase;
use Magento\Framework\ObjectManagerInterface;
use Magento\Framework\Exception\LocalizedException;

/**
 * Class TemplateFactory
 */
class TemplateFactory
{
    /**
     * Object manager
     *
     * @var ObjectManagerInterface
     */
    protected $objectManager;

    /**
     * Instance name
     *
     * @var string
     */
    protected $instanceName;

    /**
     * Constructor
     *
     * @param ObjectManagerInterface $objectManager
     * @param string $instanceName
     */
    public function __construct(
        ObjectManagerInterface $objectManager,
        $instanceName = 'Magento\Framework\View\TemplateEngine\Xhtml\Template'
    ) {
        $this->objectManager = $objectManager;
        $this->instanceName = $instanceName;
    }

    /**
     * Create result
     *
     * @param array $arguments
     * @return Template
     * @throws LocalizedException
     */
    public function create(array $arguments = [])
    {
        $object = $this->objectManager->create($this->instanceName, $arguments);

        if (!($object instanceof Template)) {
            throw new LocalizedException(new Phrase('This class must inherit from a class "Template"'));
        }

        return $object;
    }
}
