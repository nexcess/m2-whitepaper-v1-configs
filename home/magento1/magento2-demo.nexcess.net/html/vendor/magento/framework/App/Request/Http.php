<?php
/**
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magento\Framework\App\Request;

use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\App\RequestSafetyInterface;
use Magento\Framework\App\Route\ConfigInterface\Proxy as ConfigInterface;
use Magento\Framework\HTTP\PhpEnvironment\Request;
use Magento\Framework\ObjectManagerInterface;
use Magento\Framework\Stdlib\Cookie\CookieReaderInterface;
use Magento\Framework\Stdlib\StringUtils;

/**
 * Http request
 */
class Http extends Request implements RequestInterface, RequestSafetyInterface
{
    /**#@+
     * HTTP Ports
     */
    const DEFAULT_HTTP_PORT = 80;
    const DEFAULT_HTTPS_PORT = 443;
    /**#@-*/

    // Configuration path
    const XML_PATH_OFFLOADER_HEADER = 'web/secure/offloader_header';

    /**
     * @var string
     */
    protected $route;

    /**
     * PATH_INFO
     *
     * @var string
     */
    protected $pathInfo = '';

    /**
     * ORIGINAL_PATH_INFO
     *
     * @var string
     */
    protected $originalPathInfo = '';

    /**
     * @var array
     */
    protected $directFrontNames;

    /**
     * @var string
     */
    protected $controllerModule;

    /**
     * Request's original information before forward.
     *
     * @var array
     */
    protected $beforeForwardInfo = [];

    /**
     * @var ConfigInterface
     */
    protected $routeConfig;

    /**
     * @var PathInfoProcessorInterface
     */
    protected $pathInfoProcessor;

    /**
     * @var ObjectManagerInterface
     */
    protected $objectManager;

    /**
     * @var bool|null
     */
    protected $isSafeMethod = null;

    /**
     * @var array
     */
    protected $safeRequestTypes = ['GET', 'HEAD', 'TRACE', 'OPTIONS'];

    /**
     * @param CookieReaderInterface $cookieReader
     * @param StringUtils $converter
     * @param ConfigInterface $routeConfig
     * @param PathInfoProcessorInterface $pathInfoProcessor
     * @param ObjectManagerInterface  $objectManager
     * @param string|null $uri
     * @param array $directFrontNames
     */
    public function __construct(
        CookieReaderInterface $cookieReader,
        StringUtils $converter,
        ConfigInterface $routeConfig,
        PathInfoProcessorInterface $pathInfoProcessor,
        ObjectManagerInterface $objectManager,
        $uri = null,
        $directFrontNames = []
    ) {
        parent::__construct($cookieReader, $converter, $uri);
        $this->routeConfig = $routeConfig;
        $this->pathInfoProcessor = $pathInfoProcessor;
        $this->objectManager = $objectManager;
        $this->directFrontNames = $directFrontNames;
    }

    /**
     * Returns ORIGINAL_PATH_INFO.
     * This value is calculated instead of reading PATH_INFO
     * directly from $_SERVER due to cross-platform differences.
     *
     * @return string
     */
    public function getOriginalPathInfo()
    {
        if (empty($this->originalPathInfo)) {
            $this->setPathInfo();
        }
        return $this->originalPathInfo;
    }

    /**
     * Set the PATH_INFO string
     * Set the ORIGINAL_PATH_INFO string
     *
     * @param string|null $pathInfo
     * @return $this
     */
    public function setPathInfo($pathInfo = null)
    {
        if ($pathInfo === null) {
            $requestUri = $this->getRequestUri();
            if ('/' === $requestUri) {
                return $this;
            }

            // Remove the query string from REQUEST_URI
            $pos = strpos($requestUri, '?');
            if ($pos) {
                $requestUri = substr($requestUri, 0, $pos);
            }

            $baseUrl = $this->getBaseUrl();
            $pathInfo = substr($requestUri, strlen($baseUrl));
            if (!empty($baseUrl) && false === $pathInfo) {
                $pathInfo = '';
            } elseif (null === $baseUrl) {
                $pathInfo = $requestUri;
            }
            $pathInfo = $this->pathInfoProcessor->process($this, $pathInfo);
            $this->originalPathInfo = (string)$pathInfo;
            $this->requestString = $pathInfo . ($pos !== false ? substr($requestUri, $pos) : '');
        }
        $this->pathInfo = (string)$pathInfo;
        return $this;
    }

