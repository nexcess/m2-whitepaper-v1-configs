<?php
/**
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magento\Indexer\Test\Unit\Console\Command;

use Magento\Indexer\Console\Command\IndexerStatusCommand;
use Symfony\Component\Console\Tester\CommandTester;

class IndexerStatusCommandTest extends AbstractIndexerCommandCommonSetup
{
    /**
     * Command being tested
     *
     * @var IndexerStatusCommand
     */
    private $command;

    public function testExecuteAll()
    {
        $this->configureAdminArea();
        $collection = $this->getMock('Magento\Indexer\Model\Indexer\Collection', [], [], '', false);
        $indexerOne = $this->getMock('Magento\Indexer\Model\Indexer', [], [], '', false);
        $indexerOne->expects($this->once())->method('getTitle')->willReturn('Title_indexerOne');
        $indexerOne
            ->expects($this->once())
            ->method('getStatus')
            ->willReturn(\Magento\Framework\Indexer\StateInterface::STATUS_VALID);
        $indexerTwo = $this->getMock('Magento\Indexer\Model\Indexer', [], [], '', false);
        $indexerTwo->expects($this->once())->method('getTitle')->willReturn('Title_indexerTwo');
        $indexerTwo
            ->expects($this->once())
            ->method('getStatus')
            ->willReturn(\Magento\Framework\Indexer\StateInterface::STATUS_INVALID);
        $indexerThree = $this->getMock('Magento\Indexer\Model\Indexer', [], [], '', false);
        $indexerThree->expects($this->once())->method('getTitle')->willReturn('Title_indexerThree');
        $indexerThree
            ->expects($this->once())
            ->method('getStatus')
            ->willReturn(\Magento\Framework\Indexer\StateInterface::STATUS_WORKING);
        $indexerFour = $this->getMock('Magento\Indexer\Model\Indexer', [], [], '', false);
        $indexerFour->expects($this->once())->method('getTitle')->willReturn('Title_indexerFour');
        $collection
            ->expects($this->once())
            ->method('getItems')
            ->willReturn([$indexerOne, $indexerTwo, $indexerThree, $indexerFour]);

        $this->collectionFactory->expects($this->once())->method('create')->will($this->returnValue($collection));
        $this->indexerFactory->expects($this->never())->method('create');
        $this->command = new IndexerStatusCommand($this->objectManagerFactory);
        $commandTester = new CommandTester($this->command);
        $commandTester->execute([]);
        $actualValue = $commandTester->getDisplay();
        $expectedValue = sprintf('%-50s ', 'Title_indexerOne' . ':') . 'Ready' . PHP_EOL
            . sprintf('%-50s ', 'Title_indexerTwo' . ':') . 'Reindex required' . PHP_EOL
            . sprintf('%-50s ', 'Title_indexerThree' . ':') . 'Processing' . PHP_EOL
            . sprintf('%-50s ', 'Title_indexerFour' . ':') . 'unknown' . PHP_EOL;

        $this->assertStringStartsWith($expectedValue, $actualValue);
    }
}
