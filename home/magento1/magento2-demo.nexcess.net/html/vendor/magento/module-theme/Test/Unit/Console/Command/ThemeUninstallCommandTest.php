<?php
/**
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\Theme\Test\Unit\Console\Command;

use Magento\Theme\Console\Command\ThemeUninstallCommand;
use Magento\Theme\Model\Theme\themePackageInfo;
use Magento\Theme\Model\Theme\ThemeUninstaller;
use Magento\Theme\Model\Theme\ThemeDependencyChecker;
use Symfony\Component\Console\Tester\CommandTester;
use Magento\Framework\Setup\BackupRollbackFactory;

/**
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class ThemeUninstallCommandTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\Framework\App\MaintenanceMode|\PHPUnit_Framework_MockObject_MockObject
     */
    private $maintenanceMode;

    /**
     * @var \Magento\Framework\Composer\DependencyChecker|\PHPUnit_Framework_MockObject_MockObject
     */
    private $dependencyChecker;

    /**
     * @var \Magento\Theme\Model\Theme\Data\Collection|\PHPUnit_Framework_MockObject_MockObject
     */
    private $collection;

    /**
     * @var \Magento\Framework\App\Cache|\PHPUnit_Framework_MockObject_MockObject
     */
    private $cache;

    /**
     * @var \Magento\Framework\App\State\CleanupFiles|\PHPUnit_Framework_MockObject_MockObject
     */
    private $cleanupFiles;

    /**
     * @var ThemeUninstallCommand
     */
    private $command;

    /**
     * @var BackupRollbackFactory|\PHPUnit_Framework_MockObject_MockObject
     */
    private $backupRollbackFactory;

    /**
     * Theme Validator
     *
     * @var ThemeValidator|\PHPUnit_Framework_MockObject_MockObject
     */
    private $themeValidator;

    /**
     * @var ThemeUninstaller|\PHPUnit_Framework_MockObject_MockObject
     */
    private $themeUninstaller;

    /**
     * @var ThemeDependencyChecker|\PHPUnit_Framework_MockObject_MockObject
     */
    private $themeDependencyChecker;

    /**
     * @var ThemePackageInfo|\PHPUnit_Framework_MockObject_MockObject
     */
    private $themePackageInfo;

    /**
     * @var CommandTester
     */
    private $tester;

    public function setUp()
    {
        $this->maintenanceMode = $this->getMock('Magento\Framework\App\MaintenanceMode', [], [], '', false);
        $composerInformation = $this->getMock('Magento\Framework\Composer\ComposerInformation', [], [], '', false);
        $composerInformation->expects($this->any())
            ->method('getRootRequiredPackages')
            ->willReturn(['magento/theme-a', 'magento/theme-b', 'magento/theme-c']);
        $this->dependencyChecker = $this->getMock(
            'Magento\Framework\Composer\DependencyChecker',
            [],
            [],
            '',
            false
        );
        $this->collection = $this->getMock('Magento\Theme\Model\Theme\Data\Collection', [], [], '', false);
        $this->cache = $this->getMock('Magento\Framework\App\Cache', [], [], '', false);
        $this->cleanupFiles = $this->getMock('Magento\Framework\App\State\CleanupFiles', [], [], '', false);
        $this->backupRollbackFactory = $this->getMock(
            'Magento\Framework\Setup\BackupRollbackFactory',
            [],
            [],
            '',
            false
        );
        $this->themeValidator = $this->getMock('Magento\Theme\Model\ThemeValidator', [], [], '', false);
        $this->themeUninstaller = $this->getMock('Magento\Theme\Model\Theme\ThemeUninstaller', [], [], '', false);
        $this->themeDependencyChecker = $this->getMock(
            'Magento\Theme\Model\Theme\ThemeDependencyChecker',
            [],
            [],
            '',
            false
        );
        $this->themePackageInfo = $this->getMock('Magento\Theme\Model\Theme\ThemePackageInfo', [], [], '', false);
        $this->command = new ThemeUninstallCommand(
            $this->cache,
            $this->cleanupFiles,
            $composerInformation,
            $this->maintenanceMode,
            $this->dependencyChecker,
            $this->collection,
            $this->backupRollbackFactory,
            $this->themeValidator,
            $this->themePackageInfo,
            $this->themeUninstaller,
            $this->themeDependencyChecker
        );
        $this->tester = new CommandTester($this->command);
    }

    public function testExecuteFailedValidationNotPackage()
    {
        $this->themePackageInfo->expects($this->at(0))->method('getPackageName')->willReturn('dummy');
        $this->themePackageInfo->expects($this->at(1))->method('getPackageName')->willReturn('magento/theme-a');
        $this->collection->expects($this->any())
            ->method('getThemeByFullPath')
            ->willReturn($this->getMockForAbstractClass('Magento\Framework\View\Design\ThemeInterface', [], '', false));
        $this->collection->expects($this->any())->method('hasTheme')->willReturn(true);
        $this->tester->execute(['theme' => ['test1', 'test2']]);
        $this->assertContains(
            'test1 is not an installed Composer package',
            $this->tester->getDisplay()
        );
        $this->assertNotContains(
            'test2 is not an installed Composer package',
            $this->tester->getDisplay()
        );
    }

    public function testExecuteFailedValidationNotTheme()
    {
        $this->themePackageInfo->expects($this->exactly(2))->method('getPackageName')->willReturn('');
        $this->collection->expects($this->any())
            ->method('getThemeByFullPath')
            ->willReturn($this->getMockForAbstractClass('Magento\Framework\View\Design\ThemeInterface', [], '', false));
        $this->collection->expects($this->any())->method('hasTheme')->willReturn(false);
        $this->tester->execute(['theme' => ['test1', 'test2']]);
        $this->assertContains(
            'Unknown theme(s): test1, test2' . PHP_EOL,
            $this->tester->getDisplay()
        );
    }

    public function testExecuteFailedValidationMixed()
    {
        $this->themePackageInfo->expects($this->exactly(4))
            ->method('getPackageName')
            ->will($this->returnValueMap([
                ['test1', 'dummy1'], ['test2', 'magento/theme-b'], ['test3', ''], ['test4', 'dummy2']
            ]));
        $this->collection->expects($this->any())
            ->method('getThemeByFullPath')
            ->willReturn($this->getMockForAbstractClass('Magento\Framework\View\Design\ThemeInterface', [], '', false));
        $this->collection->expects($this->at(1))->method('hasTheme')->willReturn(true);
        $this->collection->expects($this->at(3))->method('hasTheme')->willReturn(true);
        $this->collection->expects($this->at(5))->method('hasTheme')->willReturn(false);
        $this->collection->expects($this->at(7))->method('hasTheme')->willReturn(true);
        $this->tester->execute(['theme' => ['test1', 'test2', 'test3', 'test4']]);
        $this->assertContains(
            'test1, test4 are not installed Composer packages',
            $this->tester->getDisplay()
        );
        $this->assertNotContains(
            'test2 is not an installed Composer package',
            $this->tester->getDisplay()
        );
        $this->assertContains(
            'Unknown theme(s): test3' . PHP_EOL,
            $this->tester->getDisplay()
        );
    }

    public function setUpPassValidation()
    {
        $this->themePackageInfo->expects($this->any())->method('getPackageName')->willReturn('magento/theme-a');
        $this->collection->expects($this->any())
            ->method('getThemeByFullPath')
            ->willReturn($this->getMockForAbstractClass('Magento\Framework\View\Design\ThemeInterface', [], '', false));
        $this->themeDependencyChecker->expects($this->any())->method('checkChildTheme')->willReturn([]);
        $this->collection->expects($this->any())->method('hasTheme')->willReturn(true);
    }

    public function setupPassChildThemeCheck()
    {
        $theme = $this->getMock('Magento\Theme\Model\Theme', [], [], '', false);
        $theme->expects($this->any())->method('hasChildThemes')->willReturn(false);
        $this->collection->expects($this->any())->method('getIterator')->willReturn(new \ArrayIterator([]));
    }

    public function setupPassThemeInUseCheck()
    {
        $this->themeValidator->expects($this->once())->method('validateIsThemeInUse')->willReturn([]);
    }

    public function setupPassDependencyCheck()
    {
        $this->dependencyChecker->expects($this->once())->method('checkDependencies')->willReturn([]);
    }

    public function testExecuteFailedThemeInUseCheck()
    {
        $this->setUpPassValidation();
        $this->setupPassChildThemeCheck();
        $this->setupPassDependencyCheck();
        $this->themeValidator
            ->expects($this->once())
            ->method('validateIsThemeInUse')
            ->willReturn(['frontend/Magento/a is in use in default config']);
        $this->tester->execute(['theme' => ['frontend/Magento/a']]);
        $this->assertEquals(
            'Unable to uninstall. Please resolve the following issues:' . PHP_EOL
            . 'frontend/Magento/a is in use in default config' . PHP_EOL,
            $this->tester->getDisplay()
        );
    }

    public function testExecuteFailedDependencyCheck()
    {
        $this->setUpPassValidation();
        $this->setupPassThemeInUseCheck();
        $this->setupPassChildThemeCheck();
        $this->dependencyChecker->expects($this->once())
            ->method('checkDependencies')
            ->willReturn(['magento/theme-a' => ['magento/theme-b', 'magento/theme-c']]);
        $this->tester->execute(['theme' => ['frontend/Magento/a']]);
        $this->assertContains(
            'Unable to uninstall. Please resolve the following issues:' . PHP_EOL .
            'frontend/Magento/a has the following dependent package(s):'
            . PHP_EOL . "\tmagento/theme-b" . PHP_EOL . "\tmagento/theme-c",
            $this->tester->getDisplay()
        );
    }

    public function setUpExecute()
    {
        $this->setUpPassValidation();
        $this->setupPassThemeInUseCheck();
        $this->setupPassChildThemeCheck();
        $this->setupPassDependencyCheck();
        $this->cache->expects($this->once())->method('clean');

        $this->themeUninstaller->expects($this->once())
            ->method('uninstallRegistry')
            ->with($this->isInstanceOf('Symfony\Component\Console\Output\OutputInterface'), $this->anything());
        $this->themeUninstaller->expects($this->once())
            ->method('uninstallCode')
            ->with($this->isInstanceOf('Symfony\Component\Console\Output\OutputInterface'), $this->anything());
    }

    public function testExecuteWithBackupCode()
    {
        $this->setUpExecute();
        $backupRollback = $this->getMock('Magento\Framework\Setup\BackupRollback', [], [], '', false);
        $this->backupRollbackFactory->expects($this->once())
            ->method('create')
            ->willReturn($backupRollback);
        $this->tester->execute(['theme' => ['test'], '--backup-code' => true]);
        $this->tester->getDisplay();
    }

    public function testExecute()
    {
        $this->setUpExecute();
        $this->cleanupFiles->expects($this->never())->method('clearMaterializedViewFiles');
        $this->tester->execute(['theme' => ['test']]);
        $this->assertContains('Enabling maintenance mode', $this->tester->getDisplay());
        $this->assertContains('Disabling maintenance mode', $this->tester->getDisplay());
        $this->assertContains('Alert: Generated static view files were not cleared.', $this->tester->getDisplay());
        $this->assertNotContains('Generated static view files cleared successfully', $this->tester->getDisplay());
    }

    public function testExecuteCleanStaticFiles()
    {
        $this->setUpExecute();
        $this->cleanupFiles->expects($this->once())->method('clearMaterializedViewFiles');
        $this->tester->execute(['theme' => ['test'], '-c' => true]);
        $this->assertContains('Enabling maintenance mode', $this->tester->getDisplay());
        $this->assertContains('Disabling maintenance mode', $this->tester->getDisplay());
        $this->assertNotContains('Alert: Generated static view files were not cleared.', $this->tester->getDisplay());
        $this->assertContains('Generated static view files cleared successfully', $this->tester->getDisplay());
    }
}
