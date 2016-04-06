<?php
/**
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magento\Webapi\Model;

use Magento\Webapi\Model\Config\Converter;
use Magento\Framework\App\Cache\Type\Webapi as WebApiCache;

/**
 * Service Metadata Model
 */
class ServiceMetadata
{
    /**#@+
     * Keys that a used for service config internal representation.
     */
    const KEY_CLASS = 'class';

    const KEY_IS_SECURE = 'isSecure';

    const KEY_SERVICE_METHODS = 'methods';

    const KEY_METHOD = 'method';

    const KEY_IS_REQUIRED = 'inputRequired';

    const KEY_ACL_RESOURCES = 'resources';

    const SERVICES_CONFIG_CACHE_ID = 'services-services-config';

    const ROUTES_CONFIG_CACHE_ID = 'routes-services-config';

    const REFLECTED_TYPES_CACHE_ID = 'soap-reflected-types';

    /**#@-*/

    /**
     * API services
     *
     * @var array
     */
    protected $services;

    /**
     * List of services with route data
     *
     * @var array
     */
    protected $routes;

    /**
     * @var WebApiCache
     */
    protected $cache;

    /** @var \Magento\Webapi\Model\Config */
    protected $config;

    /**
     * @var \Magento\Webapi\Model\Config\ClassReflector
     */
    protected $classReflector;

    /**
     * @var \Magento\Framework\Reflection\TypeProcessor
     */
    protected $typeProcessor;

    /**
     * Initialize dependencies.
     *
     * @param \Magento\Webapi\Model\Config $config
     * @param WebApiCache $cache
     * @param \Magento\Webapi\Model\Config\ClassReflector $classReflector
     * @param \Magento\Framework\Reflection\TypeProcessor $typeProcessor
     */
    public function __construct(
        \Magento\Webapi\Model\Config $config,
        WebApiCache $cache,
        \Magento\Webapi\Model\Config\ClassReflector $classReflector,
        \Magento\Framework\Reflection\TypeProcessor $typeProcessor
    ) {
        $this->config = $config;
        $this->cache = $cache;
        $this->classReflector = $classReflector;
        $this->typeProcessor = $typeProcessor;
    }

    /**
     * Collect the list of services metadata
     *
     * @return array
     */
    protected function initServicesMetadata()
    {
        $services = [];
        foreach ($this->config->getServices()[Converter::KEY_SERVICES] as $serviceClass => $serviceVersionData) {
            foreach ($serviceVersionData as $version => $serviceData) {
                $serviceName = $this->getServiceName($serviceClass, $version);
                foreach ($serviceData[Converter::KEY_METHODS] as $methodName => $methodMetadata) {
                    $services[$serviceName][self::KEY_SERVICE_METHODS][$methodName] = [
                        self::KEY_METHOD => $methodName,
                        self::KEY_IS_REQUIRED => (bool)$methodMetadata[Converter::KEY_SECURE],
                        self::KEY_IS_SECURE => $methodMetadata[Converter::KEY_SECURE],
                        self::KEY_ACL_RESOURCES => $methodMetadata[Converter::KEY_ACL_RESOURCES],
                    ];
                    $services[$serviceName][self::KEY_CLASS] = $serviceClass;
                }
                $reflectedMethodsMetadata = $this->classReflector->reflectClassMethods(
                    $serviceClass,
                    $services[$serviceName][self::KEY_SERVICE_METHODS]
                );
                $services[$serviceName][self::KEY_SERVICE_METHODS] = array_merge_recursive(
                    $services[$serviceName][self::KEY_SERVICE_METHODS],
                    $reflectedMethodsMetadata
                );
                $services[$serviceName][Converter::KEY_DESCRIPTION] = $this->classReflector->extractClassDescription(
                    $serviceClass
                );
            }
        }

        return $services;
    }

    /**
     * Return services loaded from cache if enabled or from files merged previously
     *
     * @return array
     */
    public function getServicesConfig()
    {
        if (null === $this->services) {
            $servicesConfig = $this->cache->load(self::SERVICES_CONFIG_CACHE_ID);
            $typesData = $this->cache->load(self::REFLECTED_TYPES_CACHE_ID);
            if ($servicesConfig && is_string($servicesConfig) && $typesData && is_string($typesData)) {
                $this->services = unserialize($servicesConfig);
                $this->typeProcessor->setTypesData(unserialize($typesData));
            } else {
                $this->services = $this->initServicesMetadata();
                $this->cache->save(serialize($this->services), self::SERVICES_CONFIG_CACHE_ID);
                $this->cache->save(serialize($this->typeProcessor->getTypesData()), self::REFLECTED_TYPES_CACHE_ID);
            }
        }
        return $this->services;
    }

    /**
     * Retrieve specific service interface data.
     *
     * @param string $serviceName
     * @return array
     * @throws \RuntimeException
     */
    public function getServiceMetadata($serviceName)
    {
        $servicesConfig = $this->getServicesConfig();
        if (!isset($servicesConfig[$serviceName]) || !is_array($servicesConfig[$serviceName])) {
            throw new \RuntimeException(__('Requested service is not available: "%1"', $serviceName));
        }
        return $servicesConfig[$serviceName];
    }

