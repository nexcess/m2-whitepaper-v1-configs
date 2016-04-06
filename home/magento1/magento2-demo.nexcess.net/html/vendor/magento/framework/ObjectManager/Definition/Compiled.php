<?php
/**
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magento\Framework\ObjectManager\Definition;

/**
 * Compiled class definitions. Should be used for maximum performance in production.
 */
abstract class Compiled implements \Magento\Framework\ObjectManager\DefinitionInterface
{
    /**
     * Class definitions
     *
     * @var array
     */
    protected $_definitions;

    /**
     * @var \Magento\Framework\Code\Reader\ClassReader
     */
    protected $reader ;

    /**
     * @param array $definitions
     * @param \Magento\Framework\Code\Reader\ClassReader $reader
     */
    public function __construct(array $definitions, \Magento\Framework\Code\Reader\ClassReader $reader = null)
    {
        list($this->_signatures, $this->_definitions) = $definitions;
        $this->reader = $reader ?: new \Magento\Framework\Code\Reader\ClassReader();
    }

    /**
     * Unpack signature
     *
     * @param string $signature
     * @return mixed
     */
    abstract protected function _unpack($signature);

    /**
     * Get list of method parameters
     *
     * Retrieve an ordered list of constructor parameters.
     * Each value is an array with following entries:
     *
     * array(
     *     0, // string: Parameter name
     *     1, // string|null: Parameter type
     *     2, // bool: whether this param is required
     *     3, // mixed: default value
     * );
     *
     * @param string $className
     * @return array|null
     */
    public function getParameters($className)
    {
        // if the definition isn't found in the list gathered from the compiled file then  using reflection to find it
        if (!array_key_exists($className, $this->_definitions)) {
            return $this->reader->getConstructor($className);
        }

        $definition = $this->_definitions[$className];
        if ($definition !== null) {
            if (is_string($this->_signatures[$definition])) {
                $this->_signatures[$definition] = $this->_unpack($this->_signatures[$definition]);
            }
            return $this->_signatures[$definition];
        }
        return null;
    }

    /**
     * Retrieve list of all classes covered with definitions
     *
     * @return array
     */
    public function getClasses()
    {
        return array_keys($this->_definitions);
    }
}