    /**
     * Check if code declared as direct access frontend name
     * this mean what this url can be used without store code
     *
     * @param   string $code
     * @return  bool
     */
    public function isDirectAccessFrontendName($code)
    {
        return isset($this->directFrontNames[$code]);
    }

    /**
     * Get base path
     *
     * @return string
     */
    public function getBasePath()
    {
        $path = parent::getBasePath();
        if (empty($path)) {
            $path = '/';
        } else {
            $path = str_replace('\\', '/', $path);
        }
        return $path;
    }

    /**
     * Retrieve request front name
     *
     * @return string|null
     */
    public function getFrontName()
    {
        $pathParts = explode('/', trim($this->getPathInfo(), '/'));
        return reset($pathParts);
    }

    /**
     * Set route name
     *
     * @param string $route
     * @return $this
     */
    public function setRouteName($route)
    {
        $this->route = $route;
        $module = $this->routeConfig->getRouteFrontName($route);
        if ($module) {
            $this->setModuleName($module);
        }
        return $this;
    }

    /**
     * Retrieve route name
     *
     * @return string|null
     */
    public function getRouteName()
    {
        return $this->route;
    }

    /**
     * Specify module name where was found currently used controller
     *
     * @param string $module
     * @return $this
     */
    public function setControllerModule($module)
    {
        $this->controllerModule = $module;
        return $this;
    }

    /**
     * Get module name of currently used controller
     *
     * @return  string
     */
    public function getControllerModule()
    {
        return $this->controllerModule;
    }

    /**
     * Collect properties changed by _forward in protected storage
     * before _forward was called first time.
     *
     * @return $this
     */
    public function initForward()
    {
        if (empty($this->beforeForwardInfo)) {
            $this->beforeForwardInfo = [
                'params' => $this->getParams(),
                'action_name' => $this->getActionName(),
                'controller_name' => $this->getControllerName(),
                'module_name' => $this->getModuleName(),
                'route_name' => $this->getRouteName(),
            ];
        }
        return $this;
    }

    /**
     * Retrieve property's value which was before _forward call.
     * If property was not changed during _forward call null will be returned.
     * If passed name will be null whole state array will be returned.
     *
     * @param string $name
     * @return array|string|null
     */
    public function getBeforeForwardInfo($name = null)
    {
        if ($name === null) {
            return $this->beforeForwardInfo;
        } elseif (isset($this->beforeForwardInfo[$name])) {
            return $this->beforeForwardInfo[$name];
        }
        return null;
    }

    /**
     * Check is Request from AJAX
     *
     * @return boolean
     */
    public function isAjax()
    {
        if ($this->isXmlHttpRequest()) {
            return true;
        }
        if ($this->getParam('ajax') || $this->getParam('isAjax')) {
            return true;
        }
        return false;
    }

