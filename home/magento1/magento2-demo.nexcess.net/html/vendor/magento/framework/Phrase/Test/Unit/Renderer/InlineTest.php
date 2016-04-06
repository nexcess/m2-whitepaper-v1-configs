<?php
/**
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magento\Framework\Phrase\Test\Unit\Renderer;

class InlineTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\Framework\TranslateInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $translator;

    /**
     * @var \Magento\Framework\Phrase\Renderer\Translate
     */
    protected $renderer;

    /**
     * @var \Magento\Framework\Translate\Inline\ProviderInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $provider;

    /**
     * @var \Psr\Log\LoggerInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $loggerMock;

    protected function setUp()
    {
        $this->translator = $this->getMock('Magento\Framework\TranslateInterface', [], [], '', false);
        $this->provider = $this->getMock('Magento\Framework\Translate\Inline\ProviderInterface', [], [], '', false);
        $this->loggerMock = $this->getMockBuilder('Psr\Log\LoggerInterface')
            ->getMock();

        $this->renderer = new \Magento\Framework\Phrase\Renderer\Inline(
            $this->translator,
            $this->provider,
            $this->loggerMock
        );
    }

    public function testRenderIfInlineTranslationIsAllowed()
    {
        $theme = 'theme';
        $text = 'test';
        $result = sprintf('{{{%s}}{{%s}}}', $text, $theme);

        $this->translator->expects($this->once())
            ->method('getTheme')
            ->will($this->returnValue($theme));

        $inlineTranslate = $this->getMock('Magento\Framework\Translate\InlineInterface', [], [], '', []);
        $inlineTranslate->expects($this->once())
            ->method('isAllowed')
            ->will($this->returnValue(true));

        $this->provider->expects($this->once())
            ->method('get')
            ->will($this->returnValue($inlineTranslate));

        $this->assertEquals($result, $this->renderer->render([$text], []));
    }

    public function testRenderIfInlineTranslationIsNotAllowed()
    {
        $text = 'test';

        $inlineTranslate = $this->getMock('Magento\Framework\Translate\InlineInterface', [], [], '', []);
        $inlineTranslate->expects($this->once())
            ->method('isAllowed')
            ->will($this->returnValue(false));

        $this->provider->expects($this->once())
            ->method('get')
            ->will($this->returnValue($inlineTranslate));

        $this->assertEquals($text, $this->renderer->render([$text], []));
    }

    public function testRenderException()
    {
        $message = 'something went wrong';
        $exception = new \Exception($message);

        $this->provider->expects($this->once())
            ->method('get')
            ->willThrowException($exception);

        $this->setExpectedException('Exception', $message);
        $this->renderer->render(['text'], []);
    }
}
