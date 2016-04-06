<?php
/**
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magento\Developer\Test\Unit\Console\Command;

use Magento\Framework\Validator\Locale;
use Magento\Framework\View\Asset\Repository;
use Magento\Framework\App\View\Asset\Publisher;
use Magento\Framework\View\Asset\LocalInterface;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Magento\Developer\Console\Command\SourceThemeDeployCommand;

/**
 * Class SourceThemeDeployCommandTest
 *
 * @see \Magento\Developer\Console\Command\SourceThemeDeployCommand
 */
class SourceThemeDeployCommandTest extends \PHPUnit_Framework_TestCase
{
    const AREA_TEST_VALUE = 'area-test-value';

    const LOCALE_TEST_VALUE = 'locale-test-value';

    const THEME_TEST_VALUE = 'theme-test-value';

    const TYPE_TEST_VALUE = 'type-test-value';

    const FILE_TEST_VALUE = 'file-test-value/test/file';

    /**
     * @var SourceThemeDeployCommand
     */
    private $sourceThemeDeployCommand;

    /**
     * @var Locale|\PHPUnit_Framework_MockObject_MockObject
     */
    private $validatorMock;

    /**
     * @var Publisher|\PHPUnit_Framework_MockObject_MockObject
     */
    private $assetPublisherMock;

    /**
     * @var Repository|\PHPUnit_Framework_MockObject_MockObject
     */
    private $assetRepositoryMock;

    /**
     * Set up
     */
    protected function setUp()
    {
        $this->validatorMock = $this->getMockBuilder(Locale::class)
            ->disableOriginalConstructor()
            ->getMock();
        $this->assetPublisherMock = $this->getMockBuilder(Publisher::class)
            ->disableOriginalConstructor()
            ->getMock();
        $this->assetRepositoryMock = $this->getMockBuilder(Repository::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->sourceThemeDeployCommand = new SourceThemeDeployCommand(
            $this->validatorMock,
            $this->assetPublisherMock,
            $this->assetRepositoryMock
        );
    }

    /**
     * Run test for execute method
     */
    public function testExecute()
    {
        /** @var OutputInterface|\PHPUnit_Framework_MockObject_MockObject $outputMock */
        $outputMock = $this->getMockBuilder(OutputInterface::class)
            ->getMockForAbstractClass();
        $assetMock = $this->getMockBuilder(LocalInterface::class)
            ->getMockForAbstractClass();

        $this->validatorMock->expects(self::once())
            ->method('isValid')
            ->with(self::LOCALE_TEST_VALUE)
            ->willReturn(true);

        $message = sprintf(
            '<info>Processed Area: %s, Locale: %s, Theme: %s, File type: %s.</info>',
            self::AREA_TEST_VALUE,
            self::LOCALE_TEST_VALUE,
            self::THEME_TEST_VALUE,
            self::TYPE_TEST_VALUE
        );

        $outputMock->expects(self::at(0))
            ->method('writeln')
            ->with($message);
        $outputMock->expects(self::at(1))
            ->method('writeln')
            ->with('<comment>-> file-test-value/test/file</comment>');
        $outputMock->expects(self::at(2))
            ->method('writeln')
            ->with('<info>Successfully processed.</info>');

        $this->assetRepositoryMock->expects(self::once())
            ->method('createAsset')
            ->with(
                'file-test-value/test' . DIRECTORY_SEPARATOR . 'file' . '.' . self::TYPE_TEST_VALUE,
                [
                    'area' => self::AREA_TEST_VALUE,
                    'theme' => self::THEME_TEST_VALUE,
                    'locale' => self::LOCALE_TEST_VALUE,
                ]
            )->willReturn($assetMock);

        $this->assetPublisherMock->expects(self::once())
            ->method('publish')
            ->with($assetMock);

        $assetMock->expects(self::once())
            ->method('getFilePath')
            ->willReturn(self::FILE_TEST_VALUE);

        $this->sourceThemeDeployCommand->run($this->getInputMock(), $outputMock);
    }

    /**
     * @return InputInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    private function getInputMock()
    {
        $inputMock = $this->getMockBuilder(InputInterface::class)
            ->getMockForAbstractClass();

        $inputMock->expects(self::exactly(4))
            ->method('getOption')
            ->willReturnMap(
                [
                    ['area', self::AREA_TEST_VALUE],
                    ['locale', self::LOCALE_TEST_VALUE],
                    ['theme', self::THEME_TEST_VALUE],
                    ['type', self::TYPE_TEST_VALUE]
                ]
            );
        $inputMock->expects(self::once())
            ->method('getArgument')
            ->with('file')
            ->willReturn([self::FILE_TEST_VALUE]);

        return $inputMock;
    }
}
