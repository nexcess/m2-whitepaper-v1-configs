<?php
return array (
  'backend' => 
  array (
    'frontName' => 'admin_41207x',
  ),
  'install' => 
  array (
    'date' => 'Tue, 09 Feb 2016 19:48:15 +0000',
  ),
  'crypt' => 
  array (
    'key' => '856804ac7bf3418384ede93320ea9737',
  ),
  'session' => 
  array (
    'save' => 'memcache',
    'save_path' => 'tcp://127.0.0.1:11212',
  ),
  'db' => 
  array (
    'table_prefix' => '',
    'connection' => 
    array (
      'default' => 
      array (
        'host' => 'localhost',
        'dbname' => 'magento1_magento',
        'username' => 'magento1_magento',
        'password' => 'TwingeInfirmAcingSlice93',
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
          'port' => '6380',
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
  ),
);