    /**
     * Translate service interface name into service name.
     *
     * Example:
     * <pre>
     * - 'Magento\Customer\Api\CustomerAccountInterface', 'V1', false => customerCustomerAccount
     * - 'Magento\Customer\Api\CustomerAddressInterface', 'V1', true  => customerCustomerAddressV1
     * </pre>
     *
     * @param string $interfaceName
     * @param string $version
     * @param bool $preserveVersion Should version be preserved during interface name conversion into service name
     * @return string
     * @throws \InvalidArgumentException
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     */
    public function getServiceName($interfaceName, $version, $preserveVersion = true)
    {
        if (!preg_match(\Magento\Webapi\Model\Config::SERVICE_CLASS_PATTERN, $interfaceName, $matches)) {
            $apiClassPattern = "#^(.+?)\\\\(.+?)\\\\Api\\\\(.+?)(Interface)?$#";
            preg_match($apiClassPattern, $interfaceName, $matches);
        }

        if (!empty($matches)) {
            $moduleNamespace = $matches[1];
            $moduleName = $matches[2];
            $moduleNamespace = ($moduleNamespace == 'Magento') ? '' : $moduleNamespace;
            if ($matches[4] === 'Interface') {
                $matches[4] = $matches[3];
            }
            $serviceNameParts = explode('\\', trim($matches[4], '\\'));
            if ($moduleName == $serviceNameParts[0]) {
                /** Avoid duplication of words in service name */
                $moduleName = '';
            }
            $parentServiceName = $moduleNamespace . $moduleName . array_shift($serviceNameParts);
            array_unshift($serviceNameParts, $parentServiceName);
            if ($preserveVersion) {
                $serviceNameParts[] = $version;
            }
        } elseif (preg_match(\Magento\Webapi\Model\Config::API_PATTERN, $interfaceName, $matches)) {
            $moduleNamespace = $matches[1];
            $moduleName = $matches[2];
            $moduleNamespace = ($moduleNamespace == 'Magento') ? '' : $moduleNamespace;
            $serviceNameParts = explode('\\', trim($matches[3], '\\'));
            if ($moduleName == $serviceNameParts[0]) {
                /** Avoid duplication of words in service name */
                $moduleName = '';
            }
            $parentServiceName = $moduleNamespace . $moduleName . array_shift($serviceNameParts);
            array_unshift($serviceNameParts, $parentServiceName);
            if ($preserveVersion) {
                $serviceNameParts[] = $version;
            }
        } else {
            throw new \InvalidArgumentException(sprintf('The service interface name "%s" is invalid.', $interfaceName));
        }
        return lcfirst(implode('', $serviceNameParts));
    }

    /**
     * Retrieve specific service interface data with route.
     *
     * @param string $serviceName
     * @return array
     * @throws \RuntimeException
     */
    public function getRouteMetadata($serviceName)
    {
        $routesConfig = $this->getRoutesConfig();
        if (!isset($routesConfig[$serviceName]) || !is_array($routesConfig[$serviceName])) {
            throw new \RuntimeException(__('Requested service is not available: "%1"', $serviceName));
        }
        return $routesConfig[$serviceName];
    }

    /**
     * Return routes loaded from cache if enabled or from files merged previously
     *
     * @return array
     */
    public function getRoutesConfig()
    {
        if (null === $this->routes) {
            $routesConfig = $this->cache->load(self::ROUTES_CONFIG_CACHE_ID);
            $typesData = $this->cache->load(self::REFLECTED_TYPES_CACHE_ID);
            if ($routesConfig && is_string($routesConfig) && $typesData && is_string($typesData)) {
                $this->routes = unserialize($routesConfig);
                $this->typeProcessor->setTypesData(unserialize($typesData));
            } else {
                $this->routes = $this->initRoutesMetadata();
                $this->cache->save(serialize($this->routes), self::ROUTES_CONFIG_CACHE_ID);
                $this->cache->save(serialize($this->typeProcessor->getTypesData()), self::REFLECTED_TYPES_CACHE_ID);
            }
        }
        return $this->routes;
    }

    /**
     * Collect the list of services with routes and request types for use in REST.
     *
     * @return array
     */
    protected function initRoutesMetadata()
    {
        $routes = $this->getServicesConfig();
        foreach ($this->config->getServices()[Converter::KEY_ROUTES] as $url => $routeData) {
            foreach ($routeData as $method => $data) {
                $serviceClass = $data[Converter::KEY_SERVICE][Converter::KEY_SERVICE_CLASS];
                $version = explode('/', ltrim($url, '/'))[0];
                $serviceName = $this->getServiceName($serviceClass, $version);
                $methodName = $data[Converter::KEY_SERVICE][Converter::KEY_METHOD];
                $routes[$serviceName][Converter::KEY_ROUTES][$url][$method][Converter::KEY_METHOD] = $methodName;
                $routes[$serviceName][Converter::KEY_ROUTES][$url][$method][Converter::KEY_DATA_PARAMETERS]
                    = $data[Converter::KEY_DATA_PARAMETERS];

            }
        }
        return $routes;
    }
}
