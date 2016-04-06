<?php
/**
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magento\Framework\Css\Test\Unit\PreProcessor\File\FileList;

use \Magento\Framework\Css\PreProcessor\File\FileList\Collator;

class CollatorTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var Collator
     */
    protected $model;

    /**
     * @var \Magento\Framework\View\File[]
     */
    protected $originFiles;

    /**
     * @var \Magento\Framework\View\File
     */
    protected $baseFile;

    /**
     * @var \Magento\Framework\View\File
     */
    protected $themeFile;

    protected function setUp()
    {
        $this->baseFile = $this->createLayoutFile('fixture_1.less', 'Fixture_TestModule');
        $this->themeFile = $this->createLayoutFile('fixture.less', 'Fixture_TestModule', 'area/theme/path');
        $this->originFiles = [
            $this->baseFile->getFileIdentifier() => $this->baseFile,
            $this->themeFile->getFileIdentifier() => $this->themeFile,
        ];
        $this->model = new Collator();
    }

    /**
     * Return newly created theme layout file with a mocked theme
     *
     * @param string $filename
     * @param string $module
     * @param string|null $themeFullPath
     * @return \PHPUnit_Framework_MockObject_MockObject|\Magento\Framework\View\File
     */
    protected function createLayoutFile($filename, $module, $themeFullPath = null)
    {
        $theme = null;
        if ($themeFullPath !== null) {
            $theme = $this->getMockForAbstractClass('Magento\Framework\View\Design\ThemeInterface');
            $theme->expects($this->any())->method('getFullPath')->will($this->returnValue($themeFullPath));
        }
        return new \Magento\Framework\View\File($filename, $module, $theme);
    }

    public function testCollate()
    {
        $file = $this->createLayoutFile('test/fixture.less', 'Fixture_TestModule');
        $expected = [
            $this->baseFile->getFileIdentifier() => $this->baseFile,
            $file->getFileIdentifier() => $file,
        ];
        $result = $this->model->collate([$file], $this->originFiles);
        $this->assertSame($expected, $result);
    }
}