    /**
     * Get website instance base url
     *
     * @return string
     *
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     */
    public function getDistroBaseUrl()
    {
        $headerHttpHost = $this->getServer('HTTP_HOST');
        $headerHttpHost = $this->converter->cleanString($headerHttpHost);
        $headerServerPort = $this->getServer('SERVER_PORT');
        $headerScriptName = $this->getServer('SCRIPT_NAME');
        $headerHttps = $this->getServer('HTTPS');

        if (isset($headerScriptName) && isset($headerHttpHost)) {
            $secure = !empty($headerHttps)
                && $headerHttps != 'off'
                || isset($headerServerPort)
                && $headerServerPort == '443';
            $scheme = ($secure ? 'https' : 'http') . '://';

            $hostArr = explode(':', $headerHttpHost);
            $host = $hostArr[0];
            $port = isset($hostArr[1])
                && (!$secure && $hostArr[1] != 80 || $secure && $hostArr[1] != 443) ? ':' . $hostArr[1] : '';
            $path = $this->getBasePath();

            return $scheme . $host . $port . rtrim($path, '/') . '/';
        }
        return 'http://localhost/';
    }

    /**
     * Determines a base URL path from environment
     *
     * @param array $server
     * @return string
     */
    public static function getDistroBaseUrlPath($server)
    {
        $result = '';
        if (isset($server['SCRIPT_NAME'])) {
            $envPath = str_replace('\\', '/', dirname(str_replace('\\', '/', $server['SCRIPT_NAME'])));
            if ($envPath != '.' && $envPath != '/') {
                $result = $envPath;
            }
        }
        if (!preg_match('/\/$/', $result)) {
            $result .= '/';
        }
        return $result;
    }

    /**
     * Return url with no script name
     *
     * @param  string $url
     * @return string
     */
    public static function getUrlNoScript($url)
    {
        if (!isset($_SERVER['SCRIPT_NAME'])) {
            return $url;
        }

        if (($pos = strripos($url, basename($_SERVER['SCRIPT_NAME']))) !== false) {
            $url = substr($url, 0, $pos);
        }

        return $url;
    }

    /**
     * Retrieve full action name
     *
     * @param string $delimiter
     * @return string
     */
    public function getFullActionName($delimiter = '_')
    {
        return $this->getRouteName() .
            $delimiter .
            $this->getControllerName() .
            $delimiter .
            $this->getActionName();
    }

    /**
     * @return array
     */
    public function __sleep()
    {
        return [];
    }

    /**
     * {@inheritdoc}
     *
     * @return bool
     */
    public function isSecure()
    {
        if ($this->immediateRequestSecure()) {
            return true;
        }
        /* TODO: Untangle Config dependence on Scope, so that this class can be instantiated even if app is not
        installed MAGETWO-31756 */
        // Check if a proxy sent a header indicating an initial secure request
        $config = $this->objectManager->get('Magento\Framework\App\Config');
        $offLoaderHeader = trim(
            (string)$config->getValue(
                self::XML_PATH_OFFLOADER_HEADER,
                ScopeConfigInterface::SCOPE_TYPE_DEFAULT
            )
        );

        return $this->initialRequestSecure($offLoaderHeader);
    }

    /**
     * {@inheritdoc}
     */
    public function isSafeMethod()
    {
        if ($this->isSafeMethod === null) {
            if (isset($_SERVER['REQUEST_METHOD']) && (in_array($_SERVER['REQUEST_METHOD'], $this->safeRequestTypes))) {
                $this->isSafeMethod = true;
            } else {
                $this->isSafeMethod = false;
            }
        }
        return $this->isSafeMethod;
    }

    /**
     * Checks if the immediate request is delivered over HTTPS
     *
     * @return bool
     */
    protected function immediateRequestSecure()
    {
        $https = $this->getServer('HTTPS');
        return !empty($https) && ($https != 'off');
    }

    /**
     * In case there is a proxy server, checks if the initial request to the proxy was delivered over HTTPS
     *
     * @param string $offLoaderHeader
     * @return bool
     */
    protected function initialRequestSecure($offLoaderHeader)
    {
        $header = $this->getServer($offLoaderHeader);
        $httpHeader = $this->getServer('HTTP_' . $offLoaderHeader);
        return !empty($offLoaderHeader)
        && (isset($header) && ($header === 'https') || isset($httpHeader) && ($httpHeader === 'https'));
    }
}
