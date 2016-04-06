<?php
/**
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\Framework\Exception\Test\Unit;

use \Magento\Framework\Exception\EmailNotConfirmedException;
use Magento\Framework\Phrase;

/**
 * Class EmailNotConfirmedExceptionTest
 *
 * @package Magento\Framework\Exception
 */
class EmailNotConfirmedExceptionTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @return void
     */
    public function testConstructor()
    {
        $emailNotConfirmedException = new EmailNotConfirmedException(
            new Phrase(
                EmailNotConfirmedException::EMAIL_NOT_CONFIRMED,
                ['consumer_id' => 1, 'resources' => 'record2']
            )
        );
        $this->assertSame('Email not confirmed', $emailNotConfirmedException->getMessage());
    }
}
