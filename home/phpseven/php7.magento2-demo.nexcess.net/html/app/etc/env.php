<?php
return array (
  'backend' => 
  array (
    'frontName' => 'admin_1u63dh',
  ),
  'install' => 
  array (
    'date' => 'Mon, 15 Feb 2016 15:48:12 +0000',
  ),
  'crypt' => 
  array (
    'key' => '2f6b3ffb3599cc447fab89a3d3eb0892',
  ),
  'session' => 
  array (
    'save' => 'files',
  ),
  'db' => 
  array (
    'table_prefix' => '',
    'connection' => 
    array (
      'default' => 
      array (
        'host' => 'localhost',
        'dbname' => 'phpseven_magento',
        'username' => 'phpseven_magento',
        'password' => 'GoingsGlobeClocksSoared35',
        'active' => '1',
      ),
    ),
  ),
  'resource' => 
  array (
    'default_setup' => 
    array (
      'connection' => 'default',
    ),
  ),
  'x-frame-options' => 'SAMEORIGIN',
  'MAGE_MODE' => 'production',
  'cache' => 
  array (
    'frontend' => 
    array (
      'page_cache' => 
      array (
        'backend' => 'Cm_Cache_Backend_Redis',
        'backend_options' => 
        array (
          'server' => '127.0.0.1',
          'port' => '6381',
          'persistent' => '',
          'database' => 0,
          'password' => '',
          'force_standalone' => 0,
          'connect_retries' => 1,
        ),
      ),
    ),
  ),
  'cache_types' => 
  array (
    'config' => 1,
    'layout' => 1,
    'block_html' => 1,
    'collections' => 1,
    'reflection' => 1,
    'db_ddl' => 1,
    'eav' => 1,
    'config_integration' => 1,
    'config_integration_api' => 1,
    'full_page' => 1,
    'translate' => 1,
    'config_webservice' => 1,
    'compiled_config' => 1,
  ),
);
