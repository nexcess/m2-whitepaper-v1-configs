<?php
/**
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */

// @codingStandardsIgnoreFile

namespace Magento\Framework\Autoload\Test\Unit;

use Composer\Autoload\ClassLoader;
use Magento\Framework\TestFramework\Unit\Helper\ObjectManager;

class ClassLoaderWrapperTest extends \PHPUnit_Framework_TestCase
{
    const PREFIX = 'Namespace\\Prefix\\';

    const DIR = '/path/to/class/';

    const DEFAULT_PREPEND = false;

    /**
     * @var ClassLoader | \PHPUnit_Framework_MockObject_MockObject
     */
    protected $autoloaderMock;

    /**
     * @var \Magento\Framework\Autoload\ClassLoaderWrapper
     */
    protected $model;

    public function setUp()
    {
        $this->autoloaderMock = $this->getMock('Composer\Autoload\ClassLoader');
        $this->model = (new ObjectManager($this))->getObject('Magento\Framework\Autoload\ClassLoaderWrapper',
            [
                'autoloader' => $this->autoloaderMock
            ]
        );
    }

    public function testAdd()
    {
        $prepend = true;

        $this->autoloaderMock->expects($this->once())
            ->method('add')
            ->with(self::PREFIX, self::DIR, $prepend);

        $this->model->addPsr0(self::PREFIX, self::DIR, $prepend);
    }

    public function testAddPsr4()
    {
        $prepend = true;

        $this->autoloaderMock->expects($this->once())
            ->method('addPsr4')
            ->with(self::PREFIX, self::DIR, $prepend);

        $this->model->addPsr4(self::PREFIX, self::DIR, $prepend);
    }

    public function testAddDefault()
    {
        $this->autoloaderMock->expects($this->once())
            ->method('add')
            ->with(self::PREFIX, self::DIR, self::DEFAULT_PREPEND);

        $this->model->addPsr0(self::PREFIX, self::DIR);
    }

    public function testAddPsr4Default()
    {
        $this->autoloaderMock->expects($this->once())
            ->method('addPsr4')
            ->with(self::PREFIX, self::DIR, self::DEFAULT_PREPEND);

        $this->model->addPsr4(self::PREFIX, self::DIR);
    }

    public function testSet()
    {
        $paths = [self::DIR];
        $this->autoloaderMock->expects($this->once())
            ->method('set')
            ->with(self::PREFIX, $paths);

        $this->model->setPsr0(self::PREFIX, $paths);
    }

    public function testSetPsr4()
    {
        $paths = [self::DIR];
        $this->autoloaderMock->expects($this->once())
            ->method('setPsr4')
            ->with(self::PREFIX, $paths);

        $this->model->setPsr4(self::PREFIX, $paths);
    }
}
