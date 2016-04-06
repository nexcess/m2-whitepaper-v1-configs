<?php
/**
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magento\Framework\Reflection;

use Magento\Framework\Exception\SerializationException;
use Magento\Framework\Phrase;
use Zend\Code\Reflection\ClassReflection;
use Zend\Code\Reflection\ParameterReflection;

/**
 * Type processor of config reader properties
 *
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class TypeProcessor
{
    /**#@+
     * Pre-normalized type constants
     */
    const STRING_TYPE = 'str';
    const INT_TYPE = 'integer';
    const BOOLEAN_TYPE = 'bool';
    const ANY_TYPE = 'mixed';
    /**#@-*/

    /**#@+
     * Normalized type constants
     */
    const NORMALIZED_STRING_TYPE = 'string';
    const NORMALIZED_INT_TYPE = 'int';
    const NORMALIZED_FLOAT_TYPE = 'float';
    const NORMALIZED_DOUBLE_TYPE = 'double';
    const NORMALIZED_BOOLEAN_TYPE = 'boolean';
    const NORMALIZED_ANY_TYPE = 'anyType';
    /**#@-*/

    /**
     * Array of types data.
     * <pre>array(
     *     $complexTypeName => array(
     *         'documentation' => $typeDocumentation
     *         'parameters' => array(
     *             $firstParameter => array(
     *                 'type' => $type,
     *                 'required' => $isRequired,
     *                 'default' => $defaultValue,
     *                 'documentation' => $parameterDocumentation
     *             ),
     *             ...
     *         )
     *     ),
     *     ...
     * )</pre>
     *
     * @var array
     */
    protected $_types = [];

    /**
     * Retrieve processed types data.
     *
     * @return array
     */
    public function getTypesData()
    {
        return $this->_types;
    }

    /**
     * Set processed types data.
     *
     * Should be used carefully since no data consistency checks are performed.
     *
     * @param array $typesData
     * @return $this
     */
    public function setTypesData($typesData)
    {
        $this->_types = $typesData;
        return $this;
    }

    /**
     * Retrieve data type details for the given type name.
     *
     * @param string $typeName
     * @return array
     * @throws \InvalidArgumentException
     */
    public function getTypeData($typeName)
    {
        if (!isset($this->_types[$typeName])) {
            throw new \InvalidArgumentException(sprintf('Data type "%s" is not declared.', $typeName));
        }
        return $this->_types[$typeName];
    }

    /**
     * Add or update type data in config.
     *
     * @param string $typeName
     * @param array $data
     * @return void
     */
    public function setTypeData($typeName, $data)
    {
        if (!isset($this->_types[$typeName])) {
            $this->_types[$typeName] = $data;
        } else {
            $this->_types[$typeName] = array_merge_recursive($this->_types[$typeName], $data);
        }
    }

    /**
     * Process type name. In case parameter type is a complex type (class) - process its properties.
     *
     * @param string $type
     * @return string Complex type name
     * @throws \LogicException
     */
    public function register($type)
    {
        $typeName = $this->normalizeType($type);
        if (null === $typeName) {
            return null;
        }
        if (!$this->isTypeSimple($typeName) && !$this->isTypeAny($typeName)) {
            $typeSimple = $this->getArrayItemType($type);
            if (!(class_exists($typeSimple) || interface_exists($typeSimple))) {
                throw new \LogicException(
                    sprintf('Class "%s" does not exist. Please note that namespace must be specified.', $type)
                );
            }
            $complexTypeName = $this->translateTypeName($type);
            if (!isset($this->_types[$complexTypeName])) {
                $this->_processComplexType($type);
            }
            $typeName = $complexTypeName;
        }

        return $typeName;
    }

    /**
     * Retrieve complex type information from class public properties.
     *
     * @param string $class
     * @return array
     * @throws \InvalidArgumentException
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     */
    protected function _processComplexType($class)
    {
        $typeName = $this->translateTypeName($class);
        $this->_types[$typeName] = [];
        if ($this->isArrayType($class)) {
            $this->register($this->getArrayItemType($class));
        } else {
            if (!(class_exists($class) || interface_exists($class))) {
                throw new \InvalidArgumentException(
                    sprintf('Could not load the "%s" class as parameter type.', $class)
                );
            }
            $reflection = new ClassReflection($class);
            $docBlock = $reflection->getDocBlock();
            $this->_types[$typeName]['documentation'] = $docBlock ? $this->getDescription($docBlock) : '';
            /** @var \Zend\Code\Reflection\MethodReflection $methodReflection */
            foreach ($reflection->getMethods(\ReflectionMethod::IS_PUBLIC) as $methodReflection) {
                if ($methodReflection->class === "Magento\Framework\Model\AbstractModel") {
                    continue;
                }
                $this->_processMethod($methodReflection, $typeName);
            }
        }

        return $this->_types[$typeName];
    }

    /**
     * Collect metadata for virtual field corresponding to current method if it is a getter (used in WSDL generation).
     *
     * @param \Zend\Code\Reflection\MethodReflection $methodReflection
     * @param string $typeName
     * @return void
     */
    protected function _processMethod(\Zend\Code\Reflection\MethodReflection $methodReflection, $typeName)
    {
        $isGetter = (strpos($methodReflection->getName(), 'get') === 0)
            || (strpos($methodReflection->getName(), 'is') === 0)
            || (strpos($methodReflection->getName(), 'has') === 0);
        /** Field will not be added to WSDL if getter has params */
        if ($isGetter && !$methodReflection->getNumberOfRequiredParameters()) {
            $returnMetadata = $this->getGetterReturnType($methodReflection);
            $fieldName = $this->dataObjectGetterNameToFieldName($methodReflection->getName());
            if ($returnMetadata['description']) {
                $description = $returnMetadata['description'];
            } else {
                $description = $this->dataObjectGetterDescriptionToFieldDescription(
                    $methodReflection->getDocBlock()->getShortDescription()
                );
            }
            $this->_types[$typeName]['parameters'][$fieldName] = [
                'type' => $this->register($returnMetadata['type']),
                'required' => $returnMetadata['isRequired'],
                'documentation' => $description,
            ];
        }
    }

    /**
     * Get short and long description from docblock and concatenate.
     *
     * @param \Zend\Code\Reflection\DocBlockReflection $doc
     * @return string
     */
    public function getDescription(\Zend\Code\Reflection\DocBlockReflection $doc)
    {
        $shortDescription = $doc->getShortDescription();
        $longDescription = $doc->getLongDescription();

        $description = rtrim($shortDescription);
        $longDescription = str_replace(["\n", "\r"], '', $longDescription);
        if (!empty($longDescription) && !empty($description)) {
            $description .= " ";
        }
        $description .= ltrim($longDescription);

        return $description;
    }

    /**
     * Convert Data Object getter name into field name.
     *
     * @param string $getterName
     * @return string
     */
    public function dataObjectGetterNameToFieldName($getterName)
    {
        if ((strpos($getterName, 'get') === 0)) {
            /** Remove 'get' prefix and make the first letter lower case */
            $fieldName = substr($getterName, strlen('get'));
        } elseif ((strpos($getterName, 'is') === 0)) {
            /** Remove 'is' prefix and make the first letter lower case */
            $fieldName = substr($getterName, strlen('is'));
        } elseif ((strpos($getterName, 'has') === 0)) {
            /** Remove 'has' prefix and make the first letter lower case */
            $fieldName = substr($getterName, strlen('has'));
        } else {
            $fieldName = $getterName;
        }
        return lcfirst($fieldName);
    }

    /**
     * Convert Data Object getter short description into field description.
     *
     * @param string $shortDescription
     * @return string
     */
    protected function dataObjectGetterDescriptionToFieldDescription($shortDescription)
    {
        return ucfirst(substr(strstr($shortDescription, " "), 1));
    }

    /**
     * Identify getter return type by its reflection.
     *
     * @param \Zend\Code\Reflection\MethodReflection $methodReflection
     * @return array <pre>array(
     *     'type' => <string>$type,
     *     'isRequired' => $isRequired,
     *     'description' => $description
     * )</pre>
     * @throws \InvalidArgumentException
     */
    public function getGetterReturnType($methodReflection)
    {
        $methodDocBlock = $methodReflection->getDocBlock();
        if (!$methodDocBlock) {
            throw new \InvalidArgumentException(
                "Each getter must have description with @return annotation. "
                . "See {$methodReflection->getDeclaringClass()->getName()}::{$methodReflection->getName()}()"
            );
        }
        $returnAnnotations = $methodDocBlock->getTags('return');
        if (empty($returnAnnotations)) {
            throw new \InvalidArgumentException(
                "Getter return type must be specified using @return annotation. "
                . "See {$methodReflection->getDeclaringClass()->getName()}::{$methodReflection->getName()}()"
            );
        }
        /** @var \Zend\Code\Reflection\DocBlock\Tag\ReturnTag $returnAnnotation */
        $returnAnnotation = current($returnAnnotations);
        $returnType = $returnAnnotation->getType();
        /*
         * Adding this code as a workaround since \Zend\Code\Reflection\DocBlock\Tag\ReturnTag::initialize does not
         * detect and return correct type for array of objects in annotation.
         * eg @return \Magento\Webapi\Service\Entity\SimpleData[] is returned with type
         * \Magento\Webapi\Service\Entity\SimpleData instead of \Magento\Webapi\Service\Entity\SimpleData[]
         */
        $escapedReturnType = str_replace('[]', '\[\]', $returnType);
        $escapedReturnType = str_replace('\\', '\\\\', $escapedReturnType);

        if (preg_match("/.*\\@return\\s+({$escapedReturnType}).*/i", $methodDocBlock->getContents(), $matches)) {
            $returnType = $matches[1];
        }
        $isRequired = preg_match("/.*\@return\s+\S+\|null.*/i", $methodDocBlock->getContents(), $matches)
            ? false
            : true;
        return [
            'type' => $returnType,
            'isRequired' => $isRequired,
            'description' => $returnAnnotation->getDescription(),
            'parameterCount' => $methodReflection->getNumberOfRequiredParameters()
        ];
    }

    /**
     * Get possible method exceptions
     *
     * @param \Zend\Code\Reflection\MethodReflection $methodReflection
     * @return array
     */
    public function getExceptions($methodReflection)
    {
        $exceptions = [];
        $methodDocBlock = $methodReflection->getDocBlock();
        if ($methodDocBlock->hasTag('throws')) {
            $throwsTypes = $methodDocBlock->getTags('throws');
            if (is_array($throwsTypes)) {
                /** @var $throwsType \Zend\Code\Reflection\DocBlock\Tag\ThrowsTag */
                foreach ($throwsTypes as $throwsType) {
                    $exceptions = array_merge($exceptions, $throwsType->getTypes());
                }
            }
        }

        return $exceptions;
    }

    /**
     * Normalize short type names to full type names.
     *
     * @param string $type
     * @return string
     */
    public function normalizeType($type)
    {
        if ($type == 'null') {
            return null;
        }
        $normalizationMap = [
            self::STRING_TYPE => self::NORMALIZED_STRING_TYPE,
            self::INT_TYPE => self::NORMALIZED_INT_TYPE,
            self::BOOLEAN_TYPE => self::NORMALIZED_BOOLEAN_TYPE,
            self::ANY_TYPE => self::NORMALIZED_ANY_TYPE,
        ];

        return is_string($type) && isset($normalizationMap[$type]) ? $normalizationMap[$type] : $type;
    }

    /**
     * Check if given type is a simple type.
     *
     * @param string $type
     * @return bool
     */
    public function isTypeSimple($type)
    {
        $type = $this->normalizeType($type);
        if ($this->isArrayType($type)) {
            $type = $this->getArrayItemType($type);
        }

        return in_array(
            $type,
            [
                self::NORMALIZED_STRING_TYPE,
                self::NORMALIZED_INT_TYPE,
                self::NORMALIZED_FLOAT_TYPE,
                self::NORMALIZED_DOUBLE_TYPE,
                self::NORMALIZED_BOOLEAN_TYPE
            ]
        );
    }

    /**
     * Check if given type is any type.
     *
     * @param string $type
     * @return bool
     */
    public function isTypeAny($type)
    {
        $type = $this->normalizeType($type);
        if ($this->isArrayType($type)) {
            $type = $this->getArrayItemType($type);
        }

        return ($type == self::NORMALIZED_ANY_TYPE);
    }

    /**
     * Check if given type is an array of type items.
     * Example:
     * <pre>
     *  ComplexType[] -> array of ComplexType items
     *  string[] -> array of strings
     * </pre>
     *
     * @param string $type
     * @return bool
     */
    public function isArrayType($type)
    {
        return (bool)preg_match('/(\[\]$|^ArrayOf)/', $type);
    }

    /**
     * Get item type of the array.
     * Example:
     * <pre>
     *  ComplexType[] => ComplexType
     *  string[] => string
     *  int[] => integer
     * </pre>
     *
     * @param string $arrayType
     * @return string
     */
    public function getArrayItemType($arrayType)
    {
        return $this->normalizeType(str_replace('[]', '', $arrayType));
    }

    /**
     * Translate complex type class name into type name.
     *
     * Example:
     * <pre>
     *  \Magento\Customer\Api\Data\CustomerInterface => CustomerV1DataCustomer
     * </pre>
     *
     * @param string $class
     * @return string
     * @throws \InvalidArgumentException
     */
    public function translateTypeName($class)
    {
        if (preg_match('/\\\\?(.*)\\\\(.*)\\\\(Service|Api)\\\\\2?(.*)/', $class, $matches)) {
            $moduleNamespace = $matches[1] == 'Magento' ? '' : $matches[1];
            $moduleName = $matches[2];
            $typeNameParts = explode('\\', $matches[4]);

            return ucfirst($moduleNamespace . $moduleName . implode('', $typeNameParts));
        }
        throw new \InvalidArgumentException(sprintf('Invalid parameter type "%s".', $class));
    }

    /**
     * Translate array complex type name.
     *
     * Example:
     * <pre>
     *  ComplexTypeName[] => ArrayOfComplexTypeName
     *  string[] => ArrayOfString
     * </pre>
     *
     * @param string $type
     * @return string
     */
    public function translateArrayTypeName($type)
    {
        return 'ArrayOf' . ucfirst($this->getArrayItemType($type));
    }

    /**
     * Convert the value to the requested simple or any type
     *
     * @param int|string|float|int[]|string[]|float[] $value
     * @param string $type Convert given value to the this simple type
     * @return int|string|float|int[]|string[]|float[] Return the value which is converted to type
     * @throws SerializationException
     *
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     */
    public function processSimpleAndAnyType($value, $type)
    {
        $isArrayType = $this->isArrayType($type);
        if ($isArrayType && is_array($value)) {
            $arrayItemType = $this->getArrayItemType($type);
            foreach (array_keys($value) as $key) {
                if ($value !== null && !settype($value[$key], $arrayItemType)) {
                    throw new SerializationException(
                        new Phrase(
                            SerializationException::TYPE_MISMATCH,
                            ['value' => $value, 'type' => $type]
                        )
                    );
                }
            }
        } elseif ($isArrayType && $value === null) {
            return null;
        } elseif (!$isArrayType && !is_array($value)) {
            if ($value !== null && $type !== self::ANY_TYPE && !$this->setType($value, $type)) {
                throw new SerializationException(
                    new Phrase(
                        SerializationException::TYPE_MISMATCH,
                        ['value' => (string)$value, 'type' => $type]
                    )
                );
            }
        } else {
            throw new SerializationException(
                new Phrase(
                    SerializationException::TYPE_MISMATCH,
                    ['value' => gettype($value), 'type' => $type]
                )
            );
        }
        return $value;
    }

    /**
     * Get the parameter type
     *
     * @param ParameterReflection $param
     * @return string
     * @throws \LogicException
     */
    public function getParamType(ParameterReflection $param)
    {
        $type = $param->getType();
        if ($param->getType() == 'null') {
            throw new \LogicException(sprintf(
                '@param annotation is incorrect for the parameter "%s" in the method "%s:%s".'
                . ' First declared type should not be null. E.g. string|null',
                $param->getName(),
                $param->getDeclaringClass()->getName(),
                $param->getDeclaringFunction()->name
            ));
        }
        if ($type == 'array') {
            // try to determine class, if it's array of objects
            $docBlock = $param->getDeclaringFunction()->getDocBlock();
            $pattern = "/\@param\s+([\w\\\_]+\[\])\s+\\\${$param->getName()}\n/";
            $matches = [];
            if (preg_match($pattern, $docBlock->getContents(), $matches)) {
                return $matches[1];
            }
            return "{$type}[]";
        }
        return $type;
    }

    /**
     * Get parameter description
     *
     * @param ParameterReflection $param
     * @return string|null
     */
    public function getParamDescription(ParameterReflection $param)
    {
        $docBlock = $param->getDeclaringFunction()->getDocBlock();
        $docBlockLines = explode("\n", $docBlock->getContents());
        $pattern = "/\@param\s+([\w\\\_\[\]\|]+)\s+(\\\${$param->getName()})\s(.*)/";
        $matches = [];

        foreach ($docBlockLines as $line) {
            if (preg_match($pattern, $line, $matches)) {
                return $matches[3];
            }
        }
    }

    /**
     * Find the getter method name for a property from the given class
     *
     * @param ClassReflection $class
     * @param string $camelCaseProperty
     * @return string processed method name
     * @throws \Exception If $camelCaseProperty has no corresponding getter method
     */
    public function findGetterMethodName(ClassReflection $class, $camelCaseProperty)
    {
        $getterName = 'get' . $camelCaseProperty;
        $boolGetterName = 'is' . $camelCaseProperty;
        return $this->findAccessorMethodName($class, $camelCaseProperty, $getterName, $boolGetterName);
    }

    /**
     * Set value to a particular type
     *
     * @param mixed $value
     * @param string $type
     * @return true on successful type cast
     */
    protected function setType(&$value, $type)
    {
        // settype doesn't work for boolean string values.
        // ex: custom_attributes passed from SOAP client can have boolean values as string
        if ($type == 'bool' || $type == 'boolean') {
            $value = filter_var($value, FILTER_VALIDATE_BOOLEAN);
            return true;
        }
        return settype($value, $type);
    }

    /**
     * Find the setter method name for a property from the given class
     *
     * @param ClassReflection $class
     * @param string $camelCaseProperty
     * @return string processed method name
     * @throws \Exception If $camelCaseProperty has no corresponding setter method
     */
    public function findSetterMethodName(ClassReflection $class, $camelCaseProperty)
    {
        $setterName = 'set' . $camelCaseProperty;
        $boolSetterName = 'setIs' . $camelCaseProperty;
        return $this->findAccessorMethodName($class, $camelCaseProperty, $setterName, $boolSetterName);
    }

    /**
     * Find the accessor method name for a property from the given class
     *
     * @param ClassReflection $class
     * @param string $camelCaseProperty
     * @param string $accessorName
     * @param bool $boolAccessorName
     * @return string processed method name
     * @throws \Exception If $camelCaseProperty has no corresponding setter method
     */
    protected function findAccessorMethodName(
        ClassReflection $class,
        $camelCaseProperty,
        $accessorName,
        $boolAccessorName
    ) {
        if ($this->classHasMethod($class, $accessorName)) {
            $methodName = $accessorName;
            return $methodName;
        } elseif ($this->classHasMethod($class, $boolAccessorName)) {
            $methodName = $boolAccessorName;
            return $methodName;
        } else {
            throw new \LogicException(
                sprintf(
                    'Property "%s" does not have corresponding setter in class "%s".',
                    $camelCaseProperty,
                    $class->getName()
                )
            );
        }
    }

    /**
     * Checks if method is defined
     *
     * Case sensitivity of the method is taken into account.
     *
     * @param ClassReflection $class
     * @param string $methodName
     * @return bool
     */
    protected function classHasMethod(ClassReflection $class, $methodName)
    {
        return $class->hasMethod($methodName) && ($class->getMethod($methodName)->getName() == $methodName);
    }

    /**
     * Process call info data from interface.
     *
     * @param array $interface
     * @param string $serviceName API service name
     * @param string $methodName
     * @return $this
     */
    public function processInterfaceCallInfo($interface, $serviceName, $methodName)
    {
        foreach ($interface as $direction => $interfaceData) {
            $direction = ($direction == 'in') ? 'requiredInput' : 'returned';
            foreach ($interfaceData['parameters'] as $parameterData) {
                if (!$this->isTypeSimple($parameterData['type']) && !$this->isTypeAny($parameterData['type'])) {
                    $operation = $this->getOperationName($serviceName, $methodName);
                    if ($parameterData['required']) {
                        $condition = ($direction == 'requiredInput') ? 'yes' : 'always';
                    } else {
                        $condition = ($direction == 'requiredInput') ? 'no' : 'conditionally';
                    }
                    $callInfo = [];
                    $callInfo[$direction][$condition]['calls'][] = $operation;
                    $this->setTypeData($parameterData['type'], ['callInfo' => $callInfo]);
                }
            }
        }
        return $this;
    }

    /**
     * Get name of operation based on service and method names.
     *
     * @param string $serviceName API service name
     * @param string $methodName
     * @return string
     */
    public function getOperationName($serviceName, $methodName)
    {
        return $serviceName . ucfirst($methodName);
    }
}
