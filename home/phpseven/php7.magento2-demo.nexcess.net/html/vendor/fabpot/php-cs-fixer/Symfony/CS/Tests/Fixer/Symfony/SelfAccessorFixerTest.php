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
 * @author Gregor Harlan <gharlan@web.de>
 */
class SelfAccessorFixerTest extends AbstractFixerTestBase
{
    /**
     * @dataProvider provideExamples
     */
    public function testFix($expected, $input = null)
    {
        $this->makeTest($expected, $input);
    }

    public function provideExamples()
    {
        return array(
            array(
                '<?php class Foo { const BAR = self::BAZ; }',
                '<?php class Foo { const BAR = Foo::BAZ; }',
            ),
            array(
                '<?php class Foo { private $bar = self::BAZ; }',
                '<?php class Foo { private $bar = fOO::BAZ; }', // case insensitive
            ),
            array(
                '<?php class Foo { function bar($a = self::BAR) {} }',
                '<?php class Foo { function bar($a = Foo::BAR) {} }',
            ),
            array(
                '<?php class Foo { function bar() { self::baz(); } }',
                '<?php class Foo { function bar() { Foo::baz(); } }',
            ),
            array(
                '<?php class Foo { function bar() { self::class; } }',
                '<?php class Foo { function bar() { Foo::class; } }',
            ),
            array(
                '<?php class Foo { function bar() { $x instanceof self; } }',
                '<?php class Foo { function bar() { $x instanceof Foo; } }',
            ),
            array(
                '<?php class Foo { function bar() { new self(); } }',
                '<?php class Foo { function bar() { new Foo(); } }',
            ),
            array(
                '<?php interface Foo { const BAR = self::BAZ; function bar($a = self::BAR); }',
                '<?php interface Foo { const BAR = Foo::BAZ; function bar($a = Foo::BAR); }',
            ),

            array(
                '<?php class Foo { const Foo = 1; }',
            ),
            array(
                '<?php class Foo { function foo() { } }',
            ),
            array(
                '<?php class Foo { function bar() { new \Baz\Foo(); } }',
            ),
            array(
                '<?php class Foo { function bar() { new Foo\Baz(); } }',
            ),
            array(
                // PHP < 5.4 compatibility: "self" is not available in closures
                '<?php class Foo { function bar() { function ($a = Foo::BAZ) { new Foo(); }; } }',
            ),
        );
    }

    /**
     * @requires PHP 5.4
     */
    public function testFix54()
    {
        $expected = '<?php trait Foo { function bar() { self::bar(); } }';
        $input = '<?php trait Foo { function bar() { Foo::bar(); } }';

        $this->makeTest($expected, $input);
    }
}
