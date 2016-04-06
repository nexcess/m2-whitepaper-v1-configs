<?php

/*
 * This file is part of the PHP CS utility.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Symfony\CS\Tests\Fixer\Symfony;

use Symfony\CS\Tests\Fixer\AbstractFixerTestBase;

/**
 * @author Graham Campbell <graham@mineuk.com>
 */
class PhpdocNoPackageFixerTest extends AbstractFixerTestBase
{
    public function testFixPackage()
    {
        $expected = <<<'EOF'
<?php
    /**
     */

EOF;

        $input = <<<'EOF'
<?php
    /**
     * @package Foo\Bar
     */

EOF;

        $this->makeTest($expected, $input);
    }

    public function testFixSubpackage()
    {
        $expected = <<<'EOF'
<?php
    /**
     */

EOF;

        $input = <<<'EOF'
<?php
    /**
     * @subpackage Foo\Bar\Baz
     */

EOF;

        $this->makeTest($expected, $input);
    }

    public function testFixMany()
    {
        $expected = <<<'EOF'
<?php
/**
 * Hello!
 */

EOF;

        $input = <<<'EOF'
<?php
/**
 * Hello!
 * @package
 * @subpackage
 */

EOF;

        $this->makeTest($expected, $input);
    }

    public function testDoNothing()
    {
        $expected = <<<'EOF'
<?php
    /**
     * @var package
     */

EOF;

        $this->makeTest($expected);
    }
}
