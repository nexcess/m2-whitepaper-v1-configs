<?php
/**
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magento\Framework\Setup\Test\Unit;

use Magento\Framework\Backup\Factory;
use Magento\Framework\Setup\BackupRollback;
use Magento\Framework\Setup\LoggerInterface;

class BackupRollbackTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\Framework\ObjectManagerInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    private $objectManager;

    /**
     * @var LoggerInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    private $log;

    /**
     * @var \Magento\Framework\App\Filesystem\DirectoryList|\PHPUnit_Framework_MockObject_MockObject
     */
    private $directoryList;

    /**
     * @var BackupRollback
     */
    private $model;

    /**
     * @var \Magento\Framework\Filesystem\Driver\File|\PHPUnit_Framework_MockObject_MockObject
     */
    private $file;

    /**
     * @var \Magento\Framework\Backup\Filesystem|\PHPUnit_Framework_MockObject_MockObject
     */
    private $filesystem;

    /**
     * @var \Magento\Framework\Backup\Filesystem\Helper|\PHPUnit_Framework_MockObject_MockObject
     */
    private $helper;

    /**
     * @var \Magento\Framework\Backup\Db|\PHPUnit_Framework_MockObject_MockObject
     */
    private $database;

    /**
     * @var string
     */
    private $path;

    public function setUp()
    {
        $this->objectManager = $this->getMock('Magento\Framework\ObjectManagerInterface', [], [], '', false);
        $this->log = $this->getMock('Magento\Framework\Setup\LoggerInterface', [], [], '', false);
        $this->directoryList = $this->getMock('Magento\Framework\App\Filesystem\DirectoryList', [], [], '', false);
        $this->path = realpath(__DIR__);
        $this->directoryList->expects($this->any())
            ->method('getRoot')
            ->willReturn($this->path);
        $this->directoryList->expects($this->any())
            ->method('getPath')
            ->willReturn($this->path);
        $this->file = $this->getMock('Magento\Framework\Filesystem\Driver\File', [], [], '', false);
        $this->filesystem = $this->getMock('Magento\Framework\Backup\Filesystem', [], [], '', false);
        $this->database = $this->getMock('Magento\Framework\Backup\Db', [], [], '', false);
        $this->helper = $this->getMock('Magento\Framework\Backup\Filesystem\Helper', [], [], '', false);
        $this->helper->expects($this->any())
            ->method('getInfo')
            ->willReturn(['writable' => true, 'size' => 100]);
        $configLoader = $this->getMock('Magento\Framework\App\ObjectManager\ConfigLoader', [], [], '', false);
        $configLoader->expects($this->any())
            ->method('load')
            ->willReturn([]);
        $this->objectManager->expects($this->any())
            ->method('get')
            ->will($this->returnValueMap([
                ['Magento\Framework\App\State', $this->getMock('Magento\Framework\App\State', [], [], '', false)],
                ['Magento\Framework\ObjectManager\ConfigLoaderInterface', $configLoader],
            ]));
        $this->objectManager->expects($this->any())
            ->method('create')
            ->will($this->returnValueMap([
                ['Magento\Framework\Backup\Filesystem\Helper', [], $this->helper],
                ['Magento\Framework\Backup\Filesystem', [], $this->filesystem],
                ['Magento\Framework\Backup\Db', [], $this->database],
            ]));
        $this->model = new BackupRollback(
            $this->objectManager,
            $this->log,
            $this->directoryList,
            $this->file,
            $this->helper
        );
    }

    public function testCodeBackup()
    {
        $this->setupCodeBackupRollback();
        $this->filesystem->expects($this->once())
            ->method('create');
        $this->file->expects($this->once())->method('isExists')->with($this->path . '/backups')->willReturn(false);
        $this->file->expects($this->once())->method('createDirectory')->with($this->path . '/backups', 0770);
        $this->model->codeBackup(time());
    }

    /**
     * @expectedException \Magento\Framework\Exception\LocalizedException
     * @expectedExceptionMessage This backup type \'txt\' is not supported.
     */
    public function testCodeBackupWithInvalidType()
    {
        $this->model->codeBackup(time(), 'txt');
    }

    public function testCodeRollback()
    {
        $this->filesystem->expects($this->once())->method('rollback');
        $this->setupCodeBackupRollback();
        $this->file->expects($this->once())
            ->method('isExists')
            ->with($this->path . '/backups/12345_filesystem_code.tgz')
            ->willReturn(true);
        $this->model->codeRollback('12345_filesystem_code.tgz');
    }

    /**
     * @expectedException \Magento\Framework\Exception\LocalizedException
     * @expectedExceptionMessage The rollback file does not exist.
     */
    public function testCodeRollbackWithInvalidFilePath()
    {
        $this->file->expects($this->once())
            ->method('isExists')
            ->willReturn(false);
        $this->model->codeRollback('12345_filesystem_code.tgz');
    }

    /**
     * @expectedException \Magento\Framework\Exception\LocalizedException
     * @expectedExceptionMessage Invalid rollback file.
     */
    public function testCodeRollbackWithInvalidFileType()
    {
        $this->model->codeRollback('RollbackFile_A.txt');
    }

    public function testMediaBackup()
    {
        $this->setupCodeBackupRollback();
        $this->filesystem->expects($this->once())
            ->method('create');
        $this->file->expects($this->once())->method('isExists')->with($this->path . '/backups')->willReturn(false);
        $this->file->expects($this->once())->method('createDirectory')->with($this->path . '/backups', 0770);
        $this->model->codeBackup(time(), Factory::TYPE_MEDIA);
    }

    public function testMediaRollback()
    {
        $this->filesystem->expects($this->once())->method('rollback');
        $this->setupCodeBackupRollback();
        $this->file->expects($this->once())
            ->method('isExists')
            ->with($this->path . '/backups/12345_filesystem_media.tgz')
            ->willReturn(true);
        $this->model->codeRollback('12345_filesystem_media.tgz', Factory::TYPE_MEDIA);
    }

    public function testDbBackup()
    {
        $this->setupDbBackupRollback();
        $this->database->expects($this->once())->method('create');
        $this->file->expects($this->once())->method('isExists')->willReturn(false);
        $this->file->expects($this->once())->method('createDirectory');
        $this->model->dbBackup(time());
    }

    public function testDbRollback()
    {
        $this->setupDbBackupRollback();
        $this->database->expects($this->once())->method('rollback');
        $this->file->expects($this->once())
            ->method('isExists')
            ->with($this->path . '/backups/12345_db.gz')
            ->willReturn(true);
        $this->model->dbRollback('12345_db.gz');
    }

    private function setupCodeBackupRollback()
    {
        $this->filesystem->expects($this->once())
            ->method('addIgnorePaths');
        $this->filesystem->expects($this->once())
            ->method('setBackupsDir');
        $this->filesystem->expects($this->once())
            ->method('setBackupExtension');
        $this->filesystem->expects($this->once())
            ->method('setTime');
        $this->filesystem->expects($this->once())
            ->method('getBackupFilename')
            ->willReturn('RollbackFile_A.tgz');
        $this->filesystem->expects($this->atLeastOnce())
            ->method('getBackupPath')
            ->willReturn('pathToFile/12345_filesystem_code.tgz');
        $this->log->expects($this->once())
            ->method('logSuccess');
    }

    private function setupDbBackupRollback()
    {
        $this->database->expects($this->once())
            ->method('setBackupsDir');
        $this->database->expects($this->once())
            ->method('setBackupExtension');
        $this->database->expects($this->once())
            ->method('setTime');
        $this->database->expects($this->once())
            ->method('getBackupFilename')
            ->willReturn('RollbackFile_A.gz');
        $this->database->expects($this->atLeastOnce())
            ->method('getBackupPath')
            ->willReturn('pathToFile/12345_db.tgz');
        $this->log->expects($this->once())
            ->method('logSuccess');
    }

    public function testGetFSDiskSpaceback()
    {
        $size = $this->model->getFSDiskSpace();
        $this->assertEquals(100, $size);
    }

    public function testGetDBDiskSpace()
    {
        $this->database->expects($this->once())->method('getDBSize')->willReturn(100);
        $size = $this->model->getDBDiskSpace();
        $this->assertEquals(100, $size);
    }
}
