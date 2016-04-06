<?php
/**
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magento\Framework\Exception\Test\Unit;

use \Magento\Framework\Exception\AuthorizationException;
use Magento\Framework\Phrase;

class AuthorizationExceptionTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @return void
     */
    public function testConstructor()
    {
        $authorizationException = new AuthorizationException(
            new Phrase(
                AuthorizationException::NOT_AUTHORIZED,
                ['consumer_id' => 1, 'resources' => 'record2']
            )
        );
        $this->assertSame('Consumer is not authorized to access record2', $authorizationException->getMessage());
    }
}
