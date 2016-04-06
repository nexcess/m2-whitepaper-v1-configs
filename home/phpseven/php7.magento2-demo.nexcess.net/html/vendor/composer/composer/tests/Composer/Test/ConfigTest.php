<?php

/*
 * This file is part of Composer.
 *
 * (c) Nils Adermann <naderman@naderman.de>
 *     Jordi Boggiano <j.boggiano@seld.be>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Composer\Test;

use Composer\Config;

class ConfigTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @dataProvider dataAddPackagistRepository
     */
    public function testAddPackagistRepository($expected, $localConfig, $systemConfig = null)
    {
        $config = new Config(false);
        if ($systemConfig) {
            $config->merge(array('repositories' => $systemConfig));
        }
        $config->merge(array('repositories' => $localConfig));

        $this->assertEquals($expected, $config->getRepositories());
    }

    public function dataAddPackagistRepository()
    {
        $data = array();
        $data['local config inherits system defaults'] = array(
            array(
                'packagist' => array('type' => 'composer', 'url' => 'https?://packagist.org', 'allow_ssl_downgrade' => true)
            ),
            array(),
        );

        $data['local config can disable system config by name'] = array(
            array(),
            array(
                array('packagist' => false),
            )
        );

        $data['local config adds above defaults'] = array(
            array(
                1 => array('type' => 'vcs', 'url' => 'git://github.com/composer/composer.git'),
                0 => array('type' => 'pear', 'url' => 'http://pear.composer.org'),
                'packagist' => array('type' => 'composer', 'url' => 'https?://packagist.org', 'allow_ssl_downgrade' => true),
            ),
            array(
                array('type' => 'vcs', 'url' => 'git://github.com/composer/composer.git'),
                array('type' => 'pear', 'url' => 'http://pear.composer.org'),
            ),
        );

        $data['system config adds above core defaults'] = array(
            array(
                'example.com' => array('type' => 'composer', 'url' => 'http://example.com'),
                'packagist' => array('type' => 'composer', 'url' => 'https?://packagist.org', 'allow_ssl_downgrade' => true)
            ),
            array(),
            array(
                'example.com' => array('type' => 'composer', 'url' => 'http://example.com'),
            ),
        );

        $data['local config can disable repos by name and re-add them anonymously to bring them above system config'] = array(
            array(
                0 => array('type' => 'composer', 'url' => 'http://packagist.org'),
                'example.com' => array('type' => 'composer', 'url' => 'http://example.com')
            ),
            array(
                array('packagist' => false),
                array('type' => 'composer', 'url' => 'http://packagist.org')
            ),
            array(
                'example.com' => array('type' => 'composer', 'url' => 'http://example.com'),
            ),
        );

        $data['local config can override by name to bring a repo above system config'] = array(
            array(
                'packagist' => array('type' => 'composer', 'url' => 'http://packagistnew.org'),
                'example.com' => array('type' => 'composer', 'url' => 'http://example.com')
            ),
            array(
                'packagist' => array('type' => 'composer', 'url' => 'http://packagistnew.org')
            ),
            array(
                'example.com' => array('type' => 'composer', 'url' => 'http://example.com'),
            ),
        );

        $data['incorrect local config does not cause ErrorException'] = array(
            array(
                'packagist' => array('type' => 'composer', 'url' => 'https?://packagist.org', 'allow_ssl_downgrade' => true),
                'type' => 'vcs',
                'url' => 'http://example.com',
            ),
            array(
                'type' => 'vcs',
                'url' => 'http://example.com',
            ),
        );

        return $data;
    }

    public function testMergeGithubOauth()
    {
        $config = new Config(false);
        $config->merge(array('config' => array('github-oauth' => array('foo' => 'bar'))));
        $config->merge(array('config' => array('github-oauth' => array('bar' => 'baz'))));

        $this->assertEquals(array('foo' => 'bar', 'bar' => 'baz'), $config->get('github-oauth'));
    }

    public function testVarReplacement()
    {
        $config = new Config(false);
        $config->merge(array('config' => array('a' => 'b', 'c' => '{$a}')));
        $config->merge(array('config' => array('bin-dir' => '$HOME', 'cache-dir' => '~/foo/')));

        $home = rtrim(getenv('HOME') ?: getenv('USERPROFILE'), '\\/');
        $this->assertEquals('b', $config->get('c'));
        $this->assertEquals($home.'/', $config->get('bin-dir'));
        $this->assertEquals($home.'/foo', $config->get('cache-dir'));
    }

    public function testRealpathReplacement()
    {
        $config = new Config(false, '/foo/bar');
        $config->merge(array('config' => array(
            'bin-dir' => '$HOME/foo',
            'cache-dir' => '/baz/',
            'vendor-dir' => 'vendor'
        )));

        $home = rtrim(getenv('HOME') ?: getenv('USERPROFILE'), '\\/');
        $this->assertEquals('/foo/bar/vendor', $config->get('vendor-dir'));
        $this->assertEquals($home.'/foo', $config->get('bin-dir'));
        $this->assertEquals('/baz', $config->get('cache-dir'));
    }

    public function testFetchingRelativePaths()
    {
        $config = new Config(false, '/foo/bar');
        $config->merge(array('config' => array(
            'bin-dir' => '{$vendor-dir}/foo',
            'vendor-dir' => 'vendor'
        )));

        $this->assertEquals('/foo/bar/vendor', $config->get('vendor-dir'));
        $this->assertEquals('/foo/bar/vendor/foo', $config->get('bin-dir'));
        $this->assertEquals('vendor', $config->get('vendor-dir', Config::RELATIVE_PATHS));
        $this->assertEquals('vendor/foo', $config->get('bin-dir', Config::RELATIVE_PATHS));
    }

    public function testOverrideGithubProtocols()
    {
        $config = new Config(false);
        $config->merge(array('config' => array('github-protocols' => array('https', 'git'))));
        $config->merge(array('config' => array('github-protocols' => array('https'))));

        $this->assertEquals(array('https'), $config->get('github-protocols'));
    }
}
